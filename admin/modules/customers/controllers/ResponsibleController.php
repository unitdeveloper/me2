<?php

namespace admin\modules\customers\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\SalesPeople;
use common\models\Customer;
use common\models\CustomerGroups;
use common\models\SalesHasCustomer;
use admin\modules\customers\models\ResponsibleSearch;
use admin\modules\salepeople\models\HasCustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use admin\models\FunctionCenter;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use common\models\Items;

/**
 * ResponsibleController implements the CRUD actions for SalesPeople model.
 */
class ResponsibleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $fn = new FunctionCenter();
        $fn->RegisterRule();
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
     * Lists all SalesPeople models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ResponsibleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesPeople model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    

    public function actionView($id)
    {
        $model = $this->findModel($id);


   
        $dataProvider = new \yii\data\ArrayDataProvider([
            'key'=>'id',
            'allModels' => $model->myCustomer,
            'sort' => [
                'attributes' => ['name', 'code','group'],
             ],
            'pagination' => false,
        ]);
        

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SalesPeople model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesPeople();

        if ($model->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->user->identity->id;
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();

            self::createSaleHasCustomer((Object)[
                'model'     => $model,
                'customers' => Yii::$app->request->post('customer_id'),
                'groups'    => Yii::$app->request->post('group')
            ]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

       
        $searchModel = new HasCustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['sale_id' => $model->id]);
        return $this->render('create', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    static function createSaleHasCustomer($obj){

        $customers  = $obj->customers;
        $groups     = $obj->groups;
        $model      = $obj->model;
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($groups){
                foreach ($groups as $key => $group) {

                    $custg = CustomerGroups::findOne($group);
                    if ($custg != null){
                        
                        $CGroup                 = new SalesHasCustomer();
                        $CGroup->type_of        = 'group';
                        $CGroup->cust_id        = 0;
                        $CGroup->customer_group = $custg->id;
                        $CGroup->sale_id        = $model->id;                        
                        $CGroup->comp_id        = Yii::$app->session->get('Rules')['comp_id'];

                        if(!$CGroup->save()){
                            var_dump($CGroup->getErrors());
                            $transaction->rollBack();
                        }
                        $transaction->commit();
                    }             

                }
            }
                
            if ($customers){
                foreach ($customers as $key => $customer) {

                    $cust = Customer::findOne($customer);
                    if ($cust != null){
                        
                        $Cmodel                 = new SalesHasCustomer();
                        $Cmodel->type_of        = 'customer';
                        $Cmodel->cust_id        = $cust->id;
                        $Cmodel->customer_group = $cust->id;
                        $Cmodel->sale_id        = $model->id;                      
                        $Cmodel->comp_id        = Yii::$app->session->get('Rules')['comp_id'];

                        if(!$Cmodel->save()){
                            var_dump($Cmodel->getErrors());
                            $transaction->rollBack();
                        }
                        $transaction->commit();
                    }             

                }
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    /**
     * Updates an existing SalesPeople model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            
            self::createSaleHasCustomer((Object)[
                'model'     => $model,
                'customers' => Yii::$app->request->post('customer_id'),
                'groups'    => Yii::$app->request->post('group')
            ]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $searchModel = new HasCustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['sale_id' => $model->id]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Deletes an existing SalesPeople model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(SalesHasCustomer::deleteAll(['sale_id' => $id])){
                $this->findModel($id)->delete();
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the SalesPeople model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesPeople the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesPeople::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }


    public function actionGetSaleItems(){
        $request_body   = file_get_contents('php://input');
        $body           = json_decode($request_body);

        $comp           = Yii::$app->session->get('Rules')['comp_id'];
        $keys           = 'customers%2Fresponsible%2Fview&id:'.$body->id;


        if(!Yii::$app->cache->get($keys)){
            $filterItem = RcInvoiceLine::find()
                            ->joinWith('rcInvoiceHeader')
                            ->select('rc_invoice_line.item as item')
                            ->where(['rc_invoice_header.sale_id' => $body->id])
                            ->andWhere(['rc_invoice_header.comp_id' => $comp])
                            ->groupBy('rc_invoice_line.item');
            $itemList   = Items::find()->where(['IN','id',$filterItem])->orderBy(['description_th' => SORT_ASC])->all();
            $items      = [];
            foreach ($itemList as $key => $item) {
                $items[] = (Object)[
                    'id'    => $item->id,
                    'name'  => $item->description_th,
                    'code'  => $item->master_code,
                    'qty'   => RcInvoiceLine::find()
                                ->joinWith('rcInvoiceHeader')
                                ->select('rc_invoice_line.quantity as quantity')
                                ->where(['rc_invoice_header.sale_id' => $body->id])
                                ->andWhere(['rc_invoice_line.item' => $item->id])
                                ->andWhere(['rc_invoice_header.comp_id' => $comp])->sum('quantity') * 1,
                    'img'   => $item->picture
                ];
            }
            $rawData = (Object)[                 
                'items'         => $items
            ];
            Yii::$app->cache->set($keys, $rawData, 10);
        }

        return json_encode(Yii::$app->cache->get($keys));

    }
}
