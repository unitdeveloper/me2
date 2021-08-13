<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\ItemJournal;
use admin\modules\warehousemoving\models\StockSearch;
use admin\modules\itemgroup\models\SearchItemGroup;
use admin\modules\itemgroup\models\SearchItemGroupCommon;
use common\models\ItemgroupCommon;
use common\models\ItemsHasGroups;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use admin\models\FunctionCenter;
use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use common\models\Items;
use common\models\Location;

/**
 * StockController implements the CRUD actions for ItemJournal model.
 */
class StockController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'get-item-group' => ['POST'],
                    'get-item-list' => ['POST'],
                    'post-update' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ItemJournal models.
     * @return mixed
     */
    public function actionIndex()
    {
        // $searchModel = new StockSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel = new SearchItemGroupCommon();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $session = \Yii::$app->session;
        $session->set('workdate', date('Y-m-d'));
       
        if(Yii::$app->request->get('for')){
            if(Yii::$app->request->get('for') == 'all'){
                $dataProvider->query->andWhere([
                    "child"     => '0',
                    'status'    => "1",
                    'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
                ]);
            }else{
                $dataProvider->query->andWhere([
                    "child"     => '0',
                    'status'    => "1", 
                    'group_for' => Yii::$app->request->get('for'), 
                    'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
                ]);
            }            
        }else{
            $dataProvider->query->andWhere([
                "child"     => '0',
                'status'    => "1", 
                'group_for' => "inv", 
                'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
            ]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetItemGroup(){
        $request_body   = file_get_contents('php://input'); //Request Payload 
        $data           = json_decode($request_body);
        
        $models = ItemgroupCommon::find()->where([
            "child"     => $data->id,
            'status'    => "1", 
            'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
        ])->orderBy('sequent')->all();

        $data = array();
        foreach ($models as $key => $model) {
            $data[]= (Object)[
                'child' => self::getChildGroup($model),
                'id' => $model->id,
                'name' => $model->name,
                'name_en' => $model->name_en
            ];
        }
        
        return json_encode($data); 
    }

    public static function getChildGroup($data){

        $models = ItemgroupCommon::find()->where([
            "child"     => $data->id,
            'status'    => "1", 
            'comp_id'   => $data->comp_id
        ])->orderBy('sequent')->all();

        $data = array();
        foreach ($models as $key => $model) {
            $data[]= (Object)[
                'child' => self::getChildGroup($model),
                'id' => $model->id,
                'name' => $model->name,
                'name_en' => $model->name_en
            ];
        }

        return $data;
    }



    public function actionGetItemList(){
        $request_body   = file_get_contents('php://input'); //Request Payload 
        $data           = json_decode($request_body);
        $force          = isset($data->force) ? 1 : 0;

        $models = ItemsHasGroups::find()
                    // ->joinWith('items')
                    // ->joinWith('groups')
                    // ->select('items.description_th as name, items_has_groups.id, itemgroup_common.name as group, itemgroup_common.photo as photo')
                    ->where([
                        "group_id"  => $data->id,
                        'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
                    ])->all();

        $datas = array();
        foreach ($models as $key => $model) {
            $datas[]= (Object)[
                //'id'    => $model->id,
                'id'            => $model->items->id,
                'name'          => $model->items->description_th. ' ' .$model->items->detail. ' ' . $model->items->size,
                'alias'         => $model->items->alias,
                'code'          => $model->items->master_code,
                'group'         => $model->groups->name,
                'group_id'      => $model->groups->id,
                'photo'         => $model->items->picture,
                //'inven' => $model->items->inven,
                'force'         => $force == 1 ? $model->items->qtyForce : '',
                'inven'         => $model->items->myItems->last_stock * 1,
                'remain'        => "",
                'diff'          => "",
                'outstanding'   => $model->items->quantity_to_ship * 1,
                'workdate'      => Yii::$app->session->get('workdate').date(' H:i:s')
            ];
        }

        return $this->asJson($datas); 
    }

    public function actionPostUpdate(){
        $request_body   = file_get_contents('php://input'); //Request Payload 
        $data           = json_decode($request_body);
        
        $json           = json_decode($data->json, TRUE);
        $groupId        = $data->groupId;
        $postdate       = date("Y-m-d", strtotime($data->postDate));
        $fullDateTime   = $postdate.' '.$data->times;  
        $remark         = $data->remark;
        $inspector      = $data->inspector;

        $tmp_head       = WarehouseHeader::find()->where([
                            'TypeOfDocument' => 'Adjust',
                            'comp_id' => Yii::$app->session->get('Rules')['comp_id']
                            ]);         

        $lastLine       = $tmp_head->max('line_no'); // เอา line no มา + 1 เพื่ีอทำเป็น Runing no
        $nextLine       = sprintf("%03d",($lastLine + 1));
        $documentNo     = 'AJ'.date('ym').'-'.($nextLine);
        
 
        $groups         = ItemgroupCommon::findOne($groupId);

        $que            = [];

        foreach ($json as $key => $value) {   
            
            if(count($value['data'])){
                // Create Header
                $model                  = new WarehouseHeader();
		
                $model->PostingDate		= $fullDateTime;  
                $model->ship_date       = $postdate;
                $model->DocumentDate 	= date('Y-m-d');
                $model->TypeOfDocument 	= 'Adjust';
                $model->Description     = $groups->name;
                $model->SourceDocNo     = $groups->id;
                $model->Quantity        = count($value['data']);
                $model->DocumentNo 	    = $documentNo;
                $model->customer_id     = 909;
                $model->ship_to 		= 0;
                $model->status 			= 'Adjust';
                $model->remark          = $remark;
                $model->contact         = $inspector;
                $model->user_id 		= Yii::$app->user->identity->id;
                $model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $model->line_no 		= $nextLine;

                 
                if($model->save()){
                    // Create Line
                    $que = self::stockChange($value['data'],$documentNo,$fullDateTime,$model);
                }else{
                    $que = [
                        'id' => $source->id,
                        'status' => 0,
                        'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                    ];
                }
                
            }

            
        }     
        
        // Notify

        try{
					 
            $msg = "Stock Adjust : "."\r\n\r\n";

            foreach ($que->data as $key => $notice) {
                $msg.= ($key + 1) .') '.$notice['code']."  ".$notice['name']."\r\n";            
                $msg.= Yii::t('common','Quantity').' : ' .$notice['qty']."\r\n";
                $msg.= Yii::t('common','Remain').' : ' .$notice['inven']."\r\n\r\n";
            } 
            
            $msg.= Yii::t('common','Remark')." : ". $remark ."\r\n";
            $msg.= Yii::t('common','By')." : ". $inspector ."\r\n";
            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
             

            // Line Notify
            $bot =  \common\models\LineBot::findOne(2);
            $bot->notify_message($msg);					

        } catch (\Exception $e) {					 
            // $status 		= 500;
            // $message 		= Yii::t('common','{:e}',[':e' => $e]);	
        }
   
         
        return $this->asJson([
            'data'      => $que->data,
            'id'        => $que->id,
            'groupId'   => $groupId,
            'no'        => $documentNo            
        ]); 
    }

    public static function stockChange($models,$documentNo,$postdate,$header){

        $que            = [];

        foreach ($models as $keys => $source) {
                
            $source = (Object)$source;

            $item   = Items::findOne($source->id);

            $model                  = new WarehouseMoving();                 
            $model->PostingDate     = $postdate;                                          
            $model->line_no         = date('dmYhis');
            $model->source_id       = $header->id;
            $model->DocumentNo      = $header->DocumentNo;
            $model->TypeOfDocument  = $header->TypeOfDocument;
            $model->item            = $source->id;
            $model->ItemNo          = $item->No;
            $model->Description     = $item->Description;
            $model->SourceDocNo     = $model->DocumentNo;
            $model->SourceDoc       = $header->SourceDocNo;            
            $model->Quantity        = $source->remain - $item->inven;
            $model->QtyToMove       = $source->remain;
            $model->QtyMoved        = $item->inven;
            $model->QtyOutstanding  = 0;
            $model->DocumentDate    = date('Y-m-d');  
            $model->qty_per_unit    = 1;
            $model->unit_price      = 0;
            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->session_id      = Yii::$app->session->getId();

            $model->qty_before      = $item->inven;
            $model->qty_after       = $model->qty_before + $model->Quantity;

            $Location               = Location::find()
                                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                        ->orderBy(['defaultlocation' => SORT_DESC])
                                        ->one();
            $model->location        = $Location != null ? $Location->id : 1;

            if($model->save()){
                // update item 
                $model->items->updateQty;
                // $item  = Items::findOne($model->item);
                // $item->last_stock = $model->qty_after;
                // $item->save(false);
                
                $que[] = [
                    'id'        => $source->id,
                    'status'    => 1,
                    'date'      => $postdate,
                    'item'      => $item->id,
                    'code'      => $item->master_code,
                    'name'      => $model->Description,
                    'qty'       => $model->Quantity,
                    'inven'     => $item->inven,
                    'doc_no'    => $model->DocumentNo,
                    'doc_id'    => $model->id
                ];
            }else{
                $que[] = [
                    'id'        => $source->id,
                    'status'    => 0,
                    'name'      => '',
                    'qty'       => 0,
                    'inven'     => 0,
                    'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                ];
            }

        }


        return (Object)[
            'data'  => $que,
            'id'    => $header->id
        ];
    }


    public function actionPrint($id)
    {
        $model = ItemgroupCommon::findOne($id);     
        return $this->renderpartial('print',['model' => $model]);
      
    }

    public function actionPrintReport($id)
    {
        $head   = $this->findModelWh($id);
        $models = WarehouseMoving::find()
        ->where(['source_id' => $head->id])
        ->andWhere(['comp_id'   => Yii::$app->session->get('Rules')['comp_id']])
        ->all(); 
        return $this->renderpartial('print-report',[
            'models' => $models,
            'head' => $head
            ]);
      
    }


    /**
     * Displays a single ItemJournal model.
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
     * Creates a new ItemJournal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemJournal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ItemJournal model.
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
     * Deletes an existing ItemJournal model.
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
     * Finds the ItemJournal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemJournal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemJournal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }

    protected function findModelWh($id)
    {
        if (($model = WarehouseHeader::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
    
}
