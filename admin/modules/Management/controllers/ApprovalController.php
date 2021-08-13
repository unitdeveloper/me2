<?php

namespace admin\modules\Management\controllers;

use Yii;
use common\models\Approval;
use admin\modules\Management\models\ApprovalSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ApprovalController implements the CRUD actions for Approval model.
 */
class ApprovalController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['create', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['approve', 'reject', 'index', 'create', 'update', 'view', 'delete', 'print',],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'approve' => ['POST'],
                    'reject' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Approval models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ApprovalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = \common\models\ApproveSetup::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['enabled' => 1]);

        $table_name = [];
        foreach ($query->all() as $key => $model) {             
            $table_name[] = $model->table_name;
        }   

        $dataProvider->query->andFilterWhere(['table_name' => $table_name]);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Approval model.
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

    protected function callModel($path,$table){
        $model =  ucfirst($table); // ตัวอักษรใหญ่ตัวแรก
        return $path.'\\'.$model;
    }

    public function actionApprove($id){

        $model = $this->findModel($id);

        $model->approve_by = Yii::$app->user->identity->id;
        $model->approve_date = date('Y-m-d H:i:s');
        $model->approve_status = '1';

       
        if($model->save()){

            // Update source 
            $source = self::callModel('\common\models',$model->table_name)::findOne($model->source_id);
            $field = $model->field_name;
            // var_dump($source->$field);
            $source->status = 4;
            $source->save();

            Yii::$app->session->addFlash('success', Yii::t('common','Success'));
            return $this->render('approve',[
                'model' => $model,
                'source' => $source
            ]);
        }else{
            Yii::$app->session->addFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
            return $this->redirect(['index']);
        }
         
        
    }

    public function actionReject(){
        return $this->render('reject');
    }

    /**
     * Creates a new Approval model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Approval();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Approval model.
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
     * Deletes an existing Approval model.
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
     * Finds the Approval model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Approval the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Approval::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
