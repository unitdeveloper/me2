<?php

namespace admin\modules\customers\controllers;

use Yii;
use yii\db\Expression;

use common\models\Customer;
use common\models\SalesHasCustomer;
use common\models\SalesPeople;
use common\models\CustomerHasGroup;
use admin\modules\customers\models\SearchCustomer;
use admin\modules\customers\models\MoveCustomer;
use admin\modules\customers\models\AjaxCustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\web\Response;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

use admin\modules\apps_rules\models\SysRuleModels;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


/**
 * CustomerController implements the CRUD actions for customer model.
 */
class MoveController extends Controller
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
                    'index-change' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all customer models.
     * @return mixed
     */
    public function actionIndex($status = false)
    {
        $searchModel = new MoveCustomer();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);   

        
        if(!in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){
            Yii::$app->session->setFlash('warning', Yii::t('common','You don\'t have permission.'));
            return $this->redirect(['/customers/customer/']);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexChange(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

       
        $transaction    = Yii::$app->db->beginTransaction();
        try {

            foreach ($data->customers as $key => $custId) {
                $Cust = Customer::findOne($custId);
 
                // เพื่อลบรายการที่ถูกจัดกลุ่มไว้ก่อนหน้าให้หมด
                $owner          = SalesHasCustomer::findOne(['cust_id' => $Cust->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                if ($owner!=null) SalesHasCustomer::deleteAll(['cust_id' => $Cust->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);                
                

                $owner_sale = [];
                foreach ($data->sales as $key => $saleId) {
                    // 
                    $Sale          = SalesPeople::findOne($saleId);
                    if ($Sale != null){
                        $owner_sale[]       = $Sale->code;
                    }

                    $HasC                   = new SalesHasCustomer();
                    $HasC->type_of          = 'customer';
                    $HasC->sale_id          = $Sale->id;
                    $HasC->cust_id          = $Cust->id;
                    $HasC->customer_group   = $Cust->id;
                    $HasC->comp_id          = $data->comp_id;

                    if(!$HasC->save()){
                        $transaction->rollBack();

                        return json_encode([
                            'status' => 500,
                            'message' => Yii::t('common','Error Sale has customer'),
                            'suggestion' => json_encode($HasC->getErrors(),JSON_UNESCAPED_UNICODE)
                        ]);   
                    }
                }
               
                $Cust->comp_id         = $data->comp_id;
                $Cust->owner_sales     = implode(',',$owner_sale);   

                if(!$Cust->save(false)){
                    $transaction->rollBack();

                    return json_encode([
                        'status' => 501,
                        'message' => Yii::t('common','Error Customer'),
                        'suggestion' => json_encode($Cust->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);   
                }
               
            } 

            $transaction->commit();  
            return $this->asJson([
                'status' => 200,
                'count' => count($data->customers),
                'message' => Yii::t('common','Success'),
                'suggestion' => ''
            ]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
                'status' => 500,
                'message' => Yii::t('common','Error'),
                'suggestion' => Yii::t('common','{:e}',[':e' => $e]),
            ]);   
                
        }
        
        
    }
 

    /**
     * Displays a single customer model.
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
     * Creates a new customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
            
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->create_date     = date('Y-m-d H:i:s');

            $model->owner_sales     = implode(',',$_POST['Customer']['owner_sales']); 

            $model->logo    = $model->upload($model,'logo');
            $model->photo   = $model->upload($model,'photo');

            
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    static function createHasCustomer($obj){
        $obj        = (Object)$obj;
        $id         = $obj->model->id;
        $model      = $obj->model;
        $owner_sale = [];
        $groups     = $obj->model->customer_group;
        
   
        $Exists = CustomerHasGroup::find()->where(['customer_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists();
        if($Exists){
            CustomerHasGroup::deleteAll(['customer_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        }
        if ($groups){
          
            foreach($groups as $key => $cGroup){
                
                $cHasGroup              = new CustomerHasGroup();
                $cHasGroup->customer_id = $id;
                $cHasGroup->group_id    = $cGroup;
                $cHasGroup->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
                $cHasGroup->save();
                
            }
        }



        $owner  = SalesHasCustomer::findOne(['cust_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if ($owner!=null){
            //clear
            SalesHasCustomer::deleteAll(['cust_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);                
        }

        
        if($obj->sales){
            foreach ($obj->sales as $key => $sale) {

                $Sales                  = SalesPeople::findOne($sale);

                if ($Sales != null){
                    $owner_sale[]       = $Sales->code;
                }

                $HasC                   = new SalesHasCustomer();
                $HasC->type_of          = 'customer';
                $HasC->sale_id          = $sale;
                $HasC->cust_id          = $model->id;
                $HasC->customer_group   = $model->id;
                $HasC->comp_id          = $model->comp_id;

                if(!$HasC->save()){
                    Yii::$app->getSession()->addFlash('warning',json_encode($HasC->getErrors(),JSON_UNESCAPED_UNICODE));   
                }
            }
        }

        return $owner_sale;
    }

    /**
     * Updates an existing customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
            
        }
        if ($model->load(Yii::$app->request->post())) {

            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];

            $owner_sale     =   self::createHasCustomer([
                                    'model' => $model,
                                    'sales' => Yii::$app->request->post('Customer')['owner_sales']
                                ]);

            $model->owner_sales     = implode(',',$owner_sale);   

            $model->credit_limit    = str_replace(',', '', $model->credit_limit);

            $model->logo    = $model->upload($model,'logo');
            $model->photo   = $model->upload($model,'photo');
            
            
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
            //return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = \common\models\SaleHeader::findOne(['customer_id' => $id]);

        if ($model!=null){
            
            Yii::$app->getSession()->setFlash('warning','<i class="fab fa-connectdevelop"></i> '.Yii::t('common','This deletion is not allowed because there are any transactions.')); 
        }else{
            if ($this->findModel($id)->delete()) {
                //Yii::$app->getSession()->addFlash('success',' '.Yii::t('common','Delete')); 
                Yii::$app->session->setFlash('success', '<i class="fas fa-thumbs-up"></i> '.Yii::t('common','Success'));
            }
        }
        

        return $this->redirect(['index']);
    }

    /**
     * Finds the customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne(['id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


 
 
}
