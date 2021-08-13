<?php

namespace admin\modules\measure\controllers;

use Yii;
use common\models\Unitofmeasure;
use admin\modules\measure\models\SearchMeasure;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MeasureController implements the CRUD actions for unitofmeasure model.
 */
class MeasureController extends Controller
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
     * Lists all unitofmeasure models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchMeasure();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single unitofmeasure model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new unitofmeasure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Unitofmeasure();

        if ($model->load(Yii::$app->request->post()) ) {
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
            return $this->redirect(['index', 'id' => $model->UnitCode]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing unitofmeasure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Unitofmeasure::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->UnitCode]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing unitofmeasure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the unitofmeasure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return unitofmeasure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Unitofmeasure::find()
        ->where(['id' => $id])
        ->andWhere(['or',
            ['<>','comp_id',null],
            ['<>','comp_id',1]
        ])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionGetMeasure()
    {
        $models = Unitofmeasure::find()
        ->where(['or',
            ['comp_id' => null],
            ['comp_id' => 1],
            ['comp_id' => Yii::$app->session->get('Rules')['comp_id']]
        ])
        ->all();
        $data = [];
        foreach ($models as $key => $model) {
            $data[] = [
                'id' => $model->id,
                'name' => $model->UnitCode,
                'desc' => $model->Description
            ];
        }
        return json_encode([
            'list' => $data,
        ]);
    }
}
