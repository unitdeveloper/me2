<?php

namespace admin\modules\Manufacturing\controllers;

use Yii;
use common\models\ProductionRequest;
use admin\modules\Manufacturing\models\PdrSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Company;
use admin\models\Series;
use common\models\WarehouseHeader;

/**
 * ProductionRequestController implements the CRUD actions for ProductionRequest model.
 */
class ProductionRequestController extends Controller
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
                    'update-ajax' => ['POST'],
                    'get-production' => ['POST'],
                    'cutting-consumption' => ['POST'],
                    'delete-production' => ['POST'],
                    'output-craft' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ProductionRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PdrSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductionRequest model.
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

    public function actionDeleteProduction(){
        
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = '';

        if(!$this->findModel($data->id)->delete()){
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
        ]);

    }

    /**
     * Creates a new ProductionRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $comp   = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $model  = new ProductionRequest();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'id'    => 0,
            'item'  => Yii::$app->request->get('item'),
            'logo'  => $comp->logo,
            'qty'   => Yii::$app->request->get('qty')?: 0,
            'no'    => Series::gen('production_request','no','all')
        ]);
    }

    /**
     * Updates an existing ProductionRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $comp   = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $model  = $this->findModel($id);

       
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'item'  => $model->item,
            'id'    => $id, 
            'logo'  => $comp->logo,
            'qty'   => $model->quantity,
            'no'    => $model->no
        ]);
    }

    public function actionUpdateAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $item           = $data->item;
        $id             = $data->id;
        $status         = 200;
        $message        = '';
        $newId          = 0;
        $refresh        = false;

        if($id==0){     // Create
            $model = new ProductionRequest;
            
            $model->create_date = date('Y-m-d');
            $model->item        = $item;
            $model->quantity    = $data->qty;
            $model->remark      = $data->remark;
            $model->no          = $data->no;
            $model->posting_date= $data->date;
            $model->request_date= $data->until;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            $refresh            = true;
            if(!$model->save()){
                $status         = 500;
                $message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
           
        }else {         // Update
            $model = ProductionRequest::findOne($data->id);
            $refresh            = false;

            if($model == null){
                $model = new ProductionRequest;
                $refresh        = true;
            }

            $model->create_date = date('Y-m-d');
            $model->item        = $item;
            $model->quantity    = $data->qty;
            $model->remark      = $data->remark;
            $model->no          = $data->no;
            $model->posting_date= $data->date;
            $model->request_date= $data->until;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            
            if(!$model->save()){
                $status         = 500;
                $message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
           
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'id'        => $model->id,
            'qty'       => $model->quantity,
            'refresh'   => $refresh
        ]);
       
    }

    /**
     * Deletes an existing ProductionRequest model.
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

    public function actionRequest(){
        // Create 
        return $this->render('request');
    }
 

    public function actionRequestPrint($id){
        $comp   = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $model  = $this->findModel($id);

        return $this->render('update', [
            'model' => $model,
            'id'    => $id, 
            'logo'  => $comp->logo,
            'qty'   => Yii::$app->request->get('qty')?: 0,
            'no'    => Series::gen('production_request','no','all')
        ]);
    }

 
    /**
     * Finds the ProductionRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductionRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductionRequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionGetProduction(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp_id        = Yii::$app->session->get('Rules')['comp_id'];
        $raws           = [];

        $query          = ProductionRequest::find()
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->orderBy(['LENGTH(no) , no' => SORT_DESC])
                        ->all();

        foreach ($query as $key => $model) {
            $raws[] = [
                'id'        => $model->id,
                'no'        => $model->no,
                'date'      => date('Y-m-d', strtotime($model->posting_date)),
                'itemCode'  => $model->items->master_code,
                'itemId'    => $model->item,
                'itemName'  => $model->items->description_th,
                'cons'      => $model->consumption ?: 0,
                'status'    => $model->status,
                'inv'       => $model->items->qtyAfter,
                'qty'       => $model->quantity
            ];
        }

        return json_encode([
            'status' => 200,
            'raws' => $raws,
            'comp_id' => $comp_id
        ]);

    }

    public function actionCuttingConsumption(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $model          = ProductionRequest::findOne($data->id);

        $WH             = new WarehouseHeader();
        $consumption    = $WH->cuttingConsumption($model);
       


        return json_encode([
            'status'        => 200,
            'consumption'   => $consumption
        ]);
    }

    public function actionOutputCraft(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $model          = ProductionRequest::findOne($data->id);

        if($model != null){

        
            $WH             = new WarehouseHeader();
            $consumption    = $WH->cuttingConsumption($model);
        }else{
            $status = 404;
        }


        return json_encode([
            'status'        => 200,
            'consumption'   => $consumption
        ]);
    }
    
}
