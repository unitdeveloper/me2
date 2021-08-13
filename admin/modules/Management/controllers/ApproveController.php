<?php

namespace admin\modules\Management\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\models\RcInvoiceHeader;

use common\models\RcInvoiceLine;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


use common\models\Cheque;
use common\models\Customer;
use admin\modules\accounting\models\ChequeSearch;
use admin\modules\accounting\models\RcinvheaderSearch;

use common\models\Approval;


class ApproveController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel    = new ChequeSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        
         
        $dataProvider->pagination->pageSize=50;

 
        $dataProvider->query->andwhere(['NOT IN','cheque.id',Approval::find()
            ->select('source_id')
            ->where(['comp_id'=>Yii::$app->session->get('Rules')['comp_id']])]);
        $dataProvider->query->indexBy('source_id');
 

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            ]);
    }

    public function actionClearing()
    {
        $myRule         = Yii::$app->session->get('Rules');
        // $searchModel    = new ChequeSearch();
        // $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         
        // $dataProvider->pagination->pageSize=50;

        // $dataProvider->query->andwhere(['NOT IN','cheque.id',Approval::find()
        //     ->select('source_id')
        //     ->where(['comp_id' => $myRule['comp_id']])]);

        // // เห็นเฉพาะ ลูกค้าตัวเอง
        // if($myRule['rules_id'] == 3){
        //     //$dataProvider->query->andwhere(['cust_no_' => \common\models\Customer::getMyCustomer()]);
        // }    
        //$dataProvider->query->indexBy('source_id');  
        
        $query      = Cheque::find()->where(['cust_no_' => @$_GET['ChequeSearch']['cust_no_']])
                        ->andWhere(['NOT IN','id',Approval::find()
                            ->select('source_id')
                            ->where(['comp_id' => $myRule['comp_id']])
                        ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);
                
             

        return $this->render('clearing',[
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxApprove()
    {
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('approve');
        }else {
            return $this->render('approve');
        }
        
    }

    public function actionCheque()
    {
        $myRule         = Yii::$app->session->get('Rules');
        $searchModel    = new RcinvheaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         
        $dataProvider->pagination->pageSize=50;



        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')   $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        }

        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')           $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }


        if(isset($_GET['payment']))
        {
            switch ($_GET['payment']) {
                case 'payment':
                    $cheque = Cheque::find()->where(['comp_id' => $myRule['comp_id']])->all();
                    $cheList = array();
                    foreach ($cheque as $key => $value) {
                        $cheList[]= $value['apply_to'];
                    }
                    $dataProvider->query->andWhere(['rc_invoice_header.id' => $cheList]);
                    break;
                case 'not_payment':
                    $cheque = Cheque::find()->where(['comp_id' => $myRule['comp_id']])->all();
                    $cheList = array();
                    foreach ($cheque as $key => $value) {
                        $cheList[]= $value['apply_to'];
                    }
                    $dataProvider->query->andWhere(['not in','rc_invoice_header.id',$cheList]);
                    break;
                
                default:
                     //$dataProvider->query->andWhere(['rc_invoice_header.id' => $cheList]);
                    break;
            }
            //if($_GET['payment']!='')  $dataProvider->query->andWhere(['rc_invoice_header.id' => $cheList]);
        }


        
        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-').date('m-').'01';

        $todate     = date('Y-').date('m-').$LastDay;

        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));

        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));

        $dataProvider->query->andWhere(['between', 'posting_date', $formdate,$todate]);
        //--- /. Date Filter ---

 
        $dataProvider->query->orderBy(['customer.name' => SORT_ASC,'posting_date'=>SORT_DESC]);
     

        return $this->render('cheque',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            ]);
    }

    public function actionApproval(){
        $query = \common\models\ApproveSetup::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['enabled' => 1]);

        $resultData = [];
        foreach ($query->all() as $key => $model) {             
            foreach (Yii::$app->db->createCommand("SELECT * FROM $model->table_name")->queryAll() as $key => $res) {
                $resultData[] =  $res;
            }            
        }       
        
        return $this->render('approval',[
            'resultData' => $resultData,
        ]);
   
    }

    public function actionApprove(){

        return $this->render('blank');
    }

    public function actionReject(){
        return $this->render('blank');
    }

}
