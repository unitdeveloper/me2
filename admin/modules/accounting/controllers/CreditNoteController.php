<?php

namespace admin\modules\accounting\controllers;
use Yii;
use yii\data\ActiveDataProvider;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use common\models\PrintPage;
use admin\modules\accounting\models\CreditNoteListSearch;
use yii\web\NotFoundHttpException;
 
use common\models\SaleHeader;
use admin\modules\tracking\models\FunctionTracking;
use admin\modules\accounting\models\SaleinvlineSearch;
use common\models\SaleInvoiceHeader;
use common\models\Items;
use admin\models\Generater;
use common\models\SalesPeople;

use common\models\SaleInvoiceLine;
use common\models\ViewRcInvoice;

use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


class CreditNoteController extends \yii\web\Controller
{
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actionIndex()
    {
        $searchModel = new CreditNoteListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
         
        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionChangeShowDoc(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        $status                 = 200;
        $message                = Yii::t('common','Success');
        $raws                   = [];
         
        $model  = \common\models\ViewRcInvoice::find()->where([
                        'id'        => $data->id, 
                        'no_'       => $data->no_, 
                        'comp_id'   => Yii::$app->session->get('Rules')['comp_id']
                    ])->one();

        if($model != null){
            if($model->status=='Open'){ 
                $IV             = SaleInvoiceHeader::findOne($data->id);  
                $IV->show_doc   = $data->sw;
                if(!$IV->save()){
                    $status     = 500;
                    $message    = json_encode($IV->getErrors(),JSON_UNESCAPED_UNICODE);
                }else{
                    $raws[] = (Object)[
                        'id' => $IV->id,
                        'no' => $IV->no_
                    ];
                }

                
            }else{
                $IV             = RcInvoiceHeader::findOne($data->id);
                $IV->show_doc   = $data->sw;
                if(!$IV->save()){
                    $status     = 500;
                    $message    = json_encode($IV->getErrors(),JSON_UNESCAPED_UNICODE);
                }else{
                    $raws[] = (Object)[
                        'id' => $IV->id,
                        'no' => $IV->no_
                    ];
                }

                 
            }
        }else{
            $status     = 404;
            $message    = 'Not found';
        }

        return json_encode([
            'status' => $status,
            'message' => $message,
            'raws' => $raws
        ]);
    }

    public function actionView($id){
        // แสดงรายการที่ทำ CN แล้ว         
        //$Inv  = ViewRcInvoice::findOne(['id' => base64_decode($id), 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $Inv  = ViewRcInvoice::findOne(['id' => base64_decode($id), 'no_' => Yii::$app->request->get('no'), 'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if ($Inv->status == 'Posted') {
            $model  = RcInvoiceHeader::findOne(base64_decode($id));
            $query  = RcInvoiceLine::find()->where(['source_id' => $model->id]); 
        }else {
            $model  = SaleInvoiceHeader::findOne(base64_decode($id));           
            $query  = SaleInvoiceLine::find()->where(['source_id' => $model->id]); 
        }
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,            
        ]);
        
        return $this->render('view',[
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]);
    }

    public function actionCreate()
    {
        $model              = new SaleInvoiceHeader();
        
        // Auto Create
        $NoSeries           = Generater::getRuning('rc_invoice_header','no_','all');        
        if($NoSeries=='Error'){ return $this->redirect(['/install', 'id' => 1]); exit();}
        
        $model->no_             = $NoSeries;
        $model->status          = 'Open';
        $model->user_id         = Yii::$app->user->identity->id;
        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
        $model->cust_name_      = 'เงินสด';
        $model->cust_address    = '';
        $model->cust_no_        = 909;
        $model->session_id      = Yii::$app->session->getId();
        $SALES                  = SalesPeople::findOne(Yii::$app->session->get('Rules')['sale_id']);
        $model->sales_people    = $SALES->code;
        $model->sale_id         = $SALES->id;
        $model->posting_date    = date('Y-m-d H:i:s');
        $model->order_date      = date('Y-m-d');
        $model->ship_date       = date('Y-m-d');
        $model->cust_code       = '999';
        $model->document_no_    = $NoSeries;
        $model->doc_type        = 'Credit-Note';
        $model->city            = 814;
        $model->district        = 7352;
        $model->province        = 59;
        $model->postcode        = '74000';
        $model->status          = 'Open';
        $model->paymentdue      = date('Y-m-d');
        $model->discount        = 0;
        $model->percent_discount= 0;
        $model->vat_percent     = 7;
        $model->payment_term    = 0;
        $model->include_vat     = 1;

        
        if($model->save()){
            $UpdateSeries       = Generater::CreateNextNumber('rc_invoice_header','no_','all',$model->no_);
            return $this->redirect(['update', 'id' => base64_encode($model->id)]); 
        }else{
            Yii::$app->getSession()->addFlash('error',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));         
            return $this->redirect(['index']);
        }
       

    }

    public function actionUpdateReceive(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        
        $status                 = 200;
        $message                = Yii::t('common','Success');
        
        $model                  = SaleInvoiceLine::findOne($data->id);
        $model->return_receive  = $data->set == true ? $model->quantity : NULL;

        if(!$model->save()){
            $staus      = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }
        
        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function actionUpdateCheckreceive(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        
        $status                 = 200;
        $message                = Yii::t('common','Success');

        $SaleInvoiceLine        = SaleInvoiceLine::find()->where(['source_id' => $data->id])->all();

        foreach ($SaleInvoiceLine as $key => $value) {
            $model  = SaleInvoiceLine::findOne($value->id);
            $qty    = $data->all == true ? $value->quantity : NULL;
            $model->return_receive = $qty;     
            $model->save();
        }

         
        
        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function actionUpdate($id)
    {
       
        $model = $this->findModel(base64_decode($id));

        $query   = SaleInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query'     => $query,
            'pagination' => false,            
        ]);
        
        if(Yii::$app->request->isAjax) {
            // Reference Invoice
            if(Yii::$app->request->post('cn_reference')){   
                $model->cn_reference        = NULL;  
                if($model->save()){
                    return json_encode([
                        'status' => 200,
                        'data' => [
                            'cn_reference' => $model->cn_reference,
                        ]
                    ]);
                }else{
                    return json_encode([
                        'status' => 500,
                        'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);
                }
                
            }

            // Percent Discount
            if(Yii::$app->request->post('percentDiscount') != ''){   
                $model->discount            = $model->sumLine * (Yii::$app->request->post('percentDiscount') / 100);  
                $model->percent_discount    = Yii::$app->request->post('percentDiscount');
                if($model->save()){
                    return json_encode([
                        'status' => 200,
                        'data' => [
                            'percent_discount' => $model->percent_discount,
                            'discount' => $model->discount
                        ]
                    ]);
                }else{
                    return json_encode([
                        'status' => 500,
                        'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);
                }
                
            }

            // Discount
            if(Yii::$app->request->post('discount') != ''){   
                $model->percent_discount = (Yii::$app->request->post('discount') / $model->sumLine) * 100;
                $model->discount         = Yii::$app->request->post('discount');
                if($model->save()){
                    return json_encode([
                        'status' => 200,
                        'data' => [
                            'percent_discount' => $model->percent_discount,
                            'discount' => $model->discount
                        ]
                    ]);
                }else{
                    return json_encode([
                        'status' => 500,
                        'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);
                }
                
            }

            if(Yii::$app->request->post('hasEditable'))
            {
                
                $InvLine = SaleInvoiceLine::findOne(Yii::$app->request->post('editableKey'));

                $out = json_encode(['output' => '', 'message' => '']);

              
                $posted = current($_POST['RcInvoiceLine']);
     
                $post   = ['RcInvoiceLine' => $posted];

                if($InvLine->load($post))
                {

                    $InvLine->save();
                   

                }

                echo $out;
                
                return;

      
            }


            if ($model->load(Yii::$app->request->post())) {

                $model->include_vat = Yii::$app->request->post('include_vat');
                if(!$model->save())
                {
                    print_r($model->getErrors());
                }

                return $this->renderAjax('_form', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ]);

            } else {

                return $this->renderAjax('_form', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ]);
            }

        }else {

            if ($model->load(Yii::$app->request->post())) {
                 
                $model->user_id         = Yii::$app->user->identity->id;
                $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                $SALES                  = SalesPeople::findOne($model->sale_id);
                $model->sales_people    = $SALES->code;
                $model->sale_id         = $SALES->id;
                $model->discount        = ($model->discount) * -1;
                //$model->doc_type 		= 'Credit-Note';

                // $GenSeries        = new Generater();
                // $NoSeries         = $GenSeries->LastNumber('vat_type','vat_value',$model->vat_percent);                 

                // $UpdateSeries       = $GenSeries->CreateNextNumber('vat_type','vat_value',$model->vat_percent,$NoSeries);

                if(!$model->save())
                {
                    print_r($model->getErrors());
                }

                return $this->render('update', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ]);

            } else {
                
                return $this->render('update', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                ]);
            }

        }

    }

 

    public function actionPrint($id)
    {
        //$findModel =ViewRcInvoice::findOne(['id' => base64_decode($id),'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        $findModel =ViewRcInvoice::findOne(['id' => base64_decode($id),'no_' => Yii::$app->request->get('no') ,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if ($findModel->status=='Posted'){
            $model      = $this->findRcModel(base64_decode($id));
            $query      = RcInvoiceLine::find()->where(['source_id' => $model->id]);
        }else{
            $model      = $this->findModel(base64_decode($id));
            $query      = SaleInvoiceLine::find()->where(['source_id' => $model->id]);
        }
        

        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            //'sort'=> ['defaultOrder' => ['priority'=>SORT_ASC]],
        ]);

        $template      = PrintPage::findOne(PrintPage::findCustomPrint('credit_note'));
  
        $Company  = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

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

        
                            
        $value_old  = abs($model->invfromCreditNote     // ถ้ามีการอ้างอิงเลขที่บิล
                            ? ($model->include_vat===1  // Vat นอก
                                ? $model->invfromCreditNote->sumtotals->subtotal
                                : $model->invfromCreditNote->sumtotals->before)
                            : 0); 

        // มูลค่าที่ถูกต้อง
        $real_total = abs($model->invfromCreditNote     // 
                        ? ($model->include_vat===1      // Vat นอก
                            ? $model->invfromCreditNote->sumtotals->sumline 
                            : $value_old)
                        : 0)  - abs($model->sumtotals->subtotal);
                        
        // ผลต่าง
        $before_vat = $model->invfromCreditNote             // ถ้ามีการอ้างอิงเลขที่บิล
                        ? ($model->include_vat===1          // Vat นอก
                            ? ($value_old - $real_total)   
                            : abs($model->sumtotals->total) - abs($model->sumtotals->incvat))
                        : abs($model->sumtotals->total) - abs($model->sumtotals->incvat);

     

        $defineHeader = [
            '{VALUE_TITLE}'         => $model->no_,
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 110px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $model->no_,
         
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->posting_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->posting_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,                  
         
            '{CREATOR}'             => $model->salesPeople->name,        
            '{CUSTOMER_CODE}'       => $model->customer->code,
            '{CUSTOMER_NAME}'       => $model->customer->name,
            '{CUSTOMER_PHONE}'      => $model->customer->phone,
            '{CUSTOMER_BRANCH}'     => $model->customer->branch,
            '{CUSTOMER_BRANCH_NAME}'=> $model->customer->branch_name,
            '{CUSTOMER_FAX}'        => $model->customer->fax,
            '{CUSTOMER_ADDRESS}'    => $model->customer->locations->address,
            '{CUSTOMER_PROVINCE}'   => $model->customer->locations->province,
            '{CUSTOMER_ZONE}'       => $model->customer->locations->zone,
            '{CUSTOMER_TAX}'        => $model->customer->vat_regis,        
            '{SALE_NAME}'           => $model->salesPeople->name,
            '{SALE_CODE}'           => $model->salesPeople->code,   
            '{CUSTOMER_HEAD_OFFICE}'=> $model->customer->headoffice == 0 
                                            ? $model->customer->branch_name
                                            : $model->customer->headofficetb->data_char,               
         
            '{REF_TO}'              => ($model->invfromCreditNote)? $model->invfromCreditNote->no_ : '',    
            '{REF_OTHER}'           => $model->other_ref,        
         
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term,
            '{DUE_DATE}'            => $model->paymentdue,
            '{INVOICE_REFERENCE}'   => $model->ext_document,
        
            //'{VALUE_BEFOR_VAT}'     => number_format(abs($model->sumtotals->subtotal - $model->sumtotals->incvat),2),
            // ผลต่าง
            '{VALUE_BEFOR_VAT}'     => number_format($before_vat, 2),
            '{IF_VAT_TYPE_COLSPAN}' => ($model->include_vat===1)?  '5'  : '4',
            '{IF_VAT_TYPE_ROWSPAN}' => ($model->include_vat===1)?  '3'  : '4',
            '<!--IF_VAT_TYPE-->'    => ($model->include_vat===1)? '<!--': ' ',
            '<!--IF_VAT_TYPE_END-->'=> ($model->include_vat===1)? '-->' : ' ', 
            
            '{VALUE_PERCENT_DISCOUNT}' => ($model->percent_discount)? '('.number_format($model->percent_discount).' %)' : '',
         
            '{LABEL_ORG_INV}'       => 'มูลค่าของสินค้าตามใบกำกับภาษีเดิม',
            '{VALUE_ORG_INV}'       => number_format($value_old, 2),
            // '{VALUE_REAL_TOTAL}'    => number_format(($real_total * 7 /100) + $real_total,2),   
            // '{VALUE_REAL_TOTAL}'    => $model->invfromCreditNote 
            //                             ? number_format(abs(abs($model->sumtotals->subtotal) - abs($model->invfromCreditNote->sumtotals->sumline)),2) 
            //                             : number_format($real_total,2),   

            // มูลค่าของสินค้าที่ถูกต้อง
            '{VALUE_REAL_TOTAL}'    => $model->include_vat===0  // Vat ใน
                                        ? number_format(($value_old - $before_vat),2)
                                        : number_format($real_total,2),
            // '{VALUE_REAL_TOTAL}'    => $model->invfromCreditNote    // ถ้ามีการอ้างอิงเลขที่บิล
            //                             ? $model->include_vat===1   // Vat นอก
            //                                 ? (number_format(abs(abs($model->sumtotals->subtotal) - abs($model->invfromCreditNote->sumtotals->sumline)),2)) 
            //                                 : number_format(($value_old - $before_vat),2) 
            //                             : number_format(($value_old - $before_vat),2),  
                                        
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => wordwrap($model->remark, 145, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format(abs($model->sumtotals->sumline),2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format(abs($model->sumtotals->discount),2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format(abs($model->sumtotals->subtotal),2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->vat_percent.' %',
            '{VALUE_INCLUDEVAT}'    => number_format(abs($model->sumtotals->incvat),2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format(abs($model->sumtotals->total),2),
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht(abs($model->sumtotals->total)),     
             
        ];

  
        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-theme',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'print' => $template,
            'Company' => $Company,
            'header' => $header,
            'body'  => $body,
            'defineHeader' => $defineHeader
        ]);

        // get your HTML raw content without any layouts or scripts
  
        $content = $this->renderPartial('_print_content',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
            'print' => $template,
            'body'  => $body
        ]);
  
  

 
        
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format'        => $template->paper_size,
            // portrait orientation //ORIENT_LANDSCAPE,ORIENT_PORTRAIT
            'orientation'   => $template->paper_orientation,
            // stream to browser inline
            'destination'   => Pdf::DEST_BROWSER,
            
            // your html content input
            'content'       => $content,
            //'cssFile'       => 'https://use.fontawesome.com/releases/v5.0.13/css/all.css',
            'filename'      => $model->no_.'.pdf',
            // any css to be embedded if required @page {margin: 0; }
            'cssInline'     => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px; }',
            // set mPDF properties on the fly
            'options'       => [
                'title' => 'SQ : '.$model->no_.' ',
            ],
            // call mPDF methods on the fly
            'methods'       => [
                //'WriteHTML' => $PrintTemplate,
                 
            ]
        ]);

        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->WriteHtml($PrintTemplate);
        
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


    public function actionPostCreditNote($id){

        
        if(!isset($_POST['chk'])){
            Yii::$app->session->setFlash('error', Yii::t('common','Nothing selected.'));
            return $this->redirect(['posted-invoice','id' => $id]);
        }

        $id         = base64_decode($id);      
        $chkList    = $_POST['chk'];
        
        // ดึงรายการมาทำ CN        
        $transaction = Yii::$app->db->beginTransaction();
		try {
			//------- Header ----------	
			$model = new RcInvoiceHeader();

			$source = RcInvoiceHeader::findOne($id);            

            // ถ้า -CN ถูกใช้ไปแล้ว ให้ต่อด้วย -CN-2
            $model->no_             = self::validateSeries($source);   

            if($_POST['RcInvoiceHeader']['no_'] != $model->no_){
                $model->no_         = $_POST['RcInvoiceHeader']['no_'];
            }

			$model->cust_no_ 		= $source->cust_no_;
			$model->cust_name_		= $source->cust_name_;
			$model->document_no_	= $source->document_no_;
			$model->posting_date 	= $_POST['RcInvoiceHeader']['posting_date'].' '.date('H:i:s');
			$model->doc_type 		= 'Credit-Note';
			$model->order_id 		= $source->order_id;
			$model->sales_people 	= $source->sales_people;
			$model->sale_id 		= $source->sale_id;
			$model->cust_code 		= $source->cust_code;
			$model->order_date	 	= $source->order_date;
			$model->ship_date 		= $source->ship_date;
			$model->cust_address 	= $source->cust_address;
			$model->cust_address2 	= $source->cust_address2;
			$model->contact 		= $source->contact;
			$model->phone 			= $source->phone;
			$model->district 		= $source->district;
			$model->city 			= $source->city;
			$model->province 		= $source->province;
			$model->postcode 		= $source->postcode;
			$model->discount 		= $source->discount;
			$model->percent_discount= $source->percent_discount;
			$model->vat_percent 	= $source->vat_percent;
			$model->include_vat		= $source->include_vat;
			$model->paymentdue		= $source->paymentdue;
			$model->payment_term 	= $source->payment_term;
            $model->ext_document	= $source->ext_document;
            $model->other_ref       = $source->other_ref;
			$model->remark 			= $_POST['RcInvoiceHeader']['remark'];
			$model->status 			= 'Posted';
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
                $data       = self::mapData($_POST['chk'],$_POST['quantity'],$_POST['unit_price']); 
                foreach ($data as $key => $value) {                   

                    $InvLine     = RcInvoiceLine::find()
                    ->where(['source_id' => $source->id])
                    ->andWhere(['id' => $value['id']])
                    ->one();                    
                    
                    $line  = new RcInvoiceLine();
                    
                    $line->type 			= $InvLine->type;
                    $line->item		 	    = $InvLine->item;
                    $line->doc_no_ 		    = $model->no_;
                    $line->line_no_ 		= $InvLine->id;
                    $line->source_id 		= $model->id;
                    $line->customer_no_	    = $model->cust_no_;
                    $line->code_no_		    = $InvLine->code_no_;
                    $line->code_desc_		= $InvLine->code_desc_;
                    $line->quantity 		= $value['qty'];
                    $line->unit_price 		= ($value['price']) * -1;
                    $line->vat_percent 	    = $InvLine->vat_percent;
                    $line->line_discount 	= $InvLine->line_discount;
                    $line->order_id 		= $InvLine->order_id;
                    $line->source_doc		= $InvLine->source_doc;
                    $line->source_line		= $InvLine->source_line;
                    $line->session_id 		= Yii::$app->session->getId();
                    $line->cn_reference     = $InvLine->id; 
                    $line->posting_date     = $model->posting_date;
                    $line->comp_id          = Yii::$app->session->get('Rules')['comp_id'];

                    if($line->save()){
                         //----------- Stock --------------	 
                        $stock = $WH->invenAdjust($line, $model);
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
                 
                
				// ถ้าดึงมาจากใบ SO 
				// ให้เก็บประวัติ
				if($model->order_id != ''){
					if(($SaleHeader = SaleHeader::findOne($model->order_id)) !== null) {
					
					
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
					

						$SaleHeader->status = 'Credit-Note';
						$SaleHeader->save();

					}
				}
				//------- /. Line ----------------	

			 
				 
                $transaction->commit();	
                
                
                return $this->redirect(['view','id' => base64_encode($model->id)]);

			}else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                //var_dump($model->getErrors());
                return $this->redirect(['index']);
            }

		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}

        //------- /. Header -------------------
        



    }



    protected function findModel($id)
    {
        if (($model = SaleInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findRcModel($id)
    {
        if (($model = RcInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    protected function validateSeries($source){
        $model = RcInvoiceHeader::find()
        ->where(['cn_reference' => $source->id])
        ->andWhere(['no_' => $source->no_])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->orderBy(['id' => SORT_DESC])
        ->one();

        $newNo = $source->no_;
        if($model != null){
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

    protected function mapData($id,$qty,$price){
        $set        = array_map(null,$id,$qty,$price); 
        
        $dataSet    = [];
        foreach ($set as $key => $value) {
            if($value[0]!=''){
                $dataSet[] = [
                    'id' => $value[0],
                    'qty' => $value[1],
                    'price' => $value[2],
                ];
            }
        }
        return $dataSet;
    }


    public function actionDelete($id,$no){
        
        $transaction = Yii::$app->db->beginTransaction();
		try {
            
            $findModel =ViewRcInvoice::findOne(['id' => base64_decode($id),'no_' => $no]);

            $source= (Object)[
                'id'        => 0,
                'source'    => 0,
                'no_'       => 0,
                'sumLine'   => 0,
                'order_id'  => 0
            ];

            try{
                       
    
                // Line Notify
                $bot =  \common\models\LineBot::findOne(5);
                $msg = 'Delete Credit Note '.$findModel->status."\r\n";
                $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                $msg.= $findModel->cust_name_."\r\n";
                $msg.= $findModel->no_."\r\n";
                $msg.= number_format($findModel->sumtotals->total,2)." บาท \r\n\r\n";
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                $bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }

            
            if ($findModel->status=='Posted'){
                $model = RcInvoiceHeader::findOne(base64_decode($id));
                if($model){
                    // เก็บค่าไว้ เพื่อ redirect กลับไปหา  Invoice
                    $source= (Object)[
                        'id'        => $model->id,
                        'source'    => $model->cn_reference,
                        'no_'       => $model->no_,
                        'sumLine'   => $model->sumLine,
                        'order_id'  => $model->order_id
                    ];                    
    
                    if(RcInvoiceLine::find()->where(['source_id' => $model->id])->exists()){
                        # Exists Sale Line
                        if(RcInvoiceLine::deleteAll(['source_id' => $model->id])){
                             
                            $model->delete();                            
                        }
                    }else{    
                        $model->delete();                        
                    }
                                
                                
                }
            }else{
                $model = SaleInvoiceHeader::findOne(base64_decode($id));
                if($model!=null){
                    // เก็บค่าไว้ เพื่อ redirect กลับไปหา  Invoice
                    $source= (Object)[
                        'id'        => $model->id,
                        'source'    => $model->cn_reference,
                        'no_'       => $model->no_,
                        'sumLine'   => $model->sumLine,
                        'order_id'  => $model->order_id
                    ];
                    
    
                    if(SaleInvoiceLine::find()->where(['source_id' => $model->id])->exists()){
                        # Exists Sale Line
                        if(SaleInvoiceLine::deleteAll(['source_id' => $model->id])){                             
                            $model->delete();                                                    
                        }
                    }else{    
                        if(!$model->delete()){
                            Yii::$app->getSession()->addFlash('danger',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
                        }                                            
                    }
                                
                                
                }else{
                    // Delete ซ้ำอีกครั้ง
                    // เนื่องจาก การสร้างมาจากหน้า Posted Invoice  แต่ status == Open
                    // ซึ่งใน table rc_invoice_header ไม่ควรมีสถานะ ​Open
                    $model = RcInvoiceHeader::findOne(base64_decode($id));
                    $source= (Object)[
                        'id'        => $model->id,
                        'source'    => $model->cn_reference,
                        'no_'       => $model->no_,
                        'sumLine'   => $model->sumLine,
                        'order_id'  => $model->order_id
                    ];                    
    
                    if(RcInvoiceLine::find()->where(['source_id' => $model->id])->exists()){
                        # Exists Sale Line
                        if(RcInvoiceLine::deleteAll(['source_id' => $model->id])){
                             
                            $model->delete();                            
                        }
                    }else{    
                        $model->delete();                        
                    }

                     
                    
                }
            }

           
           
            
            
            if($source->source==0){
                // ถ้าลดหนี้ไม่มีการอ้างอิงใบกำกับภาษี 
                // ให้ไปที่หน้าหลัก
                //Yii::$app->getSession()->addFlash('danger',json_encode($header->getErrors(),JSON_UNESCAPED_UNICODE)); 
                Yii::$app->getSession()->addFlash('success','Deleted');                
                
                $transaction->commit();	
                return $this->redirect(['index']);
            }else{
                $this->updateSaleOrder($source);
                $transaction->commit();	
                return $this->redirect(['index']);
                //return $this->redirect(['/accounting/posted/posted-invoice','id' => base64_encode($source->source)]);
            }
            

		} catch (\Exception $e) {
            Yii::$app->getSession()->addFlash('danger','Error'); 
			$transaction->rollBack();
			throw $e;
		}
    }


    public function actionJsonCreateItemLine()
	{

        $Item  	= Items::findOne(Yii::$app->request->post('item')); 
        if($Item == null){
            $Item = Items::findOne(1414);
            $Item->No = $_POST['code'];
            $Item->master_code = $_POST['code'];
        }  
                
        $model  = new SaleInvoiceLine();
        
        $model->source_id 	= 0;
        $model->type 		= 'Item';
        $model->item 		= $Item->id;
        $model->code_no_ 	= $Item->No;
        $model->code_desc_ 	= $Item->description_th;

        if(isset($_POST['desc'])) 	$model->code_desc_	= $_POST['desc'];
        if(isset($_POST['id'])) 	$model->source_id 	= $_POST['id'];
        if(isset($_POST['no'])) 	$model->doc_no_		= $_POST['no'];
        if(isset($_POST['type'])) 	$model->type		= $_POST['type'];
        if(isset($_POST['code'])) 	$model->code_no_	= $_POST['code'];
        
        $model->quantity 	    = Yii::$app->request->post('qty');
        $model->unit_price 	    = Yii::$app->request->post('price') * -1;
        $model->session_id 	    = Yii::$app->session->getId();
        $model->return_receive  = Yii::$app->request->post('qty');
        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

        if($model->save()){

            if ($model->source_id != 0){
                $header             = SaleInvoiceHeader::findOne($model->source_id);
                $total 	            = ($header->sumLine)? $header->sumLine : 1;
                $header->discount   = $total  * ($header->percent_discount / 100);
                if (!$header->save()){
                    return json_encode([
                        'status'    => 500,
                        'message'   => json_encode($header->getErrors(), JSON_UNESCAPED_UNICODE),
                    ]);
                }
            }

            return json_encode([
                'id' 		=> $model->id,		
                'itemid' 	=> $Item->id,		
                'item' 		=> $Item->master_code,
                'barcode' 	=> $Item->barcode,
                'desc' 		=> $model->code_desc_,
                'qty' 		=> $model->quantity,
                'price' 	=> $model->unit_price * -1,
                'data' 		=> [
                    'percent_discount' 	=> ($model->source_id)? $header->percent_discount : 0,
                    'discount'			=> ($model->source_id)? $header->discount : 0
                ]
            ]);

        }else {
           return json_encode([
                'status'    => 500,
                'message'   => json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE),
            ]);
        }	 
    }
    
    protected function updateSaleOrder($source){
        // ถ้าดึงมาจากใบ SO 
        // ให้เก็บประวัติ
        $model = RcInvoiceHeader::findOne($source->source);
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

	public function actionJsonGetCustomer($id)
    {
      
    	 
        $model = SaleInvoiceHeader::findOne($id);
        
         
        if($model->province=='') $model->province = 'Province';
        if($model->city=='') $model->city = 'city';
        if($model->district=='') $model->district = 'district';
        //if($model->postcode=='') $model->postcode = '10150';
        
        $data = [
        		 
        		'address'   => $model->cust_address,
        		'address2'	=> $model->cust_address2,
                'district'	=> $model->district,
                'city'	    => $model->city,
                'province'	=> $model->province,
                'postcode'	=> $model->postcode,
                 
        ];


        return json_encode($data);
        
        
    }
    public function actionGetInvoiceLine($id){

        $searchModel = new SaleinvlineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['source_id' => $id]);
        $dataProvider->pagination=false;

        
        return json_encode([
            'status' => 200,        
            'html' => $this->renderAjax('__invoice_line',['dataProvider' => $dataProvider])
        ]);
    }

    public function actionSaleInvoiceLine($id)
    {
       

        if(Yii::$app->request->isAjax){

            if(Yii::$app->request->post('key') != ''){
                $name   = '';
                $key    = 0;
                $value  = '';
                $model  = SaleInvoiceLine::findOne(Yii::$app->request->post('key'));

                $name   = Yii::$app->request->post('name');

                switch ($name) {
                    case 'desc':
                        $model->code_desc_      = Yii::$app->request->post('val');
                        break;
                    case 'qty':
                        $model->quantity        = Yii::$app->request->post('val');
                        $model->return_receive  = Yii::$app->request->post('val');
                        break;
                    case 'price':
                        $model->unit_price  = Yii::$app->request->post('val') * -1;
                        break;
                    default:
                        # code...
                        break;
                }

                $model->save();

                $header = SaleInvoiceHeader::findOne($model->source_id);
                $total  = ($header->sumtotals->sumline)? $header->sumtotals->sumline : 1;
				$header->discount 			= $total * ($header->percent_discount /100);			
				if(!$header->save()){
                    return json_encode([
                        'status' => 500,
                        'message' => json_encode($header->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);
                }

                $searchModel = new SaleinvlineSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $dataProvider->query->where(['source_id' => $id]);
                $dataProvider->pagination=false;

                $line = [];
                foreach ($dataProvider->models as $key => $model) {
                    $line[] = (Object)[
                        'id'            => $model->id,
                        'item'          => $model->item,
                        'code'          => $model->items->master_code,
                        'desc'          => ($model->code_desc_)? $model->code_desc_ : $model->items->description_th,
                        'price'         => $model->unit_price * 1,
                        'qty'           => $model->quantity * 1,
                        'line_amount'   => ($model->unit_price * $model->quantity) * 1
                    ];
                }

                return json_encode([
                    'status' => 200,
                    'data' => [
                        'discount' => ($model->source_id!=0)? $header->discount : 0,
                        'line' => $line
                    ],
                    'html' => $this->renderPartial('__invoice_line',['dataProvider' => $dataProvider])
                ]);
            }
        }

        $searchModel = new SaleinvlineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['source_id' => $id]);
        $dataProvider->pagination=false;

        return $this->renderAjax('__invoice_line',['dataProvider' => $dataProvider]);

    }


    public function actionCreateCreditNote($id){

        $status     = 200;
        $message    = Yii::t('common','Success');
        $source     = 0;
        $raws       = [];

        if(!isset($_POST['source'])){
            Yii::$app->session->setFlash('error', Yii::t('common','Nothing selected.'));
            return $this->redirect([
                'update',
                'id' => base64_encode(Yii::$app->request->post('source')['id']),
                'no' => Yii::$app->request->post('source')['no']]
            );
        }

        $sourceId         = Yii::$app->request->post('source')['id'];      
        
        // ดึงรายการมาทำ CN        
        $transaction = Yii::$app->db->beginTransaction();
		try {
			//------- Header ----------	
			$model  = SaleInvoiceHeader::findOne($id);
			$source = RcInvoiceHeader::findOne($sourceId);

			$model->cust_no_ 		= $source->cust_no_;
			$model->cust_name_		= $source->cust_name_;
			$model->document_no_	= $source->document_no_;
			$model->posting_date 	= date('Y-m-d H:i:s');
			$model->doc_type 		= 'Credit-Note';
			$model->order_id 		= $source->order_id;
			$model->sales_people 	= $source->sales_people;
			$model->sale_id 		= $source->sale_id;
			$model->cust_code 		= $source->cust_code;
			$model->order_date	 	= $source->order_date;
			$model->ship_date 		= $source->ship_date;
			$model->cust_address 	= $source->cust_address;
			$model->cust_address2 	= $source->cust_address2;
			$model->contact 		= $source->contact;
			$model->phone 			= $source->phone;
			$model->district 		= $source->district;
			$model->city 			= $source->city;
			$model->province 		= $source->province;
			$model->postcode 		= $source->postcode;
			$model->discount 		= $source->discount * -1;
			$model->percent_discount= $source->percent_discount;
			$model->vat_percent 	= $source->vat_percent;
			$model->include_vat		= $source->include_vat;
			$model->paymentdue		= $source->paymentdue;
			$model->payment_term 	= $source->payment_term;
			$model->ext_document	= $source->no_;
			$model->remark 			= '';
            $model->status 			= 'Open';
            ($model->cn_reference)?: $model->cn_reference    = $source->id;
            //$model->cn_reference    = ($model->cn_reference)?: $source->id;
			$model->session_id 		= Yii::$app->session->getId();
			$model->user_id 		= Yii::$app->user->identity->id;
            $model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];

			if($model->save()) {

                $sourceLine = RcInvoiceLine::find()
                ->where(['source_id' => $source->id])
                ->all();

                foreach ($sourceLine as $key => $value) {                   

                   
                    $line  = new SaleInvoiceLine();
                    
                    $line->type 			= $value->type;
                    $line->item		 	    = $value->item;
                    $line->doc_no_ 		    = $model->no_;
                    $line->line_no_ 		= $value->id;
                    $line->source_id 		= $model->id;
                    $line->customer_no_	    = $model->cust_no_;
                    $line->code_no_		    = $value->code_no_;
                    $line->code_desc_		= $value->code_desc_;
                    $line->quantity 		= $value->quantity;
                    $line->unit_price 		= ($value->unit_price) * -1;
                    $line->vat_percent 	    = $value->vat_percent;
                    $line->line_discount 	= $value->line_discount;
                    $line->order_id 		= $source->id;
                    $line->source_doc		= $source->no_;
                    $line->source_line		= $value->id;
                    $line->session_id 		= Yii::$app->session->getId();
                    $line->cn_reference     = $value->id;
                    $line->status 		    = 'Credit-Note';
                    $line->comp_id          = Yii::$app->session->get('Rules')['comp_id'];

                    if($line->save()){
                        $raws[] = [
                            'id'        => $line->id,
                            'status'    => 200,
                            'message'   => Yii::t('common','Done')
                        ];
                    }else{
                        $raws[] = [
                            'id'        => $line->id,
                            'status'    => 500,
                            'message'   => json_encode($line->getErrors(),JSON_UNESCAPED_UNICODE)
                        ];
                    }
                     
                }

                $source = $source->id;

                $status = 200;

			}else{ 
                $status     = 500;     
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);                 
            }

            $transaction->commit();	
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
        }
        
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => [
                'source'    => $source,
                'raws'      => $raws
            ]
        ]);

        
    }


    public function actionAjaxPost()
	{
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        
		$id 	                = (int)$data->id;
		$comp 	                = Yii::$app->session->get('Rules')['comp_id'];
		$keys 	                = 'posting-credit&inv:'.$id."&comp".$comp;

		$source	                = $this->findModel($id);		
        $ready 	                = RcInvoiceHeader::find()->where(['no_' => $source->no_, 'comp_id' => $comp])->one();
        
        $status                 = 200;
        $message                = Yii::t('common','Success');
        $returnId               = base64_encode($id);
        $returnNo               = '';
        $raw                    = [];
		// ถ้ามีเลขที่อยู่แล้ว ไม่อนุญาตให้บันทึกซ้ำ
		if($ready != null){
			return json_encode([
				'status' => 403,
				'message' => $id
			]);
			exit();
		}
			
		// ถ้ากำลังทำงาน ไม่ให้ post ซ้ำ
		if(Yii::$app->cache->get($keys)){
			return json_encode([
				'status' => 202,
				'message' => Yii::t('common','working')
			]);
		}else{
			// บันทึกการทำงาน
			// ถ้าทำเสร็จให้ไปลบที่   
			Yii::$app->cache->set($keys, true, 60);

			$transaction = Yii::$app->db->beginTransaction();
			try {								
				
				$posted = $this->doPostCreditNote($source);		    	
				
				if($posted->status == 200){
					//---- Clear Sale Invoice --------
					// Delete SaleInvoicLine
					SaleInvoiceLine::deleteAll(['source_id' => $id]);
					// Delete SaleInvoiceHeader
					$source->delete(); 			
                    //---- /. Clear Sale Invoice -----
                    $raw = $posted->stock;
                    Yii::$app->cache->delete($keys); 
                    $transaction->commit();			                                        
                    $returnId       = base64_encode($posted->id);
                    $returnNo       = $posted->no;
                    $message        = $posted->message;					 
				}else {		
                    Yii::$app->cache->delete($keys);   
                    $transaction->rollBack();                 
                    $status         = $posted->status;
                    $message        = $posted->message;					 			 
                }                

			} catch (\Exception $e) {
				Yii::$app->cache->delete($keys);
                $transaction->rollBack();
                $status         = 500;
                $message        = Yii::t('common','{:e}',[':e' => $e]);					 
            }
            
            return json_encode([
                'status' 	=> $status,
                'id' 		=> $returnId,
                'message' 	=> $message,
                'raw'       => $raw,
                'no'        => $returnNo
            ]);	 	
		}
		
    }
    

    protected function doPostCreditNote($source){

        $status     = 200;
        $message    = Yii::t('common','Success');
        $returnId   = 0;
        $returnNo   = '';
        // ดึงรายการมาทำ CN        
        $transaction = Yii::$app->db->beginTransaction();
		try {
            //------- Header ----------

            $model  = new RcInvoiceHeader();                    

            // ถ้า -CN ถูกใช้ไปแล้ว ให้ต่อด้วย -CN-2
            $model->no_             = self::validateSeries($source);   

			$model->cust_no_ 		= $source->cust_no_;
			$model->cust_name_		= $source->cust_name_;
			$model->document_no_	= $source->document_no_;
            $model->posting_date 	= $source->posting_date;
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
			$model->discount 		= $source->discount;
			$model->percent_discount= $source->percent_discount;
			$model->vat_percent 	= $source->vat_percent;
			$model->include_vat		= $source->include_vat;
			$model->paymentdue		= $source->paymentdue;
			$model->payment_term 	= $source->payment_term;
			$model->ext_document	= $source->ext_document;
			$model->remark 			= $source->remark;
			$model->status 			= 'Posted';
			$model->ref_inv_header 	= $source->id;
			$model->session_id 		= Yii::$app->session->getId();
			$model->user_id 		= Yii::$app->user->identity->id;
            $model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
            $model->cn_reference    = $source->cn_reference; 
            $model->other_ref       = $source->other_ref;
            $model->revenue         = $source->revenue;
            $model->show_doc        = $source->show_doc;
        
			if($model->save()){

                $returnId   = $model->id;
                $returnNo   = $model->no_;

                // ตามไป Update ที่การชำระเงินด้วย 
                $cheque     = \common\models\Cheque::find()->where(['apply_to_no' => $source->no_, 'apply_to' => $source->id, 'comp_id' => $model->comp_id])->one();
                if($cheque != null){
                    $cheque->apply_to           = $model->id;
                    $cheque->apply_to_status    = $model->status;
                    $cheque->save();
                }

                // Create Receive Header 
                //----------- Stock --------------	 
                // ไม่ต้องตัดสต๊อก เนื่องจากต้องรอการตรวจสอบจากผู้รับสินค้าก่อน ว่าสินค้าดีหรือสินค้าชำรุด
                // แล้วจะแจ้งจำนวนให้รับเข้าอีกครั้ง 
                /*  // Disabled 09/10/2020
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
                */

            
                $InvLine            = SaleInvoiceLine::find()->where(['source_id' => $source->id])->all();
                $stock              = [];
                foreach ($InvLine as $key => $SaleLine) {                 
                    
                    $line                   = new RcInvoiceLine();
                    
                    $line->type 			= $SaleLine->type;
                    $line->item		 	    = $SaleLine->item;
                    $line->doc_no_ 		    = $model->no_;
                    $line->line_no_ 		= $SaleLine->id;
                    $line->source_id 		= $model->id;                    
                    $line->customer_no_	    = $model->cust_no_;
                    $line->code_no_		    = $SaleLine->code_no_;
                    $line->code_desc_		= $SaleLine->code_desc_;
                    $line->quantity 		= $SaleLine->quantity * 1;
                    $line->unit_price 		= $SaleLine->unit_price * 1;
                    $line->vat_percent 	    = $SaleLine->vat_percent * 1;
                    $line->line_discount 	= $SaleLine->line_discount * 1;
                    $line->order_id 		= $SaleLine->order_id;
                    $line->source_doc		= $SaleLine->source_doc;
                    $line->source_line		= $SaleLine->source_line;
                    $line->session_id 		= Yii::$app->session->getId();
                    $line->cn_reference     = $SaleLine->id; 
                    $line->posting_date     = $model->posting_date;
                    $line->return_receive   = $SaleLine->return_receive;
                    $line->comp_id          = Yii::$app->session->get('Rules')['comp_id'];
                                  

                    if($line->save()){ 
                                                
                        //----------- Stock --------------	 
                        // ไม่ต้องตัดสต๊อก เนื่องจากต้องรอการตรวจสอบจากผู้รับสินค้าก่อน ว่าสินค้าดีหรือสินค้าชำรุด
                        // แล้วจะแจ้งจำนวนให้รับเข้าอีกครั้ง
                        //$stock[] = $WH->invenAdjust($line, $model);
                      
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
					}
				}
                //------- /. Line ----------------
                  
			}else{
                $status     = 500;
                $message    = Yii::t('common',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));                 
            }
            $transaction->commit();
		} catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);        
        }
        
        return (Object)[
            'status'    => $status,
            'id'        => $returnId,
            'message'   => $message,
            'stock'     => $stock,
            'no'        => $returnNo
        ];
    }

    public function actionUpdateField(){
        $request_body           = file_get_contents('php://input');
        $data                   = json_decode($request_body);
        $status                 = 200;
        $message                = Yii::t('common','Success');
        $id     = (int)base64_decode($data->id);

       // $findModel =ViewRcInvoice::findOne(['id' => $id,'no_' => Yii::$app->request->get('no') ,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        if ($data->status=='Posted'){
            $model      = $this->findRcModel($id);           
        }else{
            $model      = $this->findModel($id);            
        }
        

        $field          = $data->field;
        //$model  = RcInvoiceHeader::findOne($id);   
        $model->$field  = $data->value;

        if(!$model->save()){

            
            
            $status     = 500;
            $message    = Yii::t('common',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
            $data       = '';
        } 

        try{ 
            // Line Notify
            $bot =  \common\models\LineBot::findOne(5);
            $msg = "\r\n".'Update Field Credit Note '.$model->status."\r\n";
            $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
            $msg.= $model->doc_type."\r\n\r\n";
            $msg.= $model->cust_name_."\r\n";
            $msg.= $model->no_."\r\n";
            $msg.= number_format($model->sumtotals->total,2)." บาท \r\n\r\n";
            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

            $bot->notify_message($msg);					

        } catch (\Exception $e) {					 
            //Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
        }
        // else{
        //     return json_encode([
        //         'status'    => 500,
        //         'message'   => Yii::t('common',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)),
        //         'data'      => ''
        //     ]);
        // }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => $data->value
        ]);

    }
}
