<?php

namespace admin\modules\Itemset\controllers;

use Yii;
use common\models\Itemset;
use admin\modules\Itemset\models\ItemsetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ItemsetController implements the CRUD actions for Itemset model.
 */
class ItemsetController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all Itemset models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemsetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->pagination->pageSize=100;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Itemset model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Itemset model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Itemset();

        $model->user_id = Yii::$app->user->identity->id;
        $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Itemset model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Itemset model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    

    public function actionUpdateStatus($id){
        $model = Itemset::findOne($id);
        $status = 200;
        $message = '';

        if($model != null){
            $model->status = Yii::$app->request->post('param')['val'] == 'true' 
                                ? 1
                                : 0;
            if(!$model->save()){
                $status = 500;
                $message= json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Finds the Itemset model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Itemset the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Itemset::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
