<?php

namespace admin\modules\apps_rules\controllers;

use Yii;
use common\models\AppsRulesSetup;
use admin\modules\apps_rules\models\SetupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SetupController implements the CRUD actions for AppsRulesSetup model.
 */
class SetupController extends Controller
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
     * Lists all AppsRulesSetup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SetupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AppsRulesSetup model.
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
     * Creates a new AppsRulesSetup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AppsRulesSetup();

        if ($model->load(Yii::$app->request->post())) {

            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AppsRulesSetup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function beforeSave($id)
    {
        
        // $model = $this->findModel($id);

        // $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];

        // $model->save(false);
        // if (parent::beforeSave($insert)) {

        //     $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
             
        //     return true;
        // } else {
        //     return false;
        // }
    }

    /**
     * Deletes an existing AppsRulesSetup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AppsRulesSetup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppsRulesSetup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppsRulesSetup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
