<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use common\models\SaleLine;
use common\models\SaleHeader;
use admin\modules\SaleOrders\models\OrderSearch;
use admin\modules\SaleOrders\models\LineListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use admin\models\Generater;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * OrderController implements the CRUD actions for SaleLine model.
 */
class OrderController extends Controller
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
                    'cancel-reserve' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SaleLine models.
     * @return mixed
     */
    public function actionIndex()
    {

        $myCompany  = Yii::$app->session->get('Rules')['comp_id'];
        $SaleCode   = Yii::$app->session->get('sales_id');


        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->joinwith('orderNo');
        //$dataProvider->query->joinwith('itemstb');
        $dataProvider->query->andwhere(['or',
                        ['sale_header.status'=>'Shiped'],
                        ['sale_header.status' => 'Checking'],
                        ['sale_header.status' => 'Invoiced']
                    ]);
        // Alias function  "SaleFilter"
        //if(Yii::$app->session->get('Rules')['rules_id']==3){
        $myRule = Yii::$app->session->get('Rules')['rules_id'];
        if(in_array($myRule,SysRuleModels::getPolicy('Data Access','SaleOrders','order','actionIndex','SaleFilter'))){  
            $dataProvider->query->andwhere(['sale_header.comp_id' => $myCompany,'sale_header.sales_people' => $SaleCode]);
        }else {
            $dataProvider->query->andwhere(['sale_header.comp_id' => $myCompany]);
        }
        //$dataProvider->query->OrderBy(['order_no'=>SORT_DESC]);
        
        $dataProvider->pagination->pageSize=50;       

        if(Yii::$app->request->isAjax) {
            $formdate = date('Y-m-d',strtotime($_GET['fil_from_date']));
            $todate = date('Y-m-d',strtotime($_GET['fil_to_date']));
            if($_GET['fil_from_date']!='')
            $dataProvider->query->andFilterWhere(['between', 'sale_header.order_date', $formdate,$todate]);
            if($_GET['textSearch']!=''){
                $dataProvider->query->andFilterWhere(['or',
                    ['like', 'sale_header.no', $_GET['textSearch']],
                    ['like', 'sale_header.sales_people', $_GET['textSearch']],
                    ['like', 'sale_line.item_no', $_GET['textSearch']],
                    ['like', 'sale_line.description_th', $_GET['textSearch']]]);                
                $dataProvider->pagination=false;
            }                   
            return $this->renderAjax('_ajax_index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else {
            if(isset($_GET['table_search'])){
                $textSearch = $_GET['table_search'];
                //$dataProvider->query->andFilterWhere(['like', 'sale_header.order_date', $textSearch]);
                $dataProvider->query->andFilterWhere(['or',
                        ['like', 'sale_header.no', $textSearch],
                        ['like', 'sale_header.sales_people', $textSearch],
                        ['like', 'sale_line.item_no', $textSearch],
                        ['like', 'sale_line.description', $textSearch],
                        ['like', 'sale_header.customer_id', $textSearch]
                    ]);
            }       
            //------Search Date------
            $formdate   = date('Y-m-d');
            $todate     = date('Y-m-d');
            $textSearch = '';
            if(isset($_GET['fil_from_date']))    $formdate   = date('Y-m-d',strtotime($_GET['fil_from_date']));
            if(isset($_GET['fil_to_date']))      $todate     = date('Y-m-d',strtotime($_GET['fil_to_date']));          
            if(isset($_GET['fil_from_date'])){
                if($_GET['fil_from_date']!='')
                $dataProvider->query->andFilterWhere(['between', 'sale_header.order_date', $formdate,$todate]);                
            }
            if(isset($_GET['vat_type'])){
                if($_GET['vat_type']!='')
                $dataProvider->query->andFilterWhere(['sale_header.vat_type'=> $_GET['vat_type']]);                
            }            
            //------/. Search Date ------

            $dataProvider->query->andFilterWhere(['>','sale_line.quantity_shipped',0]);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionIdentify()
    {

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
      
    }
    

    /**
     * Displays a single SaleLine model.
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
     * Creates a new SaleLine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $NoSeries = new Generater();
        //var_dump($NoSeries->GenerateNoseries('SaleOrder'));
        $model = new SaleLine();
        $SaleHeader = new SaleHeader();
        $SaleHeader->no     = $NoSeries->GenerateNoseries('SaleOrder');
        $SaleHeader->save();
        $model->type   = 'Item';

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())) {


            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'SaleHeader' => $SaleHeader,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing SaleLine model.
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

    public function actionReserved($item){

        $query =  SaleLine::find()
                    ->where(['comp_id'          => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['stock_reserve' => 1])
                    ->andWhere(['item'          => $item]);

        $dataProvider = new ActiveDataProvider([
            'query'         => $query,
            'pagination'    => false,
        ]);

        return $this->render('reserved', [
            'dataProvider'  => $dataProvider
        ]);
    }

    /**
     * Deletes an existing SaleLine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionCancelReserve($id){
        $model = $this->findModel($id);

        if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){        
            $model->stock_reserve = 0;
            if($model->save()){
                Yii::$app->session->setFlash('success', Yii::t('common','Success'));
            }            
        }else{
            Yii::$app->session->setFlash('error', Yii::t('common',"You don't have permission"));
        }

        return $this->redirect(['reserved', 'item' => $model->item]);
    }

    /**
     * Finds the SaleLine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleLine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleLine::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionLineList()
    {
        $searchModel    = new LineListSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['sale_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->pagination->pageSize=100; 
        
        return $this->render('line-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
