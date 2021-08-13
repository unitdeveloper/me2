<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use admin\modules\items\models\SearchPicItems;
use common\models\ItemMystore;
use common\models\Items;
use admin\modules\SaleOrders\models\ItemSearch;
use common\models\TmpMenuGroup;
use common\models\TmpMenuGroupChild;

use common\models\SaleLine;
use common\models\SaleHeader;
use common\models\SalesPeople;
use admin\modules\SaleOrders\models\OrderSearch;

use admin\modules\SaleOrders\models\FunctionSaleOrder;

use common\models\WarehouseMoving;
use common\models\ViewRcInvoice;

use common\models\ViewRcInvoiceTotal;
use common\models\ViewMonthlySalesReport;
use common\models\ViewMonthlyBySales;
use admin\modules\apps_rules\models\SysRuleModels;
 
class AjaxController extends \yii\web\Controller
{
	public $company;
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }
    public function actionIndex()
    {
    	$searchModel = new SearchPicItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderpartial('index',[        	
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]);
    }

    public function actionItems()
    {
    	// $this->company = Yii::$app->session->get('Rules')['comp_id'];
      
        // $model = new SearchPicItems();

        // // ###################### (Fn 0) ######################
        // $Iset = Items::find()
        // ->select('itemset')
        // ->where(['id'=> Items::getMyitem(Yii::$app->session->get('Rules')['comp_id'])])
        // ->GroupBY('itemset')->all();

        // # Find Item Set
        // $data = array();
        // foreach ($Iset as $value) {
            
        //     # Get one item from itemset.           
        //     $data[] = $model->find()
        //             ->where(['itemset' => $value->itemset,])
        //             ->one()->No;
        // }

        $company = Yii::$app->session->get('Rules')['comp_id'];

       
      
        //$model = new ItemSearch();

        // ###################### (Fn 0) ######################
        $Iset = Items::find()
                    ->joinwith(['item_mystore'])
                    ->select('items.itemset')        
                    ->andWhere(['item_mystore.comp_id' => $company])
                    ->GroupBY('items.itemset')
                    ->all();

        # Find Item Set
        $data = array();
        foreach ($Iset as $value) {
            
			# Get one item from itemset.  
			//$data[] = $value->No; 
			$getItem =  Items::find()
                            ->select('items.id')
                            ->joinwith(['item_mystore'])
                            ->where(['items.itemset' => $value->itemset])
                            ->andWhere(['item_mystore.comp_id' => $company])
                            ->one();

            $data[] = $getItem != null 
                        ? $getItem->id 
                        : NULL;
		}
        
        
        // ###################### (End Fn 0) ######################

       
        $query   = Items::find()
                    ->joinwith(['item_mystore'])
                    ->joinwith(['itemSet'])
                    ->where(['items.id' => $data])
                    ->andWHere(['items.ItemGroup' => self::hrefVal($_POST['param']['href'])])
                    ->andwhere(['itemset.comp_id' => $company])
                    ->orderBy(['itemset.priority' => SORT_ASC]);
        //->orderBy(['LPAD(lower(itemset.name), 8,0)' => SORT_ASC, 'itemset.name' => SORT_ASC]);
        //->orderBy(['LENGTH(itemset.name)' => SORT_ASC, 'itemset.name' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
         

        // ###################### (1) ######################
        // $dataProvider = $model->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where([
        //      'items.ItemGroup' => self::hrefVal($_POST['param']['href']),
		// 		'items.id' => $data,
		// 		//'item_mystore.comp_id' => $company
        // ]);
        // $dataProvider->query->joinwith(['itemSet']);
        // $dataProvider->query->orderBy(['itemset.name' => SORT_ASC]);
        // $dataProvider->query->orderBy(['LPAD(lower(itemset.name), 10,0)' => SORT_ASC]);
        // $dataProvider->pagination=false;
        // echo $dataProvider->query->createCommand()->getRawSql();
        // ###################### (End 1) ######################
        

        return $this->renderpartial('items',[     	
            'dataProvider' => $dataProvider,
        ]);
    }



    protected function hrefVal($data)
    {
    	$data = explode('=',$data);
    	 
    	return $data['1'];
    }


    public function actionItemValidate()
    {
        $FincSale = new FunctionSaleOrder();
        return $FincSale->getItemSet();
        
        
    }


    public function addMenuChil($value)
    {
        var_dump($value);



    }
    


    public function actionItemGetdata()
    {
        $keys  = 'items-getdata&item:'.Yii::$app->request->post('param')['item'].'&user:'.Yii::$app->user->identity->id;
        $cache  = Yii::$app->cache;

		if(!$cache->get($keys)){
		 
            $model = Items::findOne(Yii::$app->request->post('param')['itemid']);
            if($model==null){
                $model = Items::find()->where(['No'=>Yii::$app->request->post('param')['item']])->one();
            }
            
            
            // $Query = WarehouseMoving::find()->where(['item' => $model->id]);
            // $RealInven = $Query->sum('Quantity');


            // $Remaining = $model->Inventory + $RealInven;

            // Get Last Price
            // if($model->StandardCost<=0){
            //     //$model->StandardCost = FunctionSaleOrder::lastprice($model->No)->unit_price *1;
            //     $model->StandardCost = $model->lastPrice *1;
            // }
            //$inven          = $model->qtyAfter *1;
            $inven      = $model->ProductionBom > 0
                            ? ($model->last_possible <= 0
                                ? $model->qtyForce->last_possible
                                : $model->last_possible)
                            : $model->last_stock;

            $data = [
                'status'    => 200,
                'id'        => $model->id,
                'item'      => $model->No,
                'ig'        => $model->ItemGroup,
                'Photo'     => $model->picture,
                'std'       => $model->lastPrice *1,
                'desc'      => '<div class="row">
                                    <div class="text-gray col-xs-12 col-sm-4 col-md-2">'.Yii::t('common','Product Name (en)').' : </div>
                                    <div class="col-xs-12 col-sm-8 col-md-10"><h4>'.$model->Description.'</h4></div>
                                </div>
                                <div style="margin-top:20px;" class="row">
                                    <div class="text-gray col-xs-12 col-sm-4 col-md-2">'.Yii::t('common','Product Name (th)').' : </div>
                                    <div class="col-xs-12 col-sm-8 col-md-10"><h4>'.$model->description_th.'</h4></div>
                                </div>
                                ',
                'code'      => $model->master_code,
                'workdate'  => Yii::$app->session->get('workdate'),
                //'remain'    => $Remaining,
                //'inven'     => $inven, 
                //'inven'     => $model->invenByBom,
                'inven'     => $inven,
                'html'      => 'Html',
                'message'   => $inven <= 0 
                                    ? Yii::t('common','OUT OF STOCK') 
                                    : ''      
            ];

            $cache->set($keys, json_encode($data), 10);
        }
        return $cache->get($keys);
    }

    public function actionJsonFindItem()
    {
        //return $_POST['param']['item'];
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $Items = Items::find()
        ->where(['or',
            ['like','barcode'      , $_POST['param']['item']],
            ['like','master_code'  , $_POST['param']['item']]
        ])
        ->andWhere(['company_id' => $company]);



        if($Items->count() >0)
        {
            //$model      = Items::find()->where(['master_code'=>$_POST['param']['item'],'company_id' => $company ])->one();
            $model      = $Items->one();

            $Query      = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
            $RealInven  = $Query->sum('Quantity');
            $Remaining  = $model->Inventory + $RealInven;

            $data = [
                    'id'        => $model->id,
                    'item'      => $model->No,
                    'ig'        => $model->ItemGroup,
                    'Photo'     => $model->Photo,
                    'std'       => $model->StandardCost,
                    'desc'      => $model->Description,
                    'descTh'    => $model->description_th,
                    'code'      => $model->master_code,
                    'remain'    => $Remaining,
                ];
            return json_encode($data);
        }else {
            $data = [
                    'id'        => 0,
                    'item'      =>'eWinl',
                    'ig'        => 0,
                    'Photo'     => 0,
                    'std'       => 0,
                    'desc'      => Yii::t('common','Empty'),
                    'descTh'    => 'ไม่มี Item นี้',
                    'code'      => 'eWinl',
                    'remain'    => 0,
                ];
            return json_encode($data);
        }
        
    }



    public function actionMenuRandom()
    {
        $this->company = Yii::$app->session->get('Rules')['comp_id'];
      
        $model = new SearchPicItems();
   
        // ###################### (Fn 0) ######################
        $Iset = Items::find()
        ->select('itemset')
        ->where(['id'=> Items::getMyitem(Yii::$app->session->get('Rules')['comp_id'])])
        ->andWhere(['<>','itemset',0])
        ->GroupBY('itemset')
        ->orderBy(new Expression('rand()'))
        ->limit(4)
        ->all();

        
         

        # Find Item Set
        $data = array();
        foreach ($Iset as $value) {
            
            # Get one item from itemset.           
            $data[] = $model->find()
                    ->where(['itemset' => $value->itemset])
                    ->orderBy(new Expression('rand()'))
                    ->one()->No;
        }
        
        
        // ###################### (End Fn 0) ######################



        // ###################### (1) ######################

        $dataProvider = $model->search(Yii::$app->request->queryParams);
        //'ItemGroup' => '8', 
        //->orderBy(new Expression('rand()'))
        $dataProvider->query->where(['No' => $data])
                            ->orderBy(new Expression('rand()'))
                            ->limit(4); 
        
         
        ###################### (End 1) ######################

        return $this->renderpartial('items',[       
            'dataProvider' => $dataProvider,
            ]);
    }

    protected function sortData($data){
        $total 	= [];
        foreach ($data as $key => $value) {
            $total[$key] =  $value->total;
        }
        array_multisort($total, SORT_DESC, $data);
        return $data;
    }

    public function actionDataChart(){
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $keys       = 'topSales&comp'.$comp;
        $salePeople = SalesPeople::find()->where(['status' => 1,'comp_id' => $comp]);
        
        if(Yii::$app->cache->get($keys)){
            return Yii::$app->cache->get($keys);
        }else{
            $data 	= [];
            foreach ($salePeople->all() as $key => $sale) {            
                $total = 0;
                $query = ViewRcInvoiceTotal::find()
                ->where(['sale_id' => $sale->id])
                ->andWhere(['between','DATE(posting_date)',date('Y').'-01-01',date('Y-m-d')])
                ->all();
                foreach ($query as $key => $model) {
                        $total += $model->totals;
                }
                $data[] = (Object)[
                    'id' => $sale->id,
                    'name' => $sale->name,
                    'total' => $total,
                    'img' => $sale->picture
                ];            
            }
            //จัดเรียงข้อมูล จากจำนวนเงิน (มาก) ไปหา (น้อย)
            $DataSort   = self::sortData($data);

            // จัดเรียงเฉพาะชื่อ sale
            $sales	    = [];
            foreach ($DataSort as $key => $value) {
                $sales[] 	= $value->name;
            }

            // จัดเรียงเฉพาะจำนวนเงิน
            $dataList = [];
            foreach ($DataSort as $key => $value) {
                $dataList[] 	= round($value->total/1000000,1);
            }
            
            $rawData = json_encode([
                'status' => 200,
                'sales' => $sales,
                'data' => $dataList,
                'fulldata' => $DataSort
            ]);
            Yii::$app->cache->set($keys, $rawData, (60 * 60 *30));
            return Yii::$app->cache->get($keys);

        }
    }

    public function actionCountSchedule(){
        return json_encode([
            'dialy'     => self::getDialy(),
            //'weekly'    => self::getWeekly(),
            'monthly'   => self::getMonthly(),
            'summary' => json_decode(self::actionSaleBalanceHeader())
        ]);
    }

    public function actionCountOrders()
    {   

        return json_encode([
            'Release'   => self::getStatus('Release'),
            'Checking'  => self::getStatus('Checking'),
            'Shiped'    => self::getStatus('Shiped'),
            'cancel'    => self::getStatus('cancel'),
            'invoiced'  => self::getStatus('Invoiced'),
            //'dialy'     => self::getDialy(),
            //'weekly'    => self::getWeekly(),
            //'monthly'   => self::getMonthly(),
            //'summary' => json_decode(self::actionSaleBalanceHeader())
        ]);
    }


    protected function getDialy(){
 
        $Today          = self::getDailyCommand('Today');
        $yesterday      = self::getDailyCommand('Yesterday');
        $lastmonth      = self::getDailyCommand('LastMonth');
        $percent        = 0;

        if ( ($yesterday!=0) && ($Today!=0) ){
            $percent        = (($Today - $yesterday) / $yesterday)  * 100;
        }    

        return (Object)[
            'color' => ($percent < 0 )? 'red' : 'green',
            'icon'  => ($percent < 0 )? 'fas fa-chevron-circle-down text-red' : 'fas fa-chevron-circle-up text-green',
            'percent' => $percent,
            'sign' => ($percent < 0 )? null : '+',
            'amount' => $Today,
            'yesterday' => $yesterday,
            'currency' => 'THB',
            'lastmonth' => $lastmonth,
            'panel' => [
                'class' => ($percent < 0 )? 'panel panel-danger' : 'panel panel-success',
                'head' => ($percent < 0 )? 'panel-heading bg-red' : 'panel-heading bg-green'
            ]
        ];
    }
    protected function getDailyCommand($time){

        if (in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','order','actionIndex','SaleFilter'))) {  
        
            switch ($time) {
                case 'Today':
                    $models = ViewRcInvoice::find()
                    ->where(['=','DATE(posting_date)',date('Y-m-d')])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;

                case 'Yesterday':                
                    $models = ViewRcInvoice::find()
                    ->where(['=','DATE(posting_date)',date('Y-m-d',strtotime('yesterday'))])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                case 'LastMonth':
                    $models = ViewRcInvoice::find()
                    ->where(['DATE(posting_date)' => date('Y-m-d',strtotime('today - 1 MONTH'))])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                default:
                    # code...
                    break;
            }      
            
        } else {

            switch ($time) {
                case 'Today':
                    $models = ViewRcInvoice::find()
                    ->where(['=','DATE(posting_date)',date('Y-m-d')])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;

                case 'Yesterday':                
                    $models = ViewRcInvoice::find()
                    ->where(['=','DATE(posting_date)',date('Y-m-d',strtotime('yesterday'))])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                case 'LastMonth':
                    $models = ViewRcInvoice::find()
                    ->where(['DATE(posting_date)' => date('Y-m-d',strtotime('today - 1 MONTH'))])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                default:
                    # code...
                    break;
            }   

        }
        //echo $models->createCommand()->rawSql;
        
        $total = 0;
        if($models->count() > 0){
            foreach ($models->all() as $key => $model) {
                $total += $model->total;
            }
        }

        return $total;
    }
    


    protected function getWeekly(){
         
        $thisweek       = self::getWeeklyCommand('ThisWeek');
        $lastweek       = self::getWeeklyCommand('LastWeek');
        $lastmonth      = self::getWeeklyCommand('LastMonth');
        $percent        = 0;

        if ( ($lastweek!=0) && ($thisweek!=0) ){
            $percent        = (($thisweek - $lastweek) / $lastweek )  * 100 ;
        }
    

        return (Object)[
            'color' => ($percent < 0 )? 'red' : 'green',
            'icon'  => ($percent < 0 )? 'fas fa-chevron-circle-down text-red' : 'fas fa-chevron-circle-up text-green' ,
            'percent' => $percent,
            'sign' => ($percent < 0 )?  null : '+' ,
            'amount' => $thisweek,
            'lastweek' => $lastweek,
            'currency' => 'THB',
            'lastmonth' => $lastmonth,
            'panel' => [
                'class' => ($percent < 0 )?  'panel panel-danger' : 'panel panel-success' ,
                'head' => ($percent < 0 )?   'panel-heading bg-red' : 'panel-heading bg-green'
            ]
        ];
    }

    protected function getWeeklyCommand($time){


        $monday         = strtotime("last monday");
        $monday         = date('W', $monday)==date('W') ? $monday-7*86400 : $monday;        
        $sunday         = strtotime(date("Y-m-d",$monday)." +6 days");
        $this_week_sd   = date("Y-m-d",$monday);
        $this_week_ed   = date("Y-m-d",$sunday);

        if (in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','order','actionIndex','SaleFilter'))) {  
        
            switch ($time) {
                case 'ThisWeek':
                    $models = ViewRcInvoice::find()
                    ->where(['between','DATE(posting_date)', date('Y-m-d',strtotime('this week')), date('Y-m-d')])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;

                case 'LastWeek':
                
                    $models = ViewRcInvoice::find()
                    ->where(['between','posting_date',$this_week_sd,$this_week_ed])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                case 'LastMonth':
                    $models = ViewRcInvoice::find()
                    ->where(['between','posting_date',date('Y-m-d',strtotime('this week - 1 MONTH')),date('Y-m-d',strtotime('today - 1 MONTH'))])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                default:
                    # code...
                    break;
            }      
            
        } else {
            switch ($time) {
                case 'ThisWeek':
                    $models = ViewRcInvoice::find()
                    ->where(['between','DATE(posting_date)', date('Y-m-d',strtotime('this week')), date('Y-m-d')])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;

                case 'LastWeek':
                    $models = ViewRcInvoice::find()
                    ->where(['between','posting_date',$this_week_sd,$this_week_ed])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                case 'LastMonth':
                    $models = ViewRcInvoice::find()
                    ->where(['between','posting_date',date('Y-m-d',strtotime('this week - 1 MONTH')),date('Y-m-d',strtotime('today - 1 MONTH'))])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                default:
                    # code...
                    break;
            }      
        }

        
        $total = 0;
        if($models->count() > 0){
            foreach ($models->all() as $key => $model) {
                $total += $model->total;
            }
        }

        return $total;
    }


    protected function getMonthly(){
         
        $thismonth      = self::getMonthlyCommand('Thismonth');
        $lastmonth      = self::getMonthlyCommand('LastMonth');
        $percent        = 0;

        if ( ($lastmonth!=0) && ($thismonth!=0) ){
            $percent        = (($thismonth - $lastmonth) / $lastmonth )  * 100 ;
        }
    
        return (Object)[
            'color' => ($percent < 0 )? 'red' : 'green',
            'icon'  => ($percent < 0 )? 'fas fa-chevron-circle-down text-red' : 'fas fa-chevron-circle-up text-green' ,
            'percent' => $percent,
            'sign' => ($percent < 0 )?  null : '+' ,
            'amount' => $thismonth,
            'currency' => 'THB',
            'lastmonth' => $lastmonth,
            'panel' => [
                'class' => ($percent < 0 )?  'panel panel-danger' : 'panel panel-success' ,
                'head' => ($percent < 0 )?   'panel-heading bg-red' : 'panel-heading bg-green'
            ]
        ];
    }

    protected function getMonthlyCommand($time){

        if (in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','order','actionIndex','SaleFilter'))) {  
        
            switch ($time) {
                case 'Thismonth':
                    $models = ViewRcInvoice::find()
                    ->where(['between','DATE(posting_date)', date('Y-m-d',strtotime('first day of this month')), date('Y-m-d',strtotime('last day of this month'))])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                case 'LastMonth':
                    $models = ViewRcInvoice::find()
                    ->where(['between','DATE(posting_date)',date('Y-m-d',strtotime('first day of this month - 1 MONTH')),date('Y-m-d',strtotime('last day of this month - 1 MONTH'))])
                    ->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                default:
                    # code...
                    break;
            }      
            
        } else {
            switch ($time) {
                case 'Thismonth':
                    $models = ViewRcInvoice::find()
                    ->where(['between','DATE(posting_date)', date('Y-m-d',strtotime('first day of this month')), date('Y-m-d',strtotime('last day of this month'))])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                case 'LastMonth':
                    $models = ViewRcInvoice::find()
                    ->where(['between','DATE(posting_date)',date('Y-m-d',strtotime('first day of this month - 1 MONTH')),date('Y-m-d',strtotime('last day of this month - 1 MONTH'))])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                    break;
                
                default:
                    # code...
                    break;
            }      
        }

        
        $total = 0;
        if($models->count() > 0){
            foreach ($models->all() as $key => $model) {
                $total += $model->total;
            }
        }

        return $total;
    }



   
    // protected function getDialyX(){

    //     // Today
    //     $models = ViewRcInvoice::find()
    //     ->where(['=','DATE(posting_date)',date('Y-m-d')]);
    //     $Today = 0;
    //     if($models->count() > 0){
    //         foreach ($models->all() as $key => $model) {
    //             $Today += $model->total;
    //         }
    //     }

    //     // Yesterday
    //     $models2 = ViewRcInvoice::find()
    //     ->where(['=','DATE(posting_date)',date('Y-m-d',strtotime('yesterday'))]);
    //     $yesterday = 0;
    //     if($models2->count() > 0){
    //         foreach ($models2->all() as $key => $model) {
    //             $yesterday += $model->total;
    //         }
    //     }
        
    //     $Cyesterday     = ($yesterday > 0)? $yesterday : 1 ;
    //     $Ctoday         = ($Today > 0)? $Today : 1;
    //     $sign           = ($Ctoday < $Cyesterday)? '-' : '+';
         
    //     $percent        = ($Ctoday/$Cyesterday) * 100;


    //      // This day on last month
    //      $models3 = ViewRcInvoice::find()
    //      ->where(['DATE(posting_date)' => date('Y-m-d',strtotime('today - 1 MONTH'))]);
    //      //echo $models3->createCommand()->rawSql;
    //      $lastmonth = 0;
    //      if($models3->count() > 0){
    //          foreach ($models3->all() as $key => $model) {
    //              $lastmonth += $model->total;
    //          }
    //      }
         

    //     return (Object)[
    //         'color' => ($Ctoday < $Cyesterday)? 'red' : 'green',
    //         'icon'  => ($Ctoday < $Cyesterday)? 'fas fa-chevron-circle-down fa-4x text-red' : 'fas fa-chevron-circle-up fa-4x text-green',
    //         'percent' => $sign.$percent,
    //         'sign' => ($Ctoday < $Cyesterday)? null : '+',
    //         'amount' => $Today,
    //         'yesterday' => $yesterday,
    //         'currency' => '฿',
    //         'lastmonth' => $lastmonth,
    //         'panel' => [
    //             'class' => ($Ctoday < $Cyesterday)? 'panel panel-danger' : 'panel panel-success',
    //             'head' => ($Ctoday < $Cyesterday)? 'panel-heading bg-red' : 'panel-heading bg-green'
    //         ]
    //     ];
    // }


    // protected function weekDialyX(){
    //     // Weekly
    //     $models = ViewRcInvoice::find()
    //     ->where(['between','DATE(posting_date)', date('Y-m-d',strtotime('this week')), date('Y-m-d')])
    //     ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

    //     //echo $models->createCommand()->rawSql;
    //     $thisweek = 0;
    //     if($models->count() > 0){
    //         foreach ($models->all() as $key => $model) {
    //             $thisweek += $model->total;
    //         }
    //     }

    //     // Last Week
    //     $monday = strtotime("last monday");
    //     $monday = date('W', $monday)==date('W') ? $monday-7*86400 : $monday;
        
    //     $sunday = strtotime(date("Y-m-d",$monday)." +6 days");
    //     $this_week_sd = date("Y-m-d",$monday);
    //     $this_week_ed = date("Y-m-d",$sunday);

    //     $models2 = ViewRcInvoice::find()
    //     ->where(['between','posting_date',$this_week_sd,$this_week_ed])
    //     ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
    //     // echo $models2->createCommand()->rawSql;
    //     $lastweek = 0;
    //     if($models2->count() > 0){
    //         foreach ($models2->all() as $key => $model) {
    //             $lastweek += $model->total;
    //         }
    //     }

    //     // This week on last month
    //     $models3 = ViewRcInvoice::find()
    //     ->where(['between','posting_date',date('Y-m-d',strtotime('this week - 1 MONTH')),date('Y-m-d',strtotime('today - 1 MONTH'))])
    //     ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
    //     //echo $models3->createCommand()->rawSql;
    //     $lastmonth = 0;
    //     if($models3->count() > 0){
    //         foreach ($models3->all() as $key => $model) {
    //             $lastmonth += $model->total;
    //         }
    //     }

        
    //     $Clastweek      = ($lastweek > 0)? $lastweek : 1 ;
    //     $Cthisweek      = ($thisweek > 0)? $thisweek : 1;
    //     $sign           = ($Cthisweek < $Clastweek)? '-' : '+';
         
    //     $percent        = ($Cthisweek/$Clastweek) * 100;
    //     return (Object)[
    //         'color' => ($Cthisweek < $Clastweek)? 'red' : 'green',
    //         'icon'  => ($Cthisweek < $Clastweek)? 'fas fa-chevron-circle-down fa-4x text-red' : 'fas fa-chevron-circle-up fa-4x text-green',
    //         'percent' => $sign.$percent,
    //         'sign' => ($Cthisweek < $Clastweek)? null : '+',
    //         'amount' => $thisweek,
    //         'lastweek' => $Clastweek,
    //         'currency' => '฿',
    //         'lastmonth' => $lastmonth,
    //         'panel' => [
    //             'class' => ($Cthisweek < $Clastweek)? 'panel panel-danger' : 'panel panel-success',
    //             'head' => ($Cthisweek < $Clastweek)? 'panel-heading bg-red' : 'panel-heading bg-green'
    //         ]
    //     ];
    // }
    

    protected function getStatus($status){
        $myCompany  = Yii::$app->session->get('Rules')['comp_id'];
        $SaleCode   = Yii::$app->session->get('sales_id');
        
            
        if(Yii::$app->session->get('Rules')['rules_id']==3)
        {
            $count = \common\models\SaleHeader::find()
            ->where(['status' => $status])
            ->andWhere(['comp_id' => $myCompany])
            ->andWhere(['sales_people' => $SaleCode])
            ->andWhere(['YEAR(order_date)' => (Yii::$app->session->get('workyears')? Yii::$app->session->get('workyears') : date('Y'))])
            ->count();

        }else{
            $count = \common\models\SaleHeader::find()
            ->where(['status' => $status])
            ->andWhere(['comp_id' => $myCompany])
            ->andWhere(['YEAR(order_date)' => (Yii::$app->session->get('workyears')? Yii::$app->session->get('workyears') : date('Y'))])
            ->count();
        }

        return $count;
    }

    public function actionSaleBalance()
    {
        $myCompany  = Yii::$app->session->get('Rules')['comp_id'];
        $SaleCode   = Yii::$app->session->get('sales_id');
        $userid     = Yii::$app->user->identity->id;

        if(Yii::$app->session->get('Rules')['rules_id']==3){    

            $header = \common\models\SaleHeader::find()
            ->where(['comp_id' => $myCompany])
            ->andWhere(['sales_people' => $SaleCode])
            ->andWhere(['or',
                        ['status' => 'Shiped'],
                        ['status' => 'Checking'],
                        ['status' => 'Invoiced']])
            ->all();

        }else {
            $header = \common\models\SaleHeader::find()
            ->where(['comp_id' => $myCompany])
            ->andWhere(['or',
                        ['status'=>'Shiped'],
                        ['status' => 'Checking'],
                        ['status' => 'Invoiced']])
            ->all();

        }

        $orderno    = '';
        foreach ($header as $key => $value) {
            $orderno[]= $value->no;
        }
    
        $query      = \common\models\SaleLine::find();
        if(Yii::$app->session->get('Rules')['rules_id']==3){
            $query->where(['comp_id' => $myCompany,'user_id' => $userid]);
        }else{
            $query->where(['comp_id' => $myCompany]);
        }

        $query->where(['order_no' => $orderno]);
        $balance    = $query->sum('quantity * unit_price'); 

        return number_format($balance);

    }

    public function actionSaleBalanceHeader()
    {
        $myCompany      = Yii::$app->session->get('Rules')['comp_id'];
        $SaleId         = Yii::$app->session->get('Rules')['sale_id'];
        $keys           = 'SaleBalanceHeader&comp:'.$myCompany.'&sale:'.$SaleId.'&workdate:'.Yii::$app->session->get('workdate');
        
        if(Yii::$app->cache->get($keys)){
            return json_encode(Yii::$app->cache->get($keys));
        }else{
             
            $status = $this->getTotalStatus();
            $list = [
                'saleorder'     => $this->getSaleThis('01',date('m')),
                'invoice'       => $status->myinvoice,
                'notinvoice'    => $status->notinvoice,
                'year'          => Yii::$app->session->get('workyears'),
                'salethismonth' => $this->getSaleThisMonth(),
                'invthismonth'  => $this->getInvThisMonth()
            ];

            Yii::$app->cache->set($keys, $list, 300);

            return json_encode(Yii::$app->cache->get($keys));



            /**
             * ให้ดึงรายการจาก BI มาใช้
             * ถ้าไม่มี ให้ดึงไปเก็บ (ทุก 1 ชั่วโมง)
             */
            /*
            $myinfo         = \common\models\SalesPeople::findOne($SaleId);
            $bi             = \common\models\BiDashboard::find()
                            ->where(['user_id' => Yii::$app->user->identity->id])
                            ->andWhere(['filter_years' => Yii::$app->session->get('workyears')])
                            ->one();

            

            if($bi !== NULL){

                #UPDATE


                if(Yii::$app->request->get('reload')){
                    #UPDATE
                    $bi->getdate        = date('Y-m-d H:i:s');
                    $bi->filter_years   = Yii::$app->session->get('workyears');
                    $bi->user_id        = Yii::$app->user->identity->id;
                    $bi->sale_invoice   = ($myinfo!==null)? $myinfo->orders->myinvoice : 0;
                    $bi->sale_balance   = ($myinfo!==null)? $myinfo->orders->saleTotal : 0;
                    $bi->not_invoice    = ($myinfo!==null)? $myinfo->orders->notinvoice : 0;
                    $bi->save();              
                }

                if(date('H',strtotime($bi->getdate)) != date('H')){
                    #UPDATE
                    $bi->getdate        = date('Y-m-d H:i:s');
                    $bi->filter_years   = Yii::$app->session->get('workyears');
                    $bi->user_id        = Yii::$app->user->identity->id;
                    $bi->sale_invoice   = ($myinfo!==null)? $myinfo->orders->myinvoice : 0;
                    $bi->sale_balance   = ($myinfo!==null)? $myinfo->orders->saleTotal : 0;
                    $bi->not_invoice    = ($myinfo!==null)? $myinfo->orders->notinvoice : 0;
                    $bi->save();               

                } 

                #RETURN DATA
                $balance    = $bi->sale_balance;
                $invoice    = $bi->sale_invoice;
                $notinvoice = $bi->not_invoice;

                
            }else {
                #CREATE        

                $newbi      = new \common\models\BiDashboard();
                $newbi->getdate         = date('Y-m-d H:i:s');
                $newbi->filter_years    = Yii::$app->session->get('workyears');
                $newbi->user_id         = Yii::$app->user->identity->id;
                $newbi->sale_invoice    = ($myinfo!==null)? $myinfo->orders->myinvoice : 0;
                $newbi->sale_balance    = ($myinfo!==null)? $myinfo->orders->saleTotal : 0;
                $newbi->not_invoice     = ($myinfo!==null)? $myinfo->orders->notinvoice : 0;
                $newbi->save();

                #RETURN DATA
                $balance    = $newbi->sale_balance;
                $invoice    = $newbi->sale_invoice;
                $notinvoice = $newbi->not_invoice;

            }
            
             
            */
        }

       

    }

    protected function getDateRang($con){

        $workMonth  = Yii::$app->session->get('workmonth') ? str_pad(Yii::$app->session->get('workmonth'),2,"0",STR_PAD_LEFT) : date('m');
        $workYears  = Yii::$app->session->get('workyears') ? Yii::$app->session->get('workyears') : date('Y');
        $workDay    = Yii::$app->session->get('workdate') ? date('d',strtotime(Yii::$app->session->get('workdate'))) : date('d');  
        $workDate   = Yii::$app->session->get('workdate') ? $workYears.'-'.$workMonth.'-'.$workDay : date('Y-m-').'01';  

        if($con == 'start'){
            return $workYears.'-'.$workMonth.'-01';
        }else{
            return $workYears.'-'.$workMonth.'-'.date('t');
        }
        
    }
     
    protected function getSaleThisMonth(){

        $total = 0;
        
        $query =  SaleHeader::find()
        ->where(['between', 'date(order_date)', self::getDateRang('start'), self::getDateRang('end')])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['NOT IN','status',['Reject', 'Credit-Note','Cancel']]);

        !in_array(Yii::$app->session->get('Rules')['rules_id'],[1,14,13,7]) 
        ? $query->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']]) 
        : null;

        foreach ($query->all() as $key => $model) {
            $total+= $model->sumtotal->total;
        }
        
        return $total;
        
    }

    protected function getSaleThis($Start,$Month){

        $total = 0;
        $workYears  = Yii::$app->session->get('workyears') ? Yii::$app->session->get('workyears') : date('Y');
        $query =  SaleHeader::find()
        ->where(['between', 'date(order_date)', date('Y-m-d', strtotime($workYears.'-'.$Start.'-01')), date('Y-m-t', strtotime($workYears.'-'.$Month.'-10'))])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['NOT IN','status',['Reject', 'Credit-Note','Cancel']]);

        !in_array(Yii::$app->session->get('Rules')['rules_id'],[1,14,13,7]) 
        ? $query->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']]) 
        : null;

        foreach ($query->all() as $key => $model) {
            $total+= $model->sumtotal->total;
        }
        
        return $total;
        
    }

    protected function getInvThisMonth(){
        $total = 0;
      
        $query = \common\models\ViewRcInvoiceTotal::find()
            ->select('total')
            ->andWhere(['between', 'date(posting_date)', $this->getDateRang('start'), $this->getDateRang('end')])
            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->andWhere(['doc_type' => 'Sale']);
       
        !in_array(Yii::$app->session->get('Rules')['rules_id'],[1,14,13,7]) 
                    ? $query->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']]) 
                    : null;

        foreach ($query->all() as $key => $model) {
            $total+= $model->totals;
        }

        return $total;
        
    }


    public function actionPercentDiscount($key)
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel(Yii::$app->request->post('id'));       
        
        $model->discount            = @$_POST['discount'] ?: 0;        
        $model->percent_discount    = @$_POST['percent'] ?: null;
        //$model->vat_type            = $_POST['vat_type'];
        $model->include_vat         = $_POST['inc_vat'];
        //$model->vat_percent         = $model->vattb->vat_value;
        $model->vat_percent         = (@$_POST['vat_percent'])? @$_POST['vat_percent'] : 0;
        $model->payment_term        = $_POST['credit'];
        $model->paymentdue          = $_POST['due'];

        
        if(@$_POST['discount']==0){             
            $model->discount = @$_POST['percent'] * $model->sumLine /100;
        }
         
        // if($key=='discount'){
        //     $division = ($model->sumLine > 0)? $model->sumLine : 1;
        //     $model->percent_discount    = ($model->discount / $division) *100; 
        // }
        
        
        //$this->percent_discount * $model->sumtotal->total /100;  
        //discount/subtotal*100;     
        //$model->balance             = $model->sumtotal->total;

        //$model->balance             = FunctionSaleOrder::GrandTotalSaleOrder($model);
        
        if(!$model->save()){
            var_dump($model->getErrors(),JSON_UNESCAPED_UNICODE);            
        }

        // $searchModel    = new OrderSearch();
        // $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['sourcedoc' => $model->id]);
        // $dataProvider->query->andwhere(['comp_id' => $company]);

        return json_encode(
            [ 
                'html' => $this->renderAjax('../saleorder/_sum_line',
                            [
                                'model' => $model,
                                //'dataProvider' => $dataProvider,
                            ]),
                'promo' => $this->getPromotions($model)
            ]);
        // return $this->renderAjax('../saleorder/_sum_line',
        //                     [
        //                         'model' => $model,
        //                         'dataProvider' => $dataProvider,
        //                     ]);

    }

    protected function getPromotions($header){

        // ตรวจสอบ Promotion
        
        $lines = SaleLine::find()
        ->select('item, sum(quantity) as quantity, sum(unit_price * quantity) as total')
        ->where(['sourcedoc' => $header->id])
        ->groupBy('item')
        ->all();

        $data = [];
       
        foreach ($lines as $key => $model) {

            // ถ้ามี Promotion ให้คำนวนส่วนลด
            $promo = \common\models\PromotionsItemGroup::find()
            ->where(['item' => $model->item])
            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();

            // ตรวจสอบสินค้า มีโปรโมชัน หรือไม่
            if ($promo!=null){
                // มีโปรโมชัน
                // 1. โปรโมชัน มีรายละเอียดอะไรบ้าง
                // 2. ถึงโปรโมชันหรือยัง
                    $checkPromo = \common\models\Promotions::find()
                    ->where(['item_group' => trim($promo->name)])
                    ->andWhere(['<=', 'start_date', new Expression('CURDATE()')])
                    ->andWhere(['>=', 'end_date', new Expression('CURDATE()')])
                    ->andWhere(['status' => 4])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->one();

                    $saleAmount = 0;
                    $salePrice  = 0;

                    if ($checkPromo != null){
                        $saleAmount = $checkPromo->sale_amount * $model->quantity;
                        $salePrice  = $model->total;

                        $data[] = (Object)[
                            'name' => $promo->name,
                            'promotion' => $checkPromo->sale_amount,                            
                            'item' => $model->item,
                            'discount_perunit' => $checkPromo->discount,
                            'current_total' => $model->total,
                            'current_discount' => ($model->total >= $checkPromo->sale_amount)? $checkPromo->discount : 0,
                            'sum_discount' => (floor($model->total / $checkPromo->sale_amount) *$checkPromo->discount )
                            //'discount' => $saleAmount,
                            //'unit_price' => $salePrice
                        ];
                    }
                    // $data[] = $promo->name;
                    // 2.1 ยังไม่ถึง 
                    // 2.2 ถึงโปรโมชันแล้ว
            }else{
                // ไม่มีโปรโมชัน
            }
        }

        return (Object)([
            'status' => 200,
            'data' => $data,
            'id' => $header->id,
            'text' => [
                'label_promotion' => Yii::t('common','Promotion'),
                'label_buy' => Yii::t('common','Buy the product.'),
                'label_getdiscount' => Yii::t('common','Get Discount'),
                'label_totaldiscount' => Yii::t('common','Total Discount')
            ]
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

    public function actionCustomersCount()
    {
        $comp = Yii::$app->session->get('Rules')['comp_id'];
        return json_encode([
            'count'     => \common\models\Customer::find()->where(['comp_id' => $comp])->count(),
            'cancel'    => \common\models\Customer::find()->where(['status' => 0,'comp_id' => $comp])->count(),
            'cancelThisMonth'    => \common\models\Customer::find()->where(['status' => 0,'MONTH(create_date)' => date('m'),'YEAR(create_date)' => date('Y'),'comp_id' => $comp])->count(),
            'new'       => \common\models\Customer::find()->where(['MONTH(create_date)' => date('m'),'YEAR(create_date)' => date('Y'),'comp_id' => $comp])->count()
        ]);  

       
    }


    public function actionCustomerCount($param)
    {
        switch ($param) {

            case 'count':
                 $model = \common\models\Customer::find()->count();
                 return $model;
                break;
            

            case 'status':
                 $model = \common\models\Customer::find()->where(['status' => 0])->count();
                 return $model;
                break;


            case 'new':
                 $model = \common\models\Customer::find()->where(['MONTH(create_date)' => date('m')])->count();
                 return $model;
            break;

            default:
                # code...
                break;
        }

       
    }

    public function actionHasShip($source)
    {

        $line = SaleLine::findOne($source);
        //$Query = WarehouseMoving::find()->where(['SourceDoc' => $source]);

        // $Query = \common\models\WarehouseMoving::find()
        //                   ->joinwith('header')
        //                   //->where(['warehouse_moving.SourceDoc' => $source])
        //                   ->where(['warehouse_moving.item' => $line->item])
        //                   //->andwhere(['IN','warehouse_moving.TypeOfDocument' ,['Sale']])
        //                   ->andwhere(['NOT IN','warehouse_moving.TypeOfDocument' ,['Undo-Output']])
        //                   ->andwhere(['warehouse_header.order_id' => $line->saleHeader->id])
        //                   ->andwhere(['NOT IN','warehouse_header.status' ,['Undo', 'Adjust', 'Receive']]);
        
        // $Query  = \common\models\WarehouseMoving::find()
        //         ->joinwith('header')
        //         ->where(['warehouse_moving.item' => $line->item])
        //         ->andwhere(['IN','warehouse_moving.TypeOfDocument' ,['Sale']])               
        //         ->andwhere(['warehouse_header.order_id' => $line->saleHeader->id])
        //         ->andwhere(['NOT IN','warehouse_header.status' ,['Undo', 'Adjust', 'Receive']]);


        $data = [
            'id'        => 'Pass', 
            'header'    => NULL, 
            'doc'       => NULL, 
            'item'      => $line->item, 
            'des'       => $line->description
        ];

        if($line->quantity_shipped != 0){
            $model =\common\models\WarehouseMoving::find()
                    ->joinwith('header')
                    ->where(['warehouse_moving.SourceDoc' => $source])
                    ->andwhere(['warehouse_header.order_id' => $line->sourcedoc])
                    ->andwhere(['warehouse_moving.TypeOfDocument'  => 'Sale'])
                    ->andWhere(['warehouse_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['warehouse_header.status' => 'Shiped'])
                    ->one();
            if($model != null){            
                $data = [
                    'id'        => $model->id,
                    'header'    => $model->SourceDoc,
                    'doc'       => $model->DocumentNo .': SH' ,
                    'item'      => $model->ItemNo,
                    'des'       => $model->Description,    
                    'type'      => $model->TypeOfDocument,
                    'order_id'  => $line->sourcedoc                     
                ];
            }
        }

        // if($Query->exists()){         
        //     $model  = $Query->one();
        //     $data = [
        //                 'id'        => $model->id,
        //                 'header'    => $model->SourceDoc,
        //                 'doc'       => $model->DocumentNo .': SH' ,
        //                 'item'      => $model->ItemNo,
        //                 'des'       => $model->Description,    
        //                 'type'      => $model->TypeOfDocument                     
        //             ];

        // } 

        return json_encode($data);

    }


    public function actionCreateSaleLine(){
       
        
        $orderid    = $_POST['id'];
        if(isset($_POST['data'])){
         
            foreach ($_POST['data'] as $key => $file) {

                $model      = new  SaleLine();
                $model->sourcedoc = $orderid;
                
                // Get data from Sale Header
                $Header = SaleHeader::findOne($orderid);

                // Sale Line
                $Items              = FunctionSaleOrder::getItemFromBarcode($file['no']);
                if($Items!==null){

                    $model->item        = $Items['id'];
                    $model->item_no     = $Items['No'];
                    $model->order_no    = $Header->no;
                    $model->quantity    = $file['qty'];
                    $model->unit_price  = $file['price'];

                    $model->create_date = date('Y-m-d H:i:s');
                    $model->vat_percent = $Header->vat_percent;
                    $model->user_id     = Yii::$app->user->identity->id;
                    $model->api_key     = Yii::$app->session->getId();
                    $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
                    $model->sourcedoc   = $orderid;
                    $model->unit_measure= $Items['unit_of_measure'];

                    // แยก Vat
                    $model->unit_price_exvat = ($model->unit_price * 100) /107;

                    if(!$model->save()){
                        $status     = 500;
                        $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                        $value[]    = $file['no'];
                    }else {
                        $status     = 200;
                        $message    = 'Success';
                        $value[]    = $file['no'];
                    }

                }else{
                    $status     = 404;
                    $message    = 'No item found';
                    $value[]    = $file['no'];
                }

            }
        }else {
            $status     = 500;
            $message    = 'Error! Data lost';
            $value      = '';
        }
        
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'value'     => [
                'id'        => $orderid,
                'val'       => $value
            ]
        ]);
    }

    public function actionGetSaleline($id){
        $model = SaleLine::findOne(base64_decode($id));
        if($model){
            //$Item = Items::find()->where(['id' => $model->item])->one();
            return json_encode([
                'status'    => 200,
                'message'   => 'done',
                'value'     => [
                    'id'        => $model->id,
                    'item'      => $model->item,
                    'so'        => $model->sourcedoc,
                    'name'      => ($model->description)
                                    ? $model->description 
                                    : $model->items->description_th,
                    'detail'    => $model->items->detail,
                    'code'      => $model->items->master_code,
                    'price'     => number_format($model->unit_price, 2, '.', ''),
                    'qty'       => number_format($model->quantity, 2, '.', ''),
                    'sumline'   => (($model->unit_price * $model->quantity) - (($model->unit_price * $model->quantity) * $model->line_discount/100)) * 1,
                    'discount'  => $model->line_discount * 1
                ]
            ]);
        };
    }


    public function actionMonthlyReport($year = null ,$notnull = false){
        if(!isset($year)) $year = date('Y'); 
        $keys   = 'MonthlyReport&comp'.Yii::$app->session->get('Rules')['comp_id'].'&years:'.$year;
        $data   = Yii::$app->cache->get($keys);
        if($data){
            return $data;
        }else{
        
            $model = ViewMonthlySalesReport::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id'],'Years' => $year])->One();
            $month = [];

            if($notnull=='true'){
                
                    $month['January']     = ($model->January  > 0)?   $model->January : self::getMonthlyReport('January',$year -1);
                    $month['February']    = ($model->February > 0)?   $model->February : self::getMonthlyReport('February',$year -1);
                    $month['March']       = ($model->March    > 0)?   $model->March : self::getMonthlyReport('March',$year -1);
                    $month['April']       = ($model->April    > 0)?   $model->April : self::getMonthlyReport('April',$year -1);
                    $month['May']         = ($model->May      > 0)?   $model->May : self::getMonthlyReport('May',$year -1);
                    $month['June']        = ($model->June     > 0)?   $model->June : self::getMonthlyReport('June',$year -1);
                    $month['July']        = ($model->July     > 0)?   $model->July : self::getMonthlyReport('July',$year -1);
                    $month['August']      = ($model->August   > 0)?   $model->August : self::getMonthlyReport('August',$year -1);
                    $month['September']   = ($model->September> 0)?   $model->September : self::getMonthlyReport('September',$year -1);
                    $month['October']     = ($model->October  > 0)?   $model->October : self::getMonthlyReport('October',$year -1);
                    $month['November']    = ($model->November > 0)?   $model->November : self::getMonthlyReport('November',$year -1);
                    $month['December']    = ($model->December > 0)?   $model->December : self::getMonthlyReport('December',$year -1);
                
            }else{
                
                    $month['January']     = $model->January;
                    $month['February']    = $model->February;
                    $month['March']       = $model->March;
                    $month['April']       = $model->April;
                    $month['May']         = $model->May;
                    $month['June']        = $model->June;
                    $month['July']        = $model->July;
                    $month['August']      = $model->August;
                    $month['September']   = $model->September;
                    $month['October']     = $model->October;
                    $month['November']    = $model->November;
                    $month['December']    = $model->December;
                
            }
            //return json_encode($month);
            Yii::$app->cache->set($keys,json_encode($month),(60 * 60 * 15));

            return Yii::$app->cache->get($keys);
        }
    }

    static function getMonthlyReport($month,$year){
        $model = ViewMonthlySalesReport::findOne(['comp_id' => Yii::$app->session->get('Rules')['comp_id'],'Years' => $year]);
        if($model != null){
            return $model->$month;
        }else{
            return 0;
        }

    } 



    public function actionMonthlyBySales($year = null ,$notnull = false){
        if(!isset($year)) $year = date('Y'); 
        $sale   = Yii::$app->session->get('Rules')['sale_id'];
        $keys   = 'MonthlyBySales&comp'.Yii::$app->session->get('Rules')['comp_id'].'&sale'.$sale.'&years'.$year;
        $data   = Yii::$app->cache->get($keys);
        if($data){
            return $data;
        }else{
            $model  = ViewMonthlyBySales::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id'],'Years' => $year,'sale_id' => $sale])->One();
            $month  = [];

            if($notnull=='true'){
                
                    $month['January']     = ($model->January  > 0)?   $model->January : self::getMonthlyBySales('January',$year -1);
                    $month['February']    = ($model->February > 0)?   $model->February : self::getMonthlyBySales('February',$year -1);
                    $month['March']       = ($model->March    > 0)?   $model->March : self::getMonthlyBySales('March',$year -1);
                    $month['April']       = ($model->April    > 0)?   $model->April : self::getMonthlyBySales('April',$year -1);
                    $month['May']         = ($model->May      > 0)?   $model->May : self::getMonthlyBySales('May',$year -1);
                    $month['June']        = ($model->June     > 0)?   $model->June : self::getMonthlyBySales('June',$year -1);
                    $month['July']        = ($model->July     > 0)?   $model->July : self::getMonthlyBySales('July',$year -1);
                    $month['August']      = ($model->August   > 0)?   $model->August : self::getMonthlyBySales('August',$year -1);
                    $month['September']   = ($model->September> 0)?   $model->September : self::getMonthlyBySales('September',$year -1);
                    $month['October']     = ($model->October  > 0)?   $model->October : self::getMonthlyBySales('October',$year -1);
                    $month['November']    = ($model->November > 0)?   $model->November : self::getMonthlyBySales('November',$year -1);
                    $month['December']    = ($model->December > 0)?   $model->December : self::getMonthlyBySales('December',$year -1);
                
            }else{
                
                    $month['January']     = ($model!=null)? $model->January : 0;
                    $month['February']    = ($model!=null)? $model->February : 0;
                    $month['March']       = ($model!=null)? $model->March : 0;
                    $month['April']       = ($model!=null)? $model->April : 0;
                    $month['May']         = ($model!=null)? $model->May : 0;
                    $month['June']        = ($model!=null)? $model->June : 0;
                    $month['July']        = ($model!=null)? $model->July : 0;
                    $month['August']      = ($model!=null)? $model->August : 0;
                    $month['September']   = ($model!=null)? $model->September : 0;
                    $month['October']     = ($model!=null)? $model->October : 0;
                    $month['November']    = ($model!=null)? $model->November : 0;
                    $month['December']    = ($model!=null)? $model->December : 0;
                
            }
            //return json_encode($month);
            Yii::$app->cache->set($keys,json_encode($month),(60 * 60 * 15));
            return Yii::$app->cache->get($keys);
        }
    }

    static function getMonthlyBySales($month,$year){
        $sale   = Yii::$app->session->get('Rules')['sale_id'];
        $model  = ViewMonthlyBySales::findOne(['comp_id' => Yii::$app->session->get('Rules')['comp_id'],'Years' => $year,'sale_id' => $sale]);

        if($model != null){
            return $model->$month;
        }else{
            return 0;
        }

    } 



    protected function getTotalStatus(){
        return (Object)[
            'notinvoice' => self::orderBalance(['Checking','Shiped'],false),
            'myinvoice' => self::getMyinvoice(),
            'saleTotal' => self::orderBalance(['Checking','Shiped','Invoiced'],true)
        ];
    }

    protected function getMyinvoice(){

        $Rules = SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve');

        $query = ViewRcInvoiceTotal::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['between','DATE(posting_date)',Yii::$app->session->get('workyears').'-01-01',date('Y-m-t', strtotime(Yii::$app->session->get('workyears').'-12-31'))]);


        in_array(Yii::$app->session->get('Rules')['rules_id'],$Rules) 
        ? $query->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
        :null;

        $data = 0;        
        foreach ($query->all() as $key => $model) {
            $data+= $model->totals;
        }
        return $data;
    }

    protected function orderBalance($status,$invoiced){
        
        $query = \common\models\ViewSaleSumTotal::find()
        ->where(['status' => $status])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['between','DATE(order_date)',Yii::$app->session->get('workyears').'-01-01',Yii::$app->session->get('workyears').'-12-31']);

        Yii::$app->session->get('Rules')['rules_id']==3 
        ? $query->andWhere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
        : null;
        
        $total = 0;
        foreach ($query->all() as $key => $model) {
            // Not yet invoice
            // ไม่นับใบงานที่ออกบิลแล้ว
            if(!$invoiced){            
                // ตรวจสอบว่า มีการเปิดบิลหรือยัง
                if(!$model->invoiced){
                    // Not yet invoice
                    // ยังไม่เปิดบิล
                    $total += $model->totals;
                }
            }else{
            // นับใบงานทั้งหมด ทั้งออกบิลแล้ว และยังไม่ออกบิล
                $total += $model->totals;
            }
        }
        return $total;
    }


    public function actionUpdateStatus(){
        $request_body               = file_get_contents('php://input');
        $data                       = json_decode($request_body);    
        $status                     = 200;
        $message                    = Yii::t('common','Success');
        
        $model                      = SaleHeader::findOne($data->id); 

        // ป้องกันการส่งใบงานโดยไม่มีรายการสินค้า 03/07/2020
        if(SaleLine::find()->where(['sourcedoc' => $model->id])->count() <= 0){
            $model->status              = 'Open';
            $model->balance             = 0;
            $model->balance_befor_vat   = 0;
            $model->save();
            return json_encode([
                'status'    => 301,
                'message'   => Yii::t('common','No items yet.')
            ]);
            exit;
        }
        


        if($model->status != $data->status){        
            if($model->allowUpdate->status == true){    
                 
                $model->status              = $data->status;
                $model->update_status       = Yii::$app->user->identity->id;
                $model->update_status_date  = date('Y-m-d H:i:s');
                $model->balance             = $model->sumtotal->total;    

                if($data->status == 'Checking'){
                    // Set new confirm                                    
                    $model->confirm     = '0';                     
                    $model->live        = 1;
                }  

                if($model->save()){
                    $status     = 200;
                }else{
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                }
                
            }else{
                $model->status  = $model->allowUpdate->update;
                $model->save();
        
                $status         = 500;
                $message        = $model->allowUpdate->message;
            }
        }else{
            $status             = 301;
            $message            = Yii::t('common','Not Change');
        }
        

        
        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function actionDelete()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $id             = $data->id;
        $model          = $this->findModel($id);
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = '';
        
        if($model->allowDelete->status == true){
            # Allow Only status Open,Cancel
            # 1. Delete Sale Line
            # 2. Delete Sale Header
            $transaction = Yii::$app->db->beginTransaction();
		    try {                    
                 
                # Delete Sale Line
                SaleLine::deleteAll(['sourcedoc' => $id]);

                # Delete Sale Header
                try{ // Line Notify                                            
                                
                    $bot =  \common\models\LineBot::findOne(6);
                    $msg = "\r\n".'DELETE ID : '.$model->id."\r\n\r\n";
                    $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
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
                    
                    if(Yii::$app->getRequest()->getUserIP()!="::1"){
                        $bot->notify_message($msg);	
                    }
                    
                     
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
                $this->findModel($id)->delete();
                                            
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status     = 501;
                $message    = Yii::t('common','Error');
                $suggestion = Yii::t('common','{:e}',[':e' => $e]);
                //throw $e;
            }

        }else {
            # Error Sale Header Status
            $status     = 501;
            $message    = Yii::t('common','Error {:transac}', [':transac' => $model->allowDelete->table]);
            $suggestion = Yii::t('common','Not allow');
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'suggestion'=> $suggestion,
            'value'     => [
                'id'        => $id,
                'status'    => $model->status
            ],
        ]);
    }

    public function actionPicCustomer(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = '';
        $isNew          = true;

        $model          = SaleHeader::findOne($data->id); 
        $raws           = [];
        if($model != null){
            $isNew = $model->customer_id ? false : true;
            $model->customer_id = $data->customer;
            if(!$model->save()){
                # Error Sale Header Status
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                $suggestion = Yii::t('common','Not allow');
            }

            $raws = [
                'id' => $model->id,
                'cust' => $model->customer_id,
                'vat_percent' => $model->customer->genbus_postinggroup != 4 ? 7 : 0,
                'include_vat' => $model->customer->vatbus_postinggroup == "01" ?  0 : 1 // 0=Vat ใน, 1=Vat นอก
            ];
        }else{
            # Error Sale Header Status
            $status     = 404;
            $message    = Yii::t('common','Not found');
            $suggestion = Yii::t('common','Not allow');

        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'suggestion'=> $suggestion,
            'raws'      => $raws,
            'isNew'     => $isNew         
        ]);

    }

}
