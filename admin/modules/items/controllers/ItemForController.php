<?php

namespace admin\modules\items\controllers;

use Yii;
use common\models\ItemForCompany;
use admin\modules\items\models\ItemForSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ItemsHasGroups;
use common\models\Items;

/**
 * ItemForController implements the CRUD actions for ItemForCompany model.
 */
class ItemForController extends Controller
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
                    'delete'    => ['POST'],
                    'find-item' => ['POST'],
                    'add-item-to-group' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ItemForCompany models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemForSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemForCompany model.
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
     * Creates a new ItemForCompany model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model      = new ItemForCompany();

        if(Yii::$app->session->get('Rules')['comp_id'] == 1){ // ไม่ให้เปลี่ยนตาม URL หากไม่ใช่ ginolr
            $models     = ItemForCompany::find()
                        ->where(['comp_id' => Yii::$app->request->get('company') ? Yii::$app->request->get('company') : Yii::$app->session->get('Rules')['comp_id']])
                        ->all();
        }else{
            $models     = ItemForCompany::find()
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->all();
        }

        // Item ที่จัดกลุ่มซ้ำกัน
        $duplicate  = ItemForCompany::find()->select('item')->groupBy('item')->having(['>','count(*)',1])->count();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model'     => $model,
            'models'    => $models,
            'duplicate' => $duplicate
        ]);
    }

    /**
     * Updates an existing ItemForCompany model.
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
     * Deletes an existing ItemForCompany model.
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
     * Finds the ItemForCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemForCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemForCompany::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }


    public function actionFindItem(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 2 ? '' : 5;
         
        $query          = Items::find()
                        ->where(['or',
                            ['like','master_code',explode(' ',trim($data->search))],
                            ['like','description_th',explode(' ',trim($data->search))],
                            ['like','Description',explode(' ',trim($data->search))],
                            ['like','barcode',explode(' ',trim($data->search))]
                        ])
                        ->andWhere(['status' => 1])
                        ->limit($limit)
                        ->all();

        $obj = [];
        foreach ($query as $key => $model) {
          
            $obj[] = (Object)[
                'id'        => $model->id,
                'name'      => $model->description_th,
                'name_en'   => $model->Description,
                'code'      => $model->master_code,
                'exists'    => []
            ];
        }

        return $this->asJson([
            'limit'     => $limit ? $limit : 'unlimited',
            'data'      => $obj,
            'search'    => $data->search
        ]);
    }



    public function actionAddItemToGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status             = 200;
        $message            = Yii::t('common','Success');

        if(Yii::$app->session->get('Rules')['comp_id']!=1){
            $status         = 403;
            $message        = Yii::t('common','Forbidden');
        }else{

            if(ItemForCompany::find()->where(['item' => $data->id, 'comp_id' => $data->comp_id])->exists()){
                $status         = 403;
                $message        = Yii::t('common','Already exists');
            }else{

                $model              = new ItemForCompany();
                $item               = Items::findOne($data->id);
                $model->item        = $data->id;
                $model->name        = $item->alias ? $item->alias : $item->description_th;
                $model->create_date = date('Y-m-d H:i:s');
                $model->comp_id     = $data->comp_id;
                $model->user_id     = Yii::$app->user->identity->getId();

                if($model->save()){
                    $status     = 200;                
                }else{
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                }
            }
        }
        
        return $this->asJson([
            'id'        => $data->id,
            'status'    => $status,
            'message'   => $message
        ]);

    }

    public function actionRemoveItemFromGroup(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status         = 200;
        $model          = ItemForCompany::find()
                        ->where(['item' => $data->id])
                        ->andWhere(['comp_id' => $data->comp_id])
                        ->one();

        if($model!==null){
            if($model->delete()){
                $status = 200;
            }else{
                $status = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }

        return $this->asJson([
            'id'        => $data->id,
            'status'    => $status
        ]);

    }
}
