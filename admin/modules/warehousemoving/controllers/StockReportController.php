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
use common\models\ItemMystore;
/**
 * StockController implements the CRUD actions for ItemJournal model.
 */
class StockReportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if ($action->id == 'hr') {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }
    
        return parent::beforeAction($action);
    }

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
                    'add-group' => ['POST'], 
                    'remove-group' => ['POST'], 
                    'move-item-group' => ['POST'],
                    'find-item' => ['POST'],
                    'find-item-not-in-group' => ['POST'],
                    'set-item-to-goup' => ['POST'],
                    'remove-item-from-group' => ['POST'],
                    'add-item-to-group' => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex(){
        $query = WarehouseHeader::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['TypeOfDocument' => 'Adjust'])
        ->orderBy(['PostingDate' => SORT_DESC]);

        $dataProvider   = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);   

        return $this->render('index',[
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionSetting(){

        $searchModel    = new SearchItemGroupCommon();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
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

        $dataProvider->query->orderBy(['sequent' => SORT_DESC]);
        // $dataProvider->query->andWhere([
        //         "child"     => '0',
        //         //'status'    => "1", 
        //         'group_for' => "inv", 
        //         'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
        //     ])
        // ->orderBy(['sequent' => SORT_DESC]);

        return $this->render('setting', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddGroup(){
        $request_body   = file_get_contents('php://input'); //Request Payload 
        $data           = json_decode($request_body);

        $model              = new ItemgroupCommon();
        $model->name        = $data->name;
        $model->name_en     = $model->name;
        $model->child       = $data->child;
        $model->group_for   = 'inv';
        $model->status      = self::getTopParent($data->child)->status;
        $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];

        if($model->save()){
            $res = [
                'status'=>200,
                'id' => $model->id
            ];
        }else{
            $res = [
                'status'=>500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ];
        }

        return $this->asJson($res);

    }

    static function removeChild($id){
        $models = ItemgroupCommon::find()->andWhere(['child' => $id])->all();

        foreach ($models as $key => $model) {
            if(ItemgroupCommon::find()->where(['child' => $model->id])->exists()){
                $res[] = self::removeChild($model->id);                
            }

            $res[] = [
                'status'=>200,
                'id' => self::DeleteOnce($model->id)
            ];
            
        }

        return $res;
    }

    public function actionRemoveGroup(){
        $request_body   = file_get_contents('php://input'); //Request Payload 
        $data           = json_decode($request_body);

        if(ItemgroupCommon::find()->where(['child' => $data->id])->exists()){
            $res[] = [
                'next' => self::removeChild($data->id),
                'id' => self::DeleteOnce($data->id)
            ];
        }else{
            

            $res[] = [
                'status'=>200,
                'id' => self::DeleteOnce($data->id)
            ];
             
        }
 
           

        return $this->asJson($res);

    }

    
    static function getTopParent($id){

        $model = ItemgroupCommon::findOne($id);
        
        $status = 1;
        if($model !==null ){
            if($model->child > 0){
                self::getTopParent($model->child);
            }

            $status = $model->status;
        }

        
        return (Object)[
            'status' => $status
        ];   
    }



    public function actionEditGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $model          = ItemgroupCommon::findOne($data->id);
        $model->name    = $data->name;
        $model->name_en = $model->name;
        $model->status  = self::getTopParent($data->id)->status;

        if($model->save()){
            return $this->asJson([
                'status' => 200,
                'data' => $data,
                'trash' => $model->status
            ]);
        }else{
            return $this->asJson([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }

        
    }

    static function updateList($data,$parent,$i){
        $el     = [];
        foreach ($data as $key => $values) {

            foreach ($values as $key => $value) {
                $i++;
                $el[] = [
                    'id' => $value->id,
                    'sequent' => $i,
                    'name' => $value->name,
                    'child' => $value->children? self::updateList($value->children,$value->id,$i) : ''
                ];   

                $model          = ItemgroupCommon::findOne($value->id);
                $model->child   = $parent;
                $model->status  = 1;
                $model->sequent = $i;
                $model->save();
            }
        }
        return $el;
    }   

    static function updateTrash($data,$parent,$i){
        $el     = [];
        foreach ($data as $key => $values) {

            foreach ($values as $key => $value) {
                $i++;
                $el[] = [
                    'id' => $value->id,
                    'sequent' => $i,
                    'name' => $value->name,
                    'child' => $value->children? self::updateTrash($value->children,$value->id,$i) : ''
                ];
                                 
                $model          = ItemgroupCommon::findOne($value->id);
                $model->child   = $parent;
                $model->sequent = $i;
                $model->status  = 0;
                $model->save();
             
            }
        }
        return $el;
    } 

    public function actionMoveItemGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $el     = [];
        $i      = 0;
        $status = 0;
        foreach ($data[0] as $key => $value) {
            $i++;
            $el[] = [
                'id' => $value->id,
                'sequent' => $i,
                'name' => $value->name,
                'child' => $value->children? self::updateList($value->children,$value->id,$i) : ''
            ];    
            $model          = ItemgroupCommon::findOne($value->id);
            $model->child   = 0;
            $model->status  = 1;
            $model->sequent = $i;
            if($model->save()){
                $status = 200;
            }else{
                $status = 500;
            }
        }
        // Trash
        foreach ($data[1] as $key => $value) {
            $i++;
            $el[] = [
                'id' => $value->id,
                'sequent' => $i,
                'name' => $value->name,
                'child' => $value->children? self::updateTrash($value->children,$value->id,$i) : ''
            ];    
            $model          = ItemgroupCommon::findOne($value->id);
            $model->child   = 0;
            $model->sequent = $i;
            $model->status  = 0;
            if($model->save()){
                $status = 200;
            }else{
                $status = 500;
            }
        }
        return $this->asJson(['status' => $status,'data' => $el]);
    }

    public function actionAddItem($id){
        $models = ItemsHasGroups::find()
        ->where(['group_id' => $id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all();

        // Item ที่ยังไม่จัดกลุ่ม
        $newItem    = ItemMystore::find()
                    ->where(['not in', 'item', ItemsHasGroups::find()->select('item_id')])
                    ->andWhere(['status' => 1])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->count();

        // Item ที่จัดกลุ่มซ้ำกัน
        $duplicate  = ItemsHasGroups::find()
                        ->select('item_id')
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->groupBy('item_id')
                        ->having(['>','count(*)',1])
                        ->count();


        return $this->render('add-item',[
            'models' => $models,
            'newItem' => $newItem,
            'duplicate' => $duplicate
        ]);
    }

    public function actionFindItem(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 2 ? '' : 5;
         
        $query = ItemMystore::find()
                ->where(['or',
                    ['like','name',explode(' ',trim($data->search))],
                    ['like','name_en',explode(' ',trim($data->search))],
                    ['like','master_code',explode(' ',trim($data->search))],
                    ['like','barcode',explode(' ',trim($data->search))]
                ])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                //->andWhere(['not in','id', ItemsHasGroups::find()->select('item_id')])
                ->andWhere(['status' => 1])
                ->limit($limit)
                ->all();

        $obj = [];
        foreach ($query as $key => $model) {
            $groups = ItemsHasGroups::find()->where(['item_id' => $model->item])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
            $group  = [];
            foreach ($groups as $key => $g) {
                $group[] = [
                    'id' => $g->groups->id,
                    'name' => $g->groups->name
                ];
            }
            $obj[] = (Object)[
                'id'        => $model->item,
                'name'      => $model->name,
                'name_en'   => $model->name_en,
                'code'      => $model->master_code,
                'inv'       => $model->items->invenByCache,
                'exists'    => $group
            ];
        }

        return $this->asJson([
            'limit'     => $limit ? $limit : 'unlimited',
            'data'      => $obj,
            'search'    => $data->search
        ]);
    }

    public function actionFindItemNotInGroup(){
         
        $query = ItemMystore::find()
        ->where(['not in','item', ItemsHasGroups::find()->select('item_id')])
        ->andWhere(['status' => 1])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all();

        $obj = [];
        foreach ($query as $key => $model) {
            $obj[] = (Object)[
                'id'        => $model->item,
                'name'      => $model->name,
                'name_en'   => $model->name_en,
                'code'      => $model->master_code,
                'exists'    => false
            ];
        }

        return $this->asJson([
            'limit'     => 'unlimited',
            'data'      => $obj,
            'search'    => ''
        ]);
    }

    public function actionRemoveItemFromGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = '';
        $model          = ItemsHasGroups::find()
                        ->where(['item_id' => $data->id,])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->andWhere(['group_id' => $data->group])
                        ->one();

        if($model!==null){
            if($model->delete()){
                $status = 200;
            }else{
                $status = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }

        return $this->asJson([
            'id'    => $data->id,
            'group' => $data->group,
            'status' => $status
        ]);

    }

    public function actionAddItemToGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = '';
        $model          = new ItemsHasGroups();
        $model->group_id= $data->group;
        $model->item_id = $data->id;
        $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];

        if($model->save()){
            $status     = 200;
        }else{
            $status     = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }
        
        return $this->asJson([
            'id'        => $data->id,
            'group'     => $data->group,
            'status'    => $status
        ]);

    }

    public function actionSetItemToGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        //clear group 
        $status = '';
   
        ItemsHasGroups::deleteAll([
            'group_id' => $data->group,
            'comp_id' => Yii::$app->session->get('Rules')['comp_id']
        ]);
        
        foreach ($data->data as $key => $value) {

            $model          = new ItemsHasGroups();
            $model->item_id = $value->id;
            $model->group_id= $data->group;
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            if($model->save()){
                $status = 200;
            }else{
                $status = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }

        }
      
        

        return $this->asJson([
            'data'    => $data->data,
            'status' => $status
        ]);
    }

    public function actionHr(){         
        return $this->renderPartial('hr');
    }

 
    static function DeleteOnce($id)
    {
        ItemgroupCommon::findOne($id)->delete();
        return $id;
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
        if (($model = ItemgroupCommon::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}