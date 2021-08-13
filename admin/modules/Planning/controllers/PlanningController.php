<?php

namespace admin\modules\Planning\controllers;

use Yii;
use common\models\ItemMystore;
use common\models\Items;
use admin\modules\Planning\models\ItemSearch;
use admin\modules\Planning\models\ReorderSearch;
use admin\models\FunctionCenter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlanningController implements the CRUD actions for ItemMystore model.
 */
class PlanningController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'                            => ['POST'],
                    'stock-invoice-by-customer-ajax'    => ['POST'],
                    'check-server'                      => ['POST'],
                    'safety-stock-ajax'                 => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ItemMystore models.
     * @return mixed
     */
    public function actionIndex()
    {
        //$searchModel = new ItemSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // return $this->render('index', [
        //     'searchModel' => $searchModel,
        //     'dataProvider' => $dataProvider,
        // ]);

        return $this->render('index');
    }

    public function actionList()
    {
        return $this->render('list');
    }

    public function actionListAjax()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];
 

        $query          = \common\models\ReportDesign::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();

        foreach ($query as $key => $model) {

            $raws[] = [
                'id'                => $model->id,
                'item'              => $model->item,
                'img'               => $model->myItems->picture,
                'code'              => $model->myItems->master_code,                 
                'name'              => $model->name ? $model->name : $model->myItems->name,
                'stock'             => $model->items->ProductionBom > 0 
                                        ? $model->myItems->last_possible * 1
                                        : $model->myItems->last_stock * 1,
                'safety_stock'      => $model->myItems->safety_stock * 1,
                'reorder_point'     => $model->myItems->reorder_point * 1,
                'minimum_stock'     => $model->myItems->minimum_stock * 1,
                'priority'          => $model->priority
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }

    public function actionAddToList(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');
        $actions        = '';
        
        $model          = \common\models\ReportDesign::findOne(['item' => $data->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if($model != null){
            // Remove
            $actions        = 0;
            if(!$model->delete()){
                $status         = 500;
            }
        }else{
            // Create     

            $maxPriority    = \common\models\ReportDesign::find()->select('max(priority) as priority')->one();
            $model          = new \common\models\ReportDesign();
            $model->item    = $data->id;
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->user_id = Yii::$app->user->identity->id;
            $model->priority= $maxPriority->priority;

            if(!$model->save()){
                $status         = 500;
                $message        = $model->getErrors();
            }else{
                $actions        = 1;
            }

        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'actions'   => $actions
        ]);

    }

    /**
     * Displays a single ItemMystore model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ItemMystore model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemMystore();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ItemMystore model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ItemMystore model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ItemMystore model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemMystore the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemMystore::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }


    public function actionSafetyStockAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];

        // $query          = ItemMystore::find()->where([
        //                     'comp_id' => Yii::$app->session->get('Rules')['comp_id'],
        //                     'status' => 1,

        //                 ])->limit(50)->all();

        $query          = ItemMystore::find()
                        ->joinWith('items')
                        ->where(['item_mystore.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->andWhere(['item_mystore.status' => 1])
                        ->andWhere(['<>', 'items.id' , 1414])
                        ->andWhere(['or',
                                ['<=', 'items.ProductionBom', 0],
                                ['items.ProductionBom' => 'IS NULL']
                        ])->indexBy('items.id')
                        ->all();

        foreach ($query as $key => $item) {

            $raws[] = [
                'id'                => $item->item,
                'img'               => $item->items->picture,
                'code'              => $item->master_code,
                'name'              => $item->name,
                'stock'             => $item->items->ProductionBom > 0 
                                        ? $item->items->last_possible * 1
                                        : $item->items->last_stock * 1,
                'safety_stock'      => $item->safety_stock * 1,
                'reorder_point'     => $item->reorder_point * 1,
                'minimum_stock'     => $item->minimum_stock * 1
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }


    public function actionUpdateItemFieldAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $status_item    = 200;
        $status_myItem  = 200;
        $message        = Yii::t('common','Success');

        $item           = Items::findOne($data->id);
        $model          = ItemMystore::find()->where(['item' => $data->id])->one();

        if($item != null){
            
            $item->safety_stock    = $data->safety;
            $item->reorder_point   = $data->reorder;
            $item->minimum_stock   = $data->minimum;

            if(!$item->save()){
                $status_item    = 500;
                $status         = 500;
                $message        = $model->getErrors();
            }
        }
        
        if($model != null){
            $model->safety_stock    = $data->safety;
            $model->reorder_point   = $data->reorder;
            $model->minimum_stock   = $data->minimum;

            if(!$model->save()){
                $status_myItem  = 500;
                $status         = 500;
                $message        = $model->getErrors();
            }
        }



        return json_encode([
            'status'    => $status,
            'update'    => [
                'item'  => $status_item,
                'myitem'=> $status_myItem
            ],
            'message'   => $message
        ]);

    }

    public function actionReorder(){
        return $this->render('reorder');
    }

    public function actionSafetyStock(){
        return $this->render('safety-stock');
    }

    public function actionReorderAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $query      = ItemMystore::find()
                    ->joinWith('items')
                    ->where(['item_mystore.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['item_mystore.status' => 1])
                    ->andWhere(['<>', 'items.id' , 1414])
                    ->andWhere(['or',
                            ['<=', 'items.ProductionBom', 0],
                            ['items.ProductionBom' => 'IS NULL']
                    ])
                    ->andWhere(['>', 'item_mystore.safety_stock', 0])
                    ->all();

        $raws = [];

        foreach ($query as $key => $model) {
            if($model->items->qtyAfter <= $model->safety_stock){
                $raws[] = [
                    'id'                => $model->item,
                    'img'               => $model->items->picture,
                    'code'              => $model->master_code,
                    'name'              => $model->name,
                    'bom'               => $model->items->ProductionBom,
                    'stock'             => $model->items->ProductionBom > 0 
                                                ? $model->items->last_possible * 1
                                                : $model->items->last_stock,
                    'safety_stock'      => $model->safety_stock * 1,
                    'reorder_point'     => $model->reorder_point * 1,
                    'minimum_stock'     => $model->minimum_stock * 1
                ];
            }
        }
        
        return json_encode([
            'status'    => 200,
            'raws'      => $raws
        ]);

    }

    public function actionStockInvoiceByCustomer(){
        return $this->render('stock-invoice-by-customer');
    }

    public function actionStockInvoiceByCustomerAjax(){

        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $Years          = isset($data->years) ? $data->years : date('Y') ;
        $Recal          = $data->recal;
         
        $sale_id        = isset($data->saleId) ? $data->saleId : NULL;
        $fdate          = $data->fdate;
        $tdate          = $data->tdate;
        $branch         = $data->branch;
        $getBom         = isset($data->getBom) ? $data->getBom : 0;
        $cust           = $data->cust != '' ? $data->cust : NULL;
        $custList       = $data->custList;
        $customer       = \common\models\Customer::findOne($cust);
        $vat            = isset($data->vat) 
                            ? ($data->vat == 'No'
                                ? '0'
                                : ($data->vat == 'Vat'
                                    ? 7
                                    : NULL))
                            : NULL ;
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $keys           = 'StockInvoiceByCustomer&branch:'.$branch.'&comp:'.$comp.'&fdate:'.$fdate.'&tdate:'.$tdate;
        $calculating    = 'StockCustCalculating:'.$comp.'&user:'.Yii::$app->user->identity->id;
        $countKeys      = 'StockCustCount&fdate:'.$fdate.'&tdate:'.$tdate.'&comp:'.$comp;
        $nextKeys       = 'StockCustNext&fdate:'.$fdate.'&tdate:'.$tdate.'&comp:'.$comp;
        $data           = Yii::$app->cache->get($keys);
        $calculate      = Yii::$app->cache->get($calculating);
 
        
        Yii::$app->cache->delete($calculating); 
        // ถ้ากำลังคำนวณ ให้ return กลับทันที
        if($calculate){           
            $keyCount = Yii::$app->cache->get($countKeys) ? Yii::$app->cache->get($countKeys) : 1;
            return json_encode([
                'status'        => 403,
                "source"        => 'cache',
                'custId'        => $cust,
                'custName'      => $customer != null ? $customer->name : '',
                'message'       => Yii::t('common','Calculating by {:user}',[':user' => Yii::$app->session->get('Rules')['name']]). ' ' . $calculate['date'],
                'process'       => Yii::$app->cache->get('StockCustProcess:stop:'.$comp),
                'calculating'   => $calculate,
                "data"          => $data,
                'percent'       => (Yii::$app->cache->get($nextKeys)['key'] / $keyCount) *100
            ]); 
                
            exit;
        }else {

            $query = \common\models\RcInvoiceHeader::find()//->where(['id' => 8635]);
             ->where(['between','DATE(posting_date)', $fdate, $tdate])
             ->andWhere(['comp_id'      => $comp])
             ->andWhere(['doc_type'     => 'Sale'])    // ไม่เอา CN               
             ->orderBy(['posting_date'  => SORT_DESC]);

             if($sale_id != NULL){
                $query->andWhere(['sale_id'      => $sale_id]);
             }

            if(count($custList) > 0){
                $query->andWhere(['IN', 'cust_no_', $custList]);
            }else{
                if($cust){
                    $query->andWhere(['cust_no_' => $cust]);
                }                
            }
           
            if($vat != NULL){  
                 
                if($vat > 0){ // Vat
                    $query->andWhere(['>', 'vat_percent',0]);
                    
                }else{ // No Vat and NULL
                    $query->andWhere(['<=','vat_percent',0]); 
                   
                }
            }

            $count = $query->count();
            Yii::$app->cache->set($countKeys,$count);

            // Reload ถ้าคลิกที่ คำนวณใหม่
            if($Recal == 1){
                
                Yii::$app->cache->set($calculating, [
                    'date'      => date('Y-m-d H:i:s'),
                    'years'     => $Years, 
                    'company'   => $comp
                ]);

                Yii::$app->cache->delete('StockCustProcess:stop:'.$comp); // ถ้ามีการเคลี่ยร์ ให้เริ่มนับใหม่
                Yii::$app->cache->delete($keys);
                Yii::$app->cache->set('StockCustRefresh&comp:'.$comp,'force');

                $rawData = [];
                foreach ($query->all() as $key => $source) {  
                    Yii::$app->cache->set($nextKeys,['key' => $key + 1, 'id' => $source->id]);                    
                    $Line = \common\models\RcInvoiceLine::find()->where(['source_id' => $source->id])->andWhere(['<>','item',1414])->all();
                    foreach ($Line as $key => $IvLine) {
                        
                        $quantity = $IvLine->quantity * 1;

                        if($getBom==0){

                            $rawData[] = (Object)[
                                "id"        => $IvLine->items->id,
                                'row'       => $IvLine->id,
                                'rowItem'   => $IvLine->items->master_code,
                                'rowItemId' => $IvLine->items->id,
                                'rowItemName'=>$IvLine->code_desc_,
                                'price'     => $IvLine->unit_price,
                                'date'      => date('Y-m-d', strtotime($source->posting_date)),
                                'vat'       => $source->vat_percent,
                                'parent'    => $source->id,
                                'no'        => $source->no_,
                                'code'      => $IvLine->items->master_code,
                                'name'      => $IvLine->items->description_th,
                                'base_unit' => $IvLine->items->quantity_per_unit * 1,
                                "qty"       => $quantity * 1,
                                //'inven'     => $items->qtyAfter,
                                'cost'      => $IvLine->items->StandardCost,
                                'status'    => 200,
                                'message'   => 'Done'
                            ];

                        }else{
                            $rawData[] = self::validateBom($IvLine->items,$quantity,$source, $IvLine ,0);
                        }

                        if(Yii::$app->cache->get('StockCustProcess:stop:'.$comp)){ break; }
                    }
                    if(Yii::$app->cache->get('StockCustProcess:stop:'.$comp)){ break; }
                }

                return json_encode([
                    "source"    => 'api',
                    'custId'    => $cust,
                    'countCust' => count($custList),
                    'custName'  => $customer != null ? $customer->name : '',
                    "data"      => self::validateItemConsumption($rawData,$fdate, $tdate, $keys)
                ]);
            

            }else{
                // Onload and Click years
            
                if(Yii::$app->cache->get($keys)){
                    Yii::$app->cache->delete('StockCustRefresh&comp:'.$comp);
                    return json_encode([
                        "source"    => 'cache',
                        'custId'    => $cust,
                        'countCust' => count($custList),
                        'custName'  => $customer != null ? $customer->name : '',
                        "data"      => Yii::$app->cache->get($keys)
                    ]);        
                    
                }else{
                    Yii::$app->cache->set($calculating, [
                        'date'      => date('Y-m-d H:i:s'),
                        'years'     => $Years, 
                        'company'   => $comp,
                        'source'    => 'api'
                    ]);
                    Yii::$app->cache->set('StockCustRefresh&comp:'.$comp,'force');
        
                    $rawData = [];
                    foreach ($query->all() as $key => $source) {
                        Yii::$app->cache->set($nextKeys,['key' => $key + 1, 'id' => $source->id]);
                        $Line = \common\models\RcInvoiceLine::find()->where(['source_id' => $source->id])->andWhere(['<>','item',1414])->all();
                        foreach ($Line as $key => $IvLine) {
                        
                            $quantity   = $IvLine->quantity * 1;
                            
                            $rawData[] = self::validateBom($IvLine->items,$quantity,$source,$IvLine, 0);
                            if(Yii::$app->cache->get('StockCustProcess:stop:'.$comp)){ break; }
                        }
                        if(Yii::$app->cache->get('StockCustProcess:stop:'.$comp)){ break; }                    
                    }                    
                    return json_encode([
                        "source"    => 'api',
                        'custId'    => $cust,
                        'countCust' => count($custList),
                        'custName'  => $customer != null ? $customer->name : '',
                        "data"      => self::validateItemConsumption($rawData, $fdate, $tdate, $keys)
                    ]);
                }
            }
        }

    }

    protected function validateBom($items, $qty, $header, $RcLine, $loop){
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $data       = [];         
        if($items->ProductionBom != ''){
            // มี BOM            
            if($loop > 10){                
                $data[] = (Object)[
                    "id"        => $items->id,
                    'row'       => $RcLine->id,
                    'rowItem'   => $RcLine->items->master_code,
                    'rowItemId' => $RcLine->items->id,
                    'rowItemName'=>$RcLine->code_desc_,
                    'price'     => $RcLine->unit_price,
                    'date'      => date('Y-m-d', strtotime($header->posting_date)),
                    'vat'       => $header->vat_percent,
                    'parent'    => $header->id,
                    'no'        => $header->no_,
                    'code'      => $items->master_code,
                    'name'      => $items->description_th,
                    'base_unit' => $items->quantity_per_unit * 1,
                    "qty"       => $qty * 1,
                    //'inven'     => $items->qtyAfter,
                    'cost'      => $items->StandardCost,
                    'status'    => 500,
                    'message'   => 'loop ' .$loop
                ];      
                Yii::$app->cache->set('loop',json_encode(['count' => $loop, 'id' => $header->id, 'no' => $header->no_, 'item' => $items->id]));           
            }else{
                $loop++;          
                $BomLine = \common\models\BomLine::find()->where(['bom_no' => $items->ProductionBom])->all();
                foreach ($BomLine as $key => $Line) {
                    $quantity = $Line->quantity * $qty;
                    $data[] = self::validateBom($Line->items,$quantity,$header,$RcLine, $loop);
                    if(Yii::$app->cache->get('StockCustProcess:stop:'.$comp)){ break; }
                }
            }
        }else{
            // ไม่มี BOM
            $data[] = (Object)[
                "id"        => $items->id,
                'row'       => $RcLine->id,
                'rowItem'   => $RcLine->items->master_code,
                'rowItemId' => $RcLine->items->id,
                'rowItemName'=>$RcLine->code_desc_,
                'price'     => $RcLine->unit_price,
                'date'      => date('Y-m-d', strtotime($header->posting_date)),
                'vat'       => $header->vat_percent,
                'parent'    => $header->id,
                'no'        => $header->no_,
                'code'      => $items->master_code,
                'name'      => $items->description_th,
                'base_unit' => $items->quantity_per_unit * 1,
                "qty"       => $qty * 1,
                //'inven'     => $items->qtyAfter,
                'cost'      => $items->StandardCost,
                'status'    => 200,
                'message'   => 'Done'
            ];
        }
        
        return $data;
    }

    protected function validateItemConsumption($rawData, $fdate, $tdate, $keys){
        
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $countKeys  = 'StockCustCount&fdate:'.$fdate.'&tdate:'.$tdate.'&comp:'.$comp;
        $nextKeys   = 'StockCustNext&fdate:'.$fdate.'&tdate:'.$tdate.'&comp:'.$comp;
        $newData    = self::validateItemConsumptionChild($rawData);
        $next       = Yii::$app->cache->get($nextKeys);
        $calc       = Yii::$app->cache->get('StockCustCalculating:'.$comp.'&user:'.Yii::$app->user->identity->id);
        

        //https://thevaluable.dev/php-datetime-create-compare-format/
        $datetime1 = new \DateTime($calc['date']);
        $datetime2 = new \DateTime(date('Y-m-d H:i:s'));
        $interval  = $datetime1->diff($datetime2);
        

        $storeDate  = (Object)[
            'raw'       => $newData,
            'run'       => $calc['date'],
            'timestamp' => date('Y-m-d H:i:s'),
            'caltime'   => $interval->format('%Y-%m-%d %H:%i:%s'),
            'count'     => count($newData),
            'message'   => Yii::$app->cache->get('StockCustProcess:stop:'.$comp)? Yii::t('common','Interrupted') : Yii::t('common','Success'),
            'percent'   => $next ? ($next['key'] /  Yii::$app->cache->get($countKeys)) * 100 : 100
        ];

        // Radis 
        // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
        $cache      = Yii::$app->cache;
    
        //$cache->set($key, $data, 30, $dependency);
        if($cache->set($keys, $storeDate)){
            $cache->delete('StockCustCalculating:'.$comp.'&user:'.Yii::$app->user->identity->id);
            $cache->delete($countKeys);
            $cache->delete($nextKeys);
            $cache->delete('loop');
            return $cache->get($keys);           
        } 
    }

    protected function validateItemConsumptionChild($rawData){
        $data       = [];
        $dataChild  = [];
        foreach ($rawData as $raw) {
            if(is_array($raw)){                
 
                foreach (self::validateItemConsumptionChild($raw) as $key => $model) {
                    $dataChild[] = $model;                    
                }
            }else{               
                $data[] = (Object)[ 
                    "id"        => $raw->id,
                    'row'       => $raw->row,
                    'rowItem'   => $raw->rowItem,
                    'rowItemId' => $raw->rowItemId,
                    'rowItemName'=> $raw->rowItemName,
                    'price'     => $raw->price,
                    'date'      => $raw->date,
                    'vat'       => $raw->vat,
                    'parent'    => $raw->parent,
                    'no'        => $raw->no,
                    'code'      => $raw->code,
                    'name'      => $raw->name,
                    'base_unit' => $raw->base_unit * 1,
                    "qty"       => $raw->qty * 1,
                    //'inven'     => $raw->inven ,
                    'cost'      => $raw->cost,
                    'status'    => $raw->status,
                    'message'   => $raw->message
                ];
            }
         
        }
        
        return array_merge($dataChild,$data);
    }


    public function actionCheckServer(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $Years          = $data->years;
        $Recal          = $data->recal;
        $fdate          = $data->fdate;
        $tdate          = $data->tdate;
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $cal            = Yii::$app->cache->get('StockCustCalculating:'.$comp.'&user:'.Yii::$app->user->identity->id);
        $countKeys      = 'StockCustCount&fdate:'.$fdate.'&tdate:'.$tdate.'&comp:'.$comp;
        $nextKeys       = 'StockCustNext&fdate:'.$fdate.'&tdate:'.$tdate.'&comp:'.$comp;
        // ถ้ากำลังคำนวณอยู่ 
        // ให้ return data
        if($cal){
            // ถ้า 100%
            // - ยกเลิกการคำนวณ
            // - ลบตัวเลขถัดไป
            // - ลบจำนวนที่นับได้
            if(Yii::$app->cache->get($nextKeys)['key'] == Yii::$app->cache->get($countKeys)){
                Yii::$app->cache->delete('StockCustCalculating:'.$comp.'&user:'.Yii::$app->user->identity->id);
                //Yii::$app->cache->delete($countKeys);
                //Yii::$app->cache->delete($nextKeys);
                return json_encode([
                    'status'    => 200,
                    'message'   => 'Success',
                    'refresh'   => Yii::$app->cache->get('refresh&comp:'.$comp),
                    'percent'   => 100,
                    'loop'      => Yii::$app->cache->get('loop'),
                    'data'      => $cal   
                ]);
            }else{
                return json_encode([
                    'status'    => Yii::$app->cache->get('StockCustProcess:stop:'.$comp) ? 200 : 403,
                    'count'     => Yii::$app->cache->get($countKeys),
                    'next'      => Yii::$app->cache->get($nextKeys),
                    'percent'   => round((Yii::$app->cache->get($nextKeys)['key'] /  Yii::$app->cache->get($countKeys))* 100, 3) ,
                    'loop'      => Yii::$app->cache->get('loop'),
                    'data'      => $cal                
                ]);
            }
        }else{
            return json_encode([
                'status'    => 200,
                'message'   => Yii::$app->cache->get('StockCustProcess:stop:'.$comp)? 'Stop by humans.' :'Success',
                'refresh'   => Yii::$app->cache->get('refresh&comp:'.$comp),
                'percent'   => 100,
                'loop'      => Yii::$app->cache->get('loop'),
                'data'      => ['years' => $data->years]
            ]);
        }
    } 


    


    public function actionStopProcess(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        if($data->status == 'clear'){
            Yii::$app->cache->set('StockCustProcess:stop:'.$comp,true);
            Yii::$app->cache->delete('StockCustCalculating:'.$comp.'&user:'.Yii::$app->user->identity->id);
            return json_encode([
                'status' => 200,
                'message' => 'clear'
            ]);
        }else{
            Yii::$app->cache->delete('StockCustProcess:stop:'.$comp);
            return json_encode([
                'status' => 200,
                'message' => 'continue'
            ]);
        }
        
         
    }

    public function actionChangePriority(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status = 200;
        $message= Yii::t('common','Success');
        $raws   = [];

        foreach ($data->raws as $key => $value) {
            $model = \common\models\ReportDesign::findOne($value->id);
            if($model != null){
                $model->priority = ($key + 1);
                $model->save();
                $raws[] = [
                    'id' => $model->id,
                    'priority' => $model->priority
                ];
            }
        }

        return json_encode(['raws' => $raws]);

    }

}
