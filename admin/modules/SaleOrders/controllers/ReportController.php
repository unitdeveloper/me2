<?php

namespace admin\modules\SaleOrders\controllers;
use Yii;
use admin\models\FunctionCenter;
use yii\db\Expression;

use common\models\Customer;
use common\models\SalesPeople;
use common\models\SaleHeader;
use common\models\SaleLine;
use common\models\TransportOrder;
use common\models\RcInvoiceHeader;
use common\models\ViewSaleSumTotal;
use common\models\ViewRcInvoice;
use common\models\ViewRcInvoiceTotal;
use common\models\RcInvoiceLine;

use admin\modules\SaleOrders\models\SalehearderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use admin\modules\SaleOrders\models\OrderSearch;
use admin\modules\SaleOrders\models\ReportSearch;
use admin\modules\SaleOrders\models\RcorderSearch;

use admin\modules\items\models\SearchPicItems;
use common\models\Items;


use common\models\TmpMenuGroup;
use common\models\VatType;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use admin\modules\SaleOrders\models\BestsaleSearch;


class ReportController extends \yii\web\Controller
{

    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class'     => VerbFilter::className(),
                'actions'   => [
                    'delete'                => ['POST'],                     
                    'update-line-box'       => ['POST'],
                    'sale-cash-no-detail'   => ['GET'],
                    'report-daily'          => ['GET'],
                    'sale-balance'          => ['GET'],
                    'json-this-years-line-chart' => ['GET'],
                    'load-data'             => ['POST'],
                    'load-data-transport'   => ['POST'],
                    'remove-bill-from-ship' => ['POST'],
                    'remove-bill-from-transport-ship' => ['POST'],
                    'update-transport-ship' => ['POST']
                ],
            ],
        ];
    }


    public function actionDaily()
	{
        return $this->render('daily'); 

    }

    public function actionDailyBySales()
	{
        $comp_id  = Yii::$app->session->get('Rules')['comp_id'];
        $sale_id   = Yii::$app->session->get('Rules')['sale_id'];

        if(Yii::$app->session->get('Rules')['rules_id']==3){
            $models = SalesPeople::find()->where(['comp_id' => $comp_id,'id' => $sale_id, 'status' => 1])->all();
        }else {
            $models = SalesPeople::find()->where(['status' => 1,'comp_id' => $comp_id])->all();
            
        }

        $sales = [];
        foreach ($models as $key => $model) {
            $sales[] = (Object)[
                'id'            => $model->id,
                'code'          => $model->code, 
                'name'          => $model->name,
            ];
        }

        return $this->render('daily-by-sales', ['sales' => $sales]); 

    }

	public function actionReportDaily()
	{
        

        if(Yii::$app->session->get('Rules')['rules_id']==3) { // Sales
            // Map GPS
            // $contacts = Customer::find()->joinWith('zipcode')
            // ->where(['<>','customer.postcode',""])
            // ->andWhere(['<>','zipcode.latitude',""])
            // ->andWhere(['customer.status'=>'1'])
            // ->andWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            // ->andWhere(new Expression('FIND_IN_SET(:owner_sales, owner_sales)'))
            // ->addParams([':owner_sales' => Yii::$app->session->get('Rules')['sales_id']])
            // ->all();
            $contacts = [];

            return $this->render('saleReport',['contacts'=>$contacts]);
        }else {

            // // Map GPS
            // $contacts = Customer::find()->joinWith('zipcode')
            // ->where(['<>','customer.postcode',""])
            // ->andWhere(['<>','zipcode.latitude',""])
            // ->andWhere(['customer.status'=>'1'])
            // ->andWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            // ->all();
            $contacts = [];
            return $this->render('report-daily',['contacts'=>$contacts]);
        }
		
	}

    public function actionSalePeopleChart(){
        return $this->renderAjax('_chart_sales',['div' => $_GET['id'],'saleCode' => $_GET['saleCode']]);
    }

	public function actionSaleBalance()
    {
        
        $myCompany  = Yii::$app->session->get('Rules')['comp_id'];
        $SaleCode   = Yii::$app->session->get('Rules')['sale_id'];
        $key        = 'salebalance:'.$myCompany.'sale:'.$SaleCode.'date:'.date('Y-m-d');

        if(Yii::$app->cache->get($key)){
            return json_encode([
                'data' => 'cache',
                'raw' => Yii::$app->cache->get($key)
            ]);
        }else{
            if(Yii::$app->session->get('Rules')['rules_id']==3){
                $models = SalesPeople::find()->where(['comp_id' => $myCompany,'id' => $SaleCode, 'status' => 1])->all();
            }else {
                $models = SalesPeople::find()->where(['status' => 1,'comp_id' => $myCompany])->all();
                
            }
            

            $line = array();
            foreach ($models as  $model) {
                $line[] = [
                    'code'          => $model->code, 
                    'name'          => $model->name,
                    'thismonth'     => 0,
                    'Q_amount'      => 0,
                    'Y_amount'      => 0,
                    'iVthismonth'   => (int)$this->getSumPostedSaleInv($model->id,'m'),
                    'iVquater'      => 0,
                    'iVthisyear'    => (int)$this->getSumPostedSaleInv($model->id,'y')
                ];
                // $line[] = [
                //             'code'          => $model->code, 
                //             'name'          => $model->name,
                //             'thismonth'     => (int)$this->getSumSaleHeader($model->id,'m'),
                //             'Q_amount'      => (int)$this->getSumSaleHeader($model->id,'Q'),
                //             'Y_amount'      => (int)$this->getSumSaleHeader($model->id,'y'),
                //             'iVthismonth'   => (int)$this->getSumPostedSaleInv($model->id,'m'),
                //             'iVquater'      => (int)$this->getSumPostedSaleInv($model->id,'Q'),
                //             'iVthisyear'    => (int)$this->getSumPostedSaleInv($model->id,'y'),
                            
                //         ];
            }


            Yii::$app->cache->set($key,$line,3600);
            //return json_encode($line);
            return json_encode([
                'data' => 'api',
                'raw' => Yii::$app->cache->get($key)
            ]);
        }

    }



    public static function getSumSaleHeader($sales_people,$thisTime)
    {

        switch ($thisTime) {
            case 'm':            
                $models = SaleHeader::find()
                ->select('sum(balance) as balance')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between','DATE(order_date)',date('Y-m-d',strtotime('first day of this month')),date('Y-m-d')])
                ->groupby('sale_id')
                ->one();
                break;

            case 'Q': 

                $date =  new \DateTime(); // Current Date and Time
                $quarter_start = clone($date);

                // Find the offset of months
                $months_offset = ($date->format('m') - 1) % 3;

                // Modify quarter date
                $quarter_start->modify(" - " . $months_offset . " month")->modify("first day of this month");

                $quarter_end = clone($quarter_start);
                $quarter_end->modify("+ 3 month");

                $startDate = $quarter_start->format('Y-m-d');
                $endDate = $quarter_end->format('Y-m-d');

                $models = SaleHeader::find()
                ->select('sum(balance) as balance')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between', 'DATE(order_date)', $startDate, $endDate])
                ->groupby('sale_id')
                ->one();
                break;

            case 'y':            
                $models = SaleHeader::find()
                ->select('sum(balance) as balance')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between','DATE(order_date)',date('Y').'-01-01', date('Y').'-12-31'])
                ->groupby('sale_id')
                ->one();
                break;
            
            default:
                $models = SaleHeader::find()
                ->select('sum(balance) as balance')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between','DATE(order_date)',date('Y').'-01-01', date('Y').'-12-31'])
                ->groupby('sale_id')
                ->one();
                break;
        }

        // $total = 0;
    	// foreach ($models as $key => $model) {
    	// 	$total +=  $model->balance;
    	// }

    	return $models != null ? $models->balance : 0;
    	

    }
    public static function getSumPostedSaleInv($sales_people,$thisTime)
    {
        switch ($thisTime) {
            case 'm':
                $models = ViewRcInvoiceTotal::find()
                ->select('sum(total) as total')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between','DATE(posting_date)',date('Y-m-d',strtotime('first day of this month')),date('Y-m-d')])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['doc_type' => 'Sale'])
                ->groupby('sale_id')
                ->one();           
                break;
            
            case 'Q':

                $date =  new \DateTime(); // Current Date and Time
                $quarter_start = clone($date);

                // Find the offset of months
                $months_offset = ($date->format('m') - 1) % 3;

                // Modify quarter date
                $quarter_start->modify(" - " . $months_offset . " month")->modify("first day of this month");

                $quarter_end = clone($quarter_start);
                $quarter_end->modify("+ 3 month");

                $startDate = $quarter_start->format('Y-m-d');
                $endDate = $quarter_end->format('Y-m-d');

                $models = ViewRcInvoiceTotal::find()
                ->select('sum(total) as total')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between', 'DATE(posting_date)', $startDate, $endDate])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['doc_type' => 'Sale'])
                ->groupby('sale_id')
                ->one(); 
            
            case 'y':
                $models = ViewRcInvoiceTotal::find()
                ->select('sum(total) as total')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between','DATE(posting_date)', date('Y').'-01-01', date('Y').'-12-31'])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['doc_type' => 'Sale'])
                ->groupby('sale_id')
                ->one(); 
            
            default:
                $models = ViewRcInvoiceTotal::find()
                ->select('sum(total) as total')
                ->where(['sale_id' => $sales_people])
                ->andWhere(['between','DATE(posting_date)', date('Y').'-01-01', date('Y').'-12-31'])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['doc_type' => 'Sale'])
                ->groupby('sale_id')
                ->one(); 
                break;
        }       
         

    	
    	// $sumLine = 0;
    	// foreach ($models as $key => $model) {
     
    	// 	$sumLine +=  $model->totals;
 
    	// }

    	return $models != null ? $models->total : 0;

      


    }

    public static function actionJsonSaleHeaderArmchart()
    {
        $myCompany  = Yii::$app->session->get('Rules')['comp_id'];
        $SaleCode   = Yii::$app->session->get('sales_id');

        

         if(Yii::$app->session->get('Rules')['rules_id']==3)
        {
            $models = SaleHeader::find()
            ->where(['or',
                        ['sale_header.status'=>'Shiped'],
                        ['sale_header.status' => 'Checking'],
                        ['sale_header.status' => 'Invoiced']
                    ])
            ->andwhere(['comp_id' => $myCompany,'sales_people' => $SaleCode])->all();
             
        }else {
            $models = SaleHeader::find()
            ->where(['or',
                        ['sale_header.status'=>'Shiped'],
                        ['sale_header.status' => 'Checking'],
                        ['sale_header.status' => 'Invoiced']
                    ])
            ->andwhere(['comp_id' => $myCompany])->all();
            
        }

        //'Sun Jan 01 2017 02:04:29 GMT+0700 (+07)'
        $showLine = array();
        foreach ($models as $key => $model) {

            $value = round($model->balance);
            //$value = 0;

            $showLine[] = [
                'date' => date('D M d Y \G\M\TO ',strtotime($model->order_date)),
                'value1' => $value*1,
                'value2' => $value*1,
            ];
            //$showLine[] = '{"date":"'.date('D M m Y H:i:s \G\M\TO (+07)',strtotime($model->order_date)).'","value1":"'.$value.'","value2":"'.$value.'"}';
        }

         //$data = implode(',', $showLine);
           //$showLine.= '';
        return json_encode($showLine);
        //echo '['.$data.']';


    }

    public static function actionJsonSaleLine()
    {
        // $models = SaleHeader::find()
        // ->select('sale_line.*,sale_header.*')
        // ->leftJoin('sale_line','sale_line.order_no=sale_header.no');
    	$models = SaleLine::find()->all();
        // $models->joinwith(['orderNo']);

        // $models->where(['or',
        //             ['sale_header.status'=>'Shiped'],
        //             ['sale_header.status' => 'Checking'],
        //             ['sale_header.status' => 'Invoiced']
        //         ])->all();

        //'Sun Jan 01 2017 02:04:29 GMT+0700 (+07)'
    	$showLine = array();
    	foreach ($models as $key => $model) {

            $Header = SaleHeader::find()->where(['no' => $model->order_no])->one();
            $value = ($model->quantity * $model->unit_price);
            //$value = 0;

    		$showLine[]= [
                'date' => date('D M d Y H:i:s \G\M\TO (+07)',strtotime($Header->create_date)),
                'value1' => $value *1,
                'value2' => $value *1,
            ];
    	}

    	return json_encode($showLine);

    }

    public function getHeaderOfMonth($sales_people)
    {
        $models = SaleHeader::find()
            ->select(['sum(balance) as balance'])
            ->where(['sales_people' => $sales_people])
            ->andWhere(['MONTH(order_date)' => date('m')])
            ->andWhere(['or',
                        ['status'=>'Shiped'],
                        ['status' => 'Checking'],
                        ['status' => 'Invoiced'],
                        ['status' => 'Pre-Cancel']
                    ])

            ->groupBy(['sales_people'])->all();

        $sumLine = 0;
        foreach ($models as $key => $model) {
             
            $sumLine +=  $model->balance;
            
             
        }

        return $sumLine * 1;    
    }

    public function actionJsonSalePeopleColumnChart($saleId)
    {
        $models = SaleHeader::find()
        ->select(['Month(order_date) orderdate,sum(balance) as balance'])
        ->where(['sales_people' => $saleId])
        ->andWhere(['or',
                    ['status'=>'Shiped'],
                    ['status' => 'Checking'],
                    ['status' => 'Invoiced'],
                    ['status' => 'Pre-Cancel']
                ])

        ->groupBy(['YEAR(order_date), MONTH(order_date)'])->all();

        $line = array();
        foreach ($models as $key => $model) {

            $line[] = [
                        'month' => date('F', mktime(0, 0, 0, $model->orderdate, 10)) .' ('.$model->orderdate.')', 
                        'saleAmount' => $model->balance * 1,
                        
                      ];
        }

        return json_encode($line);

 

    }

    public function actionJsonThisYearsLineChart()
    {
        $comp = Yii::$app->session->get('Rules')['comp_id'];
        if(!Yii::$app->session->get('workyears')){
            Yii::$app->session->set('workyears',date('Y'));
        }
        $keys = 'jsonThisyears-lineChart&years:'.Yii::$app->session->get('workyears').'&comp:'.$comp;
        $data = Yii::$app->cache->get($keys);
        if($data){
            return json_encode([
                'data' => 'cache',
                'raw' => json_decode($data)
            ]);
        }else{

        
            $query = SaleHeader::find()
            ->select(['YEAR(order_date) as years_filter', 'MONTH(order_date) as month_filter' , 'sum(balance) as balance'])
            ->where(['IN','status', ['Shiped','Checking','Invoiced','Pre-Cancel']])
            ->andWhere(['comp_id' => $comp])
            ->andWhere(['between', 'DATE(order_date)', date('Y').'-01-01', date('Y-m-t')])
            ->groupBy(['YEAR(order_date)', 'MONTH(order_date)'])
            ->orderBy([
                'month_filter' => SORT_ASC
            ])
            ->all();

            $line = [];
            foreach ($query as $key => $model) {
                $balance = ($model->balance * 1) / 1000000;
                $line[] = [
                    'date'  => $model->years_filter.'-'.str_pad($model->month_filter, 2, "0", STR_PAD_LEFT).'-01',
                    'value' => number_format((float)$balance, 2, '.', ''),
                ];
            }

            Yii::$app->cache->set($keys, json_encode($line), 30);

            return json_encode([
                'data'  => 'api',
                'raw'   => json_decode(Yii::$app->cache->get($keys))
            ]);

        }

    }



    public function actionSalesDashboard(){

        return json_encode([
            'status' => 201,
            'message' => 'done',
            'value'     => [
                'status'        => $_POST['status'],
                'model'         => $_POST['model']
            ]
        ]);

    }



    public function actionSaleCashNoDetail()
    {
        //return Yii::$app->runAction('/Management/financial/sale-cash-no-detail');

        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $searchModel    = new \admin\modules\Management\models\RcinvheaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->where(['view_rc_invoice.comp_id' => $company]);
        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')  $dataProvider->query->andWhere(['sale_id' => $_GET['search-from-sale']]);
        }else{
            $dataProvider->query->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']]);
        }
        
        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));
        $formdate   = date('Y-').date('m-').'01';
        $todate     = date('Y-').date('m-').$LastDay;
        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));
        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));
 
        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //$dataProvider->query->andWhere(['view_rc_invoice.status' => 'Posted']);
        //--- /. Date Filter ---

        $dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);

        
        $dataProvider->query->orderBy(['cust_no_' => SORT_ASC,'posting_date' => SORT_ASC]);

        $dataProvider->pagination=false;
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('sale-cash-no-detail',[
                        'dataProvider' => $dataProvider,
                    ]);
        }

        return $this->render('sale-cash-no-detail',[
                    'dataProvider' => $dataProvider,
                ]);
    }

    public function actionBestSale(){
        
        $searchModel    = new BestsaleSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=10;
        
        if(isset($_POST['BestsaleSearch']['fdate'])){   
            if(($_POST['BestsaleSearch']['fdate']!='') && ($_POST['BestsaleSearch']['tdate']!='')) 
            $dataProvider->query->andWhere(['between', 'date(warehouse_moving.PostingDate)', date('Y-m-d',strtotime($_POST['BestsaleSearch']['fdate'])),date('Y-m-d',strtotime($_POST['BestsaleSearch']['tdate']))]);
        }

        //echo $dataProvider->query->createCommand()->rawSql;
        return $this->render('best-sale', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    public function actionInvoiceList(){

        $searchModel    = new \admin\modules\SaleOrders\models\ViewRcInvoiceSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=100;  
        $dataProvider->pagination=false;         

        // if(!isset($_POST['ViewRcInvoiceSearch']['posting_date'])){                
        //     $dataProvider->query->andWhere(['between', 
        //     'DATE(view_rc_invoice.posting_date)', 
        //     Yii::$app->session->get('workyears').'-01-01', 
        //     Yii::$app->session->get('workyears').'-12-31']);
        // }
        //echo $dataProvider->query->createCommand()->rawSql;

        
        return $this->render('invoice-list',[
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

    }

    public function actionInvoiceListPerDay(){

        $searchModel    = new \admin\modules\SaleOrders\models\ViewRcInvoiceDateSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=100;          

        return $this->render('invoice-list-date',[
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

    }


    public function actionSaleOrderList(){

        $searchModel    = new \admin\modules\SaleOrders\models\SalehearderFilterSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=500;              

        return $this->render('sale-order-list',[
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

    }

    public function actionIndex(){
        return $this->render('index');
    }

    public function actionSaleOrder(){
        $Company        = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        return $this->render('sale-order',[
            'company' => $Company
        ]);
    }

    public function actionSaleOrderModernTrade(){
        $Company        = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        return $this->render('sale-order-modern-trade',[
            'company' => $Company
        ]);
    }

    public function actionLoadData(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $trade          = isset($data->trade) ? $data->trade : '';

        $Query          = SaleHeader::find()
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        //->andWhere(['between','DATE(order_date)',date('Y-01-d',strtotime('first day of this month')), date('Y-m-d')])
                        //->andWHere(['>','balance', 0])
                        ->andWHere(['print_ship' => 1])
                        ->orderBy(['order_date' => SORT_DESC])
                        ->all();

        $raw    = [];
        foreach ($Query as $key => $model) {
            $raw[] = [
                'id'        => $model->id,
                'date'      => $model->order_date,
                'no'        => $model->no,
                'custId'    => $model->customer_id,
                'custName'  => $model->customer 
                                ? ($model->customer->nick_name != ''
                                    ? $model->customer->nick_name
                                    : $model->customer->name)
                                : '',
                'province'  => $model->customer
                                ? ($model->customer->provincetb
                                    ? $model->customer->provincetb->PROVINCE_ID
                                    : '')
                                : '',
                'modern'    => $model->customer
                                ? ($model->customer->genbus_postinggroup == 2 
                                    ? true
                                    : false)
                                : false,
                
                'saleName'  => $model->salespeople ? $model->salespeople->name : '',
                'saleCode'  => $model->salespeople ? $model->salespeople->code : '',
                // 'shiped'    => $model->customer 
                //                 ? $model->customer->transportList 
                //                     ? $model->customer->transportList->nick_name != '' 
                //                         ? $model->customer->transportList->nick_name
                //                         : $model->customer->transportList->name
                //                     : ''
                //                 : '',
                'boxs'      => $model->warehouse
                                ? $model->warehouse->boxs != '' 
                                    ? $model->warehouse->boxs
                                    : ''                                   
                                : '',
                'shipId'    => $model->shipment 
                                ? $model->shipment->id 
                                : '',
                'balance'   => $model->balance,
                'transport' => $model->warehouse
                            ? ($model->warehouse->transport 
                                ? ($model->warehouse->transport->nick_name != '' 
                                    ? $model->warehouse->transport->nick_name
                                    : $model->warehouse->transport->name)
                                : $model->warehouse->Description)
                            : '',
                'invoice'   => $model->hasInvoice 
                                    ? (Object)[
                                        'id' => $model->hasInvoice->id,
                                        'no' => $model->hasInvoice->no_,
                                        'status' => $model->hasInvoice->status,
                                        'date' => date('Y-m-d', strtotime($model->hasInvoice->posting_date))
                                    ]
                                    : []
                
            ];
        }

        return json_encode([
            'data' => $raw
        ]);
    }


    public function actionMakeBillToShip(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = Yii::t('common','Success');

        $model              = $this->findModel($data->id);
        $model->print_ship  = 1;


        if($model->save()){
            $status     = 200;
        }else{
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'status'    => $status,
            'data'      => $data,
            'message'   => $message
        ]);

    }


    public function actionRemoveBillFromShip(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $model          = $this->findModel($data->id);
        $model->print_ship = 0;
        
        if($model->save()){
            $status = 200;
        }else{
            $status = 500;
        }

        return json_encode([
            'status' => 200,
            'data' => $data
        ]);

    }

    public function actionRemoveBillFromTransportShip(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $model          = TransportOrder::findOne($data->id);
        
        if($model->delete()){
            $status = 200;
        }else{
            $status = 500;
        }

        return json_encode([
            'status' => 200,
            'data' => $data
        ]);

    }

    protected function findModel($id)
    {
        if (($model = SaleHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    

    public function actionOrderTracking(){
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
 
        return $this->render('order-tracking', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLoadDataTransport(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $trade          = isset($data->trade) ? $data->trade : '';
        
        $Query          = TransportOrder::find()
                        ->joinwith('saleOrder')
                        ->where(['transport_order.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->andWhere(['transport_order.user_id' => Yii::$app->user->identity->id])
                        ->orderBy(['sale_header.order_date' => SORT_DESC])
                        ->all();

        $raw    = [];
        foreach ($Query as $key => $model) {
            $raw[] = [
                'id'        => $model->id,
                'date'      => $model->saleOrder->order_date,
                'create'    => date('Y-m-d H:i:s', strtotime($model->saleOrder->create_date)),
                'no'        => $model->saleOrder->no,
                'orderId'   => $model->saleOrder->id,
                'custId'    => $model->saleOrder->customer_id,
                'custName'  => $model->saleOrder->customer 
                                ? ($model->saleOrder->customer->nick_name != ''
                                    ? $model->saleOrder->customer->nick_name
                                    : $model->saleOrder->customer->name)
                                : '',
                'province'  => $model->saleOrder->customer
                                ? ($model->saleOrder->customer->provincetb
                                    ? $model->saleOrder->customer->provincetb->PROVINCE_ID
                                    : '')
                                : '',
                'modern'    => $model->saleOrder->customer
                                ? ($model->saleOrder->customer->genbus_postinggroup == 2 
                                    ? true
                                    : false)
                                : false,
                
                'saleName'  => $model->saleOrder->salespeople ? $model->saleOrder->salespeople->name : '',
                'saleCode'  => $model->saleOrder->salespeople ? $model->saleOrder->salespeople->code : '',
                'boxs'      => $model->boxs?: ' ',
                'shipId'    => $model->saleOrder->shipment 
                                ? $model->saleOrder->shipment->id 
                                : '',
                'balance'   => $model->saleOrder->balance,
                //'transport' => $model->saleOrder->transport,
                'transport' => $model->transport  
                                ?: ($model->saleOrder->warehouse
                                    ? ($model->saleOrder->warehouse->transport 
                                        ? ($model->saleOrder->warehouse->transport->nick_name != '' 
                                            ? $model->saleOrder->warehouse->transport->nick_name
                                            : $model->saleOrder->warehouse->transport->name)
                                        : $model->saleOrder->warehouse->Description)
                                    : $model->saleOrder->transport),
                'area'      => $model->area?: ' ',
                'area_2'    => $model->area_2?: ' ',
                'remark'    => $model->remark?: ' ',
                'invoice'   => $model->saleOrder->hasInvoice 
                                    ? (Object)[
                                        'id' => $model->saleOrder->hasInvoice->id,
                                        'no' => $model->saleOrder->hasInvoice->no_,
                                        'status' => $model->saleOrder->hasInvoice->status,
                                        'date' => date('Y-m-d', strtotime($model->saleOrder->hasInvoice->posting_date))
                                    ]
                                    : []
                
            ];
        }

        return json_encode([
            'data' => $raw
        ]);
    }

    public function actionUpdateTransportShip(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;  
        $message        = Yii::t('common','{:e}',[':e' => 'Success']);

        $transaction    = Yii::$app->db->beginTransaction();
        try {
            $field          = $data->field;
        
            $model          = TransportOrder::findOne($data->id);
            $model->$field  = $data->value;

            if($model->save()){
                $status     = 200;
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
            'status' => 200,
            'message' => $message,
            'val' => $data->value
        ]);

    }

    public function actionDeleteTransportShip(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;  
        $message        = Yii::t('common','{:e}',[':e' => 'Success']);

        $transaction    = Yii::$app->db->beginTransaction();
        try {
             
            if(TransportOrder::deleteAll(['user_id' => Yii::$app->user->identity->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])){
                $status     = 200;
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
            'status' => 200,
            'message' => $message
        ]);
    }

}