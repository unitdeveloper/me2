<?php

namespace admin\modules\Purchase\controllers;

use Yii;
use yii\filters\AccessControl;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


use common\models\PurchaseHeader;
use admin\modules\Purchase\models\OrderSearch;
use admin\modules\Purchase\models\PurchaseLineRender;
use common\models\Items;
use common\models\PurchaseLine;
use admin\models\FunctionCenter;
use admin\models\Generater;
use common\models\PrintPage;
use common\models\Company;

use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use common\models\ItemMystore;

class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                                      'angular',
                                      'index',
                                      'indexs',
                                      'create',
                                      'update',
                                      'view',
                                      'delete',
                                      'print-po',
                                      'json-create-item-line',
                                      'ajax-delete-pur-line',
                                      'print',
                                      'print-options',
                                      'print-editable',
                                      'print-editable-index',
                                      'ajax-total',
                                      'receive',
                                      'get-source',
                                      'get-receive',
                                      'view-ajax',
                                      'update-field',
                                      'received-list',
                                      'search'
                                    ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'ajax-delete-pur-line'  => ['post'],
                    'json-create-item-line' => ['post'],
                    'get-source'    => ['post'],
                    'view-ajax'     => ['POST'],
                    'received-list' => ['POST'],
                    'search'        => ['POST']
                ],
            ],
        ];

    }

    /**
     * Lists all PurchaseHeader models.
     * @return mixed
     */

    public function actionIndexs()
    {
        return $this->render('loading');        
    }
    public function actionIndex()
    {
        $searchModel    = new OrderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        
        //var_dump($searchModel->search);
        $dataProvider->query->andWhere(['purchase_header.status' => 10]);
        $dataProvider->query->andWhere(['purchase_header.deletion' => 0]);        
          
        $cond           = $searchModel->search ?: '';  

        $wait = new ActiveDataProvider([
            'query' => PurchaseHeader::find()->joinWith(['vendors'])->where([
                            'purchase_header.status' => 0,
                            'purchase_header.comp_id' => Yii::$app->session->get('Rules')['comp_id'],
                            'purchase_header.deletion' => 0
                        ]),
            'pagination' => ['pageSize' => 20],
            'sort'  => ['defaultOrder' => ['order_date'=>SORT_DESC]],
        ]);
        $wait->query->andWhere(['or',
                                ['like', 'purchase_header.doc_no', $cond],
                                ['like', 'vendors.name', $cond],
                                ['like', 'purchase_header.ref_no', $cond]
        ]);

        $work = new ActiveDataProvider([
            'query' => PurchaseHeader::find()->joinWith(['vendors'])->where([
                'purchase_header.status' => 1,
                'purchase_header.comp_id' => Yii::$app->session->get('Rules')['comp_id'],
                'purchase_header.deletion' => 0
            ]),
            'pagination' => ['pageSize' => 20],
            'sort'=> ['defaultOrder' => ['order_date'=>SORT_DESC]],
        ]);
        $work->query->andWhere(['or',
                                ['like', 'purchase_header.doc_no', $cond],
                                ['like', 'vendors.name', $cond],
                                ['like', 'purchase_header.ref_no', $cond]
        ]);        

        return $this->render('index', [
            'searchModel'   => $searchModel,
            'work'          => $work,
            'wait'          => $wait,
            'dataProvider'  => $dataProvider,
            'search'        => $cond
        ]);
    }

    public function actionPrintEditableIndex()
    {
        $query = PrintPage::find()
        ->where(['module_group' => 'purchase'])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if($query->count() <= 0){
            $query = PrintPage::find()
            ->where(['module_group' => 'purchase'])
            ->andWhere(['comp_id' => 1]);
        }
  
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['module'=>SORT_ASC]],
        ]);
  
        return $this->render('_print_editable_index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PurchaseHeader model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViewAjax()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $model      = $this->findModel($data->id);
        $receive    = $model->receive;

        $Received   = [];
        foreach ($receive as $key => $rc) {
            $Received[] = [
                'id'        => $rc->id,
                'no'        => $rc->DocumentNo,
                'ext_doc'   => $rc->ext_document
            ];
        }

        return json_encode([
            'status' => 200,
            'id'        => $model->id,
            'rc'        => $Received,
            'pay_inv'   => [],
            'payment'   => []
        ]);
    }

    /**
     * Creates a new PurchaseHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    protected function findEmpty()
	{
		$model = PurchaseHeader::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->andWhere(['balance' => 0])
		->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere("MONTH(create_date) = '".date('m')."' ")
        ->andWhere(['status' => 'Open'])
        ->andWhere(['deletion' => 0])
		->One();
		return $model;
	}
    public function actionCreate()
    {
        
        /*
        $Free   = $this->findEmpty();
        if(isset($Free->id)){
            Yii::$app->getSession()->addFlash('info','Already'); 
            return $this->redirect(['update', 'id' => $Free->id]);
            exit;
        }
        */

        $model = new PurchaseHeader();
        
        $model->vendor_id       = 1;
        $model->vendor_name     = 'เงินสด';
        $model->doc_no          = Generater::getRuning('purchase_header','vatbus_posting_group','01');
        $model->order_date      = date('Y-m-d');
        $model->create_date     = date('Y-m-d H:i:s');
        $model->delivery_date   = date('Y-m-d');
        $model->percent_discount= '';
        $model->include_vat     = '1';
        $model->session_id      = Yii::$app->session->getId();
        $model->user_id         = Yii::$app->user->identity->id;
        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

        if($model->save()){
            $UpdateSeries       = Generater::UpdateSeries('purchase_header','vatbus_posting_group','01',$model->doc_no);
            //Yii::$app->getSession()->addFlash('success','Created'); 
            return $this->redirect(['update', 'id' => $model->id]);             
        }else{
        
          
            Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
            return $this->redirect(['index', 'id' => $model->id]);   
        }


        
        
    }

    /**
     * Updates an existing PurchaseHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->deletion == 1){
            Yii::$app->getSession()->addFlash('warning','<i class="fas fa-exclamation-triangle"></i> '.Yii::t('common','Not Allow')); 
            return $this->redirect(['index']);
            exit;
        }

        $searchModel    = new PurchaseLineRender();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['source_id' => $id]);
        $dataProvider->query->orderBy(['priority' => SORT_ASC]);

        if(Yii::$app->request->post('ajax')){
            /**
            * Update Purchase Line
            *
            */

            $data   = Yii::$app->request->post('data');
            $field  = (string)Yii::$app->request->post('name');
            $Line   = PurchaseLine::findOne(Yii::$app->request->post('key'));
            if($Line->received->complete){
                return json_encode([
                    'status' => 500,
                    'message' => 'You can\'t change the received line.',
                    'id' => $Line->id
                ]);
                exit();
            }
            $old                = $Line->$field;
            $Line->vendor_id    = $model->vendor_id;
            $Line->$field       = $data;
            $Line->source_id    = $id;

            


            if($_POST['name']=='unit_of_measure'){
                $measure = \common\models\Itemunitofmeasure::findOne(['measure' => $_POST['data'],'item' => $Line->item,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                $Line->quantity_per_unit = $measure->qty_per_unit;
            }
            

            if($Line->save()){

                if($model->status != 0){
                    try{
                        // Line Notify
                        $bot =  \common\models\LineBot::findOne(4);
                        $msg = 'Line'."\r\n";
                        $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                        $msg.= 'PO : '.$model->no."\r\n\r\n";
    
                        $msg.= 'Item : '.$Line->items->master_code."\r\n";
                        $msg.= 'Field : '.$field."\r\n";
                        $msg.= $old.' --> '.$data."\r\n\r\n";
    
                        $msg.= 'Balance : '.number_format($model->total->total,2)."\r\n\r\n";
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
    
                        $bot->notify_message($msg);					
    
                    } catch (\Exception $e) {					 
                        Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                    }
                }


                return json_encode([
                    'status' => 200,
                    'recive' => $Line->received->complete,
                    'message' => Yii::t('common','Done'),
                    'header' => [
                        'status' => $model->status
                    ],
                    'id' => $Line->id
                ]);
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => 'error',
                    'suggestion' => json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE),
                    'id' => $Line->id
                ]);
            }


        }


        if ($model->load(Yii::$app->request->post())) {
            /*
            * Validate new vendor.
            * Automatic create customer when get from customer table.
            * Validate by refer_id
            */      
            $transaction = Yii::$app->db->beginTransaction();
		    try {

                   
                if($model->vendor_id==0){                
                    $model->vendor_id = $model->createVendor();              
                }
                $model->session_id  = Yii::$app->session->getId();
                $model->user_id     = Yii::$app->user->identity->id;
                $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
                $model->balance     = $model->total->total;
                $model->status      = 1;
                // update line
                PurchaseLine::updateAll([
                    'source_no' => $model->doc_no, 
                    'vendor_id' => $model->vendor_id
                ], ['source_id' => $model->id]);
                
                $model->save();   
                
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->addFlash('warning',json_encode($e,JSON_UNESCAPED_UNICODE)); 
                return $this->render('update', [
                    'model'           => $model,
                    'searchModel'     => $searchModel,
                    'dataProvider'    => $dataProvider,
                ]);
                throw $e;
            }   
            Yii::$app->getSession()->addFlash('success',Yii::t('common','Success')); 
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->status      = 0;
            return $this->render('update', [
                'model'         => $model,
                'searchModel'   => $searchModel,
                'dataProvider'  => $dataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing PurchaseHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(in_array($model->status,['0'])){
            # Allow Only status Open
            # 1. Delete Purchase Line
            # 2. Delete Purchase Header
            $transaction = Yii::$app->db->beginTransaction();
		    try {
                if(PurchaseLine::find()->where(['source_id' => $id])->exists()){
                    # Exists Purchase Line
                    if(PurchaseLine::deleteAll(['source_id' => $id])){
                        # Delete Purchase Line                        
                        if($model->delete()){                         
                           # Delete Purchase Header
                            $transaction->commit();                              
                        }else {
                            # Error Delete Purchase Header
                            $transaction->rollBack();
                        }
                    }else {
                        # Error Delete Purchase Line
                        $transaction->rollBack();
                    }
                }else {
                    // Empty Purchase Line
                    if($model->delete()){
                        # Delete Purchase Header
                        $transaction->commit();
                        Yii::$app->getSession()->addFlash('warning','Deleted'); 
                    }else {
                        # Error Delete Sale Header
                        $transaction->rollBack();                        
                    }
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->addFlash('warning',json_encode($e,JSON_UNESCAPED_UNICODE)); 
                throw $e;
            }
        }else{
            $transaction = Yii::$app->db->beginTransaction();
		    try {
                $model->deletion    = 1;
                $model->delete_by   = Yii::$app->user->identity->id;
                $model->save();
                Yii::$app->getSession()->addFlash('warning','Deleted'); 
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->addFlash('warning',json_encode($e,JSON_UNESCAPED_UNICODE)); 
                throw $e;
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the PurchaseHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchaseHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchaseHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionPrintEditable($page){


        $model = PrintPage::findOne(['id' => PrintPage::findCustomPrint($page)]);

        if ($model->load(Yii::$app->request->post())) {

            // ถ้าปรับค่าให้สร้างใหม่เป็นของตัวเอง
            if($model->comp_id === (int)Yii::$app->session->get('Rules')['comp_id']){
                $model->water_mark_img    = $model->upload($model,'water_mark_img');
                $model->save();
            }else{
                $model = $model->clonePurchasePrint($model);                
            }
            
            return $this->render('_print_options',[
                'model' => $model
            ]);
        }else{
            return $this->render('_print_options',[
                'model' => $model
            ]);
        }

      
    }


    public function actionPrintOptions(){

        
        $model = PrintPage::findOne(['id' => PrintPage::findCustomPrint('purchase_header')]);

        if ($model->load(Yii::$app->request->post())) {

            // ถ้าปรับค่าให้สร้างใหม่เป็นของตัวเอง
            if($model->comp_id === (int)Yii::$app->session->get('Rules')['comp_id']){
                $model->water_mark_img    = $model->upload($model,'water_mark_img');
                $model->save();
            }else{
                $model = $model->clonePurchasePrint($model);                
            }
            
            return $this->render('_print_options',[
                'model' => $model
            ]);
        }else{
            return $this->render('_print_options',[
                'model' => $model
            ]);
        }

      
    }

    

    public function actionAjaxTotal($id){

      $model = PurchaseHeader::findOne($id);

      $JSON = [
        'total'             => '',
        'discount'          => '',
        'percentDiscount'   => '',
        'subtotal'          => '',
        'beforeVat'         => 0,
        'vat'               => '0',
        'vat_type'          => '0',
        'include_vat'       => '1',
        'grandtotal'        => '',
        'withholdTax'       => '0',
        'transport'         => 0,
        'vatValue'          => 0,
        'terms'             => '0'
      ];

      if (($model) !== null) {

          $JSON = [
            'total'           => $model->total->beforediscount,
            'discount'        => $model->total->discount,
            'percentDiscount' => $model->percent_discount,
            'subtotal'        => $model->total->subtotal,
            'beforeVat'       => $model->total->beforeVat,
            'vat'             => (string)abs($model->vat_percent),
            'vat_type'        => (string)abs($model->vat_type),
            'include_vat'     => (string)$model->include_vat,
            'grandtotal'      => $model->total->total,
            'withholdTax'     => $model->withholdTax,
            'transport'       => $model->transport,
            'vatValue'        => $model->total->includevat,
            'terms'             => $model->payment_term
          ];

      }

      return json_encode($JSON);
    }

 

    public function actionPrint($id){



        $model    = $this->findModel($id);

        $query    = PurchaseLine::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $countRow = $query->count();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['priority'=>SORT_ASC]],
        ]);

        $template   = PrintPage::findOne(PrintPage::findCustomPrint('purchase_header'));
        $Company    = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

      
        $header = (Object)[
            'height'    => $template->header_height,
            'top'       => $template->margin_top,
            'fontsize'  => $template->font_size,
            'title'     => $model->doc_no,
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
        $term       = date('Y-m-d',strtotime(date('Y-m-d'). " +  $model->payment_term Days"));

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
            '{DOCUMENT_NO}'         => $countRow > 0 
                                            ? ($model->rev_no != '' ? ($model->doc_no.'_'.$model->rev_no) : $model->doc_no )
                                            : '',
            '{REV_NO}'              => $model->rev_no != '' ? $model->rev_no : '',
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => $countRow > 0 ? date('d/m/y',strtotime($model->order_date)) : '',
            '{ORDER_DATE_TH}'       => $countRow > 0 ? date('d/m/y',strtotime($model->order_date.' + 543 Years')) : '',
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,
            '{PAGE_TITLE}'          => 'PO : '.$model->doc_no,
        
            '{APPLY_FOR}'           => $model->detail,
            '{CREATER}'             => $model->purchaser,
        
            '{VENDOR_CODE}'         => $countRow > 0 ? $model->vendor->code : '',
            '{VENDOR_NAME}'         => $countRow > 0 ? $model->vendor->name : '',
            '{VENDOR_ADDRESS}'      => $model->vendor->address,
            '{VENDOR_TAX}'          => $model->vendor->vat_regis,
            '{VENDOR_PHONE}'        => $model->vendor->phone,
            '{VENDOR_FAX}'          => $model->vendor->fax,
        
            '{REF_NO}'              => $model->ref_no,
            '{REF_TO}'              => $model->ext_document,
            '{PURCHASE_REQUEST}'    => $model->ref_pr,
            '{PAYMENT_TERM}'        => $model->payment_term > 0 
                                        ? $model->payment_term . ' ' . Yii::t('common','Day')
                                        : Yii::t('common','Cash'),
            '{REF_TERM_OF_PAYMENT}' => $countRow > 0 
                                        ? $model->payment_term > 0 
                                            ?  $term . ' ' . Yii::t('common','Day')
                                            : Yii::t('common','Cash')
                                        : '',

            '{REF_TERM_OF_PAYMENT_TH}' => $countRow > 0 
                                            ? $model->payment_term > 0 
                                                ? date('d/m/y',strtotime(($model->payment_due != '' ? $model->payment_due : $term).' + 543 Years'))
                                                : Yii::t('common','Cash')
                                            : '',
            '{DELIVERY_SCHEDULE}'   => $countRow > 0 ? $model->delivery_date : '<p style="color:#fff;">00</p>',
            '{DELIVERY_SCHEDULE_TH}'=> $model->delivery_date ? date('d/m/y',strtotime($model->delivery_date.' + 543 Years')) : '',
            '{DELIVERY_ADDRESS}'    => $model->delivery_address,
        
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => $model->remark, //wordwrap($model->remark, 350, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->total->beforediscount,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->total->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->total->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => ($model->vat_percent * 1).' %',
            '{VALUE_INCLUDEVAT}'    => number_format($model->total->includevat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->total->total,2),
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht($model->total->total - (($model->withholdTax * $model->total->subtotal)/100)),

            '<!--IF_WHT-->'         => ($model->withholdTaxSwitch===1)? ' ': '<!--',
            '<!--IF_WHT_END-->'     => ($model->withholdTaxSwitch===1)? ' ': '-->',
            '<!--IF_NOT_WHT-->'     => $model->withholdTax <= 0 ? ' ': '<!--',
            '<!--IF_NOT_WHT_END-->' => $model->withholdTax <= 0 ? ' ': '-->',

            '{LABEL_TRANSPORT}'     => 'ค่าขนส่ง',
            '{VALUE_TRANSPORT}'     => number_format($model->transport,2),
            '<!--IF_TR-->'          => ($model->transport != 0)? ' ': '<!--',
            '<!--IF_TR_END-->'      => ($model->transport != 0)? ' ': '-->',
            '{VALUE_TRANSPORT_ROW}' => ($model->transport != 0)? 5: 4,

            '{LABEL_WHT_PERCENT}'   => 'หักภาษี ณ ที่จ่าย',
            '{VALUE_WHT_PERCENT}'   => $model->withholdTax * 1,
            '{VALUE_WHT}'           => number_format(($model->withholdTax * $model->total->subtotal)/100, 2),
            '{LABEL_TOTAL_WHT}'     => 'ยอดชำระ',
            '{VALUE_TOTAL_WHT}'     => number_format($model->total->total - (($model->withholdTax * $model->total->subtotal)/100), 2)
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
            'filename'    => $model->doc_no.'.pdf',
            'cssInline'   => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px;}',
            'options'     => [
                'title'             => 'PO : '.$model->doc_no,
                'autoScriptToLang'  => true, 
                'autoLangToFont'    => true,
                'languageToFont'    => new CustomLanguageToFontImplementation(), //แสดงภาษาจีน และภาษาไทย โดยกำหนด Font-family เอง
            ],           
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

    public function actionJsonCreateItemLine()
    {

        $Item  = ItemMystore::findOne(['item' => Yii::$app->request->post('itemId'), 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if($Item===null){
            $Item  = ItemMystore::findOne(['barcode' => Yii::$app->request->post('item'), 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        }

        $model      = new PurchaseLine();
        

        if(isset($_POST['code'])) 	{
            $Item               = ItemMystore::findOne(['item' => Yii::$app->request->post('code'), 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            //$Item->master_code  = Yii::$app->request->post('item_no');
        }
        
        if($Item===null){
            $Item  = ItemMystore::findOne(['item_no' => Yii::$app->request->post('item'), 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            //$Item->master_code  = Yii::$app->request->post('item_no');
        }

        $lastPrice  = $model->getLastprice($Item->id);

        $model->source_id   = 0;
        $model->source_no   = 0;
        $model->type        = 'Item';
        $model->vendor_id   = 0;
        $model->item        = $Item ? $Item->item : 1414;
        $model->items_no    = $Item ? $Item->master_code : '';
        $model->description = $Item ? $Item->name. ' ' . $Item->detail . ' ' . $Item->size : ' ';
        $model->location    = $model->defaultlocation;
        $model->quantity    = 0;
        $model->unitcost    = Yii::$app->request->post('price') > 0 ? Yii::$app->request->post('price') : $lastPrice;


        if(isset($_POST['desc']))   $model->description = $_POST['desc']. ' ' .$_POST['detail']. ' ' .$_POST['size'];
        if(isset($_POST['id']))     $model->source_id   = $_POST['id'];
        if(isset($_POST['no']))     $model->source_no   = $_POST['no'];
        if(isset($_POST['type']))   $model->type        = $_POST['type'];
        if(isset($_POST['qty']))    $model->quantity    = ($_POST['qty'])? $_POST['qty'] : 0;

        // if(isset($_POST['price']))  $model->unitcost    = ($_POST['price'])
        //                                                     ? $_POST['price'] > 0
        //                                                         ? $_POST['price']
        //                                                         : $lastPrice
        //                                                     : 0;


        
        $measureList = [];
        if($Item != null){
       
            foreach ( $Item->items->itemunitofmeasures as $key => $value) {
                $measureList[] = [
                    'id'        => (string)$value->measure,
                    'name'      => $value->measures->UnitCode,
                    'qty_per'   => $value->qty_per_unit * 1,
                ];           
            }
        }
        
       
        $model->unit_of_measure     = $Item ? $Item->unit_of_measure : '';
        $model->quantity_per_unit   = $Item ? $Item->items->quantity_per_unit : '';
        $model->lineamount          = $model->quantity * $model->unitcost;
        $Priority                   = PurchaseLine::find()->select('max(priority) as priority')->where(['source_id' => $_POST['id']])->one();
        $model->priority            = $Priority->priority +1;

        $model->user_id             = Yii::$app->user->identity->id;
        $model->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

        

        if($model->save(false)){

            return json_encode([
                'id'        => $model->id,
                'item'      => $model->item,
                'item_no'   => (string)$model->items->master_code,
                'desc'      => $model->description,
                'qty'       => $model->quantity,
                'price'     => $model->unitcost,
                'location'  => $model->location,
                'measure'   => (string)$model->unit_of_measure,
                'unitofmeasure' => $measureList,
                'qty_per_unit'  => $model->items->quantity_per_unit,
                'size'      => $model->items->size ? $model->items->size : '',
                'detail'    => $model->items->detail ? $model->items->detail : ''
            ]);
        }else {

            print_r($model->getErrors());
            exit();

        }



    }

    public function actionAjaxDeletePurLine(){
        $model = PurchaseLine::find()
        ->where(['source_id' => $_POST['pur']])
        ->andWhere(['id' => $_POST['data']])
        ->one();

         
        if($model->received->complete){
            
            return json_encode([
                'status' => 404,
                'message' => 'You can\'t delete the received line.'
            ]);
             
        }else{ // ถ้ายังไม่ได้ Receive
            if($model){
                if($model->delete()){
                    
                    return json_encode([
                        'status' => 200,
                        'message' => 'Removed'
                    ]);
                    

                }else{
                    return json_encode([
                        'status' => 500,
                        'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);
                }
            }else {
                return json_encode([
                    'status' => 404,
                    'message' => 'No data'
                ]);
                // Yii::$app->getSession()->setFlash('alert',[
                //     'body'=>'<i class="fa fa-times-circle text-red"></i> '.json_encode($model,JSON_UNESCAPED_UNICODE),
                //     'options'=>['class'=>'bg-danger']
                // ]);
                // return $this->redirect(['update','id' => $_POST['pur']]);
            }
        }

    }


    public function actionReceive($id){
        $model  = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            $location = Yii::$app->request->post('location');
            $quantity = Yii::$app->request->post('quantity');

           
            // ไม่อนุญาตให้รับเพิ่ม
            //if(!$model->completeReceive){
            
                $transaction = Yii::$app->db->beginTransaction();
                try {                
                    $header = $model->genReceiveHeader($model);
                    if($header !==null){                    
                        foreach ($location as $key => $value) {
                            if(in_array($key,Yii::$app->request->post('receive'))){                           
                                if($quantity[$key] != 0){                                 
                                    $Line = PurchaseLine::findOne($key);
                                    $Line->location = (int)$value;                                             
                                    if($Line->save(false)){
                                        $model->genReceive($header,$Line,$quantity[$key]);
                                    }else{   
                                        //$transaction->rollBack();    
                                        Yii::$app->getSession()->addFlash('warning','{:e}', [':e' => $Line->getErrors()]);                              
                                        //var_dump($Line->getErrors()); exit();                                    
                                    }                                
                                }
                            }                        
                        } 
                        $model->status = 1;
                        $model->save();
                        $transaction->commit(); 
                        return $this->redirect(['/warehousemoving/receive/view','id' => $header->id,'po' => $model->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

            // }else{
            //     $model->status = 10;
            //     $model->save();
            //     Yii::$app->getSession()->addFlash('warning','Completely Received'); 
            // }
            
        }
        
        $searchModel = new PurchaseLineRender();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['source_id' => $id]);
        $dataProvider->query->orderBy(['priority' => SORT_ASC]);
        $dataProvider->pagination=false;

        return $this->render('receive', [
            'model'         => $model,
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
        ]);

    }

    
    

    public function actionGetSource($id){

        $header = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        try {  
            foreach (Yii::$app->request->post('id') as $key => $lineId) {
                $line = \common\models\PurchaseReqLine::findOne($lineId);
            
                
    
                    $model = new PurchaseLine();
                    $model->source_id       = $header->id;
                    $model->source_no       = $header->doc_no;
                    $model->type            = 'item';
                    $model->item            = $line->item;
                    $model->items_no        = $line->items_no;
                    $model->description     = $line->description;
                    $model->location        = $line->location;
                    $model->quantity        = $line->quantity;
                    $model->unit_of_measure = $line->unit_of_measure;
                    $model->quantity_per_unit= $line->quantity_per_unit;
                    $model->unitcost        = $line->unitcost;
                    $model->lineamount      = $line->lineamount;
                    $model->linediscount    = $line->linediscount;
                    $model->expected_date   = $header->delivery_date;
                    $model->planned_date    = $line->planned_date;
                    $model->priority        = $line->priority;
                    $model->ref_line        = $line->id;
                    $model->user_id         = Yii::$app->user->identity->id;
                    $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

                    if(!$model->save()){
                        var_dump($model->getErrors());
                        $transaction->rollBack();
                        exit();
                    }
                    $ids[] = $model->id;
            }

            $transaction->commit();
            $status = 200;

        } catch (\Exception $e) {
            $status = 404;
            $transaction->rollBack();
            throw $e;  
        }
        return json_encode([
            'status' => $status,
            'id' => Yii::$app->request->post('id'),
            'ids' => $ids
        ]);
    }

    public function actionGetReceive($id){
        $models = WarehouseHeader::find()
        ->where(['TypeOfDocument' => 'Purchase'])
        ->andWhere(['SourceDocNo' => $id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->orderBy(['PostingDate' => SORT_DESC]);

        $header           = [];
        
        if($models->count() > 0){
            foreach ($models->all() as $key => $model) {

                $lines = WarehouseMoving::find()->where(['source_id' => $model->id])->all();
                $receive_line   = [];
                foreach ($lines as $key => $line) {
                    if($line->item==1414 && $line->Quantity < 0){

                    }else{
                        $receive_line[] = [
                            'id'        => $line->id,
                            'item'      => $line->item,
                            'item_no'   => ($line->item==1414)? $line->ItemNo : $line->items->master_code,
                            'desc'      => $line->Description,
                            'qty'       => $line->Quantity,
                            'qty_per'   => $line->qty_per_unit,
                            'price'     => $line->unit_price,
                            'measure'   => $line->unitofmeasures->UnitCode,
                            'type'      => $line->TypeOfDocument,
                            'img'       => $line->items->picture
                        ];
                    }
                }

                $header[] = [
                    'id'        => $model->id,
                    'no'        => $model->DocumentNo,
                    'date'      => $model->PostingDate,
                    'status'    => $model->status,
                    'line'      => $receive_line
                ];
            }

            return json_encode([
                'status' => 200,
                'header'  => $header
            ]);
        }else{
            return json_encode([
                'status' => 404
            ]);
        }
    }

    public function actionUpdateField(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $field          = $data->field;
        $status         = 200;
        $message        = Yii::t('common','Success');

        

        $model          = PurchaseHeader::findOne($data->id);
        $model->$field  = $data->value;

        if(!$model->save()){
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function actionReceivedList(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

         
        $status         = 200;
        $message        = Yii::t('common','Success');

        $Purchase       = PurchaseLine::findOne($data->id);
        $query          = WarehouseMoving::find()
                        ->joinWith('header')
                        ->where(['warehouse_moving.item' => $Purchase->item])
                        ->andWhere(['warehouse_header.purchase_id' => $Purchase->source_id])
                        ->all();
         $raws          = [];
        foreach ($query as $key => $model) {

            $raws[] = [
                'id'    => $model->header->id,
                'date'  => date('Y-m-d',strtotime($model->PostingDate)),
                'doc'   => $model->DocumentNo,
                'code'  => $model->items->master_code,
                'item'  => $model->item,
                'name'  => $model->Description,
                'qty'   => $model->Quantity,
                'stock' => $model->qty_after,
                'ref'   => $model->header->ext_document,
                'remark'=> $model->header->remark
            ];
        }


        

        return json_encode([
            'status' => $status,
            'message' => $message,
            'raws' => $raws
        ]);
    }

    public function actionSearch(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

         
        $status         = 200;
        $message        = Yii::t('common','Success');
        $raws          = [];

        if($data->search != ''){

            $Purchase       = PurchaseHeader::find()
                            ->where([
                                'or', ['like', 'doc_no',$data->search]
                            ])
                            ->andWhere(['deletion' => 0])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->orderBy(['id' => SORT_DESC])
                            ->all();
            
            
            foreach ($Purchase as $key => $model) {

                $raws[] = [
                    'id'        => $model->id,
                    'date'      => date('Y-m-d',strtotime($model->order_date)),
                    'no'        => $model->doc_no,
                    'name'      => $model->vendors->name,
                    'ref'       => $model->ext_document,
                    'remark'    => $model->remark,
                    'complete'  => $model->completeReceive,
                    'received'  => $model->received
                ];
            }

        }
        

        return json_encode([
            'status' => $status,
            'message' => $message,
            'raws' => $raws
        ]);
    }
}

class CustomLanguageToFontImplementation extends \Mpdf\Language\LanguageToFont{

    public function getLanguageOptions($llcc, $adobeCJK)
    {
        if ($llcc === 'th') {
            return [false, 'saraban']; // for thai language, font is not core suitable and the font is Frutiger
        }

        return parent::getLanguageOptions($llcc, $adobeCJK);
    }

}