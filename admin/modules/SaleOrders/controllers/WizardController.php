<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\WizardSearch;
use admin\modules\SaleOrders\models\OrderWizardSearch;
use common\models\Customer;
use common\models\Items;
use common\models\SaleHeader;
use common\models\SaleLine;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use admin\models\Generater;
use common\models\BomHeader;
use common\models\BomLine;
use common\models\WarehouseMoving;
use common\models\WarehouseHeader;
use common\models\Transections;
use yii\web\UploadedFile;


class WizardController extends \yii\web\Controller
{

    public function beforeAction($action)
    {
        if ($action->id == 'import-file') {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }
    
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        if(\common\models\Options::getSystemStatus()){
            $searchModel = new WizardSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andFilterWhere(['rc_invoice_header.extra' => 'wizard']);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->renderpartial('@admin/views/site/system-off');
        }
    }



    public function actionOrder()
    {
        $searchModel = new OrderWizardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('order', [
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
                        // 'pdftohtml_path'    => '/usr/local/bin/pdftohtml',  // When Mac OS X
                        // 'pdfinfo_path'      => '/usr/local/bin/pdfinfo',    // When Mac OS X
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
        if(Yii::$app->user->identity->id !==1){
            Yii::$app->session->setFlash('error', Yii::t('common','Not Allow'));
            return $this->redirect(['/SaleOrders/wizard/index']);
        }else{
            $transaction    = Yii::$app->db->beginTransaction();
            try {
                if(($Invoice    = RcInvoiceHeader::findOne($inv)) !==null){
                    if(RcInvoiceLine::deleteAll(['source_id' => $inv])){
                        self::DeleteLog((Object)['table' => 'rc_invoice_header','field' => 'id','value' => $Invoice->id, 'lot' => $lot_session]);
                        sleep(1);
                        self::DeleteLog((Object)['table' => 'rc_invoice_header','field' => 'no_','value' => $Invoice->no_, 'lot' => $lot_session]);
                        $Invoice->delete();                    
                    }else{
                        $transaction->rollBack();
                    } 
                }
                $SaleHeader = SaleHeader::find()
                            ->where(['id' => $order])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->one();
                if($SaleHeader !==null){
                    if(SaleLine::deleteAll(['sourcedoc' => $order])){                    
                        self::DeleteLog((Object)['table' => 'sale_header','field' => 'id','value' => $order, 'lot' => $lot_session]);
                        sleep(1);
                        self::DeleteLog((Object)['table' => 'sale_header','field' => 'no','value' => $SaleHeader->no, 'lot' => $lot_session]);
                        $SaleHeader->delete();
                    }else{
                        $transaction->rollBack();
                    }
                }
                
                $Warehouse = WarehouseHeader::find()
                            ->where(['SourceDocNo' => $order])
                            ->andWhere(['TypeOfDocument' => 'Invoice'])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->one();
                            //->andWhere(['between','DATE(PostingDate)', date('Y').'-01-01', date('Y').'-12-31'])
                            

                if($Warehouse !==null){
                    if(WarehouseMoving::deleteAll(['source_id' => $Warehouse->id])){                    
                        self::DeleteLog((Object)['table' => 'warehouse_header','field' => 'id','value' => $order, 'lot' => $lot_session]);
                        sleep(1);
                        self::DeleteLog((Object)['table' => 'warehouse_header','field' => 'no','value' => $Warehouse->DocumentNo, 'lot' => $lot_session]);
                        $Warehouse->delete();
                    }else{
                        $transaction->rollBack();
                    }
                }

                

                $transaction->commit();  
                return $this->redirect(['/SaleOrders/wizard/index']);

            } catch (\Exception $e) {
                $transaction->rollBack();
                return json_encode([
                    'status' => 500,
                    'message' => Yii::t('common','Error'),
                    'suggestion' => Yii::t('common','{:e}',[':e' => $e]),
                ]);   
                    
            }
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



    public function actionImportFile(){
        // https://github.com/tonchik-tm/pdf-to-html
        // ต้องติดตั้ง package บน server ก่อน

        // ### Debian/Ubuntu
        // sudo apt-get install poppler-utils

        // ### Docker alpine
        // apk add poppler-utils

        // ### Mac OS X
        // brew install poppler

        // ตรวจสอบการติดตั้ง 
        // ### Debian/Ubuntu
        // $ whereis pdftohtml
        // pdftohtml: /usr/bin/pdftohtml
        // $ whereis pdfinfo
        // pdfinfo: /usr/bin/pdfinfo
        
        // ### Mac OS X / Docker alpine
        // $ which pdfinfo
        // /usr/local/bin/pdfinfo
        // $ which pdftohtml
        // /usr/local/bin/pdfinfo   

        $text           = '';
        $countPages     = 1;
 
        if (isset($_POST['submit'])) {
            if(isset($_FILES["file"])){
                
                if($_FILES["file"]['size'] > 0){

                    $pdf = new \TonchikTm\PdfToHtml\Pdf($_FILES["file"]["tmp_name"], [
                        //  'pdftohtml_path'    => '/usr/local/bin/pdftohtml',  // When Mac OS X
                        //  'pdfinfo_path'      => '/usr/local/bin/pdfinfo',    // When Mac OS X                    
                        'generate' => [     // settings for generating html
                            'singlePage'    => false,    // we want separate pages
                            'imageJpeg'     => false,   // we want png image
                            'ignoreImages'  => false,   // we need images
                            'zoom'          => 1.05,     // scale pdf
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

        return $this->renderPartial('import_file',['text' => $text,'page' => $countPages]);
    }

    public function actionLoadData(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $customer = Customer::findOne($data->headers->customer->id);

        
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
        
        $list = [];
        foreach ($data->line as $key => $value) {

            // หาใน Cross Reference ก่อน
            $item_cross = Items::find()
                        ->select('items.id as id, item_cross_reference.item_no as master_code, item_cross_reference.description as description_th, items.Description, item_cross_reference.barcode as barcode, items.UnitOfMeasure, item_cross_reference.description as alias')
                        ->joinWith('itemCrossReference')
                        ->where(['or',
                            ['item_cross_reference.barcode' => $value->item],
                            ['item_cross_reference.item_no' => $value->item],
                        ])
                        ->andWhere(['IN', 'item_cross_reference.reference_no',$customer_list]);            

            if($item_cross->count() > 0){
                $item = $item_cross->one();
                $barcode = $customer != null 
                            ? $customer->show_item_code == 1 
                                ? $item->barcode 
                                : $item->master_code 
                            : $item->master_code;

                $list[] = [
                    'id'            => $item->id,
                    'alias'         => $item->alias,
                    'name'          => $item->description_th,
                    'name_en'       => $item->Description,
                    'barcode'       => $barcode,
                    'unit'          => $item->UnitOfMeasure,
                    'code'          => $barcode,
                    'qty'           => $value->qty,
                    'price'         => $value->price,
                    'status'        => true,
                    'discount'      => 0
                ];
            }else{
                 
                $item = Items::findOne(['barcode' => $value->item]);
                if($item !== null){
                    //$ItemCross = $item->getItemCrossByCustomer($data->headers->customer->id);                    
                    $list[] = [
                        'id'            => $item->id,
                        'name'          => $item->description_th,
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

        $checkPo = RcInvoiceHeader::find()->where(['ext_document' => $data->headers->header->po, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        return $this->asJson([
            //'data'      => $data,
            'item'      => $list,
            'po'        => [
                'exists' => $checkPo->exists(),
                'inv_id' => $checkPo->exists() ? $checkPo->one()->id : 0,
                'inv_no' => $checkPo->exists() ? $checkPo->one()->no_ : '',
                'message' => $checkPo->exists() ? Yii::t('common','This PO Already exists') : ''
            ]
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
        $status         = 'done';
        
        $customer       = Customer::findOne($data->headers->customer->id);
        $customer->payment_term = $data->headers->customer->term;
        $customer->save(false);

        $list           = [];  
        

        $transaction    = Yii::$app->db->beginTransaction();
        try {

            $totals     = self::lineTotal($data);
            $NoSeries   = new Generater();
            // Create sale header
            $model      = SaleHeader::findOne(['session_id' => $data->headers->session->id]); 

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
            $model->paymentdue      = date('Y-m-d',strtotime($orderDate . "+ ".$data->headers->customer->term." days"));
            $model->payment_term    = $customer->payment_term;
            $model->discount        = 0;
            $model->percent_discount= 0;
            $model->confirm         = count($data->line);
            $model->confirm_by      = 1; // Auto confirm
            $model->ext_document    = $data->headers->header->po;
            $model->remark          = $data->headers->header->remark;
            $model->session_id      = $data->headers->session->id;
            $model->extra           = 'wizard';

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
                        $CreateSaleLine->quantity       = $value->qty * 1;
                        $CreateSaleLine->unit_price     = $value->price * 1;
                        $CreateSaleLine->line_discount  = (isset($value->discount) ? $value->discount : 0 )* 1;
                        $CreateSaleLine->create_date    = date('Y-m-d H:i:s');
                        $CreateSaleLine->quantity_shipped= $value->qty;
                        $CreateSaleLine->vat_percent    = $model->vat_percent;
                        $CreateSaleLine->user_id        = Yii::$app->user->identity->id;
                        $CreateSaleLine->api_key        = $model->session_id;
                        $CreateSaleLine->comp_id        = $model->comp_id;
                        $CreateSaleLine->sourcedoc      = $model->id;
                        $CreateSaleLine->save_order     = 'saved';
                        $CreateSaleLine->confirm        = $value->qty;
                        $CreateSaleLine->confirm_by     = Yii::$app->user->identity->id;
                        $CreateSaleLine->session_id     = $model->session_id;
                        $CreateSaleLine->unit_price_exvat= ($value->price) ? ($value->price * 100) /107 : 0;                        
                
                        if($CreateSaleLine->save()){
                            $barcode = $customer !==null 
                                        ? $customer->show_item_code == 1 
                                            ? $item->barcode                                               
                                                ? $item->getItemCrossByCustomer($data->headers->customer->id)->barcode
                                                : $item->master_code                                                                                           
                                            : $item->master_code 
                                        : $item->master_code;

                            $list[] = [
                                'id'            => $item->id,
                                'name'          => $value->name,
                                'name_en'       => $item->Description,
                                'barcode'       => $barcode,
                                'unit'          => $item->UnitOfMeasure,
                                'code'          => $barcode,
                                'qty'           => $value->qty,
                                'price'         => $value->price,
                                'discount'      => $value->discount,
                                'status'        => true
                            ];
                        }
                        
                        
                    } 
                    
                }

                // Create Invoice header
                $inv_header = RcInvoiceHeader::findOne(['session_id' => $data->headers->session->id]); 
                if ($inv_header == null) {
                    $inv_header             = new RcInvoiceHeader();
                }

                if($data->headers->header->inv){
                    if(RcInvoiceHeader::find()->where(['no_' => $data->headers->header->inv])->andWhere(['<>','session_id',$data->headers->session->id])->exists()){
                        //$transaction->rollBack();
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
                $inv_header->posting_date 	= $model->order_date. ' '.date('H:i:s');
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
                $inv_header->extra          = 'wizard';

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
                            $RcInvoiceLine->quantity 		= $value->qty * 1;
                            $RcInvoiceLine->unit_price 		= $value->price * 1;
                            $RcInvoiceLine->line_discount   = $value->discount * 1;
                            $RcInvoiceLine->vat_percent 	= $inv_header->vat_percent;
                            $RcInvoiceLine->order_id 		= $inv_header->order_id;
                            $RcInvoiceLine->source_doc		= $model->no;
                            $RcInvoiceLine->source_line		= 0;
                            $RcInvoiceLine->session_id 		= $inv_header->session_id;
                            $RcInvoiceLine->posting_date    = $inv_header->posting_date;
                            $RcInvoiceLine->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

                            if(!$RcInvoiceLine->save()){
                                //$transaction->rollBack();
                                $status     = json_encode($RcInvoiceLine->getErrors(),JSON_UNESCAPED_UNICODE);
                                return json_encode([
                                    'status' => 500,
                                    'message' => 'Error',
                                    'suggestion' => json_encode($RcInvoiceLine->getErrors(),JSON_UNESCAPED_UNICODE)
                                ]);
                                exit;
                            }

                        }
                    }

                    
                }
            
            }

            $transaction->commit();

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
                'item'      => $list,
                'stock'     => self::stockCalculate($inv_header)
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


    static function stockAdjust($model){
        // model = RcInvoiceHeader
        $JSON[] = [
            'status' => false,
            'text'      => [
                'message' => 'NULL'
            ]
        ];  
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
 

            $GenSeries  = new Generater();

            if ($model !== null) {

                // Clear status (Clear array)
                unset($JSON);
                        
                $Header                 = new WarehouseHeader();             

                $Header->line_no        = $model->id;
                $Header->PostingDate    = date('Y-m-d',strtotime($model->order_date)).' '.date('H:i:s');
                $Header->DocumentDate   = date('Y-m-d');
                $Header->TypeOfDocument = "Sale";
                $Header->SourceDocNo    = $model->order_id;
                $Header->DocumentNo     = $GenSeries->GenerateNoseries('Shipment',true);
                $Header->customer_id    = $model->cust_no_;
                $Header->SourceDoc      = $model->document_no_;
                $Header->order_id       = $model->order_id;

                $Header->Description    = '';
                $Header->Quantity       = 0;
                $Header->address        = '';
                $Header->address2       = '';
                $Header->district       = 0;
                $Header->city           = 0;
                $Header->province       = 0;
                $Header->postcode       = 0;
                $Header->contact        = '';
                $Header->phone          = '';
                $Header->gps            = '';
                $Header->update_date    = '';
                $Header->status         = 'Shiped';
                $Header->user_id        = Yii::$app->user->identity->id;
                $Header->comp_id        = Yii::$app->session->get('Rules')['comp_id'];
                $Header->ship_to        = 0;
                $Header->AdjustType     = NULL;
                $Header->remark         = '';
                $Header->comment        = '';
                $Header->session_id     = $model->session_id;

                if($Header->save()){

                    $query              = RcInvoiceLine::find()->where(['source_id' => $model->id]);

                    if($query->exists()){
                    
                        foreach ($query->all() as $key => $line) {

                            $ItemCross = $line->items->getItemCrossByCustomer($Header->customer_id);

                            $barcode = $Header->customer != null
                                        ? $Header->customer->show_item_code == 1
                                            ? $line->items->barcode == ''
                                                ? $ItemCross != null
                                                    ? $ItemCross->barcode
                                                    : $ItemCross->item_no
                                                : $line->items->barcode
                                            : $line->items->master_code
                                        : $line->items->master_code;

                            $JSON[] = [
                                    'id'        => $line->items->id,
                                    'orderid'   => $model->order_id,
                                    'order'     => $model->no_,
                                    'item'      => $line->items->No,                
                                    'code'      => $line->items->master_code,
                                    'name'      => $ItemCross ? $ItemCross->description : $line->items->description_th,
                                    'name_en'   => $line->items->Description,
                                    'barcode'   => $barcode,
                                    'show_code' => $barcode,
                                    'unit'      => $line->items->UnitOfMeasure,
                                    'inven'     => $line->items->getInven(),
                                    'qty'       => $line->quantity,
                                    'need'      => $line->quantity,
                                    'price'     => $line->unit_price * 1,
                                    'img'       => $line->items->getPicture(),
                                    'qty_per'   => $line->items->quantity_per_unit,
                                    'status'    => true,
                                    //'child'     => $Header->getAssembly($line->items->ProductionBom,0),
                                    'production'=> $Header->producer($line, $Header, $line->quantity),
                                    'shipment'  => $Header->shipment($line, $Header, $line->quantity)
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

                }
                
            } else {

                $JSON[] = [
                    'status' => false,
                    'text'      => [
                        'message' => 'Error'
                    ]
                ];   

            }

            $transaction->commit();

           
          
        } catch (\Exception $e) {
            $transaction->rollBack();

            $JSON[] = [
                'status' => false,
                'text'      => [
                    'message' => Yii::t('common','{:e}',[':e' => $e])
                ]
            ];                
            
           
        }

        return $JSON;
    }
     


    // static function getAssembly($id,$loop){
    //     $Bomheader  = \common\models\BomHeader::findOne($id);
    //     $Bomline    = \common\models\BomLine::find()->where(['bom_no' => $id])->all();

    //     $data = [];
    //     if($Bomline !== null){
            
    //         $maxLoop = 20;
    //         foreach ($Bomline as $key => $model) {
    //             $loop++;
                
    //              $data[] = [
    //                  'id'       => $model->items->id,
    //                  'item'     => $model->item_no,
    //                  'head'     => $Bomheader->id,
    //                  'code'     => $model->items->master_code,
    //                  'desc'     => $model->items->description_th,
    //                  'img'      => $model->items->getPicture(),
    //                  'qty'      => $model->quantity,
    //                  'qtyprint' => $model->quantity,
    //                  'need'     => $model->quantity,
    //                  'unit'     => $model->measure, 
    //                  'child'    => ($loop > $maxLoop)? Yii::t('common','Error!').' '.Yii::t('common','Over').' '.$maxLoop.' '.Yii::t('common','Loop') : self::getAssembly($model->items->ProductionBom,$loop),
    //                  'status'   => ($loop > $maxLoop)? false : true,                   
    //              ];
    //         }

    //     }else {
    //        $data = ['status' => false];
    //     }

    //     return $data;
    // }


    public function actionFindItems(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 3 ? '' : 5;

        $customer       = Customer::findOne($data->customer);

        // หาจากลูกค้าก่อน 
        $cross          = true;
        $query          = Items::find()
                        ->select('items.id as id, item_cross_reference.item_no as master_code, items.description as description_th, items.Description, item_cross_reference.barcode, items.UnitOfMeasure, item_cross_reference.description as alias')
                        ->joinWith('itemCrossReference')
                        ->where(['or',
                            ['item_cross_reference.barcode' => trim($data->search)],
                            ['item_cross_reference.item_no' => trim($data->search)]
                        ])
                        ->andWhere(['item_cross_reference.reference_no' => $data->customer]);

        if($query->count() <= 0){
            $cross          = false;
                       
            $query          = Items::find()
                            ->select('items.id as id, items.master_code as master_code, items.description as description_th, items.Description, items.barcode, items.UnitOfMeasure, item_cross_reference.description as alias')
                            ->joinWith('itemCrossReference')
                            ->where(['or',
                                ['items.master_code'    => $data->search],
                                ['items.barcode'        => $data->search],
                                ['items.description_th' => trim($data->search)],
                                ['item_cross_reference.item_no' => $data->search]
                            ]);
        }

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
            $barcode = $customer !==null 
                        ? $customer->show_item_code == 1 
                            ? $model->barcode 
                                ? $model->barcode
                                : $model->master_code
                            : $model->master_code 
                        : $model->master_code;

            $items[]= [
                'id'        => $model->id,
                'alias'     => $model->alias,
                'name'      => $model->description_th,
                'name_en'   => $model->Description,
                'code'      => $barcode,
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
        $request_body       = file_get_contents('php://input');
        $data               = json_decode($request_body);
        $inv                = [];
        $order              = [];
        $RcInvoiceHeader    = RcInvoiceHeader::findOne($data->inv_id); 
        $SaleHeader         = SaleHeader::findOne($data->order_id); 
        $stock              = self::stockAdjust($RcInvoiceHeader);
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
            'stock' => $stock,
            'inv' => $inv
        ]);
    }


    // static function producer($list,$Header,$qty){
    //     // Cancel
    //     $data       = [];        
    //     $line       = \common\models\BomLine::find()
    //     ->where(['bom_no' => $list->items->ProductionBom]);

    //     // ถ้ามี Bom ให้ Output FG (+)              
    //     if($line->count() > 0){         
    //         /*
    //         * ถ้าจำนวนใน Bomline น้อยกว่า 0 จะไม่ต้อง Output
    //         *  
    //         */
            
    //         $output         = self::Output($list,$Header,$qty);
    //         foreach ($line->all() as $key => $model) {                    
    //             // ถ้ามี bom ย่อย ให้ไปหาอีก
    //             // ถ้าไม่มี ให้ return item และ จำนวนที่ต้องใช้ กลับ
    //             // เพื่อนำไปตัด consumption
    //             if($model->items->ProductionBom!= ''){

    //                 // ถ้ามี bom ย่อย ให้ไปหาอีก
    //                 $data[]         = self::producer($model,$Header,$qty);  

    //                 // Consumption ตัวเอง เพื่อประกอบเป็น Item หลัก
    //                 $consumption    = self::Consumption($model,$Header,$output);
    //                 $data[] = [
    //                     'status'    => 200,      
    //                     'code'      => $model->items->master_code,
    //                     'id'        => $model->items->id,
    //                     'qty'       => $consumption->value->Quantity,
    //                     'cost'      => $model->items->StandardCost,
    //                     'name'      => $model->items->description_th,                
    //                     'message'   => $consumption->message,
    //                 ];                     
    //             }else {
    //                 // Consumption Item ย่อย
    //                 $consumption    = self::Consumption($model,$Header,$output);
    //                 $data[] = [
    //                     'status'    => 200,     
    //                     'code'      => $model->items->master_code,
    //                     'id'        => $model->items->id,
    //                     'qty'       => $consumption->value->Quantity,    
    //                     'cost'      => $model->items->StandardCost,
    //                     'name'      => $model->items->description_th,        
    //                     'message'   => $consumption->message,
    //                 ];                        
    //             }                  
    //         }            
    //         $data[] = [
    //             'status'    => 200, 
    //             'code'      => $list->items->master_code,    
    //             'id'        => $list->items->id,
    //             'qty'       => $output->value->Quantity,     
    //             'cost'      => $model->items->StandardCost,
    //             'name'      => $list->items->description_th,           
    //             'message'   => $output->message,
    //         ];
            
    //     }
    
    //     return $data;       
    // }

    // static function Output($saleLine,$Header,$qty){
    //     $Order      = SaleHeader::findOne($Header->SourceDocNo);

    //     $model      = new WarehouseMoving();        
    //     $model->line_no             = Yii::$app->session->get('Rules')['comp_id'].$Header->SourceDocNo;
    //     $model->source_id           = $Header->id;
    //     $model->DocumentNo          = $Header->DocumentNo;          
    //     $model->PostingDate         = date('Y-m-d H:i:s', strtotime($Header->PostingDate.'-1 seconds'));
    //     $model->TypeOfDocument      = 'Output';
    //     $model->SourceDoc           = $Order->id;
    //     $model->SourceDocNo         = $Order->no;
         
    //     $model->item                = $saleLine->items->id;
    //     $model->ItemNo              = $saleLine->items->No;
    //     $model->Description         = $saleLine->items->description_th;
    //     $model->Quantity            = $qty;
    //     $model->QtyToMove           = $qty;
    //     $model->QtyMoved            = 0;
    //     $model->QtyOutstanding      = 0;      
    //     $model->DocumentDate        = date('Y-m-d');        
    //     $model->user_id            = Yii::$app->user->identity->id;
    //     $model->comp_id            = Yii::$app->session->get('Rules')['comp_id'];        
    //     $model->session_id         = $Header->session_id;
    //     $model->qty_per_unit       = $saleLine->items->quantity_per_unit;        
    //     $model->unit_price         = $saleLine->items->StandardCost;
    //     if($model->save()){
    //         return (object)['message'=>'Output','value' => $model];
    //     }else {
    //         return (object)['message'=> json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),'value' => ''];
    //     }

   
    // }

    // static function Consumption($Bom,$Header,$output){

    //     $Order      = SaleHeader::findOne($Header->SourceDocNo);

    //     $model      = new WarehouseMoving();

    //     $model->line_no             = Yii::$app->session->get('Rules')['comp_id'].$Header->SourceDocNo;
    //     $model->source_id           = $Header->id;
    //     $model->DocumentNo          = $Header->DocumentNo;        
    //     $model->PostingDate         = $Header->PostingDate;
    //     $model->TypeOfDocument      = 'Consumption';
    //     $model->SourceDoc           = $Order->id;
    //     $model->SourceDocNo         = $Order->no; 
    //     //$Item 			            = Items::find()->where(['No' => $Bom->item_no])->one();
    //     $model->item                = $Bom->items->id;
    //     $model->ItemNo              = $Bom->items->No;
    //     $model->Description         = $Bom->items->description_th;
    //     // จำนวนที่ตัด Consumption 
    //     // มากจาก จำนวนที่ทำไว้ใน Bom
    //     // ถ้ามีการ Output ติดลบ(-)   ให้จำนวน consumption ติดบวก(+)
    //     // 
    //     $model->Quantity            = $output->value->Quantity < 0 ? abs($output->value->Quantity) : ($Bom->quantity * $output->value->Quantity) * -1;
    //     $model->QtyToMove           = $Bom->quantity * $output->value->Quantity;
    //     $model->QtyMoved            = 0;
    //     $model->QtyOutstanding      = 0;        
    //     $model->DocumentDate        = date('Y-m-d');
    //     $model->user_id            = Yii::$app->user->identity->id;
    //     $model->comp_id            = Yii::$app->session->get('Rules')['comp_id'];
    //     $model->apply_to           = $output->value->id;
    //     $model->session_id         = $Header->session_id;
    //     $model->unit_of_measure    = $Bom->items->quantity_per_unit;
    //     $model->qty_per_unit       = $Bom->items->quantity_per_unit;
        
    //     $model->unit_price         = $Bom->items->StandardCost;

    //     if($model->save()){
    //         //return 'Consumption';
    //         return (object)['message'=>'Consumption','value' => $model];
    //     }else {
    //         return json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
    //     }
    // }


    // static function Shipment($saleLine,$Header,$qty){
    //     $Order                      = SaleHeader::findOne($Header->SourceDocNo);

    //     $model                      = new WarehouseMoving();
    //     $model->line_no             = Yii::$app->session->get('Rules')['comp_id'].$Header->SourceDocNo;
    //     $model->source_id           = $Header->id;
    //     $model->DocumentNo          = $Header->DocumentNo;
    //     $model->PostingDate         = $Header->PostingDate;
    //     $model->TypeOfDocument      = 'Sale';
    //     $model->SourceDoc           = $Order->id;
    //     $model->SourceDocNo         = $Order->no;
        
    //     $model->item                = $saleLine->items->id;
    //     $model->ItemNo              = $saleLine->items->No;
    //     $model->Description         = $saleLine->items->description_th;
    //     $model->Quantity            = $qty * -1;
    //     $model->QtyToMove           = $qty;
    //     $model->QtyMoved            = 0;
    //     $model->QtyOutstanding      = 0;
    //     $model->DocumentDate        = date('Y-m-d');
    //     $model->user_id            = Yii::$app->user->identity->id;
    //     $model->comp_id            = Yii::$app->session->get('Rules')['comp_id'];
    //     $model->session_id         = $Header->session_id;
    //     $model->qty_per_unit       = $saleLine->items->quantity_per_unit;
    //     $model->unit_price         = $saleLine->items->StandardCost;
    //     if($model->save()){
    //         return (object)['message'=>'Shiped','value' => $model];
    //     }else {
    //         return (object)['message'=> json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),'value' => ''];
    //     }

   
    // }

    static function stockCalculate($model){
        $JSON[] = [
            'status' => false,
            'text'      => [
                'message' => 'NULL'
            ]
        ];
       
        $Header     = new WarehouseHeader();

        if ($model !== null) {

            // Clear status (Clear array)
            unset($JSON);

            $query  = RcInvoiceLine::find()->where(['source_id' => $model->id]);
              

                if($query->exists()){
                
                    foreach ($query->all() as $key => $line) {

                        $ItemCross = $line->items->getItemCrossByCustomer($model->cust_no_);
                        $barcode = $model->customer != null 
                                    ? $model->customer->show_item_code == 1 
                                        ? $line->items->barcode == ''
                                            ? $ItemCross != null 
                                                ? $ItemCross->barcode 
                                                : $ItemCross->item_no
                                            : $line->items->barcode
                                        : $line->items->master_code 
                                    : $line->items->master_code;

                        $JSON[] = [
                                'id'        => $line->items->id,
                                'orderid'   => $model->order_id,
                                'order'     => $model->no_,
                                'item'      => $line->items->No,                                
                                //'child'     => $Header->getAssembly($line->items->ProductionBom,0),
                                'code'      => $line->items->master_code,
                                'name'      => $ItemCross ? $ItemCross->description : $line->items->description_th,
                                'name_en'   => $line->items->Description,
                                'barcode'   => $barcode,
                                'unit'      => $line->items->UnitOfMeasure,
                                'inven'     => $line->items->getInven(),
                                'qty'       => $line->quantity,
                                'need'      => $line->quantity,
                                'price'     => $line->unit_price * 1,
                                'discount'  => $line->line_discount,
                                'img'       => $line->items->getPicture(),
                                'qty_per'   => $line->items->quantity_per_unit,
                                'status'    => true,
                                'production'=> $Header->testProduce($line, $Header, $line->quantity),   
                                'shipment'  => $Header->testShipment($line, $Header, $line->quantity),                             
                                'show_code' => $barcode                          
                        ];
                    }
            }
            
        } else {

            $JSON[] = [
                'status' => false,
                'text'      => [
                    'message' => 'Error'
                ]
            ];   

        }
        return $JSON;
    }
     
}
