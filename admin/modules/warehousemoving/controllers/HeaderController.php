<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use admin\modules\warehousemoving\models\HeaderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\TransportList;
use common\models\SaleHeader;

/**
 * HeaderController implements the CRUD actions for WarehouseHeader model.
 */
class HeaderController extends Controller
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
                    'delete-rc' => ['POST'],
                    'update-box' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all WarehouseHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HeaderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WarehouseHeader model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $query = WarehouseMoving::find()
        ->where(['source_id' => $id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new WarehouseHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // $model = new WarehouseHeader();

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // } else {
        //     return $this->render('create', [
        //         'model' => $model,
        //     ]);
        // }
        //echo "<script> alert('Disalbed Create function()'); window.location.href='index.php?r=warehousemoving/header/index';</script>";
        Yii::$app->getSession()->addFlash('warning','Disalbed Create function()');
        return $this->redirect(['index']);

    }

    /**
     * Updates an existing WarehouseHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $SaleHeader = SaleHeader::findOne($model->SourceDocNo);
            $SaleHeader->ship_date = $model->ship_date;
            $SaleHeader->save();


            if(Yii::$app->request->isAjax){
                $transport= TransportList::findOne($model->gps);
                $customer = \common\models\Customer::findOne($SaleHeader->customer_id);
                $customer->text_comment = $model->remark;
                $customer->text_remark  = $model->comment;
                //$customer->transport    = $model->Description;
                $customer->transport    = $transport->name;
                $customer->ship_address = $model->address;

                $model->Description     = $transport->name;
                $model->transport_id    = $transport->id;
                $model->save();

                if($customer->save(false)){

                    return json_encode([                   
                        'status' => 200,
                        'message' => 'done',
                        'value' => $model->id,
                    ]);

                }else {

                    return json_encode([                   
                        'status' => 500,
                        'message' => $customer->getErrors(),
                        'value' => $SaleHeader->customer_id,
                    ]);

                }

                
                //return $this->renderAjax('_view',['model' => $model]);

            }else {

                return $this->redirect(['view', 'id' => $model->id]);

            }

           
        } else {

            if(Yii::$app->request->isAjax) {
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WarehouseHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionDeleteRc($id)
    {
        if(Yii::$app->user->identity->id==1){
            if(WarehouseMoving::deleteAll(['source_id' => $id])){
                if($this->findModel($id)->delete()){
                    Yii::$app->getSession()->addFlash('success','Deleted');
                }
            }
        }else{
            Yii::$app->getSession()->addFlash('warning','Disalbed Delete function()');
        }
       
        
        
        return $this->redirect(['/Purchase/order']);
        
    }

    
    public function actionDelete($id)
    {

        if(Yii::$app->user->identity->id==1){
            if(WarehouseMoving::deleteAll(['source_id' => $id])){
                $this->findModel($id)->delete();
            }
        }else{
            Yii::$app->getSession()->addFlash('warning','Disalbed Delete function()');
        }
        // return $this->redirect(['index']);
        //echo "<script> alert('Disalbed delete function()'); window.location.href='index.php?r=warehousemoving/header/index';</script>";
        //
        return $this->redirect(['index']);
        //return $this->redirect(['index']);
    }

    public function actionPrint($id){
        return $this->render('print');
    }


    public function actionUpdateBox(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200; 

        $model = $this->findModel($data->id);
        $model->boxs = $data->boxs;
        
        if($model->save(false)){
            $status = 200; 
        }else{
            $status = 500; 
        }

        return json_encode([
            'status' => $status,
            'boxs'  => $data->boxs
        ]);
    }


    public function actionUpdateTransport(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200; 

        $model = $this->findModel($data->id);
        $model->transport_id = $data->transport;
        
        if($model->save(false)){
            $status = 200; 
        }else{
            $status = 500; 
        }

        return json_encode([
            'status' => $status,
            'transport'  => $data->transport * 1
        ]);
    }

    /**
     * Finds the WarehouseHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WarehouseHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WarehouseHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
