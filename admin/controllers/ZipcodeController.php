<?php

namespace admin\controllers;

use Yii;
use common\models\Zipcode;
use admin\models\ZipcodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ZipcodeController implements the CRUD actions for Zipcode model.
 */
class ZipcodeController extends Controller
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
     * Lists all Zipcode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ZipcodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Zipcode model.
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
     * Creates a new Zipcode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Zipcode();

        if ($model->load(Yii::$app->request->post())) {

            $model->DISTRICT_CODE = $model->district->DISTRICT_CODE;

            if(!$model->save()){
                var_dump($model->getErrors());   
            }
            return $this->redirect(['view', 'id' => $model->ZIPCODE_ID]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Zipcode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->DISTRICT_CODE = $model->district->DISTRICT_CODE;

            $model->save();
            return $this->redirect(['view', 'id' => $model->ZIPCODE_ID]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Zipcode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();

        //return $this->redirect(['index']);
        
        Yii::$app->session->setFlash('danger', Yii::t('common','Disabled function.'));
        return $this->redirect(['/zipcode']);
       // echo '<script>/*alert("Disabled function.");*/ window.location="index.php?r=zipcode";</script>';
        
    }

    /**
     * Finds the Zipcode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Zipcode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Zipcode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
