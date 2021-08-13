<?php

namespace admin\modules\Manufacturing\controllers;

use Yii;
use common\models\ProductionOrder;
use common\models\ProductionOrderLine;
use common\models\PrintPage;
use common\models\Company;
use admin\modules\Manufacturing\models\ProductionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use common\models\ItemCraft;
use admin\models\FunctionCenter;

/**
 * ProductionController implements the CRUD actions for ProductionOrder model.
 */
class ProductionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-ajax' => ['POST'],
                    'change-remark' => ['POST'],
                    'list-craft' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ProductionOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
    public function actionRequest(){
        // Create 
        return $this->render('request');
    }
 

    public function actionRequestPrint($id){
        $comp = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        return $this->render('request_print', [
            'id' => $id, 
            'logo' => $comp->logo,
            'qty' => Yii::$app->request->get('qty')?: 0
            ]);
    }
*/
    public function actionListCraft(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $raws           = [];
        $model          = $this->findModel($data->id);
        

        if($model != null){
            $query      = ProductionOrderLine::find()
                        ->where(['source_id' => $model->id])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->all();

            foreach ($query as $key => $value) {

                $craft          = [];
                $query          = ItemCraft::find()
                                ->andWhere(['source_item' => $value->item])
                                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                ->orderBy(['priority' => SORT_ASC])
                                ->all();                
                
                foreach ($query as $key => $model) {
                    
                    $craft[] = [
                        'id'    => $model->id,
                        'item'  => $model->item,
                        'code'  => $model->items->master_code,
                        'name'  => $model->name,
                        'nameTh'=> $model->items->description_th,
                        'alias' => $model->items->name?:' -- ',
                        'qty'   => $model->quantity,
                        'cost'  => $model->cost,
                        'img'   => $model->picture,
                        'stock' => $model->items->qtyAfter,
                        'prio'  => $model->priority
                    ];
                }

                $raws[] = [
                    'id' => $value->id,
                    'child' => $craft
                ];
            }
        }
        
        return json_encode([
            'status' => 200,
            'message' => 'done',
            'raws' => $raws
        ]);
 
    }

    /**
     * Displays a single ProductionOrder model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProductionOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductionOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductionOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProductionOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');

        $transaction = Yii::$app->db->beginTransaction();
        try {

            
            $model  = $this->findModel($data->id);
            if($model != null){
                ProductionOrderLine::deleteAll(['source_id' => $model->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                $model->delete();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $status     = 500;
            $message    = Yii::t('common','Error Header {:e}', [':e' => $e]);                 
            $transaction->rollBack();
        }   


        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Finds the ProductionOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductionOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductionOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }


    public function actionPrint($id){



        $model    = $this->findModel($id);

        $query    = ProductionOrderLine::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $countRow = $query->count();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            //'sort'=> ['defaultOrder' => ['item'=>SORT_ASC]],
        ]);

        $template   = PrintPage::findOne(PrintPage::findCustomPrint('production_order'));
        $Company    = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

      
        $header = (Object)[
            'height'    => $template->header_height,
            'top'       => $template->margin_top,
            'fontsize'  => $template->font_size,
            'title'     => $model->no,
            'watermark' => (Object)[
                'text'      => $template->water_mark,
                'left'      => $template->water_mark_left,
                'top'       => $template->water_mark_top,
                'color'     => $template->water_mark_color,
                'size'      => $template->water_mark_size,
                'radius'    => $template->water_mark_radius,
                'padding'   => $template->water_mark_padding,
                'border'    => $template->water_mark_border,
                'border_color' => $template->water_mark_border_color,
                'css'       => $template->water_mark_css,
                'switch'    => $template->water_mark_switch,
                'img'       => $template->watermark,
                'img_alpha' => $template->water_mark_img_alpha,
                'img_width' => $template->water_mark_img_width
            ],
        ];

        $pageSize   = $template->pagination ?: 15;
        if(isset($_GET['pagesize']))    $pageSize   = $_GET['pagesize'];
        
        $body   = (Object)[
            'height'    => $template->body_height,
            'pagesize'  => $pageSize,
            'fontsize'  => $template->font_size
        ];

        $Bahttext   = new \admin\models\FunctionBahttext();
        

        $defineHeader = [           
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 110px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $countRow > 0 ? $model->no : '',
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => $countRow > 0 ? date('d/m/y',strtotime($model->order_date)) : '',
            '{ORDER_DATE_TH}'       => $countRow > 0 ? date('d/m/y',strtotime($model->order_date.' + 543 Years')) : '',
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,
            '{PAGE_TITLE}'          => 'PDR : '.$model->no,
            '{SALE_NAME}'           => $model->order->sales->name,
            '{SALE_SUR_NAME}'       => $model->order->sales->surname,
            '{SALE_CODE}'           => $model->order->sales->code,             
            '{PO_REFERENCE}'        => $model->order->ext_document,
            '{ORDER_NO}'            => $model->order->no,
            '{CUSTOMER_TAX}'        => $model->order->customer->vat_regis,   
            '{HEAD_OFFICE}'         => $model->order->customer->headofficetb->data_char,  


            '{CUSTOMER_CODE}'       => $model->order->customer->code,
            '{CUSTOMER_NAME}'       => $model->order->customer->name,
            '{CUSTOMER_PHONE}'      => $model->order->customer->phone,
            '{CUSTOMER_FAX}'        => $model->order->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->order->customer->fullAddress['address'],


        
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => $model->remark, //wordwrap($model->remark, 350, "<br/>\n", false),
 
            
 
 
        ];

 

        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-template',[
            'model'         => $model,
            //'dataProvider'  => $dataProvider,
            'print'         => $template,
            'Company'       => $Company,
            'header'        => $header,
            'body'          => $body,
            'defineHeader'  => $defineHeader
        ]);


        // get your HTML raw content without any layouts or scripts

        $content = $this->renderPartial('_print_body',[
                  'model'           => $model,
                  'dataProvider'    => $dataProvider,
                  'header'          => $header,
                  'print'           => $template,
                  'body'            => $body
        ]);

      // setup kartik\mpdf\Pdf component
      $pdf = new Pdf([
          'mode'        => Pdf::MODE_UTF8,
          'format'      => $template->paper_size,  
          'orientation' => $template->paper_orientation,
          'destination' => Pdf::DEST_BROWSER,
          'content'     => $content,
          'cssFile'     => 'css/pdf.css',
          'filename'    => $model->no.'.pdf',
          'cssInline'   => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px;}',
          'options'     => ['title' => 'PO : '.$model->no],           
          'methods' => [
              'WriteHTML' => $PrintTemplate,
          ]
      ]);

      /* Thai Font */
      $defaultConfig = (new ConfigVariables())->getDefaults();
      $fontDirs = $defaultConfig['fontDir'];

      $defaultFontConfig = (new FontVariables())->getDefaults();
      $fontData = $defaultFontConfig['fontdata'];

      $pdf->options['fontDir'] = array_merge($fontDirs, [
          Yii::getAlias('@webroot').'/fonts'
      ]);

      $pdf->options['fontdata'] = $fontData + [
          'saraban' => [
              'R' => 'thsarabunnew-webfont.ttf',
          ],
          'freesiaupc' => [
              'R' => 'FreesiaUPC.ttf', 
          ]
      ];

      return $pdf->render();
    }




    public function actionChangeRemark(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');

        $model          = ProductionOrder::findOne($data->id);
        $model->remark  = $data->remark;

        if(!$model->save()){
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }
}
