<?php

namespace admin\modules\Management\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\models\RcInvoiceHeader;

use common\models\RcInvoiceLine;
use common\models\SaleInvoiceLine;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


use common\models\Cheque;

use admin\modules\Management\models\RcinvheaderSearch;
use admin\modules\Management\models\ReceiveSearch;


class FinancialController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCheque()
    {
        $myRule         = Yii::$app->session->get('Rules');
        $searchModel    = new ReceiveSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);

   
       
        if(Yii::$app->request->get('search-from-sale')!=''){
            //$dataProvider->query->andWhere(['sales_people' => Yii::$app->request->get('search-from-sale')]); // Disabled 11/09/2020
            $dataProvider->query->andWhere(['sale_id' => Yii::$app->request->get('search-from-sale')]); // Enabled 11/09/2020
        }   
        
        if(Yii::$app->request->get('customer')!=''){
            $dataProvider->query->andWhere(['cust_no_' => Yii::$app->request->get('customer')]);
        } 
        
        if(Yii::$app->request->get('total-summary')!='all'){
            $dataProvider->pagination->pageSize=Yii::$app->request->get('total-summary');
        }else {
            $dataProvider->pagination=false;
        }

        // if(!Yii::$app->request->get('fdate')){
        //     $dataProvider->pagination->pageSize = 5;
        // }
       

        switch (Yii::$app->request->get('payment')) {
            case 'payment':
                $dataProvider->query->andWhere(['view_rc_invoice.status' => 'Posted']);
                break;
            case 'not_payment':
                $dataProvider->query->andWhere(['view_rc_invoice.status' => 'Open']);
                break;

            default:
                    //$dataProvider->query->andWhere(['rc_invoice_header.id' => $cheList]);
                break;
        }
        

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-').date('m-').'01  00:00:0000';

        $todate     = date('Y-').date('m-').$LastDay.' 23:59:59.9999';

        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d 00:00:0000',strtotime(Yii::$app->request->get('fdate')));

        if(@$_GET['tdate']!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime(Yii::$app->request->get('tdate')));

        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        $dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);
        $dataProvider->query->andWhere(['view_rc_invoice.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if(Yii::$app->request->get('show-cn')=='false'){
            $dataProvider->query->andWhere(['doc_type' => 'Sale']);
        }

        if(Yii::$app->request->get('vat')=='Vat'){
            $dataProvider->query->andWhere(['>', 'vat_percent', 0]);
        }else if(Yii::$app->request->get('vat')=='No'){
            $dataProvider->query->andWhere(['<=', 'vat_percent', 0]);
        }
        

        $dataProvider->query->orderBy([
            'customer.name' => SORT_ASC,
            'view_rc_invoice.no_' => SORT_ASC,            
            'view_rc_invoice.posting_date' => SORT_ASC
            ]);

        
        return $this->render('cheque',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            ]);

    }




    public function actionChequeAjax()
    {
        
        return $this->render('cheque-ajax');

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

        //$dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);

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
        $company        = Yii::$app->session->get('Rules')['comp_id'];
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

        if(isset($_GET['credit-note'])){
            if($_GET['credit-note']=='1'){
                $dataProvider->query->andWhere(['doc_type' => 'Sale']);
            }  
        }

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));
        $formdate   = date('Y-').date('m-').'01';
        $todate     = date('Y-').date('m-').$LastDay;
        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));
        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));
        $dataProvider->query->andWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        //$dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);
        $dataProvider->query->andWhere(['view_rc_invoice.show_doc' => Yii::$app->request->get('show')]);

        $dataProvider->query->orderBy(['cust_no_' => SORT_ASC,'no_' => SORT_ASC]);
        
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

        if(Yii::$app->request->get('search-from-sale')){
            if(Yii::$app->request->get('search-from-sale')!='')  $dataProvider->query->andWhere(['sales_people' => Yii::$app->request->get('search-from-sale')]);
        }

        if(Yii::$app->request->get('customer')){
            if(Yii::$app->request->get('customer')!='')  $dataProvider->query->andWhere(['cust_no_' => $_GET['customer']]);
        }

        if(Yii::$app->request->get('credit-note')){
            if($_GET['credit-note']=='1'){
                $dataProvider->query->andWhere(['doc_type' => 'Sale']);
            }  
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

 
        //if(Yii::$app->request->get('show')){ // Enabled 09/08/2020
            $dataProvider->query->andWhere(['view_rc_invoice.show_doc' => Yii::$app->request->get('show')]);
        //} 
              
        

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

    public function actionTaxInvoiceAjax()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

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

        //$dataProvider->query->andWhere(['view_rc_invoice.revenue' => 0]);

        $dataProvider->query->orderBy(['no_' => SORT_ASC]);

        $dataProvider->pagination=false;

        $raws = [];
        foreach ($dataProvider->models as $key => $model) {
            
            $list   = [];

            if($model->status=='Open'){
                $invLine 	= SaleInvoiceLine::find()->where(['source_id' => $model->id])->all();                
            }else{
                $invLine 	= RcInvoiceLine::find()->where(['source_id' => $model->id])->all();
            }

            foreach ($invLine as $key => $line) {
                $list[] = [
                    'id'    => $line->id,
                    'item'  => $line->item,
                    'name'  => $line->code_desc_,
                    'code'  => $line->items->master_code
                ];
            }
            
            $raws[] = [
                'id'        => $model->id,
                'no'        => $model->no_,
                'list'      => $list,
                'status'    => $model->status,
                'cust'      => $model->cust_name_
            ];

            
        }


        return json_encode([
            'status' => 200,
            'raws' => $raws
        ]);



    }

}
