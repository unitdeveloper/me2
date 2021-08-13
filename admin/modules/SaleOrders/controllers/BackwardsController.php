<?php
namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\SaleorderSearch;
use common\models\Customer;
use common\models\Items;
use common\models\SaleHeader;
use common\models\SaleLine;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use admin\models\Generater;
use yii\web\UploadedFile;
 


class BackwardsController extends \yii\web\Controller
{
 
    public function beforeAction($action)
    {
        if ($action->id == 'finished') {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }
    
        return parent::beforeAction($action);
    }
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'     => VerbFilter::className(),
                'actions'   => [
                    'delete'            => ['POST'],
                    'find-customer'     => ['POST'],
                    'find-items'        => ['POST'],
                    'load-data'         => ['POST'],
                    'create-sale-line'  => ['POST'],
                    'finished'          => ['POST']                    
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SaleorderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['rc_invoice_header.extra' => 'backward']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView()
    {
        return $this->render('view');
    }



    public function actionCreate()
    {
        $model          = new SaleHeader();
        $text           = '';
        $countPages     = 1;

        if ($model->load(Yii::$app->request->post())) {
 
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

            if(isset($model->pdfFile)){
                
                if($model->pdfFile->size > 0){

                    $pdf = new \TonchikTm\PdfToHtml\Pdf($model->pdfFile->tempName, [
                        //'pdftohtml_path'    => '/usr/local/bin/pdftohtml',  // When Mac OS X
                        //'pdfinfo_path'      => '/usr/local/bin/pdfinfo',    // When Mac OS X                    
                        'generate' => [     // settings for generating html
                            'singlePage'    => false,    // we want separate pages
                            'imageJpeg'     => false,   // we want png image
                            'ignoreImages'  => false,   // we need images
                            'zoom'          => 1.50,     // scale pdf
                            'noFrames'      => true,    // we want separate pages
                        ],
                        'clearAfter'        => true,    // auto clear output dir (if removeOutputDir==false then output dir will remain)
                        'removeOutputDir'   => true,    // remove output dir
                        'outputDir'         => Yii::getAlias('@webroot').'/uploads/file/temp', // output dir
                        'html' => [         // settings for processing html
                            'inlineCss'     => true,    // replaces css classes to inline css rules
                            'inlineImages'  => true,   // looks for images in html and replaces the src attribute to base64 hash
                            'onlyContent'   => true,    // takes from html body content only
                                
                        ]
                    ]);
                    
                    // get pdf info
                    $pdfInfo = $pdf->getInfo();
                    
                    // get count pages
                    $countPages = $pdf->countPages();
                    
                    // get content from one page
                    $contentFirstPage = $pdf->getHtml()->getPage(1);
                    
                    // get content from all pages and loop for they
                    foreach ($pdf->getHtml()->getAllPages() as $page) {
                        $text.= $page . '<br/>';
                    }                    
                }

            }
        }

        return $this->render('create',[
            'model' => $model,
            'text' => $text,
            'page' => $countPages
        ]);
         
    }

    public function actionUpdate()
    {
        $model          = new SaleHeader();
        $text           = '';
        $countPages     = 1;

        if ($model->load(Yii::$app->request->post())) {
 
            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

            if(isset($model->pdfFile)){
                
                if($model->pdfFile->size > 0){

                    $pdf = new \TonchikTm\PdfToHtml\Pdf($model->pdfFile->tempName, [
                        //  'pdftohtml_path'    => '/usr/local/bin/pdftohtml',  // When Mac OS X
                        //  'pdfinfo_path'      => '/usr/local/bin/pdfinfo',    // When Mac OS X                    
                        'generate' => [     // settings for generating html
                            'singlePage'    => false,    // we want separate pages
                            'imageJpeg'     => false,   // we want png image
                            'ignoreImages'  => false,   // we need images
                            'zoom'          => 1.50,     // scale pdf
                            'noFrames'      => true,    // we want separate pages
                        ],
                        'clearAfter'        => true,    // auto clear output dir (if removeOutputDir==false then output dir will remain)
                        'removeOutputDir'   => true,    // remove output dir
                        'outputDir'         => Yii::getAlias('@webroot').'/uploads/file/temp', // output dir
                        'html' => [         // settings for processing html
                            'inlineCss'     => true,    // replaces css classes to inline css rules
                            'inlineImages'  => true,   // looks for images in html and replaces the src attribute to base64 hash
                            'onlyContent'   => true,    // takes from html body content only
                                
                        ]
                    ]);
                    
                    // get pdf info
                    $pdfInfo = $pdf->getInfo();
                    
                    // get count pages
                    $countPages = $pdf->countPages();
                    
                    // get content from one page
                    $contentFirstPage = $pdf->getHtml()->getPage(1);
                    
                    // get content from all pages and loop for they
                    foreach ($pdf->getHtml()->getAllPages() as $page) {
                        $text.= $page . '<br/>';
                    }                    
                }

            }
        }

        return $this->render('update',[
            'model' => $model,
            'text' => $text,
            'page' => $countPages
        ]);
       
    }


    public function actionDelete($inv,$order)
    {
        $lot_session    = strtotime(date('Y-m-d H:i:s'));
        $transaction    = Yii::$app->db->beginTransaction();
        try {
            if(($Invoice    = RcInvoiceHeader::findOne($inv)) !==null){
                self::DeleteLog((Object)['table' => 'rc_invoice_header','field' => 'id','value' => $Invoice->id, 'lot' => $lot_session]);
                sleep(1);
                self::DeleteLog((Object)['table' => 'rc_invoice_header','field' => 'no_','value' => $Invoice->no_, 'lot' => $lot_session]);
                if(RcInvoiceLine::deleteAll(['source_id' => $inv])){  
                    $Invoice->delete();
                }else{
                    $transaction->rollBack();
                } 
            }

            if(($SaleHeader = SaleHeader::findOne($order)) !==null){
                self::DeleteLog((Object)['table' => 'sale_header','field' => 'id','value' => $order, 'lot' => $lot_session]);
                sleep(1);
                self::DeleteLog((Object)['table' => 'sale_header','field' => 'no','value' => $SaleHeader->no, 'lot' => $lot_session]);
                if(SaleLine::deleteAll(['sourcedoc' => $order])){                    
                    $SaleHeader->delete();
                }else{
                    $transaction->rollBack();
                }
            }

            $transaction->commit();  
            return $this->redirect(['/SaleOrders/backwards/index']);

        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
                'status' => 500,
                'message' => Yii::t('common','Error'),
                'suggestion' => Yii::t('common','{:e}',[':e' => $e]),
            ]);   
                
        }
        
        
    }


    static function DeleteLog($data){
        $model              = new \common\models\DeletionLog();
        $model->user_id     = Yii::$app->user->identity->id;
        $model->table_name  = $data->table;
        $model->field_name  = $data->field;
        $model->field_value = $data->value;
        $model->date_time   = date('Y-m-d H:i:s');
        $model->lot         = $data->lot;
        $model->save();
    }



    protected function findModel($id)
    {
        if (($model = SaleHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    public function actionFindCustomers(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 3 ? '' : 5;
         
        $query = Customer::find()
        ->where(['or',
            ['like','name',explode(' ',trim($data->search))],
            ['like','name_en',explode(' ',trim($data->search))],
            ['like','code',explode(' ',trim($data->search))]
        ])        
        ->andWhere(['status' => 1])
        ->limit($limit)
        ->all();

        $obj = [];
        foreach ($query as $key => $model) {
            $obj[] = (Object)[
                'id'        => $model->id,
                'name'      => $model->name,
                'name_en'   => $model->name_en,
                'code'      => $model->code,
                'address'   => $model->fulladdress,
                'head'      => (int)$model->headoffice,
                'term'      => $model->payment_term ? $model->payment_term : 0
            ];
        }

        return $this->asJson([
            'limit'     => $limit ? $limit : 'unlimited',
            'data'      => $obj,
            'search'    => $data->search
        ]);
    }


    public function actionLoadData(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $customer = Customer::findOne($data->headers->customer->id);

        $list = [];
        foreach ($data->line as $key => $value) {
            // ถ้าไม่เจอ ให้เข้าไปหาใน item cross reference
            $item = Items::findOne(['barcode' => $value->item]);
            if($item !== null){

                $ItemCross = $item->getItemCrossByCustomer($data->headers->customer->id);
                $list[] = [
                    'id'            => $item->id,
                    'name'          => $ItemCross ? $ItemCross->description : $item->description_th,
                    'name_en'       => $item->Description,
                    'barcode'       => $item->barcode,
                    'unit'          => $item->UnitOfMeasure,
                    'code'          => $customer !==null ? $customer->show_item_code == 1 ? $item->barcode : $item->master_code : $item->master_code ,
                    'qty'           => $value->qty,
                    'price'         => $value->price,
                    'status'        => true,
                    'discount'      => $value->discount ? $value->discount : 0
                ];
            }else{
                /* 
                    ถ้าลูกค้าผูกกับ สำนักงานใหญ่
                    ให้ไปเทียบ code จากสำนักงานใหญ่ 
                */
                $customer_list = [];
                
                if($customer !== null){
                    $customer_list[] = $customer->id;
                    if($customer->child > 0){
                        // เข้าไปหา 1 level
                        $child = Customer::findOne($customer->child);
                        $customer_list[] = $child !==null ? $child->id : 0;
                    }
                }
                

                $item_cross = Items::find()
                ->select('items.id as id, item_cross_reference.item_no as master_code, item_cross_reference.description as description_th, items.Description, items.barcode, items.UnitOfMeasure')
                ->joinWith('itemCrossReference')
                ->where(['item_cross_reference.item_no' => $value->item])
                ->andWhere(['IN', 'item_cross_reference.reference_no',$customer_list])
                ->one();

                if($item_cross !== null){
                    $list[] = [
                        'id'            => $item_cross->id,
                        'name'          => $item_cross->description_th,
                        'name_en'       => $item_cross->Description,
                        'barcode'       => $item_cross->barcode,
                        'unit'          => $item_cross->UnitOfMeasure,
                        'code'          => $item_cross->master_code,
                        'qty'           => $value->qty,
                        'price'         => $value->price,
                        'status'        => true,
                        'discount'      => $value->discount ? $value->discount : 0
                    ];
                }else{
                    $list[] = [
                        'id'            => 0,
                        'code'          => $value->item,
                        'name'          => Yii::t('common','unknown'),
                        'qty'           => $value->qty,
                        'price'         => $value->price,
                        'status'        => false,
                        'discount'      => 0
                    ];
                }
                
            }
        }

        return $this->asJson([
            'data'      => $data,
            'item'      => $list
        ]);

    }

    static function lineTotal($data){
        
        $customer = Customer::findOne($data->headers->customer->id);
        $total = 0;
        //round( $model->price, 2, PHP_ROUND_HALF_UP)
        foreach ($data->line as $key => $model) {
            $item = Items::findOne([$model->id]);
            if($item!==null){
                $total+= $model->price * $model->qty;
            }else{
                /* 
                    ถ้าลูกค้าผูกกับ สำนักงานใหญ่
                    ให้ไปเทียบ code จากสำนักงานใหญ่ 
                */
                $customer_list = [];
                
                if($customer !== null){
                    $customer_list[] = $customer->id;
                    if($customer->child > 0){
                        // เข้าไปหา 1 level
                        $child = Customer::findOne($customer->child);
                        $customer_list[] = $child !==null ? $child->id : 0;
                    }
                }
                $item_cross = Items::find()
                ->select('items.id as id, item_cross_reference.item_no as master_code, item_cross_reference.description as description_th, items.Description, items.barcode, items.UnitOfMeasure')
                ->joinWith('itemCrossReference')
                ->where(['item_cross_reference.item_no' => $model->code])
                ->andWhere(['IN', 'item_cross_reference.reference_no',$customer_list])
                ->one();
                if($item_cross!==null){
                    $total+= $model->price * $model->qty;  
                }
            }
        }

        $vat            = $data->headers->header->vat;             
        $BeforeDisc     = $total;
        $Discount       = 0;
        $subtotal       = $BeforeDisc - $Discount;
        if($data->headers->header->incvat == 1){
            // Vat นอก
            $InCVat   = ($subtotal * $vat )/ 100;
            $total    = ($InCVat + $subtotal);
        }else {
            // Vat ใน
            // 1.07 = 7%
            $vat_revert = ($vat/100) + 1;
            $InCVat     = $subtotal - ($subtotal / $vat_revert);
            $total      = $subtotal;
        }
        return  (Object)[
            'subtotal'  => $subtotal,
            'incvat'    => $InCVat,
            'vat'       => $vat,
            'discount'  => $Discount,
            'sumline'   => $BeforeDisc,
            'total'     => $total,
        ];

    }

    public function actionCreateSaleLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        
        $customer       = Customer::findOne($data->headers->customer->id);
        $customer->payment_term = $data->headers->customer->term;
        $customer->save(false);

        $list           = [];  
        

        $transaction    = Yii::$app->db->beginTransaction();
        try {

            $totals                 = self::lineTotal($data);
            $NoSeries               = new Generater();
            // Create sale header
            $model = SaleHeader::findOne(['session_id' => $data->headers->session->id]); 

            if ($model == null) {
                $model              = new SaleHeader();
                $model->no          = Generater::getRuning('sale_header','no','all'); 
                Generater::UpdateSeries('sale_header','no','all',$model->no);
            }
            
            
            
            $model->customer_id     = $customer->id;
            $model->sale_address    = $customer->fulladdress['address'];
            $model->bill_address    = $customer->fulladdress['address'];
            $model->ship_address    = $customer->fulladdress['address'];

            $orderDate              = str_replace('-', '/', $data->headers->header->date);
            $model->ship_date       = date('Y-m-d',strtotime($orderDate));
            $model->order_date      = date('Y-m-d',strtotime($orderDate));
            $model->balance         = $totals->total;

            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->sales_people    = Yii::$app->session->get('Rules')['sale_code'];
            $model->sale_id         = Yii::$app->session->get('Rules')['sale_id'];
            $model->vat_type        = $data->headers->header->incvat;
            $model->vat_percent     = $data->headers->header->vat;
            $model->include_vat     = $data->headers->header->incvat;
            $model->balance_befor_vat = 0;

            $model->create_date     = date('Y-m-d H:i:s');
            $model->status          = 'Shiped';
            $model->paymentdue      = date('Y-m-d',strtotime($orderDate . "+ ".$customer->payment_term." days"));
            $model->payment_term    = $customer->payment_term;
            $model->discount        = 0;
            $model->percent_discount= 0;
            $model->confirm         = count($data->line);
            $model->confirm_by      = 1; // Auto confirm
            $model->ext_document    = $data->headers->header->po;
            $model->remark          = $data->headers->header->remark;
            $model->session_id      = $data->headers->session->id;
            $model->extra           = 'backward';

            if($model->save()){

                // ลบรายการออกก่อน เพื่อป้องกันการซ้ำจากการคลิกรอบที่แล้ว
                $ClearSaleLine  = SaleLine::deleteAll(['sourcedoc' => $model->id]);

                
                foreach ($data->line as $key => $value) {

                    $item = Items::findOne($value->id);
                    if($item !== null){
                
                        // สร้าง Sale line ใหม่
                        $CreateSaleLine                 = new SaleLine();
                        $CreateSaleLine->order_no       = $model->no;
                        $CreateSaleLine->description    = $value->name;
                        $CreateSaleLine->item           = $item->id;
                        $CreateSaleLine->item_no        = $item->No;
                        $CreateSaleLine->quantity       = $value->qty;
                        $CreateSaleLine->unit_price     = $value->price;
                        $CreateSaleLine->line_discount  = $value->discount;
                        $CreateSaleLine->create_date    = date('Y-m-d H:i:s');
                        $CreateSaleLine->vat_percent    = $model->vat_percent;
                        $CreateSaleLine->user_id        = Yii::$app->user->identity->id;
                        $CreateSaleLine->api_key        = $model->session_id;
                        $CreateSaleLine->comp_id        = $model->comp_id;
                        $CreateSaleLine->sourcedoc      = $model->id;
                        $CreateSaleLine->save_order     = 'saved';
                        $CreateSaleLine->unit_price_exvat= ($value->price) ? ($value->price * 100) /107 : 0;
                
                        if($CreateSaleLine->save()){
                            $list[] = [
                                'id'            => $item->id,
                                'name'          => $value->name,
                                'name_en'       => $item->Description,
                                'barcode'       => $item->barcode,
                                'unit'          => $item->UnitOfMeasure,
                                'code'          => $customer !==null ? $customer->show_item_code == 1 ? $item->barcode : $item->master_code : $item->master_code ,
                                'qty'           => $value->qty,
                                'price'         => $value->price,
                                'discount'      => $value->discount,
                                'status'        => true
                            ];
                        }else{
                            $transaction->rollBack();
                        }
                   
                        
                        
                    } 
                    
                }

                // Create Invoice header
                $inv_header = RcInvoiceHeader::findOne(['session_id' => $data->headers->session->id]); 
                if ($inv_header == null) {
                    $inv_header             = new RcInvoiceHeader();                      
                }else{
                    if($inv_header->id == ''){
                        $transaction->rollBack();
                        return $this->asJson([
                            'status' => 500,
                            'message' => 'Error',
                            'suggestion' => $data->headers->session->id
                        ]);
                    }
                }

                

                
                if($data->headers->header->inv){
                    if(RcInvoiceHeader::find()->where(['no_' => $data->headers->header->inv])->exists()){
                        $transaction->rollBack();
                        return $this->asJson([
                            'status' => 500,
                            'message' => Yii::t('common','Already exists.'),
                            'suggestion' => $data->headers->header->inv
                        ]);
                    }
                    $inv_header->no_ 			= $data->headers->header->inv;
                }else{
                    $inv_header->no_ 			= $NoSeries->GenNumber('vat_type','vat_value',($model->vat_percent)? $model->vat_percent : 0 ,false);
                }
                
                $inv_header->cust_no_ 		= $customer->id;
                $inv_header->cust_name_		= $customer->name;
                $inv_header->document_no_	= $model->no;
                $inv_header->posting_date 	= $model->order_date;
                $inv_header->doc_type 		= 'Sale';            
                $inv_header->order_id 		= $model->id;
                $inv_header->sales_people 	= $model->sales_people;
                $inv_header->sale_id 		= $model->sale_id;
                $inv_header->cust_code 		= $customer->code;
                $inv_header->order_date	 	= $model->order_date;
                $inv_header->ship_date 		= $model->ship_date;
                $inv_header->cust_address 	= $customer->address;
                $inv_header->cust_address2 	= $customer->address2;            
                $inv_header->phone 			= $customer->phone;
                $inv_header->district 		= $customer->district;
                $inv_header->city 			= $customer->city;
                $inv_header->province 		= $customer->province;
                $inv_header->postcode 		= $customer->postcode;
                $inv_header->discount 		= $model->discount;            
                $inv_header->percent_discount= $model->percent_discount;
                $inv_header->vat_percent 	= $model->vat_percent;
                $inv_header->include_vat	= $model->include_vat;
                
                $inv_header->paymentdue		= $model->paymentdue;
                $inv_header->payment_term 	= $model->payment_term;
                $inv_header->ext_document	= $model->ext_document;
                $inv_header->remark 		= $model->remark;
                $inv_header->status 		= 'Posted';
                $inv_header->ref_inv_header = $model->id;
                $inv_header->user_id 		= Yii::$app->user->identity->id;
                $inv_header->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $inv_header->host 			= gethostname();
                $inv_header->session_id     = $data->headers->session->id;
                $inv_header->extra          = 'backward';

                if($inv_header->save()){

                    // ลบรายการออกก่อน เพื่อป้องกันการซ้ำจากการคลิกรอบที่แล้ว
                    $ClearInvLine  = RcInvoiceLine::deleteAll(['source_id' => $inv_header->id]);


                    foreach ($data->line as $key => $value) {
                        
                        $item = Items::findOne($value->id);

                        if($item !== null){

                            $RcInvoiceLine                  = new RcInvoiceLine();
                            $RcInvoiceLine->type 			= 'Item';
                            $RcInvoiceLine->item		 	= $item->id;
                            $RcInvoiceLine->doc_no_ 		= $inv_header->no_;
                            $RcInvoiceLine->line_no_ 		= $item->id;
                            $RcInvoiceLine->source_id 		= $inv_header->id;
                            $RcInvoiceLine->customer_no_	= $inv_header->cust_no_;
                            $RcInvoiceLine->code_no_		= $item->No;
                            $RcInvoiceLine->code_desc_		= $value->name;
                            $RcInvoiceLine->quantity 		= $value->qty;
                            $RcInvoiceLine->unit_price 		= $value->price;
                            $RcInvoiceLine->line_discount   = $value->discount;
                            $RcInvoiceLine->vat_percent 	= $inv_header->vat_percent;
                            $RcInvoiceLine->order_id 		= $inv_header->order_id;
                            $RcInvoiceLine->source_doc		= $model->no;
                            $RcInvoiceLine->source_line		= 0;
                            $RcInvoiceLine->session_id 		= $inv_header->session_id;
                            $RcInvoiceLine->posting_date    = $inv_header->posting_date;
                            $RcInvoiceLine->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

                            if(!$RcInvoiceLine->save()){
                                $transaction->rollBack();
                                return $this->asJson([
                                    'status' => 500,
                                    'message' => 'Error',
                                    'suggestion' => json_encode($RcInvoiceLine->getErrors(),JSON_UNESCAPED_UNICODE)
                                ]);
                                
                            }

                        }
                    }

                    $transaction->commit();  
                }else{
                    $transaction->rollBack();
                }
            
            }else{
                $transaction->rollBack();
            }

            
            

            

            

            return $this->asJson([
                'status' => 200,
                'order'     => [
                    'id'        => $model->id,
                    'no'        => $model->no,
                    'balance'   => $model->balance
                ],
                'invoice'   => [
                    'id' => $inv_header->id,
                    'no' => $inv_header->no_
                ],
                'item'      => $list
            ]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
                'status' => 500,
                'message' => Yii::t('common','Error'),
                'suggestion' => Yii::t('common','{:e}',[':e' => $e]),
                'data' => $data 
            ]);   
                
        }

    }




    public function actionFindItems(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 3 ? '' : 5;

        $customer       = Customer::findOne($data->customer);

        $query          = Items::find()
        ->select('items.id as id, items.master_code as master_code, items.description as description_th, items.Description, items.barcode, items.UnitOfMeasure')
        ->joinWith('itemCrossReference')
        ->where(['or',
            ['items.master_code'    => $data->search],
            ['items.barcode'        => $data->search],
            ['items.description_th' => trim($data->search)],
            ['item_cross_reference.item_no' => $data->search]
        ]);

        $items          = [];
        foreach ($query->limit($limit)->all() as $key => $model) {

            // ราคา ล่าสุดของลูกค้า
            $lastPrice  = SaleLine::find()
                        ->joinWith('saleHeader')
                        ->select('sale_line.unit_price')
                        ->where(['sale_header.customer_id'  => $data->customer])
                        ->andWhere(['sale_line.item'        => $model->id])
                        ->orderBy(['sale_line.id'           => SORT_DESC])
                        ->limit(1)
                        ->one();

            $items[]= [
                'id'        => $model->id,
                'name'      => $model->description_th,
                'name_en'   => $model->Description,
                'code'      => $customer !==null ? $customer->show_item_code == 1 ? $model->barcode : $model->master_code : $model->master_code ,
                'barcode'   => $model->barcode,
                'lastprice' => $lastPrice !== null ? $lastPrice->unit_price : 0
            ];
        }

        return $this->asJson([
            'status'    => $query->count() > 0 ? 200 : 404,
            'data'      => $data,
            'items'     => $items,
            'message'   => $query->count() > 0 ? Yii::t('common','Done') : Yii::t('common','Not found'),
        ]);
    }

    public function actionFinished(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
         

        $inv    = [];
        $order  = [];

        $RcInvoiceHeader    = RcInvoiceHeader::findOne($data->inv_id); 
        $SaleHeader         = SaleHeader::findOne($data->order_id); 

        // Invoice 
        if($RcInvoiceHeader!==null){
            
            $inv[] = [
                'status'    => 200,
                'message'   => [
                    'id' => $RcInvoiceHeader->id,
                    'no' => $RcInvoiceHeader->no_,
                    'total' => $RcInvoiceHeader->sumtotals->total
                ]
            ];
            
        }else{
            $inv[] = [
                'status'    => 404,
                'message'   => 'Not found'
            ];
        }
        
        // Sale Order
        if($SaleHeader!==null){
           
            $order[] = [
                'status'    => 200,
                'message'   => [
                    'id' => $SaleHeader->id,
                    'no' => $SaleHeader->no,
                    'total' => $SaleHeader->sumtotal->total
                ]
            ];
            
        }else{
            $order[] = [
                'status'    => 404,
                'message'   => 'Not found'
            ];
        }

        

        return json_encode([
            'status' => 200,
            'order' => $order,
            'inv' => $inv
        ]);
    }

}
