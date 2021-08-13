<?php

namespace admin\modules\accounting\controllers;
use admin\models\FunctionCenter;
use Yii;
use yii\data\ActiveDataProvider;

use common\models\Cheque;
use admin\modules\accounting\models\ChequeSearch;
use admin\modules\accounting\models\ChequeFilterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use common\models\RcInvoiceHeader;
use common\models\SaleInvoiceHeader;
use admin\modules\accounting\models\FunctionAccounting;
use common\models\Customer;
/**
 * ChequeController implements the CRUD actions for Cheque model.
 */
class ChequeController extends Controller
{
    /**
     * @inheritdoc
     */
    public $message;
    public $status;
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'    => ['POST'],
                    'get-data'  => ['POST'],
                    'get-line'  => ['POST'],
                    'update-field' => ['POST'],
                    'delete-row'    => ['POST'],
                    'delete-source' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Cheque models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChequeFilterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $dataProvider->query->orderBy(['cheque.create_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexAjax()
    {
        return $this->render('index-ajax');
    }

    public function actionIndexAjaxDetail()
    {
        return $this->render('index-ajax-detail');
    }

    
    /**
     * Displays a single Cheque model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model          = $this->findModel($id);
        $invoice        = RcInvoiceHeader::find()->where(['id' => $model->apply_to]);
        $dataProvider   = new ActiveDataProvider([
            'query'     => $invoice,
            'pagination'=> false      
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionPostedInvList($id,$cust)
    {
        $fdate  = Yii::$app->request->get('fdate');
        $tdate  = Yii::$app->request->get('tdate');


        $customerList   = [];
        $customer       = Customer::findOne($cust);
        $headOffice     = 0;

        if($customer != null){
            // หาสำนักงานใหญ่
            $headOffice = $customer->childOff 
                            ? $customer->childOff->id
                            : $customer->id;
            // หาสาขาของบริษัทนี้

            $query = Customer::find()
                    ->select('id')
                    ->where(['child' => $headOffice])
                    ->andWhere(['status' => 1])
                    ->groupBy(['id'])
                    ->all();

            foreach ($query as $key => $model) {
                $customerList[] =  $model->id;
            }

 
        }
        
       // var_dump($customerList); exit;

        $model = RcInvoiceHeader::find()
        ->where(['id' => $id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        //->andWhere(['between', 'date(posting_date)', date('Y-m-d 00:00:0000',strtotime($fdate)),date('Y-m-d 23:59:59.9999',strtotime($tdate))])
        //->andWhere(['doc_type' => 'Sale'])
        ->One();

        $query = RcInvoiceHeader::find();
        // Disabled 07/10/2017 Enabled Again 09/10/2020
        //$query = \common\models\ViewRcInvoice::find(); // Disabled  09/10/2020
        
        $query->where(['or',['cust_no_' => $headOffice],['in','cust_no_', $customerList]]);

        $query->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        //$query->andWhere(['doc_type' => 'Sale']);
        $query->andWhere(['between', 'date(posting_date)', date('Y-m-d 00:00:0000',strtotime($fdate)),date('Y-m-d 23:59:59.9999',strtotime($tdate))]);
        $query->orderBy(['no_' => SORT_DESC, 'posting_date' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        

       

        return $this->renderAjax('_postedinv_list',[
            'model'=> $model,
            'dataProvider' => $dataProvider,
            'fdate' => $fdate,
            'tdate' => $tdate
        ]);

    }

    /**
     * Creates a new Cheque model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */


    public function actionCreate()
    {
        $model = new Cheque();

        if ($model->load(Yii::$app->request->post())) {

            // $document   = Yii::$app->session->get('apply_to');
            $i          = 0;
            $len        = count(Yii::$app->session->get('apply_to'));
            $sumCheque  = 0;

            $maxVal = Cheque::find()->select('max(source_id) as source_id')->one();
            $Nextid = $maxVal->source_id + 1;

            foreach (Yii::$app->session->get('apply_to') as $key => $value) {

              $models = new Cheque();

              if($value['status']=='Posted'){

                $Inv = RcInvoiceHeader::findOne($value['id']);

                $sumLine = FunctionAccounting::getTotalBalance($Inv,'RcInvoiceLine');

              }else {

                $Inv = SaleInvoiceHeader::findOne($value['id']);

                $sumLine = FunctionAccounting::getTotalBalance($Inv,'SaleInvoiceLine');
                
              }

              

              // ผลรวมของ invoice แต่ละใบ
              $sumCheque += $sumLine;
               
              
              

              // ยอดเช็ค ที่รับ
              $PostCheque               = $_POST['Cheque']['balance'];
              $model->know_date         = $_POST['Cheque']['know_date'];

              $models->type             = $model->type;
              $models->bank             = $model->bank;
              $models->bank_account     = '-';
              $models->bank_branch      = $model->bank_branch;
              $models->bank_id          = $model->bank_id;
              $models->create_date      = date('Y-m-d H:i:s');
              $models->transfer_time    = date('H:i',strtotime($model->transfer_time));
              

              $models->posting_date     = $_POST['Cheque']['posting_date'];
              $models->tranfer_to       = $model->tranfer_to;

              $models->cust_no_         = $value['cust'];

              
              $models->apply_to         = $Inv->id;
              $models->apply_to_no      = $Inv->no_;
              $models->apply_to_status  = $value['status'];

              // if($value['status']=='Open'){
              //   $models->apply_to         = 0;
              // }


              //$models->balance          = $sumLine;
              $models->balance          = $_POST['row-balance'][$Inv->id];
              $models->balance_cheque   = $PostCheque;

              $models->inv_total        = $_POST['row-inv_total'][$Inv->id] * 1;
              $models->inv_discount     = $_POST['row-discount'][$Inv->id] * 1; 
              $models->inv_vat          = $_POST['row-inv_vat'][$Inv->id] * 1;        
              $models->inv_include_vat  = $_POST['row-inv_include_vat'][$Inv->id] * 1;        
              
              $models->source_id        = $Nextid;

              $models->post_date_cheque = ($model->post_date_cheque != '' 
                                            ? $model->post_date_cheque
                                            : '');
              $models->remark           = $model->remark;
              $models->user_id          = Yii::$app->user->identity->id;
              $models->comp_id          = Yii::$app->session->get('Rules')['comp_id'];

              //$models->balance          = $_POST['row-balance'][$Inv->id];

              
/*

              if ($i == $len - 1) {

                // ถ้ามีหลายใบ
                // ให้บันทึกยอดที่เกิน ในใบสุดท้าย 

                if($len >= 2){
                  
                  // ถ้ายอดรับเช็ค มากกว่า ผลรวม 
                  // ให้เอาส่วนเกินไปรวมกับยอดสุดท้าย
                  $TotalCheque    = $PostCheque - $sumCheque;

                  if($TotalCheque > 0) {


                    $models->balance   = $sumLine + $TotalCheque;
                    
                  }
                  
                }else {
                  // ถ้ามีใบเดียว
                  // ให้บันทึกยอดที่รับเช็ค
                  $models->balance   = $PostCheque;
                }

              }
*/
                $models->save(false);
   
                $i++;

                if($models!=null){
                    
               

                    try{            
                
                        // <!---- Save Log ----->
                        $tracking   = new \common\models\OrderTracking();
                        

                        $tracking->event_date       = date('Y-m-d H:i:s');
                        $tracking->doc_type         = 'Payment';
                        $tracking->doc_id           = $models->id;
                        $tracking->doc_no           = $models->bank;
                        $tracking->doc_status       = 'Pending';
                        $tracking->amount           = $models->balance;
                        $tracking->remark           = $models->type.',STATUS :  Pending ,ID : '.$models->bank_id.', GROUP : '.$models->source_id;
                        $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
                        $tracking->lat_long         = '';
                        $tracking->create_by        = Yii::$app->user->identity->id;
                        $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
                        $tracking->track_for_table  = 'sale_header';
                        $tracking->track_for_id     = $models->getSaleHeader();


                        $tracking->save();

                    // <!---- /.Save Log ----->
                    } catch (\Exception $e) {	
                        
                        $bot =  \common\models\LineBot::findOne(5);
                        $msgs = "\r\n".'Payment ERROR'."\r\n\r\n";
                        $msgs.= Yii::t('common','{:e}',[':e' => $e])."\r\n";                    
                        $msgs.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                        $bot->notify_message($msgs);							 
                        //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                    }
                }	

            }



            try{                      
        
                // Line Notify                  
                $bot =  \common\models\LineBot::findOne(1);
                $msg = "\r\n".'Payment'."\r\n\r\n";
                $msg.= $model->bankaccount->name.' #'.$model->bankaccount->bank_no.' #'.$model->bankaccount->banklist->name."\r\n";
                $msg.= ($model->know_date == 1 ? $model->post_date_cheque : 'ไม่ระบุวันที่')."\r\n";
                $msg.= number_format($model->balance,2)." บาท \r\n\r\n";
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                $bot->notify_message($msg);		
                

            } catch (\Exception $e) {	
                $bot =  \common\models\LineBot::findOne(5);
                $msgs = "\r\n".'Payment ERROR'."\r\n\r\n";
                $msgs.= Yii::t('common','{:e}',[':e' => $e])."\r\n";                    
                $msgs.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                $bot->notify_message($msgs);							 
                //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }


            // ##### All in a Cheque. #####
            // $doc    = array();
            // $cust   = '';
            // foreach (Yii::$app->session->get('apply_to') as $key => $value) {

            //           $doc[]= $value['id'];
            //           $cust = $value['cust'];
                        

            // }
 
            // $model->cust_no_        = $cust;

            // $model->apply_to        = implode(',',$doc);

            // $model->user_id         = Yii::$app->user->identity->id;
            // $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

            // $model->create_date     = date('Y-m-d');

            // $model->save(false);

            // ##### /.All in a Cheque. #####




            //return $this->renderPartial('_view', ['id' => $model->id]);

            if(Yii::$app->request->isAjax){
                
                return $this->renderAjax('_view',['model' => $models]);
            }else {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            

        } else {

            if(Yii::$app->request->isAjax){
                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }


            return $this->render('create', [
                'model' => $model,
            ]);

        }
    }

    /**
     * Updates an existing Cheque model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        

        if ($model->load(Yii::$app->request->post())) {

            $oldBalance = $model->balance;
            //$document   = Yii::$app->session->get('apply_to');

            // var_dump(Yii::$app->session->get('apply_to'));
            // exit();
            // $doc    = array();
            // $cust   = '';
            // foreach (Yii::$app->session->get('apply_to') as  $value) {

            //           $doc[]= $value['id'];
            //           $cust = $value['cust'];
                        

            // }
 
            // $model->cust_no_        = $cust;

            // $model->apply_to        = implode(',',$doc);

            /*
            // ตรวจสอบก่อน ว่ามีการ approve ไปหรือยัง
            // ถ้า approve แล้ว ไม่สามารถแก้ไขได้
            $ApproveCheck           = \common\models\Approval::find()->where(['source_id' => $model->id]);
            if($ApproveCheck->exists()){
              //echo 'Error! ไม่สามารถแก้ไขได้ เนื่องจากมีการอนุมัติรายการแล้ว';
              Yii::$app->session->setFlash('danger', '<i class="fas fa-exclamation"></i> '.Yii::t('common','Error <br />ไม่สามารถแก้ไขได้ <h4>เนื่องจากมีการอนุมัติรายการแล้ว</h4>'));
                return $this->render('update', [
                    'model' => $model,
                ]);
              //exit();
            }
            */

            $model->transfer_time   = date('H:i',strtotime($model->transfer_time));

            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            
            $model->save(false);

            if(isset($_POST['row-id'])){
                foreach ($_POST['row-id'] as $key => $value) {
                    
                    // $cheque             = Cheque::find()->where(['apply_to' => $key, 'apply_to_status' => $_POST['row-status'][$key]])
                    //                     ->andWhere(['source_id' => $_POST['Cheque']['source_id']])->one();
                    $cheque             = Cheque::find()
                                        ->where(['id' => $_POST['row-id'][$key]])
                                        ->andWhere(['source_id' => $_POST['Cheque']['source_id']])
                                        ->one();
                    if($cheque != null){
                         
                        $cheque->balance            = $_POST['row-balance'][$key];
                        $cheque->balance_cheque     = $_POST['Cheque']['balance'];

                        if(isset($_POST['know_date'])){
                            $cheque->know_date         = $_POST['Cheque']['know_date'];
                            
                            if($_POST['Cheque']['know_date'] == 1){
                                if(isset($_POST['post_date_cheque'])){
                                    $cheque->post_date_cheque     = $_POST['Cheque']['post_date_cheque'];
                                }        
                            }else{
                                $cheque->post_date_cheque = NULL;
                            }
                        }
            
                        
                        
                        $cheque->inv_total           = $_POST['row-inv_total'][$key] * 1;
                        $cheque->inv_discount        = $_POST['row-discount'][$key] * 1;        
                      
                        $cheque->inv_vat             = $_POST['row-inv_vat'][$key] * 1;        
                        $cheque->inv_include_vat     = $_POST['row-inv_include_vat'][$key] * 1;        
            
                        $cheque->create_date        = date('Y-m-d');
                        if($cheque->save(false)){
                            //var_dump( $cheque->balance);
                        }
                    }
                    
                }
                
            }

            
            

           // $model->save(false);

            try{                      
                if(Yii::$app->user->identity->id != 1){
                    // Line Notify
                    $bot =  \common\models\LineBot::findOne(1);
                    $msg = "\r\n".'Payment Update'."\r\n\r\n";
                    $msg.= ($model->invoice ? $model->invoice->no_ : ' ')."\r\n";
                    $msg.= $model->bankaccount->name."\r\n#".$model->bankaccount->bank_no."\r\n#".$model->bankaccount->banklist->name."\r\n";
                    $msg.= ($model->know_date == 1 ? $model->post_date_cheque : 'ไม่ระบุวันที่')."\r\n";
                    $msg.= number_format($oldBalance,2).' => ' .number_format($model->balance,2)." บาท \r\n\r\n";
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                    $bot->notify_message($msg);	
                }				

            } catch (\Exception $e) {					 
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }

            
            // <!---- Save Log ----->
            $tracking   = new \common\models\OrderTracking();

            

            $tracking->event_date       = date('Y-m-d H:i:s');
            $tracking->doc_type         = 'Payment';
            $tracking->doc_id           = $model->id;
            $tracking->doc_no           = $model->bank;
            $tracking->doc_status       = 'Update';
            $tracking->amount           = $model->balance;
            $tracking->remark           = $model->type.',STATUS :  Update , ID : '.$model->bank_id.', GROUP : '.$model->source_id;
            $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
            $tracking->lat_long         = '';
            $tracking->create_by        = Yii::$app->user->identity->id;
            $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
            $tracking->track_for_table  = 'sale_header';
            $tracking->track_for_id     = $model->getSaleHeader();


            $tracking->save();

            // <!---- /.Save Log ----->
             


            // return $this->renderPartial('_view', ['id' => $model->id]);

            if(Yii::$app->request->isAjax){
                return $this->renderAjax('_view',['model' => $model]);
            }else {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            

        } else {

            if(Yii::$app->request->isAjax){


                // $ApproveCheck           = \common\models\Approval::find()->where(['source_id' => $model->id]);
                // if($ApproveCheck->exists()){

                //   echo "<script>
                //         swal(
                //           '".Yii::t('common','Error ! ไม่สามารถแก้ไขรายการได้')."',
                //           '".Yii::t('common','เนื่องจาก ถูกอนุมัติแล้ว')."',
                //           'warning'
                //         );
                //         $('#chequeModal').modal('hide'); 
                //         $('body').attr('style','overflow:auto; margin-right:0px; padding-right: 15px;');
                //         </script>";
                //   exit();
                // }


                return $this->renderAjax('_form', [
                    'model' => $model,
                ]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionUpdates($id)
    {
        $model = $this->findModel($id);        

        if ($model->load(Yii::$app->request->post())) {

            $oldBalance = $model->balance;
             

            $model->transfer_time   = date('H:i',strtotime($model->transfer_time));

            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            
            $model->save(false);

            if(isset($_POST['row-id'])){
                foreach ($_POST['row-id'] as $key => $value) {
                    $cheque             = Cheque::find()
                                        ->where(['id' => $_POST['row-id'][$key]])
                                        ->andWhere(['source_id' => $_POST['Cheque']['source_id']])
                                        ->one();
                    if($cheque != null){
                         
                        $cheque->balance            = $_POST['row-balance'][$key];
                        $cheque->balance_cheque     = $_POST['Cheque']['balance'];

                        if(isset($_POST['know_date'])){
                            $cheque->know_date         = $_POST['Cheque']['know_date'];
                            
                            if($_POST['Cheque']['know_date'] == 1){
                                if(isset($_POST['post_date_cheque'])){
                                    $cheque->post_date_cheque     = $_POST['Cheque']['post_date_cheque'];
                                }        
                            }else{
                                $cheque->post_date_cheque = NULL;
                            }
                        }
            
                        
                        
                        $cheque->inv_total           = $_POST['row-inv_total'][$key] * 1;
                        $cheque->inv_discount        = $_POST['row-discount'][$key] * 1;        
                      
                        $cheque->inv_vat             = $_POST['row-inv_vat'][$key] * 1;        
                        $cheque->inv_include_vat     = $_POST['row-inv_include_vat'][$key] * 1;        
            
                        $cheque->create_date        = date('Y-m-d');
                        if($cheque->save(false)){
                             
                        }
                    }
                    
                }
                
            }


            try{                      
    
                // Line Notify
                $bot =  \common\models\LineBot::findOne(1);
                $msg = "\r\n".'Payment Update'."\r\n\r\n";
                $msg.= ($model->invoice ? $model->invoice->no_ : ' ')."\r\n";
                $msg.= $model->bankaccount->name."\r\n#".$model->bankaccount->bank_no."\r\n#".$model->bankaccount->banklist->name."\r\n";
                $msg.= ($model->know_date == 1 ? $model->post_date_cheque : 'ไม่ระบุวันที่')."\r\n";
                $msg.= number_format($oldBalance,2).' => ' .number_format($model->balance,2)." บาท \r\n\r\n";
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                $bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }

            
            // <!---- Save Log ----->
            $tracking   = new \common\models\OrderTracking();

            

            $tracking->event_date       = date('Y-m-d H:i:s');
            $tracking->doc_type         = 'Payment';
            $tracking->doc_id           = $model->id;
            $tracking->doc_no           = $model->bank;
            $tracking->doc_status       = 'Update';
            $tracking->amount           = $model->balance;
            $tracking->remark           = $model->type.',STATUS :  Update , ID : '.$model->bank_id.', GROUP : '.$model->source_id;
            $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
            $tracking->lat_long         = '';
            $tracking->create_by        = Yii::$app->user->identity->id;
            $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
            $tracking->track_for_table  = 'sale_header';
            $tracking->track_for_id     = $model->getSaleHeader();


            $tracking->save();

            // <!---- /.Save Log ----->
             


           

            if(Yii::$app->request->isAjax){
                return json_encode([
                    'html' => $this->renderAjax('_view',['model' => $model]),
                    'status' => 200
                ]);
                //return $this->renderAjax('_view',['model' => $model]);
            }else {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            

        } else {

            if(Yii::$app->request->isAjax){

                return json_encode([
                    'html' => $this->renderAjax('_form',['model' => $model]),
                    'status' => 200,
                    'id' => $model->source_id
                ]);

                // return $this->renderAjax('_form', [
                //     'model' => $model,
                // ]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Cheque model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model      = $this->findModel($id);
        $status     = 200;
        $message    = Yii::t('common','Success');

        $total      = $model->sumTotal;
        $transaction = Yii::$app->db->beginTransaction();
		try {
        
            try{                      
        
                // Line Notify
                $bot =  \common\models\LineBot::findOne(1);
                $msg = "\r\n".'Payment Deleted'."\r\n\r\n";
                $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";

                $msg.= $model->bankaccount->name.' #'.$model->bankaccount->bank_no.' #'.$model->bankaccount->banklist->name."\r\n";
                $msg.= ($model->know_date == 1 ? $model->post_date_cheque : 'ไม่ระบุวันที่')."\r\n";
                $msg.= number_format($total,2)." บาท \r\n\r\n";
                
                $bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }

            // <!---- Save Log ----->
            $tracking   = new \common\models\OrderTracking();

            $tracking->event_date       = date('Y-m-d H:i:s');
            $tracking->doc_type         = 'Payment';
            $tracking->doc_id           = $model->id;
            $tracking->doc_no           = $model->bank;
            $tracking->doc_status       = 'Delete';
            $tracking->amount           = $total;
            $tracking->remark           = $model->type.',STATUS :  Delete , ID : '.$model->bank_id.', GROUP : '.$model->source_id;
            $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
            $tracking->lat_long         = '';
            $tracking->create_by        = Yii::$app->user->identity->id;
            $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
            $tracking->track_for_table  = 'sale_header';
            $tracking->track_for_id     = $model->getSaleHeader();

            $tracking->save();

            // <!---- /.Save Log ----->

            $model->delete();

             
            $transaction->commit();	

        } catch (\Exception $e) {
            
            Yii::$app->getSession()->addFlash('danger',Yii::t('common','{:e}',[':e' => $e])); 
            $transaction->rollBack();

            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
            //throw $e;
        }

        if(Yii::$app->request->isAjax){
            return json_encode([
                'status'    => $status,
                'message'   => $message,
                'total'     => 0
            ]);
        }



        return $this->redirect(['index']);
    }



    public function actionPrint($id)
    {
        
        $query      = Cheque::find()->where(['source_id' => $id])->orderBy(['priority' => SORT_ASC]); 
        

        $paper      = Yii::$app->request->get('paper')!= '' ? Yii::$app->request->get('paper') : 'A4';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        $model      = $query->one();

        $template   = \common\models\PrintPage::findPrint('cheque');  
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
            'height'    => $template->body_height,
            'pagesize'  => $pageSize,
            'fontsize'  => $template->font_size
        ];

        
        $companyLogo = Yii::$app->session->get('logo');
 
 
        $Bahttext   = new \admin\models\FunctionBahttext();
    
        $defineHeader = [
            '{VALUE_TITLE}'         => $model->no,
            '{COMPANY_LOGO}'        => '<img src="'.$companyLogo.'" style="width: 100px;">',
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
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->posting_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,                  
         
            '{CREATOR}'             =>  '',        
            '{CUSTOMER_CODE}'       => $model->customer->code,
            '{CUSTOMER_NAME}'       => $model->customer->name,
            '{CUSTOMER_PHONE}'      => $model->customer->phone,
            '{CUSTOMER_FAX}'        => $model->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->customer->fullAddress['address'],
            '{CUSTOMER_TAX}'        => $model->customer->vat_regis,       
            '{TRANSPORT_BY}'        => $model->customer->transport, 
            '{CUSTOMER_HEAD}'       => $model->customer->headofficetb->data_char,                  
            '{SALE_NAME}'           => '',
            '{SALE_SUR_NAME}'       => '',
            '{SALE_CODE}'           => '',                  
         
            '{REF_TO}'              => $model->apply_to_no,        
         
            //'{DUE_DATE}'            => $model->paymentdue,
            //'{DUE_DATE_TH}'         => date('d/m/y',strtotime($model->paymentdue.' + 543 Years')),
            '{PO_REFERENCE}'        => $model->apply_to_no,
        
            //'{VALUE_BEFOR_VAT}'     => number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2),
        
             
            '{BANK_NAME}'           => $model->banklist->name,
            '{CHEQUE_NO}'           => $model->bank != 0 
                                            ? $model->bankaccount->bank_no
                                            : '-  ',
            '{CHEQUE_DATE}'         => date('d/m/y',strtotime($model->post_date_cheque.' + 543 Years')),
            //'{VALUE_PERCENT_DISCOUNT}' => ($model->percent_discount)? '('.number_format($model->percent_discount).' %)' : '',
         
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => wordwrap($model->remark, 250, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            // '{VALUE_TOTAL}'         => number_format($model->sumTotal,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            //'{VALUE_SUBTOTAL}'      => number_format($model->sumTotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            //'{VALUE_VAT}'           => $model->vat_percent.' %',
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->sumTotal,2),
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht($model->sumTotal),     
             
        ]; 


 
        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme-gray',[
            'model'         => $model,
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
            'mode'          => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            'orientation'   => $template->paper_orientation,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            'filename'      => $model->apply_to_no.'.pdf',
            'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } ',
            'options'       => [
                'title' =>  $model->no
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


    public function actionGetData(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $fdate  = date('Y-m-d', strtotime($data->fdate));
        $tdate  = date('Y-m-d', strtotime($data->tdate));
        $status = 200;
        $message= Yii::t('common','Success');

        $cust           = $data->cust != '' ? $data->cust : NULL;
        $custList       = $data->custList;

        $source = [];
        $raws   = [];
        
        $query = Cheque::find()
                ->select('source_id')
                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWHere(['between', 'DATE(post_date_cheque)', $fdate, $tdate])               
                ->groupBy('source_id');

        if($data->bank > 0){
            $query->andWhere(['tranfer_to' => $data->bank]);
        }

         

        if(count($custList) > 0){
            $query->andWhere(['IN', 'cust_no_', $custList]);
        }else{
            if($cust){
                $query->andWhere(['cust_no_' => $cust]);
            }                
        }
        
        foreach ($query->all() as $value) {
            $model     = Cheque::findOne(['source_id' => $value->source_id]);
           // $source[] = $RC->id;
            $raws[] = (Object)[
                'id'            => $model->id,
                'source_id'     => $model->source_id,
                'cheque_date'   => $model->post_date_cheque,
                'posting_date'  => $model->posting_date,
                // 'vat'           => $model->inv 
                //                     ? $model->inv->vat_percent
                //                     : '',
                // 'type'          => $model->inv
                //                     ? $model->inv->doc_type
                //                     : '',
                'inv'           => $model->apply_to_no,
                'inv_list'      => $model->chequeList, //[ 0 => ['no' => $model->apply_to_no]],// $model->chequeList,
                'no'            => $model->no 
                                    ? $model->no
                                    : '',
                'remark'        => $model->remark,
                'balance'       => $model->balance_cheque * 1,
                'bank'          => $model->bankaccount->name,
                'cust_name'     => $model->customer->name,
                // 'cust_addr' => $model->customer
                //                 ? $model->customer->fulladdress
                //                 : ''
            ];
        }

         
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }

    public function actionGetLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status             = 200;
        $message            = Yii::t('common','Success');
        $bankId             = 0;
        $bank_id            = '000-0-00000-0';
        $bankName           = 0;
        $tranfer_to         = 11;
        $tranfer_name       = '';
        $remark             = '';
        $know_date          = 1;
        $posting_date       = date('Y-m-d');
        $post_date_cheque   = date('Y-m-d');
        $type               = 'Cash';
        $bankImg            = 'note-icon-63268.png';
        $owner              = '';
        $owner_name         = '';
        $update_by_id       = '';
        $update_by_name     = '';

        $raws   = [];        
         
        $queryAll = Cheque::find()->where(['source_id' => $data->id])->all();

        foreach ($queryAll as $key => $model) {
            $raws[] = (Object)[
                'id'            => $model->id,
                'source_id'     => $model->source_id,                
                'cheque_date'   => $model->post_date_cheque,
                'posting_date'  => $model->posting_date,               
                'inv'           => $model->apply_to_no,
                'invId'         => $model->apply_to,
                'no'            => $model->no,
                //'remark'        => $model->remark,
                'balance'       => $model->balance * 1,
                'total_balance' => $model->balance_cheque * 1,
                'bank'          => $model->bank,
                //'bankName'      => $model->banklist->name,
                'bankId'        => $model->bank_id,
                'to'            => $model->tranfer_to,
                'cust'          => $model->cust_no_,
                'invTotal'      => $model->inv_total,
                'priority'      => $model->priority
            ];

            $type               = $model->type;
            $bankName           = $model->banklist->name;
            $bankId             = $model->bank;
            $bank_id            = $model->bank_id !='' ? $model->bank_id :'000-0-00000-0';
            $tranfer_to         = $model->tranfer_to;
            $tranfer_name       = $model->bankaccount->name. ' ' .$model->bankaccount->branch. ' ' .$model->bankaccount->bank_no;
            $remark             = $model->remark;
            $know_date          = $model->know_date;
            $posting_date       = $model->posting_date;   
            $post_date_cheque   = $model->post_date_cheque;
            $bankImg            = $model->banklist->imageFile;

            if($key == 0){
                $owner              = $model->user_id;
                $owner_name         = $model->user->username;
            }
            
            $update_by_id       = $model->user_id;
            $update_by_name     = $model->user->username;
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
            'header' => [
                'type'              => $type,
                'bankName'          => $bankName,
                'bankImg'           => 'uploads/'.$bankImg,
                'bankId'            => $bankId,
                'bank_id'           => $bank_id,
                'tranferId'         => $tranfer_to,
                'tranferName'       => trim($tranfer_name),
                'remark'            => trim($remark),
                'know_date'         => $know_date,
                'posting_date'      => $posting_date,
                'post_date_cheque'  => $post_date_cheque,
                'owner'             => $owner,
                'owner_name'        => $owner_name,
                'update_by_id'      => $update_by_id,
                'update_by_name'    => $update_by_name,
               
            ],
            'raws'   => $raws
        ]);
    }

    public function actionUpdateHeader(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status             = 200;
        $message            = Yii::t('common','Success');

        Cheque::updateAll([
            'type'              => $data->bankType, 
            'bank'              => $data->bankList, 
            'posting_date'      => $data->cheque_date,
            'bank_id'           => $data->bank_id,
            'tranfer_to'        => isset($data->tranfer_to) ? $data->tranfer_to : 11,
            'know_date'         => $data->know_date,
            'post_date_cheque'  => $data->pdc,
            'remark'            => $data->remark       
        ], ['source_id' => $data->source]);
         
    }

    public function doCreateLine($source, $post){

        $model = new Cheque();

        $clone = Cheque::findOne(['source_id' => $post->source_id]);
        if($clone == null){

            
            $maxVal = Cheque::find()->select('max(source_id) as source_id')->one();
            $Nextid = $maxVal->source_id + 1;

            // New Record
            $clone = (Object)[
                'source_id'     => $Nextid,
                'type'          => 'Cash',
                'bank'          => 0,
                'bank_branch'   => NULL,
                'bank_id'       => $post->bank_id,
                'transfer_time' => NULL,
                'tranfer_to'    => $post->tranfer_to,
                'cust_no_'      => $source->cust_no_,
                'sumtotals'     => (Object)[
                    'total' => $source->sumtotals->total
                ],
                'discount'      => $source->discount,
                'vat_percent'   => $source->vat_percent,
                'include_vat'   => $source->include_vat,
                'status'        => $source->status,
                'post_date_cheque' => date('Y-m-d'),
                'inv_vat'       => $source->vat_percent
            ];

            $model->source_id           = $Nextid;

        }else{
            $model->source_id           = $post->source_id;
        }

        
        
        
        $model->no                  = $post->no;
        $model->type                = $clone->type;
        $model->bank                = $clone->bank;
        $model->bank_account        = '-';
        $model->bank_branch         = $clone->bank_branch;
        $model->bank_id             = $clone->bank_id!= '' ? $clone->bank_id :'000-0-00000-0';
        $model->create_date         = date('Y-m-d H:i:s');
        $model->know_date           = 1; //1=Know date
        $model->transfer_time       = $clone->transfer_time;       
        $model->posting_date        = $post->posting_date;
        $model->tranfer_to          = isset($post->tranfer_to) ? $post->tranfer_to : 11;
        $model->cust_no_            = $clone->cust_no_;      
        $model->apply_to            = $source->id;
        $model->apply_to_no         = $source->no_;
        $model->inv_total           = $source->sumtotals->total;
        $model->inv_discount        = $source->discount;        
      
        $model->inv_vat             = $clone->inv_vat;
        $model->inv_include_vat     = $source->include_vat;

        $model->apply_to_status     = $source->status;
        $model->balance             = $post->balance;
        $model->balance_cheque      = $post->balance;   

        

        $model->post_date_cheque    = $clone->post_date_cheque;
        $model->remark              = $post->remark;
        $model->user_id             = Yii::$app->user->identity->id;
        $model->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

        if(!$model->save()){
            $this->status  = 500;
            $this->message = Yii::t('common','{:e}',[':e' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)]);
            //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)]));
        };

        // update all $model->balance_cheque => $post->balance
        Cheque::updateAll([ 
            'balance_cheque'    => $model->sumTotal, 
            'no'                => $post->no,
            'remark'            => $post->remark,
            'post_date_cheque'  => $model->post_date_cheque,
            'type'              => $model->type,
            'know_date'         => $model->know_date,
            'bank_account'      => $model->bank_account,
            'tranfer_to'        => isset($post->tranfer_to) ? $post->tranfer_to : 11
        ], ['source_id' => $post->source_id]);

        return $model;
    
    }

    public function actionCreateLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $this->status   = 200;
        $this->message  = Yii::t('common','Success');
        $id             = 0;
        $source_id      = 0;

        // ต้อง Posted แล้วเท่านั้น
        $source     = \common\models\RcInvoiceHeader::find()
                    ->where(['no_' => $data->inv])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->one();

        if($source != null){ // ดึงข้อมูล

           
            $save   = $this->doCreateLine($source, $data);                
            if(!$save){
                $this->status     = 500;
                $this->message    = json_encode($save->getErrors(),JSON_UNESCAPED_UNICODE);
            }else{
                $id         = $save->id;
                $source_id  = $save->source_id;
            }
            
 

        }else{

          
            $save   = $this->doCreateLine((Object)[
                'id'        => NULL,
                'discount'  => 0,
                'no_'       => $data->inv,
                'status'    => NULL,
                'source_id' => NULL,
                'vat_percent'=> NULL,
                'include_vat'=> NULL,
                'sumtotals' => (Object)[
                    'total' => 0
                ]
            ], $data);    

            if(!$save){
                $this->status     = 500;
                $this->message    = json_encode($save->getErrors(),JSON_UNESCAPED_UNICODE);
            }else{
                $id         = $save->id;
                $source_id  = $save->source_id;
            }
  
        }


        return json_encode([
            'status'    => $this->status,
            'message'   => $this->message,
            'id'        => $id,
            'source_id' => $source_id,
            'rand'      => $data->rand
        ]);
    }


    public function actionUpdateField(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status         = 200;
        $message        = Yii::t('common','Success');
        $value          = $data->value;

        if(isset($data->id)){

            
            $model          = Cheque::find()
                                ->where(['source_id' => $data->id])
                                ->orderBy(['id' => SORT_ASC])
                                ->one();

            
            $transaction    = Yii::$app->db->beginTransaction();
            try {     
                
                // ไม่ต้องเช็ค
                // $exists     = Cheque::find()
                //             ->where(['no' => $data->value])
                //             ->andWhere(['<>','source_id',$model->source_id])
                //             ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                //             ->one();

                // if($data->value == ''){
                //     $exists = false;  // ลบเลขที่
                // }
                $exists = false; 

                if($exists != null){
                    $status     = 403;
                    $message    = $data->value. '  ' .Yii::t('common','Already exists') . ' '.$model->post_date_cheque.' ('. ($model->customer ? $model->customer->name : ')');    
                }else{            
                    // update all
                    Cheque::updateAll([$data->field => $data->value, 'balance_cheque' => $model->sumTotal, 'post_date_cheque' => $model->post_date_cheque], ['source_id' => $data->id]);
                    
                }             
                
                $transaction->commit();
            } catch (\Exception $e) {           
                $transaction->rollBack();
                $status     = 500;
                $message    = json_encode($e,JSON_UNESCAPED_UNICODE);       
            } 

            
        
            return json_encode([
                'status' => $status,
                'message' => $message,
                'value'   => $value
            ]);

        }else{
            return json_encode([
                'status' => 404,
                'message' => Yii::t('common','Not found')
            ]);
        }


    }

    
    public function actionDeleteRow(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status         = 200;
        $message        = Yii::t('common','Success');
       
       
         
        $model          = $this->findModel($data->id);
        
        $source         = $model->source_id;
      
        try{                      
    
            // Line Notify
            $bot =  \common\models\LineBot::findOne(1);
            $msg = "\r\n".'Payment Deleted'."\r\n\r\n";
            $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";

           // $msg.= $inv."\r\n";
            $msg.= $model->bankaccount->name.' #'.$model->bankaccount->bank_no.' #'.$model->bankaccount->banklist->name."\r\n";
           // $msg.= $customer."\r\n";
            $msg.= ($model->know_date == 1 ? $model->post_date_cheque : 'ไม่ระบุวันที่')."\r\n";
            $msg.= number_format($model->balance,2)." บาท \r\n\r\n";
            
            $bot->notify_message($msg);					

       
            // <!---- Save Log ----->
            $tracking   = new \common\models\OrderTracking();

            

            $tracking->event_date       = date('Y-m-d H:i:s');
            $tracking->doc_type         = 'Payment';
            $tracking->doc_id           = $model->id;
            $tracking->doc_no           = $model->bank;
            $tracking->doc_status       = 'Delete';
            $tracking->amount           = $model->balance;
            $tracking->remark           = $model->type.',STATUS :  Delete , ID : '.$model->bank_id.', GROUP : '.$model->source_id;
            $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
            $tracking->lat_long         = '';
            $tracking->create_by        = Yii::$app->user->identity->id;
            $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
            $tracking->track_for_table  = 'sale_header';
            $tracking->track_for_id     = $model->getSaleHeader();


            $tracking->save();

            // <!---- /.Save Log ----->

            
            
        } catch (\Exception $e) {					 
            //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
        }



        if(!$this->findModel($data->id)->delete()){
            $status = 500;
            $message= 'Error';
        }

        // update all $model->balance_cheque => $post->balance
        Cheque::updateAll([ 'balance_cheque' => $model->sumTotal], ['source_id' => $source]);


        return json_encode([
            'status' => $status,
            'message' => $message,
        ]);
    }


    public function actionDeleteSource(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status         = 200;
        $message        = Yii::t('common','Success');         
        $model          = Cheque::find()->where(['source_id' => $data->id])->one();
        
        $customer       = $model->customer
                            ? $model->customer->name
                            : '';

        $transaction = Yii::$app->db->beginTransaction();
        try {    

            try{                      
        
                // Line Notify
                $bot =  \common\models\LineBot::findOne(5);
                $msg = "\r\n".'Payment Deleted'."\r\n\r\n";
                $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";
            
                $msg.= $model->bankaccount->name.' #'.$model->bankaccount->bank_no.' #'.$model->bankaccount->banklist->name."\r\n";
                $msg.= $customer."\r\n";
                $msg.= ($model->know_date == 1 ? $model->post_date_cheque : 'ไม่ระบุวันที่')."\r\n";
                $msg.= number_format($model->balance,2)." บาท \r\n\r\n";

                $inv = json_encode($model->chequeList,JSON_UNESCAPED_UNICODE);
                $msg.= $inv."\r\n";
                
                $bot->notify_message($msg);					
            
            } catch (\Exception $e) {					 
                //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }

            // <!---- Save Log ----->
            $tracking   = new \common\models\OrderTracking();            

            $tracking->event_date       = date('Y-m-d H:i:s');
            $tracking->doc_type         = 'Payment';
            $tracking->doc_id           = $model->id;
            $tracking->doc_no           = $model->bank;
            $tracking->doc_status       = 'Delete';
            $tracking->amount           = $model->balance;
            $tracking->remark           = $model->type.',STATUS :  Delete , ID : '.$model->bank_id.', GROUP : '.$model->source_id;
            $tracking->ip_address       = $_SERVER['REMOTE_ADDR'];
            $tracking->lat_long         = '';
            $tracking->create_by        = Yii::$app->user->identity->id;
            $tracking->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
            $tracking->track_for_table  = 'sale_header';
            $tracking->track_for_id     = $model->getSaleHeader();

            $tracking->save();
            // <!---- /.Save Log -----> 


            if(!Cheque::deleteAll(['source_id' => $data->id])){
                $status     = 500;
                $message    = 'Error Delete';   
            }

            $transaction->commit();	

        } catch (\Exception $e) {
            $transaction->rollBack();
            $status         = 500;
            $message        = Yii::t('common','{:e}',[':e' => $e]);  
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function actionCalculateRow(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status = 200;
        $message= Yii::t('common','Success');
       
        $raws   = [];        
         
        $queryAll = Cheque::find()->where(['source_id' => $data->id])->all();

        foreach ($queryAll as $key => $model) {

            //$source     = \common\models\RcInvoiceHeader::find()
            $source     = \common\models\ViewRcInvoice::find()
                        //->where(['id' => $model->apply_to])
                        ->where(['no_' => $model->apply_to_no])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->one();
            if($source != null){
                $model->inv_total       = $source->sumtotals->total;
                $model->inv_discount    = $source->sumtotals->discount;
                $model->inv_vat         = $source->vat_percent;
                $model->inv_include_vat = $source->include_vat;
                $model->apply_to_no     = $source->no_;
                $model->apply_to_status = $source->status;
                $model->apply_to        = $source->id;
                $model->save();
            }
           
            $raws[] = (Object)[
                'id'            => $model->id,
                'source_id'     => $model->source_id,
                'cheque_date'   => $model->post_date_cheque,
                'posting_date'  => $model->posting_date,               
                'inv'           => $model->apply_to_no,
                'invId'         => $model->apply_to,
                'no'            => $model->no,
                'remark'        => $model->remark,
                'balance'       => $model->balance * 1,
                'total_balance' => $model->balance_cheque * 1,
                'bank'          => $model->banklist->name,
                'invTotal'      => $model->inv_total,
                'priority'      => $model->priority
            ];
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }

    /**
     * Finds the Cheque model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cheque the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cheque::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChangePriority(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status = 200;
        $message= Yii::t('common','Success');
        $raws   = [];

        foreach ($data->raws as $key => $value) {
            $model = Cheque::findOne($value->id);
            if($model != null){
                $model->priority = ($key + 1);
                $model->save();
                $raws[] = [
                    'id' => $model->id,
                    'priority' => $model->priority
                ];
            }
        }

        return json_encode(['raws' => $raws]);

    }

    public function actionGetDataDetail(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $fdate  = date('Y-m-d', strtotime($data->fdate));
        $tdate  = date('Y-m-d', strtotime($data->tdate));
        $status = 200;
        $message= Yii::t('common','Success');

        $cust           = $data->cust != '' ? $data->cust : NULL;
        $custList       = $data->custList;

        $source = [];
        $raws   = [];
        
        $query = Cheque::find()
                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWHere(['between', 'DATE(post_date_cheque)', $fdate, $tdate]);

        if($data->bank > 0){
            $query->andWhere(['tranfer_to' => $data->bank]);
        }

         

        if(count($custList) > 0){
            $query->andWhere(['IN', 'cust_no_', $custList]);
        }else{
            if($cust){
                $query->andWhere(['cust_no_' => $cust]);
            }                
        }
        
        foreach ($query->all() as $model) {
            //$model     = Cheque::findOne(['source_id' => $value->source_id]);
           // $source[] = $RC->id;
            $raws[] = (Object)[
                'id'            => $model->id,
                'source_id'     => $model->source_id,
                'cheque_date'   => $model->post_date_cheque,
                'posting_date'  => $model->posting_date,
                'bankFrom'      => $model->bank_id,
                'bankType'      => $model->type,
                'bankTo'        => $model->bankaccount->bank_no,
                // 'vat'           => $model->inv 
                //                     ? $model->inv->vat_percent
                //                     : '',
                // 'type'          => $model->inv
                //                     ? $model->inv->doc_type
                //                     : '',
                'inv'           => $model->apply_to_no,
                'inv_list'      => [ 0 => ['no' => $model->apply_to_no]],// $model->chequeList,
                'no'            => $model->no 
                                    ? $model->no
                                    : '',
                'remark'        => $model->remark,
                'balance'       => $model->balance * 1,
                'inv_total'     => $model->inv_total * 1,
                'bank'          => $model->bankaccount->name,
                'cust_name'     => $model->customer->name,
                'owner'         => $model->user->username
                // 'cust_addr' => $model->customer
                //                 ? $model->customer->fulladdress
                //                 : ''
            ];
        }

         
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);
    }
}
