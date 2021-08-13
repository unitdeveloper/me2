<?php

namespace admin\modules\customers\controllers;

use Yii;
use common\models\CustomerGroups;
use common\models\CustomerHasGroup;
use common\models\Customer;
use admin\modules\customers\models\GroupsSearch;
use admin\modules\customers\models\HasGorupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GroupsController implements the CRUD actions for CustomerGroups model.
 */
class GroupsController extends Controller
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
     * Lists all CustomerGroups models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerGroups model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new HasGorupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['group_id' => $model->id]);


        $dataProviders = new \yii\data\ArrayDataProvider([
            'key'=>'id',
            'allModels' => $model->salepeople,
            'sort' => [
                'attributes' => ['sale_id', 'customer_group'],
             ],
            'pagination' => false,
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProviders' => $dataProviders
        ]);
    }

    /**
     * Creates a new CustomerGroups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CustomerGroups();

        if ($model->load(Yii::$app->request->post())) {

            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();

            self::createHasGroup([
                'model' => $model,
                'customers' => Yii::$app->request->post('customer_id') 
            ]);
            
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $searchModel = new HasGorupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['group_id' => $model->id]);

        return $this->render('create', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    static function createHasGroup($obj){
        $obj        = (Object)$obj;
        $customers  = $obj->customers;
        $model      = $obj->model;

        if ($customers){
            foreach ($customers as $key => $customer) {

                $cust = Customer::findOne($customer);
                if ($cust != null){
                    
                    $Cmodel                 = new CustomerHasGroup();
                    $Cmodel->customer_id    = $cust->id;
                    $Cmodel->group_id       = $model->id;                        
                    $Cmodel->comp_id        = Yii::$app->session->get('Rules')['comp_id'];

                    if(!$Cmodel->save()){
                        var_dump($Cmodel->getErrors());
                    }

                }             

            }
        }
    }

    /**
     * Updates an existing CustomerGroups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            self::createHasGroup([
                'model' => $model,
                'customers' => Yii::$app->request->post('customer_id') 
            ]);
            
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $searchModel = new HasGorupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['group_id' => $model->id]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing CustomerGroups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(CustomerHasGroup::deleteAll(['group_id' => $id])){
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
     * Finds the CustomerGroups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerGroups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerGroups::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
