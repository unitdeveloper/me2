<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\SaleHeader;
use admin\modules\SaleOrders\models\SalehearderSearch;
use admin\modules\SaleOrders\models\SaleListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\OrderSearch;
use common\models\SaleLine;
use admin\models\Generater;
use common\models\Customer;
use common\models\SalesPeople;
use common\models\TransportList;
use admin\modules\items\models\SearchPicItems;
use common\models\Items;

use common\models\ItemsHasProperty;
use common\models\Itemset;
use admin\models\FunctionCenter;

use admin\modules\SaleOrders\models\FunctionSaleOrder;

use admin\modules\tracking\models\FunctionTracking;


use common\models\TmpMenuGroup;
use common\models\VatType;
use common\models\SaleInvoiceHeader;
use common\models\SaleInvoiceLine;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


use common\models\WarehouseMoving;
use common\models\WarehouseHeader;

use admin\modules\SaleOrders\models\ViewNotInvSearch;
use common\models\ViewSaleNotInvoice;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SaleorderController implements the CRUD actions for SaleHeader model.
 */
class SaleorderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class'     => VerbFilter::className(),
                'actions'   => [
                    'delete'                    => ['POST'],
                    'clear-sale-line'           => ['POST'],
                    'update-line-box'           => ['POST'],
                    'create-saleline'           => ['POST'],
                    'load-sale-line'            => ['POST'],
                    'index-ajax'                => ['POST'],
                    'detail-ajax'               => ['POST'],
                    'detail-shipment-ajax'      => ['POST'],
                    'detail-invoice-ajax'       => ['POST'],
                    'confirm-order'             => ['POST'],
                    'shipment'                  => ['POST'],
                    'create-invoice'            => ['POST'],
                    'create-invoice-from-item'  => ['POST'],
                    'update-header-ajax'        => ['POST'],
                    'get-header-ajax'           => ['POST'],
                    'hide-order'                => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all SaleHeader models.
     * @return mixed
     */
    public function actionIndexAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $company        = Yii::$app->session->get('Rules')['comp_id'];

        $fdate          = isset($data->fdate) ? $data->fdate : date('Y');
        $tdate          = isset($data->tdate) ? $data->tdate : date('Y-m-t');
        $limit          = isset($data->limit) ? $data->limit : 50;

        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];
        $query          = SaleHeader::find()
                        ->where(['between', 
                            'DATE(order_date)', 
                            date('Y-m-d', strtotime($fdate.'-01-01')), 
                            date('Y-m-d', strtotime($tdate))
                        ])
                        ->andWhere(['NOT IN','status',['Open', 'Shiped', 'Invoiced']])
                        ->andWHere(['extra' => NULL])
                        ->andWhere(['comp_id' => $company])
                        ->orderBy(['id' => SORT_DESC])
                        ->all();
                    
        foreach ($query as $key => $model) {
            $raws[] = (Object)[
                'id'        => $model->id,
                'no'        => $model->no,
                'balance'   => $model->balance,
                'custId'    => $model->customer_id,
                'type'      => $model->extra,
                'saleId'    => $model->sale_id,
                'vat'       => $model->vat_percent,
                'incVat'    => $model->vat_type,
                'status'    => $model->status=='Checking'
                                ? ($model->confirm * 1) > 0  
                                    ? 'Confirmed'
                                    : $model->status
                                : $model->status
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }

    public function actionDetailAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $company        = Yii::$app->session->get('Rules')['comp_id'];

        $fdate          = isset($data->fdate) ? $data->fdate : date('Y-m');
        $tdate          = isset($data->tdate) ? $data->tdate : date('Y-m-t');
        $limit          = isset($data->limit) ? $data->limit : 50;

        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];
        $model          = SaleHeader::findOne($data->id);
        $saleLine       = SaleLine::find()->where(['sourcedoc' => $data->id])->all();
        foreach ($saleLine as $key => $line) {
            $raws[] = (Object)[
                'id'        => $line->id,
                'item'      => $line->item,
                'name'      => $line->description,
                'qty'       => $line->quantity * 1,
                'price'     => $line->unit_price * 1,
                'discount'  => $line->line_discount * 1,
                'confirm'   => $line->confirm * 1
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'header'    => [
                'id'        => $model->id,
                'no'        => $model->no,
                'balance'   => $model->balance * 1,
                'status'    => $model->status,
                'custId'    => $model->customer_id,
                'ship_address' => $model->ship_address == ''
                                    ? ($model->customer != null 
                                        ? ($model->customer->ship_address != '' 
                                                ? $model->customer->ship_address
                                                : $model->customer->address) 
                                        : '')
                                    : $model->ship_address ,
                'custName'  => $model->customer != null 
                                    ? ($model->customer->ship_name != '' 
                                        ? $model->customer->ship_name
                                        : $model->customer->name) 
                                    : '',
                'ship_phone' => $model->customer != null ? $model->customer->phone: '',
                'transport' => $model->customer != null
                                ? ($model->customer->transportList != null
                                    ? (string)$model->customer->transportList->id
                                    : '0')
                                : '0'
            ],
            'raws'      => $raws
        ]);
    }

    public function actionDetailShipmentAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $company        = Yii::$app->session->get('Rules')['comp_id'];

        $status         = 200;
        $message        = Yii::t('common','Success');
        $heads          = [];
        $raws           = [];
        $Header         = WarehouseHeader::find()
                        ->where(['order_id' => $data->id])
                        ->orderBy(['TypeOfDocument' => SORT_DESC, 'id' => SORT_DESC])
                        ->all();

        foreach ($Header as $key => $head) {
            
            // $Query          = WarehouseMoving::find()
            //                 ->where(['source_id' => $head->id])
            //                 ->all();

            // foreach ($Query as $key => $line) {
            //     $raws[] = (Object)[
            //         'id'        => $line->id,
            //         'item'      => $line->item,
            //         'name'      => $line->Description,
            //         'qty'       => $line->Quantity * 1,
            //         'price'     => $line->unit_price * 1,
            //         'type'      => $line->TypeOfDocument
            //     ];
            // }


            $heads[]    = [
                'id'        => $head->id,
                'type'      => $head->TypeOfDocument,
                'no'        => $head->DocumentNo,
                'status'    => $head->status,
                //'raws'      => $raws
            ];
        }


        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => $heads
        ]);
    }

    public function actionDetailInvoiceAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $model          = $this->findModel($data->id);

        $status         = 200;
        $message        = Yii::t('common','Success');
        $heads          = [];
        $raws           = [];
        $vat_percent    = 0;
        $Header         = \common\models\ViewRcInvoiceTotal::find()
                        ->where(['order_id' => $data->id])
                        ->andWhere(['comp_id' => $company])
                        ->orderBy(['id' => SORT_DESC]);

        if($Header->count() <= 0){
           // $Series     = new \admin\models\Series();
            // $heads[] = [
            //     'id' => 0,
            //     'no' => ' ',
            //     'status' => '',
            //     'type'   => 'Invoice'
            // ];
        }else{

            foreach ($Header->all() as $key => $head) {            

                $heads[]    = [
                    'id'        => $head->id,
                    'cust'      => $head->cust_no_,
                    'type'      => $head->doc_type == 'Sale' ? 'Invoice' : $head->doc_type,
                    'no'        => $head->no_,
                    'status'    => $head->status
                    //'raws'      => $raws
                ];
            }
        }


        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => $heads,
            'vat'       => $model->vat_percent,
            'no'        => $model->vat_percent > 0  
                            ? \admin\models\Series::invoiceNo('view_rc_invoice', 'no_', 'all', 'Sale', 'CT')
                            : \admin\models\Series::invoiceNo('view_rc_invoice', 'no_', 'all', 'Sale', 'IV')
            //'no'        => \admin\models\Series::invoice('view_rc_invoice', 'no_', 'all', 'Sale', $model->vat_percent),
            //'newNo'     => \admin\models\Series::invoiceNo('view_rc_invoice', 'no_', 'all', 'Sale', 'CT')
        ]);
    }

    public function actionConfirmOrder(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        $company                = Yii::$app->session->get('Rules')['comp_id'];

        $status                 = 200;
        $message                = Yii::t('common','Success');
        $model                  = $this->findModel($data->id);
        
        $model->status          = 'Checking'; // (ทำให้มีปัญหากับการ Confirm ซ้ำ) ต้องแก้ไขโดยการตรวจสอบการผลิตก่อน
        $model->confirm_date    = date('Y-m-d H:i:s');
        $model->confirm         = 0;
        if(!$model->save()){
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => [
                'id'        => $model->id,
                'status'    => $model->status
            ]
        ]);
    }
    
    public function actionIndexList(){
        return $this->render('index-list'); 
    }

    public function actionHideOrder(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        $company                = Yii::$app->session->get('Rules')['comp_id'];
        $status                 = 200;
        $message                = Yii::t('common','Success');

        $model                  = SaleHeader::findOne($data->id);
        $model->op_to_ct        = $data->set;
       
        if(!$model->save()){
            $status             = 500;
            $message            = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }
        
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'set'       => $data->set
        ]);
    }

    public function actionIndexListAjax(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        $company                = Yii::$app->session->get('Rules')['comp_id'];

        $status                 = 200;
        $message                = Yii::t('common','Success');
        
        $raws                   = [];
        
        $query                  = SaleHeader::find()
                                ->where(['comp_id' => $data->comp])
                                ->andWhere(['between','order_date', date('Y-m-d', strtotime($data->fdate)),date('Y-m-d', strtotime($data->tdate))])
                                ->andWhere(['>','confirm',0])
                                ->andWhere(['op_to_ct' => $data->order_status])
                                ->orderBy(['order_date' => SORT_DESC])
                                ->all();
        foreach ($query as $key => $model) {
            // $rows               = [];
            // $saleLine           = SaleLine::find()->where(['sourcedoc' => $model->id])->all();
            // if($saleLine != null){
            //     foreach ($saleLine as $key => $line) {
            //         $rows[]         = (Object)[
            //                             'id'    => $line->id,
            //                             'item'  => $line->item
            //                         ];
            //     }
            // }

            $raws[] = (Object)[
                'id'            => $model->id,
                'order_date'    => $model->order_date,
                'no'            => $model->no,
                'order_status'  => $model->op_to_ct,
                'shipdate'      => $model->ship_date,
                //'saleline'      => $rows,
                'cust_id'       => $model->customer ? $model->customer->id : '',
                'cust_code'     => $model->customer ? $model->customer->code : '',
                'sale_id'       => $model->salespeople ? $model->salespeople->id : '',
                'sale_code'     => $model->salespeople ? $model->salespeople->code : '',
                'sale_name'     => $model->salespeople ? $model->salespeople->name : '',
                'sale_surname'  => $model->salespeople ? $model->salespeople->surname : ''
                
                //'cust_name'     => $model->customer ? $model->customer->name : '',
                //'balance'       => $model->sumtotal->total
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }

    public function actionIndexLineAjax(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        $company                = Yii::$app->session->get('Rules')['comp_id'];

        $status                 = 200;
        $message                = Yii::t('common','Success');
        $raws                   = [];
        
        $header                 = SaleHeader::findOne($data->id);
        $query                  = SaleLine::find()->where(['sourcedoc' => $data->id])->all();
        foreach ($query as $key => $model) {
            $raws[] = (Object)[
                'id'            => $model->id,
                'source_id'     => $model->sourcedoc,
                'item'          => $model->item,
                'name'          => $model->description != "" 
                                    ? $model->description 
                                    : $model->items->description_th,
                'code'          => $model->items->master_code,
                'qty'           => $model->quantity * 1,
                'detail'        => $model->items->ProductionBom > 0
                                    ? $model->items->bomLine
                                    : [],
                'measure'       => $model->unit_measure,
                'cost'          => ($model->items->lastPurchase ? $model->items->lastPurchase->unitcost : 0) * 1,
                'price'         => $model->items->lastPrice * 1
            ];

            // CloneItem            
            $clone     = $model->items->exists 
                            ? NULL 
                            : $model->items->cloneItem($model->items,['clone' => 1]);
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'header'    => [
                'id'            => $header->id,
                'shipment'      => $header->shipment ? $header->shipment->id : NULL
            ],
            'raws'      => $raws
        ]);
    }



    public function actionIndex()
    {
        if(\common\models\Options::getSystemStatus()){
            if((Yii::$app->session->get('workyears')==NULL) || (Yii::$app->session->get('workyears')== 1970)) 
            { 
                Yii::$app->session->set('workyears',date('Y')); 
            }

            Yii::$app->session->set('workdate',date('Y-m-d')); 

            // if(Yii::$app->session->get('Rules')['rules_id'] == '')
            // {
            //     echo '<script>window.location.href = "index.php?r=site/index";</script>';
            // }

            $searchModel = new SaleListSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            
            
            // if(Yii::$app->session->get('theme')==1){
            //     $dataProvider->pagination->pageSize=20;
            // }else{
            //     $dataProvider->pagination->pageSize=50;
            // }

            //if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalehearderSearch','view'))){   
                $dataProvider->pagination->pageSize=10;
                return $this->render('index-mobile', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }else{ //if(Yii::$app->user->identity->id == 1 || Yii::$app->user->identity->id == 21){
                $dataProvider->pagination->pageSize=10;
                if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','saleorder','actionIndex','read-only'))){   
                    return $this->render('index-read-only', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);
                }else if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalesDirector','SalesDirector'))){
                    return $this->render('index-mobile', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);
                }else {
                    return $this->render('index-admin', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider
                    ]);
                }
                
            }
        }else{
            return $this->renderpartial('@admin/views/site/system-off');
        }
    }

    
    /**
     * Displays a single SaleHeader model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if(\common\models\Options::getSystemStatus()){
            $company = Yii::$app->session->get('Rules')['comp_id'];

            $model = $this->findModel($id);
            if(Yii::$app->user->identity->id!=1){  // ถ้าไม่ใช่บริษัทตัวเอง ให้ออกไป
                if($model->comp_id != Yii::$app->session->get('Rules')['comp_id']){
                    Yii::$app->session->setFlash('error', Yii::t('common','You do not have permission to access this Sale Order.'));
                    return $this->redirect(['index']);
                }
            }
            // if((int)$model->balance <= 0){   // ยกเลิก เนื่องจากบางใบงาน ส่งสินค้าฟรี (ราคาเป็น 0)
            //   return $this->redirect(['/SaleOrders/saleorder/update', 'id' => $model->id]);
            // }

            if(empty($model->customer->id)){
                return $this->redirect(['/SaleOrders/saleorder/update', 'id' => $model->id]);            
            }

            $query   = SaleLine::find()
                ->where(['sourcedoc' => $id])
                ->andwhere(['comp_id' => $company]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,

            ]);

            return $this->render('view_mobile', [
                'model' => $model,                 
                'dataProvider' => $dataProvider,
            ]);   
        }else{
            return $this->renderpartial('@admin/views/site/system-off');
        }    
    }


    public function actionViewitem()
    {
        $param  = Yii::$app->request->post('param');
        $keys   = 'SaleOrders-saleorder-viewitem&item:'.$param['itemno'].'&so:'.$param['orderno'].'&set:'.$param['pset'].'&itemset:'.$param['itemset'].'&user:'.Yii::$app->user->identity->id;
        $cache  = Yii::$app->cache;

		if(!$cache->get($keys)){
            $session = Yii::$app->session;
            $session->set('ew-set', (object)['pval' => NULL,'pid' => NULL]);  // Clear pval

            #1 Set นี้มี Item อะไรบ้าง
            #2 ในแต่ละ Item มี Property อะไรบ้าง
                #2.1 Property มีค่าเท่าไหร่บ้าง

            # Find Set (1)
            $FincSale = new FunctionSaleOrder();


            $InSet = Items::find()->where(['itemset' => $param['itemset']])->all();

            /*
            * Clear Temp
            */
            $Temp = \common\models\TmpMenuGroup::deleteAll(['session_id' => Yii::$app->session->getId()]);


            // Create set (By insert to table).
            foreach ($InSet as $items) {
                    $FincSale->ItemSet($items->id);
            }
            $cache->set($keys, json_encode($InSet), 60);
        }
         
        return $this->renderpartial('../modal/_modal_pickitem');

    }

    public function actionCreate_saleline()
    {
        $id         = Yii::$app->request->post('param')['soid'];
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        
        // Get data from Sale Header
        $Header     = SaleHeader::find()
                        ->where(['id' => $id])
                        ->andWHere(['comp_id' => $company])
                        ->one();
                        
        $model      = new SaleLine();
        // Sale Line
        $model->order_no    = Yii::$app->request->post('param')['orderno'];
        $model->description = isset(Yii::$app->request->post('param')['desc']) ? Yii::$app->request->post('param')['desc'] : NULL;

        // ถ้า ไม่มี Item
        // ให้ไปดึงจาก session (Register from /Modules/Manufacturing/model/FunctionManufac.php #CreateBom)
        $Item 			= Items::findOne(Yii::$app->request->post('param')['itemid']);
        if($Item == null){
            $Item       = Items::find()->where(['No' => Yii::$app->session->get('item_no')])->one();
        }
        $model->item    = $Item->id;
        $model->item_no = $Item->No;

        //if($Item->invenByBom > 0){
            // if(Yii::$app->request->post('param')['itemno'] == 'eWinl'){}             
            $model->quantity    = Yii::$app->request->post('param')['amount'];
            $model->unit_price  = (Yii::$app->request->post('param')['price'] == 0) ? $model->lastPrice : Yii::$app->request->post('param')['price'];
    
            $model->create_date = date('Y-m-d H:i:s');
            $model->vat_percent = $Header->vat_percent;
            $model->user_id     = Yii::$app->user->identity->id;
            $model->api_key     = Yii::$app->session->getId();
            $model->comp_id     = $company;
            $model->sourcedoc   = $Header->id;

            // แยก Vat
            $model->unit_price_exvat = ($model->unit_price) ? ($model->unit_price * 100) /107 : 0;

            if($model->save()){

                // Session Clear
                Yii::$app->session->set('item_no',' ');

                // Sale people update
                $Policy = SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve');            
                if(in_array(Yii::$app->session->get('Rules')['rules_id'],$Policy)){
                    $Header->status     = 'Open';
                    $Header->confirm    = '0';
                    if(!$Header->save()){
                        Yii::$app->session->setFlash('error', json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE));
                    }
                }else{
                    // Admin update
                    $Header->confirm    = '0';
                    if(!$Header->save()){
                        echo "<script> alert(".(json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE))."); </script>";
                    }
                }

            }else {
                Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
            }

        //}else{
            //Yii::$app->session->setFlash('error', Yii::t('common','Out of stock'));
        //}

        $query   = SaleLine::find()
                    ->where(['sourcedoc' => $id])
                    ->andwhere(['comp_id' => $company]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $this->renderpartial('_saleline_mobile',[
            'model' => $model, 
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreateSaleline()
    {
        $id             = Yii::$app->request->post('param')['soid'];
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $Quantity       = Yii::$app->request->post('param')['amount'];
        $data           = [];
        $message        = 'Done';
        $suggestion     = '';
        $status         = 200;
        $Compare        = 0;
        $reserved       = 0;

        if($Quantity > 0){      // จำนวนสินค้าต้องมากกว่า 0

            $transaction = Yii::$app->db->beginTransaction();
            try {

                $Item 			= Items::findOne(Yii::$app->request->post('param')['itemid']);
                if($Item == null){
                    $Item       = Items::findOne(['No' => Yii::$app->request->post('param')['itemno']]);
                }

                

                if($Item->id == 1414){
                    
                    $data = self::CreateSaleLine((Object)[
                        'id'        => Yii::$app->request->post('param')['soid'],
                        'orderno'   => Yii::$app->request->post('param')['orderno'],
                        'desc'      => isset(Yii::$app->request->post('param')['desc'])
                                            ? Yii::$app->request->post('param')['desc']
                                            : null,
                        'amount'    => Yii::$app->request->post('param')['amount'],
                        'price'     => Yii::$app->request->post('param')['price'],
                        'item'      => $Item
                    ]);

                }else {
                    $itemProduce    = $Item->ProductionBom > 0
                                        ? $Item->qtyForce->last_possible
                                        : $Item->qtyForce->last_stock;
                    $reserved       = $Item->reserveInSaleLine * 1;
                    $AlreadyInline  = $Item->getQtyInSaleLine(Yii::$app->request->post('param')['soid']) * 1;
                    $Compare        = $itemProduce - ($AlreadyInline + $Quantity) ;   // ยอดที่สามารถผลิตได้ หัก(ลบ) กับยอดที่กำลังเปิดใบสั่งขาย(เฉพาะใบนี้ ไม่รวมของคนอื่น)
                    
                    // เปิดการใช้งานตรวจสต๊อกก่อนเปิด SO
                    if($Item->lockStock){      
                        
                            if($Compare >= 0){  // ถ้าจำนวนสินค้า หัก(ลบ)จำนวนในใบงาน ถ้ามากกว่าหรือเท่ากับ 0 ให้สร้าง sale line

                                // หักลบจำนวนที่จอง
                                // ถ้าน้อยกว่า 0 ไม่อนุญาตให้เพิ่ม
                                if(($Compare - $reserved ) < 0){ 
                                    // ติดจอง
                                    $data = (Object)[
                                        'status'        => 404,
                                        'message'       => Yii::t('common','Not enough raw materials to produce'),
                                        'reserve'       => $reserved,
                                        'suggestion'    => Yii::t('common','Quantity to reserved')
                                    ];
                                    
                                    $status 		    = 404;
                                    $message 		    = Yii::t('common','Not enough raw materials to produce');    
                                    $suggestion         = Yii::t('common','Quantity to reserved');   
                                    
                                    

                                }else{
                                    $data = self::CreateSaleLine((Object)[
                                        'id'        => Yii::$app->request->post('param')['soid'],
                                        'orderno'   => Yii::$app->request->post('param')['orderno'],
                                        'desc'      => isset(Yii::$app->request->post('param')['desc']) ? Yii::$app->request->post('param')['desc'] : null,
                                        'amount'    => Yii::$app->request->post('param')['amount'],
                                        'price'     => Yii::$app->request->post('param')['price'],
                                        'item'      => $Item
                                    ]);
                                }

                            }else{

                                if($itemProduce <= 0){
                                    // สินค้าหมด

                                    if($reserved > 0){
                                        //มีจอง

                                        $data = (Object)[
                                            'status'        => 404,
                                            'message'       => Yii::t('common','Out of stock'),
                                            'reserve'       => $reserved,
                                            'suggestion'    => Yii::t('common','Quantity to reserved')
                                        ];


                                        $status 		= 404;
                                        $message 		=Yii::t('common','Out of stock');   
                                        $suggestion     = Yii::t('common','Quantity');

                                        
                                    }else{
                                        //ไม่มีจอง

                                        $data = (Object)[
                                            'status'    => 500,
                                            'message'   => Yii::t('common','Out of stock'),
                                            'reserve'   => $reserved
                                        ];

                                        $status 		= 500;
                                        $message 		= Yii::t('common','Out of stock');
                                        $suggestion     = Yii::t('common','Quantity');       
                                    }
                                    
                                }else {
                                    // 
                                    $data = (Object)[
                                        'status'    => 403,
                                        'message'   => Yii::t('common','Not enough raw materials to produce'),
                                        'stock'     => $Item->ProductionBom > 0
                                                            ? $Item->myItems->last_possible *1
                                                            : $Item->myItems->last_stock * 1,
                                        'qtyOfSale' => $AlreadyInline
                                    ];

                                    $status 		= 403;
                                    $message 		= Yii::t('common','Not enough raw materials to produce'); 
                                    $suggestion     = Yii::t('common','Quantity');  
                                    
                                }

                                try{ // Line Notify                                            
                                
                                    $bot =  \common\models\LineBot::findOne(5);
                                    $msg = "\r\n".'CREATE LINE'."\r\n";
                                    $msg.= $message."\r\n";
                                    $msg.= $Item->master_code."\r\n";
                                    $msg.= $Item->description_th."\r\n";
                                    $msg.= $suggestion.' : ' .$Item->myItems->last_stock."\r\n";               
                                    $msg.= 'Sale Order : ' .Yii::$app->request->post('param')['orderno']."\r\n";         
                                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                                    
                                    $bot->notify_message($msg);					
                
                                } catch (\Exception $e) {					 
                                    // $status 		= 500;
                                    // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                                }	
                            }

                            


                    }else{

                        $data = self::CreateSaleLine((Object)[
                            'id'        => Yii::$app->request->post('param')['soid'],
                            'orderno'   => Yii::$app->request->post('param')['orderno'],
                            'desc'      => isset(Yii::$app->request->post('param')['desc']) ? Yii::$app->request->post('param')['desc'] : null,
                            'amount'    => Yii::$app->request->post('param')['amount'],
                            'price'     => Yii::$app->request->post('param')['price'],
                            'item'      => $Item
                        ]);

                    }
                }

            

                $transaction->commit();

            } catch (\Exception $e) {
                $transaction->rollBack();
                $data = (Object)[
                    'status'        => 500,
                    'message'       => Yii::t('common','Error'),
                    'reserve'       => $reserved,
                    'suggestion'    => Yii::t('common','{:e}',[':e' => $e])                
                ];                            
            
            }

        }else{
            $data = (Object)[
                'status'    => 500,
                'message'   => Yii::t('common','Quantity must be greater than 0')
            ];

            
        }

        if($status == 200){
            $model  = $this->findModel($id);

            $query  = SaleLine::find()
                        ->where(['sourcedoc'    => $id])
                        ->andwhere(['comp_id'   => $company]);
    
            $dataProvider = new ActiveDataProvider([
                'query'         => $query,
                'pagination'    => false,
            ]);
    
    
            return json_encode([
                'data'  => $data,
                'html'  => $this->renderpartial('_saleline_mobile',[
                            'model'         => $model,
                            'dataProvider'  => $dataProvider
                        ]),
                'reserved'  => $reserved,
                'workdate'  => Yii::$app->session->get('workdate'),
                'worktime'  => Yii::$app->session->get('worktime'),
                'status' => $data->status,
                'message' => $message
            ]);
        }else{
            return json_encode([
                'data'      => $data,
                'status'    => $data->status,
                'message'   => $message
            ]);
            exit;
        }

        
    }

    public static function CreateSaleLine($obj){

        $id                 = $obj->id;
        $company            = Yii::$app->session->get('Rules')['comp_id'];
        $Item               = $obj->item;
        $message            = '';
        $status             = 200;

        // Get data from Sale Header
        $Header             = SaleHeader::findOne($id);

        // Sale Line           
        $model              = new SaleLine();        
        $model->order_no    = $obj->orderno;
        $model->description = $obj->desc;
        $model->item        = $Item->id;
        $model->item_no     = $Item->No;
        $model->quantity    = $obj->amount;
        $model->unit_price  = $obj->price;
        $model->create_date = date('Y-m-d H:i:s');
        $model->vat_percent = $Header->vat_percent;
        $model->user_id     = Yii::$app->user->identity->id;
        $model->api_key     = Yii::$app->session->getId();
        $model->comp_id     = $company;
        $model->sourcedoc   = $id;
        $model->unit_measure= $Item->defaultMeasure ? $Item->defaultMeasure->measure : '';

        // แยก Vat
        $model->unit_price_exvat = ($model->unit_price) ? ($model->unit_price * 100) /107 : 0;
        

        if($model->save()){
            
            $status     = 200;
            $message    = Yii::t('common','Success');  
                                     
            // Session Clear
            Yii::$app->session->set('item_no',' ');

            // Sale people update
            if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve'))){
                $Header->status     = 'Open';                
            }

            $Header->confirm    = '0';
            $Header->balance    = $Header->sumtotal->total;
            if(!$Header->save()){
                $message    = json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE);
                $status     = 500;
            }

        }else {                
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        return (Object)([  
            'id'        => $model->id,          
            'message'   => $message,
            'item'      => $Item->id,
            'name'      => $Item->description_th,
            'code'      => $Item->master_code,
            'stock'     => $Item->last_possible,
            'status'    => (int)$status
        ]);
    }

    public function actionGetLine(){

        $model  = $this->findModel(Yii::$app->request->post('id'));
        $query  = SaleLine::find()
                    ->where(['sourcedoc' => Yii::$app->request->post('id')])
                    ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);

        return $this->renderpartial('_saleline_mobile',[
            'model' => $model, 
            'dataProvider' => $dataProvider
            ]);

    }

    public function actionUpdateSaleLine()
    {
        // ถ้า Ship แล้ว ไม่ให้แก้ไขจำนวนใน Sale Line
        $Query = WarehouseMoving::find()
                          ->joinwith('header')
                          ->where(['warehouse_moving.SourceDoc' => $_POST['param']['lineno']])
                          ->andwhere(['<>','warehouse_header.status' ,'Undo']);

        if($Query->exists()){

            echo '<span class="text-danger">THIS LINE ALREADY EXISTS.</span>';

            exit();
        }


        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $model      = SaleLine::findOne($_POST['param']['lineno']);

        if($_POST['param']['updatefield'] == 'qty'){

            $model->quantity        = $_POST['param']['edit'];
            $model->save(false);

        }else if($_POST['param']['updatefield'] == 'price'){

            $model->unit_price       = $_POST['param']['edit'];
            $model->unit_price_exvat = ($_POST['param']['edit'] * 100) /107;
            $model->save(false);

        }

         
        // Update Sale Header to Open status
        $Policy = SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve');            
        if(in_array(Yii::$app->session->get('Rules')['rules_id'],$Policy)){
            //if(Yii::$app->session->get('Rules')['rules_id'] == 3){ // Sales
            $Header             = SaleHeader::findOne($model->sourcedoc);                    
            $Header->status     = 'Open';
            $Header->balance    = $Header->sumtotal->total;
            $Header->save(false);
        }
        
        $query   = SaleLine::find()
        ->where(['sourcedoc' => $model->sourcedoc])
        ->andwhere(['comp_id' => $company]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);

        if(Yii::$app->session->get('theme')==1){
            return $this->renderAjax('_saleline_mobile',[
                'dataProvider' => $dataProvider
                ]);
        }else{
            return $this->renderAjax('_saleline_editable',[
                'dataProvider' => $dataProvider
                ]);                       
        }
        
        
        //return $this->renderpartial('_test');
    }

    public function actionUpdateLineAjax(){
        if(Yii::$app->request->isAjax){
            if(isset($_POST['key'])){
                $name   = '';
                $key    = 0;
                $value  = '';
                $model  = SaleLine::findOne($_POST['key']);
                if(isset($_POST['name'])) $name   = $_POST['name'];
                switch ($name) {
                    case 'desc':
                        $model->description  = $_POST['val'];
                        break;
                    case 'qty':
                        $model->quantity    = $_POST['val'];
                        break;
                    case 'price':
                        $model->unit_price  = $_POST['val'];
                        break;
                    default:
                        # code...
                        break;
                }
                $model->save();

                $query   = SaleLine::find()
                ->where(['sourcedoc' => $model->sourcedoc])
                ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false,

                ]);
                if(Yii::$app->session->get('theme')==1){
                    return $this->renderAjax('_saleline_mobile',[
                        'dataProvider' => $dataProvider
                        ]);
                }else{
                    return $this->renderAjax('_saleline_editable',[
                        'dataProvider' => $dataProvider
                        ]);
                }
                
                //return $this->renderAjax('_saleline_editable',['dataProvider' => $dataProvider]);
            }

            
        }
    }

    public function actionUpdateLineBox($id){
        $so         = base64_decode($id);
        $status     = 200;
        $message    = 'Done';
        $raws       = [];
        $reserved   = 0;

        if(Yii::$app->request->isAjax){       
            
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $dataArr    = Yii::$app->request->post('data');              
                
                foreach($dataArr as $data){
                    $model          = SaleLine::findOne($dataArr[0]['key']);
                    $field          = (string)$data['name'];
                    $model->$field  = $data['val'];                    
        
                    if((string)$data['name'] == 'quantity'){                        

                        if($model->items->lockStock){                             
                                $itemProduce    = $model->items->last_possible <= 0 
                                                    ? $model->items->qtyForce->last_possible 
                                                    : $model->items->last_possible;
                                                
                                $QtyInLine      = $model->qtyInLineNotMe * 1;
                                $Quantity       = $data['val'];
                                $reserved       = $model->items->reserveInSaleLine * 1;
                                $Remaining      = ($itemProduce - $reserved) - $QtyInLine ;   // ยอดที่สามารถผลิตได้ หัก(ลบ) กับยอดที่กำลังเปิดใบสั่งขาย(เฉพาะใบนี้ ไม่รวมของคนอื่น)
                                
                                if($Remaining >= 0){
                                
                                    // หักลบจำนวนที่จอง
                                    // ถ้าน้อยกว่า 0 ไม่อนุญาตให้เพิ่ม
                                    if(($Remaining - $Quantity) < 0){
                                    
                                        $raws[] = (Object)[
                                            'status'        => 404,
                                            'message'       => Yii::t('common','Not enough raw materials to produce'),
                                            'reserve'       => $reserved,
                                            'suggestion'    => Yii::t('common','Quantity to reserved'),
                                            'case'          => (string)$data['name'],
                                            'qty-before'    => $QtyInLine
                                        ];
                                    }else{
                                        //save                                                          
                                        if($model->save()){
                                        
                                            $raws[] = (Object)[
                                                'status'        => 200,
                                                'message'       => Yii::t('common','Done'),
                                                'reserve'       => $reserved,
                                                'case'          => (string)$data['name']                                               
                                            ];                                                           
                                            
                                        }else {
                                            
                                            $transaction->rollBack();
                                            $raws[] = (Object)[
                                                'status'        => 500,
                                                'message'       => Yii::t('common','Out of stock'),
                                                'reserve'       => $reserved,
                                                'suggestion'    => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                                                'case'          => (string)$data['name']   
                                            ];
                                            
                                        }
                                    }

                                
                                }else{

                                    if($itemProduce <= 0){
                                    
                                        if($reserved > 0){
                                            $raws[] = (Object)[
                                                'status'        => 404,
                                                'message'       => Yii::t('common','Out of stock'),
                                                'reserve'       => $reserved,
                                                'suggestion'    => Yii::t('common','Quantity to reserved'),
                                                'case'          => (string)$data['name']   
                                            ];
                                        }else{
                                            $raws[] = (Object)[
                                                'status'    => 500,
                                                'message'   => Yii::t('common','Out of stock'),
                                                'reserve'   => $reserved,
                                                'case'          => (string)$data['name']   
                                            ];
                                        }
                                        
                                    }else {

                                        if($reserved > 0){
                                            $raws[] = (Object)[
                                                'status'        => 404,
                                                'message'       => Yii::t('common','Not enough raw materials to produce'),
                                                'reserve'       => $reserved,
                                                'suggestion'    => Yii::t('common','Quantity to reserved'),
                                                'case'          => (string)$data['name']   
                                            ];
                                        }else{
                                            $raws[] = (Object)[
                                                'status'    => 403,
                                                'message'   => Yii::t('common','Not enough raw materials to produce'),
                                                'qty'       => $itemProduce,
                                                'compare'   => $Remaining,
                                                'case'          => (string)$data['name']   
                                            ];
                                        }
                                    }
                                }
                                
                            
                        }else{

                            if($model->save()){

                                $raws[] = (Object)[
                                    'status'        => 200,
                                    'message'       => Yii::t('common','Done'),
                                    'reserve'       => $reserved,
                                    'case'          => (string)$data['name']                                           
                                ]; 

                            }else{

                                $transaction->rollBack();
                                $raws[] = (Object)[
                                    'status'        => 500,
                                    'message'       => Yii::t('common','Error'),
                                    'reserve'       => $reserved,
                                    'suggestion'    => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                                    'case'          => (string)$data['name']   
                                ];

                            }
                        }

                    }else{
                        if($model->save()){

                            $raws[] = (Object)[
                                'status'        => 200,
                                'message'       => Yii::t('common','Done'),
                                'reserve'       => $reserved,
                                'case'          => (string)$data['name']                                           
                            ]; 

                        }else{

                            $transaction->rollBack();
                            $raws[] = (Object)[
                                'status'        => 500,
                                'message'       => Yii::t('common','Error'),
                                'reserve'       => $reserved,
                                'suggestion'    => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                                'case'          => (string)$data['name']   
                            ];

                        }
                    }
                }

                $transaction->commit();

            } catch (\Exception $e) {
                $transaction->rollBack();
                $raws[] = (Object)[
                    'status'        => 500,
                    'message'       => Yii::t('common','Error'),
                    'reserve'       => $reserved,
                    'suggestion'    => Yii::t('common','{:e}',[':e' => $e])
                ];                            
            
            }
            
            $NewLine        = [];
            $LineTotal      = [];
            $SumLine        = 0;
            $QueryNewLine   = SaleLine::find()->where(['sourcedoc' => $model->sourcedoc])->all();
            foreach ($QueryNewLine as $key => $line) {
                $NewLine[] = [
                    'key'   => $line->id, 
                    'name'  => '', 
                    'val'   => $line->quantity,
                    'price' => $line->unit_price
                ];

                $LineTotal[$line->id] = (Object)[
                    'unit_price' => $line->unit_price,
                    'quantity'   => $line->quantity
                ];
                $SumLine    = ($line->unit_price * $line->quantity) - (($line->unit_price * $line->quantity) * ($line->line_discount / 100)) ;
            }
           
            return json_encode([
                'data'      => $raws,
                'message'   => $message,
                'status'    => $status,   
                'value'     => [
                                'data'      => $NewLine,
                                'id'        => $model->id,
                                'so'        => $model->sourcedoc,
                                'linetotal' => $SumLine,
                                'sumline'   => $LineTotal[$dataArr[0]['key']]->unit_price * $LineTotal[$dataArr[0]['key']]->quantity,
                                'qty'       => $LineTotal[$dataArr[0]['key']]->quantity * 1,
                                'price'     => $LineTotal[$dataArr[0]['key']]->unit_price * 1                       
                            ]                
            ]);
                     
        
            
        }
    }

    /**
     * Creates a new SaleHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    static function createHeader($obj){
        // ถ้ามีเลขนี้แล้ว ให้รันเลขใหม่
        // $keys = 'sale-order&no:'.$obj->no.'&comp:'.Yii::$app->session->get('Rules')['comp_id'].'&user:'.Yii::$app->user->identity->id;
        // $cacheData = Yii::$app->cache->get($keys);
         
        // if($cacheData){
        //     self::createHeader(json_decode($cacheData));
        // }else{            
            
        //     Yii::$app->cache->set($keys, json_encode($obj), 60);
                
            if(SaleHeader::find()->where(['no' => $obj->no])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists()){    
                Yii::$app->session->setFlash('warning', Yii::t('common','Try create'));   
                return false;   
                //return (Object)['id' => 'SO1912-0001']; 
                //return self::createHeader((Object)['no' => 'SO1912-0001']);
                //return self::createHeader((Object)['no' => Generater::getRuning('sale_header','no','all')]);
            }else{
                
                $model                  = new SaleHeader();  
                $model->no              = $obj->no;      
                $model->user_id         = Yii::$app->user->identity->id;
                $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                $model->sales_people    = Yii::$app->session->get('Rules')['sale_code'];
                $model->sale_id         = Yii::$app->session->get('Rules')['sale_id'];
                $model->vat_type        = 2;
                $model->vat_percent     = 0;
                $model->balance_befor_vat = 0;
                $model->paymentdue      = date('Y-m-d',strtotime(date('Y-m-d'). "+1 days"));
                $model->ship_date       = date('Y-m-d',strtotime(date('Y-m-d'). "+3 days"));
                $model->order_date      = date('Y-m-d');
                $model->create_date     = date('Y-m-d H:i:s');
                $model->status          = 'Open';
                $model->payment_term    = 0;

                if($model->save()){    
                    Yii::$app->session->setFlash('warning', Yii::t('common','New create'));   
                    //Yii::$app->cache->delete($keys);         
                    return $model;
                }else{      
                    Yii::$app->session->setFlash('error', Yii::t('common','Error'));            
                    return false;
                }
            }
        //}
    }

    public function actionCreate()
    {
        // ใส่ cache เพื่อป้องกันการสร้างเอกสารซ้ำ
        $keys       = 'create-order&comp:'.Yii::$app->session->get('Rules')['comp_id'].'&user:'.Yii::$app->user->identity->id;
        $cacheData  = Yii::$app->cache->get($keys);
        $Fnc        = new FunctionSaleOrder();
        $Free       = $Fnc->findEmpty();

        if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','saleorder','actionIndex','read-only'))){
            Yii::$app->session->setFlash('warning', Yii::t('common','Permission Denine'));
            return $this->redirect(['index']);
            exit;
        }else{

            if($cacheData){
            
                if($Free != null){
                    //Yii::$app->session->setFlash('warning', Yii::t('common','Already exists'));
                    sleep(5);
                    Yii::$app->cache->delete($keys);
                    return $this->redirect(['update', 'id' => $Free->id]);
                    exit;
                }else{
                    Yii::$app->session->setFlash('error', Yii::t('common','cashed'));
                    sleep(5);
                    Yii::$app->cache->delete($keys);
                    return $this->redirect(['index']);
                    exit;
                }
                
            }else{
                Yii::$app->cache->set($keys, true, 60);                   
                if($Free  != null){
                    //Yii::$app->session->setFlash('warning', Yii::t('common','Already exists'));
                    Yii::$app->cache->delete($keys);
                    return $this->redirect(['update', 'id' => $Free->id]);
                    exit;
                }else {
                    
                    $transaction    = Yii::$app->db->beginTransaction();
                    try {

                        $no         = 'SO'.date('ym').'-0001';
                        try {
                            $no     = Generater::getRuning('sale_header','no','all');
                                   Generater::UpdateSeries('sale_header','no','all',$no);
                        } catch (\Exception $e) {
                                Yii::$app->session->setFlash('error', Yii::t('common','Error Header {:e}', [':e' => $e]));
                                $transaction->rollBack();
                                return $this->redirect(['index']);
                        }   
                          
                        $model                  = new SaleHeader();
                        $model->no              = $no;
                        $model->user_id         = Yii::$app->user->identity->id;
                        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                        $model->sales_people    = Yii::$app->session->get('Rules')['sale_code'];
                        $model->sale_id         = Yii::$app->session->get('Rules')['sale_id'];
                        $model->vat_type        = 2;
                        $model->vat_percent     = 0;
                        $model->balance_befor_vat = 0;
                        $model->paymentdue      = date('Y-m-d',strtotime(date('Y-m-d'). "+1 days"));
                        $model->ship_date       = date('Y-m-d',strtotime(date('Y-m-d'). "+3 days"));
                        $model->order_date      = date('Y-m-d');
                        $model->create_date     = date('Y-m-d H:i:s');
                        $model->status          = 'Open';
                        $model->live            = 0; // Locked
                        $model->payment_term    = 0;

                        $model->save();

                        if($model != null){
                            FunctionTracking::CreateTracking(
                            [
                                'doc_type'          => 'Sale-Order',
                                'doc_id'            => $model->id,
                                'doc_no'            => $model->no,
                                'doc_status'        => 'Create',
                                'amount'            => $model->balance,
                                'remark'            => 'Status : '.$model->status.', Credit : '.$model->payment_term.' : '.date('Y-m-d', strtotime($model->paymentdue)),
                                'track_for_table'   => 'sale_header',
                                'track_for_id'      => $model->id,
                            ]);
                            
                            Yii::$app->cache->delete($keys);
                            $transaction->commit();    
                            //Yii::$app->session->setFlash('warning', Yii::t('common','Already exists and update'));                        
                            return $this->redirect(['update', 'id' => $model->id]);
                            exit;
                        }else{
                            Yii::$app->session->setFlash('error', Yii::t('common','Error Header'));
                            $transaction->rollBack();     
                            Yii::$app->cache->delete($keys);                    
                            return $this->redirect(['index']);
                        }
                    } catch (\Exception $e) {
                            $transaction->rollBack();
                            Yii::$app->cache->delete($keys);     
                            Yii::$app->session->setFlash('error', Yii::t('common','Error Header {:e}', [':e' => $e])); 
                            return $this->redirect(['index']);
                            //throw $e;
                    }
                }
            }
        }
        
    }

    /**
     * Updates an existing SaleHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateHeader($id)
    {
        $model = $this->findModel($id);

        $customer = customer::findOne($model->customer_id);

        if ($model->load(Yii::$app->request->post())) {

            // Update Transport to Customer & Sale Header
            $customer->transport    = $model->transport;
            if($customer->district=='') $customer->district     = $model->district;
            if($customer->city=='')     $customer->city         = $model->city;
            if($customer->province=='') $customer->province     = $model->province;
            if($customer->postcode=='') $customer->postcode     = $model->zipcode;
            $customer->save(false);

            // if($model->vat_type == 2) // 1 = Vat, 2 = No Vat
            // {
            //     // ถ้าเป็น ​​ No Vat ให้ไปใช้ field unit_price ปกติ
            //     // โดยกำหนด include_vat = 0 หมายถึง ไปใช้ unit_price_exvat
            //     $model->include_vat = 1;
            // }
            $model->live        = 0;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id, 'action' => 'saved']);
        }
    }

    public function actionUpdateSomeField($id){

        $model              = $this->findModel($id);
        $field              = $_POST['field'];
        $data               = $_POST['data'];

        $model->$field      = $data;
        $model->live        = 0;

        if($model->save()){
            return json_encode([
                'status'    => 200,
                'field'     => $field,
                'data'      => $data
            ]);
        }else{
            return json_encode([
                'status'    => 500,
                'message'   => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }

        
       
    }
    public function actionUpdate($id)
    {
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        Yii::$app->session->set('workdate',date('Y-m-d')); 

        $model      = $this->findModel($id);
        if(Yii::$app->user->identity->id!=1){  // ถ้าไม่ใช่บริษัทตัวเอง ให้ออกไป
            if($model->comp_id != Yii::$app->session->get('Rules')['comp_id']){
                Yii::$app->session->setFlash('error', Yii::t('common','You do not have permission to access this Sale Order.'));
                return $this->redirect(['index']);
            }
        }
        $raws       = [];
        $undo       = [];
        $undoShip   = [];
        $status     = 200;
        $message    = '';
        $messageLine= '';
        $lineStatus = [];
        $IsQty      = '';
        
        if(Yii::$app->request->post('ajax')){
            /**
            * Update Sale Line
            * เมื่อมีการแก้ไขตัวเลขในบรรทัดสินค้า
            */
            $data       = Yii::$app->request->post('data');
            $field      = (string)Yii::$app->request->post('name');
            $Quantity   = 0;
            $Remaining  = 0;
            $reserved   = 0;           

            $transaction = Yii::$app->db->beginTransaction();
            try {
               
                 

                $Line               = SaleLine::findOne(Yii::$app->request->post('key'));
                $oldValue           = $Line->quantity;
                $Line->$field       = $data;
                $Line->sourcedoc    = $id;
                $Line->confirm      = 0;    // Reset Confirm Line

                
                
                


                if((string)Yii::$app->request->post('name') == 'quantity'){

                    $IsQty = true;

                    // ถ้า Ship แล้วไม่ให้แก้ไข 17/01/2020 
                    //----->
                    $hasShiped  =\common\models\WarehouseMoving::find()
                                ->joinwith('header')
                                ->where(['warehouse_moving.SourceDoc' => $Line->id])
                                ->andwhere(['warehouse_header.order_id' => $id])
                                ->andwhere(['warehouse_moving.TypeOfDocument'  => 'Sale'])
                                ->andwhere(['<>', 'warehouse_header.status', 'Undo'])
                                ->andWhere(['warehouse_moving.comp_id' => $company])
                                ->one();

                    if($hasShiped != null){            
                        return json_encode([
                            'status'    => 403,
                            'message'   => Yii::t('common','Please cancel the delivery before editing.') ,                
                            'value' => [
                                'id'    => $Line->id,
                                'val'   => $oldValue,
                                'total' => $oldValue * $Line->unit_price,                            
                            ],                
                            'remaining' => $Remaining,
                            'reserved'  => $reserved,
                            'data'      => (Object)[
                                            'status'        => 500,
                                            'message'       => Yii::t('common','Please cancel the delivery before editing.') ,  
                                            'reserve'       => $reserved,
                                            'suggestion'    => Yii::t('common','Quantity to reserved'),
                                            'inven'         => $Line->items->last_possible,
                                            'text'          => Yii::t('common','Remaining'),
                                            //'qty-before'    => $QtyInLine
                                        ],
                            'undoShip'  => $undoShip,
                            'undo'      => $undo                        
                        ]);
                        exit;
                    }

                    //<------
                     
                    if($Line->items->lockStock){                           
                        $itemProduce    = $Line->items->last_possible <= 0 
                                            ? $Line->items->qtyForce->last_possible 
                                            : $Line->items->last_possible;

                        $QtyInLine      = $Line->qtyInLineNotMe * 1;
                        $Quantity       = $data;
                        $reserved       = $Line->items->reserveInSaleLine * 1;
                        $remain         = $itemProduce - $reserved;
                        $Remaining      = $remain - $QtyInLine ;   // ยอดที่สามารถผลิตได้ หัก(ลบ) กับยอดที่กำลังเปิดใบสั่งขาย(เฉพาะใบนี้ ไม่รวมของคนอื่น)
                        
                        if($Remaining >= 0){
                            
                            // หักลบจำนวนที่จอง
                            // ถ้าน้อยกว่า 0 ไม่อนุญาตให้เพิ่ม
                            if(($Remaining - $Quantity) < 0){

                                $raws = (Object)[
                                    'status'        => 404,
                                    'message'       => Yii::t('common','Not enough raw materials to produce'),
                                    'reserve'       => $reserved,
                                    'suggestion'    => Yii::t('common','Quantity to reserved'),
                                    'inven'         => $remain,
                                    'text'          => Yii::t('common','Remaining'),
                                    //'qty-before'    => $QtyInLine
                                ];
                            }else{

                                //save                                                          
                                if($Line->save(false)){
                                    // ยกเลิกไปใช้ตอน Undo Ship  02/10/62
                                    /*
                                    // มีผลกับสต๊อก
                                    // ตรวจสอบว่ามีการผลิตหรือไม่
                                    // ถ้ามีการผลิตแล้ว ให้ลบการผลิตทิ้ง (output(FG) ถูกถอดตั้งแต่ Undo Ship แล้ว)
                                    if(WarehouseMoving::deleteAll([
                                        'AND', 
                                            'SourceDoc = :id', 
                                            ['IN', 'TypeOfDocument',['Consumption','Output']],
                                            ['comp_id' => Yii::$app->session->get('Rules')['comp_id']],
                                            ['between','DATE(PostingDate)', date('Y').'-01-01', date('Y').'-12-31']
                                        ], [':id' => $id])){
                                        self::DeleteLog((Object)['table' => 'warehouse_moving','field' => 'SourceDoc','value' => $id, 'lot' => $Line->items->id]);
                                    }
                                    */

                                    // ถ้าเปลี่ยนจำนวน ให้ยกเลิกการผลิตด้วย  
                                    $hasOutput  =\common\models\WarehouseMoving::find()
                                                ->joinwith('header')
                                                ->where(['warehouse_moving.order_line_table' => 'sale_line'])
                                                ->andWhere(['warehouse_moving.order_line_id' => $Line->id])
                                                ->andWhere(['warehouse_moving.TypeOfDocument' => 'Output'])
                                                ->andWhere(['<>','warehouse_header.status','Undo-Produce'])
                                                ->all();
                                    foreach ($hasOutput as $key => $line) {
                                        

                                        $undoChild =\common\models\WarehouseMoving::find()
                                                    ->joinwith('header')
                                                    ->where(['warehouse_moving.source_id' => $line->source_id])
                                                    ->andWhere(['<>','warehouse_header.status','Undo-Produce'])
                                                    ->all(); 
                                        foreach ($undoChild as $key => $do) {                 
                                        
                                            $undo[$key] = $line->header->UndoProduction($do->header, $do);
                                            
                                            if($undo[$key]->status==200){
                                                $line->header->status = 'Undo-Produce';
                                                $line->header->save();
                                            }  
                                        }                      
                                    
                                    }
                                    

                                    // Set new confirm                                    
                                    $model->confirm     = '0'; 
                                    $model->balance     = $model->sumtotal->total;    
                                    $model->live        = 0;

                                    $raws = (Object)[
                                        'status'        => 200,
                                        'message'       => Yii::t('common','Done'),
                                        'reserve'       => $reserved                                                
                                    ];                                                           
                                    $lineStatus[]       = $raws;
                                }else {

                                    $status             = 500;
                                    $message            = json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE);
                                    $messageLine        = json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE);
                                    //$transaction->rollBack();
                                    $raws = (Object)[
                                        'status'        => 500,
                                        'message'       => Yii::t('common','Out of stock'),
                                        'reserve'       => $reserved,
                                        'suggestion'    => json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE)
                                    ];
                                    $lineStatus[]       = $raws;
                                }
                            }

                            
                        }else{

                            if($itemProduce <= 0){
                            
                                if($reserved > 0){
                                    $raws = (Object)[
                                        'status'        => 404,
                                        'message'       => Yii::t('common','Out of stock'),
                                        'reserve'       => $reserved,
                                        'suggestion'    => Yii::t('common','Quantity to reserved'),
                                        'inven'         => $remain,
                                        'text'          => Yii::t('common','Remaining'),
                                    ];
                                }else{
                                    $raws = (Object)[
                                        'status'    => 500,
                                        'message'   => Yii::t('common','Out of stock'),
                                        'reserve'   => $reserved
                                    ];
                                }
                                
                            }else {

                                if($reserved > 0){
                                    $raws = (Object)[
                                        'status'        => 404,
                                        'message'       => Yii::t('common','Not enough raw materials to produce'),
                                        'reserve'       => $reserved,
                                        'suggestion'    => Yii::t('common','Quantity to reserved'),
                                        'inven'         => $remain,
                                        'text'          => Yii::t('common','Remaining'),
                                    ];
                                }else{
                                    $raws = (Object)[
                                        'status'    => 403,
                                        'message'   => Yii::t('common','Not enough raw materials to produce'),
                                        'qty'       => $itemProduce,
                                        'compare'   => $Remaining
                                    ];
                                }
                            }
                        }
                    }else{
                        $IsQty = 'ไม่ได้ล๊อกสต๊อก';
                        if($Line->save()){
                            
                            $model->live        = 0;
    
                            $raws = (Object)[
                                'status'        => 200,
                                'message'       => Yii::t('common','Done'),
                                'reserve'       => $Line->items->reserveInSaleLine                                                
                            ]; 
                            
                            $lineStatus[]       = $raws;
                        }else{
                            $status             = 500;
                            $messageLine        = json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE);
                            
                            $raws = (Object)[
                                'status'        => 500,
                                'message'       => Yii::t('common','Error'),
                                'reserve'       => $Line->items->reserveInSaleLine,
                                'suggestion'    => json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE)
                            ];
                            $lineStatus[]       = $raws;
                             
                        }
                    }
                    
                }else{
                    $IsQty = false;
                    if($Line->save()){
                        // $model->confirm = '0'; แก้อย่างอื่นที่ไม่ใช้จำนวน ไม่ต้องคอนเฟิร์ม
                        $model->live        = 0;

                        $raws = (Object)[
                            'status'        => 200,
                            'message'       => Yii::t('common','Done'),
                            'reserve'       => $Line->items->reserveInSaleLine                                                
                        ]; 
                        
                        $lineStatus[]       = $raws;
                    }else{
                        $status             = 500;
                        $messageLine        = json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE);
                        
                        $raws = (Object)[
                            'status'        => 500,
                            'message'       => Yii::t('common','Error'),
                            'reserve'       => $Line->items->reserveInSaleLine,
                            'suggestion'    => json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE)
                        ];
                        $lineStatus[]       = $raws;
                         
                    }
                }

                
                if($model->save()){
                    $status     = 200;
                    $message    = Yii::t('common','Success');
                    
                }else{
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                    
                    $raws = (Object)[
                        'status'        => 500,
                        'message'       => $message,
                        'suggestion'    => $messageLine
                    ];  
                     
                }  
                $transaction->commit();   
            } catch (\Exception $e) {                
                $raws = (Object)[
                    'status'        => 500,
                    'message'       => Yii::t('common','Error'),
                    'suggestion'    =>  Yii::t('common','{:e}',[':e' => $e])
                ]; 
                $transaction->rollBack();                       
            }
            
            $LoadLine   = SaleLine::findOne(Yii::$app->request->post('key'));               
            

            return json_encode([
                'status'    => $status,
                'message'   => $message,                
                'value' => [
                    'id'    => $LoadLine->id,
                    'val'   => $LoadLine->$field,
                    //'total' => $LoadLine->quantity * $LoadLine->unit_price,
                    'total' => ($LoadLine->quantity * $LoadLine->unit_price) - (($LoadLine->line_discount /100) * ($LoadLine->quantity * $LoadLine->unit_price)),                        
                    'status'=> $lineStatus,
                    'isQty' => $IsQty
                    //'remain'=> $Line->items->invenByBom < 0 ? 0 : (int)($Line->items->invenByBom)
                ],                
                'remaining' => $Remaining,
                'reserved'  => $reserved,
                'data'      => $raws,
                'undoShip'  => $undoShip,
                'undo'      => $undo,
                'live'      => $model->live,
                'id'        => $model->id
                //'opt'       => $Line->items->invenByBom. ' - '. $Quantity .'('.$Quantity.' NewQty) = ' .( $Line->items->invenByBom - $Quantity ),
            ]);
            exit;
        }


        
        

        if ($model->load(Yii::$app->request->post())) {

            if($model->status != 'Open'){ // ป้องกันการส่งใบงานโดยไม่มีรายการสินค้า 03/07/2020
                if(SaleLine::find()->where(['sourcedoc' => $model->id])->count() <= 0){
                    $model->status              = 'Open';
                    $model->balance             = 0;
                    $model->balance_befor_vat   = 0;
                    $model->save();
                    Yii::$app->session->setFlash('error', Yii::t('common','No items yet.'));
                    return $this->redirect(['update', 'model' => $model,'id' => $id]);
                    exit;
                }
            }

            $transac = Yii::$app->db->beginTransaction();
            try {
                
                //$model->customer_id     = $_POST['SaleHeader']['customer_id'];
                
                $model->update_by       = Yii::$app->user->identity->id;
                $model->update_date     = date('Y-m-d H:i:s');
                
                //$model->vat_percent     = $model->vattb->vat_value;          
                $model->balance         = $model->sumtotal->total;
                $model->balance_befor_vat = $model->sumtotal->sumline;
                $originalDate           = $model->paymentdue;
                $date1                  = str_replace('/', '-', $originalDate);
                $newDate                = date("Y-m-d", strtotime($date1));
                $model->paymentdue      = $newDate;
                
                //$model->order_date      = date('Y-m-d');
                
                // Update Sale people again.
                // For make sure this order.
                $SALES                  = SalesPeople::findOne($model->sale_id);
                if($SALES == null){

                    $transac->rollBack();
                   
                    Yii::$app->session->setFlash('error', Yii::t('common','Error! Please check sale people.'));
                    Yii::$app->session->setFlash('warning', Yii::t('common','Please contact administrator.'));
                    return $this->render('update', [
                        'model' => $model
                    ]);
                }
                $model->sales_people    = $SALES->code;
                $model->sale_id         = $SALES->id;
                //$model->confirm         = '0'; ไม่ต้องให้คอนเฟิร์มใหม่ (เพราะคอนเฟิร์มไปแล้ว)
                $model->extra           = NULL;       
                $model->live            = 1;       

                if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve'))){
                    // For Sales Person
                    // ถ้าไม่มียอดสั่งขาย
                    // ไม่ให้เปลี่ยนสถานะเป็น 'ส่งใบงาน' => Open
                    if($model->balance > 0){
                        if(in_array($model->status, ['Open'])){

                            //--- Order Tracking ----        
                            $Remark = 'Current : '.$model->status.', 
                                        Status : '.$model->status.', 
                                        Remark : '.$model->remark.', 
                                        Transport : '.$model->transport.', 
                                        Credit : '.$model->payment_term.' : '.date('Y-m-d', strtotime($model->paymentdue));      
                            // บันทึกการแก้ไข (ส่งใบงาน)
                            FunctionTracking::CreateTracking(
                                        [
                                            'doc_type'          => 'Sale-Order',
                                            'doc_id'            => $model->id,
                                            'doc_no'            => $model->no,
                                            'doc_status'        => $model->status,
                                            'amount'            => $model->balance,
                                            'remark'            => $Remark,
                                            'track_for_table'   => 'sale_header',
                                            'track_for_id'      => $model->id,
                                        ]);
                            //--- /. Order Tracking ----
                            
                            // ถ้าไม่มีลูกค้า ให้ Status เป็น Open เหมือนเดิม
                            if($model->customer_id!=''){
                                $model->status      = 'Release';
                            }else {
                                $model->status      = 'Open';
                            }                        
                        }

                    }else {
                        $model->status      = 'Open';
                    }
                    
                } 

                // UPDATE STATUS
                if($model->status=='Release'){
                    $model->release_date = date('Y-m-d H:i:s');
                }
                
                
                //--- Order Tracking ----
                $TrackRemark = 'Current : '.$model->status.', 
                                Status : '.$model->status.', 
                                Remark : '.$model->remark.', 
                                Transport : '.$model->transport.', 
                                Credit : '.$model->payment_term.' : '.date('Y-m-d', strtotime($model->paymentdue));
                
                FunctionTracking::CreateTracking(
                            [
                                'doc_type'          => 'Sale-Order',
                                'doc_id'            => $model->id,
                                'doc_no'            => $model->no,
                                'doc_status'        => $model->status,
                                'amount'            => $model->balance,
                                'remark'            => $TrackRemark,
                                'track_for_table'   => 'sale_header',
                                'track_for_id'      => $model->id,
                            ]);
                //--- /. Order Tracking ----  

                if($model->save()){

                    // ถ้าไม่มีสินค้า ไม่ให้ส่งใบง่าน
                    $CheckStock = [];
                    // Update Sale Line  เพื่อไปแสดงในหน้า  Delivery Confirm (index.php?r=warehousemoving/default)
                    // โดยยึดสถาณะ saved
                    $SaleLine = SaleLine::find()->where(['sourcedoc' => $model->id])->all();
                    foreach ($SaleLine as $key => $line) {
                        $line->vat_percent      = $model->vat_percent;
                        $line->order_date       = date('Y-m-d H:i:s');
                        $line->save_order       = 'saved';
                        $line->quantity_to_ship = $line->quantity;
                        $line->quantity_shipped = 0;
                        //$line->confirm        = '0';  ไม่ต้องให้คอนเฟิร์มใหม่                       
                        $line->update(false);
                        
                        $itemProduce            = $line->items->last_possible <= 0 
                                                    ? $line->items->qtyForce->last_possible 
                                                    : $line->items->last_possible;

                        $CheckStock[$line->items->id]  = $itemProduce > 0 ? true : false;

                    }

                    if(Yii::$app->user->identity->id != 1){
                    
                        try{ // Line Notify                                            
                                    
                            $bot =  \common\models\LineBot::findOne(6);
                            $msg = 'ID : '.$model->id."\r\n\r\n";
                            $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";

                            $msg.= $model->no."\r\n";

                            $msg.= $model->salespeople 
                                    ? ('['.$model->salespeople->code.'] '.$model->salespeople->name)."\r\n"
                                    : ' '."\r\n";

                            $msg.= $model->customer 
                                    ? ('['.$model->customer->code.'] '.$model->customer->name)."\r\n\r\n" 
                                    : ' '."\r\n";
                            
                            $msg.= number_format($model->balance,2)." ฿\r\n";
                            $msg.= $model->status."\r\n";
                            $msg.= Yii::t('common','Remark').' : ' .$model->remark."\r\n";

                            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                            
                            $bot->notify_message($msg);	
                            
                        } catch (\Exception $e) {	
                            
                            $bot =  \common\models\LineBot::findOne(5);
                            $msg = "\r\n".'Sale Order Error'."\r\n";
                            $msg.= Yii::t('common','{:e}',[':e' => $e]);	
                            $msg.= $model->no."\r\n";
                            $msg.= $model->salespeople 
                                    ? ('['.$model->salespeople->code.'] '.$model->salespeople->name)."\r\n"
                                    : ' '."\r\n";
                            $msg.= $model->customer 
                                    ? ('['.$model->customer->code.'] '.$model->customer->name)."\r\n\r\n" 
                                    : ' '."\r\n";
                            $msg.= number_format($model->balance,2)."฿\r\n";
                            $msg.= $model->status."\r\n";
                            
                            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                            
                            $bot->notify_message($msg);	
                            
                        }	
                    }

                    if(in_array(false,$CheckStock)){
                        // Yii::$app->session->setFlash('warning', '<i class="far fa-paper-plane"></i> '.Yii::t('common','Some items are out of stock.'));
                        // $model->status      = 'Open';
                        // $model->save();
                        //return $this->redirect(['update', 'id' => $model->id]);
                        $transac->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }else{
                        Yii::$app->session->setFlash('info', $model->customer_id);
                        Yii::$app->session->setFlash('info', '<i class="far fa-paper-plane"></i> '.Yii::t('common','ข้อมูลถูกส่งแล้ว !'));
                        $transac->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }


                   

                    
                }else{
                    $transac->rollBack();
                    Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                }

            } catch (\Exception $e) {
                $transac->rollBack();
            }
        } 

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    public function actionLoadSaleLine($id){
        $query  = SaleLine::find()
                ->where(['sourcedoc'    => $id])
                ->andwhere(['comp_id'   => $company]);

        $dataProvider = new ActiveDataProvider([
            'query'         => $query,
            'pagination'    => false,
        ]);


        return json_encode([
            'html'          => $this->renderpartial('_saleline_mobile',[
                                'model'         => $model,
                                'dataProvider'  => $dataProvider
                            ])
        ]);
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
    

    public function actionUpdatecus($id)
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        // ถ้ายังไม่ Undo Ship ไม่ให้แก้ลูกค้า
        $SaleLine = SaleLine::find()->where(['sourcedoc' => $id])->all();
        foreach ($SaleLine as $key => $line) {
            # code...
            $Whquery = \common\models\WarehouseMoving::find()
                              ->joinwith('header')
                              ->where(['warehouse_moving.SourceDoc' => $line->id])
                              ->andwhere(['<>','warehouse_header.status','Undo']);
            if($Whquery->exists()){
                echo '<script>swal(
                  "สินค้าถูกบรรจุแล้ว",
                  "ต้อง ยกเลิก รายการ  ก่อนทำการแก้ไข",
                  "warning"
                ); </script>';
                exit();
            }
        }
        // ถ้าสถาณะไม่ใช่ Open , Release  ไม่ให้แก้ลูกค้า
        $checkStatus = SaleHeader::findOne($id);
        if(!in_array($checkStatus->status, ['Open','release'])){
            //echo '<script>alert("'.Yii::t('common','Not allow change customer.').'"); </script>';
            return '0';
            exit();
        }
        $model                  = SaleHeader::find()->where(['id' => $id])->andWhere(['or',['status' => 'Open'],['status' => 'release']])->one();
        $Customer               = Customer::findOne($_POST['param']['cust']);
        $model->customer_id     = $_POST['param']['cust'];
        if(Yii::$app->session->get('Rules')['rules_id']==3) // 3=Sales,4=Sales Admin
        {
            $model->sales_people = Yii::$app->session->get('Rules')['sales_id'];
        }else{
            $sales  = explode(',',$Customer->owner_sales);
            $model->sales_people = $sales[0];
        }
        // $date = date('Y-m-d');
        // $date1 = str_replace('-', '/', $date);
        if($Customer->payment_term=='') $Customer->payment_term = 0;
        $model->paymentdue      = date('Y-m-d',strtotime(date('Y-m-d') . "+ ".$Customer->payment_term." days"));
        $model->payment_term    = $Customer->payment_term;
        $model->sale_address    = $Customer->address;
        $model->bill_address    = $Customer->address;
        $model->ship_address    = $Customer->address .' '. $Customer->postcode;
        $model->transport       = $Customer->transport;
        $model->discount        = 0;
        $model->vat_type        = 2;
        if($model->save())
        {
            echo 'Loading....';
            //return $this->redirect(['update', 'model' => $model,'id' => $id]);
            echo '<script>window.location.href = "index.php?r=SaleOrders/saleorder/update&id='.$id.'";</script>';

        }else {
            echo 'Error . . .';
            print_r($model->getErrors());
            exit();
            //echo $model->payment_term;
        }


    }

    /**
     * Deletes an existing SaleHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model      = $this->findModel($id);
        $status     = 200;
        $message    = Yii::t('common','Done');

        

        if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','saleorder','actionIndex','read-only'))){   
            return json_encode([
                'status'    => 403,
                'message'   => Yii::t('common','Not Allow'),
                'value'     => [
                    'id'        => $id,
                    'status'    => Yii::t('common','Permission Denine')
                ],
            ]);
        }else{

        
        
            if(in_array($model->status,['Open','Cancel'])){
                # Allow Only status Open,Cancel
                # 1. Delete Sale Line
                # 2. Delete Sale Header

                $transaction = Yii::$app->db->beginTransaction();
                try {
                        
                        if(SaleLine::find()->where(['sourcedoc' => $id])->exists()){
                            # Exists Sale Line
                            if(SaleLine::deleteAll(['sourcedoc' => $id])){
                                # Delete Sale Line
                                if($this->findModel($id)->delete()){
                                    # Delete Sale Header

                                    try{ // Line Notify                                            
                                
                                        $bot =  \common\models\LineBot::findOne(6);
                                        $msg = "\r\n".'DELETE ID : '.$model->id."\r\n\r\n";
                                     
                                        $msg.= $model->no."\r\n";
                            
                                        $msg.= $model->salespeople 
                                                ? ('['.$model->salespeople->code.'] '.$model->salespeople->name)."\r\n"
                                                : ' '."\r\n";
                                                
                                        $msg.= $model->customer 
                                                ? ('['.$model->customer->code.'] '.$model->customer->name)."\r\n\r\n" 
                                                : ' '."\r\n";
                                        
                                        $msg.= number_format($model->balance,2)."฿\r\n";
                                        $msg.= $model->status."\r\n";
                                        $msg.= Yii::t('common','Remark').'  ' .$model->remark."\r\n";
                            
                                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                                        
                                        $bot->notify_message($msg);	
                                         
                                    } catch (\Exception $e) {	
                                        
                                        $bot =  \common\models\LineBot::findOne(5);
                                        $msg = "\r\n".'Delete Error'."\r\n";
                                        $msg.= Yii::t('common','{:e}',[':e' => $e]);	
                                        $msg.= $model->no."\r\n";
                                        $msg.= $model->salespeople 
                                                ? ('['.$model->salespeople->code.'] '.$model->salespeople->name)."\r\n"
                                                : ' '."\r\n";
                                        $msg.= $model->customer 
                                                ? ('['.$model->customer->code.'] '.$model->customer->name)."\r\n\r\n" 
                                                : ' '."\r\n";
                                        $msg.= number_format($model->balance,2)."฿\r\n";
                                        $msg.= $model->status."\r\n";
                                           
                                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                                        
                                        $bot->notify_message($msg);	
                                        
                                    }

                                    $transaction->commit();
                                    return json_encode([
                                        'status'    => 200,
                                        'message'   => 'delete',
                                        'value'     => [
                                            'id'        => $id,
                                            'status'    => $model->status
                                        ],
                                    ]);
            
                                }else {
                                    # Error Delete Sale Header
                                    $transaction->rollBack();
                                    return json_encode([
                                        'status'    => 510,
                                        'message'   => 'Error Sale header',
                                        'value'     => [
                                            'id'        => $id,
                                            'status'    => $model->status
                                        ],
                                    ]);
                                }
        
                            }else {
                                # Error Delete Sale Line
                                $transaction->rollBack();
                                return json_encode([
                                    'status'    => 510,
                                    'message'   => 'Error Sale line',
                                    'value'     => [
                                        'id'        => $id,
                                        'status'    => $model->status
                                    ],
                                ]);
                            }

                        }else {
                            // Empty Sale Line
                            if($this->findModel($id)->delete()){
                                # Delete Sale Header
                                try{ // Line Notify                                            
                                
                                    $bot =  \common\models\LineBot::findOne(6);
                                    $msg = "\r\n".'DELETE ID : '.$model->id."\r\n\r\n";
                                 
                                    $msg.= $model->no."\r\n";
                        
                                    $msg.= $model->salespeople 
                                            ? ('['.$model->salespeople->code.'] '.$model->salespeople->name)."\r\n"
                                            : ' '."\r\n";
                                            
                                    $msg.= $model->customer 
                                            ? ('['.$model->customer->code.'] '.$model->customer->name)."\r\n\r\n" 
                                            : ' '."\r\n";
                                    
                                    $msg.= number_format($model->balance,2)."฿\r\n";
                                    $msg.= $model->status."\r\n";
                                    $msg.= Yii::t('common','Remark').'  ' .$model->remark."\r\n";
                        
                                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                                    
                                    $bot->notify_message($msg);	
                                     
                                } catch (\Exception $e) {	
                                    
                                    $bot =  \common\models\LineBot::findOne(5);
                                    $msg = "\r\n".'Delete Error'."\r\n";
                                    $msg.= Yii::t('common','{:e}',[':e' => $e]);	
                                    $msg.= $model->no."\r\n";
                                    $msg.= $model->salespeople 
                                            ? ('['.$model->salespeople->code.'] '.$model->salespeople->name)."\r\n"
                                            : ' '."\r\n";
                                    $msg.= $model->customer 
                                            ? ('['.$model->customer->code.'] '.$model->customer->name)."\r\n\r\n" 
                                            : ' '."\r\n";
                                    $msg.= number_format($model->balance,2)."฿\r\n";
                                    $msg.= $model->status."\r\n";
                                       
                                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                                    
                                    $bot->notify_message($msg);	
                                    
                                }
                                $transaction->commit();
                                return json_encode([
                                    'status'    => 200,
                                    'message'   => 'delete',
                                    'value'     => [
                                        'id'        => $id,
                                        'status'    => $model->status
                                    ],
                                ]);
        
                            }else {
                                # Error Delete Sale Header
                                $transaction->rollBack();
                                return json_encode([
                                    'status'    => 510,
                                    'message'   => 'Error Sale header',
                                    'value'     => [
                                        'id'        => $id,
                                        'status'    => $model->status
                                    ],
                                ]);
                            }
                        }                                   

                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

            }else {
                # Error Sale Header Status
                return json_encode([
                    'status'    => 501,
                    'message'   => 'error',
                    'suggestion'=> Yii::t('common','Not allow'),
                    'value'     => [
                        'id'        => $id,
                        'status'    => $model->status
                    ],
                ]);
            }

        }
        
    }



    public function actionDelete_line($id)
    {

        $status     = 200;
        $message    = Yii::t('common','Success');
        $suggestion = '';
        $undo       = [];

        // ถ้า Ship แล้ว ไม่ให้ลบรายการใน Sale Line
        // $Query = WarehouseMoving::find()
        //                   ->joinwith('header')
        //                   ->where(['warehouse_moving.SourceDoc' => (int)Yii::$app->request->post('param')['lineno']])
        //                   ->andwhere(['NOT IN','warehouse_header.status' ,['Undo', 'Adjust', 'Receive']]);

        // if($Query->exists()){            
        //     return json_encode([
        //         'status'    => 403,
        //         'message'   => Yii::t('common','Forbidden'),
        //         'value'     => (int)Yii::$app->request->post('param')['lineno']
        //     ]);
        //     exit();
        // }
 

        if((int)Yii::$app->request->post('param')['lineno']){

            $transaction = Yii::$app->db->beginTransaction();
            try {

                $SaleLine = SaleLine::findOne((int)Yii::$app->request->post('param')['lineno']);

                if($SaleLine !== null){

                    // ตรวจสอบว่ามีการผลิตหรือไม่
                    // ถ้ามีการผลิตแล้ว ให้ลบการผลิตทิ้ง (output(FG) ถูกถอดตั้งแต่ Undo Ship แล้ว)
                    
                    // ยกเลิก 31/10/2019
                    // if(WarehouseMoving::deleteAll(['AND', 'SourceDoc = :id', ['IN', 'TypeOfDocument',['Consumption','Output']]], [':id' => $id])){
                    //     self::DeleteLog((Object)['table' => 'warehouse_moving','field' => 'SourceDoc','value' => $id, 'lot' => Yii::$app->request->post('param')['lineno']]);
                    // }
                    // ตรวจทานอีกรอบ 
                    // 13/11/2019

                    // ถ้ามีผลิตให้ Undo Production
                    // ถ้ามี Undo แล้วลบได้เลย

                    $hasOutput  =\common\models\WarehouseMoving::find()
                                ->joinwith('header')
                                ->where(['warehouse_moving.order_line_table' => 'sale_line'])
                                ->andWhere(['warehouse_moving.order_line_id' => $SaleLine->id])
                                ->andWhere(['warehouse_moving.TypeOfDocument' => 'Output'])
                                ->andWhere(['<>','warehouse_header.status','Undo-Produce'])
                                ->all();
                    foreach ($hasOutput as $key => $line) {
                        

                        $undoChild =\common\models\WarehouseMoving::find()
                                    ->joinwith('header')
                                    ->where(['warehouse_moving.source_id' => $line->source_id])
                                    ->andWhere(['<>','warehouse_header.status','Undo-Produce'])
                                    ->all(); 
                        foreach ($undoChild as $key => $do) {                 
                        
                            $undo[$key] = $line->header->UndoProduction($do->header, $do);
                            
                            if($undo[$key]->status==200){
                                $line->header->status = 'Undo-Produce';
                                $line->header->save();
                            }  
                        }                       
                    
                    }
                        

                    if($SaleLine->delete()){

                        
                        // Update Sale Header to Open status
                        $model = SaleHeader::findOne($id);

                        $Policy = SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve');            
                        if(in_array(Yii::$app->session->get('Rules')['rules_id'],$Policy)){
                            
                            $model->status  = 'Open';
                            $model->confirm = '0';
                            $model->live    = 0;
                            $model->save();
                        }else{
                            $model->confirm = '0';
                            $model->live    = 0;
                            $model->save();
                        }

                        
                        
                        
                        $message    = Yii::t('common','Removed');
                         

                    }else {                         
                        $status     = 500;
                        $message    = Yii::t('common','Fail');
                    }
                    
                }else {
                    $status     = 404;
                    $message    = Yii::t('common','Not Found');
                    $suggestion = '';
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status     = 500;
                $message    = Yii::t('common','Error');
                $suggestion = Yii::t('common','{:e}',[':e' => $e]);
          
            }


            return json_encode([
                'status'    => $status,
                'message'   => $message,
                'value'     => (int)Yii::$app->request->post('param')['lineno'],
                'suggestion'=> $suggestion,
                'undo'      => $undo
            ]);

        }
    }

    public function actionPrintPage($id)
    {
        
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        if($model->customer_id == ''){
            echo '<script>
                    alert("Error! '.Yii::t('common','Customer').'");
                    window.location.href = "index.php?r=SaleOrders/saleorder/update&id='.$model->id.'";
                    window.close();
                    </script>';
            exit();
            //return $this->redirect(['update','id' => $model->id]);
        }


        $query   = SaleLine::find()
        ->where(['sourcedoc' => $model->id])
        ->andwhere(['comp_id' => $company]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);

        //$dataProvider->query->where(['order_no' => $model->no]);
        //$dataProvider->query->andwhere(['comp_id' => $company]);



        $text1 = '<div style="font-size:21px;">
                    - หากมีปัญหาเกี่ยวกับตัวสินค้า โปรดติดต่อกลับทางบริษัทฯ ภายใน 7 วัน<br>
                    - กรณีชำระด้วยเช็คโปรดสั่งจ่าย และขีดคร่อมในนาม บริษัท จีโนล กรุ๊ป ซีที อิเล็คทริคฟิเคชั่น จำกัด เท่านั้น<br>
                    &nbsp;&nbsp;และการชำระเงินจะสมบูรณ์ต่อเมื่อเช็คนั้นเรียกเก็บจากธนาคารได้ครบถ้วนแล้ว<br>
                    - กรณีโอนเงิน โอนในนาม บริษัท จีโนล กรุ๊ป ซีที อิเล็คทริคฟิเคชั่น จำกัด ธนาคารกสิกรไทย สาขาถนนเศรษฐกิจ 1 บัญชีเลขที่ 464-1-02799-0'."
                  '</div><br>\r\n";

        $text2 = '<div style="font-size:21px;">
                    <br><br>
                    <p style="padding: 2px;">
                    จ่ายชำระเงิน (...) เงินโอน  (...) เงินสด (...) เช็คเลขที่................. </p>
                    <p>ลงวันที่...................ธนาคาร........................สาขา................ </p>
                    <p>จำนวนเงิน.................... บาท (...........................................)</p>
                 </div>';

        $PageHeader = $this->renderPartial('_print_page',[
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'text1' => '',
                    'text2' => '',
                ]);



        // get your HTML raw content without any layouts or scripts

        $content = $this->renderPartial('_print_page_body',[
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'text1' => '',
                    'text2' => '',
                ]);





        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format

            // Pdf::FORMAT_A3 or A3

            // Pdf::FORMAT_A4 or A4

            // Pdf::FORMAT_LETTER or Letter

            // Pdf::FORMAT_LEGAL or Legal

            // Pdf::FORMAT_FOLIO or Folio

            // Pdf::FORMAT_LEDGER or Ledger-L

            // Pdf::FORMAT_TABLOID or Tabloid

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
            'filename' => $model->no.'.pdf',
            // any css to be embedded if required
            'cssInline' => '@page {margin: 0;} body{font-family: saraban, sans-serif; font-size:11px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'INVOICE : '.$model->no.' ',],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=> [''.$PageHeader.''],
                //'SetFooter'=>['{PAGENO}'],
                //'SetDisplayMode' => 'fullpage',
                //'SetPageTemplate' => '2',
                //'SetWatermarkText' => "Paid",
                'WriteHTML' => $PageHeader,

                // 'autoScriptToLang' => true,
                // 'autoLangToFont' => true,
                // 'direction' => 'rtl',


            ]




        ]);

        Yii::$app->session->set('vat',$model->include_vat);        
        if($model->vat_percent <= 0){
            // Print with form
            $pdf->format = Pdf::FORMAT_A4;
            $pdf->content = $this->renderPartial('_print_page_body',[
                                'model' => $model,
                                'dataProvider' => $dataProvider,
                                'text1' => '',
                                'text2' => '',
                            ]);
            $pdf->methods = [
                                'WriteHTML' => $this->renderPartial('_print_page',[
                                                'model' => $model,
                                                'dataProvider' => $dataProvider,
                                                'text1' => '',
                                                'text2' => '',
                                            ]),
                            ];
        }
        if(isset($_GET['download']))
        {
            $pdf->destination = Pdf::DEST_DOWNLOAD;
        }

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


    public function actionPrint($id)
    {
              

        $model      = $this->findModel($id);

        $query      = SaleLine::find()
        ->where(['sourcedoc' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->orderBy(['id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            //'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]],
        ]);

        $template      = \common\models\PrintPage::findOne(\common\models\PrintPage::findCustomPrint( $model->vat_percent > 0 ? 'sale_order' : 'sale_order_clean' ));
  
        $Company  = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

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
            '{VALUE_TITLE}'         => $model->no,
            '{COMPANY_LOGO}'        => '<img src="'.Yii::$app->session->get('logo').'" style="width: 100px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_ID}'          => $Company->id.' '.$model->id,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $model->no,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->order_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            //'{HEAD_OFFICE}'         => $Company->headofficetb->data_char,     
            '{HEAD_OFFICE}'         => $model->customer 
                                            ? $model->customer->headofficetb->data_char
                                            : '',                 
         
            '{CREATOR}'             => $model->customer 
                                        ? $model->salespeople->name
                                        : '',        
            '{CUSTOMER_CODE}'       => $model->customer 
                                        ? $model->customer->code
                                        : '',
            '{CUSTOMER_NAME}'       => $model->customer 
                                        ? $model->customer->name
                                        : '',
            '{CUSTOMER_PHONE}'      => $model->customer 
                                        ? $model->customer->phone
                                        : '',
            '{CUSTOMER_FAX}'        => $model->customer 
                                        ? $model->customer->fax
                                        : '',
            '{CUSTOMER_ADDRESS}'    => $model->customer 
                                        ? $model->customer->fullAddress['address']
                                        : '',
            '{CUSTOMER_TAX}'        => $model->customer 
                                        ? $model->customer->vat_regis
                                        : '',        
            '{SALE_NAME}'           => $model->salespeople 
                                        ? $model->salespeople->name
                                        : '',
            '{SALE_SUR_NAME}'       => $model->salespeople 
                                        ? $model->salespeople->surname
                                        : '',
            '{SALE_CODE}'           => $model->salespeople 
                                        ? $model->salespeople->code
                                        : '',                  
         
            '{REF_TO}'              => $model->ext_document,        
         
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term > 0 ? $model->payment_term.' '.Yii::t('common','Day') : Yii::t('common','Cash'),
            '{DUE_DATE}'            => (date('Y',strtotime($model->paymentdue)) > 1970)?  date('d/m/y',strtotime($model->paymentdue.' + 543 Years')) : " ",
            '{DUE_DATE_EN}'         => $model->paymentdue,
            '{PO_REFERENCE}'        => $model->ext_document,
        
            '{VALUE_BEFOR_VAT}'     => number_format($model->sumtotal->subtotal - $model->sumtotal->incvat,2),
        
            '{IF_VAT_TYPE_COLSPAN}' => ($model->include_vat===1)?  '6'  : '5',
            '{IF_VAT_TYPE_ROWSPAN}' => ($model->include_vat===1)?  '4'  : '5',
            '<!--IF_VAT_TYPE-->'    => ($model->include_vat===1)? '<!--': ' ' ,
            '<!--IF_VAT_TYPE_END-->'=> ($model->include_vat===1)? '-->' : ' ',  
            
            '{VALUE_PERCENT_DISCOUNT}' => ($model->percent_discount)? '('.number_format($model->percent_discount).' %)' : '',
         
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => wordwrap($model->remark, 250, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->sumtotal->sumline,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->sumtotal->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->sumtotal->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->vat_percent.' %',
            '{VALUE_INCLUDEVAT}'    => number_format($model->sumtotal->incvat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->sumtotal->total,2),
            '{VALUE_BAHTTEXT}'      => $model->sumtotal->total == 0 ? 'ศูนย์บาทถ้วน' : $Bahttext->ThaiBaht($model->sumtotal->total),     
             
        ]; 


 
        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'print' => $template,
            'Company' => $Company,
            'header' => $header,
            'body'  => $body,
            'defineHeader' => $defineHeader
        ]);
  
        $content = $this->renderPartial('_print_content',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
            'print' => $template,
            'body'  => $body
        ]);

  
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            'orientation'   => $template->paper_orientation,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            'filename'      => $model->no.'.pdf',
            'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } ',
            'options'       => [
                'title' => 'SO : '.$model->no.' '
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


    

    public function actionPrintShip($id)
    {

        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->where(['order_no' => $model->no]);
        $dataProvider->query->where(['sourcedoc' => $model->id]);
        $dataProvider->query->andwhere(['comp_id' => $company]);









        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('__print_ship',[
                    'model' => $model,
                    'searchModel' => $searchModel,
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
            'filename' => 'transport_'.$model->no.'.pdf',
            // any css to be embedded if required
            //'cssInline' => '.bd{border:1.5px solid; text-align: center;} .ar{text-align:right} .imgbd{border:1px solid}',
            // set mPDF properties on the fly
            'options' => ['title' => 'transport : '.$model->no.' '],
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


    /**
     * Finds the SaleHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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


        //$file           = Yii::getAlias('@webroot').'/uploads/file.pdf';
        $source         = Yii::getAlias('@webroot').'/uploads/file/temp';


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
                        'outputDir'         => $source, // output dir
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

        return $this->renderPartial('_import_file',['text' => $text,'page' => $countPages]);
    }


    public function actionClearSaleLine(){

        $id = $_POST["id"];
        $status = 0;
        $message = 'Nothing';
        // \Yii::$app
        //     ->db
        //     ->createCommand()
        //     ->delete('sale_line', ['sourcedoc' => $_POST['id'],'api_key' => Yii::$app->session->getId()])
        //     ->execute();
        $models = SaleLine::find()->where(['sourcedoc' => $id])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();

        foreach ($models as $key => $model) {

            $shiped = \common\models\WarehouseMoving::find() //->where(['warehouse_moving.SourceDoc' => $model->id])
                    ->joinwith('header')                    
                    ->where(['warehouse_header.order_id' => $model->id])
                    ->andwhere(['<>','warehouse_header.status','Undo']);
    
            if($shiped->exists()){
                $status = 500;
                $message = 'Shiped';
            }else {                
                $model->delete(); 
                $status = 200;
                $message = 'done';
            }
            
        }
        // เอาแค่นี้ก่อน (รีบ)
        return json_encode([
                    'status' => $status,
                    'message' => $message,
                    'value' => [
                        'source' => $id
                    ]
                ]);
        

    // if(SaleLine::deleteAll('sourcedoc = :source AND user_id = :user AND comp_id =:comp', 
        //     [
        //         ':user' => Yii::$app->user->identity->id, 
        //         ':source' => $id,
        //         ':comp' => Yii::$app->session->get('Rules')['comp_id']
        //     ]
        //     )){
        //     return json_encode([
        //         'status' => 200,
        //         'message' => 'done',
        //         'value' => [
        //             'source' => $id
        //         ]
        //     ]);
        // }else {
        //     return json_encode([
        //         'status' => 500,
        //         'message' => 'Error',
        //         'value' => [
        //             'source' => $id
        //         ]
        //     ]);
        // }
        
    }

    public function actionNotInvoice(){
        $searchModel = new ViewNotInvSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=50;

        return $this->render('index-not-inv', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

        
    }


    public function actionShipment(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = '';
        $status         = 200;
        $message        = Yii::t('common','Success');
        $SHIPMENT       = '';
        $raws           = [];
        $GenSeries      = new Generater();
        $model          = SaleHeader::findOne($data->id);

        if($model->confirm=='0'){
            return json_encode([
                'status'    => 403,
                'message'   => Yii::t('common','Waiting Confirm'),
            ]);
        }else{

        

            $transaction = Yii::$app->db->beginTransaction();
            try { 
                
                $model                  = SaleHeader::findOne($data->id);

                $Header                 = new WarehouseHeader();             

                $Header->line_no        = $model->id;
                $Header->PostingDate    = date('Y-m-d',strtotime($model->order_date)).' '.date('H:i:s');
                $Header->DocumentDate   = isset($data->ship_date)? $data->ship_date : date('Y-m-d');
                $Header->TypeOfDocument = "Sale";
                $Header->SourceDocNo    = $model->id;
                $Header->DocumentNo     = $GenSeries->GenerateNoseries('Shipment',true);
                $Header->customer_id    = $model->customer_id;
                $Header->SourceDoc      = $model->no;
                $Header->order_id       = $model->id;

                $transport              = TransportList::findOne(isset($data->transport) ? $data->transport : NULL);

                $Header->Description    = $transport ? $transport->name : NULL;
                $Header->transport_id   = $transport ? $transport->id : NULL;
                $Header->Quantity       = 0;
                $Header->address        = '';
                $Header->address2       = '';
                $Header->district       = 0;
                $Header->city           = 0;
                $Header->province       = 0;
                $Header->postcode       = 0;
                $Header->contact        = '';
                $Header->phone          = isset($data->ship_phone)? $data->ship_phone : $model->customer->phone;
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
                $Header->ship_address   = isset($data->ship_address)? $data->ship_address : $model->ship_address;
                $Header->ship_name      = isset($data->ship_name)? $data->ship_name : $model->customer->name;

                if($Header->save()){
                    $id         = $Header->id;
                    $SHIPMENT   = $Header->DocumentNo;

                    $SaleLine   = SaleLine::find()->where(['sourcedoc' => $data->id])->all();
                    foreach ($SaleLine as $key => $line) {                  

                        
                        $raws[] = [
                                //'inven'     => $line->items->invenByBom,                            
                                'status'    => true,
                                //'production'=> $Header->producer($line, $Header, $line->quantity),
                                'shipment'  => $Header->shipment($line, $Header, $line->quantity)
                        ];
                        

                    }

                    $model->status          = 'Shiped';
                    $model->transport       = $Header->Description;
                    $model->transport_id    = $Header->transport_id;
                    $model->ship_date       = isset($data->ship_date)? $data->ship_date : date('Y-m-d');


                    $model->save();

                    // update customer
                    $cust                   = Customer::findOne($model->customer_id);
                    if($cust!=null){                    
                        $cust->ship_name            = $Header->ship_name;
                        $cust->ship_address         = $Header->ship_address;
                        $cust->transport            = $Header->Description;
                        $cust->default_transport    = $Header->transport_id;
                        $cust->save();
                    }

                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status     = 500;
                $message    = Yii::t('common','{:e}',[':e' => $e]);
            }
        } 

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'no'        => $SHIPMENT,
            'id'        => $id,
            'raws'      => $raws
        ]);
        
    }

    protected static function checkNumber($no){
        return \common\models\ViewRcInvoice::find()->where(['no_' => $no, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
    }

    public function actionCreateInvoiceFromItem(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $order_date     = $data->order_date;
        $raw            = [];
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = '';
        $returnId       = 0;
        $invNo          = '';


        
        $transaction    = Yii::$app->db->beginTransaction();
        try{
             
            $Already        = self::checkNumber($data->no);
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
                                $SaleInvoiceHeader  = SaleInvoiceHeader::find()->where(['no_' => $data->no])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();
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
                        $RcInvoiceHeader    = RcInvoiceHeader::find()
                                            ->where(['no_' => $data->no])
                                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            ->one();

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

            }else{

            

                // DO CREATE        
                 
                $customer                   = \common\models\Customer::findOne($data->custId);
                $salePeople                 = \common\models\SalesPeople::findOne(Yii::$app->session->get('Rules')['sale_id']);
                // Create Invoice header
                $inv_header                 = new RcInvoiceHeader();
                
                $inv_header->no_            = $data->no;

                $inv_header->cust_no_ 		= $customer->id;
                $inv_header->cust_name_		= $customer->name;
                $inv_header->document_no_	= NULL;
                $inv_header->posting_date 	= $order_date;
                $inv_header->doc_type 		= 'Sale';            
                $inv_header->order_id 		= NULL;
                $inv_header->sales_people 	= $salePeople ? $salePeople->code : '';
                $inv_header->sale_id 		= $salePeople ? $salePeople->id : '';
                $inv_header->cust_code 		= $customer->code;
                $inv_header->order_date	 	= $order_date;
                $inv_header->ship_date 		= $order_date;
                $inv_header->cust_address 	= $customer->address;
                $inv_header->cust_address2 	= $customer->address2;
                $inv_header->phone 			= $customer->phone;
                $inv_header->district 		= $customer->district;
                $inv_header->city 			= $customer->city;
                $inv_header->province 		= $customer->province;
                $inv_header->postcode 		= $customer->postcode;
                $inv_header->discount 		= 0;
                $inv_header->percent_discount= 0;
                $inv_header->vat_percent 	= $data->vat;
                $inv_header->include_vat	= $data->vat > 0 ? 1 : 0; // 1 = Vat นอก                
                $inv_header->paymentdue		= date('Y-m-d');
                $inv_header->payment_term 	= 0;
                $inv_header->ext_document	= NULL;
                $inv_header->remark 		= $data->remark;
                $inv_header->status 		= 'Posted';
                $inv_header->ref_inv_header = NULL;
                $inv_header->user_id 		= Yii::$app->user->identity->id;
                $inv_header->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $inv_header->host 			= gethostname();
                $inv_header->session_id     = Yii::$app->session->getId();
                $inv_header->extra          = NULL;

                $lastRefNo                  = '';
                if($inv_header->save()){
                    
                    $returnId                   = $inv_header->id;
                    $invNo                      = $inv_header->no_;
                     

                    foreach ($data->items as $line) {
    
                        $RcInvoiceLine                      = new RcInvoiceLine();
                        $RcInvoiceLine->type 			    = 'Item';
                        $RcInvoiceLine->item		 	    = $line->item;
                        $RcInvoiceLine->measure             = 1;
                        $RcInvoiceLine->doc_no_ 		    = $inv_header->no_;
                        $RcInvoiceLine->line_no_ 		    = $line->item;
                        $RcInvoiceLine->source_id 		    = $inv_header->id;
                        $RcInvoiceLine->customer_no_	    = $inv_header->cust_no_;
                        $RcInvoiceLine->code_no_		    = $line->code;
                        $RcInvoiceLine->code_desc_		    = $line->name;
                        $RcInvoiceLine->quantity 		    = $line->qty;
                        $RcInvoiceLine->unit_price 		    = $line->price * 1;
                        $RcInvoiceLine->line_discount       = 0;
                        $RcInvoiceLine->vat_percent 	    = $data->vat;
                        $RcInvoiceLine->order_id 		    = NULL;
                        $RcInvoiceLine->source_doc		    = $line->no;
                        $RcInvoiceLine->source_line		    = $line->id;
                        $RcInvoiceLine->session_id 		    = $inv_header->session_id;
                        $RcInvoiceLine->posting_date        = $inv_header->posting_date;
                        $RcInvoiceLine->quantity_to_stock   = $line->qty;
                        $RcInvoiceLine->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

                        if($RcInvoiceLine->save()){
                            

                            $raw[]      = (Object)[
                                'status'    => 200,
                                'id'        => $RcInvoiceLine->id,
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
                        $lastRefNo = $line->no;
                        $source = SaleHeader::findOne($line->source_id);
                        if($source != null){
                            $source->op_to_ct = 2; // เปิดบิลแล้ว
                            $source->save();
                        }
                        
                    }

                    $UpdateSeries               = Generater::CreateNextNumber('vat_type','vat_value',$inv_header->vat_percent, $data->no);
                    $inv_header->balance        = $inv_header->sumtotals->total;
                    $inv_header->ext_document   = $lastRefNo;
                    $inv_header->save();
                }else{
                    $status     = 500;
                    $message    = Yii::t('common','Error');
                    $suggestion = Yii::t('common','{:e}',[':e' => json_encode($inv_header->getErrors(),JSON_UNESCAPED_UNICODE)]);
                }

                
            }

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

    public function actionCreateInvoice(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $id             = $data->id;
        $order_date     = $data->date;
        $raw            = [];
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = '';
        $returnId       = 0;
        $invNo          = '';


        
        $transaction    = Yii::$app->db->beginTransaction();
        try{
             
            $Already        = self::checkNumber($data->no);
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
                                $SaleInvoiceHeader  = SaleInvoiceHeader::find()->where(['no_' => $data->no])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();
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
                        $RcInvoiceHeader    = RcInvoiceHeader::find()
                                            ->where(['no_' => $data->no])
                                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            ->one();

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

            }else{

            

                // DO CREATE        
                $model                      = $this->findModel($id); // Sale Header
                
                // Create Invoice header
                $inv_header                 = new RcInvoiceHeader();
                
                
                $inv_header->no_            = $data->no;

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
                $inv_header->ext_document	= $model->no;
                $inv_header->remark 		= $model->remark;
                $inv_header->status 		= 'Posted';
                $inv_header->ref_inv_header = $model->id;
                $inv_header->user_id 		= Yii::$app->user->identity->id;
                $inv_header->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $inv_header->host 			= gethostname();
                $inv_header->session_id     = Yii::$app->session->getId();
                $inv_header->extra          = NULL;

                if($inv_header->save()){
                    
                    $returnId                   = $inv_header->id;
                    $invNo                      = $inv_header->no_;
                    $SaleLine                   = SaleLine::find()->where(['sourcedoc' => $model->id])->all();

                    foreach ($SaleLine as $line) {
    
                        $RcInvoiceLine                      = new RcInvoiceLine();
                        $RcInvoiceLine->type 			    = 'Item';
                        $RcInvoiceLine->item		 	    = $line->item;
                        $RcInvoiceLine->measure             = $line->unit_measure;
                        $RcInvoiceLine->doc_no_ 		    = $inv_header->no_;
                        $RcInvoiceLine->line_no_ 		    = $line->item;
                        $RcInvoiceLine->source_id 		    = $inv_header->id;
                        $RcInvoiceLine->customer_no_	    = $inv_header->cust_no_;
                        $RcInvoiceLine->code_no_		    = $line->item_no;
                        $RcInvoiceLine->code_desc_		    = $line->description?: $line->items->description_th;
                        $RcInvoiceLine->quantity 		    = $line->quantity;
                        $RcInvoiceLine->unit_price 		    = $line->unit_price * 1;
                        $RcInvoiceLine->line_discount       = $line->line_discount *1;
                        $RcInvoiceLine->vat_percent 	    = $inv_header->vat_percent;
                        $RcInvoiceLine->order_id 		    = $model->id;
                        $RcInvoiceLine->source_doc		    = $model->no;
                        $RcInvoiceLine->source_line		    = $line->id;
                        $RcInvoiceLine->session_id 		    = $inv_header->session_id;
                        $RcInvoiceLine->posting_date        = $inv_header->posting_date;
                        $RcInvoiceLine->quantity_to_stock   = $line->quantity;
                        $RcInvoiceLine->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

                        if($RcInvoiceLine->save()){
                            
                            $line->stock_reserve = 0;  // ยกเลิกจอง
                            $line->save();

                            $raw[]      = (Object)[
                                'status'    => 200,
                                'id'        => $RcInvoiceLine->id,
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

                    $UpdateSeries           = Generater::CreateNextNumber('vat_type','vat_value',$inv_header->vat_percent, $data->no);
                    $inv_header->balance    = $inv_header->sumtotals->total;
                    $inv_header->save();
                }

                // update order status
                $model->status      = 'Invoiced';
                $model->order_date  = $order_date;
                $model->save();
            }

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

    public function actionUpdateHeaderAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
       // $params         = json_decode(base64_decode($data->param));
        
        $status         = 200;
        $message        = '';

        $model          = $this->findModel($data->id);
        foreach ($data->raw as $key => $param) {
            $field              = $param->field;
            $model->$field      = $param->value;            
        }

        if(!$model->save()){
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }
        
        

        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function actionGetHeaderAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
       
        
        $status         = 200;
        $message        = '';

        $model          = $this->findModel($data->id);
        $raw            = (Object)[
            "id"            => $model->id,
            "no"            => $model->no,
            'customer_id'   => $model->customer_id,
            'customer_name' => $model->customer ? $model->customer->name : '',
            'order_date'    => $model->order_date,
            'ship_date'     => $model->ship_date,
            'paymentdue'    => $model->paymentdue,

            'sumline'           => $model->sumtotal->sumline,
            'discount'          => $model->discount *1,
            'percent_discount'  => $model->percent_discount *1,
            'vat_percent'       => $model->vat_percent,
            'include_vat'       => $model->include_vat,
            'balance'           => $model->balance *1,

            'status'        => $model->status,
            'create_date'   => $model->create_date,
            
            'sales_people'  => $model->sales_people,
            'ext_document'  => $model->ext_document,
            'payment_term'  => $model->payment_term,
            
            'remark'        => $model->remark,
        ];

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raw'       => $raw
        ]);
    }
    
    public function actionCreateOrderAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
       
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = '';
        $raw            = [];
        $transaction    = Yii::$app->db->beginTransaction();
        try {

            $no         = 'SO'.date('ym').'-0001';
            try {
                $no     = Generater::getRuning('sale_header','no','all');
                        Generater::UpdateSeries('sale_header','no','all',$no);
            } catch (\Exception $e) {
                    // Yii::$app->session->setFlash('error', Yii::t('common','Error Header {:e}', [':e' => $e]));
                    // $transaction->rollBack();
                    //return $this->redirect(['index']);
            }   
            
            $model                  = new SaleHeader();
            $model->no              = $no;
            $model->customer_id     = $data->custId;
            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->sales_people    = Yii::$app->session->get('Rules')['sale_code'];
            $model->sale_id         = Yii::$app->session->get('Rules')['sale_id'];
            $model->vat_type        = 2;
            $model->vat_percent     = $data->vat;
            $model->balance_befor_vat = 0;
            $model->paymentdue      = date('Y-m-d',strtotime(date('Y-m-d'). "+1 days"));
            $model->ship_date       = date('Y-m-d',strtotime(date('Y-m-d'). "+3 days"));
            $model->order_date      = date('Y-m-d');
            $model->create_date     = date('Y-m-d H:i:s');
            $model->status          = 'Checking';
            $model->live            = 0; // Locked
            $model->payment_term    = 0;
            $model->confirm         = count($data->items);

            $lastOrderNo            = '';
            if($model->save()){
                

                foreach ($data->items as $value) {
                    
                    $saleLine              = new SaleLine();        
                    
                    $saleLine->order_no    = $model->no;
                    $saleLine->description = $value->name;
                    $saleLine->item        = $value->item;
                    $saleLine->item_no     = $value->code;
                    $saleLine->quantity    = $value->qty;
                    $saleLine->unit_price  = $value->price;
                    $saleLine->create_date = date('Y-m-d H:i:s');
                    $saleLine->vat_percent = $model->vat_percent;
                    $saleLine->user_id     = Yii::$app->user->identity->id;
                    $saleLine->api_key     = Yii::$app->session->getId();
                    $saleLine->comp_id     = $model->comp_id;
                    $saleLine->sourcedoc   = $model->id;
                    $saleLine->unit_measure= $value->measure > 0 ? $value->measure : 1;
                    $saleLine->confirm      = $value->qty;
                    $saleLine->confirm_by   = Yii::$app->user->identity->id;
                    $saleLine->confirm_date = date('Y-m-d H:i:s');
            
                    $saleLine->save();

                    $lastOrderNo            = $value->no; // กำหนดเอกสารอ้างอง

                    $source = SaleHeader::findOne($value->source_id);
                    if($source != null){
                        $source->op_to_ct       = 2; // เปิดบิลแล้ว                       
                        $source->save();
                    }

                }

                $raw  = (Object)[
                    'id' => $model->id,
                    'no' => $model->no,
                    'line' => $model->saleLine
                ];
            }
            
            $model->balance         = $model->sumtotal->total;
            $model->ext_document    = $lastOrderNo; // Set Reference No
            $model->save();
            $transaction->commit(); 

        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','Error');
            $suggestion = Yii::t('common','{:e}', [':e' => $e]);
           
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'suggestion'=> $suggestion,
            'header'       => $raw
        ]);
         
    }
}
