<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use common\models\Promotions;
use common\models\PromotionsItemGroup;
use admin\modules\SaleOrders\models\PromotionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PromotionsController implements the CRUD actions for Promotions model.
 */
class PromotionsController extends Controller
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
     * Lists all Promotions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromotionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Promotions model.
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

    /**
     * Creates a new Promotions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promotions();

        if ($model->load(Yii::$app->request->post())) {

            $model->create_by = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s');
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            //$model->approve_id = $models->id;

            if($model->status==1){
                $model->status==2;
                Yii::$app->session->addFlash('success', Yii::t('common','Send Approve'));
            }else{
                $model->status==1;
                Yii::$app->session->addFlash('info', Yii::t('common','Saved'));
            }

            $model->save();

            //Create Approve
            $models = new \common\models\Approval();

            $models->source_id      = $model->id;
            $models->detail         = $model->item_group;
            $models->table_name     = 'promotions';
            $models->field_name     = 'item_group';
            $models->field_data     = $model->sale_amount;
            $models->ip_address     = $_SERVER['REMOTE_ADDR'];
            $models->document_type  = 'Promotions';
            $models->sent_by        = $model->create_by;
            $models->sent_time      = $model->create_date;
            //$models->approve_date   = date('Y-m-d H:i:s');
            //$models->approve_by     = Yii::$app->user->identity->id;
            $models->comp_id        = Yii::$app->session->get('Rules')['comp_id'];
            $models->approve_status = ($model->status==1)? '0' : '2';
            $models->gps            = '';
            $models->balance        = $model->discount;

            if(!$models->save())
            {
                print_r($models->getErrors());
            }

            $model->updateAttributes(['approve_id' => $models->id]);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        

        return $this->render('create', [
            'model' => $model,
            
        ]);
    }

    /**
     * Updates an existing Promotions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            //Yii::$app->session->addFlash('warning', json_encode($model->status,JSON_UNESCAPED_UNICODE));
            //Create Approve
            $approve = \common\models\Approval::findOne(['table_name' => 'promotions','source_id' => $model->id]);
            if ($approve!=null){

                $approve->source_id     = $model->id;
                $approve->detail        = $model->item_group;
                $approve->table_name    = 'promotions';
                $approve->field_name    = 'item_group';
                $approve->field_data    = $model->sale_amount;
                $approve->ip_address    = $_SERVER['REMOTE_ADDR'];
                $approve->document_type = 'Promotions';
                $approve->sent_by       = $model->create_by;
                $approve->sent_time     = $model->create_date;
                // $approve->approve_date  = date('Y-m-d H:i:s');
                // $approve->approve_by    = Yii::$app->user->identity->id;
                $approve->comp_id       = Yii::$app->session->get('Rules')['comp_id'];
                $approve->approve_status= ($model->status==1)? '0' : '2';
                $approve->gps           = '';
                $approve->balance       = $model->discount;

                if(!$approve->save())
                {
                    print_r($approve->getErrors());
                }
                $model->approve_id = $approve->id;
                    
            }else{
            
                $models = new \common\models\Approval();

                $models->source_id      = $model->id;
                $models->detail         = $model->item_group;
                $models->table_name     = 'promotions';
                $models->field_name     = 'item_group';
                $models->field_data     = $model->sale_amount;
                $models->ip_address     = $_SERVER['REMOTE_ADDR'];
                $models->document_type  = 'Promotions';
                $models->sent_by        = $model->create_by;
                $models->sent_time      = $model->create_date;
                // $models->approve_date   = date('Y-m-d H:i:s');
                // $models->approve_by     = Yii::$app->user->identity->id;
                $models->comp_id        = Yii::$app->session->get('Rules')['comp_id'];
                $models->approve_status = ($model->status==1)? '0' : '2';
                $models->gps            = '';
                $models->balance        = $model->discount;

                if(!$models->save())
                {
                    print_r($models->getErrors());
                }
                $model->approve_id = $models->id;
            }

            $model->create_by = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s');
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            

            if($model->status==1){
                $model->status==2;
                Yii::$app->session->addFlash('success', Yii::t('common','Send Approve'));
            }else{
                $model->status==1;
                Yii::$app->session->addFlash('info', Yii::t('common','Saved'));
            }

            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionGetItemList($id){
        $model = $this->findModel($id);

        $query = PromotionsItemGroup::find()
        ->where(['name' => $model->item_group])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all();

        if ($query != null){
            $data = [];
            foreach ($query as $key => $value) {
                $data[] = (Object)[
                    'id' => $value->id,
                    'name' => $value->name,
                    'description' => $value->description,
                    'item' => $value->item,
                    'item_code' => $value->items->master_code,
                    'item_name' => $value->items->description_th,
                    'status' => $value->status
                ];
            }
            return json_encode([
                'status' => 200,
                'id' => $model->id,
                'name' => $model->item_group,
                'data' => [
                    $data
                ]
            ]);
        }else {
            return json_encode([
                'status' => 404
            ]);
        }
        
    }

    /**
     * Deletes an existing Promotions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $approveId = $model->approve_id;

        $transaction = Yii::$app->db->beginTransaction();
        try {

            if($this->findModel($id)->delete()){
                $approve = \common\models\Approval::findOne($approveId);
                if($approve!=null){
                    $approve->delete();
                }
                
                $transaction->commit();
                Yii::$app->session->addFlash('success', Yii::t('common','Deleted'));
            }else{
                $transaction->rollBack();
            }

            

            return $this->redirect(['index']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * Finds the Promotions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Promotions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Promotions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
