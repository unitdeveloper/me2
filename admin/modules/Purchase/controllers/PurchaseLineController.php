<?php

namespace admin\modules\Purchase\controllers;

use Yii;
use common\models\PurchaseLine;
use admin\modules\Purchase\models\PurchaseLineSearch;
use admin\modules\Purchase\models\LineListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PurchaseLineController implements the CRUD actions for PurchaseLine model.
 */
class PurchaseLineController extends Controller
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
                    'delete'        => ['POST'],
                    'change-date'   => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all PurchaseLine models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchaseLineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['purchase_header.deletion' => 0]);
        $dataProvider->query->andwhere(['purchase_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if(isset($_GET['PurchaseLineSearch']['vendor_name'])){
            $dataProvider->pagination->pageSize=100;
        }
        //$dataProvider->pagination->pageSize=100;


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PurchaseLine model.
     * @param integer $id
     * @return mixed
     */
    public function actionAngularGet($id){
      $query = PurchaseLine::find()->where(['source_id' => $id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
      
      $JSON = [];
      $i    = 0;
      foreach ($query as $key => $model) {
        $i++;

        $measureList = [];
        foreach ($model->items->itemunitofmeasures as $key => $value) {
            $measureList[] = [
                'id' => (string)$value->measure,
                'name' => $value->measures->UnitCode,
                'qty_per' => $value->qty_per_unit  * 1,
            ];
           
        }

        $JSON[]= [
            'i'             => $i,
            'id'            => $model->id,
            'source_no'     => $model->source_no,
            'type'          => $model->type,
            'item'          => $model->item,
            'item_no'       => (string)$model->items_no,
            'description'   => $model->description,
            'quantity'      => $model->quantity,
            'unitcost'      => $model->unitcost,
            'lineamount'    => $model->lineamount,
            'linediscount'  => $model->linediscount,
            'priority'      => $model->priority,
            'expected_date' => $model->expected_date,
            'measure'       => (string)$model->unit_of_measure,
            'unitofmeasure' => $measureList,
            'qty_per_unit'  => $model->items->quantity_per_unit,
            'complete_rec'  => $model->received->complete,
            'received'      => $model->received->receive
        ];
      }


      return json_encode($JSON);
    }

    public function actionPurchaseLine($id){
        $query = PurchaseLine::find()->where(['source_id' => $id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
        
        $JSON = [];
        $i    = 0;
        foreach ($query as $key => $model) {
          $i++;
  
          $measureList = [];
          foreach ($model->items->itemunitofmeasures as $key => $value) {
              $measureList[] = [
                  'id' => (string)$value->measure,
                  'name' => $value->measures->UnitCode,
                  'qty_per' => $value->qty_per_unit  * 1,
              ];
             
          }
  
          $JSON[]= [
              'i'             => $i,
              'id'            => $model->id,
              'source_no'     => $model->source_no,
              'type'          => $model->type,
              'item'          => $model->item,
              'item_no'       => (string)$model->items_no,
              'description'   => $model->description,
              'quantity'      => $model->quantity,
              'unitcost'      => $model->unitcost,
              'lineamount'    => $model->lineamount,
              'linediscount'  => $model->linediscount,
              'priority'      => $model->priority,
              'expected_date' => $model->expected_date,
              'measure'       => (string)$model->unit_of_measure,
              'unitofmeasure' => $measureList,
              'qty_per_unit'  => $model->items->quantity_per_unit
            //   'complete_rec'  => $model->received->complete,
            //   'received'      => $model->received->receive
          ];
        }
  
  
        return json_encode($JSON);
    }
    
    public function actionView($id)
    {
      if(Yii::$app->request->isAjax) {

        if(Yii::$app->request->post('ids')){
          $array = $_POST['ids'];
          $prop = '';
          foreach ($array as $key => $value) {
            $prop = $this->findModel($value['id']);

            if($prop){
              $prop->priority = $key;
              $prop->save(false);
            }

          }

        }


      }else {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
      }

    }

    /**
     * Creates a new PurchaseLine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PurchaseLine();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PurchaseLine model.
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

    /**
     * Deletes an existing PurchaseLine model.
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
     * Finds the PurchaseLine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchaseLine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchaseLine::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionList()
    {
        $searchModel = new LineListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['purchase_header.deletion' => 0]);
        $dataProvider->query->andwhere(['<>','purchase_header.status',0]);
        $dataProvider->query->andwhere(['purchase_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->pagination->pageSize=100;


        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangeDate(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

         
        $transaction = Yii::$app->db->beginTransaction();
        try {
 
            $status                 = 200;
            $message                = Yii::t('common','Success');

            $model                  = PurchaseLine::findOne($data->id);
            $model->expected_date   = isset($data->date)? date('Y-m-d', strtotime($data->date)) : date('Y-m-d');

            if(!$model->save()){
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status         = 500;
            $message        = Yii::t('common','{:e}',[':e' => $e]);
        }   

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'date'      => $model->expected_date
        ]);
    }

}
