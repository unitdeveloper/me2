<?php

namespace admin\modules\location\controllers;

use Yii;
use common\models\Location;
use admin\modules\location\models\SearchLocation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\WarehouseMoving;

/**
 * LocationController implements the CRUD actions for location model.
 */
class LocationController extends Controller
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
     * Lists all location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchLocation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single location model.
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
     * Creates a new location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post())) {
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    public function actionGetList(){
        
        $model = Location::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all();
        $data = [];
        foreach ($model as $key => $value) {
            $data[] = [
                'id'    => $value->id,
                'code'  => $value->code,
                'name'  => $value->name
            ];
        }

        return json_encode($data);
    }

    /**
     * Updates an existing location model.
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

    public function actionGps()
    {
        return $this->render('gps');
    }

    /**
     * Deletes an existing location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        
        $CheckUsageInWharehouseMoving = WarehouseMoving::find()
        ->where(['location' => $id])
        ->one();

        if($CheckUsageInWharehouseMoving){
            Yii::$app->session->setFlash('error',Yii::t('common','You can\'t remove this location. The already in use.'));
            return $this->redirect(['view','id' => $id]);
        }else{
            $this->findModel($id)->delete();
            return $this->redirect(['index']);
        }

        
    }

    /**
     * Finds the location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
