<?php

namespace admin\modules\customers\controllers;

use Yii;
use yii\db\Expression;
use yii\data\ActiveDataProvider;

use common\models\Customer;
use common\models\SalesHasCustomer;
use common\models\SalesPeople;
use common\models\CustomerHasGroup;
use common\models\RcInvoiceHeader;
use admin\modules\customers\models\SearchCustomer;
use admin\modules\customers\models\MoveCustomer;
use admin\modules\customers\models\AjaxCustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\web\Response;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use admin\models\FunctionCenter;
use admin\modules\apps_rules\models\SysRuleModels;
use common\models\RcInvoiceLine;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


/**
 * CustomerController implements the CRUD actions for customer model.
 */
class CustomerController extends Controller
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
                    'delete'            => ['POST'],
                    'item-sale-ajax'    => ['POST'],
                    'branch-list'       => ['POST']
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
        $searchModel = new SearchCustomer();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);   
        $dataProvider->query->andFilterWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        //$dataProvider->pagination->pageSize=100; 
        // $comp       = Yii::$app->session->get('Rules')['comp_id'];

        // $key = 'customer:comp:'.$comp; // + Data uniquely referring to your search parameters
        // $cache = \Yii::$app->cache;
        // $dataProvider = $cache->get($key);
        //     if (!$dataProvider) {
        //     $dependency = \Yii::createObject([
        //         'class' => 'yii\caching\DbDependency',
        //         'sql' => 'SELECT * FROM customer WHERE comp_id ='.$comp.'',
        //     ]);            

        //     $dataProvider = $searchModel->search(Yii::$app->request->queryParams);   
        //     $cache->set($key, $dataProvider, 180, $dependency);
        // } 
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPickCustomer()
    {
        $appRule        = Yii::$app->session->get('Rules');

        //$searchModel    = new AjaxCustomerSearch();
        //$dataProvider   = $searchModel->search(Yii::$app->request->queryParams);    
        $search         = trim(Yii::$app->request->get('search'));
        
        
        $myCustomer     = \common\models\SalesHasCustomer::find()->where(['sale_id' => $appRule['sale_id']])->all();
       
        $custList       = [909];
        foreach ($myCustomer as $key => $value) {
            $custList[].= $value->cust_id;
        }

        $query          = \common\models\Customer::find();
        //$dataProvider->query->andFilterWhere(['or',['id' => 909],['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']]]);
        if (in_array($appRule['rules_id'],SysRuleModels::getPolicy('Data Access','accounting','Invoice','Customer','SearchCustomer'))) {
        // 4 Sale admin,7 Sale Director        
            $query->where(['or',
                    ['like','name',explode(' ',$search)],
                    ['like','address', explode(' ',$search)],
                    ['like','code',explode(' ',$search)]
                    //['like','province.PROVINCE_NAME', explode(' ',$_GET['search'])],
                    
                ]); 
            $query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);           
            $query->orWhere(['id' => 909]); 
        }else {
            $query->where(['or',
                ['like','name',explode(' ',$search)],
                ['like','code',explode(' ',$search)],
                //['like','province.PROVINCE_NAME', explode(' ',$_GET['search'])],
                
            ]);
            
            $query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            $query->andWhere(['IN', 'id', $custList]);
            //$dataProvider->query->andFilterWhere(['IN', 'customer.id', $custList]); 
            //$dataProvider->query->andFilterWhere(['like','customer.owner_sales',Yii::$app->session->get('Rules')['sales_id']]); 
        }        
         
        //$dataProvider->query->andFilterWhere(['customer.status' => 1]);
        //$dataProvider->query->orderby(['code' => SORT_ASC]);
        //$dataProvider->pagination->pageSize=5; 

        //$dataProvider->query->limit(1); -> not work
        //echo $appRule['rules_id'];
        $query->andWhere(['status' => 1]);
        $query->orderby(['code' => SORT_ASC]);
         
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 5],            
        ]);
        
        
        if(Yii::$app->request->isAjax) {
            return $this->renderAjax('_pickcustomer', [
                //'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else {
            return $this->redirect(['/SaleOrders/saleorder/update', 'id' => (string)$_GET['id']]);
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


    public function actionViewOnly($id)
    {
       
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return $this->render('view-only', [
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
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
    
                
                $model->create_date     = date('Y-m-d H:i:s');               

                $model->logo            = $model->upload($model,'logo');
                $model->photo           = $model->upload($model,'photo');
                $model->owner_sales     = '01';
                
                if($model->save()){                    
                    try {
                        $owners         =   self::createHasCustomer([
                            'model' => $model,
                            'sales' => Yii::$app->request->post('Customer')['owner_sales']
                        ]);
                        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                        $model->owner_sales     = implode(',',$owners);  
                        $model->save();
                    } catch (\Exception $e) {
                        Yii::$app->getSession()->addFlash('warning',json_encode(Yii::t('common','{:e}',[':e' => $e]),JSON_UNESCAPED_UNICODE));                        
                    }
                }else{
                    Yii::$app->getSession()->addFlash('error',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->addFlash('error',json_encode(Yii::t('common','{:e}',[':e' => $e]),JSON_UNESCAPED_UNICODE));    
                //var_dump(json_encode(Yii::t('common','{:e}',[':e' => $e]),JSON_UNESCAPED_UNICODE)); exit;              
            }            
            
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
        
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $Exists     = CustomerHasGroup::find()->where(['customer_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists();
            if($Exists){
                CustomerHasGroup::deleteAll(['customer_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            }
            if ($groups){
                // ถ้ามีอยู่แล้วให้ ​Update
            
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

                   

                    $HasC                   = new SalesHasCustomer();
                    $HasC->type_of          = 'customer';
                    $HasC->sale_id          = $sale;
                    $HasC->cust_id          = $model->id;
                    $HasC->customer_group   = $model->id;
                    $HasC->comp_id          = Yii::$app->session->get('Rules')['comp_id'];

                    if(!$HasC->save()){
                        Yii::$app->getSession()->addFlash('warning',json_encode($HasC->getErrors(),JSON_UNESCAPED_UNICODE));   
                        Yii::$app->getSession()->addFlash('error',json_encode($id,JSON_UNESCAPED_UNICODE));
                    }else{                        
                        $owner_sale[]       = $Sales->code;                         
                    }
                }
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->addFlash('error',json_encode(Yii::t('common','{:e}',[':e' => $e]),JSON_UNESCAPED_UNICODE)); 
            return self::render('create', [
                'model' => $model,
            ]);   
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

            // $owner = SalesHasCustomer::findOne(['cust_id' => $id]);
            // if ($owner!=null){
            //     //clear
            //     SalesHasCustomer::deleteAll(['cust_id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);                
            // }

            // foreach ($_POST['Customer']['owner_sales'] as $key => $sale) {

            //     $Sales = SalesPeople::findOne($sale);
            //     $owner_sale[] = $Sales->code;

            //     $owner = new SalesHasCustomer();
            //     $owner->type_of         = 'customer';
            //     $owner->sale_id         = $sale;
            //     $owner->cust_id         = $model->id;
            //     $owner->customer_gorup  = $model->id;
            //     $owner->comp_id         = $model->comp_id;

            //     if(!$owner->save()){
            //         Yii::$app->getSession()->addFlash('warning',json_encode($owner->getErrors(),JSON_UNESCAPED_UNICODE));   
            //     }
            // }

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
        if(Yii::$app->session->get('Rules')['comp_id'] ==1){
            if (($model = Customer::findOne(['id' => $id])) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }else{

        
            if (($model = Customer::findOne(['id' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']])) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }




    public function actionPrintShip($id)
    {

        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model   = $this->findModel($id);

        $content = $this->renderPartial('__print_ship',['model' => $model]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            'cssFile' => 'css/pdf.css',

            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'filename' => 'transport_'.$model->name.'.pdf',
            // any css to be embedded if required
            'cssInline' => '@page {margin: 20 60 0 60;} body{font-family: freesiaupc,freesia, sans-serif; font-size:20px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'transport : '.$model->name.' '],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[''],
                //'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        /* Thai Font */
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf->options['fontDir'] = array_merge($fontDirs, [
            Yii::getAlias('@webroot').'/fonts'
        ]);

        $pdf->options['fontdata'] = $fontData + [
            'saraban' => [
                'R' => 'thsarabunnew-webfont.ttf',
            ],
            'freesiaupc' => [
                'R' => 'FreesiaUPC.ttf', 
            ]
        ];

        return $pdf->render();


    }

    public function actionReadonly() {

        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        
        $searchModel = new SearchCustomer();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);   
        $dataProvider->query->andFilterWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
 
        return $this->render('readonly', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
 
    }

    public function actionMoveCustomer(){
        $searchModel = new MoveCustomer();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);   
 
        return $this->render('move-customer', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionItemSale(){        
        return $this->render('item-sale');
    }

    public function actionItemSaleCost(){        
        return $this->render('item-sale-cost');
    }

    public function actionItemSaleAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $cust       = $data->cust;
        $sale       = $data->sale;
        $comp       = Yii::$app->session->get('Rules')['comp_id'];
        $keys       = 'Item-Sale-Ajax&cust:'.$cust.'&comp:'.$comp.'&sale:'.$sale.'&fdate:'.$data->fdate.'&tdate:'.$data->tdate;
        $ReadCost   = in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','items','report','common','cost'));

        $customer   = Customer::findOne($cust);
        $SalesPeople= SalesPeople::findOne($sale);

        // Radis 
        // https://stackoverflow.com/questions/41592402/yii2-redis-as-database
        $cache      = Yii::$app->cache;
        if($cache->get($keys)){
            return json_encode([
                'status'    => 200,
                "source"    => 'cache',
                "data"      => $cache->get($keys),
            ]);
        }else {
    
            $query = RcInvoiceHeader::find()        
            ->where(['between','DATE(posting_date)',date('Y-m-d 00:00:0000', strtotime($data->fdate)), date('Y-m-d 23:59:59.9999', strtotime($data->tdate))])
            //->andWhere(['not in','id', RcInvoiceHeader::find()->select('cn_reference')->where(['is not','cn_reference', null])])
            //->andWhere(['doc_type' => 'Sale'])
            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

            $cust ? $query->andWhere(['cust_no_' => $cust]) : null;
            $sale ? $query->andWhere(['sale_id' => $sale]) : null;

            $count = $query->count();
            if($count > 5000){
                return json_encode([
                    'status'    => 403,
                    'message'   => 'ข้อมูลมากเกินไป',
                    'count'     => $count
                ]);
            }else{
                $rawData = [];
                $RcInvoiceHeader = $query->all();
                foreach ($RcInvoiceHeader as $key => $source) {    
                    $RcInvoiceLine =  RcInvoiceLine::find()->where(['source_id' => $source->id])->all();      
                    foreach ($RcInvoiceLine as $key => $IvLine) {
                        //$Cost       = $IvLine->items->pricing->conCost;
                        $rawData[]  = (Object)[
                            'id'        => $IvLine->id,
                            'no'        => $source->no_,
                            'date'      => date('Y-m-d', strtotime($source->posting_date)),
                            'saleId'    => $source->salesPeople->id,
                            'saleCode'  => $source->salesPeople->code,
                            'vat'       => $source->vat_percent,
                            'qty'       => $IvLine->quantity,
                            'price'     => $IvLine->unit_price,
                            'item'      => $IvLine->item,
                            'unit'      => $IvLine->items->UnitOfMeasure,
                            'code'      => $IvLine->items->master_code,
                            'cost'      => $IvLine->items->StandardCost,
                            // 'cost'      => $ReadCost
                            //                 ? ($Cost > 0 ? $Cost : $IvLine->items->StandardCost)
                            //                 : 0,
                            // 'RealCost'  => $ReadCost
                            //                 ? $IvLine->items->pricing->conCost
                            //                 : 0,
                            // 'stdCost'   => $ReadCost
                            //                 ? $IvLine->items->StandardCost
                            //                 : 0,
                            'name'      => $IvLine->items->id == 1414 
                                            ? 'General Ledger' 
                                            : $IvLine->code_desc_,
                            'parent'    => $source->id,
                        ];
                    }
                }

                $data = [
                    'raw'       => $rawData,
                    'custId'    => $customer != null ? $cust : '',
                    'custName'  => $customer != null ? $customer->name : '-',
                    'saleId'    => $SalesPeople != null ? $SalesPeople->id : '',
                    'saleName'  => $SalesPeople != null ? $SalesPeople->name : Yii::t('common','Every one'),
                    'fdate'     => $data->fdate,
                    'tdate'     => $data->tdate
                ];

                $cache->set($keys, $data, 1800);

                return json_encode([
                    'status'    => 200,
                    "source"    => 'api',
                    'timestamp' => date('Y-m-d H:i:s'),
                    "data"      => $cache->get($keys),
                ]);
            }
        }

    }


    public function actionCustomerListAjax(){
        $query = Customer::find()
                ->where(['or',
                    ['like', 'name', Yii::$app->request->get('q')], 
                    ['like', 'code', Yii::$app->request->get('q')]
                ])
                ->andWhere([ 'comp_id'=>Yii::$app->session->get('Rules')['comp_id']])
                ->andWHere(['status'=>'1'])
                ->orderBy(['code' => SORT_ASC])
                ->all();
                
        $data = [];
        foreach ($query as $key => $model) {
            $data[] = [
                'id' => $model->id,
                'text' => '['.$model->code.'] '.$model->name                
            ];
        }
        return json_encode(['results' => $data]);
    }


    public function actionBranchList(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $raws   = [];
        $name   = '';
        $branch = '';
        $id     = '';
        $code   = '';

        $customer   = Customer::findOne($data->cust);

        if($customer != null){
            $query = Customer::find()
                    ->where(['or', 
                        ['id'       => $customer->id], 
                        ['child'    => $customer->id]
                    ])
                    ->andWhere(['status' => 1])
                    ->orderBy(['branch' => SORT_ASC])
                    ->all();

            foreach ($query as $key => $model) {
                $raws[] = [
                    'id'        => $model->id,
                    'name'      => $model->name,
                    'branch'    => $model->branch,
                    'head'      => $model->headoffice
                ];
            }

            $id     = $customer->id;
            $code   = $customer->code;
            $name   = $customer->name;
            $branch = $customer->branch;
        }
        
        return json_encode([
            'status'    => 200,
            'name'      => $name,
            'branch'    => $branch,
            'code'      => $code,
            'id'        => $id,
            'raws'      => $raws
        ]);
    }
}
