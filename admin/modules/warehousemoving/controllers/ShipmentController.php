<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use yii\helpers\Html;
use common\models\WarehouseMoving;
use common\models\WarehouseHeader;

use admin\modules\warehousemoving\models\ShipmentSearch;
use admin\modules\warehousemoving\models\FunctionWarehouse;
use admin\modules\warehousemoving\models\WarehouseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Items;

use admin\modules\tracking\models\FunctionTracking;

use common\models\SaleHeader;
use common\models\SaleLine;
use admin\models\Generater;

use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

/**
 * ShipmentController implements the CRUD actions for WarehouseMoving model.
 */
class ShipmentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'            => ['POST'],
                    'confirm-checklist' => ['POST'],
                    'undo-ship'         => ['POST'],
                    'shipped-line'      => ['POST'],
                    'load-data'          => ['POST'],
                    'confirm-stock'     => ['POST'],
                    'print'             => ['GET'],
                    'modify-shipment'    => ['POST'],
                    'get-shipment'    => ['POST'],
                    'print-transport-ajax'  => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all WarehouseMoving models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ShipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if(Yii::$app->request->isAjax) {

            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionLoadData(){        
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $raws           = [];
        $model          = SaleHeader::findOne($data->id);

        if (($model) !== null) {

            $query   = SaleLine::find()->where(['sourcedoc' => $model->id])->all();
            
            foreach ($query as $line) {

                $raws[] = [
                        'id'        => $line->id,
                        'orderid'   => $model->id,
                        'confirm'   => $model->confirm,
                        'order'     => $model->no,
                        //'custid'    => $model->customer->id,
                        //'custcode'  => $model->customer->code,
                        //'custname'  => $model->customer->name,
                        //'province'  => $model->customer->getAddress()['province'],
                        'item'      => $line->item,
                        'no'        => $line->items->No,
                        //'child'     => $this->getAssembly($line->items->ProductionBom,0),
                        'code'      => $line->items->master_code,
                        'desc_th'   => $line->items->description_th,
                        'desc_en'   => $line->items->Description,
                        'barcode'   => $line->items->barcode,
                        'unit'      => $line->items->UnitOfMeasure,
                        'stock'     => $line->items->hasbom != null 
                                            ? $line->items->invenByCache
                                            : $line->items->qtyAfter
                                        ,
                        //'inven'     => $line->items->inven,
                        'qty'       => $line->quantity,
                        //'qtyprint'  => ($line->items->inven <= $line->quantity) ?  $line->quantity - $line->items->inven : 0,
                        //'need'      => $line->quantity,
                        'img'       => $line->items->picture,
                        'comp_id'   => Yii::$app->session->get('Rules')['comp_id'],
                        'qty_per'   => $line->items->quantity_per_unit,
                        'status'    => true,
                        // 'text'      => [
                        //     'message' => Yii::t('common','done'),
                        //     'confirm' => Yii::t('common','Do you want to confirm ?'),
                        // ],
                        // 'type'      => $line->items->hasbom != null 
                        //                     ? 'Produce'
                        //                     : 'Purchase',
                ];

            }
            
        }else{
            $status = 404;
        }

        return json_encode([
            'status'    => $status,
            'raws'      => $raws,
            'id'        => $model->id,
            'confirm'   => (int)$model->confirm
        ]);

    }

    public function actionConfirmStock()
    {
        $JSON[] = [
            'status' => false,
            'text'      => [
                'message' => 'NULL'
            ]
        ];

        if(empty(Yii::$app->request->post('id'))){

            $JSON[] = [
                'status' => false,
                'text'      => [
                    'message' => 'Error'
                ]
            ];

            return json_encode($JSON);
        }

        $model = SaleHeader::findOne(Yii::$app->request->post('id'));

        if (($model) !== null) {

            // Clear status (Clear array)             
            unset($JSON);

            $query   = SaleLine::find()
                    ->where(['sourcedoc' => $model->id])
                    ->andWhere(['save_order' => 'saved']);
            
            if($query->exists()){
               
            
                foreach ($query->all() as $key => $line) {

                    $qtyPrint = 0;
                    // if($line->items->qtyAfter <= $line->quantity) $qtyPrint =  $line->quantity - $line->items->qtyAfter;
                    $Stock      = $line->items->ProductionBom > 0
                                    ? $line->items->last_possible
                                    : $line->items->last_stock;

                    $qtyPrint =  $Stock <= $line->quantity
                                    ? $line->quantity - $Stock
                                    : 0;


                    $JSON[] = [
                            'id'        => $line->id,
                            'orderid'   => $model->id,
                            'confirm'   => $model->confirm,
                            'order'     => $model->no,
                            //'custid'    => $model->customer->id,
                            //'custcode'  => $model->customer->code,
                            //'custname'  => $model->customer->name,
                            //'province'  => $model->customer->getAddress()['province'],
                            'no'        => $line->items->No,
                            //'child'     => $this->getAssembly($line->items->ProductionBom,0),
                            'code'      => $line->items->master_code,
                            'desc_th'   => $line->items->description_th,
                            'desc_en'   => $line->items->Description,
                            //'barcode'   => $line->items->barcode,
                            'unit'      => $line->items->UnitOfMeasure,
                            //'stock'     => $line->items->qtyAfter,
                            'stock'     => $line->items->last_stock,
                            //'inven'     => $line->items->invenByCache,
                            'inven'     => $Stock,
                            'qty'       => $line->quantity,
                            'qtyprint'  => $qtyPrint,
                            'need'      => $line->quantity,
                            'img'       => $line->items->picture,
                            'comp_id'   => Yii::$app->session->get('Rules')['comp_id'],
                            'qty_per'   => $line->items->quantity_per_unit,
                            'status'    => true,
                            'text'      => [
                                'message' => Yii::t('common','done'),
                                'confirm' => Yii::t('common','Do you want to confirm ?'),
                            ],
                            //'type'      => $line->items->replenishment,
                            'type'      => $line->items->ProductionBom > 0 
                                                ? 'Produce'
                                                : 'Purchase',
                    ];

                }

            }else {
                $JSON[] = [
                    'status' => false,
                    'text'      => [
                        'message'   => 'Error 404',
                        'alert'     => Yii::t('common','Please check sale order.'),
                    ]
                ];   
            }

            
            
        } else {

            $JSON[] = [
                'status' => false,
                'text'      => [
                    'message' => 'Error'
                ]
            ];   

        }
        //var_dump($model->id);
        return json_encode($JSON);

    }


    static function getAssembly($id,$loop){
        $Bomline    = \common\models\BomLine::find()->where(['bom_no' => $id])->all();
        $data = [];
        if($Bomline !== null){
            
            $maxLoop = 20;
            foreach ($Bomline as $key => $model) {
                $loop++;
                

                 $data[] = [
                     'id'       => $model->items->id,
                     'item'     => $model->item_no,
                     'code'     => $model->items->master_code,
                     'desc'     => $model->items->description_th,
                     'img'      => $model->items->picture,
                     'qty'      => $model->quantity,
                     'qtyprint' => $model->quantity,
                     'need'     => $model->quantity,
                     'unit'     => $model->measure, 
                     'stock'    => $model->items->invenByCache,
                     'child'    => ($loop > $maxLoop)? Yii::t('common','Error!').' '.Yii::t('common','Over').' '.$maxLoop.' '.Yii::t('common','Loop') : self::getAssembly($model->items->ProductionBom,$loop),
                     'status'   => ($loop > $maxLoop)? false : true,                   
                 ];
            }

        }else {

           $data = ['status' => false];

        }

        return $data;
    }

    public function actionShipline()
    {

        $model  = SaleHeader::findOne(Yii::$app->request->post('param')['id']);
        $query  = SaleLine::find();
        $query->where(['sourcedoc' => Yii::$app->request->post('param')['id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        // $dataProvider->pagination->pageSize=100;

        return $this->renderPartial('_shipline', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionShippedLine()
    {

        $model = WarehouseHeader::findOne($_POST['param']['id']);


        $query   = WarehouseMoving::find()
                    ->where(['source_id' => $_POST['param']['id']])
                    ->andWhere(['IN', 'TypeOfDocument', ['Sale','Undo-Sale']])
                    ->orderBy(['matching' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        // $dataProvider->pagination->pageSize=100;


        return $this->renderAjax('_shippedline', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionUndoShip()
    {
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $Fnc            = new FunctionWarehouse();
            $params         = Yii::$app->request->post('param');
            $source         = $params['id'];

            $model          = WarehouseHeader::findOne($source);
            // Check invoice before undo the document.
            if ($model->invoiced){
                
                $transaction->rollBack();
                return json_encode([
                    'status'        => 412,
                    'message'       => Yii::t('common','Invoiced'),
                    'suggestion'    => Yii::t('common','Please delete invoice "{:inv}" before undo this document',[
                            ':inv'  => Html::a($model->invoiced->no_,
                                        ($model->invoiced->status=='Open')
                                            ? ['/accounting/saleinvoice/update','id'    => ($model->invoiced->status=='Open')? $model->invoiced->id : base64_encode($model->invoiced->id)] 
                                            : ['/accounting/posted/posted-invoice','id' => ($model->invoiced->status=='Open')? $model->invoiced->id : base64_encode($model->invoiced->id)],
                                        ['target' => '_blank'])
                    ])
                ]);  
                exit();
            }


            $data           = [];
            foreach ($params['qtyshipped'] as  $value) {
                $data[]     = $Fnc->undoShip($model,$value['name'],$value['value']);  
            }

            $Undo           = $model->RevertProduction($model);
            
            $model->order_id= $model->SourceDocNo;
            $model->status  = 'Undo';
            

            if(!$model->save()){
                $transaction->rollBack();
                return json_encode([
                    'status' => 500,
                    'message' => Yii::t('common','Error'),
                    'suggestion' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                ]); 
            }


           

            FunctionTracking::CreateTracking(
            [
                'doc_type'          => 'Sale-Ship',
                'doc_id'            => $model->id,
                'doc_no'            => $model->DocumentNo,
                'doc_status'        => 'Undo',
                'amount'            => abs(WarehouseMoving::find()->where(['source_id' => $source])->sum('Quantity * unit_price')),
                'remark'            => 'Current : Shiped, Status : Undo',
                'track_for_table'   => 'sale_header',
                'track_for_id'      => $model->SourceDocNo,
            ]);

    
            $transaction->commit();

            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => $data,
            ]); 
          
        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
                'status' => 500,
                'message' => Yii::t('common','Error'),
                'suggestion' => Yii::t('common','{:e}',[':e' => $e])
            ]); 
            throw $e;
           
        }

        
    }
 
    /**
     * Displays a single WarehouseMoving model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new WarehouseMoving model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WarehouseMoving();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WarehouseMoving model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WarehouseMoving model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }



    public function actionPrintShip($id)
    {

        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = WarehouseHeader::findOne($id);

        // $searchModel = new WarehouseSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['source_id' => $model->id]);
        // $dataProvider->query->andwhere(['comp_id' => $company]);
        // $dataProvider->pagination->pageSize=false;
        $query  = WarehouseMoving::find()
                    ->where(['source_id' => $model->id])
                    ->andWhere(['comp_id' => $company]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);







        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('__print_ship',[
                    'model' => $model,
                    // 'searchModel' => $searchModel,
                      'dataProvider' => $dataProvider,

                ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            'cssFile' => 'css/pdf.css',

            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'filename' => 'transport_'.$model->DocumentNo.'.pdf',
            // any css to be embedded if required
            'cssInline' => '@page {margin: 0 60 0 60;} body{font-family: freesiaupc,freesia, sans-serif; font-size:20px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'transport : '.$model->DocumentNo.' '],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[''],
                //'SetFooter'=>['{PAGENO}'],
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


        //return $this->renderpartial('__print_sale_order');
       // return $this->renderpartial('__print_so');
    }

    public function actionPrintTransport($id)
    {

        $company        = Yii::$app->session->get('Rules')['comp_id'];

        // $model = WarehouseHeader::findOne($id);

        // $searchModel = new WarehouseSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['source_id' => $model->id]);
        // $dataProvider->query->andwhere(['comp_id' => $company]);
        // $dataProvider->pagination->pageSize=false;
        $model          = WarehouseHeader::findOne($id);
        $query          = WarehouseMoving::find()->where(['source_id' => $model->id]); 

        $Company        = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('__print_transport',[
                    'model' => $model,
                    'Company' => $Company,
                    //'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            //'cssFile' => 'css/pdf.css',
            'cssFile'   => '@admin/web/css/saraban.css',
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'filename' => 'transport_'.$model->DocumentNo.'.pdf',
            'cssInline' => 'body{ line-height: 1.5; }',
            // any css to be embedded if required
            //'cssInline' => '.bd{border:1.5px solid; text-align: center;} .ar{text-align:right} .imgbd{border:1px solid}',
            // set mPDF properties on the fly
            'options' => ['title' => 'transport : '.$model->DocumentNo.' '],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[''],
                //'SetFooter'=>['{PAGENO}'],
            ]
        ]);


        /* Thai Font */
        $defaultConfig      = (new ConfigVariables())->getDefaults();
        $fontDirs           = $defaultConfig['fontDir'];

        $defaultFontConfig  = (new FontVariables())->getDefaults();
        $fontData           = $defaultFontConfig['fontdata'];

        $pdf->options['fontDir']    = array_merge($fontDirs, [ Yii::getAlias('@webroot').'/fonts' ]);

        $pdf->options['fontdata']   = $fontData + [
            'saraban'       => ['R' => 'thsarabunnew-webfont.ttf'],
            'freesiaupc'    => ['R' => 'FreesiaUPC.ttf']
        ];

        return $pdf->render();

    }

    public function actionPrintTransportAjax()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = $data->id;

        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model          = WarehouseHeader::findOne($id);

        $query          = WarehouseMoving::find()->where(['source_id' => $model->id]); 


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        $content = $this->renderPartial('__print_transport',[
                    'model' => $model,
                    //'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            //'cssFile' => 'css/pdf.css',
            'cssFile'   => '@admin/web/css/saraban.css',
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'filename' => 'transport_'.$model->DocumentNo.'.pdf',
            'cssInline' => 'body{ line-height: 1.5; }',
            // any css to be embedded if required
            //'cssInline' => '.bd{border:1.5px solid; text-align: center;} .ar{text-align:right} .imgbd{border:1px solid}',
            // set mPDF properties on the fly
            'options' => ['title' => 'transport : '.$model->DocumentNo.' '],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[''],
                //'SetFooter'=>['{PAGENO}'],
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

        return json_encode([
            'pdf' => $pdf->render(),
            'status' => 200
        ]);
 
    }

    public function actionPrint($id)
    {
        
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $model      = WarehouseHeader::findOne($id);
        $query      = WarehouseMoving::find()->where(['source_id' => $model->id]); 

        $paper      = Yii::$app->request->get('paper')!= '' ? Yii::$app->request->get('paper') : 'A4';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        

        $template   = \common\models\PrintPage::findOne(\common\models\PrintPage::findCustomPrint('transport'));  
        $Company    = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

        $header = (Object)[
            'height'    => $template->header_height,
            'top'       => $template->margin_top,
            'fontsize'  => $template->font_size,
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
            'height' => $template->body_height,
            'pagesize' => $pageSize,
            'fontsize' => $template->font_size
        ];

        

 
        $Bahttext   = new \admin\models\FunctionBahttext();
    
        $defineHeader = [
            '{VALUE_TITLE}'         => $model->DocumentNo,
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 100px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_ID}'          => $Company->id.' '.$model->id,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $model->DocumentNo,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->PostingDate)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->PostingDate)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->PostingDate.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{HEAD_OFFICE}'         => $Company->headofficetb->data_char,                  
         
             
            '{CUSTOMER_CODE}'       => $model->customer->code,
            '{CUSTOMER_NAME}'       => $model->customer->name,
            '{CUSTOMER_PHONE}'      => $model->customer->phone,
            '{CUSTOMER_FAX}'        => $model->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->customer->fullAddress['address'],
            '{CUSTOMER_TAX}'        => $model->customer->vat_regis,       
            '{TRANSPORT_BY}'        => $model->customer->transport, 
          
          
            
            
         
             
             
        ]; 


 
        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme-gray',[
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'print'         => $template,
            'Company'       => $Company,
            'header'        => $header,
            'body'          => $body,
            'defineHeader'  => $defineHeader
        ]);
  
        $content = $this->renderPartial('_print_content',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
            'print' => $template,
            'body'  => $body
        ]);

  
        $pdf = new Pdf([
            'mode'          => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            'orientation'   => $template->paper_orientation,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            'filename'      => $model->DocumentNo.'.pdf',
            'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } ',
            'options'       => [
                'title' =>  $model->DocumentNo
            ],
            'methods'       => [
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

    /**
     * Finds the WarehouseMoving model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WarehouseMoving the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WarehouseMoving::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    public function actionConfirmChecklist($id){
        $model      = SaleHeader::findOne($id);
        $saleLine   = SaleLine::find()->where(['sourcedoc' => $model->id])->all();
        $qty        = 0;

        // Delete Consumption 
        // ถ้ามี Shipment อยู่แล้ว ให้ลบก่อน (?)

        foreach (Yii::$app->request->post('input') as $key => $line) {
            $conFirm                = SaleLine::findOne($line['name']);
            $conFirm->confirm       = $line['value'];                  // จำนวนที่ต้องการใน sale order
            $conFirm->confirm_by    = Yii::$app->user->identity->id;
            $conFirm->confirm_date  = date('Y-m-d H:i:s');
            $conFirm->produce       = $line['name'] == Yii::$app->request->post('output')[$key]['name'] 
                                        ? Yii::$app->request->post('output')[$key]['value'] 
                                        : 0;
            $conFirm->need_ship_date= $model->ship_date;
            $conFirm->stock_reserve = 0;
            
            if($conFirm->save()){

                $qty += $conFirm->confirm;               

                $production[] = (Object)[
                    'status' => 200,
                    'output' => self::producer($conFirm,$id,$conFirm->produce)
                ];
                
            }else {
                $production = (Object)[
                    'status' => 500,
                    'message'=> json_encode($conFirm->getErrors(),JSON_UNESCAPED_UNICODE)
                ];
            }
        }

        $model->confirm        = $qty;
        $model->confirm_by     = Yii::$app->user->identity->id;
        $model->confirm_date   = date('Y-m-d H:i:s');


        $data   = (Object)[
            'status'    => $model->save()? 200 : 500,
            'id'        => $model->id,
            'text'      => [
                'Success'   => 'Success',
                'Error'     => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ],             
            'production'=> $production
        ];
        
        return json_encode($data);
         
    }


    static function producer($list,$id,$qty){
        
        // ถ้ามีการลบ Items ทิ้ง  จะไม่ทำการ Output ใดๆ (ดังนั้น จะทำให้มีของใน Stock)

        $line   = \common\models\BomLine::find()->where(['bom_no' => $list->items->ProductionBom]);

        $data = [];
        // ถ้ามี Bom ให้ Output FG (+)              
        if($line->count() > 0){         
            /*
             * ถ้าจำนวนใน Bomline น้อยกว่า 0 จะไม่ต้อง Output
             *  
             */
            if($qty != 0){
                //Create Header 
                $GenSeries 				= new Generater();
                $Header                 = new WarehouseHeader();                 
                $SaleHeader 			= SaleHeader::findOne($id); 
        
                $Header->DocumentNo     = $GenSeries->GenerateNoseries('Production',true);
                $Header->PostingDate    = date('Y-m-d H:i:s');
                $Header->ship_date		= date('Y-m-d H:i:s');
                $Header->DocumentDate 	= date('Y-m-d');
                $Header->TypeOfDocument = 'Production';
        
                $Header->customer_id 	= $SaleHeader->customer_id;
                $Header->SourceDocNo 	= $SaleHeader->id;
                $Header->SourceDoc 		= $SaleHeader->no;
                $Header->order_id       = $SaleHeader->id;
                $Header->ship_to 		= $SaleHeader->customer_id;                                    
                $Header->Description 	= '';
                $Header->address 		= '';
                $Header->address2 		= '';
                $Header->district 		= '';
                $Header->city 			= '';
                $Header->province 		= '';
                $Header->postcode 		= '';
                $Header->status         = 'Produce';
        
                $Header->user_id 		= Yii::$app->user->identity->id;
                $Header->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $Header->line_no 		= $SaleHeader->id;      
                 
                
                if($Header->save()){

                    $output         = $Header->Output($list,(Object)['order_id' => $Header->order_id],$qty, $Header);

                    foreach ($line->all() as $key => $model) {                    
                        // ถ้ามี bom ย่อย ให้ไปหาอีก
                        // ถ้าไม่มี ให้ return item และ จำนวนที่ต้องใช้ กลับ
                        // เพื่อนำไปตัด consumption
                        if($model->items->ProductionBom > 0){

                            // ถ้ามี bom ย่อย ให้ไปหาอีก
                            $data[] = $Header->producer2($model, (Object)['order_id' => $id], $qty, $list);  

                            // Consumption ตัวเอง เพื่อประกอบเป็น Item หลัก
                            $consumption = $Header->Consumption($model,(Object)['order_id' => $Header->order_id],$output, $Header);
                            $data[] = [
                                'status'    => 200,      
                                'code'      => $model->items->master_code,
                                'id'        => $model->items->id,
                                'qty'       => $consumption->value->Quantity,                
                                'message'   => $consumption->message,
                            ];                     
                        }else {
                            // Consumption Item ย่อย
                            $consumption = $Header->Consumption($model,(Object)['order_id' => $Header->order_id],$output, $Header);
                            $data[] = [
                                'status'     => 200,     
                                'code'      => $model->items->master_code,
                                'id'        => $model->items->id,
                                'qty'       => $consumption->value->Quantity,            
                                'message'   => $consumption->message,
                            ];                        
                        }                  
                    }

                }

                $data[] = [
                    'status'    => 200, 
                    'code'      => $list->items->master_code,    
                    'id'        => $list->items->id,
                    'qty'       => $output->value->Quantity,                
                    'message'   => $output->message,
                ];
            }
        }
        return $data;       
    }

    public function actionGetShipment(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);


        $raw = [];
        $model  = WarehouseHeader::findOne($data->id);

        
        return json_encode([
            'status'        => 200,
            'id'            => $model->id,
            'ship_date'     => $model->DocumentDate,
            'phone'         => $model->phone?: $model->customer->phone,
            'Description'   => $model->Description,
            'transport_id'  => $model->transport_id,
            'ship_address'  => $model->ship_address
                                ? $model->ship_address
                                : ($model->customer->ship_address=='' 
                                    ? $model->customer->address
                                    : $model->customer->ship_address),
            'ship_name'     => $model->ship_name?:$model->customer->name
        ]);
    }

    public function actionModifyShipment(){        
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = '';

        $model                  = WarehouseHeader::findOne($data->id);
        $transport              = \common\models\TransportList::findOne(isset($data->transport) ? $data->transport : NULL);

        $model->transport_id    = $transport ? $transport->id : NULL;            
        $model->Description     = $transport ? $transport->name : NULL;
        $model->DocumentDate    = $data->shipDate;   
        $model->ship_name       = $data->ship_name;
        $model->ship_address    = $data->ship_address;
        $model->phone           = $data->ship_phone;

        if(!$model->save()){

            
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }else{
            // update customer
            $cust                   = \common\models\Customer::findOne($model->customer_id);
            if($cust!=null){                    
                $cust->ship_name            = $model->ship_name;
                $cust->ship_address         = $model->ship_address;
                $cust->transport            = $model->Description;
                $cust->default_transport    = $model->transport_id;
                $cust->save();
            }

            // update sale order 
            $order  = SaleHeader::findOne($model->order_id);
            if($order!=null){
                $order->transport       = $model->Description;
                $order->transport_id    = $model->transport_id;
                $order->save();
            }
        }
         
        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }
    
 
}
