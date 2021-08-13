<?php

namespace admin\modules\accounting\controllers;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\data\ActiveDataProvider;

use common\models\SaleLine;
use common\models\SaleHeader;
 


use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use common\models\Items;
use admin\modules\accounting\models\RcinvoiceSearch;
use admin\modules\accounting\models\RcinvheaderSearch;
use admin\modules\accounting\models\CreditnoteSearch;

use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use common\models\SaleInvoiceLine;
use admin\modules\tracking\models\FunctionTracking;
use admin\modules\accounting\models\FunctionAccounting;

class PostedController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions'   => ['login', 'error','help'],
                        'allow'     => true,
                    ],
                    [
                        'actions'   => [
                                        'index', 
                                        'posted-invoice',
                                        'posted-print',
                                        'print',
                                        'print-inv',
                                        'print-receipt',
                                        'create',
                                        'credit-note',
                                        'view-credit',
                                        'credit-note-list',
                                        'delete', 
                                        'posted-invoice-update',
                                        'read-only',
                                        'posted-invoice-comment-update',
                                        'posted-invoice-update-line',
                                        'get-invoice-line',
                                        'cancel-document',
                                        'no-exists'
                                    ],
                        'allow'     => true,
                        'roles'     => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout'        => ['post'],
                    'credit-note'   => ['POST'],
                    'delete'        => ['POST'],
                    'posted-invoice-update' => ['POST'],
                    'posted-invoice-comment-update' => ['POST'],
                    'posted-invoice-update-line' => ['POST'],
                    'get-invoice-line' => ['POST'],
                    'cancel-document' => ['POST'],
                    'no-exists' => ['POST']
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $searchModel    = new RcinvheaderSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
         
        // if($searchModel->posting_date!=''){
             
        //     $dateFilter = explode(' - ',trim($searchModel->posting_date));
        //     $newDate    = explode('/',$dateFilter[0]);
        //     $newEndDate = explode('/',$dateFilter[1]);

        //     $datetime   = new \DateTime();
        //     $datetime->setDate($newDate[2],$newDate[1],$newDate[0]);
        //     $start      = date('Y-m-d',strtotime($datetime->format('Y-m-d')));

        //     $dateEnd    = new \DateTime();
        //     $dateEnd->setDate($newEndDate[2],$newEndDate[1],$newEndDate[0]);            
        //     $end        = date('Y-m-d',strtotime($dateEnd->format('Y-m-d')));
        //     $dataProvider->query->andWhere(['between','date(posting_date)' , $start,$end]);
            
        // }

        //$dataProvider->query->andFilterWhere(['between','date(posting_date)' , '2018-01-15','2018-01-17']);
        $dataProvider->pagination->pageSize=200;
        //echo $dataProvider->query->createCommand()->getRawSql();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        //return $this->render('index');
    }

    public function actionCreate()
    {
        return $this->redirect(['/accounting/saleinvoice/create']);
    }

    public function actionPostedPrint($id, $no){
        $ids     = base64_decode($id);   
        $model  = \common\models\ViewRcInvoice::find()->where([
                        'id'        => $ids, 
                        'no_'       => $no, 
                        'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
                    ])->one();

       
       
        if($model != ''){
            if($model->doc_type=='Credit-Note'){ 
                return $this->redirect(['/accounting/credit-note/view', 'id' => base64_encode($model->id), 'no' => $model->no_]); 
                exit;
            }else{
                return $this->redirect(['posted-invoice', 'id' => base64_encode($model->id), 'no' => $model->no_]); 
                exit;
            }
        }else{
            
        }
    }

    public function actionPostedInvoice($id)
    {
        
        $id     = base64_decode($id);      
        $model  = $this->findModel($id);
        if($model->doc_type=='Credit-Note'){ return $this->redirect(['/accounting/credit-note/view', 'id' => base64_encode($model->id), 'no' => $model->no_]); }
        $query  = RcInvoiceLine::find()->where(['source_id' => $id]); 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,            
        ]);

        
        return $this->render('posted-invoice',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'doc_no' => self::validateSeries($model)
        ]);
    }

    public function actionGetInvoiceLine()
    {
         
        $query  = RcInvoiceLine::find()->where(['source_id' => Yii::$app->request->post('id')]); 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,            
        ]);

        
        return $this->renderPartial('posted-invoice-line',[                
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionReadOnly($id)
    {
        
        $model  = $this->findModel($id);
        if($model->doc_type=='Credit-Note'){ return $this->redirect(['view-credit','id' => base64_encode($model->id)]); }
        $query  = RcInvoiceLine::find()->where(['source_id' => $id]); 
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,            
        ]);

        return $this->render('read-only',[
                'model' => $model,
                'dataProvider' => $dataProvider,
                'doc_no' => self::validateSeries($model)
            ]);
    }



    public function actionPrint($id)
    {
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $model      = $this->findModel(base64_decode($id));
        $query      = RcInvoiceLine::find()->where(['source_id' => $model->id]); 

        $paper      = Yii::$app->request->get('paper')!= '' ? Yii::$app->request->get('paper') : 'A4';

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        $template   = \common\models\PrintPage::findOne(\common\models\PrintPage::findCustomPrint( $paper == 'A4' ? 'invoice' : 'invoice_letter'));  
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
            '{VALUE_TITLE}'         => $model->no_,
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
            '{DOCUMENT_NO}'         => $model->no_,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->posting_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{HEAD_OFFICE}'         => $Company->headofficetb->data_char,                  
         
            '{CREATOR}'             => $model->salesPeople ? $model->salesPeople->name : '',        
            '{CUSTOMER_CODE}'       => $model->customer->code,
            '{CUSTOMER_NAME}'       => $model->customer->name,
            '{CUSTOMER_PHONE}'      => $model->customer->phone,
            '{CUSTOMER_FAX}'        => $model->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->customer->fullAddress['address'],
            '{CUSTOMER_TAX}'        => $model->customer->vat_regis,       
            '{TRANSPORT_BY}'        => $model->customer->transport, 
            '{SALE_NAME}'           => $model->salesPeople ? $model->salesPeople->name : '',
            '{SALE_SUR_NAME}'       => $model->salesPeople ? $model->salesPeople->surname : '',
            '{SALE_CODE}'           => $model->salesPeople ? $model->salesPeople->code : '',                  
         
            '{REF_TO}'              => $model->ext_document,        
         
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term > 0 ? ($model->payment_term.' '.Yii::t('common','Day')) : Yii::t('common','Cash'),
            '{DUE_DATE}'            => $model->paymentdue,
            '{DUE_DATE_TH}'         => date('d/m/y',strtotime($model->paymentdue.' + 543 Years')),
            '{PO_REFERENCE}'        => $model->ext_document,
        
            '{VALUE_BEFOR_VAT}'     => number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2),
        
            '{IF_VAT_TYPE_COLSPAN}' => ($model->include_vat===1)?  '6'  : '5',
            '{IF_VAT_TYPE_ROWSPAN}' => ($model->include_vat===1)?  '4'  : '5',
            '<!--IF_VAT_TYPE-->'    => ($model->include_vat===1)? '<!--': ' ',
            '<!--IF_VAT_TYPE_END-->'=> ($model->include_vat===1)? '-->' : ' ',  
            
            '{VALUE_PERCENT_DISCOUNT}' => ($model->percent_discount)? '('.number_format($model->percent_discount).' %)' : '',
         
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => wordwrap($model->remark, 250, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->sumtotals->sumline,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->sumtotals->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->sumtotals->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->vat_percent.' %',
            '{VALUE_INCLUDEVAT}'    => number_format($model->sumtotals->incvat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->sumtotals->total,2),
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht($model->sumtotals->total),     
             
        ]; 


 
        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme-gray',[
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'print'         => $template,
            'Company'       => $Company,
            'header'        => $header,
            'body'          => $body,
            'defineHeader'  => $defineHeader
        ]);
  
        $content = $this->renderPartial('_print_content',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
            'print' => $template,
            'body'  => $body
        ]);

  
        $pdf = new Pdf([
            'mode'          => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            'orientation'   => $template->paper_orientation,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            'filename'      => $model->no_.'.pdf',
            'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } ',
            'options'       => [
                'title' =>  $model->no_
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

    public function actionPrintReceipt($id)
    {
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $model      = $this->findModel(base64_decode($id));
        $query      = RcInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        $template   = \common\models\PrintPage::findOne(\common\models\PrintPage::findCustomPrint('receipt'));  
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
            '{VALUE_TITLE}'         => $model->no_,
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
            '{DOCUMENT_NO}'         => $model->no_,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->posting_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{HEAD_OFFICE}'         => $Company->headofficetb->data_char,                  
         
            '{CREATOR}'             => $model->sales->name,        
            '{CUSTOMER_CODE}'       => $model->customer->code,
            '{CUSTOMER_NAME}'       => $model->customer->name,
            '{CUSTOMER_PHONE}'      => $model->customer->phone,
            '{CUSTOMER_FAX}'        => $model->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->customer->fullAddress['address'],
            '{CUSTOMER_TAX}'        => $model->customer->vat_regis,       
            '{TRANSPORT_BY}'        => $model->customer->transport, 
            '{SALE_NAME}'           => $model->sales->name,
            '{SALE_SUR_NAME}'       => $model->sales->surname,
            '{SALE_CODE}'           => $model->sales->code,                  
         
            '{REF_TO}'              => $model->ext_document,        
         
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term,
            '{DUE_DATE}'            => $model->paymentdue,
            '{PO_REFERENCE}'        => $model->ext_document,
        
            '{VALUE_BEFOR_VAT}'     => number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2),
        
            '{IF_VAT_TYPE_COLSPAN}' => ($model->include_vat===1)?  '6'  : '5',
            '{IF_VAT_TYPE_ROWSPAN}' => ($model->include_vat===1)?  '4'  : '5',
            '<!--IF_VAT_TYPE-->'    => ($model->include_vat===1)? '<!--': ' ' ,
            '<!--IF_VAT_TYPE_END-->'=> ($model->include_vat===1)? '-->' : ' ',  
            
            '{VALUE_PERCENT_DISCOUNT}' => ($model->percent_discount)? '('.number_format($model->percent_discount).' %)' : '',
         
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => wordwrap($model->remark, 250, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->sumtotals->sumline,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->sumtotals->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->sumtotals->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->vat_percent.' %',
            '{VALUE_INCLUDEVAT}'    => number_format($model->sumtotals->incvat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->sumtotals->total,2),
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht($model->sumtotals->total),     
             
        ]; 


 
        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme-gray',[
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'print'         => $template,
            'Company'       => $Company,
            'header'        => $header,
            'body'          => $body,
            'defineHeader'  => $defineHeader
        ]);
  
        $content = $this->renderPartial('_print_receipt',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
            'print' => $template,
            'body'  => $body
        ]);

  
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            'orientation'   => $template->paper_orientation,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            'filename'      => $model->no_.'.pdf',
            'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } ',
            'options'       => [
                'title' => 'SO : '.$model->no_.' '
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

    public function actionPrintInv($id)
    {
            
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel(base64_decode($id));

        // $searchModel = new SaleinvlineSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['source_id' => $model->id]);
        // $dataProvider->pagination->pageSize=150;

        $query   = RcInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            
        ]);

     
        $PageHeader = $this->renderPartial('../saleinvoice/__print_inv_header',[
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                     
                ]);

    
    
        // get your HTML raw content without any layouts or scripts

        $content = $this->renderPartial('../saleinvoice/__print_inv_page',[
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                     
                ]);


        $font   = isset($_GET['font']) ? $_GET['font'] :  'saraban';
        
         
         
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
             

            'format' => Pdf::FORMAT_LETTER,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,

 
            'cssFile' => 'css/pdf.css',
            //'cssFile'   => '@admin/web/css/saraban.css',
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css', 
            'filename' => 'Inv_'.$model->no_,
            // any css to be embedded if required
            'cssInline' => '@page {margin: 0;} ',
            // set mPDF properties on the fly
            'options' => ['title' => 'INVOICE : '.$model->no_.' ',],
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

        if(isset($_GET['papersize'])){

            if($_GET['papersize']=='A3')
            {
                $pdf->format = Pdf::FORMAT_A3;

            }else if($_GET['papersize']=='A4'){

                $pdf->format = Pdf::FORMAT_A4;

            } 
        } 

        if($model->vat_percent <= 0){


            // $text1 = 'ได้รับสินค้าตามรายการข้างบนนี้ไว้ถูกต้อง และอยู่ในสภาพเรียบร้อยทุกประการ'."<br>\r\n";
            // $text1.= 'การชำระค่าสินค้า กรุณาสั่งจ่ายในนาม MR. JINYU ZHAO ธ.กสิกรไทย/เชฟ-อี พุทธมลฑลสาย2'."<br>\r\n";
            // $text1.= 'เลขที่บัญชี 767-2-17421-4 (ในกรณีจ่ายเช็คกรุณาเขียนเช็คหรือโอนเงินเข้าบัญชีในนามบริษัทฯ'."<br>\r\n";
            // $text1.= 'หรือตามที่ระบุไว้เท่านั้น ห้ามจ่ายเงินสดหรือเช็คให้พนักงานขาย)'."<br>\r\n";

            $text1 = 'ได้รับสินค้าตามรายการข้างบนนี้ไว้ถูกต้อง และอยู่ในสภาพเรียบร้อยทุกประการ'."<br>\r\n";
            $text1.= 'การชำระค่าสินค้า กรุณาสั่งจ่ายในนาม MR. ZHAO JINYU ธ.กรุงศรีอยุธยา สาขาเซ็นทรัลพลาซ่า มหาชัย'."<br>\r\n";
            $text1.= 'เลขที่บัญชี 800-9-07376-9  (ในกรณีจ่ายเช็คกรุณาเขียนเช็คหรือโอนเงินเข้าบัญชีในนามบริษัทฯ '."<br>\r\n";
            $text1.= 'หรือตามที่ระบุไว้เท่านั้น ห้ามจ่ายเงินสดหรือเช็คให้พนักงานขาย)'."<br>\r\n";
            
            // Print with form
            //$pdf->format = Pdf::FORMAT_A4;
            $pdf->cssInline = '@page {margin: 0; } body{font-family: '.$font.',saraban, sans-serif; font-size:13px;}';

            $pdf->content = $this->renderPartial('../saleinvoice/__print_inv_content_with_form',[
                                'model' => $model,
                                'dataProvider' => $dataProvider,
                                'text1' => $text1,
                                 
                            ]);
            
            $pdf->methods = [
                                'WriteHTML' => $this->renderPartial('../saleinvoice/__print_inv_header_with_form',[
                                                'model' => $model,
                                                'dataProvider' => $dataProvider,
                                                'text1' => $text1, 
                                            ]),

                            ];


        }

        if(isset($_GET['download']))
        {
            $pdf->destination = Pdf::DEST_DOWNLOAD;
        }

        
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

    protected function findModel($id)
    {
        if (($model = RcInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function mapData($id, $qty, $price){   
        $set        = array_map(null, $id, $qty, $price);   
        
        $dataSet    = [];
        foreach ($set as $key => $value) {
            if($value[0]!=''){
                $dataSet[] = [
                    'id'        => $value[0],
                    'qty'       => $value[1],
                    'price'     => $value[2]                    
                ];
            }
        }
        return $dataSet;
    }
    public function actionCreditNote($id){

        $id         = base64_decode($id);
        if(!Yii::$app->request->post('chk')){
            Yii::$app->session->setFlash('error', Yii::t('common','Nothing selected.'));
            return $this->redirect(['posted-invoice','id' => base64_encode($id)]);
        }
                    
        $chkList    = Yii::$app->request->post('chk');        
        //var_dump(Yii::$app->request->post('receive')); exit;
        // ดึงรายการมาทำ CN        
        $transaction = Yii::$app->db->beginTransaction();
		try {
            //------- Header ----------

            $model  = new RcInvoiceHeader();            
			$source = $this->findModel($id);            

            // ถ้า -CN ถูกใช้ไปแล้ว ให้ต่อด้วย -CN-2
            $model->no_             = self::validateSeries($source);   

            if(Yii::$app->request->post('RcInvoiceHeader')['no_'] != $model->no_){
                $model->no_         = Yii::$app->request->post('RcInvoiceHeader')['no_'];
            }

			$model->cust_no_ 		= $source->cust_no_;
			$model->cust_name_		= $source->cust_name_;
			$model->document_no_	= $source->document_no_;
            $model->posting_date 	= Yii::$app->request->post('RcInvoiceHeader')['posting_date'].' '.date('H:i:s');
            $model->create_date	 	= date('Y-m-d H:i:s');
			$model->doc_type 		= 'Credit-Note';
			$model->order_id 		= $source->order_id;
			$model->sales_people 	= $source->sales_people;
			$model->sale_id 		= $source->sale_id;
			$model->cust_code 		= $source->cust_code;
			$model->order_date	 	= date('Y-m-d');
			$model->ship_date 		= $source->ship_date;
			$model->cust_address 	= $source->cust_address;
			$model->cust_address2 	= $source->cust_address2;
			$model->contact 		= $source->contact;
			$model->phone 			= $source->phone;
			$model->district 		= $source->district;
			$model->city 			= $source->city;
			$model->province 		= $source->province;
			$model->postcode 		= $source->postcode;
			$model->discount 		= $source->discount *-1;
			$model->percent_discount= $source->percent_discount;
			$model->vat_percent 	= $source->vat_percent;
			$model->include_vat		= $source->include_vat;
			$model->paymentdue		= $source->paymentdue;
			$model->payment_term 	= $source->payment_term;
			$model->ext_document	= $source->no_;
			$model->remark 			= Yii::$app->request->post('RcInvoiceHeader')['remark'];
			$model->status 			= $source->status;
			$model->ref_inv_header 	= $source->id;
			$model->session_id 		= Yii::$app->session->getId();
			$model->user_id 		= Yii::$app->user->identity->id;
            $model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
            $model->cn_reference    = $source->id; 
            


            

			if($model->save()){

                // Create Receive Header 
                $WH                 = new \common\models\WarehouseHeader();
            
                $WH->DocumentNo     = $model->no_;
                $WH->PostingDate    = date('Y-m-d H:i:s');
                $WH->ship_date		= date('Y-m-d H:i:s');
                $WH->DocumentDate 	= date('Y-m-d');
                $WH->TypeOfDocument = 'Credit-Note';
        
                $WH->customer_id 	= $model->cust_no_;
                $WH->SourceDocNo 	= $model->id;  // Rc-id
                $WH->SourceDoc 		= $model->no_;   
                $WH->order_id       = $model->order_id;   
                $WH->ship_to 		= $model->cust_no_;
                $WH->Description 	= $model->ext_document;
                $WH->address 		= '';
                $WH->address2 		= '';
                $WH->district 		= '';
                $WH->city 			= '';
                $WH->province 		= '';
                $WH->postcode 		= '';
                $WH->status         = 'Receive';
                $WH->session_id     = Yii::$app->session->getId();
                $WH->user_id 		= Yii::$app->user->identity->id;
                $WH->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $WH->line_no 		= $model->id;
                
                $WH->save();

                // Update Source 
                // Update ให้รู้ว่า ใบนี้ถูก CN ไปแล้ว
                //$source->cn_reference = $model->id;
                //$source->save(); 
                //(ไม่ต้องใส่ Reference ในใบ CN)
				
				//------- Line -------------------
				// ดึงรายการจาก Rc_Invoice_Line ไปใส่ใน Rc_Invoice_Line อีกครั้ง
                // เก็บ ประวัติ หลัง post invoice 
                $data       = self::mapData(Yii::$app->request->post('chk'),Yii::$app->request->post('quantity'),Yii::$app->request->post('unit_price')); 
                foreach ($data as $key => $value) {                   

                    $InvLine     = RcInvoiceLine::find()
                    ->where(['source_id' => $source->id])
                    ->andWhere(['id' => $value['id']])
                    ->one();                     
                    
                    $line                   = new RcInvoiceLine();
                    
                    $line->type 			= $InvLine->type;
                    $line->item		 	    = $InvLine->item;
                    $line->doc_no_ 		    = $model->no_;
                    $line->line_no_ 		= $InvLine->id;
                    $line->source_id 		= $model->id;                    
                    $line->customer_no_	    = $model->cust_no_;
                    $line->code_no_		    = $InvLine->code_no_;
                    $line->code_desc_		= $InvLine->code_desc_;
                    $line->quantity 		= $value['qty'] * 1;
                    $line->unit_price 		= ($value['price']) * -1;
                    $line->vat_percent 	    = $InvLine->vat_percent;
                    $line->line_discount 	= $InvLine->line_discount;
                    $line->order_id 		= $InvLine->order_id;
                    $line->source_doc		= $InvLine->source_doc;
                    $line->source_line		= $InvLine->source_line;
                    $line->session_id 		= Yii::$app->session->getId();
                    $line->cn_reference     = $InvLine->id; 
                    $line->posting_date     = $model->posting_date;
                    $line->return_receive   = 0;
                    $line->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
                  
                    // update จำนวนที่รับ
                    if(Yii::$app->request->post('receive')){
                        foreach (Yii::$app->request->post('receive') as $key => $qty_rc) {
                            if($InvLine->id == $key){
                                $line->return_receive   = $qty_rc;
                            }                    
                        }
                    }

                    

                    if($line->save()){ 
                        
                         
                        
                         
                        
                        //----------- Stock --------------	 
                        $stock = $this->invenAdjust($line, $model, $WH);
                        if($stock->status==500){
                            Yii::$app->session->setFlash('error', $stock->message);
                            $transaction->rollBack();
                            return $this->redirect(['/accounting/posted/posted-invoice','id' => base64_encode($id)]);
                        }else{
                            // ถ้าตัดสต๊อกแล้ว ให้บันทึกรายการได้
                            // ถ้าเอามาไว้ตรงนี้ จะไม่มี id ส่งไป (เนื่องจากยังไม่สร้าง)
                        }
                        //----------- /.Stock --------------
                    }
                     
                }
                //------- /. Header -------------------

				// ถ้าดึงมาจากใบ SO 
				// ให้เก็บประวัติ
				if($model->order_id != ''){
					if(($SaleHeader = SaleHeader::findOne($model->order_id)) !== null) {
					
                        $SaleHeader->status = 'Credit-Note';
                        $SaleHeader->save();
                        
						FunctionTracking::CreateTracking(
									[
										'doc_type'          => 'Credit-Note',
										'doc_id'            => $model->id,
										'doc_no'            => $model->no_,
										'doc_status'        => 'Credit-Note',
										'amount'            => $model->sumLine,
										'remark'            => 'Current : Invoiced , Status : Credit Note (CN)',
										'track_for_table'   => 'sale_header',
										'track_for_id'      => $SaleHeader->id,
									]);

					}
				}
                //------- /. Line ----------------
                $transaction->commit();
                
                return $this->redirect(['view-credit','id' => base64_encode($model->id)]);
			}else{
                Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                return $this->redirect(['posted-invoice','id' => base64_encode($id)]);
            }

		} catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            return $this->redirect(['posted-invoice','id' => base64_encode($id)]);
			//throw $e;
		}
    }


    // Inventory Adjustment
    protected function invenAdjust($getSource, $Header, $WH){

        $status     = 200;
        $message    = Yii::t('common','Success');

        if($getSource->return_receive != 0){
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $model 			        = new \common\models\WarehouseMoving();  	             
                $model->source_id 		= $WH->id;
                $model->header_id       = $WH->id;
                $model->DocumentNo		= $getSource->doc_no_;
                $model->PostingDate 	= date('Y-m-d H:i:s');
                $model->TypeOfDocument 	= 'Credit-Note';
                $model->SourceDoc 		= $getSource->id;
                $model->SourceDocNo		= $getSource->doc_no_;
                $model->item 			= $getSource->item;
                $model->ItemNo 			= $getSource->code_no_;	 
                $model->Description 	= $getSource->code_desc_;

                // Credit Note รับของคืนต้องเป็นค่า บวก(+)
                $model->Quantity 		= $getSource->return_receive;
                $model->QtyToMove 		= $getSource->return_receive;
                $model->QtyMoved 		= $getSource->return_receive;        
                $model->QtyOutstanding	= 0;
                $model->unit_price 		= $getSource->unit_price;

                $model->DocumentDate 	= date('Y-m-d H:i:s');
                $model->session_id 		= Yii::$app->session->getId();
                $model->user_id 		= Yii::$app->user->identity->id;
                $model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
                $model->line_no 		= $getSource->id;

                $model->qty_before      = $getSource->items->inven;
                $model->qty_after       = $model->qty_before + $model->Quantity;

                if(!$model->save()){                
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                }

                $transaction->commit();	

            } catch (\Exception $e) {

                $transaction->rollBack();
                $status     = 500;
                $message    = Yii::t('common','{:e}',[':e' => $e]);

            }
        }

        return (Object)[
            'status'    => $status,
            'message'   => $message
        ];

    }

    public function actionViewCredit($id){
            // แสดงรายการที่ทำ CN แล้ว                            
            $model  = $this->findModel(base64_decode($id));
            $query  = RcInvoiceLine::find()->where(['source_id' => base64_decode($id)]); 

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,            
            ]);
            
            return $this->render('view-credit',[
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ]);
    }

    public function actionCreditNoteList(){
         // แสดงรายการที่ทำ CN แล้ว                            
        $searchModel = new CreditnoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
         
         return $this->render('credit-note-list',[
                 'searchModel' => $searchModel,
                 'dataProvider' => $dataProvider,
             ]);
    }

    protected function validateSeries($source){
        $model = RcInvoiceHeader::find()
        ->where(['cn_reference' => $source->id])
        ->orderBy(['id' => SORT_DESC])
        ->one();
        $newNo = $source->no_.'-CN';
        if($model){
            // มีคำว่า CN ใน Document No หรือไม่
            if(strpos($model->no_,'CN')){
                        
                // แยกข้อความระหว่าง CN ออกมา
                $findLast = explode('CN',$model->no_);

                // หาตัวเลขจาก Array ตัวสุดท้าย
                preg_match_all('/\d+/', end($findLast), $numbers);
                
                // ตัวเลขตัวสุดท้าย (ยังเป็น Array อยู่)
                $number = end($numbers);

                // มีตัวเลขต่อท้ายหรือไม่
                if(isset($number[0])){               
                    // นับว่ามีตัวเลขกี่หลัก (ทำให้เป็นจำนวนติดลบ)
                    $countLastChar  = strlen($number[0]) * -1;

                    // ตัดตัวอักษรตัวสุดท้าย ของเลขที่เอกสาร โดยตัดออกตามจำนวนที่หาได้____________(1)
                    $lastCharector  = substr($model->no_,0,$countLastChar);

                    // กำหนดเลขตัวถัดไป โดยเอาเลขตัวสุดท้าย มา + 1_________________________(2)
                    $last           = ($number[0]*1) +1;

                    // นำ (1) มาต่อด้วย (2)
                    $newNo          = $lastCharector.''.$last;

                }else{
                    // ไม่มีตัวเลขต่อท้าย
                    // ใส่เป็นเลข 1
                    $newNo = $model->no_.'-1';
                }

                
            }
            
        } 
        
         
        return $newNo;
        
    }

 

    public function actionDelete($id){
        
        // if(!in_array(Yii::$app->session->get('Rules')['rules_id'],\admin\modules\apps_rules\models\SysRuleModels::getPolicy('Data Access','accounting','Approve','Cheque','cost'))){  
        //     Yii::$app->session->setFlash('warning', Yii::t('common','Not Allow'));
        //     return $this->redirect(['index']);
        // }else{
        $model      = RcInvoiceHeader::findOne(base64_decode($id));
        $balance    = $model->sumtotals->total;
        $custName   = $model->customer ? $model->customer->name : '';

        if($model->deletePermission){            

            $transaction = Yii::$app->db->beginTransaction();
            try {                
                
                $source= (Object)[
                    'id'        => 0,
                    'source'    => 0,
                    'no_'       => 0,
                    'sumLine'   => 0,
                    'order_id'  => 0
                ];

                if($model){
                    // เก็บค่าไว้ เพื่อ redirect กลับไปหา  Invoice
                    $source= (Object)[
                        'id'        => $model->id,
                        'source'    => $model->cn_reference,
                        'no_'       => $model->no_,
                        'sumLine'   => $model->sumLine,
                        'order_id'  => $model->order_id
                    ];

                    // Update Shipment Status
                    // Shipment can be convert to invoice again
                    if ($model->shipments){

                        $wh = \common\models\WarehouseHeader::findOne($model->shipments->id);
                        $wh->status = 'Shiped';
                        if(!$wh->save()){
                            Yii::$app->session->setFlash('error', json_encode($wh->getErrors(),JSON_UNESCAPED_UNICODE));
                        }
                        
                    }                    

                    if(RcInvoiceLine::find()->where(['source_id' => $model->id])->exists()){
                        # Exists Sale Line
                        if(RcInvoiceLine::deleteAll(['source_id' => $model->id])){
                            
                            // คืนสินค้าในคลัง (ถ้าเป็น CN)
                            // ลบ header
                            $model->delete();
                            $this->updateSaleOrder($model);
                            $transaction->commit();	
                        }
                    }else{
                        $model->delete();
                        $this->updateSaleOrder($model);
                        $transaction->commit();	
                    }


                    try{
                       
    
                        // Line Notify
                        $bot =  \common\models\LineBot::findOne(1);
                        $msg = 'Delete Invoice (Posted)'."\r\n";
                        $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                        $msg.= $custName."\r\n";
                        $msg.= $model->no_."\r\n";
                        $msg.= number_format($balance,2)." บาท \r\n\r\n";
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                        $bot->notify_message($msg);					
    
                    } catch (\Exception $e) {					 
                        Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                    }
                                
                                
                }
                if($source->source==0){
                    // ถ้าลดหนี้ไม่มีการอ้างอิงใบกำกับภาษี 
                    // ให้ไปที่หน้าหลัก
                    return $this->redirect(['index']);
                }else{
                    return $this->redirect(['posted-invoice','id' => base64_encode($source->source)]);
                }
                

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                return $this->redirect(['index']);
                //throw $e;
            }
            
        }else{
            Yii::$app->session->setFlash('warning', Yii::t('common','Not Allow'));
            return $this->redirect(['index']);
        }
    }


    protected function updateSaleOrder($model){
        // ถ้าดึงมาจากใบ SO 
        // ให้เก็บประวัติ
        if($model!==null){
        
            if($model->order_id != ''){
                if(($SaleHeader = SaleHeader::findOne($model->order_id)) !== null) {                
                
                    FunctionTracking::CreateTracking(
                                [
                                    'doc_type'          => 'Rc-Inv',
                                    'doc_id'            => $model->id,
                                    'doc_no'            => $model->no_,
                                    'doc_status'        => 'Posted',
                                    'amount'            => $model->sumLine,
                                    'remark'            => 'Current : Credit Note (CN) , Status : Remove Credit Note',
                                    'track_for_table'   => 'sale_header',
                                    'track_for_id'      => $SaleHeader->id,
                                ]);                

                    $SaleHeader->status = 'Shiped';
                    $SaleHeader->save();

                }
            }            

        }else{

            if(($SaleHeader = SaleHeader::findOne($source->order_id)) !== null) {   
                FunctionTracking::CreateTracking(
                            [
                                'doc_type'          => 'Rc-Inv',
                                'doc_id'            => $source->id,
                                'doc_no'            => $source->no_,
                                'doc_status'        => 'Posted',
                                'amount'            => $source->sumLine,
                                'remark'            => 'Current : Credit Note (CN) , Status : Remove Credit Note',
                                'track_for_table'   => 'sale_header',
                                'track_for_id'      => $source->order_id,
                            ]);
            

                $SaleHeader->status = 'Shiped';
                $SaleHeader->save();
            }
            
        }       

    }

    public function actionPostedInvoiceUpdate(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        
        $status         = 200;
        $message        = Yii::t('common','Saved');  
        $suggestion     = Yii::t('common','Success');
        $model          = RcInvoiceHeader::findOne($data->id);

        $transaction = Yii::$app->db->beginTransaction();
		try {

            if(isset($data->name)){
                $name           = $data->name;
                $model->$name   = $data->value;

                if($data->name == 'percent_discount'){
                    $model->discount    = ($model->percent_discount * $model->sumtotals->sumline) /100;
                }
            }

            
            if(isset($data->term)){
                $model->payment_term    = $data->term;
            }

            if(isset($data->due)){
                $model->paymentdue      = $data->due;
            }
 
            if(isset($data->posting)){
                $model->posting_date    = $data->posting.date(' H:i:s');
            }

            if(isset($data->remark)){
                $model->remark          = $data->remark;
            }

            // ตรวจสิทธิ์
            if(isset($data->vat_percent) || isset($data->include_vat)){

                if($model->modifyPermission){ 

                    if(isset($data->vat_percent)){
                        $model->vat_percent     = $data->vat_percent;
                    }

                    if(isset($data->include_vat)){
                        $model->include_vat     = $data->include_vat;
                    }

                    try{
                       
    
                        // Line Notify
                        $bot =  \common\models\LineBot::findOne(1);
                        $msg = 'Change Vat Invoice'."\r\n";
                        $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                        $msg.= $model->no_."\r\n";
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                        $bot->notify_message($msg);					
    
                    } catch (\Exception $e) {					 
                        Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                    }

                    
                }else{
                    $status     = 500;
                    $message    = Yii::t('common','Warning');  
                    $suggestion = Yii::t('common','Permission Denine');     
                }
            }
            

            if(isset($data->sale)){
                $model->sale_id         = $data->sale;
                $sale                   = \common\models\SalesPeople::findOne($model->sale_id);
                $model->sales_people    = $sale->code;
                // update sale header
                $SaleOrder              = SaleHeader::findOne($model->order_id);
                if($SaleOrder != null){
                    $SaleOrder->sales_people    = $model->sales_people;
                    $SaleOrder->sale_id         = $model->sale_id;
                    $SaleOrder->save();
                }
            }
            
            $model->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $status     = 500;
            $message    = Yii::t('common','Error');  
            $suggestion = Yii::t('common','{:e}',[':e' => $e]); 
            $transaction->rollBack();     
        }
        
        return $this->asJson([
            'status'        => $status,
            'message'       => $message,
            'suggestion'    => $suggestion,
            'total'         => $model->sumtotals
        ]);
    }

    public function actionPostedInvoiceUpdateLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        
        $status         = 200;
        $message        = Yii::t('common','Saved');  
        $suggestion     = Yii::t('common','Success');
        $model          = RcInvoiceLine::findOne($data->id);
        $no             = $model->doc_no_;

        $transaction = Yii::$app->db->beginTransaction();
		try {
            $old = '';
            // ตรวจสิทธิ์
            if($model->rcInvoiceHeader->modifyPermission){ 

                if(isset($data->field)){
                   
                    $field          = $data->field;
                    $old            = $model->$field;
                    $model->$field  = $data->value;
                }

                // Change Default Unit of Measure
                if($data->field == 'measure'){     
                    $item = Items::findOne($model->item);           
                    $item->unit_of_measure   = $model->measure;
                    $item->UnitOfMeasure     = $model->unitofmeasures->UnitCode;
                    $item->update();
                    
                }
    
                try{
                    // Line Notify
                    $bot =  \common\models\LineBot::findOne(1);
                    $msg = "\r\n".'Change Invoice Line'."\r\n\r\n";
                    $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                    $msg.= 'No : '.$no."\r\n";
                    $msg.= 'Field : '.$data->field."\r\n";
                    $msg.= 'Value : '.$old.' => '.$data->value."\r\n\r\n";
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                    $bot->notify_message($msg);					

                } catch (\Exception $e) {					 
                    Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                }

                
            }else{
                $status     = 500;
                $message    = Yii::t('common','Warning');  
                $suggestion = Yii::t('common','Permission Denine');     
            }
            
            $model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
            $model->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $status     = 500;
            $message    = Yii::t('common','Error');  
            $suggestion = Yii::t('common','{:e}',[':e' => $e]); 
            $transaction->rollBack();     
        }
        
        return $this->asJson([
            'status'        => $status,
            'message'       => $message,
            'suggestion'    => $suggestion,
            'total'         => $model->rcInvoiceHeader->sumtotals
        ]);
    }

    public function actionPostedInvoiceCommentUpdate(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        

        $model                  = RcInvoiceHeader::findOne($data->id);
        $model->comments        = $data->comment;

        if($model->save()){
            return $this->asJson([
                'status' => 200,
                'message' => Yii::t('common','Saved'),
                'suggestion' => Yii::t('common','Success')
            ]);
        }else{
            return $this->asJson([
                'status' => 500,
                'message' => Yii::t('common','Error'),
                'suggestion' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
            ]);
        }
    }


    public function actionCancelDocument(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status = 200;
        $message = Yii::t('common','Success');
        
        $model = RcInvoiceHeader::findOne($data->id);
        if($model != null){
            $model->revenue = $data->action;
            if($model->save()){
                $status = 200;
            }else{
                $status = 500;
                $message = $model->getErrors();
            }
        }

        return json_encode([
            'status' => 200,
            'message' => $message
        ]);
    }

    public function actionNoExists(){
		$request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		
		$model = \common\models\RcInvoiceHeader::findOne(['no_' => $data->no,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
		if ($model!=null){
			return json_encode([
				'status' => 200,
				'data' => [
					'id' => $model->id,
                    'no' => $model->no_,
                    'balance' => $model->sumtotals->total
				]
			]);
		}else{
			return json_encode([
				'status' => 404,
				'data' => [
					'no' => $data->no
				]
			]);
		}
	}


}
