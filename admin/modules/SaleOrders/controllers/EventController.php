<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use common\models\SaleEventHeader;
use common\models\SaleEventLine;
use admin\modules\SaleOrders\models\EventSearch;
use admin\modules\SaleOrders\models\EventlineSearch as EventLineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Items;
use yii\data\ActiveDataProvider;

use admin\models\Generater;
use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\apps_rules\models\SysRuleModels;

use common\models\ItemMystore;
use admin\modules\SaleOrders\models\PosFilter;
/**
 * EventController implements the CRUD actions for SaleEventHeader model.
 */
class EventController extends Controller
{
    /**
     * @inheritdoc
     */
    public $company;

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'find-barcode' => ['POST'],
                    'update-qty' => ['POST'],
                    'cashier' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all SaleEventHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        // Set Menu Collapse
        #$session = Yii::$app->session;
        #$session->set('collapse', 'sidebar-collapse');
        // \.Set Menu Collapse


        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['order_date' => SORT_DESC]);
        $dataProvider->query->andWhere(["comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->pagination->pageSize=10;


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SaleEventHeader model.
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
     * Creates a new SaleEventHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SaleEventHeader();
        //
        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // } else {
        //     return $this->render('create', [
        //         'model' => $model,
        //     ]);
        // }
        if($model->findMachine()==false){
            return $this->render('_machine');
        }

        $Free = $model->findEmpty();
        if(isset($Free))
        {
            return $this->redirect(['update', 'id' => $Free->id]);
        }



        $GenSeries                = new Generater();

        $model->no                = $GenSeries->getRuning('sale_event_header','vat_value','1');
        $model->user_id           = Yii::$app->user->identity->id;
        $model->comp_id           = Yii::$app->session->get('Rules')['comp_id'];

        $model->sales_people      = Yii::$app->session->get('Rules')['sales_id'];
        $model->vat_percent       = 7;

        $model->balance_befor_vat = 0;

        $date                     = date('Y-m-d');
        $date1                    = str_replace('-', '/', $date);

        $model->paymentdue        = date('Y-m-d',strtotime($date1 . "+1 days"));
        $model->ship_date         = date('Y-m-d',strtotime($date1 . "+3 days"));


        $model->order_date        = Yii::$app->session->get('workdate');
        $model->create_date       = date('Y-m-d H:i:s');
        $model->status            = 'Open';
        $model->api_key           = Yii::$app->session->getId();
        $model->point             = Yii::$app->session->get('Machine');



        if($model->save()){
          //var_dump($model->no);
          $UpdateSeries         = Generater::CreateNextNumber('sale_event_header','vat_value',1,$model->no);
          return $this->redirect(['update', 'id' => $model->id]);
        }else {
          print_r($model->getErrors());
        }


    }

    /**
     * Updates an existing SaleEventHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Set Menu Collapse
        $session = Yii::$app->session;
        $session->set('collapse', 'sidebar-collapse');
        // \.Set Menu Collapse

        
        
        /*
        * ถ้า Status = closed แก้ได้เฉพาะ admin
        */
        if($model->status == 'closed'){
          if(Yii::$app->session->get('Rules')['rules_id'] != 1){
            // Yii::$app->getSession()->setFlash('alert',[
            //     'body'=>'<i class="fa fa-times-circle text-red"></i> '.Yii::t('common','You do not have permission to edit closed job.').' '.$model->no,
            //     'options'=>['class'=>'bg-danger']
            // ]);
            Yii::$app->session->setFlash('error', Yii::t('common','You do not have permission to edit closed job.').' '.$model->no);
            return $this->redirect(['index']);
          }
        }


        // Update Only status, From Finance posted button.
        if(isset($_POST['status'])){
          if($_POST['status'] != ''){

            $model->status      = $_POST['status'];
            $model->balance     = $_POST['amount'];
            $model->rc_money    = $_POST['pay'];
            $model->rc_change   = $_POST['rcchange'];
            $model->update_by   = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s');
            $model->order_date  = Yii::$app->session->get('workdate');
            $model->point       = Yii::$app->session->get('Machine');

            if($model->save()){
              return true;
            }else {
              return json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }

          }
        }


        if ($model->load(Yii::$app->request->post())) {


            $model->update_by   = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s');
            $model->order_date  = Yii::$app->session->get('workdate');
            $model->point       = Yii::$app->session->get('Machine');
            //ถ้าสินค้าไม่มีราคา จะไม่ปิดใบงาน
            if($model->balance<=0){
                $model->status = 'Open';
            }
            $model->save();
            
            return $this->redirect(['index']);


        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Deletes an existing SaleEventHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = SaleEventHeader::findOne($id);
        if($model->status=='closed'){
            if($model->balance<=0){
                if($this->findModel($id)->delete()){
                    Yii::$app->session->setFlash('success', Yii::t('common','Successfully deleted').' ('.$model->no.')');
                    return $this->redirect(['index']);
                }else{
                    Yii::$app->session->setFlash('error', Yii::t('common','Error delete process...').' ('.$model->no.')');
                    return $this->redirect(['index']);
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('common','You can\'t delete status closed job').' ('.$model->no.')');
                return $this->redirect(['index']);
            }
            
        }else {
            if(SaleEventLine::deleteAll(['order_no' => $id])){
                
                if($this->findModel($id)->delete()){
                    Yii::$app->session->setFlash('success', Yii::t('common','Successfully deleted').' ('.$model->no.')');
                    return $this->redirect(['index']);
                }else{
                    Yii::$app->session->setFlash('error', Yii::t('common','Error delete process...').' ('.$model->no.')');
                    return $this->redirect(['index']);
                }
                
            }

            if($this->findModel($id)->delete()){
                Yii::$app->session->setFlash('success', Yii::t('common','Successfully deleted').' ('.$model->no.')');
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('error', Yii::t('common','Error delete process...').' ('.$model->no.')');
                return $this->redirect(['index']);
            }

        }
        
    }

    /**
     * Finds the SaleEventHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleEventHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleEventHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }












    public function actionFindBarcode()
    {

      $model      = new SaleEventHeader();
    

      if((isset($_POST['code'])) || (isset($_POST['no']))){
          if(($_POST['code']!='') || ($_POST['no']!=''))
          {
            
            if(isset($_POST['no'])){
                $item = Items::find()->where(['No' => $_POST['no']])->one();
            }else {
                $item = Items::find()->where(['barcode' => $_POST['code']])->one();
            }
            

            $htmlPrint =[];

            if($item){


                //$model->dosaleline($item);

                $htmlPrint  = [
                  'current' =>  [
                                  'no'      => $item->No,
                                  'img'     => $item->getPicture(),
                                  'desc_th' => '<div style="font-size:17; color:#07b3cb;" />'.$item->description_th.'</div>',
                                  'desc_en' => $item->Description,
                                  'cost'    => $item->StandardCost,
                                  'item'    => $item->master_code,
                                  'inven'   => $item->getInven($model),
                                  'barcode' => $item->barcode,
                                  'unit'    => $item->UnitOfMeasure,
                                  'price'   => $item->CostGP,
                                  'validate'=> true,
                                ],
                  'newData' => $model->dosaleline($item,$_POST),
                ];

                // $htmlPrint= [
                //   'no'      => $item->No,
                //   'img'     => $item->getPicture(),
                //   'desc_th' => $item->description_th,
                //   'desc_en' => $item->Description,
                //   'cost'    => $item->StandardCost,
                //   'item'    => $item->master_code,
                //   'inven'   => $item->getInven($model),
                //   'barcode' => $item->barcode,
                //   'unit'    => $item->UnitOfMeasure,
                //   'price'   => $item->CostGP,
                // ];

            }else {
                Yii::$app->session->set('barcode',$_POST['code']);
                $htmlPrint= [
                  'current' =>[
                                'desc_th' => '<span class="text-danger">ยังไม่มี BARCODE : <a href="#">'.$_POST['code'].'</a></span>',
                                'validate'=> false,
                              ],
                  'newData' => $model->LoadSaleLine($_POST['orderno'])];
            }

          }else{
            $htmlPrint= [];
          }

      }else {
        $htmlPrint= [];
      }

          return json_encode($htmlPrint);

    }


    public function actionLoadSaleLine($id){
      $query = SaleEventLine::find()->where(['order_no' => $id]);
      $JSON = [];
      if($query->exists()){
        foreach ($query->all() as $key => $model) {
          $JSON[] = [
            'id'        => $model->id,
            'no'        => $model->items->No,
            'barcode'   => $model->items->barcode,
            'name'      => $model->items->description_th,
            'unit'      => $model->items->UnitOfMeasure,
            'quantity'  => $model->quantity,
            'price'     => $model->unit_price,
            'discount'  => $model->line_discount,
          ];
        }

      }
      return json_encode($JSON);
    }

    public function actionDeleteLine(){
      //$Temp = SaleEventLine::deleteAll(['session_id' => Yii::$app->session->getId()]);
      if(SaleEventLine::findOne($_POST['lineno'])->delete()){
          return 'Delete';
      }else {
          return 'Fail';
      }
    }



    public function actionDashboard($sdate,$edate){

      $workdate   = date('Y',strtotime(Yii::$app->session->get('workdate')));

      $thisYear   = $workdate ? $workdate : date('Y');

      $brand      = (isset($_GET['brand'])) ? ($_GET['brand']!='')? $_GET['brand'] : 'ALL' : 'ALL' ;

      $sdate      = ($sdate!='')? $sdate : $thisYear.'-01-01';
      $edate      = ($edate!='')? $edate : $thisYear.'-12-31';

      $head       = SaleEventHeader::find()
                    ->where(['between', 'sale_event_header.order_date', $sdate,$edate])
                    ->andWhere(["sale_event_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']])
                    ->all();

      
      $line       = SaleEventLine::find()
                    ->joinwith('header')
                    ->joinwith('items')                    
                    ->where(['sale_event_header.status' => 'closed'])
                    ->andWhere(["sale_event_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['between', 'sale_event_header.order_date', $sdate,$edate]);

      if($brand != 'ALL')  $line->andWhere(['items.brand' => $brand]); 
      
 

      $total  = 0;
      foreach ($line->all() as $key => $model) {
         $total += $model->quantity * ($model->unit_price - $model->line_discount);
      }

      $GroupDate  = SaleEventLine::find()
                    ->select(['DATE(sale_event_line.order_date) as DateOnly','sum(quantity * (unit_price - line_discount)) as line_amount'])
                    ->joinwith('header')
                    ->joinwith('items')
                    ->where(['sale_event_header.status' => 'closed'])
                    ->andWhere(['between', 'sale_event_header.order_date', $sdate,$edate])
                    ->andWhere(["sale_event_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']])
                    //->where(['order_no' => SaleEventHeader::find()->select('id')->where(['status' => 'closed'])])
                    ->groupBy(['DateOnly']);
      if($brand != 'ALL')  $GroupDate->andWhere(['items.brand' => $brand]);

      $GroupData = [];
      foreach ($GroupDate->all() as $key => $model) {
        $date = $model->DateOnly.' 00:00:01';
        $GroupData[] = [
          'date'    => date('D M d Y H:i:s O',strtotime($date)),
          'amount'  => $model->line_amount,
        ];
      }


      $GroupItems = SaleEventLine::find()
                    ->select(['item_no,
                              sum(quantity) as quantity,
                              sum(quantity * (unit_price - line_discount)) as line_amount,
                              avg(unit_price) as unit_price'
                            ])
                    ->joinwith('header')
                    ->joinwith('items')
                    ->where(['between', 'sale_event_header.order_date', $sdate,$edate])
                    ->andWhere(["sale_event_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['sale_event_header.status' => 'closed'])
                    ->groupBy(['item_no']);

      if($brand != 'ALL')  $GroupItems->andWhere(['items.brand' => $brand]);
                    


      $GroupItem = [];
      foreach ($GroupItems->all() as $key => $model) {

        $itemName   = ($model->items->alias ? $model->items->alias : $model->items->description_th);

        $GroupItem[] = [
          'no'      => $model->items->No,
          'code'    => $model->items->master_code,
          'barcode' => $model->items->barcode,
          'name'    => $itemName,
          'qty'     => $model->quantity,
          'price'   => $model->unit_price,
          'total'   => $model->line_amount,
          'color'   => $model->items->color,
          'img'     => $model->items->getPicture(),
          'group'   => $model->items->group_chart,

        ];
      }




      $customGroupItems = SaleEventLine::find()
                          ->select(['items.group_chart as item_no,
                                    sum(quantity) as quantity,
                                    sum(quantity * (unit_price - line_discount)) as line_amount,
                                    avg(unit_price) as unit_price'
                                  ])
                          ->joinwith('header')
                          ->joinwith('items')
                          ->where(['between', 'sale_event_header.order_date', $sdate,$edate])
                          ->andWhere(["sale_event_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']])
                          ->andWhere(['sale_event_header.status' => 'closed'])                          
                          ->groupBy(['items.group_chart']);
      if($brand != 'ALL')  $customGroupItems->andWhere(['items.brand' => $brand]);


      $customGroupItem = [];
      foreach ($customGroupItems->all() as $key => $model) {



        $customGroupItem[] = [
          'name'   => $model->item_no,
          'qty'    => $model->quantity,
          'total'  => $model->line_amount
        ];
      }

      $JSON = [
        'jobs'  => count($head),
        'total' => $total,
        'line'  => $GroupData,
        'items' => $GroupItem,
        'groups'=> $customGroupItem,
        'brand' => $brand,
        'range' => $sdate.' to '.$edate,
      ];

      return json_encode($JSON);
    }





    public function actionSaleLine(){
      $searchModel = new EventLineSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
      $dataProvider->query->andWhere(['order_no' => SaleEventHeader::find()->select('id')->where(['status' => 'closed'])]);
      
      if(isset($_GET['No'])){
        $dataProvider->query->andWhere(['item_no' => $_GET['No']]);
      }
      $dataProvider->query->andWhere(["comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
      $dataProvider->query->orderBy(['order_date' => SORT_DESC]);

      return $this->render('sale-line', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
      ]);

    }


    public function actionBarcodePrint(){

      $query = SaleEventLine::find()
      ->select(['item_no,
                sum(quantity) as quantity,
                sum(quantity * (unit_price - line_discount)) as line_amount,
                avg(unit_price) as unit_price'
              ])
      ->joinwith('header')
      ->andWhere(['sale_event_header.status' => 'closed'])
      ->groupBy(['item_no']);
      
      $dataProvider = new ActiveDataProvider([
          'query' => $query,
          'pagination' => false,

      ]);
      $dataProvider->query->andWhere(["sale_event_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);



      return $this->render('barcode-print', [
          //'searchModel' => $query,
          'dataProvider' => $dataProvider,
      ]);

    }
    public function actionBarcodePrintAll(){

        $searchModel = new \admin\modules\items\models\SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=20;


        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        if($this->company != 1){
            $dataProvider->query->andWhere(['No'=> $this->getMyitem($this->company)]);

        }
        $dataProvider->query->andWhere(['<>','barcode','']);
        
  
  
        return $this->render('barcode-print-all', [
            //'searchModel' => $query,
            'dataProvider' => $dataProvider,
        ]);
  
      }

    public function actionItems(){
        $searchModel = new \admin\modules\items\models\SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=20;


        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        if($this->company != 1){
            $dataProvider->query->andWhere(['No'=> $this->getMyitem($this->company)]);

        }

        return $this->render('items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    static function getMyitem($company)
    {


        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item_no;
            }

            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateQty($id){

        $model = SaleEventLine::findOne($id);
        $model->quantity = $_POST['qty'];
        if($model->save()){
            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => [
                    'id' => $id,
                    'qty' => $_POST['qty'],
                ]
            ]);
        }else {
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                'value' => [
                    'id' => $id,
                    'qty' => $_POST['qty'],
                ]
            ]);
        }
        
    }


    public function actionCashier($id){
        /**
         * 1.Check permission to access cashier.
         * 2.Register Machine session.
         */
        $package = PosFilter::findPermission();
        if($package->id=="Error"){
            // ถ้าไม่มี ให้สร้าง Free Package POS.
            $model = new \common\models\PackageControl();
            $model->name = 'POS';
            $model->type = 'sale';
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];

            if($model->save()){
                Yii::$app->session->set('Machine',$id);
                return json_encode([
                    'status' => 200,
                    'message' => 'done',
                    'value' => [
                        'id' => $id,
                        'comp' => Yii::$app->session->get('Rules')['comp_id']
                    ]
                ]);
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => Yii::t('common','No package available'),
                    'value' => [
                        'id' => $id,
                        'comp' => Yii::$app->session->get('Rules')['comp_id']
                    ]
                ]);
            }
            
        }else {
            Yii::$app->session->set('Machine',$id);
            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => [
                    'id' => $id
                ]
            ]);
        }
    }

    public function actionFilterProduct($search){

        $word  = explode(' ',$search);
        $model = Items::find()
                ->where(['No'=> self::getMyitem(Yii::$app->session->get('Rules')['comp_id'])])
                ->andFilterWhere(['or',
                    ['like','No',$word],
                    ['like','barcode',$word],
                    ['like','Description',$word],
                    ['like','description_th',$word],
                    ])
                ->limit(12)
                ->all();
         
        $product = PosFilter::renderItemList($model);

        return json_encode([
            'status' => 200,
            'message' => 'done',
            'value' => [
                'html' => $product
            ]            
            ]);

    }
}
