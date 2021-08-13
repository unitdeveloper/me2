<?php

namespace admin\controllers;

use Yii;
use common\models\SetupNoSeries;
use admin\models\SetupNosSearch;

use common\models\NumberSeries;


use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SetupnosController implements the CRUD actions for SetupNoSeries model.
 */
class SetupnosController extends Controller
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
    public function actionAjaxFindSeries()
    {
        $query = NumberSeries::find()
        ->where(['table_name' => $_POST['table']['table']])
        ->andWhere(['field_name' => $_POST['table']['field']])
        ->andWhere(['cond' => $_POST['table']['cond']]);


        $data = [
            'id' => NULL,
            'code' => '',
            'table_name' => $_POST['table']['table'],
            'field_name' => $_POST['table']['field'],
            'cond' => $_POST['table']['cond']
        ];

        if($query->exists())
        {
            // $model = NumberSeries::find()
            // ->where(['table_name' => $_POST['table']['name']])
            // ->andWhere(['field_name' => $_POST['table']['field']])
            // ->andWhere(['cond' => $_POST['table']['cond']]) 
            // ->one();
            $model = $query->one();

            $data = ['id' => $model->id,'code' => $model->starting_char];
            

        }else {
            
        }
        return json_encode($data);
    }


    /**
     * Lists all SetupNoSeries models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SetupNosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SetupNoSeries model.
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
     * Creates a new SetupNoSeries model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SetupNoSeries();

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

    /**
     * Updates an existing SetupNoSeries model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SetupNoSeries model.
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
     * Finds the SetupNoSeries model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SetupNoSeries the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SetupNoSeries::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
