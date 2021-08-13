<?php

namespace admin\modules\accounting\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

use common\models\BillingNote;
use admin\modules\accounting\models\BillingNoteSearch;
use admin\modules\accounting\models\RcinvBillSearch;
use common\models\RcInvoiceHeader;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BillingController implements the CRUD actions for BillingNote model.
 */
class BillingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'     => VerbFilter::className(),
                'actions'   => [
                    'delete'        => ['POST'],
                    'ajax-update'   => ['POST'],
                    'delete-line'   => ['POST'],
                    'create-line'   => ['POST'],
                    'update-line'   => ['POST'],
                    'confirm-bill'  => ['POST'],
                    'change-series' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all BillingNote models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new BillingNoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(["billing_note.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
        //$dataProvider->pagination->pageSize=20;
        $dataProvider->query->orderBy(['no_' => SORT_DESC, 'create_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionNote()
    {
        $searchModel = new BillingNoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('note', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BillingNote model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        // return $this->render('view', [
        //     'model' => $this->findModel($id),
        // ]);

        return $this->redirect(['update', 'id' => base64_encode($id)]);
    }

    /**
     * Creates a new BillingNote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $searchModel = new RcinvBillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // $query = RcInvoiceHeader::find()
        // ->select('cust_no_, paymentdue')
        // ->where(['<>','cust_no_','909'])
        // ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        // ->groupBy('cust_no_, paymentdue');

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => false
        // ]);   


        //---  Vat Filter ---
        if(isset($_GET['searchVat']))
        {
            if($_GET['searchVat']!='') $dataProvider->query->andWhere(['vat_percent' => $_GET['searchVat']]);
        }


        //--- Customer Filter ---
        if(Yii::$app->request->get('customer')){
            //if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);

            $customer = \common\models\Customer::findOne(Yii::$app->request->get('customer'));
            $list = [];
            if($customer != null){
                foreach ($customer->branchList as $key => $cust) {
                    $list[] = $cust->id;
                }
            }

            $dataProvider->query->andWhere(['IN', 'cust_no_', $list]);
           //var_dump($list);
        }
 

        //--- Date Filter ---
        if(Yii::$app->request->get('fdate')){

        
            $formdate   = Yii::$app->request->get('fdate')?: date('Y-m-d');
        
            $todate     = Yii::$app->request->get('tdate')?: date('Y-m-t').' 23:59:59.9999';

            // if(@$_GET['fdate']!='') $formdate     = date('Y-m-d 00:00:0000',strtotime(Yii::$app->request->get('fdate')));

            // if(@$_GET['tdate']!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime(Yii::$app->request->get('tdate')));

            //$dataProvider->query->andWhere(['between', 'paymentdue', $formdate,$todate]);
            $dataProvider->query->andWhere(['between', 'DATE(posting_date)', $formdate,$todate]);
           
            
        }else{
            $dataProvider->query->andWhere(['between', 'DATE(posting_date)', date('Y-m-01'),date('Y-m-01')]);
        }
        //--- /. Date Filter ---
 
        //$dataProvider->pagination->pageSize=10;
        $dataProvider->query->orderBy(['paymentdue' => SORT_DESC]);


       return $this->render('__create_filter',[
                        //'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]); 
         
    }

    /**
     * Updates an existing BillingNote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {


        if(Yii::$app->request->get('searchVat')){

            $company = Yii::$app->session->get('Rules')['comp_id'];

            $query   = RcInvoiceHeader::find(); 
            $query->joinWith('customer')
            ->where(['rc_invoice_header.comp_id' => $company]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false
            ]);     

            if(Yii::$app->request->get('searchVat') != null){
                $dataProvider->query->andWhere(['vat_percent' => Yii::$app->request->get('searchVat')]);
            }

            if(Yii::$app->request->get('customer') != null){
                $dataProvider->query->andWhere(['cust_no_' => Yii::$app->request->get('customer')]);
            }

            //--- Date Filter ---
            $LastDay    = date('t',strtotime(date('Y-m-d'))); 
     
            $formdate   = Yii::$app->request->get('fdate') 
                            ? date('Y-m-d H:i:s',strtotime(Yii::$app->request->get('fdate'))) 
                            : date('Y-').date('m-').'01';
            $todate     = Yii::$app->request->get('tdate')
                            ? date('Y-m-d H:i:s',strtotime(Yii::$app->request->get('tdate'))) 
                            : date('Y-').date('m-').$LastDay;

            $dataProvider->query->andWhere(['between', 'posting_date', $formdate,$todate]);
            //--- /. Date Filter ---

            return $this->render('update',[
                        'dataProvider' => $dataProvider,
                        'customer'  => Yii::$app->request->post('customer'),
                        'id'        => base64_decode($id)
                    ]);
        

        }



        $query  = $this->findModelFromNo($id);
       
        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => false
            
        ]);
     
        
        return $this->render('update', [
            'dataProvider' => $dataProvider,
            'customer' => $query->one()->cust_no_,
            'id'        => base64_decode($id)
        ]);
    }

    /**
     * Deletes an existing BillingNote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($no)
    {
        // $this->findModel($id)->delete();

        $clear = \common\models\BillingNote::find()
        ->where(['no_' => base64_decode($no)])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all();

        $inv    = '';
        $total  = 0;
        $cust   = '';
        foreach ($clear as $model) {
            $cust = $model->customer->name."\r\n";
            $inv.= $model->no_ .' : '.$model->description.' ['.$model->balance."]\r\n";
            $total += $model->balance;
            $model->delete();
        }

        try{                     
			// Line Notify
			$bot =  \common\models\LineBot::findOne(1);
			$msg = date('Y-m-d H:i:s')."\r\nลบ ใบวางบิล \r\n";
            $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
            $msg.= $cust."\r\n";	
            $msg.= $inv."\r\n";			 
			$msg.= number_format($total,2)." บาท\r\n";
 
			$msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

			$bot->notify_message($msg);					

		} catch (\Exception $e) {					 
			Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
        }


        return $this->redirect(['index']);
    }

    public function actionDeleteLine($id)
    {
        $Bahttext 	= new \admin\models\FunctionBahttext;  
        $model      = $this->findModel($id);
        $no         = $model->no_;

        if($model->delete()){
            $bill   = new BillingNote();
            $total  = $bill->getTotalBalanceByNo($no)->amount;
            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => [
                    'id' => $id,
                    'no' => $no
                ],
                'total' => $total,
                'textbaht' => $Bahttext->ThaiBaht($total)
            ]);
        }else{

            $bill   = new BillingNote();
            $total  = $bill->getTotalBalanceByNo($no)->amount;
            return json_encode([
                'status' => 500,
                'message' => 'error',
                'value' => [
                    'id' => $id,
                    'no' => $no
                ],
                'total' => $total,
                'textbaht' => $Bahttext->ThaiBaht($total)
            ]);
        }
    }

    /**
     * Finds the BillingNote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BillingNote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BillingNote::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    protected function findModelFromNo($id)
    {
        $model = BillingNote::find()
                ->where(['no_' => base64_decode($id)])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if ($model->count() > 0) {             
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');

        }
    }





    public function actionRenderTable()
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $query   = RcInvoiceHeader::find(); 
        $query->joinWith('customer')
        ->where(['rc_invoice_header.comp_id' => $company]);


        // <!-- Not In BIllingNote -->
        $BillingNote    = BillingNote::find()->all();
        $Billing        = array();
        foreach ($BillingNote as $value) {
            $Billing[] = $value->inv_no;
        }        
         
        $query->where(['NOT IN','rc_invoice_header.id',$Billing]);
        // <!-- /.Not In BIllingNote -->



        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => false
            
        ]);


 

        // if(isset($_GET['search-from-sale']))
        // {
        //     if($_GET['search-from-sale']!='')  $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        // }

        if(isset($_GET['searchVat']))
        {
            if($_GET['searchVat']!='') $dataProvider->query->andWhere(['vat_percent' => $_GET['searchVat']]);
        }

        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }


        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-').date('m-').'01 00:00:0000';

        $todate     = date('Y-').date('m-').$LastDay.' 23:59:59.9999';

        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d 00:00:0000',strtotime($_GET['fdate']));

        if(@$_GET['tdate']!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime($_GET['tdate']));


        if((@$_GET['tdate']=='')){
             
        }else {
           $dataProvider->query->andWhere(['between', 'posting_date', $formdate,$todate]); 
        }
        


        //--- /. Date Filter ---

 
         
        $GenSeries        = new \admin\models\Generater();
        $NoSeries         = $GenSeries->NextRuning('billing_note','vat_type','0',false);

        Yii::$app->session->set('billingNo',$NoSeries);


        
        if(isset($_GET['action'])){// <-- Update Billing Note -->

            if($_GET['action'] == 'update'){

                $GenSeries        = new \admin\models\Generater();
                $NoSeries         = $GenSeries->NextRuning('billing_note','vat_type','0',false);

                Yii::$app->session->set('billingNo',$NoSeries);



                $dataProvider->query->orderBy(['no_' => SORT_ASC]);
            
                $clear = \common\models\BillingNote::deleteAll('no_ = "'.Yii::$app->session->get('billingNo').'"');
                
                $allData = $dataProvider->getTotalCount();
                $i = 0;

                foreach ($dataProvider->models as $key => $value) {
                    $comp       = \common\models\Company::find()
                                ->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
                                ->one();
                
                    $model = new \common\models\BillingNote;

                    $model->inv_no          = $value->id;
                    $model->cust_no_        = $value->cust_no_;
                    $model->no_             = Yii::$app->session->get('billingNo');
                    $model->description     = $value->no_;
                    $model->vat_type        = $value->vat_percent;
                    $model->inv_date        = $value->order_date;
                    $model->paymentdue      = $value->paymentdue;
                    $model->amount          = $value->getSumTotal();
                    $model->paid            = $value->getPayment();
                    $model->balance         = $value->getSumTotal() - $value->getPayment();
                    $model->create_date     = date('Y-m-d H:i:s');
                    $model->posting_date    = $value->posting_date;
                    $model->text_comment    = 'ได้รับบิลเงินเชื่อหรือเงินสดไว้ เพื่อตรวจสอบและพร้อมจะชำระเงินให้ตามบิลดังต่อไปนี';
                    $model->text_lecture    = 'ในนาม '.$comp->name;
                    $model->user_id         = Yii::$app->user->identity->id;
                    $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

                    if(!$model->save()){
                        print_r($model->getErrors());
                    }

                    if(++$i === $allData) {

                        $UpdateSeries  = $GenSeries->CreateNextNumber('billing_note','vat_type','0',$NoSeries);

                        return $this->redirect(['update', 'id' => base64_encode($model->no_)]);
                    }

                    
                
                }

                
            }
        }// <-- /.Update Billing Note -->

        

        if(Yii::$app->request->isAjax){
    
            return $this->renderAjax('__billing_table',[
                        
                        'dataProvider' => $dataProvider,
                         
                    ]);
        }



       
        
    

        // return $this->render('note',[
                    
        //             'dataProvider' => $dataProvider,
                     
        //         ]);
        
    }


    public function actionCustomerStyle(){

        $searchModel = new RcinvBillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        


        if(Yii::$app->request->isAjax){
    
           return $this->renderAjax('__customer_style',[
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);
        }else {
           return $this->render('__customer_style',[
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]); 
        }

        
    }

    public function actionAjaxUpdate($id,$field,$data){
        $model = BillingNote::find()->where(['no_' => base64_decode($id)])->orderBy(['id' => SORT_DESC])->one();

        //ALTER TABLE `billing_note` ADD `text_remark` TEXT NULL AFTER `posting_date`, ADD `text_comment` TEXT NULL AFTER `text_remark`, ADD `text_lecture` TEXT NULL AFTER `text_comment`;

        $model->$field = $data;
        $model->save();

        return $model->$field;


    }   



    public function actionConfirmBill()
    {
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $comp       = \common\models\Company::findOne($company);
        $invList    = Yii::$app->request->post('inv');
        $GenSeries  = new \admin\models\Generater();        

        $query      = RcInvoiceHeader::find()
                    ->joinWith('customer')
                    ->where(['rc_invoice_header.comp_id' => $company])
                    ->andWhere(['IN', 'rc_invoice_header.id', $invList])
                    ->orderBy(['rc_invoice_header.posting_date' => SORT_ASC]);   

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
         
        
        
        if(isset($_POST['action'])){ // <-- Update Billing Note -->

            if($_POST['action'] == 'update'){

                $no         = Yii::$app->request->post('no');
                Yii::$app->session->set('billingNo',$no);
                
                //$dataProvider->query->orderBy(['posting_date' => SORT_ASC]);
            
                //$clear = \common\models\BillingNote::deleteAll('no_ = "'.Yii::$app->session->get('billingNo').'"');
                
                $allData = (int)$query->count();
                $i = 0;
                $raws = [];
                 
                foreach ($query->all() as $key => $iv) {
                    
                     
                    $model                  = new \common\models\BillingNote;

                    $model->inv_no          = $iv->id;
                    $model->cust_no_        = $iv->cust_no_;
                    $model->no_             = $no;
                    $model->description     = $iv->no_;
                    $model->vat_type        = $iv->vat_percent;
                    $model->inv_date        = $iv->order_date;
                    $model->paymentdue      = $iv->paymentdue;
                    $model->amount          = $iv->getSumTotal() * 1;
                    $model->paid            = $iv->getPayment() * 1;
                    $model->balance         = $iv->getSumTotal() - $iv->getPayment() * 1;
                    $model->create_date     = date('Y-m-d H:i:s');
                    $model->posting_date    = $iv->posting_date;
                    $model->text_comment    = 'ได้รับบิลเงินเชื่อหรือเงินสดไว้ เพื่อตรวจสอบและพร้อมจะชำระเงินให้ตามบิลดังต่อไปนี';
                    $model->text_lecture    = 'ในนาม '.$comp->name;
                    $model->user_id         = Yii::$app->user->identity->id;
                    $model->comp_id         = $company;

                    if($model->save()){

                        $raws[] = (Object)[
                            'status' => 200,
                            'id'    => $model->id,
                            'iv'    => $model->inv_no,
                            'no_'   => $model->description                
                        ];

                    }else{
                        $raws[] = (Object)[
                            'status' => 500,
                            'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                        ];
                       
                    } 


                    // ถ้าครบแล้วให้ไปหน้า Update
                    if(++$i === $allData) {

                        
                        //return $this->redirect(['update', 'id' => base64_encode($no)]);
                    }
                
                }

                $UpdateSeries  = $GenSeries->CreateNextNumber('billing_note','vat_type','0',$no);
                         
                // return json_encode([
                //     'no' => $no,
                //     'count' => $allData,
                //     //'series' => $UpdateSeries,
                //     'progress' => $raws,
                //     'list'  => $invList                   
                // ]);
                
                return $this->redirect(['update', 'id' => base64_encode($no)]);
                exit;
                
            }
        }else { // <-- /.Update Billing Note -->

            $no         = $GenSeries->NextRuning('billing_note','vat_type','0',false);
                        Yii::$app->session->set('billingNo',$no);

    
        }

        if(Yii::$app->request->isAjax){
    
            // return $this->renderAjax('__billing_table',[
                        
            //             'dataProvider'  => $dataProvider,
            //             'customer'      => $_POST['customer']
                         
            //         ]);

            // Change to JSON 21/05/2020
            return json_encode([
                'status'    => 200,
                'message'   => 'done',
                'no'        => $no,
                'html'      => $this->renderAjax('__billing_table',[                        
                    'dataProvider'  => $dataProvider,
                    'customer'      => $_POST['customer']                     
                ])
            ]);
        }


    }

    public function actionCreateLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $total          = 0;

        $status = 200;
        $message = Yii::t('common','Success');
        
        $source = BillingNote::findOne(['no_' => $data->no]);

        $model = new BillingNote();
        
        $model->no_         = $source->no_;
        $model->description = $data->text;
        $model->cust_no_    = $source->cust_no_;
        $model->vat_type    = $source->vat_type;
        $model->inv_no      = 0;
        $model->inv_date    = date('Y-m-d');
        $model->paymentdue  = date('Y-m-d');
        $model->amount      = $source->amount * -1;
        $model->paid        = null;
        $model->balance     = $source->balance * -1;
        $model->create_date = date('Y-m-d');
        $model->posting_date= date('Y-m-d');
        $model->text_remark = $source->text_remark;
        $model->text_comment= $source->text_comment;
        $model->text_lecture= $source->text_lecture;
        $model->user_id     = Yii::$app->user->identity->id;
        $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];      
        $model->type_of_document    = 2;
           
        $raw = [];

        $Bahttext 	= new \admin\models\FunctionBahttext;  
        if($model->save()){

            $total  = $model->totalBalance->amount;

            $status = 200;
            $raw   = (Object)[
                'id' => $model->id,
                'text' => $model->description,
                'val' => $model->balance
            ];
        }else{
            $status = 500;
            $message = $model->getErrors();
        }
        

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raw'       => $raw,            
            'total'     => $total,
            'textbaht'  => $Bahttext->ThaiBaht($total)
        ]);
    }

    public function actionUpdateLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status = 200;
        $message = Yii::t('common','Success');
        
        $model = BillingNote::findOne($data->id);
        $field = $data->field;

        $model->$field = $data->val;
        
        if($field=='balance'){
            $model->amount = $data->val;
        }
           
        $raw = [];
        if($model->save()){
            $status = 200;
            $raw   = (Object)[
                'id' => $model->id,
                'text' => $model->description,
                'val' => $model->balance
            ];
        }else{
            $status = 500;
            $message = $model->getErrors();
        }
        

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raw'      => $raw
        ]);
    }

    public function actionChangeSeries(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status = 200;
        $message= Yii::t('common','Success');
        
        $query  = BillingNote::find()->where(['no_' => $data->no])->all();

           
        $raw = [];
        foreach ($query as $key => $model) {
            $model->no_ = $data->val;        
            if($model->update()){
                
                $raw[]   = (Object)[
                    'id' => $model->id,
                    'text' => $model->description,
                    'val' => $model->balance,
                    'status' => 200
                ];
            }else{
                
                $raw[]   = (Object)[
                    'id' => $model->id,
                    'text' => $model->description,
                    'val' => $model->balance,
                    'status' => 500,
                    'message' => $model->getErrors()
                ];
            
            }
        }
        
        

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'no'        => $data->val,
            'raw'      => $raw
        ]);
    }
}
