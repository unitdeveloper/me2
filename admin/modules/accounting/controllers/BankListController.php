<?php

namespace admin\modules\accounting\controllers;

use Yii;
use common\models\BankList;
use admin\modules\accounting\models\BankListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\UploadedFile;

/**
 * BankListController implements the CRUD actions for BankList model.
 */
class BankListController extends Controller
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
     * Lists all BankList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxView($id){

        $model = $this->findModel($id);

        
        $data = [
                 
                'id' => $model->id,
                'name'  => $model->name,
                'desc'  => $model->description,
                'img'  => $model->imageFile,
                 
        ];


        return json_encode($data);
    }

    /**
     * Displays a single BankList model.
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
     * Creates a new BankList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BankList();

        if ($model->load(Yii::$app->request->post())) {

            if(UploadedFile::getInstance($model, 'imageFile') != NULL){
                $model->imageFile = $model->upload($model,'imageFile');
            }
            //$model->imageFile = UploadedFile::getInstance($model, 'imageFile');



            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            
            //$model->upload($model,'imageFile');
                 
            $model->save(false);
            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BankList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            //$file = UploadedFile::getInstance($model, 'imageFile');
            //$model->imageFile = UploadedFile::getInstance($model, 'imageFile');

             
            if(UploadedFile::getInstance($model, 'imageFile') != NULL){
                $model->imageFile = $model->upload($model,'imageFile');
            }
                 
            $model->save(false);
             

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing BankList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->comp_id== Yii::$app->session->get('Rules')['comp_id']){
            $this->findModel($id)->delete();
        }else{
            Yii::$app->session->setFlash('error', Yii::t('common','You don\'t have permission to delete.'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the BankList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BankList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BankList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
