<?php
namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\ReserveSearch;
use admin\modules\SaleOrders\models\StockReserveSearch;

use common\models\Customer;
use common\models\Items;
use common\models\SaleHeader;
use common\models\SaleLine;
use common\models\RcInvoiceHeader;
use common\models\WarehouseHeader;
use common\models\ProductionOrder;
use common\models\ProductionOrderLine;
use common\models\TransportOrder;
use common\models\RcInvoiceLine;
use admin\models\Generater;
use yii\web\UploadedFile;
use common\models\ViewRcInvoice;
use common\models\SaleInvoiceHeader;


class ReserveController extends \yii\web\Controller
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
                    'update-sale-line'  => ['POST'],
                    'finished'          => ['POST'],
                    'import-data'       => ['POST'],
                    'prepare-invoice'   => ['POST'],
                    'prepare-stock'     => ['POST'],
                    'create-invoice'    => ['POST'],
                    'create-stock'      => ['POST'],
                    'delete-line'       => ['POST'],
                    'load-line'         => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ReserveSearch();

        $mainOption     = new \common\models\Options;
        $AutoCutStock   = $mainOption->autoCutStock;
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->andFilterWhere(['NOT IN', 'sale_header.status', ['Checking']]);
        $dataProvider->query->andFilterWhere(['IN','sale_header.extra',['reserve','wizard','backward']]);
        $dataProvider->query->orderBy(['id' => SORT_DESC]);


        return $this->render('index', [
            'searchModel'   => $searchModel,
            //'confirmed'     => $confirmed,
            //'checking'      => $checking,
            'dataProvider'  => $dataProvider,  
            'AutoCutStock' => $AutoCutStock
        ]);
    }

    public function actionIndexCutstock()
    {
        $searchModel = new StockReserveSearch();        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['IN','sale_header.extra',['reserve','wizard','backward']]);
        $dataProvider->query->orderBy(['order_date' => SORT_DESC]);


        return $this->render('index-cutstock', [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider     
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
                        // 'pdftohtml_path'    =>  'C:\poppler\poppler-0.68.0\bin\pdftohtml.exe',
                        // 'pdfinfo_path'      =>  'C:\poppler\poppler-0.68.0\bin\pdfinfo.exe',       
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

    public function actionUpdate($id)
    {
        $model          = SaleHeader::findOne($id);
        $text           = '';
        $countPages     = 1;

        $transaction    = Yii::$app->db->beginTransaction();
        try {

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
                //$model->confirm = 0;
                //$model->save();
            }

            $transaction->commit();  
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            return $this->redirect(['/SaleOrders/reserve/index']);
           
        }

        return $this->render('update',[
            'model' => $model,
            'text' => $text,
            'page' => $countPages
        ]);
         
    }


    public function actionDelete($order)
    {
        $lot_session    = strtotime(date('Y-m-d H:i:s'));
        $transaction    = Yii::$app->db->beginTransaction();
       
        try {
            if(WarehouseHeader::find()->where(['order_id' => $order, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists()){
                Yii::$app->session->setFlash('warning', Yii::t('common','Not Allow'));
            }else{            
                if(($inv    = Yii::$app->request->post('inv')) !== null){
                    if(($Invoice    = RcInvoiceHeader::findOne($inv)) !==null){
                        self::DeleteLog((Object)['table' => 'rc_invoice_header','field' => 'id','value' => $Invoice->id, 'lot' => $lot_session]);
                        sleep(1);
                        self::DeleteLog((Object)['table' => 'rc_invoice_header','field' => 'no_','value' => $Invoice->no_, 'lot' => $lot_session]);
                        if(RcInvoiceLine::deleteAll(['source_id' => $Invoice->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])){  
                            $Invoice->delete();
                        }else{
                            $transaction->rollBack();
                        }
                    }
                }

                if(($SaleHeader = SaleHeader::findOne($order)) !==null){
                    self::DeleteLog((Object)['table' => 'sale_header','field' => 'id','value' => $order, 'lot' => $lot_session]);
                    sleep(1);
                    self::DeleteLog((Object)['table' => 'sale_header','field' => 'no','value' => $SaleHeader->no, 'lot' => $lot_session]);
                    if(SaleLine::deleteAll(['sourcedoc' => $order, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])){                    
                        $SaleHeader->delete();
                    }else{
                        $transaction->rollBack();
                    }
                }
            }

            $transaction->commit();  
            return $this->redirect(['/SaleOrders/reserve/index']);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            return $this->redirect(['/SaleOrders/reserve/index']);
           
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


    public function actionDeleteLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = Yii::t('common','Success');

        $transaction    = Yii::$app->db->beginTransaction();
        try {
            $model      = SaleLine::findOne($data->id);
            $model->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);                
        }

        return json_encode([
            'status'     => $status,
            'message'   => $message
        ]);
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
                'term'      => $model->payment_term ? $model->payment_term : 0,
                'vat_percent' => $model->genbus_postinggroup != 4 ? 7 : 0,
                'include_vat' => $model->vatbus_postinggroup == "01" ?  0 : 1 // 0=Vat ใน, 1=Vat นอก
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

        $customer       = Customer::findOne($data->headers->customer->id);
        $rawdata        = $data->line;
        $items          = new Items();
        $list = [];
         
        if(count($rawdata) > 0){
            
            foreach ($rawdata as $key => $value) {
                
                
                $list[] = $items->getItemCustomer((Object)[
                                'customer'  => $customer->id,
                                'search'    => $value->item,
                                'item'      => $value->item,
                                'qty'       => $value->qty,
                                'price'     => $value->price,
                                'sumline'   => $value->sumline,
                                'discount'  => $value->discount,
                            ]);

                /*

                // ถ้าไม่เจอ ให้เข้าไปหาใน item cross reference
                $item = Items::findOne(['barcode' => $value->item]);
                if($item !== null){

                    $ItemCross = $item->getItemCrossByCustomer($customer->id);

                    $list[] = [
                        'id'            => $ItemCross ? $ItemCross->id : $item->id,
                        'name'          => $ItemCross ? $ItemCross->description : $item->description_th,
                        'name_en'       => $item->Description,
                        'barcode'       => $item->barcode,
                        'unit'          => $item->UnitOfMeasure,
                        'code'          => $customer !==null ? $customer->show_item_code == 1 ? $item->barcode : $item->master_code : $item->master_code ,
                        'qty'           => $value->qty,
                        'price'         => $value->price * 1,
                        'status'        => true,
                        'discount'      => $value->discount ? $value->discount : 0
                    ];
                }else{
                    
                        // ถ้าลูกค้าผูกกับ สำนักงานใหญ่
                        // ให้ไปเทียบ code จากสำนักงานใหญ่ 
                    
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
                            'price'         => $value->price * 1,
                            'status'        => true,
                            'discount'      => 0
                        ];
                    }else{
                        $list[] = [
                            'id'            => 0,
                            'code'          => $value->item,
                            'name'          => Yii::t('common','unknown'),
                            'qty'           => $value->qty,
                            'price'         => $value->price  * 1,
                            'status'        => false,
                            'discount'      => 0
                        ];
                    }
                    
                    
                }*/
            }
        }

        return $this->asJson([
            'data'      => $data,
            'item'      => $list
        ]);

    }

    public function actionImportData(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $customer       = Customer::findOne($data->headers->customer->id);
        $items          = new Items();

        $list           = [];
        $produce        = [];


        foreach ($data->line->item as $key => $value) {

            $list[] = $items->getItemCustomer((Object)[
                'customer'  => $customer->id,
                'search'    => $value->item,
                'item'      => $value->item,
                'qty'       => $value->qty,
                'price'     => $value->price,
                'sumline'   => $value->sumline,
                'discount'  => $value->discount,
            ]);
        }



        return json_encode([
            'data'      => $data,
            'item'      => $list,
            'produce'   => $produce
        ]);

    }

    public function actionLoadLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $model      = $this->findModel($data->headers->header->id);
        $customer   = Customer::findOne($data->headers->customer->id);
        $SaleLine   = SaleLine::find()
                        ->where(['sourcedoc' => $model->id])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->all();

        $list = [];
        foreach ($SaleLine as $key => $value) {
            // ถ้าไม่เจอ ให้เข้าไปหาใน item cross reference
            $item = Items::findOne($value->item);
            if($item !== null){

                $ItemCross = $item->getItemCrossByCustomer($data->headers->customer->id);
                $list[] = [
                    'saleline'      => $value->id,
                    'id'            => $item->id,
                    'bom'           => $item->ProductionBom,
                    'make'          => $item->production ? true : false,
                    'name'          => $ItemCross ? $ItemCross->description : $item->description_th,
                    'name_en'       => $item->Description,
                    'barcode'       => $item->barcode,
                    'unit'          => $item->UnitOfMeasure,
                    'code'          => $customer !==null ? $customer->show_item_code == 1 ? $item->barcode : $item->master_code : $item->master_code ,
                    'qty'           => $value->quantity,
                    'price'         => $value->unit_price * 1,
                    'status'        => true,
                    'discount'      => $value->line_discount ? $value->line_discount : 0
                ];
            }else{
                /* 
                    ถ้าลูกค้าผูกกับ สำนักงานใหญ่
                    ให้ไปเทียบ code จากสำนักงานใหญ่ 
                */
                $customer_list = [];
                
                if($customer !== null){

                    $customer_list[]        = $customer->id;

                    if($customer->child > 0){
                        // เข้าไปหา 1 level
                        $child              = Customer::findOne($customer->child);
                        $customer_list[]    = $child !==null ? $child->id : 0;
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
                        'bom'           => $item_cross->ProductionBom,
                        'make'          => false,
                        'name'          => $item_cross->description_th,
                        'name_en'       => $item_cross->Description,
                        'barcode'       => $item_cross->barcode,
                        'unit'          => $item_cross->UnitOfMeasure,
                        'code'          => $item_cross->master_code,
                        'qty'           => $value->quantity,
                        'price'         => $value->unit_price * 1,
                        'status'        => true
                    ];

                }else{

                    $list[] = [
                        'id'            => 0,
                        'bom'           => '',
                        'make'          => false,
                        'code'          => $value->item,
                        'name'          => Yii::t('common','unknown'),
                        'qty'           => $value->quantity,
                        'price'         => $value->unit_price  * 1,
                        'status'        => false
                    ];
                    
                }
                
            }
        }

        return $this->asJson([
            'data'      => $data,
            'item'      => $list,
            'order'     => [
                'id'    => $model->id,
                'date'  => $model->order_date,
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

    protected function createProductionBill($list,$order){
        
        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];
        $head           = [];

        if(count($list) > 0){

            $transaction    = Yii::$app->db->beginTransaction();
            try {

                // ลบใบอื่นทิ้ง ใช้ใบล่าสุด
                $AllProduction  = ProductionOrder::find()
                                ->where(['order_id' => $order])
                                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                ->all();
                foreach ($AllProduction as $key => $pdr) {
                    // ลบ Line ก่อน
                    // แล้วลบใบงานตัวเอง
                    ProductionOrderLine::deleteAll(['source_id' => $pdr->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    $pdr->delete();
                }
                

                $model  = new ProductionOrder();
                $no     = $model->number;
                if($no->status==200){

                    $header = $model->createHeader((Object)[
                        'no'        => $no->no,
                        'order_id'  => $order
                    ]);

                    $head = [
                        'id'        => $header->model->id,
                        'no'        => $header->model->no,
                        'status'    => $header->status,
                        'message'   => $header->message
                    ];

                    if($header->status==200){

                        

                        foreach ($list as $key => $pd) {
                            // Create Line
                            $line   = new ProductionOrderLine();
                            $line->source_id    = $header->model->id;
                            $line->item         = $pd->id;
                            $line->code         = $pd->code;
                            $line->name         = $pd->name;
                            $line->quantity     = $pd->qty;
                            $line->measures     = 'PCS';
                            $line->comp_id      = Yii::$app->session->get('Rules')['comp_id'];

                            if($line->save()){                        
                                $raws[] = [
                                    'id'    => $pd->id,
                                    'name'  => $pd->name,
                                    'qty'   => $pd->qty,
                                    'code'  => $pd->code,
                                    'name'  => $pd->name
                                ];
                            }
                        }
                    }

                }
                
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status = 500;
                $message = Yii::t('common','{:e}',[':e' => $e]);
                    
            }

        }

        return (Object)([
            'status'        => $status,
            'message'       => $message,
            'header'        => $head,
            'data'          => $raws
        ]);   
    }



    public function actionCreateSaleLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $produce        = [];
        
        $customer       = Customer::findOne($data->headers->customer->id);
        $customer->payment_term = $data->headers->customer->term;
        $customer->save(false);

        $list           = [];  
        $message        = '';

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
            $model->status          = 'Checking';
            $model->paymentdue      = date('Y-m-d',strtotime($orderDate . "+ ".$customer->payment_term." days"));
            $model->payment_term    = $customer->payment_term;
            $model->discount        = 0;
            $model->percent_discount= 0;
            $model->confirm         = count($data->line);
            $model->confirm_by      = 1; // Auto confirm
            $model->ext_document    = $data->headers->header->po;
            $model->remark          = $data->headers->header->remark;
            $model->session_id      = $data->headers->session->id;
            $model->extra           = 'reserve';
            $model->reserve_inv_no  = $data->headers->header->inv;

            if($model->save()){

                // ออกใบสั่งผลิต
                $produce    = isset($data->produce) 
                                ? self::createProductionBill($data->produce, $model->id) 
                                : [];

                // ลบรายการออกก่อน เพื่อป้องกันการซ้ำจากการคลิกรอบที่แล้ว
                $ClearSaleLine  = SaleLine::deleteAll(['sourcedoc' => $model->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                
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
                        $CreateSaleLine->quantity_to_invoice = $value->qty; // Auto Confirm
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
                        $CreateSaleLine->stock_reserve  =  $item->ProductionBom != '' ? 0 : 1;
                
                        if($CreateSaleLine->save()){
                            $list[] = [
                                'id'            => $item->id,
                                'name'          => $value->name,
                                'name_en'       => $item->Description,
                                'barcode'       => $item->barcode,
                                'unit'          => $item->UnitOfMeasure,
                                'code'          => $item->master_code,
                                // 'code'          => $customer !==null 
                                //                     ? $customer->show_item_code == 1 
                                //                         ? $item->barcode 
                                //                         : $item->master_code 
                                //                     : $item->master_code ,
                                'qty'           => $value->qty,
                                'price'         => $value->price,
                                'discount'      => $value->discount,
                                'status'        => true
                            ];
                            $message = 'Done';
                        }else{
                            //$transaction->rollBack();
                            $message = json_encode($CreateSaleLine->getErrors(),JSON_UNESCAPED_UNICODE);
                        }
                    }                     
                }
                $transaction->commit();              
            }else{
                $transaction->rollBack();
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }

            return json_encode([
                'status'    => 200,
                'order'     => [
                                'id'        => $model->id,
                                'no'        => $model->no,
                                'balance'   => $model->balance
                            ],
                'item'      => $list,
                'message'   => $message,
                'produce'   => $produce
            ]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
                'status'        => 500,
                'message'       => Yii::t('common','Error'),
                'suggestion'    => Yii::t('common','{:e}',[':e' => $e]),
                'data'          => $data,
                'produce'       => $produce
            ]);   
                
        }

    }


    public function actionUpdateSaleLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $produce        = [];
        
        $customer       = Customer::findOne($data->headers->customer->id);
        $customer->payment_term = $data->headers->customer->term;
        $customer->save(false);

        $list           = [];  
        $QtyToConfirm   = 0;
        $status         = 200;
        $message        = Yii::t('common','Success');
        $model          = SaleHeader::findOne($data->headers->header->id); 

        $transaction    = Yii::$app->db->beginTransaction();
        try {
            
            
            
            $model->customer_id     = $customer->id;
            $model->sale_address    = $customer->fulladdress['address'];
            $model->bill_address    = $customer->fulladdress['address'];
            $model->ship_address    = $customer->fulladdress['address'];

            $orderDate              = str_replace('-', '/', $data->headers->header->date);
            $model->ship_date       = date('Y-m-d',strtotime($orderDate));
            $model->order_date      = date('Y-m-d',strtotime($orderDate));
            

            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->sales_people    = Yii::$app->session->get('Rules')['sale_code'];
            $model->sale_id         = Yii::$app->session->get('Rules')['sale_id'];
            
            $model->vat_type        = $data->headers->header->incvat;
            $model->vat_percent     = $data->headers->header->vat;
            $model->include_vat     = $data->headers->header->incvat;
            $model->balance_befor_vat = 0;

            $model->create_date     = date('Y-m-d H:i:s');
            $model->status          = 'Checking';
            $model->paymentdue      = date('Y-m-d',strtotime($orderDate . "+ ".$customer->payment_term." days"));
            $model->payment_term    = $customer->payment_term;
            $model->discount        = 0;
            $model->percent_discount= 0;
            
            $model->confirm_by      = Yii::$app->user->identity->id; // Auto confirm
            $model->ext_document    = $data->headers->header->po;
            $model->remark          = $data->headers->header->remark;
            $model->session_id      = Yii::$app->session->getId();
            $model->extra           = 'reserve';
            $model->reserve_inv_no  = $data->headers->header->inv;

            if($model->save()){
                // $StoreLineId = [];
                // foreach ($data->line as $key => $line) {    
                //     $StoreLineId[] = $line->saleline;                       
                // }

               

                //SaleLine::deleteAll(['sourcedoc' => $model->id]);

                foreach ($data->line as $key => $value) {   
                               
                    $item = Items::findOne($value->id);

                    // ถ้ามีให้ update 
                    // ถ้าไม่มี ให้ลบทิ้ง
                    $UpdateSaleLine = SaleLine::findOne(isset($value->saleline) ? $value->saleline : null);
                    if($UpdateSaleLine != null){ // Exists
                        
                        $UpdateSaleLine->sourcedoc          = $model->id;
                        $UpdateSaleLine->order_no           = $model->no;
                        $UpdateSaleLine->description        = $value->name;
                        $UpdateSaleLine->item               = $item->id;
                        $UpdateSaleLine->item_no            = $value->code;
                        $UpdateSaleLine->quantity           = $value->qty;
                        $UpdateSaleLine->quantity_to_invoice = $value->qty; // Auto Confirm
                        $UpdateSaleLine->unit_price         = $value->price;
                        $UpdateSaleLine->line_discount      = isset($value->discount) ? $value->discount : 0;
                        $UpdateSaleLine->create_date        = date('Y-m-d H:i:s');
                        $UpdateSaleLine->vat_percent        = $model->vat_percent;
                        $UpdateSaleLine->user_id            = Yii::$app->user->identity->id;
                        $UpdateSaleLine->api_key            = $model->session_id;
                        $UpdateSaleLine->comp_id            = $model->comp_id;                        
                        $UpdateSaleLine->save_order         = 'saved';
                        $UpdateSaleLine->unit_price_exvat   = ($value->price) ? ($value->price * 100) /107 : 0;
                
                        $UpdateSaleLine->save();

                            $list[] = [
                                'saleline'      => $UpdateSaleLine->id,
                                'id'            => $item->id,
                                'bom'           => $item != null ? $item->ProductionBom : '',
                                'name'          => $value->name,
                                'name_en'       => $item->Description,
                                'barcode'       => $item->barcode,
                                'unit'          => $item->UnitOfMeasure,
                                'code'          => $customer !==null 
                                                    ? ($customer->show_item_code == 1 
                                                        ? $UpdateSaleLine->item_no
                                                        : $item->master_code) 
                                                    : $UpdateSaleLine->item_no ,
                                'qty'           => $UpdateSaleLine->quantity,
                                'price'         => $UpdateSaleLine->unit_price,
                                'discount'      => $UpdateSaleLine->line_discount,
                                'status'        => true
                            ];

                        //$message[]  = ['id' => $item->id, 'status' => 'Update', 'saleline' => $UpdateSaleLine->id];
                        $QtyToConfirm+= $UpdateSaleLine->quantity;

                        
                    }else{

                        // ลบ                        
                        // $removeLine = SaleLine::findOne(isset($value->saleline) ?: null);
                        // if($removeLine != null){
                        //     $removeLine->delete();
                        //     //$message[]  = ['id' => $item->id, 'status' => 'Remove', 'saleline' => $value->saleline];
                        // }else{

                            // สร้างใหม่
                            $CreateSaleLine                 = new SaleLine();
                            $CreateSaleLine->order_no       = $model->no;
                            $CreateSaleLine->description    = $value->name;
                            $CreateSaleLine->item           = $item != null ? $item->id : '';
                            $CreateSaleLine->item_no        = $value->code;
                            $CreateSaleLine->quantity       = $value->qty;
                            $CreateSaleLine->quantity_to_invoice = $value->qty; // Auto Confirm
                            $CreateSaleLine->unit_price     = $value->price;
                            $CreateSaleLine->line_discount  = isset($value->discount) ? $value->discount : 0;
                            $CreateSaleLine->create_date    = date('Y-m-d H:i:s');
                            $CreateSaleLine->vat_percent    = $model->vat_percent;
                            $CreateSaleLine->user_id        = Yii::$app->user->identity->id;
                            $CreateSaleLine->api_key        = $model->session_id;
                            $CreateSaleLine->comp_id        = $model->comp_id;
                            $CreateSaleLine->sourcedoc      = $model->id;
                            $CreateSaleLine->save_order     = 'saved';
                            $CreateSaleLine->unit_price_exvat= ($value->price) ? ($value->price * 100) /107 : 0;
                    
                            $CreateSaleLine->save();
                            $list[] = [
                                'saleline'      => $CreateSaleLine->id,
                                'id'            => $item != null ? $item->id : 1414,
                                'bom'           => $item != null ? $item->ProductionBom : '',
                                'name'          => $value->name,
                                'name_en'       => $item != null ? $item->Description: '',
                                'barcode'       => $item != null ? $item->barcode: '',
                                'unit'          => $item != null ? $item->UnitOfMeasure: '',
                                'code'          => $customer !==null 
                                                    ? ($customer->show_item_code == 1 
                                                        ? $CreateSaleLine->item_no 
                                                        : ($item != null ? $item->master_code : '')) 
                                                    : $CreateSaleLine->item_no ,
                                'qty'           => $CreateSaleLine->quantity,
                                'price'         => $CreateSaleLine->unit_price,
                                'discount'      => $CreateSaleLine->line_discount,
                                'status'        => true
                            ];

                            $QtyToConfirm+= $CreateSaleLine->quantity;
                            //$message[]  = ['id' => $item->id, 'status' => 'Create', 'saleline' => $CreateSaleLine->id];
                        //}

                        
                    }
                    
                    
                }

                

                $model->confirm         = $QtyToConfirm; // Auto
                $model->balance         = $model->sumtotal->total;
                //$model->confirm         = 0;
                $model->save();


                // ออกใบสั่งผลิต
                $produce    = isset($data->produce) 
                                ? self::createProductionBill($data->produce, $model->id) 
                                : [];


            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);                
        }

        return json_encode([
            'status'    => $status,
            'order'     => [
                            'id'        => $model->id,
                            'no'        => $model->no,
                            'balance'   => $model->balance
                        ],
            'item'      => $list,
            'message'   => $message,
            'produce'   => $produce
        ]);

    }

    public function actionPrepareInvoice(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = $data->id;
        $status         = 200;
        $invNo          = '';
        $raws           = [];

        $model          = $this->findModel($id);

        if($model->reserve_inv_no != ''){     
            $invNo          = $model->reserve_inv_no;
        }else{
            $NoSeries   = new Generater();
            //$invNo      = $NoSeries->GenNumber('vat_type','vat_value',($model->vat_percent)? $model->vat_percent : 0 ,false);
            //$invNo      = Generater::NextRuning('vat_type','vat_value', ($model->vat_percent)? $model->vat_percent : 0,false);  
            $invNo      =\admin\models\Series::invoice('view_rc_invoice', 'no_', 'all', 'Sale', $model->vat_percent);
        }  
        
        $SaleLine   = SaleLine::find()
                    ->where(['sourcedoc' => $model->id])
                    //->andWhere(['>', 'quantity_to_invoice', 0])
                    ->all();

        foreach ($SaleLine as $key => $line) {
            $raws[] = [
                'id'        => $line->id,
                'code'      => $line->items->master_code,
                'name'      => $line->description,
                'qty'       => $line->quantity,
                'item'      => $line->item,
                //'shipment'  => $line->shipLine,
                'shipment'  => [],
                //'stock'     => $line->items->invenByCache,
                'stock'     => $line->items->ProductionBom > 0
                                ? ($line->items->myItems ? $line->items->myItems->last_possible : 0)
                                : ($line->items->myItems ? $line->items->myItems->last_stock : 0),
                'confirm'   => $line->quantity_to_invoice
            ];
        }

        return json_encode([
            'status'    => $status,
            'id'        => $model->id,
            'no'        => $invNo,
            'ext'       => $model->ext_document,
            'date'      => $model->order_date,
            'raws'      => $raws,
            'type'      => 'inv'
        ]);

    }

    public function actionPrepareStock(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = $data->id;
        $status         = 200;
        $invNo          = '';
        $raws           = [];

        $model      = $this->findModel($id);
        $NoSeries   = new Generater();
        $invNo      = Generater::NextRuning('warehouse_moving','no', 'all',true);    
        
        $SaleLine   = SaleLine::find()
                    ->where(['sourcedoc' => $model->id])
                    //->andWhere(['>', 'quantity_to_invoice', 0])
                    ->all();

        foreach ($SaleLine as $key => $line) {
            $raws[] = [
                'id'        => $line->id,
                'code'      => $line->items->master_code,
                'name'      => $line->description,
                'qty'       => $line->quantity,
                'item'      => $line->item,
                //'stock'     => $line->items->invenByBom,
                'stock'     => $line->items->ProductionBom > 0
                                ? $line->items->invenByCache
                                : $line->items->last_stock,
                'confirm'   => $line->quantity_to_invoice
            ];
        }

        return json_encode([
            'status'    => $status,
            'id'        => $model->id,
            'no'        => $invNo,
            'ext'       => $model->ext_document,
            'date'      => $model->ship_date,
            'raws'      => $raws,
            'type'      => 'stock'
        ]);

    }

    protected function checkNumber($no){
        return ViewRcInvoice::find()->where(['no_' => $no, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
    }

    public function actionCreateInvoice(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = $data->id;
        $order_date     = $data->date;
        $ext            = $data->ext;
        $raw            = [];
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = '';
        $returnId       = 0;
        $invNo          = '';
        $source         = $data->source;
        $shipAll        = 1; // 0=Ship some item, 1=ShipAll

         
        $transaction    = Yii::$app->db->beginTransaction();
        try{

            //$Already        = RcInvoiceHeader::find()->where(['no_' => $data->no])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            $Already    = self::checkNumber($data->no);
            
            if($Already != null){ // ถ้ามีอยู่แล้ว 
                // ให้ตรวจการว่าเป็นเอกสารที่จองหรือเปล่า 
                // ถ้าจองไว้ สามารถแก้ไขรายการได้ (ลบรายการเก่าทิ้งไปเลย แล้วใส่รายการเข้าไปใหม่)
                // แต่ถ้าไม่ได้จอง ให้เตือนกลับไปว่า เอกสารมีแล้ว	
            
                foreach ($Already as $key => $rc) {
                    if($rc->status == 'Open'){ // ลบใบเก่า
                        $SaleInv = SaleInvoiceHeader::findOne($rc->id);
                        if($SaleInv != null){
                            if($SaleInv->reserved == 1){
                                $SaleInv->delete();
                            }else{ // ไม่ใช่ใบงานที่จองไว้ ไม่ให้ทับ เพราะจะทำให้เปลี่ยนข้อมูลคนอื่น
                                // Reject 
                                $SaleInvoiceHeader  = SaleInvoiceHeader::find()
                                                    ->where(['no_' => $data->no])
                                                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                                    ->one();

                                $message            = Yii::t('common','Cancel');
                                $suggestion         = $SaleInvoiceHeader->no_.' '.Yii::t('common','ไม่สามารถใช้ใบงานนี้ได้');

                                return json_encode([
                                    'status'    => 405,
                                    'inv'       => $SaleInvoiceHeader->id,
                                    'no'        => $SaleInvoiceHeader->no_,
                                    'message'   => $message,
                                    'suggestion'=> $suggestion,
                                    'raw'       => $raw 
                                ]);  
                                exit;   
                            }
                        }
                        
                    }else{ // Posted
                        // Reject 
                        $RcInvoiceHeader    = RcInvoiceHeader::find()->where(['no_' => $data->no])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();
                        $message            = Yii::t('common','Cancel');
                        $suggestion         = $RcInvoiceHeader->no_.' '.Yii::t('common','Already exists');

                        return json_encode([
                            'status'    => 405,
                            'inv'       => $RcInvoiceHeader->id,
                            'no'        => $RcInvoiceHeader->no_,
                            'message'   => $message,
                            'suggestion'=> $suggestion,
                            'raw'       => $raw 
                        ]);  
                        exit;   
                    }
                } 

            }

            // DO CREATE
        
            $model                      = $this->findModel($id);
            //$NoSeries                   = new Generater();
            
            // Create Invoice header
            $inv_header                 = new RcInvoiceHeader();
            
            $UpdateSeries               = Generater::CreateNextNumber('vat_type','vat_value',($model->vat_percent)? $model->vat_percent : 0, $data->no);
            $inv_header->no_            = $data->no;
            //$inv_header->no_ 			= $NoSeries->GenNumber('vat_type','vat_value',($model->vat_percent)? $model->vat_percent : 0 ,false);
            
            $inv_header->cust_no_ 		= $model->customer_id;
            $inv_header->cust_name_		= $model->customer->name;
            $inv_header->document_no_	= $model->no;
            $inv_header->posting_date 	= $order_date;
            $inv_header->doc_type 		= 'Sale';            
            $inv_header->order_id 		= $model->id;
            $inv_header->sales_people 	= $model->sales_people;
            $inv_header->sale_id 		= $model->sale_id;
            $inv_header->cust_code 		= $model->customer->code;
            $inv_header->order_date	 	= $model->order_date;
            $inv_header->ship_date 		= $model->ship_date;
            $inv_header->cust_address 	= $model->customer->address;
            $inv_header->cust_address2 	= $model->customer->address2;
            $inv_header->phone 			= $model->customer->phone;
            $inv_header->district 		= $model->customer->district;
            $inv_header->city 			= $model->customer->city;
            $inv_header->province 		= $model->customer->province;
            $inv_header->postcode 		= $model->customer->postcode;
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
            $inv_header->session_id     = Yii::$app->session->getId();
            $inv_header->extra          = 'reserve';

            if($inv_header->save()){
                $returnId   = $inv_header->id;
                $invNo      = $inv_header->no_;
                //$SaleLine   = SaleLine::find()->where(['sourcedoc' => $model->id])->all();

                foreach ($source as $line) {
                    if($line->stock!='true'){
                        $shipAll        = 0; // 0=Ship some item, 1=ShipAll
                    }

                    $SaleLine                           = SaleLine::findOne($line->id);
                    $RcInvoiceLine                      = new RcInvoiceLine();
                    $RcInvoiceLine->type 			    = 'Item';
                    $RcInvoiceLine->item		 	    = $SaleLine->item;
                    $RcInvoiceLine->measure             = $SaleLine->unit_measure;
                    $RcInvoiceLine->doc_no_ 		    = $inv_header->no_;
                    $RcInvoiceLine->line_no_ 		    = $SaleLine->items->id;
                    $RcInvoiceLine->source_id 		    = $inv_header->id;
                    $RcInvoiceLine->customer_no_	    = $inv_header->cust_no_;
                    $RcInvoiceLine->code_no_		    = $SaleLine->item_no;
                    $RcInvoiceLine->code_desc_		    = $SaleLine->description;
                    $RcInvoiceLine->quantity 		    = $line->qty;
                    $RcInvoiceLine->unit_price 		    = $SaleLine->unit_price * 1;
                    $RcInvoiceLine->line_discount       = $SaleLine->line_discount *1;
                    $RcInvoiceLine->vat_percent 	    = $inv_header->vat_percent;
                    $RcInvoiceLine->order_id 		    = $inv_header->order_id;
                    $RcInvoiceLine->source_doc		    = $model->no;
                    $RcInvoiceLine->source_line		    = $SaleLine->id;
                    $RcInvoiceLine->session_id 		    = $inv_header->session_id;
                    $RcInvoiceLine->posting_date        = $inv_header->posting_date;
                    $RcInvoiceLine->quantity_to_stock   = $line->stock == 'true' ? $line->qty : 0; // ดึงค่าช่องนี้ไปตัดสต๊อก
                    $RcInvoiceLine->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

                    if($RcInvoiceLine->save()){
                        
                        $SaleLine->stock_reserve = 0;  // ยกเลิกจอง
                        $SaleLine->save();



                        $raw[]      = (Object)[
                            'status'    => 200,
                            'id'        => $line->id,
                            'message'   => Yii::t('common','Done'),
                            'suggestion'=> ''
                        ];
                    }else{
                        $raw[]      = (Object)[
                            'status'    => 500,
                            'id'        => $line->id,
                            'message'   => Yii::t('common','Error'),
                            'suggestion'=> json_encode($RcInvoiceLine->getErrors(),JSON_UNESCAPED_UNICODE)
                        ];
                    } 

                    
                    
                } 

                $inv_header->ship_all    = $shipAll;
                $inv_header->save();
                
            }

            // update order status
            $model->status      = 'Invoiced';
            $model->order_date  = $order_date;
            $model->save();

            $transaction->commit();  
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','Error');
            $suggestion = Yii::t('common','{:e}',[':e' => $e]);
        }

        return json_encode([
            'status'    => $status,
            'inv'       => $returnId,
            'no'        => $invNo,
            'message'   => $message,
            'suggestion'=> $suggestion,
            'raw'       => $raw 
        ]);
        

        exit;  

         

    }



    public function actionFindItems(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 3 ? '' : 5;
         
        $customer       = Customer::findOne($data->customer);
         /* 
            ถ้าลูกค้าผูกกับ สำนักงานใหญ่
            ให้ไปเทียบ code จากสำนักงานใหญ่ 
        */
        $customer_list = [];
        
        if($customer !== null){

            $customer_list[]        = $data->customer;

            if($customer->child > 0){
                // เข้าไปหา 1 level
                $child              = Customer::findOne($customer->child);
                $customer_list[]    = $child !==null ? $child->id : 0;
            }
        }
        

        // หาจากลูกค้าก่อน 
        $cross          = true;
        $query          = Items::find()
                        ->select('items.id as id, 
                        item_cross_reference.item_no as master_code, 
                        items.description as description_th, 
                        items.Description, 
                        item_cross_reference.barcode, 
                        items.UnitOfMeasure, 
                        item_cross_reference.description as alias')
                        ->joinWith('itemCrossReference')
                        ->where(['or',
                            ['item_cross_reference.barcode' => trim($data->search)],
                            ['item_cross_reference.item_no' => trim($data->search)]
                        ])
                        ->andWhere(['IN','item_cross_reference.reference_no',$customer_list])
                        ->andWhere(['item_cross_reference.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if($query->count() <= 0){
            $cross          = false;
                       
            $query          = Items::find()
                            ->select('items.id as id, 
                            items.master_code as master_code, 
                            items.description as description_th, 
                            items.Description, 
                            items.barcode, 
                            items.UnitOfMeasure, 
                            items.ProductionBom,
                            item_cross_reference.description as alias')
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

            $items[]= [
                'id'        => $model->id,
                'name'      => $model->alias != ''
                                ? $model->alias
                                : $model->description_th,
                'name_en'   => $model->Description,
                'code'      => $customer !==null 
                                    ? ($customer->show_item_code == 1 
                                        ? ($model->barcode != '' 
                                            ? $model->barcode 
                                            : $model->master_code) 
                                        : $model->master_code) 
                                    : $model->master_code ,
                'barcode'   => $model->barcode,
                'lastprice' => ($lastPrice !== null ? $lastPrice->unit_price : 0) * 1,
                'bom'       => (int)$model->ProductionBom,
                'make'      => $model->ProductionBom > 0
                                ? true
                                : false
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

        $order          = [];
        $status         = 200;
        $message        = Yii::t('common','Success');

        $transaction = Yii::$app->db->beginTransaction();
        try {  

            $model          = SaleHeader::findOne($data->order_id);           
          
            if($model!==null){
                $model->ship_date = isset($data->ship_date) ? date('Y-m-d', strtotime($data->ship_date)) : date('Y-m-d');
                $model->save();
                $SaleLine       = SaleLine::updateAll(['need_ship_date' => $model->ship_date],['sourcedoc' => $model->id]);  // Update ship date
                $order[] = [
                    'status'    => 200,
                    'message'   => [
                        'id'    => $model->id,
                        'no'    => $model->no,
                        'total' => $model->sumtotal->total
                    ]
                ];                
            }else{
                $order[] = [
                    'status'    => 404,
                    'message'   => 'Not found'
                ];
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }

        
        return json_encode([
            'status'    => $status,
            'order'     => $order,
            'message'   => $message
        ]);
    }


    public function actionCreateStock(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = '';
        $status         = 200;
        $message        = Yii::t('common','Success');
        $SHIPMENT       = '';
        $raws           = [];
        $GenSeries      = new Generater();

        foreach ($data->source as  $line) {	
            if($line->stock=='true'){		
                $SaleLine 	= SaleLine::findOne($line->id);
                // Force Count 
                $force      = $SaleLine->items->countStock->last_stock;
                //if($SaleLine->items->invenByBom < $line->qty){		
                $stock      = $SaleLine->items->ProductionBom > 0
                                ? $SaleLine->items->myItems->last_possible
                                : $SaleLine->items->myItems->last_stock;

                if($stock < $line->qty){	

                    try{ // Line Notify                                            
                                
                        $bot = \common\models\LineBot::findOne(5);
                        $msg = "\r\n".'OUT OF STOCK'."\r\n";
                        $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                        
                        $msg.= 'https://admin.ewinl.com/index.php?r=items/items/view&id='.$SaleLine->item."\r\n\r\n";
                        
                        $msg.= $SaleLine->items->master_code."\r\n";
                        $msg.= $SaleLine->description."\r\n\r\n";

                        $msg.= 'Stock '.number_format($SaleLine->items->last_stock,2)."\r\n\r\n"; 
                        $msg.= 'Need '.$line->qty."\r\n\r\n";
                        
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                        
                        $bot->notify_message($msg);					
    
                    } catch (\Exception $e) {					 
                        // $status 		= 500;
                        // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                    }	

                    return json_encode([
                        'status'    => 403,
                        'no'        => $SHIPMENT,
                        'id'        => $id,
                        'raws'      => $raws,
                        'message' 	=> Yii::t('common','Product').' '.$SaleLine->items->master_code.' '.$SaleLine->description,
                        'suggestion'=> Yii::t('common','There are not enough products.')
                    ]);
                    exit();
                }
			}
		}

        $transaction = Yii::$app->db->beginTransaction();
        try { 
            
            $model                  = SaleHeader::findOne($data->id);

            //$SHIPMENT             = Generater::NextRuning('warehouse_moving','no', 'all',true); 
            $Header                 = new WarehouseHeader();             

            $Header->line_no        = $model->id;
            $Header->PostingDate    = date('Y-m-d',strtotime($model->order_date)).' '.date('H:i:s');
            $Header->DocumentDate   = date('Y-m-d');
            $Header->TypeOfDocument = "Sale";
            $Header->SourceDocNo    = $model->id;
            $Header->DocumentNo     = $GenSeries->GenerateNoseries('Shipment',true);
            $Header->customer_id    = $model->customer_id;
            $Header->SourceDoc      = $model->no;
            $Header->order_id       = $model->id;

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
                $id         = $Header->id;
                $SHIPMENT   = $Header->DocumentNo;

                foreach ($data->source as $key => $line) {
                    
                    $SaleLine   = SaleLine::findOne($line->id);

                    if($line->stock=='true'){
                        $raws[] = [
                                'inven'     => $SaleLine->items->myItems->last_stock,                            
                                'status'    => true,
                                'production'=> $Header->producer($SaleLine, $Header, $line->qty),
                                'shipment'  => $Header->shipment($SaleLine, $Header, $line->qty)
                        ];
                    }

                }

                $model->status = 'Shiped';
                $model->save();

            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }
        
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'no'        => $SHIPMENT,
            'id'        => $id,
            'raws'      => $raws
        ]);

    }


    static function stockAdjust($model){
        $status = 200;
        $message= '';
        $JSON   = [];  
        
        $transaction = Yii::$app->db->beginTransaction();
        try { 

            $GenSeries  = new Generater();

            if ($model !== null) {
                        
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

                    $query              = RcInvoiceLine::find()->where(['source_id' => $model->id])->andWhere(['>', 'quantity_to_stock', 0]);

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
                                    'img'       => $line->items->picture,
                                    'qty_per'   => $line->items->quantity_per_unit,
                                    'status'    => true,
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
                
            } 
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => $JSON
        ]);
    }


    public function actionTransportListUpdate(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = Yii::t('common','{:e}',[':e' => 'Success']);
        $action         = 0; // 1==Create, 0==Delete

        if($data->id == 0){
            return json_encode([
                'status'    => 202,
                'message'   => Yii::t('common',"Don't have sale order"),
                'action'    => 0  
            ]); 
        }
        $transaction    = Yii::$app->db->beginTransaction();
        try {
 
            $model = TransportOrder::find()
                    ->where(['order_id' => $data->id])
                    ->andWhere(['user_id' => Yii::$app->user->identity->id])
                    ->one();

            if($model != null){
                $model->delete();
                $action     = 0;
            }else{
                $model = new TransportOrder();
                $model->order_id = $data->id;
                $model->user_id     = Yii::$app->user->identity->id;
                $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
                $model->save();
            
                $action     = 1;
            }

            $transaction->commit();  
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }

        

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'action'    => $action    
        ]);
    }

}
