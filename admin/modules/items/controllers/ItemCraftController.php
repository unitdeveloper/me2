<?php

namespace admin\modules\items\controllers;

use Yii;
use common\models\ItemCraft;
use common\models\Items;
use admin\modules\items\models\ItemCraftSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\WarehouseHeader;

/**
 * ItemCraftController implements the CRUD actions for ItemCraft model.
 */
class ItemCraftController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'                => ['POST'],
                    'get-item-craft'        => ['POST'],
                    'add-item-to-bom-table' => ['POST'],
                    'minus-from-table'      => ['POST'],
                    'carft-item'            => ['POST'],
                    'update-item-craft-proirity' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ItemCraft models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemCraftSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemCraft model.
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
     * Creates a new ItemCraft model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemCraft();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ItemCraft model.
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
     * Deletes an existing ItemCraft model.
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
     * Finds the ItemCraft model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemCraft the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemCraft::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }



    public function actionGetItemCraft(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $raws           = [];
        $query          = ItemCraft::find()
                        ->andWhere(['source_item' => $data->id])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->orderBy(['priority' => SORT_ASC])
                        ->all();
         
        $item           = Items::findOne($data->id);  
         
        foreach ($query as $key => $model) {
             
            $raws[] = [
                'id'    => $model->id,
                'item'  => $model->item,
                'code'  => $model->items->master_code,
                'name'  => $model->name,
                'nameTh'=> $model->items->description_th,
                'alias' => $model->items->name?:' -- ',
                'qty'   => $model->quantity,
                'cost'  => $model->cost,
                'img'   => $model->picture,
                'stock' => $model->items->qtyAfter,
                'prio'  => $model->priority
            ];
        }

        
        for ($i=count($raws); $i < 20; $i++) { 
            $raws[] = [
                'id'    => '',
                'item'  => '',
                'name'  => '',
                'nameTh'=> '',
                'alias' => '',
                'qty'   => '',
                'cost'  => '',
                'img'   => '',
                'stock' => '',
                'prio'  => 0
            ];
        }
         
        
        
         

        return json_encode([
            'status' => 200,
            'message' => '',
            'item' => $item->id,
            'name' => $item->name,
            'description' => $item->description_th,
            'img'   => $item->picture,
            'raws' => $raws
        ]);
    }


    public function actionAddItemToBomTable(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];
        $Items          = Items::findOne($data->id);

        $WH             = new WarehouseHeader();
        if($WH->craftPermission){

            $transaction = Yii::$app->db->beginTransaction();
            try { 
                // ถ้ามีให้นับมาบวกเพิ่ม
                $oldValue       = ItemCraft::find()
                            ->where(['item' => $data->id])
                            ->andWhere(['source_item' => $data->source])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->one();

                if($oldValue != null){
                    $quantity       = $oldValue->quantity + 1;
                    $model          = $oldValue;
                }else{
                    $quantity       = 1;
                    $model          = new ItemCraft();
                    $model->priority= 100;
                }
                
                $model->source_item = $data->source;
                $model->item        = $Items->id;
                $model->name        = $Items->Description;
                $model->code        = $Items->master_code;
                $model->img         = $Items->picture;
                $model->cost        = 0;
                $model->measure     = $Items->unit_of_measure;
                $model->user_id     = Yii::$app->user->identity->id;
                $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];

            
                
                $model->quantity= $quantity;
                
                if(!$model->save()){
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                    
                }else{

                    $query          = ItemCraft::find()
                                    ->andWhere(['source_item' => $model->source_item])
                                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                    ->orderBy(['priority' => SORT_ASC])
                                    ->all();

                    foreach ($query as $key => $model) {
                        $raws[] = [
                            'id'    => $model->id,
                            'item'  => $model->item,
                            'code'  => $model->items->master_code,
                            'name'  => $model->name,
                            'nameTh'=> $model->items->description_th,
                            'alias' => $model->items->name?:' -- ',
                            'qty'   => $model->quantity,
                            'cost'  => $model->cost,
                            'img'   => $model->picture,
                            'stock' => $model->items->qtyAfter,
                            'prio'  => $model->priority
                        ];
                    }

                    for ($i=count($raws); $i < 20; $i++) { 
                        $raws[] = [
                            'id'    => '',
                            'item'  => '',
                            'code'  => '',
                            'name'  => '',
                            'nameTh'=> '',
                            'alias' => '',
                            'qty'   => '',
                            'cost'  => '',
                            'img'   => '',
                            'stock' => '',
                            'prio'  => 0
                        ];
                    }
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status     = 500;
                $message    = Yii::t('common','{:e}',[':e' => $e]);
            }

        }else{
            $status     = 403;
            $message    = Yii::t('common',"You don't have permission");
        }

       

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }


    public function actionMinusFromTable(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');

        $raws           = [];

        $WH             = new WarehouseHeader();
        if($WH->craftPermission){

            $transaction = Yii::$app->db->beginTransaction();
            try { 
                // ถ้ามีให้นับมาบวกเพิ่ม
                $model       = ItemCraft::findOne($data->id);            

                if($model->quantity > 1){
                    $model->quantity= $model->quantity - 1;
                    $model->save();

                }else{
                    $model->delete();
                }

                $query          = ItemCraft::find()
                                    ->andWhere(['source_item' => $model->source_item])
                                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                    ->orderBy(['priority' => SORT_ASC])
                                    ->all();

                foreach ($query as $key => $model) {
                    $raws[] = [
                        'id'    => $model->id,
                        'item'  => $model->item,
                        'name'  => $model->name,
                        'nameTh'=> $model->items->description_th,
                        'alias' => $model->items->name?: ' -- ',
                        'qty'   => $model->quantity,
                        'cost'  => $model->cost,
                        'img'   => $model->picture,
                        'stock' => $model->items->qtyAfter,
                        'prio'  => $model->priority
                    ];
                }

                for ($i=count($raws); $i < 20; $i++) { 
                    $raws[] = [
                        'id'    => '',
                        'item'  => '',
                        'name'  => '',
                        'nameTh'=> '',
                        'alias' => '',
                        'qty'   => '',
                        'cost'  => '',
                        'img'   => '',
                        'stock' => '',
                        'prio'  => 0
                    ];
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status     = 500;
                $message    = Yii::t('common','{:e}',[':e' => $e]);
            }
        }else{
            $status     = 403;
            $message    = Yii::t('common',"You don't have permission");
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);


    }

    public function actionCarftItem(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');
        $craft          = [];

        $raws           = [];
        $Item           = Items::findOne($data->source);
        $WH             = new WarehouseHeader();
        if($WH->craftPermission){

            $CraftTable  = ItemCraft::find()
                        ->andWhere(['source_item' => $data->source])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->orderBy(['priority' => SORT_ASC]);

            if($CraftTable->count() > 0){
           
                $transaction = Yii::$app->db->beginTransaction();
                try { 
                    // Create Warehouse
                    
                    $craft      = $WH->craftItem((Object)[
                                    'source'    => $data->source,
                                    'qty'       => $data->qty
                                ]);

                    if($craft->status == 200){

                        

                        foreach ($CraftTable->all() as $key => $model) {
                            $raws[] = [
                                'id'    => $model->id,
                                'item'  => $model->item,
                                'name'  => $model->name,
                                'nameTh'=> $model->items->description_th,
                                'alias' => $model->items->name?: ' -- ',
                                'qty'   => $model->quantity,
                                'cost'  => $model->cost,
                                'img'   => $model->picture,
                                'stock' => $model->items->qtyAfter,
                                'prio'  => $model->priority
                            ];
                        }

                        for ($i=count($raws); $i < 20; $i++) { 
                                $raws[] = [
                                    'id'    => '',
                                    'item'  => '',
                                    'name'  => '',
                                    'nameTh'=> '',
                                    'alias' => '',
                                    'qty'   => '',
                                    'cost'  => '',
                                    'img'   => '',
                                    'stock' => '',
                                'prio'  => 0
                            ];
                        }


                        

                        try{ // Line Notify                                            
                                        
                            $bot =  \common\models\LineBot::findOne(5);
                            $msg = 'Craft Item '."\r\n\r\n";
                            $msg.= $Item->master_code."\r\n";
                            $msg.= $Item->Description."\r\n\r\n";
                            $msg.= Yii::t('common','Quantity').' : '.$data->qty."\r\n";
                            $msg.= Yii::t('common','Remain').' : '.$Item->qtyAfter."\r\n\r\n";
                            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                            
                            $bot->notify_message($msg);					

                        } catch (\Exception $e) {					 
                                
                            $message 		= Yii::t('common','{:e}',[':e' => $e]);	

                        }	
        
                        


                    }else{
                        $status     = 500;
                        $message    = $craft->message;
                    }

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $status     = 500;
                    $message    = Yii::t('common','{:e}',[':e' => $e]);
                }
            }else{
                $status     = 404;
                $message    = Yii::t('common',"Unable to produce.");
            }
        }else{
            $status     = 403;
            $message    = Yii::t('common',"You don't have permission");
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws,
            'item'      => $Item->id,
            'stock'     => $Item->qtyAfter,
            'craft'     => $craft
        ]);
    }

    public function actionUpdateItemCraftProirity(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $message        = Yii::t('common','Success');
        $raws           = [];
        $id             = 0;

        foreach ($data->data as $key => $value) {
            $model  = ItemCraft::findOne($value->id);
            $model->priority = $value->priority;
            $id     = $model->source_item;
            $model->save();
        }


        $query          = ItemCraft::find()
                        ->andWhere(['source_item' => $id])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->orderBy(['priority' => SORT_ASC])
                        ->all();

        foreach ($query as $key => $model) {
            $raws[] = [
                'id'    => $model->id,
                'item'  => $model->item,
                'code'  => $model->items->master_code,
                'name'  => $model->name,
                'nameTh'=> $model->items->description_th,
                'alias' => $model->items->name?:' -- ',
                'qty'   => $model->quantity,
                'cost'  => $model->cost,
                'img'   => $model->picture,
                'stock' => $model->items->qtyAfter,
                'prio'  => $model->priority
            ];
        }

        for ($i=count($raws); $i < 20; $i++) { 
            $raws[] = [
                'id'    => '',
                'item'  => '',
                'code'  => '',
                'name'  => '',
                'nameTh'=> '',
                'alias' => '',
                'qty'   => '',
                'cost'  => '',
                'img'   => '',
                'stock' => '',
                'prio'  => 0
            ];
        }
        
       
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);

    }
 
}
