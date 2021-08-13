<?php

namespace admin\modules\items\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use admin\modules\items\models\InStockSearch;
use common\models\ItemgroupCommon;
use common\models\ItemsHasGroups;
use admin\models\FunctionCenter;
use common\models\WarehouseMoving;
use common\models\ItemForCompany;
use common\models\Items;

class StockController extends \yii\web\Controller
{
    public $tdate;
    public $fdate;

    public function behaviors()
    {
        $fn = new FunctionCenter();
        $fn->RegisterRule();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index-monthly-ajax'    => ['POST'],
                    'stock-by-invoice-ajax' => ['POST'],
                    'check-server'          => ['POST'],
                    'my-stock'              => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if(Yii::$app->user->identity->id != 1){
            $session = \Yii::$app->session;
            $session->set('workdate', date('Y-m-d'));
        }

        $models = ItemgroupCommon::find()
        ->where([
            'child'         => 0,
            'group_for'     => 'inv',
            'comp_id'       => Yii::$app->session->get('Rules')['comp_id']
        ])->orderBy(['sequent' => SORT_ASC]) ->all();

        $group = [];
        foreach ($models as $key => $model) {
            $group[] = (Object)[
                'id' => $model->id,
                'name' => $model->name
            ];
        }

        return $this->render('index', [
            'group' => $group
        ]);
    }
    
    public function actionDatatable()
    {
        $searchModel = new InStockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=120;

        if(Yii::$app->user->identity->id!=1){
            $dataProvider->query->rightJoin('item_mystore',"item_mystore.item=items_has_groups.item_id AND item_mystore.comp_id='".Yii::$app->session->get('Rules')['comp_id']."'");
        }
       
        return $this->render('datatable', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexMonthly()
    {
        return $this->render('index-monthly');
    }

    protected function findParentGroup($id){
        $model = ItemgroupCommon::findOne($id);

        if($model->child == 0){
            return $model->id;
        }else{
            return self::findParentGroup($model->child);
        }
        
    }

    public function actionIndexMonthlyAjax()
    {
        $models = ItemgroupCommon::find()
        ->where([
            'child'     => 0,
            'group_for'  => 'inv',
            'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
        ])->orderBy(['sequent' => SORT_ASC])
        ->all();
    

        $group = [];
        foreach ($models as $key => $model) {
            $group[] = (Object)[
                'id' => $model->id,
                'name' => $model->name
            ];
        }
       
        return $this->renderpartial('_index-monthly', [
            'group' => $group,
            'recal' => Yii::$app->request->post('recal')
        ]);
    }

    public function actionMonthly(){
        return $this->render('monthly');
    }

    public function actionMonthlyAjax(){
        $models = ItemgroupCommon::find()
        ->where([
            'child'     => 0,
            'group_for'  => 'inv',
            'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
        ])->orderBy(['sequent' => SORT_ASC])
        ->all();

        $group = [];
        foreach ($models as $key => $model) {
            $group[] = (Object)[
                'id' => $model->id,
                'name' => $model->name
            ];
        }
       
        return $this->renderpartial('_monthly', [
            'group' => $group,
            'recal' => Yii::$app->request->post('recal')
        ]);

    }


    public function actionStockByInvoice(){
        return $this->render('stock-by-invoice');
    }

    public function actionStockByInvoiceAjax(){
        ini_set('max_execution_time', 0); // 0 = Unlimited

        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $message        = '';
        $status         = 200;

        $Years          = $data->years;
        $Recal          = $data->recal;
        $quarter        = $data->quarter;

        if(isset($data->fdate)){
            $fdate = date('Y-m-d',strtotime($data->fdate));
            $tdate = date('Y-m-d',strtotime($data->tdate));
        }else{
            $fdate = date('Y-m-d');
            $tdate = date('Y-m-t');
        }

        $this->tdate   = date('y_m_d',strtotime($tdate));
        $this->fdate   = date('y_m_d',strtotime($fdate));

        Yii::$app->session->set('fdate',$fdate);
        Yii::$app->session->set('tdate',$tdate);

        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $keys           = 'StockByInvoiceAjax&years:'.$Years.'&comp:'.$comp.'&quarter:'.$quarter;
        $calculating    = 'calculating:'.$comp;
        $countKeys      = 'invoiceCount&years:'.$Years.'&comp:'.$comp.'&quarter:'.$quarter;
        $nextKeys       = 'invoiceNext&years:'.$Years.'&comp:'.$comp.'&quarter:'.$quarter;        
        $data           = Yii::$app->cache->get($keys);        
        $calc           = Yii::$app->cache->get($calculating);
        $jsonfile       = Yii::getAlias('@webroot/assets/tmp_stock_by_invoice_'.$comp.'_'.$this->tdate.'_'.$this->fdate.'_'.$Years.'.json');

        if (!file_exists($jsonfile)){
            $rawZero    = json_encode((Object)[ 'raw' => [] ]);                    
            $fRaw         = fopen($jsonfile, 'w+');
            fwrite($fRaw, $rawZero);
            fclose($fRaw);
        }
 
        // if($quarter == 'first'){
        //     $fdate  =  date('Y-m-d', strtotime($Years.'-01-01'));
        //     $tdate  =  date('Y-m-d', strtotime($Years.'-06-30'));
        // }else if($quarter == 'last'){
        //     $fdate  =  date('Y-m-d', strtotime($Years.'-07-01'));
        //     $tdate  =  date('Y-m-d', strtotime($Years.'-12-31'));
        // }else {
        //     $fdate  =  date('Y-m-d', strtotime($Years.'-01-01'));
        //     $tdate  =  date('Y-m-d', strtotime($Years.'-12-31'));
        // }

        
        

        // ถ้ากำลังคำนวณ ให้ return กลับทันที
        if($calc){           
            return json_encode([
                'status'    => 403,
                "source"    => 'cache',
                'message'   => Yii::t('common','Calculating by {:user}',[':user' =>Yii::$app->cache->get('call-by')]). ' ' . $calc['date'],
                'process'   => Yii::$app->cache->get('process:stop:'.$comp),
                'calculating'=> $calc,
                //"data"      => $data,
                'data'      => json_decode(file_get_contents($jsonfile)),
                'percent'   => (Yii::$app->cache->get($nextKeys)['key'] / Yii::$app->cache->get($countKeys)) *100
            ]);      
            exit;
        }else {


            

           
            $query = \common\models\RcInvoiceHeader::find()//->where(['id' => 8635]);
             ->where(['between','DATE(posting_date)', $fdate, $tdate])
             ->andWhere(['comp_id' => $comp])        
             ->andWhere(['doc_type' => 'Sale'])    // ไม่เอา CN
             ->andWhere(['revenue' => 0]) // เอาเฉพาะที่คิดเป็นเงินจริง  (ไม่รวมบิลยกเลิก CANCELED)
             //->andWhere(['no_' => 'CT7006006'])
             ->orderBy(['posting_date' => SORT_DESC]);

            $count = $query->count();        
            Yii::$app->cache->set($countKeys,$count);
            
            // ถ้าคลิกที่ คำนวณใหม่
            if($Recal == 1){
                Yii::$app->cache->set('call-by', Yii::$app->session->get('Rules')['name'].' :: '.date('Y-m-d H:i:s').' Host:'.gethostname());
                Yii::$app->cache->set($calculating, [
                    'date'      => date('Y-m-d H:i:s'),
                    'years'     => $Years, 
                    'company'   => $comp
                ]);

                Yii::$app->cache->delete('process:stop:'.$comp); // ถ้ามีการเคลี่ยร์ ให้เริ่มนับใหม่
                Yii::$app->cache->delete($keys);
                

                //Clear data IN JSON file ()
                try{                            
                                
                    $productjson    = json_encode((Object)[ 'raw' => [] ]);                    
                    $fp             = fopen($jsonfile, 'w+');
                    fwrite($fp, $productjson);
                    fclose($fp);

                } catch (\Exception $e) {					 
                     // Line Notify Error					 
                    $bot =  \common\models\LineBot::findOne(5);
                    $msg = 'CALCULATE ITEM SALES'."\r\n";                                         
                    $msg.= 'Key Name : '.$keys."\r\n";                                                     
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";
                    $msg.= "Error : ".Yii::t('common','{:e}',[':e' => $e]);
                    
                    $bot->notify_message($msg);	
                }	

                $rawData        = [];
                $InvoiceHeader  = $query->all();
                $HeaderList     = [];
                $saleLine       = [];

                foreach ($InvoiceHeader as $source) {
                    Yii::$app->cache->set('IV-COMP:'.$comp, $source->id);
                    $HeaderList[] = (Object)[
                        'id'            => $source->id,
                        'no_'           => $source->no_,
                        'posting_date'  => $source->posting_date,
                        'vat_percent'   => $source->vat_percent
                    ];
                }

                foreach ($HeaderList as $key => $source) {
                        
                    $Line = \common\models\RcInvoiceLine::find()->where(['source_id' => $source->id])->andWhere(['<>','item',1414])->all();
                    foreach ($Line as $IvLine) {                        
                        
                        $quantity       = $IvLine->quantity * 1;
                        $items          = $IvLine->items;

                        $saleLine[]     = (Object)[
                            'id'        => $IvLine->id,
                            'key'       => $key + 1,
                            'items'     => (Object)[
                                'id'                => $IvLine->item,
                                'hasbom'            => $items->hasbom
                                                        ? (Object)[
                                                                'id' =>  $items->hasbom->id
                                                            ]
                                                        : NULL, 
                                'master_code'       => $items->master_code,
                                'description_th'    => $items->description_th,
                                'quantity_per_unit' => $items->quantity_per_unit,
                                'StandardCost'      => $items->myItems ? $items->myItems->StandardCost : 0,
                                'itemHasGroups'     => $items->itemHasGroups
                                                            ? ($items->itemHasGroups->groups
                                                                ? (Object)[
                                                                    'id'        => $items->itemHasGroups->groups->id,
                                                                    'topParent' => $items->itemHasGroups->groups->topParent,
                                                                    'name'      => $items->itemHasGroups->groups->name
                                                                ]
                                                                : (Object)[
                                                                    'id'        => 0,
                                                                    'topParent' => NULL,
                                                                    'name'      => NULL
                                                                ])
                                                            :  (Object)[
                                                                'id'        => 0,
                                                                'topParent' => NULL,
                                                                'name'      => NULL
                                                            ],

                                ],
                            'code_desc_'=> $IvLine->code_desc_,
                            'qty'       => $quantity,
                            'source'    => (Object)[
                                'id'            => $source->id,
                                'no_'           => $source->no_,
                                'posting_date'  => $source->posting_date,
                                'vat_percent'   => $source->vat_percent
                            ],
                            'buffer'    => self::findAlreadyItem($rawData, $IvLine->item)
                        ];
                            // ยกเลิกการหัก CN เอง 14/12/2019
                            // $quantity = $IvLine->rcInvoiceHeader->doc_type == 'Sale' 
                            //                     ? ($IvLine->quantity * 1) 
                            //                     : ($IvLine->quantity * -1);

                            // $quantity   = $IvLine->quantity * 1;
                            // $buffer     = self::findAlreadyItem($rawData, $IvLine->items->id);
                            // $rawData[]  = self::validateBom($IvLine->items,$quantity,$source, $IvLine , 0, $buffer);
                        
                        
                    }
                    
                }



                foreach ($saleLine as $IvLine) {
                
                    Yii::$app->cache->set($nextKeys,['key' => $IvLine->key, 'id' => $IvLine->source->id]); 
                    $rawData[]  = self::validateBom($IvLine->items, $IvLine->qty, $IvLine->source, $IvLine , 0, $IvLine->buffer);
                
                }


                try{ // Line Notify                                            
                                
                    $bot =  \common\models\LineBot::findOne(5);
                    $msg = 'CALCULATE ITEM SALES'."\r\n";                                         
                    $msg.= 'Key Name : '.$keys."\r\n";                                                     
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                     
                    //$bot->notify_message($msg);					

                } catch (\Exception $e) {					 
                    // $status 		= 500;
                    // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                }	

                $newData    = self::validateItemConsumption($rawData, $Years, $keys);

                


                return json_encode([
                    "source"    => 'api',
                    "data"      => $newData
                ]);

                exit;

            }else{

                // Onload             
                $fp             = file_get_contents($jsonfile);
                $dataBuffer     = json_decode($fp);
                //$dataBuffer = Yii::$app->cache->get($keys) ? Yii::$app->cache->get($keys) : (Object)[ 'raw' => [] ];
                $countRaw   = count($dataBuffer->raw);            
                if($countRaw > 0){                    
                    Yii::$app->cache->delete('refresh&comp:'.$comp);                  
                    return json_encode([
                        "source"    => 'cache',
                        "data"      => $dataBuffer,
                        'keys'      => $keys,
                        'countRaw'  => $countRaw
                    ]);
                    exit;
                }else{
                    Yii::$app->cache->set('call-by', Yii::$app->session->get('Rules')['name'].' :: '.date('Y-m-d H:i:s').' Host:'.gethostname());
                    Yii::$app->cache->set($calculating, [
                        'date'      => date('Y-m-d H:i:s'),
                        'years'     => $Years, 
                        'company'   => $comp,
                        'source'    => 'api'
                    ]);

                    Yii::$app->cache->delete('process:stop:'.$comp); // ถ้ามีการเคลี่ยร์ ให้เริ่มนับใหม่
                    Yii::$app->cache->delete($keys);  

                    //delete JSON file
                    try{                            
                                    
                        $productjson    = json_encode((Object)[ 'raw' => [] ]);
                        $fp             = fopen($jsonfile, 'w+');
                        fwrite($fp, $productjson);
                        fclose($fp);

                    } catch (\Exception $e) {					 
                         // Line Notify Error					 
                        $bot =  \common\models\LineBot::findOne(5);
                        $msg = 'CALCULATE ITEM SALES'."\r\n";                                         
                        $msg.= 'Key Name : '.$keys."\r\n";                                                     
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";
                        $msg.= "Error : ".Yii::t('common','{:e}',[':e' => $e]);
                        
                        $bot->notify_message($msg);	
                    }	           
        
                    $rawData = [];
                    $InvoiceHeader = $query->all();
                    $saleLine = [];
                    foreach ($InvoiceHeader as $key => $source) {
                        if(Yii::$app->cache->get('process:stop:'.$comp)){ 
                            break; 
                        }else{

                            
                            $Line = \common\models\RcInvoiceLine::find()->where(['source_id' => $source->id])->andWhere(['<>','item',1414])->all();
                            foreach ($Line as $IvLine) {
                                if(Yii::$app->cache->get('process:stop:'.$comp)){ 
                                    break; 
                                }else{

                                    $quantity   = $IvLine->quantity * 1;
                                    $saleLine[] = (Object)[
                                        'id'    => $IvLine->id,
                                        'key'   => $key + 1,
                                        'items'     => (Object)[
                                            'id'                => $IvLine->item,
                                            'hasbom'            => $IvLine->items->hasbom
                                                                    ? (Object)[
                                                                            'id' =>  $IvLine->items->hasbom->id
                                                                        ]
                                                                    : NULL, 
                                            'master_code'       => $IvLine->items->master_code,
                                            'description_th'    => $IvLine->items->description_th,
                                            'quantity_per_unit' => $IvLine->items->quantity_per_unit,
                                            'StandardCost'      => $IvLine->items->myItems ? $IvLine->items->myItems->StandardCost : 0,
                                            'itemHasGroups'     => $IvLine->items->itemHasGroups
                                                                        ? ($IvLine->items->itemHasGroups->groups
                                                                            ? (Object)[
                                                                                'id'        => $IvLine->items->itemHasGroups->groups->id,
                                                                                'topParent' => $IvLine->items->itemHasGroups->groups->topParent,
                                                                                'name'      => $IvLine->items->itemHasGroups->groups->name
                                                                            ]
                                                                            : (Object)[
                                                                                'id'        => 0,
                                                                                'topParent' => NULL,
                                                                                'name'      => NULL
                                                                            ])
                                                                        :  (Object)[
                                                                            'id'        => 0,
                                                                            'topParent' => NULL,
                                                                            'name'      => NULL
                                                                        ],
            
                                        ],
                                        'code_desc_' => $IvLine->code_desc_,
                                        'qty'   => $quantity,
                                        'source' => (Object)[
                                            'id'            => $source->id,
                                            'no_'           => $source->no_,
                                            'posting_date'  => $source->posting_date,
                                            'vat_percent'   => $source->vat_percent
                                        ],
                                        'buffer'    => self::findAlreadyItem($rawData, $IvLine->items->id)
                                    ];
                                }
                                // ยกเลิกการหัก CN เอง 14/12/2019
                                // $quantity = $IvLine->rcInvoiceHeader->doc_type == 'Sale' 
                                //                 ? ($IvLine->quantity * 1) 
                                //                 : ($IvLine->quantity * -1);
                                // $quantity   = $IvLine->quantity * 1;
                                // $buffer     = self::findAlreadyItem($rawData, $IvLine->items->id);
                                // $rawData[]  = self::validateBom($IvLine->items,$quantity,$source,$IvLine, 0, $buffer);
                                
                            }
                        }
                                           
                    } 
                    
                    //try{
                    
                        foreach ($saleLine as $key => $IvLine) {
                            if(Yii::$app->cache->get('process:stop:'.$comp)){ 
                                break; 
                            }else{
                                Yii::$app->cache->set($nextKeys,['key' => $IvLine->key, 'id' => $IvLine->source->id]); 
                                //$buffer     = self::findAlreadyItem($rawData, $IvLine->items->id);
                                $rawData[]  = self::validateBom($IvLine->items, $IvLine->qty, $IvLine->source, $IvLine , 0, $IvLine->buffer);
                            }
                        }

                        $newData = self::validateItemConsumption($rawData, $Years, $keys);
                        
                       
                    //    $status 		= 200;
                    // } catch (\Exception $e) {					 
                    //     $status 		= 500;
                    //     $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                    //     Yii::$app->session->set('message',Yii::t('common','{:e}',[':e' => $e]));
                        
                        
                    // }	

                    return json_encode([
                        "source"    => 'api',
                        "data"      => $newData,
                        'status'    => $status,
                        'message'   => $message
                    ]);
                   
                }
            }
        }

    }

    protected static function validateBom($items, $qty, $header, $RcLine, $loop, $buffer){
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $status     = 200;
        $message    = '';
        $data       = [];     
        

        //try{  
        
        
            if($items->hasbom){
                // มี BOM            
                if($loop > 3){      
                    $loop++;              
                    $data[] = (Object)[
                        "id"        => $buffer
                                        ? $buffer->id
                                        : $items->id,
                        'row'       => $RcLine->id,
                        'rowItem'   => $buffer
                                        ? $buffer->rowItem
                                        : $RcLine->items->master_code,
                        'rowItemId' => $buffer
                                        ? $buffer->rowItemId
                                        : $RcLine->items->id,
                        'rowItemName'=> $buffer
                                        ? $buffer->rowItemName
                                        : $RcLine->code_desc_,
                        'date'      => date('Y-m-d', strtotime($header->posting_date)),
                        'vat'       => $header->vat_percent,
                        'parent'    => $header->id,
                        'no'        => $header->no_,
                        'code'      => $buffer
                                        ? $buffer->code
                                        : $items->master_code,
                        'name'      => $buffer
                                        ? $buffer->name
                                        : $items->description_th,
                        'base_unit' => $buffer
                                        ? $buffer->base_unit
                                        : $items->quantity_per_unit * 1,
                        "qty"       => $qty * 1,
                        //'inven'     => $items->inven,
                        'cost'      => $buffer
                                        ? $buffer->cost
                                        : $items->myItems ? $items->myItems->StandardCost : 0,
                        'status'    => 200,
                        'message'   => 'Done',
                        'groupMain' => $buffer 
                                        ? $buffer->groupMain
                                        : ($items->itemHasGroups 
                                            ? ($items->itemHasGroups->groups 
                                                ? $items->itemHasGroups->groups->topParent
                                                : '') 
                                            : ''),
                        'group'     => $buffer
                                        ? $buffer->group
                                        : $items->itemHasGroups ? $items->itemHasGroups->name : NULL,
                        'groupId'   => $buffer
                                        ? $buffer->groupId
                                        : $items->itemHasGroups ? $items->itemHasGroups->id : 0,
                    ];  
                    
                    try{ // Line Notify                                            
                                    
                        $bot =  \common\models\LineBot::findOne(5);
                        $msg = 'ERROR LOOP'."\r\n";                                         
                        $msg.= 'Loop : '.$loop."\r\n";  
                        //$msg.= json_encode(['count' => $loop, 'id' => $header->id, 'no' => $header->no_, 'item' => $items->id]);                                         
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                        
                        $bot->notify_message($msg);					

                    } catch (\Exception $e) {					 
                        // $status 		= 500;
                        // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                    }	

                    //Yii::$app->cache->set('loop',json_encode(['count' => $loop, 'id' => $header->id, 'no' => $header->no_, 'item' => $items->id]));           
                }else{
                    $loop++;          
                    $BomLine    = \common\models\BomLine::find()->where(['bom_no' => $items->hasbom->id])->all();
                    $ItemInBom  = [];

                    foreach ($BomLine as $Line) {
                        if(Yii::$app->cache->get('process:stop:'.$comp)){ 
                            break; 
                        }else{                 
                            
                            $itmes       = $Line->items;

                            $ItemInBom[] = (Object)[
                                'id'        => $Line->id,
                                'items'     => (Object)[
                                    'id'                => $Line->item,
                                    'hasbom'            => $itmes->hasbom
                                                        ? (Object)[
                                                                'id' =>  $itmes->hasbom->id
                                                            ]
                                                        : NULL, 
                                    'master_code'       => $itmes->master_code,
                                    'description_th'    => $itmes->description_th,
                                    'quantity_per_unit' => $itmes->quantity_per_unit,
                                    'StandardCost'      => $itmes->StandardCost,
                                    'itemHasGroups'     => $itmes->itemHasGroups
                                                            ? ($itmes->itemHasGroups->groups
                                                                ? (Object)[
                                                                    'id'        => $itmes->itemHasGroups->groups->id,
                                                                    'topParent' => $itmes->itemHasGroups->groups->topParent,
                                                                    'name'      => $itmes->itemHasGroups->groups->name
                                                                ]
                                                                : (Object)[
                                                                    'id'        => 0,
                                                                    'topParent' => NULL,
                                                                    'name'      => NULL
                                                                ])
                                                            : NULL

                                ],
                                'quantity'  => ($Line->quantity * $qty),
                                'buffers'   => self::findAlreadyItem($buffer, $Line->items->id)
                            ];
                        }
                    }
                     
                    foreach ($ItemInBom as $Line) {
                        $data[]     = self::validateBom($Line->items,$Line->quantity,$header,$RcLine, $loop, $Line->buffers);
                    }
                }
            }else{
                // ไม่มี BOM
            
                $data[]     = (Object)[
                    "id"        => $buffer
                                    ? $buffer->id
                                    : $items->id,
                    'row'       => $RcLine->id,
                    'rowItem'   => $buffer
                                    ? $buffer->rowItem
                                    : $RcLine->items->master_code,
                    'rowItemId' => $buffer
                                    ? $buffer->rowItemId
                                    : $RcLine->items->id,
                    'rowItemName'=> $buffer
                                    ? $buffer->rowItemName
                                    : $RcLine->code_desc_,
                    'date'      => date('Y-m-d', strtotime($header->posting_date)),
                    'vat'       => $header->vat_percent,
                    'parent'    => $header->id,
                    'no'        => $header->no_,
                    'code'      => $buffer
                                    ? $buffer->code
                                    : $items->master_code,
                    'name'      => $buffer
                                    ? $buffer->name
                                    : $items->description_th,
                    'base_unit' => $buffer
                                    ? $buffer->base_unit
                                    : $items->quantity_per_unit * 1,
                    "qty"       => $qty * 1,
                    //'inven'     => $items->inven,
                    // 'inven'      => $buffer
                    //                 ? $buffer->inven
                    //                 : $items->inven,
                    'cost'      => $buffer
                                    ? $buffer->cost
                                    : $items->StandardCost,
                    'status'    => 200,
                    'message'   => 'Done',
                    'groupMain' => $buffer 
                                        ? $buffer->groupMain
                                        : $items->itemHasGroups ? $items->itemHasGroups->topParent : NULL,
                    'group'     => $buffer
                                    ? $buffer->group
                                    : $items->itemHasGroups ? $items->itemHasGroups->name : NULL,
                    'groupId'   => $buffer
                                    ? $buffer->groupId
                                    : $items->itemHasGroups ? $items->itemHasGroups->id : 0,
                    'temp'      => $buffer ? true : false
                ];
            }

           
        // } catch (\Exception $e) {					 
        //     // $status 		= 500;
        //     // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
        //     Yii::$app->session->set('message',Yii::t('common','{:e}',[':e' => $e]));	
 
        // }	

        
        
        return $data;
    }

    protected static function findAlreadyItem($data,$item){
        $newData = (Object)$data;
        $raws = [];
        if($newData != null){
            foreach ($newData as $key => $model) {
                if(isset($model[$key]->id)){
                    if($model[$key]->id == $item){
                        $raws = (Object)$model[$key];
                        break;
                    }
                }
            }
        }

        return $raws;
    }

    protected static function validateItemConsumptionChild($rawData){
        $data       = [];
        $dataChild  = [];
        $merge      = [];
        $comp       = Yii::$app->session->get('Rules')['comp_id'];

        //try{

            foreach ($rawData as $raw) {
            
                if(is_array($raw)){
                    foreach (self::validateItemConsumptionChild($raw) as $model) {
                        
                         $data[] = $model;
                         //var_dump($model);
                    }
                }else{
                    $data[] = (Object)[
                        "id"        => $raw->id,
                        'row'       => $raw->row,
                        'rowItem'   => $raw->rowItem,
                        'rowItemId' => $raw->rowItemId,
                        'rowItemName'=> $raw->rowItemName,
                        'date'      => $raw->date,
                        'vat'       => $raw->vat,
                        'parent'    => $raw->parent,
                        'no'        => $raw->no,
                        'code'      => $raw->code,
                        'name'      => $raw->name,
                        'base_unit' => $raw->base_unit * 1,
                        "qty"       => $raw->qty * 1,
                        //'inven'     => $raw->inven ,
                        'cost'      => $raw->cost *1,
                        'status'    => $raw->status,
                        'message'   => $raw->message,
                        'groupMain' => $raw->groupMain,
                        'group'     => $raw->group,
                        'groupId'   => $raw->groupId,
                        'temp'      => $raw->temp
                    ];
                }
               
            }

            return $data;

            //$merge = array_merge($dataChild,$data); // ทับที่เหมือนกัน
            //return $merge;

            // var_dump($dataChild);
            // echo '<br /><br />';
            // var_dump($data);
            // echo '<br /><br />';
            // var_dump($merge);
            //  exit;
            //$merge = array_push($dataChild,$data);
        // } catch (\Exception $e) {					 
        //     // $status 		= 500;
        //     // $message 		= Yii::t('common','{:e}',[':e' => $e]);
        //     Yii::$app->session->set('message',Yii::t('common','{:e}',[':e' => $e]));	
        // }	
        
        
    }

    protected  function validateItemConsumption($rawData, $Years, $keys){
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $countKeys  = 'invoiceCount&years:'.$Years.'&comp:'.$comp.'&quarter:all';
        $nextKeys   = 'invoiceNext&years:'.$Years.'&comp:'.$comp.'&quarter:all';
        $next       = Yii::$app->cache->get($nextKeys);
        $calc       = Yii::$app->cache->get('calculating:'.$comp);

        //https://thevaluable.dev/php-datetime-create-compare-format/
        $datetime1 = new \DateTime($calc['date']);
        $datetime2 = new \DateTime(date('Y-m-d H:i:s'));
        $interval  = $datetime1->diff($datetime2);
        
        
        //try{
        
            $newData    = self::validateItemConsumptionChild($rawData);

            $storeDate  = (Object)[
                'raw'       => $newData,
                'run'       => $calc['date'],
                'timestamp' => date('Y-m-d H:i:s'),
                'caltime'   => $interval->format('%Y-%m-%d %H:%i:%s'),
                'count'     => count($newData),
                'message'   => Yii::$app->cache->get('process:stop:'.$comp)? (Yii::t('common','Interrupted').' '.Yii::$app->cache->get('process:stop:'.$comp)) : Yii::t('common','Success'),
                'percent'   => $next ? ($next['key'] /  Yii::$app->cache->get($countKeys)) * 100 : 100
            ];

            // Radis 
            // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
            $cache      = Yii::$app->cache;

        // } catch (\Exception $e) {					 
        //     // $status 		= 500;
        //     // $message 		= Yii::t('common','{:e}',[':e' => $e]);
        //     Yii::$app->session->set('message',Yii::t('common','{:e}',[':e' => $e]));	
        // }
    
        //$cache->set($key, $data, 30, $dependency);
        try{                                         
                                
            $productjson    = json_encode($storeDate, JSON_UNESCAPED_UNICODE);
            $jsonfile       = Yii::getAlias('@webroot/assets/tmp_stock_by_invoice_'.$comp.'_'.$this->tdate.'_'.$this->fdate.'_'.$Years.'.json');
            $fp             = fopen($jsonfile, 'w+');
            fwrite($fp, $productjson);
            fclose($fp);


            $cache->delete('calculating:'.$comp);
            $cache->delete($countKeys);
            $cache->delete($nextKeys);
            $cache->delete('loop');
            $cache->delete('process:stop:'.$comp);
            Yii::$app->cache->set('refresh&comp:'.$comp,'force');
           

        } catch (\Exception $e) {
            // Line Notify Error					 
            $bot =  \common\models\LineBot::findOne(5);
            $msg = 'CALCULATE ITEM SALES'."\r\n";                                         
            $msg.= 'Key Name : '.$keys."\r\n";                                                     
            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";
            $msg.= "Error : ".Yii::t('common','{:e}',[':e' => $e]);
             
            $bot->notify_message($msg);		
        }	

        return $storeDate;   

        // if($cache->set($keys, $storeDate)){
        //     $cache->delete('calculating:'.$comp);
        //     $cache->delete($countKeys);
        //     $cache->delete($nextKeys);
        //     $cache->delete('loop');
        //     $cache->delete('process:stop:'.$comp);
        //     return $cache->get($keys);           
        // } 
    }



    public function actionCheckServer(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $Years          = $data->years;
        $Recal          = $data->recal;
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $cal            = Yii::$app->cache->get('calculating:'.$comp);
        $countKeys      = 'invoiceCount&years:'.$Years.'&comp:'.$comp.'&quarter:all';
        $nextKeys       = 'invoiceNext&years:'.$Years.'&comp:'.$comp.'&quarter:all';
        //$countKeys      = 'invoiceCount&years:'.$Years.'&comp:'.$comp;
        //$nextKeys       = 'invoiceNext&years:'.$Years.'&comp:'.$comp;
        $message        = Yii::$app->session->get('message');

        // ถ้ากำลังคำนวณอยู่ 
        // ให้ return data
        if($cal){
            // ถ้า 100%
            // - ยกเลิกการคำนวณ
            // - ลบตัวเลขถัดไป
            // - ลบจำนวนที่นับได้
            if(Yii::$app->cache->get($nextKeys)['key'] == Yii::$app->cache->get($countKeys)){
                Yii::$app->cache->delete('calculating:'.$comp);
                //Yii::$app->cache->delete($countKeys);
                //Yii::$app->cache->delete($nextKeys);
                Yii::$app->session->set('message', '');
                return json_encode([
                    'status'    => 200,
                    'message'   => 'Success',
                    'refresh'   => Yii::$app->cache->get('refresh&comp:'.$comp),
                    'percent'   => 100,
                    'loop'      => Yii::$app->cache->get('loop'),
                    'data'      => $cal ,
                    'by'        => Yii::$app->cache->get('call-by'),
                    'IV'        => Yii::$app->cache->get('IV-COMP:'.$comp),
                    'ram'       => number_format(memory_get_usage() / 1024 / 1024 ,2).' / '.number_format(memory_get_usage(true) / 1024 / 1024 ,2)
                ]);
            }else{
                Yii::$app->session->set('message', '');
                return json_encode([
                    'status'    => Yii::$app->cache->get('process:stop:'.$comp) ? 200 : 403,
                    'message'   => $message,
                    'count'     => (int)Yii::$app->cache->get($countKeys),
                    'next'      => Yii::$app->cache->get($nextKeys),
                    'percent'   => round((Yii::$app->cache->get($nextKeys)['key'] /  Yii::$app->cache->get($countKeys))* 100, 3) ,
                    'loop'      => Yii::$app->cache->get('loop'),
                    'data'      => $cal       ,
                    'by'        => Yii::$app->cache->get('call-by'),
                    'IV'        => Yii::$app->cache->get('IV-COMP:'.$comp),
                    'ram'       => number_format(memory_get_usage() / 1024 / 1024 ,2).' / '.number_format(memory_get_usage(true) / 1024 / 1024 ,2)
                ]);
            }
        }else{
            Yii::$app->session->set('message', '');
            return json_encode([
                'status'    => 200,
                'message'   => Yii::$app->cache->get('process:stop:'.$comp)? ('Stop by humans. :'.Yii::$app->user->identity->id) :'Success',
                'refresh'   => Yii::$app->cache->get('refresh&comp:'.$comp),
                'percent'   => 100,
                'loop'      => Yii::$app->cache->get('loop'),
                'data'      => ['years' => $data->years],
                'IV'        => Yii::$app->cache->get('IV-COMP:'.$comp),
                'ram'       => number_format(memory_get_usage() / 1024 / 1024 ,2).' / '.number_format(memory_get_usage(true) / 1024 / 1024 ,2)
            ]);
        }
    } 


    


    public function actionStopProcess(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $Years          = isset($data->years) ? $data->years : date('Y');

        $status         = 200;
        if($data->status == 'clear'){
            Yii::$app->cache->set('process:stop:'.$comp,true);
            Yii::$app->cache->delete('calculating:'.$comp);
            

            try{ // Line Notify           StockByInvoiceAjax&years:2020&comp:1&quarter:all                                 
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2019&comp:'.$comp.'&quarter:first');
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2019&comp:'.$comp.'&quarter:last');
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2019&comp:'.$comp.'&quarter:all');
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2020&comp:'.$comp.'&quarter:all');
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2020&comp:'.$comp.'&quarter:first');
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2020&comp:'.$comp.'&quarter:last');
                // Yii::$app->cache->delete('StockByInvoiceAjax&years:2020&comp:'.$comp.'&quarter:all');

                //delete JSON file
                try{                            
                                
                    $productjson    = json_encode((Object)[ 'raw' => [] ]);
                    $jsonfile       = Yii::getAlias('@webroot/assets/tmp_stock_by_invoice_'.$comp.'_'.$this->tdate.'_'.$this->fdate.'_'.$Years.'.json');
                    $fp             = fopen($jsonfile, 'w+');
                    fwrite($fp, $productjson);
                    fclose($fp);

                    

                } catch (\Exception $e) {					 
                     // Line Notify Error					 
                    $bot =  \common\models\LineBot::findOne(5);
                    $msg = 'CALCULATE ITEM SALES'."\r\n";                                         
                    $msg.= 'Key Name : '.$keys."\r\n";                                                     
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";
                    $msg.= "Error : ".Yii::t('common','{:e}',[':e' => $e]);
                    
                    $bot->notify_message($msg);	
                }	



                // $bot =  \common\models\LineBot::findOne(5);
                // $msg = 'Stop Process AND CLEAR'."\r\n";                                                                                             
                // $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                 
                //$bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                //$status 		= 500;
                // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
            }	

            $fdate          = date('Y-m-01');
            $tdate          = date('Y-m-t');
            $this->tdate   = date('y_m_d',strtotime($tdate));
            $this->fdate   = date('y_m_d',strtotime($fdate));

            Yii::$app->session->set('fdate',$fdate);
            Yii::$app->session->set('tdate',$tdate);

            return json_encode([
                'status' => 200,
                'message' => 'clear'
            ]);
        }else{
            Yii::$app->cache->delete('process:stop:'.$comp);
            return json_encode([
                'status' => 200,
                'message' => 'continue'
            ]);
        }
        
         
    }

    public function actionDeleteCache($key){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp           = Yii::$app->session->get('Rules')['comp_id'];

    
        Yii::$app->cache->set('process:stop:'.$comp,true);
        Yii::$app->cache->delete($key);
        Yii::$app->cache->delete('calculating:'.$comp);
        return json_encode([
            'status' => 200,
            'message' => 'clear'
        ]);
        
        
         
    }


    public function actionToday(){
        return $this->render('today');
    }

    public function actionTodayAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $keys           = 'todayAjax&comp:'.$comp;

        $rawData    = [];
        $raw        = [];
        $query = WarehouseMoving::find()
        ->where(['between','PostingDate', $data->now.' 00:00:00', $data->now.' 23:59:59'])
        ->andWhere(['comp_id' => $comp])
        ->groupBy('item')
        ->all();

        foreach ($query as $key => $model) {
            $rawData[] = self::validateBomByMoving($model->items,$model->Quantity,$model->header, $model ,0);
            $raw[] = [
                'id'    => $model->items->id,
                'code'  => $model->items->master_code
            ];
        }

        return json_encode([
            'status'    => 200,
            'raw'       => $raw,
            "data"      => self::validateItemConsumption($rawData, date('Y'), $keys)
        ]);
    }


    protected function validateBomByMoving($items, $qty, $header, $line, $loop){
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $data       = [];         
        if($items->ProductionBom != ''){
            // มี BOM            
            if($loop > 3){      
                $loop++;          
                $data[] = (Object)[
                    "id"        => $items->id,
                    'row'       => $line->id,
                    'rowItem'   => $line->items->master_code,
                    'rowItemId' => $line->items->id,
                    'rowItemName'=>$line->Description,
                    'date'      => date('Y-m-d', strtotime($header->PostingDate)),
                    'parent'    => $header->id,
                    'no'        => $header->DocumentNo,
                    'code'      => $items->master_code,
                    'name'      => $items->description_th,
                    'base_unit' => $items->quantity_per_unit * 1,
                    "qty"       => $qty * 1,
                    'inven'     => $items->last_stock,
                    'cost'      => $items->StandardCost,
                    'status'    => 500,
                    'message'   => 'loop ' .$loop
                ]; 

                try{ // Line Notify                                            
                                
                    $bot =  \common\models\LineBot::findOne(5);
                    $msg = 'LOOP'."\r\n";                                         
                    $msg.= 'Count LOOP : '.$loop."\r\n";                                                     
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                     
                    $bot->notify_message($msg);					

                } catch (\Exception $e) {					 
                    $status 		= 500;
                    $message 		= Yii::t('common','{:e}',[':e' => $e]);	
                }	
                //Yii::$app->cache->set('loop',json_encode(['count' => $loop, 'id' => $header->id, 'no' => $header->no_, 'item' => $items->id]));           
            }else{
                $loop++;          
                $BomLine = \common\models\BomLine::find()->where(['bom_no' => $items->ProductionBom])->all();
                foreach ($BomLine as $key => $Line) {
                    if(Yii::$app->cache->get('process:stop:'.$comp)){ 
                        break;
                    }else{
                        $quantity = $Line->quantity * $qty;
                        $data[] = self::validateBomByMoving($Line->items,$quantity,$header,$line, $loop);
                    }
                }
            }
        }else{
            // ไม่มี BOM
            $data[] = (Object)[
                "id"        => $items->id,
                'row'       => $line->id,
                'rowItem'   => $line->items->master_code,
                'rowItemId' => $line->items->id,
                'rowItemName'=>$line->Description,
                'date'      => date('Y-m-d', strtotime($header->PostingDate)),
                'parent'    => $header->id,
                'no'        => $header->DocumentNo,
                'code'      => $items->master_code,
                'name'      => $items->description_th,
                'base_unit' => $items->quantity_per_unit * 1,
                "qty"       => $qty * 1,
                'inven'     => $items->last_stock,
                'cost'      => $items->StandardCost,
                'status'    => 200,
                'message'   => 'Done'
            ];
        }
        
        return $data;
    }


    public function actionStockByInvoiceMonthly(){
        return $this->render('stock-by-invoice-monthly');
    }


    public function actionStockByInvoiceMonthlyAjax(){

        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $Years          = $data->years;
        $Month          = $data->month;
        $Recal          = $data->recal;
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $keys           = 'Stock-By-Invoice-Monthly&years:'.$Years.'&month:'.$Month.'&comp:'.$comp;
        $calculating    = 'calculating-month:'.$comp.'&month:'.$Month;
        $countKeys      = 'invoiceCount-month&years:'.$Years.'&month:'.$Month.'&comp:'.$comp;
        $nextKeys       = 'invoiceNext&years:'.$Years.'&month:'.$Month.'&comp:'.$comp;
        $data           = Yii::$app->cache->get($keys);        
        $calc           = Yii::$app->cache->get($calculating);
 
        $fdate          = date('Y-m-d', strtotime($Years.'-'.$Month.'-01'));
        $tdate          = date('Y-m-d', strtotime($Years.'-'.$Month.'-31'));

        // ถ้ากำลังคำนวณ ให้ return กลับทันที
        if($calc){           
            return json_encode([
                'status'    => 403,
                "source"    => 'cache',
                'message'   => Yii::t('common','Calculating by {:user}',[':user' => Yii::$app->session->get('Rules')['name']]). ' ' . $calc['date'],
                'process'   => Yii::$app->cache->get('process-month:stop:'.$comp),
                'calculating'=> $calc,
                "data"      => $data,
                'percent'   => (Yii::$app->cache->get($nextKeys)['key'] / (Yii::$app->cache->get($countKeys) ? Yii::$app->cache->get($countKeys) : 1)) *100
            ]);      
            exit;
        }else {
        

            
            // Reload ถ้าคลิกที่ คำนวณใหม่
            if($Recal == 1){
                
                $query = \common\models\RcInvoiceHeader::find()
                        ->where(['between','DATE(posting_date)', $fdate, $tdate])
                        ->andWhere(['comp_id'  => $comp])    
                        ->andWhere(['doc_type' => 'Sale'])    // ไม่เอา CN        
                        ->orderBy(['posting_date' => SORT_DESC]);

                $count = $query->count();        
                Yii::$app->cache->set($countKeys,($count > 0 ? $count : 1));

                
                Yii::$app->cache->set($calculating, [
                    'date'      => date('Y-m-d H:i:s'),
                    'years'     => $Years, 
                    'company'   => $comp
                ]);

                Yii::$app->cache->delete('process-month:stop:'.$comp); // ถ้ามีการเคลี่ยร์ ให้เริ่มนับใหม่
                Yii::$app->cache->delete($keys);
                Yii::$app->cache->set('refresh-month&comp:'.$comp,'force');

                $rawData = [];
                foreach ($query->all() as $key => $source) {  
                    Yii::$app->cache->set($nextKeys,['key' => $key + 1, 'id' => $source->id]);                    
                    $Line = \common\models\RcInvoiceLine::find()->where(['source_id' => $source->id])->andWhere(['<>','item',1414])->all();
                    foreach ($Line as $key => $IvLine) {
                        // $quantity = $IvLine->rcInvoiceHeader->doc_type == 'Sale'
                        //                 ? ($IvLine->quantity * 1)
                        //                 : ($IvLine->quantity * -1);

                        $quantity   = $IvLine->quantity * 1;
                        $buffer     = self::findAlreadyItem($rawData, $IvLine->items->id);
                        $rawData[]  = self::validateBom($IvLine->items, $quantity, $source, $IvLine ,0, $buffer);
                        if(Yii::$app->cache->get('process-month:stop:'.$comp)){ break; }
                    }
                    if(Yii::$app->cache->get('process-month:stop:'.$comp)){ break; }
                }

                return json_encode([
                    "source"    => 'api',
                    "data"      => self::validateItemConsumptionMonth($rawData,$Years, $keys, $nextKeys, $countKeys, $calc, $calculating),
                    'status'    => 200,
                    'from'      => $fdate,
                    'to'        => $tdate,
                    'new'       => true
                ]);
            

            }else{

                

                // Onload and Click years            
                if(Yii::$app->cache->get($keys)){
                    Yii::$app->cache->delete('refresh-month&comp:'.$comp);
                    return json_encode([
                        "source"    => 'cache',
                        "data"      => Yii::$app->cache->get($keys),
                        'status'    => 200,
                        'from'      => $fdate,
                        'to'        => $tdate,
                        'new'       => false
                    ]);        
                    
                }else{
                    Yii::$app->cache->set($calculating, [
                        'date'      => date('Y-m-d H:i:s'),
                        'years'     => $Years, 
                        'company'   => $comp,
                        'source'    => 'api'
                    ],10);
                    Yii::$app->cache->set('refresh-month&comp:'.$comp, 'force');
        
                    $query = \common\models\RcInvoiceHeader::find()
                            ->where(['between','DATE(posting_date)', $fdate, $tdate])
                            ->andWhere(['comp_id'  => $comp])    
                            ->andWhere(['doc_type' => 'Sale'])    // ไม่เอา CN        
                            ->orderBy(['posting_date' => SORT_DESC]);
                    
                    $rawData = [];
                    foreach ($query->all() as $key => $source) {
                        Yii::$app->cache->set($nextKeys,['key' => $key + 1, 'id' => $source->id]);
                        $Line = \common\models\RcInvoiceLine::find()->where(['source_id' => $source->id])->andWhere(['<>','item',1414])->all();
                        foreach ($Line as $key => $IvLine) {

                            // $quantity = $IvLine->rcInvoiceHeader->doc_type == 'Sale' 
                            //                 ? ($IvLine->quantity * 1)
                            //                 : ($IvLine->quantity * -1);
                            $quantity = $IvLine->quantity * 1;
                            $buffer     = self::findAlreadyItem($rawData, $IvLine->items->id);
                            $rawData[] = self::validateBom($IvLine->items, $quantity, $source, $IvLine, 0, $buffer);
                            if(Yii::$app->cache->get('process-month:stop:'.$comp)){ break; }
                        }
                        if(Yii::$app->cache->get('process-month:stop:'.$comp)){ break; }
                    }                    
                    return json_encode([
                        "source"    => 'api',
                        "data"      => self::validateItemConsumptionMonth($rawData, $Years, $keys, $nextKeys, $countKeys, $calc, $calculating),
                        'status'    => 200,
                        'from'      => $fdate,
                        'to'        => $tdate,
                        'new'       => false
                    ]);
                }

                 
            }
        }
    
        
    }

    protected function validateItemConsumptionMonth($rawData, $Years, $keys, $nextKeys, $countKeys, $calc ,$calculating){
        
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $newData    = self::validateItemConsumptionChild($rawData);
        $next       = Yii::$app->cache->get($nextKeys);
        $count      =  Yii::$app->cache->get($countKeys) ?  Yii::$app->cache->get($countKeys) : 1;

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
            'message'   => Yii::$app->cache->get('process-month:stop:'.$comp)? (Yii::t('common','Interrupted').' '.Yii::$app->cache->get('process:stop:'.$comp)) : Yii::t('common','Success'),
            'percent'   => $next ? ($next['key'] /  $count) * 100 : 100
        ];

        // Radis 
        // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
        $cache      = Yii::$app->cache;
    
        //$cache->set($key, $data, 30, $dependency);
        if($cache->set($keys, $storeDate)){
            $cache->delete($calculating);
            $cache->delete($countKeys);
            $cache->delete($nextKeys);
            $cache->delete('loop');
            $cache->delete('process:stop:'.$comp);
            return $cache->get($keys);           
        } 
    }

    public function actionCheckServerMonthly(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $Years          = $data->years;
        $Recal          = $data->recal;
        $Month          = $data->month;
        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $calculating    = 'calculating-month:'.$comp.'&month:'.$Month;
        $calc           = Yii::$app->cache->get($calculating);
        //$cal            = Yii::$app->cache->get('calculating-month:'.$comp.'&month:'.$Month);
        $countKeys      = 'invoiceCount-month&years:'.$Years.'&month:'.$Month.'&comp:'.$comp;
        $nextKeys       = 'invoiceNext&years:'.$Years.'&month:'.$Month.'&comp:'.$comp;
        $count          =  Yii::$app->cache->get($countKeys) ?  Yii::$app->cache->get($countKeys) : 1;
        // ถ้ากำลังคำนวณอยู่ 
        // ให้ return data
        if($calc){
            // ถ้า 100%
            // - ยกเลิกการคำนวณ
            // - ลบตัวเลขถัดไป
            // - ลบจำนวนที่นับได้
            if(Yii::$app->cache->get($nextKeys)['key'] == Yii::$app->cache->get($countKeys)){
                Yii::$app->cache->delete($calculating);
                //Yii::$app->cache->delete($countKeys);
                //Yii::$app->cache->delete($nextKeys);
                return json_encode([
                    'status'    => 200,
                    'message'   => 'Success',
                    'refresh'   => Yii::$app->cache->get('refresh-month&comp:'.$comp),
                    'percent'   => 100,
                    'loop'      => Yii::$app->cache->get('loop'),
                    'data'      => $calc   
                ]);
            }else{
                return json_encode([
                    'status'    => Yii::$app->cache->get('process-month:stop:'.$comp) ? 200 : 403,
                    'count'     => Yii::$app->cache->get($countKeys),
                    'next'      => Yii::$app->cache->get($nextKeys),
                    'percent'   => round((Yii::$app->cache->get($nextKeys)['key'] /  $count)* 100, 3) ,
                    'loop'      => Yii::$app->cache->get('loop'),
                    'data'      => $calc                
                ]);
            }
        }else{
            return json_encode([
                'status'    => 200,
                'message'   => Yii::$app->cache->get('process-month:stop:'.$comp)? ('Stop by humans. :'.Yii::$app->user->identity->id) :'Success',
                'refresh'   => Yii::$app->cache->get('refresh-month&comp:'.$comp),
                'percent'   => 100,
                'loop'      => Yii::$app->cache->get('loop'),
                'data'      => ['years' => $data->years]
            ]);
        }
    } 


    public function actionMyStock(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);


        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];

        $query          = ItemForCompany::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();

        foreach ($query as $key => $item) {

            $raws[] = [
                'id'                => $item->item,
                'img'               => $item->items->picture,
                'code'              => $item->items->master_code,
                'name'              => $item->name,
                'stock'             => $item->items->ProductionBom > 0
                                            ? $item->items->last_possible
                                            : $item->items->last_stock,
                'stock_customer'    => $item->items->last_possible
                // 'stock'             => $item->items->last_possible,
                // 'stock_customer'    => $item->invenByCache
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }

    
}
