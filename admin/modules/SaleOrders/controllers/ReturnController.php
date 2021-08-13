<?php

namespace admin\modules\SaleOrders\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\SaleReturnHeader;
use common\models\SaleReturnLine;
use common\models\WarehouseHeader;
use common\models\Items;
use admin\models\Generater;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\tracking\models\FunctionTracking;
use admin\modules\apps_rules\models\SysRuleModels;

use admin\modules\SaleOrders\models\SaleReturnSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

/**
 * ReturnController implements the CRUD actions for SaleReturnHeader model.
 */
class ReturnController extends Controller
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
                    'percent-discount' => ['POST'],
                    'get-line' => ['POST'],
                    'clear-sale-line' => ['POST'],
                    'update-status' => ['POST'],
                    'post-stock' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all SaleReturnHeader models.
     * @return mixed
     */

    public function actionIndex()
    {
        $searchModel = new SaleReturnSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionList()
    {
        return $this->render('list');
    }

    public function actionAjaxList(){
        $request_body               = file_get_contents('php://input');
        $data                       = json_decode($request_body);  
          
        $status                     = 200;
        $message                    = Yii::t('common','Success');
        $raws                       = [];

        $query                      = SaleReturnHeader::find()
                                        ->where(['like', 'no', $data->saleId])
                                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                        ->all();

        foreach ($query as $key => $model) {
            $raws[] = (Object)[
                'id' => $model->id,
                'no' => $model->no
            ];
        }


        return json_encode([
            'status' => 200,
            'raws' => $raws
        ]);
    }

    /**
     * Displays a single SaleReturnHeader model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $query  = SaleReturnLine::find()
        ->where(['source_id' => $id])
        ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $production  = WarehouseHeader::find()->where(['return_id' => $id])->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        //$productionLint = WarehouseMoving::find()

        $produce = new ActiveDataProvider([
            'query' => $production,
            'pagination' => false
        ]);



        return $this->render('view', [
            'model'         => $this->findModel($id),
            'dataProvider'  => $dataProvider,
            'produce'       => $produce
        ]);
    }

    /**
     * Creates a new SaleReturnHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    
    public function actionCreate()
    {
        $header     = new SaleReturnHeader();
        // ใส่ cache เพื่อป้องกันการสร้างเอกสารซ้ำ
        $keys       = 'create-return-order&comp:'.Yii::$app->session->get('Rules')['comp_id'].'&user:'.Yii::$app->user->identity->id;
        $cacheData  = Yii::$app->cache->get($keys);
        $Free       = $header->findEmpty;
        

        if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Data Access','SaleOrders','saleorder','actionIndex','read-only'))){
            Yii::$app->session->setFlash('warning', Yii::t('common','Permission Denine'));
            return $this->redirect(['index']);
            exit;
        }else{
            
            if($cacheData){            
                if($Free != null){
                    Yii::$app->cache->delete($keys);
                    return $this->redirect(['update', 'id' => $Free->id]);
                    exit;
                }else{
                    Yii::$app->session->setFlash('error', Yii::t('common','cashed'));
                    Yii::$app->cache->delete($keys);
                    return $this->redirect(['index']);
                    exit;
                }                
            }else{
                Yii::$app->cache->set($keys, true, 10);                   
                if($Free  != null){
                    Yii::$app->session->setFlash('warning', Yii::t('common','Already exists and update'));   
                    return $this->redirect(['update', 'id' => $Free->id]);
                    exit;
                }else {
                    $transaction    = Yii::$app->db->beginTransaction();
                    try {                         
                        $no         = Generater::getRuning('sale_return_header','no','all');
                                      Generater::UpdateSeries('sale_return_header','no','all',$no);                                     
                        $newHeader  = $header->createHeader((Object)['no' => $no]);                        
                        if($newHeader->status == 200){  
                            $model      = $newHeader->model;   
                            Yii::$app->cache->delete($keys);
                            $transaction->commit();                  
                            return $this->redirect(['update', 'id' => $model->id]);
                            exit;
                        }else{
                            Yii::$app->session->setFlash('error', Yii::t('common',$newHeader->model));
                            $transaction->rollBack();     
                            Yii::$app->cache->delete($keys);                    
                            return $this->redirect(['index']);
                        }
                    } catch (\Exception $e) {
                            $transaction->rollBack();
                            Yii::$app->cache->delete($keys);      
                            return $this->redirect(['index']);
                            throw $e;
                    }
                }
            }
        }
    }

    /**
     * Updates an existing SaleReturnHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);        
        
        if($model->status!='Open'){
            if($model->status=='Posted'){
                Yii::$app->session->setFlash('warning', Yii::t('common','Cannot be changed , Because posted inventory.'));
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('warning', Yii::t('common','Please change status before edit.'));
                return $this->redirect(['index']);
            }
            
        }

        if(Yii::$app->request->post('ajax')){
            /**
            * Update Sale Line
            * เมื่อมีการแก้ไขตัวเลขในบรรทัดสินค้า
            */
            $status     = 200;
            $message    = Yii::t('common','Success');
            $data       = Yii::$app->request->post('data');
            $field      = (string)Yii::$app->request->post('name');
            
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $Line               = SaleReturnLine::findOne(Yii::$app->request->post('key'));
                $Line->$field       = $data;
                $Line->save();

                $raws = (Object)[
                    'status'        => 200,
                    'message'       => Yii::t('common','Done'),
                    'reserve'       => $Line->items->reserveInSaleLine                                                
                ];  
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $raws = (Object)[
                    'status'        => 500,
                    'message'       => Yii::t('common','Error'),
                    'suggestion'    =>  Yii::t('common','{:e}',[':e' => $e])
                ];                        
            }
            
            $LoadLine   = SaleReturnLine::findOne(Yii::$app->request->post('key'));               
            

            return json_encode([
                'status'    => $status,
                'message'   => $message,
                'value' => [
                    'id'    => $LoadLine->id,
                    'val'   => $LoadLine->$field
                ],
                'data'      => $raws
            ]);
            exit;
        }

        

        if ($model->load(Yii::$app->request->post())) {
            $model->status = 'Release';
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $query  = SaleReturnLine::find()
                    ->where(['source_id' => Yii::$app->request->post('id')])
                    ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        return $this->render('update', [
            'model'         => $model,
            'dataProvider'  => $dataProvider
        ]);
    }

    public function actionPercentDiscount(){

        $model      = $this->findModel(Yii::$app->request->post('id')); 
        $key        = Yii::$app->request->post('key');
        
        
        $model->percent_discount    = Yii::$app->request->post('percent');
        $model->include_vat         = Yii::$app->request->post('inc_vat');
        $model->vat_percent         = Yii::$app->request->post('vat_percent');
        $model->payment_term        = Yii::$app->request->post('credit');
        $model->paymentdue          = Yii::$app->request->post('due');
        $model->discount            = Yii::$app->request->post('discount') == 0 
                                        ? (Yii::$app->request->post('discount') * $model->sumLine) /100 
                                        : 0;
         
         
        if($key=='discount'){
            $division = ($model->sumLine > 0)? $model->sumLine : 1;
            $model->percent_discount    = ($model->discount / $division) *100; 
        }
        
        $model->balance             = FunctionSaleOrder::GrandTotalSaleOrder($model);
        
        if(!$model->save()){
            var_dump($model->getErrors(),JSON_UNESCAPED_UNICODE);            
        }

        $query  = SaleReturnLine::find()
                    ->where(['source_id' => Yii::$app->request->post('id')])
                    ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);

        return json_encode([ 
            'html' => $this->renderpartial('../saleorder/_sum_line',
                    [
                        'model' => $model,
                        'dataProvider' => $dataProvider,
                    ]),
           // 'promo' => self::getPromotions($model)
        ]);
    }

    protected function getPromotions($header){

        // ตรวจสอบ Promotion
        
        $lines = SaleReturnLine::find()
        ->select('item, sum(quantity) as quantity, sum(unit_price * quantity) as total')
        ->where(['source_id' => $header->id])
        ->groupBy('item')
        ->all();

        $data = [];
       
        foreach ($lines as $key => $model) {

            // ถ้ามี Promotion ให้คำนวนส่วนลด
            $promo = \common\models\PromotionsItemGroup::find()
            ->where(['item' => $model->item])
            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();

            // ตรวจสอบสินค้า มีโปรโมชัน หรือไม่
            if ($promo!=null){
                // มีโปรโมชัน
                // 1. โปรโมชัน มีรายละเอียดอะไรบ้าง
                // 2. ถึงโปรโมชันหรือยัง
                    $checkPromo = \common\models\Promotions::find()
                    ->where(['item_group' => trim($promo->name)])
                    ->andWhere(['<=', 'start_date', new Expression('CURDATE()')])
                    ->andWhere(['>=', 'end_date', new Expression('CURDATE()')])
                    ->andWhere(['status' => 4])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->one();

                    $saleAmount = 0;
                    $salePrice  = 0;

                    if ($checkPromo != null){
                        $saleAmount = $checkPromo->sale_amount * $model->quantity;
                        $salePrice  = $model->total;

                        $data[] = (Object)[
                            'name'      => $promo->name,
                            'promotion' => $checkPromo->sale_amount,                            
                            'item'      => $model->item,
                            'discount_perunit'  => $checkPromo->discount,
                            'current_total'     => $model->total,
                            'current_discount'  => ($model->total >= $checkPromo->sale_amount)? $checkPromo->discount : 0,
                            'sum_discount'      => (floor($model->total / $checkPromo->sale_amount) *$checkPromo->discount )
                        ];
                    }

            }else{
                // ไม่มีโปรโมชัน
            }
        }

        return (Object)([
            'status'=> 200,
            'data'  => $data,
            'id'    => $header->id,
            'text'  => [
                'label_promotion'       => Yii::t('common','Promotion'),
                'label_buy'             => Yii::t('common','Buy the product.'),
                'label_getdiscount'     => Yii::t('common','Get Discount'),
                'label_totaldiscount'   => Yii::t('common','Total Discount')
            ]
        ]);
    }


    public function actionGetLine(){

        $model  = $this->findModel(Yii::$app->request->post('id'));
        $query  = SaleReturnLine::find()
                    ->where(['source_id' => Yii::$app->request->post('id')])
                    ->andwhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);

        return $this->renderpartial('_saleline_mobile',[
            'model' => $model, 
            'dataProvider' => $dataProvider
        ]);

    }

    /**
     * Deletes an existing SaleReturnHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status!='Open'){
            Yii::$app->session->setFlash('warning', Yii::t('common','Permission Denine'));
        }else{
            Yii::$app->session->setFlash('success', Yii::t('common','Success'));
            $this->findModel($id)->delete();
        }
        

        return $this->redirect(['index']);
    }

    /**
     * Finds the SaleReturnHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleReturnHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleReturnHeader::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }


    public function actionCreateSaleline()
    {
        $id             = Yii::$app->request->post('param')['soid'];
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $Quantity       = Yii::$app->request->post('param')['amount'];
        $data           = [];
        $message        = '';
        $status         = 200;
        $Compare        = 0;
        $reserved       = 0;
        $model          = SaleReturnHeader::findOne($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $Item 			= Items::findOne(Yii::$app->request->post('param')['itemid']);
            if($Item == null){
                $Item       = Items::findOne(['No' => Yii::$app->request->post('param')['itemno']]);
            }

            
            $data = self::CreateSaleLine((Object)[
                'id'        => Yii::$app->request->post('param')['soid'],
                'orderno'   => $model->no,
                'desc'      => isset(Yii::$app->request->post('param')['desc']) ? Yii::$app->request->post('param')['desc'] : null,
                'amount'    => Yii::$app->request->post('param')['amount'],
                'price'     => Yii::$app->request->post('param')['price'],
                'item'      => $Item
            ]);
            
             

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            $data = (Object)[
                'status'        => 500,
                'message'       => Yii::t('common','Error'),
                'reserve'       => $reserved,
                'suggestion'    => Yii::t('common','{:e}',[':e' => $e])
            ];                            
        
        }

        $model  = $this->findModel($id);

        $query  = SaleReturnLine::find()
                    ->where(['source_id'    => $id])
                    ->andwhere(['comp_id'   => $company]);

        $dataProvider = new ActiveDataProvider([
            'query'         => $query,
            'pagination'    => false,
        ]);


        return json_encode([
            'data'  => $data,
            'html'  => $this->renderpartial('_saleline_mobile',[
                        'model'         => $model,
                        'dataProvider'  => $dataProvider
                    ]),
            'reserved'  => $reserved
        ]);
    }

    public function actionClearSaleLine(){

        $id         = Yii::$app->request->post('id');
        $status     = 200;
        $message    = Yii::t('common','Success');

        $models = SaleReturnLine::find()->where(['source_id' => $id])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();

        $raw = [];
        foreach ($models as $key => $model) {           
            if($model->delete()){
                $raw[] = [
                    'satus'     => 200,
                    'message'   => 'Done'
                ];
            }else {
                $raw[] = [
                    'satus'     => 500,
                    'message'   => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                ];
            }          
        }
 
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raw'       => $raw
        ]);        
        
    }

    public static function CreateSaleLine($obj){

        $id                 = $obj->id;
        $company            = Yii::$app->session->get('Rules')['comp_id'];
        $Item               = $obj->item;
        $message            = '';
        $status             = 200;

        // Get data from Sale Header
        $Header             = SaleReturnHeader::findOne($id);

        // Sale Line           
        $model              = new SaleReturnLine();        
        $model->order_no    = $obj->orderno;
        $model->description = $obj->desc;
        $model->item        = $Item->id;
        $model->item_no     = $Item->No;
        $model->quantity    = $obj->amount;
        $model->unit_price  = 0;
        $model->create_date = date('Y-m-d H:i:s');
        $model->vat_percent = $Header->vat_percent;
        $model->user_id     = Yii::$app->user->identity->id;
        $model->api_key     = Yii::$app->session->getId();
        $model->comp_id     = $company;
        $model->source_id   = $id;
        $model->unit_measure= $Item->itemunitofmeasures
                                ? $Item->itemunitofmeasures[0]['id']
                                : $Item->defaultMeasure->id;

        // แยก Vat
        $model->unit_price_exvat = ($model->unit_price) ? ($model->unit_price * 100) /107 : 0;

        if($model->save()){
            
            $status         = 200;
            $message        = Yii::t('common','Success');  
                                     
            // Session Clear
            Yii::$app->session->set('item_no',' ');

            // Sale people update
            if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','Allow-Sent-Approve'))){
                $Header->status     = 'Open';                
            }

            $Header->confirm    = '0';
            if(!$Header->save()){
                $message    = json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE);
                $status     = 500;
            }

        }else {                
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }

        return (Object)([  
            'id'        => $model->id,          
            'message'   => $message,
            'item'      => $Item->id,
            'name'      => $Item->description_th,
            'code'      => $Item->master_code,
            'stock'     => $Item->invenByBom,
            'status'    => (int)$status
        ]);
    }

    public function actionUpdateField(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        
        $id             = (int)$data->id;
        $field          = $data->field;
        $model          = SaleReturnHeader::findOne($id);          

        if($model->status=='Posted'){            
            return json_encode([
                'status'    => 500,
                'message'   => Yii::t('common',Yii::t('common','Cannot be changed , Because posted inventory.'))
            ]);
        }else{

            $model->$field  = (int)$data->value;

            if($model->save()){
                return json_encode([
                    'status'    => 200,
                    'message'   => Yii::t('common','Success')
                ]);
            }else{
                return json_encode([
                    'status'    => 500,
                    'message'   => Yii::t('common',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE))
                ]);
            }
        }
    }


    public function actionDelete_line($id)
    {

        $status     = 200;
        $message    = Yii::t('common','Success');
        $suggestion = '';

         

        if((int)Yii::$app->request->post('param')['lineno']){

            $transaction = Yii::$app->db->beginTransaction();
            try {

                $SaleLine = SaleReturnLine::findOne((int)Yii::$app->request->post('param')['lineno']);

                if($SaleLine !== null){


                    if($SaleLine->delete()){                       
                        
                        
                        
                        $message    = Yii::t('common','Removed');
                         

                    }else {                         
                        $status     = 500;
                        $message    = Yii::t('common','Fail');
                    }
                    
                }else {
                    $status     = 404;
                    $message    = Yii::t('common','Not Found');
                    $suggestion = '';
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status     = 500;
                $message    = Yii::t('common','Error');
                $suggestion = Yii::t('common','{:e}',[':e' => $e]);
          
            }


            return json_encode([
                'status'    => $status,
                'message'   => $message,
                'value'     => (int)Yii::$app->request->post('param')['lineno'],
                'suggestion'=> $suggestion
            ]);

        }
    }

    public function actionUpdateStatus(){
        $request_body               = file_get_contents('php://input');
        $data                       = json_decode($request_body);    
        $status                     = 200;
        $message                    = Yii::t('common','Success');
        
        $model                      = SaleReturnHeader::findOne($data->id); 

        if($model->status=='Posted'){     
            $status                 = 403;
            $message                = Yii::t('common','Cannot be changed , Because posted inventory.');                  
        }else{

            if($model->status != $data->status){        
                        
                $model->status              = $data->status;
                $model->update_status_date  = date('Y-m-d H:i:s');
                if($model->save()){
                    $status     = 200;
                }else{
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                }
                        
            }else{
                $status             = 301;
                $message            = Yii::t('common','Not Change');
            }
        }
        
        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }


    public function actionPrint($id)
    {
              
        $model      = $this->findModel($id);

        $query      = SaleReturnLine::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $template   = \common\models\PrintPage::findOne(\common\models\PrintPage::findCustomPrint('sale_return_order'));  
        $Company    = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

        $header = (Object)[
            'height'    => $template->header_height,
            'top'       => $template->margin_top,
            'fontsize'  => $template->font_size,
            'watermark' => (Object)[
                'text'      => $template->water_mark,
                'left'      => $template->water_mark_left,
                'top'       => $template->water_mark_top,
                'color'     => $template->water_mark_color,
                'size'      => $template->water_mark_size,
                'radius'    => $template->water_mark_radius,
                'padding'   => $template->water_mark_padding,
                'border'    => $template->water_mark_border,
                'border_color' => $template->water_mark_border_color,
                'css'       => $template->water_mark_css,
                'switch'    => $template->water_mark_switch,
                'img'       => $template->watermark,
                'img_alpha' => $template->water_mark_img_alpha,
                'img_width' => $template->water_mark_img_width
            ],
        ];

        $pageSize   = $template->pagination ?: 15;
        if(isset($_GET['pagesize']))    $pageSize   = $_GET['pagesize'];
        
        $body   = (Object)[
            'height' => $template->body_height,
            'pagesize' => $pageSize,
            'fontsize' => $template->font_size
        ];

        $Bahttext   = new \admin\models\FunctionBahttext();
    
        $defineHeader = [
            '{VALUE_TITLE}'         => $model->no,
            '{VALUE_HEAD_DOC}'      => $model->return_type == 1 ? 'ใบส่งสินค้า' : 'ใบรับสินค้า' ,
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 100px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_ID}'          => $Company->id.' '.$model->id,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $model->no,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->order_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{HEAD_OFFICE}'         => $Company->headofficetb->data_char,
            '{CREATOR}'             => $model->sales ? $model->sales->name : '',        
            '{CUSTOMER_CODE}'       => $model->customers ? $model->customers->code : '',
            '{CUSTOMER_NAME}'       => $model->customers ? $model->customers->name : '',
            '{CUSTOMER_PHONE}'      => $model->customers ? $model->customers->phone : '',
            '{CUSTOMER_FAX}'        => $model->customers ? $model->customers->fax : '',
            '{CUSTOMER_ADDRESS}'    => $model->customers ? $model->customers->fullAddress['address'] : '',
            '{CUSTOMER_TAX}'        => $model->customers ? $model->customers->vat_regis : '',        
            '{SALE_NAME}'           => $model->sales ? $model->sales->name : '',
            '{SALE_SUR_NAME}'       => $model->sales ? $model->sales->surname : '',
            '{SALE_CODE}'           => $model->sales ? $model->sales->code : '',         
            '{REF_TO}'              => $model->ext_document,         
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term > 0 ? $model->payment_term.' '.Yii::t('common','Day') : Yii::t('common','Cash'),
            '{DUE_DATE}'            => (date('Y',strtotime($model->paymentdue)) > 1970)?  date('d/m/y',strtotime($model->paymentdue.' + 543 Years')) : " ",
            '{DUE_DATE_EN}'         => $model->paymentdue,
            '{PO_REFERENCE}'        => $model->ext_document,        
            '{VALUE_BEFOR_VAT}'     => number_format($model->sumtotal->subtotal - $model->sumtotal->incvat,2),        
            '{IF_VAT_TYPE_COLSPAN}' => ($model->include_vat===1)?  '6'  : '5',
            '{IF_VAT_TYPE_ROWSPAN}' => ($model->include_vat===1)?  '4'  : '5',
            '<!--IF_VAT_TYPE-->'    => ($model->include_vat===1)? '<!--': ' ' ,
            '<!--IF_VAT_TYPE_END-->'=> ($model->include_vat===1)? '-->' : ' ',              
            '{VALUE_PERCENT_DISCOUNT}' => ($model->percent_discount)? '('.number_format($model->percent_discount).' %)' : '',         
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => $model->remark,
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->sumtotal->sumline,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->sumtotal->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->sumtotal->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->vat_percent.' %',
            '{VALUE_INCLUDEVAT}'    => number_format($model->sumtotal->incvat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->sumtotal->total,2),
            '{VALUE_BAHTTEXT}'      => $model->sumtotal->total == 0 ? 'ศูนย์บาทถ้วน' : $Bahttext->ThaiBaht($model->sumtotal->total)             
        ]; 

        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme',[
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'print'         => $template,
            'Company'       => $Company,
            'header'        => $header,
            'body'          => $body,
            'defineHeader'  => $defineHeader
        ]);
  
        $content = $this->renderPartial('_print_content',[
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'header'        => $header,
            'print'         => $template,
            'body'          => $body
        ]);
  
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            'orientation'   => $template->paper_orientation,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            'filename'      => $model->no.'.pdf',
            'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } ',
            'options'       => [
                'title'     => 'SO : '.$model->no.' '
            ],
            'methods'       => [
                'WriteHTML' => $PrintTemplate,   
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

    public function actionPostStock(){
        $request_body               = file_get_contents('php://input');
        $data                       = json_decode($request_body);    
        $status                     = 200;
        $message                    = Yii::t('common','Success');
        
        $model                      = SaleReturnHeader::findOne($data->id); 
        $stock                      = [];
        $produe                     = [];

        if($model->checkPostPermission){
            $transaction = Yii::$app->db->beginTransaction();
            try {		
                
                $WH                 = new \common\models\WarehouseHeader();            

                $WH->DocumentNo     = $model->no;
                $WH->PostingDate    = date('Y-m-d H:i:s');
                $WH->ship_date		= date('Y-m-d H:i:s');
                $WH->DocumentDate 	= date('Y-m-d');
                $WH->TypeOfDocument = 'Sale-Return';
                $WH->customer_id 	= $model->customer_id;
                $WH->SourceDocNo 	= $model->id;
                $WH->SourceDoc 		= $model->no;
                $WH->order_id       = NULL;
                $WH->return_id      = $model->id;
                $WH->ship_to 		= $model->customer_id;
                $WH->Description 	= $model->ext_document;
                $WH->address 		= '';
                $WH->address2 		= '';
                $WH->district 		= '';
                $WH->city 			= '';
                $WH->province 		= '';
                $WH->postcode 		= '';
                $WH->status         = 'Return';
                $WH->session_id     = Yii::$app->session->getId();
                $WH->user_id 		= Yii::$app->user->identity->id;
                $WH->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $WH->line_no 		= $model->id;
                
                if($WH->save()){
                    $SaleReturnLine = SaleReturnLine::find()->where(['source_id' => $model->id])->all();
                    
                    foreach ($SaleReturnLine as $key => $line) {
                        $quantity               = $model->return_type == 1 ? ($line->quantity * -1) : ($line->quantity * 1);
                        $line->return_receive   = $quantity;
                        $line->doc_no_          = $model->no;
                        $line->code_desc_       = $line->description;
                        $line->code_no_         = $line->description;
                        //----------- Stock --------------

                        // Produce
                        $produe[] = $WH->ReturnProducer($line, $WH, ($quantity * -1));
                        //return json_encode($produe); exit;
                        if($produe[$key]->status==200){
                            // Cut Shipment
                            $stock[] = $WH->invenPostAdjust($line, $model);
                            $model->status = 'Posted';
                        }else{
                            //$transaction->rollBack();
                            $status         = 500;
                            $message        = $produe[$key]->message;		
                            $model->status = 'Open';		
                        }
                    
                        
                        //-----------/.Stock -------------
                    }

                    
                    $model->save();
                }
                $transaction->commit();
            } catch (\Exception $e) {             
                $transaction->rollBack();
                $status         = 500;
                $message        = Yii::t('common','{:e}',[':e' => $e]);					 
            }  
        }else{
            $status         = 403;
            $message        = Yii::t('common',"You don't have permission.");	
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'stock'     => $stock,
            'produce'   => $produe
        ]);
    }

}
