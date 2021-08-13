<?php

namespace admin\modules\accounting\controllers;

use Yii;
use common\models\ViewInvoiceLine;
use admin\modules\accounting\models\InvLineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvLineController implements the CRUD actions for ViewInvoiceLine model.
 */
class InvLineController extends Controller
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
     * Lists all ViewInvoiceLine models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ViewInvoiceLine model.
     * @param integer $id
     * @param string $posted
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $posted)
    {
        Yii::$app->session->setFlash('warning', Yii::t('common','This function is disabled.'));
        return $this->redirect(['index']);

        // return $this->render('view', [
        //     'model' => $this->findModel($id, $posted),
        // ]);
    }

    /**
     * Creates a new ViewInvoiceLine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->session->setFlash('warning', Yii::t('common','This function is disabled.'));
        return $this->redirect(['index']);


        // $model = new ViewInvoiceLine();

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id, 'posted' => $model->posted]);
        // }

        // return $this->render('create', [
        //     'model' => $model,
        // ]);
    }

    /**
     * Updates an existing ViewInvoiceLine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $posted
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $posted)
    {
        Yii::$app->session->setFlash('warning', Yii::t('common','This function is disabled.'));
        return $this->redirect(['index']);

        // $model = $this->findModel($id, $posted);

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id, 'posted' => $model->posted]);
        // }

        // return $this->render('update', [
        //     'model' => $model,
        // ]);
    }

    /**
     * Deletes an existing ViewInvoiceLine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $posted
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $posted)
    {
        Yii::$app->session->setFlash('warning', Yii::t('common','This function is disabled.'));
        return $this->redirect(['index']);

        // $this->findModel($id, $posted)->delete();

        // return $this->redirect(['index']);
    }

    /**
     * Finds the ViewInvoiceLine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $posted
     * @return ViewInvoiceLine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $posted)
    {
        if (($model = ViewInvoiceLine::findOne(['id' => $id, 'posted' => $posted])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
