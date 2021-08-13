<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\SaleQuoteHeader;
use admin\modules\SaleOrders\models\SaleQuoteHeaderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\SaleQuoteLineSearch;
use common\models\SaleQuoteLine;
use admin\models\Generater;
use common\models\Customer;
use common\models\SalesPeople;

use admin\modules\items\models\SearchPicItems;
use common\models\Items;

use common\models\ItemsHasProperty;
use common\models\Itemset;
use admin\models\FunctionCenter;

use admin\modules\SaleOrders\models\FunctionSaleOrder;

use admin\modules\tracking\models\FunctionTracking;


use common\models\TmpMenuGroup;
use common\models\VatType;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


use common\models\WarehouseMoving;

use admin\modules\apps_rules\models\SysRuleModels;

use common\models\PrintPage;
use common\models\SaleHeader;
use common\models\SaleLine;
/**
 * SaleorderController implements the CRUD actions for SaleQuoteHeader model.
 */
class QuotationController extends Controller
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
        return [
            'verbs' => [
                'class'     => VerbFilter::className(),
                'actions'   => [
                    'delete'            => ['POST'],
                    'clear-sale-line'   => ['POST'],
                    'update-line-box'   => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all SaleHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        if((Yii::$app->session->get('workyears')==NULL) || (Yii::$app->session->get('workyears')== 1970)) 
        { 
            Yii::$app->session->set('workyears',date('Y')); 
        }

        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();

        if(Yii::$app->session->get('Rules')['rules_id'] == '')
        {
            echo '<script>window.location.href = "index.php?r=site/index";</script>';
        }


        $searchModel = new SaleQuoteHeaderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        

        if(Yii::$app->request->get('month')!=''){
            $dataProvider->query->andWhere(["MONTH(sale_quote_header.order_date)" => $_GET['month']]);
            //$dataProvider->query->andWhere(["YEAR(order_date)" => isset($_GET['Y'])? $_GET['Y'] : date('Y')]);
        }


        // When filter customer (Request from customer detail)
        // Except Years filter 
        if(!isset($_GET['SaleQuoteHeaderSearch']['customer_id'])){
            //$dataProvider->query->andWhere(["YEAR(sale_quote_header.order_date)" => (Yii::$app->session->get('workyears'))? Yii::$app->session->get('workyears') : date('Y')]);
        }
        

        $dataProvider->query->andWhere(["sale_quote_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);

        if(Yii::$app->request->get('SaleQuoteHeaderSearch')['order_date']!=''){
            
            if (!is_null($_GET['SaleQuoteHeaderSearch']['order_date']) && 
                strpos($_GET['SaleQuoteHeaderSearch']['order_date'], ' - ') !== false ) {
                list($start_date, $end_date) = explode(' - ', $_GET['SaleQuoteHeaderSearch']['order_date']);

                $dataProvider->query->andFilterWhere(['between', 'DATE(sale_quote_header.order_date)', $start_date, $end_date]);

            }
            //echo 'date '.$start_date. ' '.$end_date;
        }
        $dataProvider->query->orderBy(['sale_quote_header.create_date' => SORT_DESC]);
        $dataProvider->pagination->pageSize=50;
        //echo $dataProvider->query->createCommand()->getRawSql();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SaleHeader model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        if($model->balance <= 0){

          return $this->redirect(['/SaleOrders/quotation/update', 'id' => $model->id]);
        }

        $searchModel = new SaleQuoteLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->where(['order_no' => $model->no]);
        $dataProvider->query->where(['sourcedoc' => $model->id]);
        $dataProvider->query->andwhere(['comp_id' => $company]);
        $dataProvider->pagination=false;


        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        //var_dump($searchModel);
        
    }


    public function actionViewitem()
    {
        $session = Yii::$app->session;
        $session->set('ew-set', (object)['pval' => NULL,'pid' => NULL]);  // Clear pval

        #1 Set นี้มี Item อะไรบ้าง
        #2 ในแต่ละ Item มี Property อะไรบ้าง
            #2.1 Property มีค่าเท่าไหร่บ้าง

        # Find Set (1)
        $FincSale = new FunctionSaleOrder();


        $InSet = Items::find()->where(['itemset' => $_POST['param']['itemset']])->all();


        // Clear list before call menu.
        //$sql = "DELETE FROM tmp_menu_group WHERE user_id = '".Yii::$app->user->identity->id."'  ";
        //AND  session_id = '".Yii::$app->session->getId()."'

        /*
        * Clear Temp
        * $sql = "DELETE FROM tmp_menu_group WHERE session_id = '".Yii::$app->session->getId()."'";
        * Yii::$app->db->createCommand($sql)->execute();
        */
        $Temp = \common\models\TmpMenuGroup::deleteAll(['session_id' => Yii::$app->session->getId()]);


        // Create set (By insert to table).
        foreach ($InSet as $items) {
                $FincSale->ItemSet($items->No);
        }


        $model = new ItemsHasProperty();
        $dataProvider = $model->find()
        ->where(['property_id' => $_POST['param']['itemset']])
        ->where(['Items_No' => $_POST['param']['itemno']])
        ->orderBy(['priority' => SORT_ASC])
        ->all();

        return $this->renderpartial('../modal/_modal_pickitem',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'orderno' => $_POST['param']['orderno'],
            ]);
        // return $this->renderpartial('_modal_pickitem',[
        //     'model' => $model,
        //     'dataProvider' => $dataProvider,
        //     'orderno' => $_POST['param']['orderno'],
        //     ]);
    }

    public function actionCreate_saleline()
    {
        $id = $_POST['param']['soid'];

        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = new SaleQuoteLine();

        // Get data from Sale Header
        $Header = SaleQuoteHeader::find()
        ->where(['id' => $id])
        ->andWHere(['comp_id' => $company])
        ->one();

        // Sale Line
        $model->order_no    = $_POST['param']['orderno'];
        if(isset($_POST['param']['desc'])){
            $ItemDesc = $_POST['param']['desc'];
        }else {
            $ItemDesc = NULL;
        }
        $model->description = $ItemDesc;

        // ถ้า ไม่มี Item
        // ให้ไปดึงจาก session (Register from /Modules/Manufacturing/model/FunctionManufac.php #CreateBom)

        if($_POST['param']['itemno'] == 'eWinl'){
            $Item 			= Items::find()->where(['No' => Yii::$app->session->get('item_no')])->one();
            $model->item    = $Item->id;
            $model->item_no = $Item->No; 
        }else{
            $Item 			= Items::find()->where(['No' => $_POST['param']['itemno']])->one();
            $model->item    = $Item->id;
            $model->item_no = $_POST['param']['itemno'];
            
        }
        
        // $model->item_no     = Yii::$app->session->get('item_no');
        $model->quantity    = $_POST['param']['amount'];
        $model->unit_price  = $_POST['param']['price'];
  
        $model->create_date = date('Y-m-d H:i:s');
        $model->vat_percent = $Header->vat_percent;
        $model->user_id     = Yii::$app->user->identity->id;
        $model->api_key     = Yii::$app->session->getId();
        $model->comp_id     = $company;
        $model->sourcedoc   = $Header->id;


        // แยก Vat
        $model->unit_price_exvat = ($model->unit_price) ? ($model->unit_price * 100) /107 : 0;




        if($model->save())
        {

            // Session Clear 
            Yii::$app->session->set('item_no',' ');

            // Update Sale Header to Open status
            $Policy = SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve');            
            if(in_array(Yii::$app->session->get('Rules')['rules_id'],$Policy)){
                //if(Yii::$app->session->get('Rules')['rules_id'] == 3){ // Sales
                $Header->status = 'Open';
                $Header->save(false);
            }
            
            $query   = SaleQuoteLine::find()
            ->where(['sourcedoc' => $id])
            ->andwhere(['comp_id' => $company]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,

            ]);

            return $this->renderAjax('_saleline',[
                'model' => $model, 
                'dataProvider' => $dataProvider
                ]);
            
            

            

        }else {
            Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
        }
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
        $model      = SaleQuoteLine::findOne($_POST['param']['lineno']);

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
            $Header = SaleQuoteHeader::findOne($model->sourcedoc);                    
            $Header->status = 'Open';
            $Header->save(false);
        }
        
        $query   = SaleQuoteLine::find()
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
                $model  = SaleQuoteLine::findOne($_POST['key']);
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

                $query   = SaleQuoteLine::find()
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
        $so = base64_decode($id);
        if(Yii::$app->request->isAjax){            
            $dataArr    = $_POST['data'];
            $model      = SaleQuoteLine::findOne($dataArr[0]['key']);
            foreach($dataArr as $data){
                $field          = (string)$data['name'];
                $model->$field  = $data['val'];
            }
            if($model->save()){
                return json_encode([
                    'status'    => 200,
                    'message'   => 'done',
                    'value'     => [
                            'item'      => $model->item,
                            'data'      => $dataArr,
                            'id'        => $model->id,
                            'so'        => $model->sourcedoc,
                            'name'      => $model->description ?: $model->items->description_th,
                            'sumline'   => $model->unit_price * $model->quantity,
                             
                    ]
                ]);
            }else {
                return json_encode([
                    'status'    => 200,
                    'message'   => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                    'value'     => [
                            'item'      => $model->item,
                            'id'        => $model->id,
                            'so'        => $model->sourcedoc,
                            'name'      => $model->description ?: $model->items->description_th,
                            'sumline'   => $model->unit_price * $model->quantity,
                            'data'      => $dataArr
                    ]
                ]);
                
            }               
        
            
        }
    }

    /**
     * Creates a new SaleHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $Fn = new FunctionCenter();
        $Fn->RegisterRule();


        $model      = new SaleQuoteHeader(); 
        if(isset($model->findEmpty)){ return $this->redirect(['update', 'id' => $model->findEmpty->id]); }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            
            $NoSeries   = new Generater();

            $model->no              = Generater::getRuning('sale_quote_header','no','all');

            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->sales_people    = Yii::$app->session->get('Rules')['sale_code'];
            $model->sale_id         = Yii::$app->session->get('Rules')['sale_id'];
            $model->vat_type        = 2;
            $model->vat_percent     = 0;
            $model->balance_befor_vat = 0;

            $date = date('Y-m-d');
            $date1 = str_replace('-', '/', $date);
            $model->paymentdue      = date('Y-m-d',strtotime($date1 . "+1 days"));
            $model->ship_date       = date('Y-m-d',strtotime($date1 . "+3 days"));
            $model->order_date      = date('Y-m-d');
            $model->create_date     = date('Y-m-d H:i:s');
            $model->status          = 'Open';
            $model->payment_term    = 0;
            $model->customer_id     = 909;

            if(!$model->save()){
                $transaction->rollBack();
                Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
                return $this->redirect(['index']);
            }

            $TrackRemark = 'Status : '.$model->status.', Credit : '.$model->payment_term.' : '.date('Y-m-d', strtotime($model->paymentdue));
            FunctionTracking::CreateTracking(
                        [
                            'doc_type'          => 'Sale-Order',
                            'doc_id'            => $model->id,
                            'doc_no'            => $model->no,
                            'doc_status'        => 'Create',
                            'amount'            => $model->balance,
                            'remark'            => $TrackRemark,
                            'track_for_table'   => 'sale_quote_header',
                            'track_for_id'      => $model->id,
                        ]);

            Generater::UpdateSeries('sale_quote_header','no','all',$model->no);

            $transaction->commit();
            return $this->redirect(['update', 'id' => $model->id]);
         } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
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

            if($model->vat_type == 2) // 1 = Vat, 2 = No Vat
            {
                // ถ้าเป็น ​​ No Vat ให้ไปใช้ field unit_price ปกติ
                // โดยกำหนด include_vat = 0 หมายถึง ไปใช้ unit_price_exvat
                $model->include_vat = 1;
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id, 'action' => 'saved']);
        }
    }

    public function actionUpdateSomeField($id){

        $model  = $this->findModel($id);
        $field  = $_POST['field'];
        $data   = $_POST['data'];

        $model->$field = $data;

        if($model->save()){
            return json_encode([
                'status' => 200,
                'field' => $field,
                'data' => $data
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }

        
       
    }
    
    public function actionUpdate($id)
    {
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $model      = $this->findModel($id);             
        
        if(Yii::$app->request->post('ajax')){
            /**
            * Update Sale Line
            */
            $data   = $_POST['data'];
            $field  = (string)$_POST['name'];

            $Line   = SaleQuoteLine::findOne($_POST['key']);
            $Line->$field       = $data;
            $Line->sourcedoc    = $id;

             
            if($Line->save()){
                return json_encode([
                    'status' => 200,
                    'message' => 'done',
                    'value' => [
                        'id'    => $Line->id,
                        'val'   => $Line->$field,
                        'total' => $Line->quantity * $Line->unit_price
                    ]
                ]);
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE)
                ]);             
            }

        }


        
        

        if ($model->load(Yii::$app->request->post())) {            
                
            $transaction = Yii::$app->db->beginTransaction();

            try {

                 
                //$model->customer_id     = $model->customer_id;
                $model->update_by           = Yii::$app->user->identity->id;
                $model->update_date         = date('Y-m-d H:i:s');    
                $model->vat_percent         = $model->vat_percent;       
                $model->balance             = $model->sumtotal->total;
                $model->balance_befor_vat   = $model->sumtotal->sumline;
                // $originalDate           = $model->paymentdue;
                // $date1                  = str_replace('/', '-', $originalDate);
                // $newDate                = date("Y-m-d", strtotime($date1));
                // $model->paymentdue      = $model->paymentdue;
                $model->order_date          = date('Y-m-d');
                
                // Update Sale people again.
                // For make sure this order.
                $SALES                      = SalesPeople::findOne($model->sale_id);
                $model->sales_people        = $SALES->code;
                $model->sale_id             = $SALES->id;
                $model->include_vat         = $model->include_vat;

                $model->confirm             = '0';              
                
                if($model->vat_percent <= 0)  
                {
                    // ถ้าเป็น ​​ No Vat ให้ไปใช้ field unit_price ปกติ
                    // โดยกำหนด include_vat = 0 (Vat ใน) , 1= (Vat นอก)
                    $model->include_vat = 1;
                }
                 
                if($model->save()){
                    
                    Yii::$app->getSession()->addFlash('info','<i class="far fa-save"></i> '.Yii::t('common','Saved'));     
                    $transaction->commit();              
                    return $this->redirect(['view', 'id' => $model->id]);       
                                
                }else{
                    Yii::$app->session->addFlash('danger', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                    $transaction->rollBack();
                    return $this->redirect(['update', 'id' => $model->id]);
                }

                
                
                //return $this->redirect(['update', 'id' => $model->id]);

            } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
            }

        } 


        $query          = SaleQuoteLine::find()->where(['sourcedoc' => $id])->andwhere(['comp_id' => $company]);
        $dataProvider   = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);   

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);

    }


    

    public function actionUpdatecus($id)
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        // ถ้ายังไม่ Undo Ship ไม่ให้แก้ลูกค้า
        $SaleLine = SaleQuoteLine::find()->where(['sourcedoc' => $id])->all();
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
        $checkStatus = SaleQuoteHeader::findOne($id);
        if(!in_array($checkStatus->status, ['Open','release'])){
            //echo '<script>alert("'.Yii::t('common','Not allow change customer.').'"); </script>';
            return '0';
            exit();
        }
        $model                  = SaleQuoteHeader::find()->where(['id' => $id])->andWhere(['or',['status' => 'Open'],['status' => 'release']])->one();
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
            echo '<script>window.location.href = "index.php?r=SaleOrders/quotation/update&id='.$id.'";</script>';

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
        $model = $this->findModel($id);

            $transaction = Yii::$app->db->beginTransaction();
		    try {
                    
                    if(SaleQuoteLine::find()->where(['sourcedoc' => $id])->exists()){
                        # Exists Sale Line
                        if(SaleQuoteLine::deleteAll(['sourcedoc' => $id])){
                            # Delete Sale Line
                            if($this->findModel($id)->delete()){
                                # Delete Sale Header
                                $transaction->commit();
                                return $this->redirect(['index']);
        
                            }else {
                                # Error Delete Sale Header
                                $transaction->rollBack();
                                return $this->redirect(['index']);
                            }
    
                        }else {
                            # Error Delete Sale Line
                            $transaction->rollBack();
                            return $this->redirect(['index']);
                        }

                    }else {
                        // Empty Sale Line
                        if($this->findModel($id)->delete()){
                            # Delete Sale Header
                            $transaction->commit();
                            return $this->redirect(['index']);
    
                        }else {
                            # Error Delete Sale Header
                            $transaction->rollBack();
                            return $this->redirect(['index']);
                        }
                    }                                   

            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

        
        
    }



    public function actionDelete_line()
    {

        if(isset($_POST['param']['lineno'])){

            $SaleLine = SaleQuoteLine::findOne($_POST['param']['lineno']);

            if($SaleLine !== null){

                if($SaleLine->delete()){
                     
                    // Update Sale Header to Open status
                    $Policy = SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve');            
                    if(in_array(Yii::$app->session->get('Rules')['rules_id'],$Policy)){

                        $model = SaleQuoteHeader::findOne($_POST['param']['orderno']);                    
                        $model->status = 'Open';
                        $model->save(false);

                    }                    
                    
                    return json_encode([
                        'status'    => 200,
                        'message'   => 'Delete',
                        'value'     => $_POST['param']['lineno']
                    ]);

                }else {

                    return json_encode([
                        'status'    => 500,
                        'message'   => 'Fail',
                        'value'     => $_POST['param']['lineno']
                    ]);

                }
                
                
            }else {
                return json_encode([
                    'status'    => 404,
                    'message'   => 'Not Found',
                    'value'     => $_POST['param']['lineno']
                ]);
            }
            
    
        }
        


    }


    public function actionPrint($id)
    {

        $model      = $this->findModel($id);

        $query      = SaleQuoteLine::find()
        ->where(['sourcedoc' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['priority'=>SORT_ASC]],
        ]);

        $template      = PrintPage::findOne(PrintPage::findCustomPrint('sale_quote'));
  
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
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 110px;">',
            '{COMPANY_NAME}'        => $Company->name,
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
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,                  
         
            '{CREATOR}'             => $model->salespeople->name,        
            '{CUSTOMER_CODE}'       => $model->customer->code,
            '{CUSTOMER_NAME}'       => $model->customer->name,
            '{CUSTOMER_PHONE}'      => $model->customer->phone,
            '{CUSTOMER_FAX}'        => $model->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->customer->locations->address,
            '{CUSTOMER_TAX}'        => $model->customer->vat_regis,        
            '{SALE_NAME}'           => $model->sales->name,
            '{SALE_CODE}'           => $model->sales->code,                  
         
            '{REF_TO}'              => $model->ext_document,        
         
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term > 0 ? $model->payment_term.' '.Yii::t('common','Day') : Yii::t('common','Cash'),
            '{DUE_DATE}'            => $model->paymentdue,
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
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht($model->sumtotal->total),     
             
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

        // get your HTML raw content without any layouts or scripts
  
        $content = $this->renderPartial('_print_content',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
            'print' => $template,
            'body'  => $body
        ]);
  
  

 
        
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            // portrait orientation //ORIENT_LANDSCAPE,ORIENT_PORTRAIT
            'orientation'   => $template->paper_orientation,
            // stream to browser inline
            'destination'   => Pdf::DEST_BROWSER,
            
            // your html content input
            'content'       => $content,
            //'cssFile'       => 'https://use.fontawesome.com/releases/v5.0.13/css/all.css',
            'filename'      => $model->no.'.pdf',
            // any css to be embedded if required @page {margin: 0; }
            'cssInline'     => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px; }',
            // set mPDF properties on the fly
            'options'       => [
                'title' => 'SQ : '.$model->no.' ',
            ],
            // call mPDF methods on the fly
            'methods'       => [
                //'WriteHTML' => $PrintTemplate,
                 
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


        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->WriteHtml($PrintTemplate);
        
 
        
        return $pdf->render();
    }

    public function actionPrintShip($id)
    {

        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        $searchModel = new SaleQuoteLineSearch();
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
        if (($model = SaleQuoteHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionImportFile(){

        //$file           = Yii::getAlias('@webroot').'/uploads/file.pdf';
        $source         = Yii::getAlias('@webroot').'/uploads/file/temp';


        $text           = '';
        $countPages     = 1;
 
        if (isset($_POST['submit'])) {
            if(isset($_FILES["file"])){
                
                if($_FILES["file"]['size'] > 0){


                    $pdf = new \TonchikTm\PdfToHtml\Pdf($_FILES["file"]["tmp_name"], [
                        // 'pdftohtml_path'    => '/usr/local/bin/pdftohtml',  // When Mac OS X
                        // 'pdfinfo_path'      => '/usr/local/bin/pdfinfo',    // When Mac OS X                    
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
        $models = SaleQuoteLine::find()->where(['sourcedoc' => $id])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();

        foreach ($models as $key => $model) {           
            $model->delete(); 
            $status = 200;
            $message = 'done';            
            
        }
        // เอาแค่นี้ก่อน (รีบ)
        return json_encode([
                    'status' => $status,
                    'message' => $message,
                    'value' => [
                        'source' => $id
                    ]
                ]);
        

 
        
    }


    public function actionPercentDiscount($id,$key=null)
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($_POST['id']);       
        
        $model->discount            = $_POST['discount'];        
        $model->percent_discount    = $_POST['percent'] ?: null;

        $model->include_vat         = $_POST['inc_vat'];

        $model->vat_percent         = $_POST['vat_percent'];
        $model->payment_term        = $_POST['credit'];
        $model->paymentdue          = $_POST['due'];

        if($_POST['discount']==0){             
            $model->discount = $_POST['percent'] * $model->sumLine /100;
        }
         
        if($key=='discount'){
            $division = ($model->sumLine > 0)? $model->sumLine : 1;
            $model->percent_discount    = ($model->discount / $division) *100; 
        }
 
        $model->balance             = FunctionSaleOrder::GrandTotalSaleOrder($model);
        
        if(!$model->save()){
            var_dump($model->getErrors(),JSON_UNESCAPED_UNICODE);            
        }


        $searchModel    = new SaleQuoteLineSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['sourcedoc' => $model->id]);
        $dataProvider->query->andwhere(['comp_id' => $company]);

        return $this->renderAjax('_sum_line',[
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);

    }


    public function actionConvert($id){

        $reqHead    = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $header     = new SaleHeader();
            $header->no                 = Generater::getRuning('sale_header','no','all');
            $header->customer_id        = $reqHead->customer_id;
            $header->sale_address       = $reqHead->sale_address;
            $header->bill_address       = $reqHead->bill_address;
            $header->ship_address       = $reqHead->ship_address;   
            $header->order_date         = $reqHead->order_date;
            
            $header->ship_date          = $reqHead->ship_date;
            $header->balance            = $reqHead->sumtotal->total;
            $header->balance_befor_vat = $reqHead->balance_befor_vat;
            $header->discount           = $reqHead->discount;
            $header->percent_discount   = $reqHead->percent_discount;
            $header->status             = 'Open';
            $header->create_date        = date('Y-m-d H:i:s');

            $header->paymentdue         = $reqHead->paymentdue;
            $header->sales_people       = $reqHead->sales_people;
            $header->sale_id            = $reqHead->sale_id;
            $header->vat_percent        = $reqHead->vat_percent;
            $header->ext_document       = $reqHead->ext_document;
            $header->payment_term       = $reqHead->payment_term;
            $header->vat_type           = $reqHead->vat_type;
            $header->remark             = $reqHead->remark;
            $header->transport          = $reqHead->transport;
            $header->update_by          = $reqHead->update_by;
            $header->update_date        = date('Y-m-d H:i:s');
            $header->include_vat        = $reqHead->include_vat;
            $header->sourcedoc          = $reqHead->sourcedoc;
            
            $header->user_id            = Yii::$app->user->identity->id;
            $header->comp_id            = Yii::$app->session->get('Rules')['comp_id'];

           
            if($header->save()){
                Generater::UpdateSeries('sale_header','no','all',$header->no);
            }else{
                Yii::$app->getSession()->addFlash('danger',json_encode($header->getErrors(),JSON_UNESCAPED_UNICODE)); 
                $transaction->rollBack();
                return $this->redirect(['view','id' => $id]);
            }

            

            $SourceLine    = SaleQuoteLine::find()->where(['sourcedoc' => $reqHead->id])->all();
            foreach ($SourceLine as $key => $line) {
                 
                $model = new SaleLine();
                $model->sourcedoc           = $header->id;
                $model->order_no            = $header->no;
                $model->type                = 'Item';
                $model->item                = $line->item;
                $model->item_no             = $line->item_no;
                $model->description         = $line->description;
                $model->quantity            = $line->quantity;
                $model->unit_measure        = $line->unit_measure;
                $model->unit_price          = $line->unit_price;
                $model->line_amount         = $line->line_amount;
                $model->line_discount       = $line->line_discount;
                $model->need_ship_date      = $line->need_ship_date;
                $model->quantity_to_ship    = $line->quantity_to_ship;
                $model->quantity_shipped    = $line->quantity_shipped;
                $model->quantity_to_invoice = $line->quantity_to_invoice;
                $model->quantity_invoiced   = $line->quantity_invoiced;
                $model->create_date         = $line->create_date;
                $model->api_key             = $line->api_key;
                $model->unit_price_exvat    = $line->unit_price_exvat;
 
                $model->user_id             = Yii::$app->user->identity->id;
                $model->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

                if(!$model->save()){
                    var_dump($model->getErrors());
                    $transaction->rollBack();
                    exit();
                }
            }
            
            $transaction->commit();
            return $this->redirect(['/SaleOrders/saleorder/update','id' => $header->id]);
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;

            return $this->redirect(['view','id' => $id]);
        }
        
    }


    public function actionGetSaleline($id){
        $model = SaleQuoteLine::findOne(base64_decode($id));
        if($model){
            $Item = Items::find()->where(['id' => $model->item])->one();
            return json_encode([
                'status'    => 200,
                'message'   => 'done',
                'value'     => [
                    'id'        => $model->id,
                    'so'        => $model->sourcedoc,
                    'name'      => ($model->description)? $model->description : $Item->description_th,
                    'detail'    => $Item->detail,
                    'price'     => number_format($model->unit_price, 2, '.', ''),
                    'qty'       => number_format($model->quantity, 2, '.', ''),
                    'sumline'   => $model->unit_price * ($model->quantity - $model->line_discount),
                    'discount'  => $model->line_discount
                ]
            ]);
        };
    }


}
