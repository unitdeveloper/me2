<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use common\models\PromotionsItemGroup;
use common\models\Items;
use admin\modules\SaleOrders\models\PromotionGroupSearch;
use admin\modules\SaleOrders\models\PromotionListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PromotionsItemGroupController implements the CRUD actions for PromotionsItemGroup model.
 */
class PromotionsItemGroupController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PromotionsItemGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromotionGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewName($name)
    {
        $model = PromotionsItemGroup::find()
        ->where(['name' => $name])
        ->one();

        $searchModel = new PromotionListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['name' => $model->name]);


        return $this->render('view-name', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }


    /**
     * Displays a single PromotionsItemGroup model.
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
     * Creates a new PromotionsItemGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PromotionsItemGroup();

        if ($model->load(Yii::$app->request->post())) {

            $item_id = Yii::$app->request->post('item_id');
            if ($item_id){
                foreach ($item_id as $key => $items) {
                    $item = Items::findOne($items);
                    if ($item != null){
                        
                        $model->item = $item->id;
                        $model->status = 1;
                        $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
                        $model->save();

                    }
                }
            }else{
                return $this->redirect(['create']);
            }
            return $this->redirect(['view-name', 'name' => $model->name]);
            //return $this->redirect(['view', 'id' => $model->id]);
        }

        $searchModel = new PromotionListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['name' => $model->name]);

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Updates an existing PromotionsItemGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = PromotionsItemGroup::findOne($id);
        if ($model !== null) {             
            //$model = $this->findModel($id);
            
            if ($model->load(Yii::$app->request->post())) {

                

                $item_id = Yii::$app->request->post('item_id');
                // $item_code = Yii::$app->request->post('item_code');
                // $item_name = Yii::$app->request->post('item_name');
                // var_dump(array_combine($item_id , $item_code));
                if ($item_id){
                    foreach ($item_id as $key => $items) {
                        $item = Items::findOne($items);
                        if ($item != null){
                            
                            $Cmodel = new PromotionsItemGroup();
                            $Cmodel->name = $model->name;
                            $Cmodel->description = $model->description;
                            $Cmodel->item = $item->id;
                            $Cmodel->status = 1;
                            $Cmodel->comp_id = Yii::$app->session->get('Rules')['comp_id'];
                            if(!$Cmodel->save()){
                                var_dump($Cmodel->getErrors());
                            }
                            
                        
                        }             

                    }
                }

                $query = PromotionsItemGroup::find()
                ->where(['name' => $model->getOldAttribute('name')])
                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();

                if($query != null){
                    foreach ($query as $key => $models) {                                    
                        $models->name           = $model->name;
                        $models->description    = $model->description;
                        if(!$models->save()){
                            echo $model->getError();
                        }
                    }
                }

                $model->status = 1;
                $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
                $model->save();

                return $this->redirect(['view-name', 'name' => $model->name]);
                //return $this->redirect(['view', 'id' => $model->id]);
            
            }
        

            $searchModel = new PromotionListSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['name' => $model->name]);

            return $this->render('update', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        }else {
            // Create
            $model = new PromotionsItemGroup();

            if ($model->load(Yii::$app->request->post())) {
                $item_id = Yii::$app->request->post('item_id');
                if ($item_id){
                    foreach ($item_id as $key => $items) {
                        $item = Items::findOne($items);
                        if ($item != null){
                            
                            $model->item = $item->id;
                            $model->status = 1;
                            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
                            $model->save();

                        }
                    }
                }else{
                    return $this->redirect(['create']);
                }
                return $this->redirect(['view-name', 'name' => $model->name]);
            }
        }
    }

    /**
     * Deletes an existing PromotionsItemGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $query = PromotionsItemGroup::deleteAll([
                'AND','name = :name', 'description = :description',
                'comp_id = :comp'
            ],
            [
                ':name' => $model->name,
                ':description' => $model->description,
                ':comp' => Yii::$app->session->get('Rules')['comp_id']
            ]
        );         

        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionDeleteLine($id){
        $model = $this->findModel($id);

        if($model->delete()){
            return json_encode([
                'status' => 200
            ]);
        }else {
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }
        
    }


    /**
     * Finds the PromotionsItemGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PromotionsItemGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PromotionsItemGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
