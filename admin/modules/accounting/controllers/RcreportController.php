<?php

namespace admin\modules\accounting\controllers;

use Yii;
use common\models\SaleLine;
use common\models\SaleHeader;
use common\models\Customer;
use admin\modules\SaleOrders\models\OrderSearch;
use admin\modules\SaleOrders\models\SalehearderSearch;
use admin\modules\accounting\models\SaleinvheaderSearch;


use common\models\SaleInvoiceHeader;
use admin\modules\accounting\models\SaleinvoiceSearch;
use admin\modules\accounting\models\SaleinvlineSearch;
use admin\modules\accounting\models\ViewRcInvoiceSearch;

use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use common\models\ViewRcInvoice;
use common\models\SaleInvoiceLine;
use common\models\VatType;

use admin\models\Generater;
use common\models\SalesPeople;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class RcreportController extends \yii\web\Controller
{

    public function actionIndex(){

        $searchModel    = new ViewRcInvoiceSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=100;
        
        $model          = new RcInvoiceHeader();
        

        if ($model->load(Yii::$app->request->post())) {
            $NoSeries       = Generater::NextRuning('vat_type','vat_value','7',false);
           //var_dump($_POST['RcInvoiceHeader']['sale_id']); exit;
            if($_POST['status'] == 'Posted'){
                $source = RcInvoiceHeader::findOne($_POST['id']);
            }else{
                $source = SaleInvoiceHeader::findOne($_POST['id']);
            }
            $transaction = Yii::$app->db->beginTransaction();

            try {

                $model->ship_date       = $source->ship_date;

                $model->sales_people    = $source->sales_people;
                $model->sale_id         = $source->sale_id;
                $SALES                  = SalesPeople::findOne($_POST['RcInvoiceHeader']['sale_id']);
                if($SALES!==null){
                    $model->sales_people= $SALES->code;
                    $model->sale_id     = $SALES->id;
                }
                
                 
                $model->document_no_    = $source->document_no_;
                $model->doc_type        = $source->doc_type;
                $model->district        = $source->district;
                $model->city            = $source->city;
                $model->province        = $source->province;
                $model->postcode        = $source->postcode;
                $model->user_id         = Yii::$app->user->identity->id;
                $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                $model->ref_inv_header  = $source->id;
                $model->vat_percent     = $_POST['vatPercent'];
                $model->ext_document    = $source->ext_document;
                $model->include_vat     = $source->include_vat;                
                $model->session_id      = Yii::$app->session->getId();
                $model->order_id        = $source->order_id;
                $model->status          = 'Posted';
                $model->revenue         = 1;

               
                // If using a new running number (Never change by yourself)
                // Need to update the last running number series
                if($NoSeries == $model->no_){
                    $UpdateSeries       = Generater::CreateNextNumber('vat_type','vat_value',$model->vat_percent,$model->no_);
                }

                if(!$model->save()){
                    Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
                    
                    $transaction->rollBack();

                    return $this->render('index',[
                        'model' => $model,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);

                }

                $source->revenue        = 0;
                $source->rf_revenue     = $model->id;
                if(!$source->save()){
                    Yii::$app->getSession()->addFlash('warning',json_encode($source->getErrors(),JSON_UNESCAPED_UNICODE));
                    $transaction->rollBack();
                    return $this->render('index',[
                        'model' => $model,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]); 
                }


                if(isset($_POST['line'])){                      
                   
                    foreach ($_POST['line'] as $line) {                                              
                        //echo $line.'<br>';
                        foreach ($_POST['qty'] as $key => $qty) {

                            if((int)$line == (int)$key){                                                       

                                $id     = $line;
                                $qty    = $_POST['qty'][(int)$line];
                                $price  = $_POST['price'][(int)$line];

                                //echo $line.' '.$qty.' '.$price.'<br>'; 

                                // Invoice Posted
                                if($_POST['status'] == 'Posted'){
                                    $getLine   = RcInvoiceLine::findOne(['id' => $line]);
                                }else {
                                    $getLine   = SaleInvoiceLine::findOne(['id' => $line]);
                                }

                                
                                $newLine = new RcInvoiceLine();
                                
                                $newLine->type          = 'Item';
                                $newLine->source_id     = $model->id;
                                $newLine->doc_no_       = $model->no_;

                                $newLine->item          = $getLine->item;
                                $newLine->line_no_      = $getLine->line_no_;
                                $newLine->customer_no_  = $getLine->customer_no_;
                                $newLine->code_no_      = $getLine->code_no_;
                                $newLine->code_desc_    = $getLine->code_desc_;
                                

                                $newLine->quantity      = $qty;
                                $newLine->unit_price    = $price;
                                $newLine->vat_percent   = $getLine->vat_percent;
                                $newLine->line_discount = $getLine->line_discount;
                                $newLine->source_line   = $line;

                                $newLine->order_id      = $source->order_id;
                                $newLine->source_doc    = $model->no_;
                                $newLine->status        = 'transfer';
                                $newLine->session_id    = Yii::$app->session->getId();     
                                $newLine->posting_date  = $model->posting_date;                         
                                $newLine->comp_id       = Yii::$app->session->get('Rules')['comp_id'];

                                if(!$newLine->save()){
                                    Yii::$app->getSession()->addFlash('warning',json_encode($newLine->getErrors(),JSON_UNESCAPED_UNICODE)); 
                                    
                                    $transaction->rollBack();

                                    return $this->render('index',[
                                        'model' => $model,
                                        'searchModel' => $searchModel,
                                        'dataProvider' => $dataProvider,
                                    ]);

                                }

                            }
                        
                        }
                         
                    }

                }else{
                    Yii::$app->getSession()->addFlash('warning',Yii::t('common','No line create')); 
                                    
                    $transaction->rollBack();
                }

                


                $transaction->commit();
                return $this->redirect(['index']);     
                //return $this->redirect(['/accounting/posted/posted-invoice', 'id' => base64_encode($model->id)]);     

            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
                

            


        }
        return $this->render('index',[
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

    }
    public function actionSaleReport()
    {
    	$model = SaleHeader::find()
        ->where(['status'=> ['Checking','Shiped','Invoiced']])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
    	->all();

    	$orderno = [];
    	foreach ($model as $value) {
    		$orderno[] = $value->no;
    	}

    	 

    	$searchModel = new OrderSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	$dataProvider->query->andWHere(['order_no' => $orderno]); 


        return $this->render('sale-report',[
        	'searchModel' => $searchModel,
        	'dataProvider' => $dataProvider,
        	]);
         
    }

    public function actionPostedInvoice($id)
    {

        $model = $this->findModel($id);

        $searchModel = new SaleinvlineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(["comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->where(['source_id' => $id]);

        

        
        return $this->render('postedinvoice',[
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
    }

    public function actionSaleTax()
    {

    	$searchModel = new SalehearderSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWHere(['sale_header.status' => ['Checking','Shiped','Invoiced']]);
        $dataProvider->query->andWhere(["sale_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
    	$dataProvider->query->andWhere(['<>','sale_header.vat_percent',0]);


        return $this->render('sale-tax',[
        	'searchModel' => $searchModel,
        	'dataProvider' => $dataProvider,
        	]);
    }

     protected function findModel($id)
    {
        if (($model = SaleInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetInvoiceLine($id){
        $model = ViewRcInvoice::findOne($id);
        $data = [];

        
        // Invoice Posted
        if($model->status == 'Posted'){
            $lines   = RcInvoiceLine::find()
            ->where(['source_id' => $model->id]);    
        }else {
            $lines   = SaleInvoiceLine::find()
            ->where(['source_id' => $model->id]);    
        }
         
        foreach ($lines->all() as $key => $line) {

            $data[] = (Object)[
                'id' => $line->id,
                'item' => $line->item,
                'barcode' => $line->items->barcode,
                'code' => $line->items->master_code,
                'desc' => $line->code_desc_,
                'qty' => $line->quantity,
                'price' => $line->unit_price,

            ];
        }

        $NoSeries           = Generater::NextRuning('vat_type','vat_value','7',false);

        return json_encode([
            'status' => 200,
            'data' => $data,
            'id' => $model->id,
            'no' => $model->no_,
            'new_doc' => $NoSeries,
            'cust' => $model->cust_no_,
            'cust_code' => $model->cust_code,
            'cust_name' => $model->cust_name_,
            'cust_addr' => $model->cust_address,
            'posting_date' => $model->posting_date,
            'sales'     => $model->sales_people,
            'sale_id'   => $model->field->sale_id,
            'discount' => $model->discount,
            'vat' => $model->vat_percent,
            'inc_vat' => $model->include_vat,
            'remark' => $model->remark,
            'status' => $model->status
        ]);

    }


    public function actionGetVatList(){
        $models = VatType::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
        $data = [];
        foreach ($models as $key => $model) {
            $data[] = (Object)[
                'value' => $model->vat_value,
                'label'  => $model->description,
                'id'    => $model->id
            ];
        }
        return json_encode([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function actionInvoice()
    {
        return $this->render('invoice');
    }

    public function actionTaxInvoice()
    {
        return $this->render('tax-invoice');
    }

    public function actionTaxInvoiceAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $comp   = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $cust   = \common\models\Customer::findOne($data->cust);
        $sale   = \common\models\SalesPeople::findOne($data->sale);

        // Radis 
        // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
        $cache      = Yii::$app->cache;
        $keys       = 'invoice&comp:'.$comp->id.'&fdate:'.$data->fdate.'&tdate:'.$data->tdate.'&cust:'.$data->cust.'&sale:'.$data->sale.'&vatpercent:'.$data->vat;
        if($cache->get($keys)){
            return json_encode([
                'status'    => 200,
                "source"    => 'cache',
                "data"      => $cache->get($keys),
            ]);
        }else {
    
            $query = RcInvoiceHeader::find()        
            ->where(['between','DATE(posting_date)',date('Y-m-d 00:00:0000',strtotime($data->fdate)), date('Y-m-d 23:59:59.9999',strtotime($data->tdate))])
            ->andWhere(['comp_id' => $comp->id]);


            $data->sale != '' ? $query->andWhere(['sale_id' => $data->sale]) : null;
            $data->cust != '' ? $query->andWhere(['cust_no_' => $data->cust]) : null;
            if($data->vat == 'Vat' ){
                $query->andWhere(['>','vat_percent',0]);
            }else if($data->vat == 'No' ){
                $query->andWhere(['<=','vat_percent',0]);
            }


            $count = $query->count();
            if($count > 50000){
                return json_encode([
                    'status'    => 403,
                    'message'   => 'ข้อมูลมากเกินไป',
                    'count'     => $count
                ]);
            }else{
                $rawData = [];
                foreach ($query->all() as $key => $model) {                       
                    $payment = [];
                    if($model->status=='Posted'){                    
                        if($model->allPayment != null){
                            foreach($model->allPayment as $k => $el){
                            
                                $payment[]= (Object)[
                                    'id'        => $el->id,
                                    'type'      => $el->type,
                                    'from'      => $el->banklist->name,
                                    'bank'      => $el->bank_id,
                                    'to'        => $el->bankaccount->banklist->name,
                                    'toNo'      => $el->bankaccount->bank_no,
                                    'status'    => $el->apply_to_status,
                                    'balance'   => $el->balance,
                                    'remark'    => $el->remark,
                                    'datetime'  => $el->create_date
                                ];
                            } 
                        }
                    }
                    $rawData[] = (Object)[
                        'id'        => $model->id,
                        'no'        => $model->no_,
                        'date'      => date('Y-m-d', strtotime($model->posting_date)),
                        'saleId'    => $model->salesPeople ? $model->salesPeople->id : '',
                        'saleCode'  => $model->salesPeople ? $model->salesPeople->code : '',
                        'vat'       => $model->vat_percent,
                        'discount'  => $model->discount * 1,
                        'custId'    => $model->cust_no_,
                        'custName'  => $model->customer->name,
                        'custCode'  => $model->customer->code,
                        'tax'       => $model->customer->vat_regis,
                        'headoffice'=> (int)$model->customer->headoffice,
                        'branch'    => $model->customer->branch,
                        'balance'   => $model->sumtotals->total,
                        'subTotal'  => $model->sumtotals->subtotal,
                        'sumline'   => $model->sumtotals->sumline * 1,
                        'before'    => $model->sumtotals->before,
                        //'sumline'   => ($model->include_vat===1 ? $model->sumtotals->sumline : $model->sumtotals->before) * 1 ,
                        'incvat'    => $model->sumtotals->incvat * 1,
                        'due'       => $model->paymentdue,
                        'orderId'   => $model->saleOrder ? $model->saleOrder->id : '',
                        'orderNo'   => $model->saleOrder ? $model->saleOrder->no : '',
                        'invat'     => $model->include_vat,
                        'pay'       => $payment,
                        'type'      => $model->doc_type,
                        'revenue'   => $model->revenue

                    ];
                    
                }

                $data = [
                    'raw'       => $rawData,
                    'compName'  => $comp->name,
                    'compAddr'  => $comp->address,
                    'compVatAddr' => $comp->vat_address,
                    'compTax'   => $comp->vat_register,
                    'headOffice'=> (int)$comp->headoffice,
                    'fdate'     => $data->fdate,
                    'tdate'     => $data->tdate,
                    'custId'    => $cust != null ? $cust->id : '',
                    'custName'  => $cust != null ? $cust->name : '',
                    'custCode'  => $cust != null ? $cust->code : '',
                    'saleId'    => $sale != null ? $sale->id : '',
                    'saleName'  => $sale != null ? $sale->name : '',
                    'saleCode'  => $sale != null ? $sale->code : ''
                ];

                $cache->set($keys, $data, 30);

                return json_encode([
                    'status'    => 200,
                    "source"    => 'api',
                    'timestamp' => date('Y-m-d H:i:s'),
                    "data"      => $cache->get($keys),
                ]);
            }
        }
    }



    public function actionAllInvoice()
    {
        return $this->render('all-invoice');
    }

    public function actionAllInvoiceAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $comp       = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $cust       = \common\models\Customer::findOne($data->cust);
        $sale       = \common\models\SalesPeople::findOne($data->sale);
        
        $rawData    = [];
        // Radis 
        // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
        $cache      = Yii::$app->cache;
        $keys       = 'accounting-rcreport-all-invoices&comp:'.$comp->id.'&fdate:'.$data->fdate.'&tdate:'.$data->tdate.'&cust:'.$data->cust.'&sale:'.$data->sale.'&vatpercent:'.$data->vat;
        if($cache->get($keys)){
            return json_encode([
                'status'    => 200,
                "source"    => 'cache',
                "data"      => $cache->get($keys),
            ]);
        }else {
    
            $query = ViewRcInvoice::find()        
            ->where(['between','DATE(posting_date)',date('Y-m-d 00:00:0000',strtotime($data->fdate)), date('Y-m-d 23:59:59.9999',strtotime($data->tdate))])
            ->andWhere(['comp_id' => $comp->id]);


            $data->sale != '' ? $query->andWhere(['sale_id' => $sale != null ? $sale->id : '']) : null;
            $data->cust != '' ? $query->andWhere(['cust_no_' => $cust != null ? $cust->id : '']) : null;
            
            if($data->vat == 'Vat' ){
                $query->andWhere(['>','vat_percent',0]);
            }else if($data->vat == 'No' ){
                $query->andWhere(['<=','vat_percent',0]);
            }


            $count = $query->count();
            if($count > 50000){
                return json_encode([
                    'status'    => 403,
                    'message'   => 'ข้อมูลมากเกินไป',
                    'count'     => $count
                ]);
            }else{
                
                foreach ($query->all() as $key => $model) {   

                    $payment    = [];
                    if($model->status=='Posted'){
                        if($model->allPayment != null){
                            foreach($model->allPayment as $k => $el){                            
                                $payment[]= (Object)[
                                    'id'        => $el->id,
                                    'type'      => $el->type,
                                    'from'      => $el->banklist->name,
                                    'bank'      => $el->bank_id,
                                    'to'        => $el->bankaccount->banklist->name,
                                    'toNo'      => $el->bankaccount->bank_no,
                                    'status'    => $el->apply_to_status,
                                    'balance'   => $el->balance,
                                    'remark'    => $el->remark,
                                    'datetime'  => $el->create_date
                                ];
                            } 
                        }
                    }

                    $rawData[] = (Object)[
                        'id'        => $model->id,
                        'no'        => $model->no_,
                        'date'      => date('Y-m-d', strtotime($model->posting_date)),
                        'saleId'    => $model->salesPeople ? $model->salesPeople->id : '',
                        'saleCode'  => $model->salesPeople ? $model->salesPeople->code : '',
                        'vat'       => $model->vat_percent,
                        'discount'  => $model->discount * 1,
                        'custName'  => $model->customer->name,
                        'custCode'  => $model->customer->code,
                        'tax'       => $model->customer->vat_regis,
                        'headoffice'=> $model->customer->headoffice,
                        'branch'    => $model->customer->branch,
                        'balance'   => $model->sumtotals->total * 1,
                        // 'sumline'   => ($model->include_vat===1 
                        //                         ? $model->sumtotals->sumline 
                        //                         : $model->sumtotals->before) * 1 ,
                        'sumline'   => $model->sumtotals->sumline,
                        'incvat'    => $model->sumtotals->incvat * 1,
                        'due'       => $model->paymentdue,
                        'orderId'   => $model->saleOrder ? $model->saleOrder->id : '',
                        'orderNo'   => $model->saleOrder ? $model->saleOrder->no : '',
                        'orderTotal'=> $model->saleOrder ? $model->saleOrder->total : 0,
                        'pay'       => $payment,
                        'posted'    => $model->status,
                        'revenue'   => $model->revenue

                    ];
                    
                }

                $data = [
                    'raw'       => $rawData,
                    'compName'  => $comp->name,
                    'compAddr'  => $comp->address,
                    'compVatAddr' => $comp->vat_address,
                    'compTax'   => $comp->vat_register,
                    'headOffice'=> $comp->headoffice,
                    'fdate'     => $data->fdate,
                    'tdate'     => $data->tdate,
                    'custId'    => $cust != null ? $cust->id : '',
                    'custName'  => $cust != null ? $cust->name : '',
                    'custCode'  => $cust != null ? $cust->code : '',
                    'saleId'    => $sale != null ? $sale->id : '',
                    'saleName'  => $sale != null ? $sale->name : '',
                    'saleCode'  => $sale != null ? $sale->code : '',
                    'posted'    => false
                ];

                $cache->set($keys, $data, 1);

                return json_encode([
                    'status'    => 200,
                    "source"    => 'api',
                    'timestamp' => date('Y-m-d H:i:s'),
                    "data"      => $cache->get($keys),
                ]);
            }
        }
    }



    public function actionAllInvoiceMobile()
    {
        return $this->render('all-invoice-mobile');
    }

    public function actionAllInvoiceMobileAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $comp   = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $cust   = \common\models\Customer::findOne($data->cust);
        $sale   = \common\models\SalesPeople::findOne($data->sale);

        // Radis 
        // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
        $cache      = Yii::$app->cache;
        $keys       = 'accounting-rcreport-all-invoice-mobile&comp:'.$comp->id.'&fdate:'.$data->fdate.'&tdate:'.$data->tdate.'&cust:'.$data->cust.'&sale:'.$data->sale.'&vatpercent:'.$data->vat;
        if($cache->get($keys)){
            return json_encode([
                'status'    => 200,
                "source"    => 'cache',
                "data"      => $cache->get($keys),
            ]);
        }else {
    
            $query = ViewRcInvoice::find()        
            ->where(['between','DATE(posting_date)',date('Y-m-d 00:00:0000',strtotime($data->fdate)), date('Y-m-d 23:59:59.9999',strtotime($data->tdate))])
            ->andWhere(['comp_id' => $comp->id]);


            $data->sale != '' ? $query->andWhere(['sale_id' => $data->sale]) : null;
            $data->cust != '' ? $query->andWhere(['cust_no_' => $data->cust]) : null;
            
            if($data->vat == 'Vat' ){
                $query->andWhere(['>','vat_percent',0]);
            }else if($data->vat == 'No' ){
                $query->andWhere(['<=','vat_percent',0]);
            }


            $count = $query->count();
            if($count > 50000){
                return json_encode([
                    'status'    => 403,
                    'message'   => 'ข้อมูลมากเกินไป',
                    'count'     => $count
                ]);
            }else{
                $rawData = [];
                foreach ($query->all() as $key => $model) {                       
                    $payment = [];
                    if($model->allPayment != null){
                        foreach($model->allPayment as $k => $el){
                        
                             $payment[]= (Object)[
                                'id'        => $el->id,
                                'type'      => $el->type,
                                'from'      => $el->banklist->name,
                                'bank'      => $el->bank_id,
                                'to'        => $el->bankaccount->banklist->name,
                                'toNo'      => $el->bankaccount->bank_no,
                                'status'    => $el->apply_to_status,
                                'balance'   => $el->balance,
                                'remark'    => $el->remark,
                                'datetime'  => $el->create_date
                             ];
                        } 
                    }
                    $rawData[] = (Object)[
                        'id'        => $model->id,
                        'no'        => $model->no_,
                        'date'      => date('Y-m-d', strtotime($model->posting_date)),
                        'saleId'    => $model->sales->id,
                        'saleCode'  => $model->sales->code,
                        'vat'       => $model->vat_percent,
                        'discount'  => $model->discount * 1,
                        'custName'  => $model->customer->name,
                        'custCode'  => $model->customer->code,
                        'tax'       => $model->customer->vat_regis,
                        'headoffice'=> $model->customer->headoffice,
                        'branch'    => $model->customer->branch,
                        'balance'   => $model->sumtotals->total * 1,
                        'sumline'   => $model->sumtotals->sumline * 1,
                        'incvat'    => $model->sumtotals->incvat * 1,
                        'due'       => $model->paymentdue,
                        'orderId'   => $model->saleOrder ? $model->saleOrder->id : '',
                        'orderNo'   => $model->saleOrder ? $model->saleOrder->no : '',
                        'pay'       => $payment,
                        'posted'    => $model->status,
                        'modern'    => $model->customer->genbus_postinggroup

                    ];
                    
                }

                $data = [
                    'raw'       => $rawData,
                    'compName'  => $comp->name,
                    'compAddr'  => $comp->address,
                    'compVatAddr' => $comp->vat_address,
                    'compTax'   => $comp->vat_register,
                    'headOffice'=> $comp->headoffice,
                    'fdate'     => $data->fdate,
                    'tdate'     => $data->tdate,
                    'custId'    => $cust != null ? $cust->id : '',
                    'custName'  => $cust != null ? $cust->name : '',
                    'custCode'  => $cust != null ? $cust->code : '',
                    'saleId'    => $sale != null ? $sale->id : '',
                    'saleName'  => $sale != null ? $sale->name : '',
                    'saleCode'  => $sale != null ? $sale->code : '',
                    'posted'    => false,
                    'modern'    => 0
                ];

                $cache->set($keys, $data, 10);

                return json_encode([
                    'status'    => 200,
                    "source"    => 'api',
                    'timestamp' => date('Y-m-d H:i:s'),
                    "data"      => $cache->get($keys),
                ]);
            }
        }
    }
}
