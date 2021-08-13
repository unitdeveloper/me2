<?php

namespace admin\modules\approval\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

use common\models\Register;
use common\models\Company;
use common\models\AppsRules;
use admin\modules\approval\models\FunctionModels;
use admin\modules\approval\models\InvoiceModels;

use common\models\SaleHeader;

use admin\modules\tracking\models\FunctionTracking;

class ApproveController extends \yii\web\Controller
{
	public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // 'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    // [
                    //     'allow' => true,
                    //     'actions' => ['index','view','byc'],
                    //     'roles' => ['?'],
                    // ],
                    [
                        'allow' => true,
                        'actions' => ['approved','reject','sale-order'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'reject' => ['POST'],
                ],
            ],
        ];
    }

    public function actionApproved($id,$code)
    {
    	$model = $this->findRegister($id);
        
        $company = new Company();

        $Rules = new AppsRules();

        // Check Company Name exsits
        $Exists = $company->find()->where(['name' => $model->regis_name])->count();
        if($Exists==0){        
        
            // Check validate code before approve status
            if($model->gen_code === $code){

            	// Create new Company
            	$company->name 			= $model->regis_name;
            	$company->address 		= $model->regis_address;
            	$company->headoffice	= $model->branch;
                $company->vat_register  = $model->vat_regis;


            	if($company->save()){
                    $comp_id = $company->id;
                }


                // Creaet new Rules
                $Rules->id              = $Rules->find()->orderBy(['id' => SORT_DESC])->one()->id +1;
                $Rules->user_id         = $model->user_id;
                $Rules->comp_id         = $comp_id;
                 

                $Rules->date_created    = date('Y-m-d H:i:s');
                $Rules->permission_id   = 0;

                $Rules->save();

            	// Approve Request
            	$model->status 			= 'approved';
            	$model->approve_date 	= date('Y-m-d H:i:s');
            	$model->approve_by		= Yii::$app->user->identity->id;
                $model->comp_id         = $comp_id;
                 
                
                

                  
                $model->save();
                 

            	return $this->render('approved',['model' => $model]);

            }else {
            	throw new ForbiddenHttpException('Activate fail.... Because Invalid Code');
            }
        }else {
            return $this->render('approved',['model' => $model]);
        }

    }

    public function actionReject($id,$code)
    {        
        $model = $this->findRegister($id);
        
        // Check validate code before approve status
        
        if($model->gen_code === $code){

        	$model->status 			= 'reject';
        	$model->approve_date 	= date('Y-m-d H:i:s');
        	$model->approve_by		= Yii::$app->user->identity->id;
        	$model->reject_reason	= $_POST['Register']['reject_reason'];

        	$model->save();

        	return $this->render('reject',['model' => $model]);
        }else {
        	throw new ForbiddenHttpException('Activate fail.... Because Invalid Code');
        }
    }

    protected function findRegister($id)
    {
        if (($model = Register::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionSaleOrder()
    {
        $Fnc = new FunctionModels();
        $Inv = new InvoiceModels();

        $id = Yii::$app->request->post('param')['id'];
        $Reason = '';

        if(isset(Yii::$app->request->post('param')['reson'])){

            if(Yii::$app->request->post('param')['reson']!='') $Reason = ', Reason : '.Yii::$app->request->post('param')['reson'];
        } 


        // ################### Tracking Keeper ###################

            $current    = Yii::$app->request->post('param')['cur'];
            $Status     = Yii::$app->request->post('param')['apk'];

            if(Yii::$app->request->post('param')['apk'] == 'Cancel')  $Status = 'Pre-Cancel';

            // ถ้าขอยกเลิก แล้วไม่อนุมัติ  ให้เก็บประวัติอีกครั้ง
            if($current=='Pre-Cancel'){
                
                $SaleHeader = SaleHeader::findOne($id);  

                FunctionTracking::CreateTracking(
                        [
                            'doc_type'          => 'Sale-Order',
                            'doc_id'            => $SaleHeader->id,
                            'doc_no'            => $SaleHeader->no,
                            'doc_status'        => 'Reject-Pre-Cancel',
                            'amount'            => $SaleHeader->balance,
                            'remark'            => 'Current : '.$current.', Status : '.Yii::$app->request->post('param')['apk'].$Reason.', Credit : '.$SaleHeader->payment_term.' : '.date('Y-m-d', strtotime($SaleHeader->paymentdue)),
                            'track_for_table'   => 'sale_header',
                            'track_for_id'      => $SaleHeader->id,
                        ]);

            }  



            $SaleHeader = SaleHeader::findOne($id);  

            FunctionTracking::CreateTracking(
                        [
                            'doc_type'          => 'Sale-Order',
                            'doc_id'            => $SaleHeader->id,
                            'doc_no'            => $SaleHeader->no,
                            'doc_status'        => $Status,
                            'amount'            => $SaleHeader->balance,
                            'remark'            => 'Current : '.$current.', Status : '.Yii::$app->request->post('param')['apk'].$Reason.', Credit : '.$SaleHeader->payment_term.' : '.date('Y-m-d', strtotime($SaleHeader->paymentdue)),
                            'track_for_table'   => 'sale_header',
                            'track_for_id'      => $SaleHeader->id,
                        ]);

        // ################### /.Tracking Keeper ###################



        // Confirm Sale Order
        if(Yii::$app->request->post('param')['apk'] == 'Confirm')
        {
             

            echo $Fnc->consoleRender();
            echo $Fnc->UpdateSaleOrder($id,'Checking');

            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);

        // Reject Sale Order     
        }else if(Yii::$app->request->post('param')['apk'] == 'Reject'){

            $model = SaleHeader::findOne(Yii::$app->request->post('param')['id']);

            $model->reason_reject   = Yii::$app->request->post('param')['reson'];
            $model->save();

            echo $Fnc->consoleRender();
            echo $Fnc->UpdateSaleOrder($id,'Reject');
            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);



        }else if(Yii::$app->request->post('param')['apk'] == 'Shiped'){


            if($current=='Invoiced')
            {

            }else {

                echo $Fnc->consoleRender();
                echo $Fnc->UpdateSaleOrder($id,'Shiped');

                $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);

            }



        }else if(Yii::$app->request->post('param')['apk'] == 'Invoiced'){


            

            echo $Inv->CreateInvoice($id);

            
            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);

            // echo '<script>setTimeout(function() {
            //       window.location.href = "index.php?r=SaleOrders/saleorder/view&id='.$id.'";
            //     }, 1000);
            //       </script>';

        }else if(Yii::$app->request->post('param')['apk'] == 'Cancel'){


            $model = SaleHeader::findOne(Yii::$app->request->post('param')['id']);

            $model->reason_reject   = Yii::$app->request->post('param')['reson'];
            $model->save();

            echo $Fnc->consoleRender();
            echo $Fnc->UpdateSaleOrder($id,'Pre-Cancel');
            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);



        }else if(Yii::$app->request->post('param')['apk'] == 'Confirm-Cancel'){


            $model = SaleHeader::findOne(Yii::$app->request->post('param')['id']);

            $model->reason_reject   = Yii::$app->request->post('param')['reson'];
            $model->save();

            echo $Fnc->consoleRender();
            echo $Fnc->UpdateSaleOrder($id,'Cancel');
            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);



        }else if(Yii::$app->request->post('param')['apk'] == 'ShipNow'){

            
            $wh = new \admin\modules\warehousemoving\models\FunctionWarehouse();

            $model = SaleHeader::findOne(Yii::$app->request->post('param')['id']);
            if($model->confirm > 0){
                return $wh->CreateShipMent(Yii::$app->request->post('param'));
            }else{
                return $this->asJson([
                    'status'    => 405,
                    'message'   => Yii::t('common','Apologize'),
                    'suggestion'=> Yii::t('common','Waiting to be confirmed.'),
                ]);
            }
            
              

        }else if(Yii::$app->request->post('param')['apk'] == 'ShipNow-Test'){

            
           var_dump(Yii::$app->request->post('param'));
              

        }else if(Yii::$app->request->post('param')['apk'] == 'Checking'){

            

            echo $Fnc->consoleRender();
            echo $Fnc->UpdateSaleOrder($id,'Checking');
            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);
            

        }else {

            $this->ewRefresh("index.php?r=SaleOrders/saleorder/view&id=".$id."",1000);
            return 'Do Something-'.$_POST['param']['apk'];
              

        }

        
        
    }


    public function ewRefresh($url,$time)
    {
        echo '<script>setTimeout(function() {
                  window.location.href = "'.$url.'";
                }, '.$time.');
                  </script>';
                  
       

    }

}
