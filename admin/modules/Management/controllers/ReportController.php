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

use admin\modules\accounting\models\ChequeSearch;

use common\models\Approval;


class ReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionInvoice()
    {
        return $this->render('invoice');
    }

    public function actionProduction()
    {
        return $this->render('production');
    }

    public function actionSales()
    {
        return $this->render('sales');
    }

    public function actionTransport()
    {
        return $this->render('transport');
    }

    public function actionApproved()
    {
        $searchModel    = new ChequeSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         
        $dataProvider->pagination->pageSize=50;

        $myCompany      = Yii::$app->session->get('Rules')['comp_id'];
        // $Approved   =  Approval::find()->where(['comp_id'=>Yii::$app->request->get('comp_id')])->all();
        // $appId  = array();
        // foreach ($Approved as $key => $value) {
        //     $appId[] = ''.$value->source_id.'';
        // }
        $dataProvider->query->andwhere(['IN','cheque.id',Approval::find()->select('source_id')->where(['comp_id'=>$myCompany])]);
        $dataProvider->query->indexBy('source_id');

        if(isset($_GET['cust_no_'])){
            if($_GET['cust_no_']!='') $dataProvider->query->andwhere(['cheque.cust_no_' => base64_decode($_GET['cust_no_'])]);
        }
        //echo $dataProvider->query->createCommand()->getRawSql();

        


        return $this->render('approved',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            ]);
         
    }

    public function actionPassCheque()
    {
        $searchModel    = new ChequeSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         
        $dataProvider->pagination->pageSize=50;
         
        $dataProvider->query->andwhere(['cheque.id'=> Approval::find()->select('source_id')->where(['comp_id'=>Yii::$app->session->get('Rules')['comp_id']])]);
        $dataProvider->query->indexBy('source_id');

        if(Yii::$app->user->identity->id !=1){
            $dataProvider->query->andwhere(['cust_no_' => \common\models\Customer::getMyCustomer()]);
        }

        

        return $this->render('passcheque',[
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

        $query   = RcInvoiceHeader::find(); 
        $query->joinWith('customer')
        ->where(['rc_invoice_header.comp_id' => $company]);



        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => false
            
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


        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-').date('m-').'01';

        $todate     = date('Y-').date('m-').$LastDay;

        if(@$_GET['fdate']!='') $formdate     = date('Y-m-d',strtotime($_GET['fdate']));

        if(@$_GET['tdate']!='') $todate       = date('Y-m-d',strtotime($_GET['tdate']));

        $dataProvider->query->andWhere(['between', 'posting_date', $formdate,$todate]);
        //--- /. Date Filter ---

 
         
     


 
        if(Yii::$app->request->isAjax){
    
            return $this->renderAjax('sale_cash',[
                        
                        'dataProvider' => $dataProvider,
                         
                    ]);
        }
        
    

        return $this->render('sale_cash',[
                    
                    'dataProvider' => $dataProvider,
                     
                ]);


 
        
    }

     
    public function actionSaleCashPdf()
    {
         $company = Yii::$app->session->get('Rules')['comp_id'];

        $query   = RcInvoiceHeader::find(); 
        $query->joinWith('customer')
        ->where(['rc_invoice_header.comp_id' => $company]);

        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => false
            
        ]);



 
 
     
        $PageHeader = $this->renderPartial('__sale_cash',[
                    //'model' => $model,
                    'dataProvider' => $dataProvider,
                     
                ]);

    
    
        // get your HTML raw content without any layouts or scripts

        $content = $this->renderPartial('__sale_cash_body',[
                    //'model' => $model,
                    'dataProvider' => $dataProvider,
                     
                ]);


 
         
    
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format 

            // Pdf::FORMAT_A3 or A3

            // Pdf::FORMAT_A4 or A4

            // Pdf::FORMAT_LETTER or Letter

            // Pdf::FORMAT_LEGAL or Legal

            // Pdf::FORMAT_FOLIO or Folio

            // Pdf::FORMAT_LEDGER or Ledger-L

            // Pdf::FORMAT_TABLOID or Tabloid

            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            // Pdf::ORIENT_LANDSCAPE,Pdf::ORIENT_PORTRAIT
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,



            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            //'cssFile' => 'css/pdf.css',
             
            'cssFile' => 'css/bootstrap.css',
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css', 
            'filename' => 'Inv_',
            // any css to be embedded if required
            'cssInline' => 'body{font-family: saraban, sans-serif; font-size:11px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'INVOICE :  ',],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=> [''.$PageHeader.''],
                //'SetFooter'=>['{PAGENO}'],
                //'SetDisplayMode' => 'fullpage',
                //'SetPageTemplate' => '2',
                'SetWatermarkText' => "Paid",
                'WriteHTML' => $PageHeader,
                 
                 
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


    // สำหรับแก้ไขใบ Invoice ที่ไม่มี Percent Discount
    public function actionInvFixed(){
        if(isset($_GET['table'])){
            if($_GET['table']=='SaleInvoiceHeader'){
                $table = 'Sale Invoice Header';
                $query = \common\models\SaleInvoiceHeader::find()
                ->where(['>','discount',0])
                ->andWhere(['percent_discount' => NULL]);
            }else{
                $table = 'Posted Invoice';
                $query = RcInvoiceHeader::find()
                ->where(['>','discount',0])
                ->andWhere(['percent_discount' => NULL]);
            }            

        }else {
            $table = 'Posted Invoice';
            $query = RcInvoiceHeader::find()
            ->where(['>','discount',0])
            ->andWhere([ '>','percent_discount',20]);
        }       

        if(isset($_GET['sale'])){
            $query->andWHere(['sales_people' => $_GET['sale']]);
        }
       
        return $this->render('_inv_fixed',[                    
            'query' => $query,             
        ]);
    }
}
