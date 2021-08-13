<?php

namespace admin\modules\financial\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\models\RcInvoiceHeader;

use common\models\RcInvoiceLine;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


use common\models\Cheque;

use admin\modules\financial\models\PayableHeaderSearch;


class PayableController extends \yii\web\Controller
{
 

    public function actionIndex()
    {
        $myRule         = Yii::$app->session->get('Rules');
        $searchModel    = new PayableHeaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         


        if(isset($_GET['total-summary'])){

            if($_GET['total-summary']!='all')
            {
                $dataProvider->pagination->pageSize=$_GET['total-summary'];

            }else {

                $dataProvider->pagination=false;

            }

        }


        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')   $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        }

        if(isset($_GET['customer']))
        {
           // if($_GET['customer']!='')           $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }


        if(isset($_GET['payment']))
        {
            switch ($_GET['payment']) {
                case 'payment':
                    $dataProvider->query->andWhere(['ap_invoice_header.status' => 'Posted']);
                    break;
                case 'not_payment':
                    $dataProvider->query->andWhere(['ap_invoice_header.status' => 'Open']);
                    break;

                default:

                    break;
            }
 
        }

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-').date('m-').'01  00:00:0000';

        $todate     = date('Y-').date('m-').$LastDay.' 23:59:59.9999';

        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d 00:00:0000',strtotime($_GET['fdate']));

        if(@$_GET['tdate']!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime($_GET['tdate']));

        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        //$dataProvider->query->andWhere(['ap_invoice_header.revenue' => 0]);

        $dataProvider->query->orderBy([
            'vendors.name' => SORT_ASC,
            'ap_invoice_header.no' => SORT_ASC,            
            'ap_invoice_header.posting_date' => SORT_ASC
            ]);

        
        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            ]);

    }



















    public function actionMonthly()
    {
        //$model = new RcInvoiceHeader();
        $myRule = Yii::$app->session->get('Rules');

        $query   = RcInvoiceHeader::find();
        $query->joinWith('customer');
        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],

        ]);

        $dataProvider->query->where(['rc_invoice_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);


        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')  $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        }

        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
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


        return $this->render('monthly',[

            'dataProvider' => $dataProvider,

            ]);
    }

    public function actionSaleCash()
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $searchModel    = new RcinvheaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        // $query   = RcInvoiceHeader::find();
        // $query->joinWith('customer')
        // ->where(['rc_invoice_header.comp_id' => $company]);

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => false
        // ]);

        $dataProvider->query->where(['view_rc_invoice.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')  $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        }

        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));
        $formdate   = date('Y-').date('m-').'01';
        $todate     = date('Y-').date('m-').$LastDay;
        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));
        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));
        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        $dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);

        $dataProvider->query->orderBy(['cust_no_' => SORT_ASC,'posting_date' => SORT_ASC]);
        $dataProvider->pagination=false;
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('sale-cash',[
                        'dataProvider' => $dataProvider,
                    ]);
        }
        return $this->render('sale-cash',[
                    'dataProvider' => $dataProvider,
                ]);
    }


    public function actionSaleCashNoDetail()
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];
        $searchModel    = new RcinvheaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        // $query   = RcInvoiceHeader::find();
        // $query->joinWith('customer')
        // ->where(['rc_invoice_header.comp_id' => $company]);

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => false
        // ]);
        $dataProvider->query->where(['view_rc_invoice.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')  $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        }
        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));
        $formdate   = date('Y-').date('m-').'01';
        $todate     = date('Y-').date('m-').$LastDay;
        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));
        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));
        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        $dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);

        $dataProvider->query->orderBy(['cust_no_' => SORT_ASC,'posting_date' => SORT_ASC]);
        $dataProvider->pagination=false;
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('sale-cash-no-detail',[
                        'dataProvider' => $dataProvider,
                    ]);
        }

        return $this->render('sale-cash-no-detail',[
                    'dataProvider' => $dataProvider,
                ]);
    }

    public function actionTaxInvoice()
    {
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $searchModel    = new RcinvheaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         
        $dataProvider->query->where(['view_rc_invoice.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if(isset($_GET['search-from-sale']))
        {
            if($_GET['search-from-sale']!='')  $dataProvider->query->andWhere(['sales_people' => $_GET['search-from-sale']]);
        }

        if(isset($_GET['customer']))
        {
            if($_GET['customer']!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }


        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));
        $formdate   = date('Y-').date('m-').'01';
        $todate     = date('Y-').date('m-').$LastDay;
        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));
        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));
        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        $dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);

        $dataProvider->query->orderBy(['no_' => SORT_ASC]);

        $dataProvider->pagination=false;

        if(Yii::$app->request->isAjax){

            return $this->renderAjax('tax-invoice',[
                        'dataProvider' => $dataProvider,
                    ]);
        }



        return $this->render('tax-invoice',[

                    'dataProvider' => $dataProvider,

                ]);




    }


}
