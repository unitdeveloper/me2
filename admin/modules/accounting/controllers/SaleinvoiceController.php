<?php

namespace admin\modules\accounting\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\SaleInvoiceHeader;
use admin\modules\accounting\models\SaleinvoiceSearch;
use admin\modules\accounting\models\SaleinvlineSearch;
use admin\modules\accounting\models\RcinvheaderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


use common\models\SaleInvoiceLine;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use admin\models\Generater;

use common\models\RcInvoiceHeader;
use common\models\SalesPeople;
use common\models\SaleHeader;
use common\models\ViewRcInvoice;
use admin\modules\accounting\models\FunctionAccounting;
/**
 * SaleinvoiceController implements the CRUD actions for SaleInvoiceHeader model.
 */
class SaleinvoiceController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['create', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['angular',
                                        'index',
                                        'create',
                                        'update',
                                        'view',
                                        'delete',
                                        'print',
                                        'print-inv',
                                        'print-inv-page',
                                        'salebilling',
                                        'salebilling-note',
                                        'salebilling-print',
                                        'sale-invoice-line',
                                        'gen-no',
                                        'cancel-document'
                                    ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'cancel-document' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all SaleInvoiceHeader models.
     * @return mixed
     */
    public function actionAngular()
    {
        return $this->render('angularjs');
    }
    public function actionIndex()
    {
        // Clear Link
        Yii::$app->session->set('lasturl',NULL);
        Yii::$app->session->set('lastid',NULL);

        $searchModel = new SaleinvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->query->andWhere(["sale_invoice_header.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);

        // if(isset($_GET['SaleinvoiceSearch']['posting_date'])){
        //     if(date('Y',strtotime($_GET['SaleinvoiceSearch']['posting_date'])) != '1970'){
        //         // Register ปีที่ค้นหา
        //         Yii::$app->session->set('workyears',date('Y',strtotime($_GET['SaleinvoiceSearch']['posting_date'])));
        //         $dataProvider->query->andWhere(['like','DATE(posting_date)' , date('Y-m-d',strtotime($_GET['SaleinvoiceSearch']['posting_date']))]);
        //     }          
        // }


        // $dataProvider->query->orderBy([
        //     'no_' => SORT_DESC,
        //     'order_date' => SORT_DESC
        //     ]);
        

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSalebilling()
    {
        $query = \common\models\BillingNote::find()->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            
        ]);

        return $this->render('salebilling_list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SaleInvoiceHeader model.
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
     * Creates a new SaleInvoiceHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionGenNo(){
        $model              = new SaleInvoiceHeader();
        return $model->getAutoNumber(0);
    }
    public function actionCreate()
    {
        $model              = new SaleInvoiceHeader();
        $status             = 200;
        $message            = '';
        
        if(Yii::$app->request->isAjax) {           
            $searchModel        = new SaleinvlineSearch();
            $dataProvider       = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->where(['source_id' => '']);
            

            if (FunctionAccounting::validateBillCreate($_GET['cust']) == true) {

                $RcHeader = \common\models\RcInvoiceHeader::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['cust_no_' => $_GET['cust']])
                    ->andWhere(['order_id' => $_GET['id']]);

                if ($RcHeader->exists()) {

                    // $searchModel = new RcinvheaderSearch();
                    // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    // $dataProvider->query->where(['cust_no_' => $_GET['cust']]);
                    // $dataProvider->query->andwhere(['order_id' => $_GET['id']]);
                    $dataProvider = new ActiveDataProvider([
                        'query' => $RcHeader,
                        'pagination' => false,                        
                    ]);

                    return $this->renderAjax('_rc_render', [
                        //'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);

                } else {
                    
                    $searchModel = new SaleinvoiceSearch();
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    $dataProvider->query->where(['cust_no_' => $_GET['cust']]);
                    $dataProvider->query->andwhere(['order_id' => $_GET['id']]);

                    return $this->renderAjax('home', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);
                }

            }else{
                return $this->renderAjax('__script_modal', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }

            

        }else {
            $transaction = Yii::$app->db->beginTransaction();
            try {

               
                // Auto Create
                $NoSeries           = $model->getAutoNumber(0);  
                
                if($NoSeries=='Error-01'){ return $this->redirect(['/install', 'id' => 1]); exit();}
                
                if(self::checkNumber($NoSeries)){ // ถ้ามีอยู่แล้ว
                    
                    //$no           = Generater::NextRuning('vat_type','vat_value','7',false);  

                    
                    $inv = ViewRcInvoice::find()->where(['no_' => $NoSeries, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();
                    if($inv!=null){
                        
                        if($inv->status=='Open'){
                            Yii::$app->session->setFlash('error', Yii::t('common','Already Exists').' ['.$NoSeries.']');
                            return $this->redirect(['update', 'id' => $inv->id]); 
                            
                        }else{
                            
                            return $this->redirect(['/accounting/posted/posted-invoice', 'id' => base64_encode($inv->id)]); 
                        }

                    }else{
                        
                        // $model->no_             = $NoSeries;
                        // $model->status          = 'Open';
                        // $model->user_id         = Yii::$app->user->identity->id;
                        // $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                        // $model->cust_name_      = 'เงินสด';
                        // $model->cust_address    = '';
                        // $model->cust_no_        = 909;
                        // $model->session_id      = Yii::$app->session->getId();
                        // $SALES                  = SalesPeople::findOne(Yii::$app->session->get('Rules')['sale_id']);
                        // $model->sales_people    = $SALES->code;
                        // $model->sale_id         = $SALES->id;
                        // $model->posting_date    = date('Y-m-d H:i:s');
                        // $model->order_date      = date('Y-m-d');
                        // $model->ship_date       = date('Y-m-d');
                        // $model->cust_code       = '999';
                        // $model->document_no_    = $NoSeries;
                        // $model->doc_type        = 'Sale';
                        // $model->city            = 814;
                        // $model->district        = 7352;
                        // $model->province        = 59;
                        // $model->postcode        = 74000;
                        // $model->status          = 'Open';
                        // $model->paymentdue      = date('Y-m-d');
                        // $model->discount        = 0;
                        // $model->percent_discount= 0;
                        // $model->vat_percent     = 7;
                        // $model->payment_term    = 0;
                        // $model->include_vat     = 1;
                        
                        // if($model->save()){
                        //     $status         = 200;
                        //     $UpdateSeries   = Generater::CreateNextNumber('vat_type', 'vat_value', '7', $model->no_);
                        // }else{
                        //     $status         = 500;
                        //     $message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                        // }
                    }
                   

                }else{ // ถ้าไม่มีเลขที่เอกสาร
                
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
                    $model->doc_type        = 'Sale';
                    $model->city            = 814;
                    $model->district        = 7352;
                    $model->province        = 59;
                    $model->postcode        = 74000;
                    $model->status          = 'Open';
                    $model->paymentdue      = date('Y-m-d');
                    $model->discount        = 0;
                    $model->percent_discount= 0;
                    $model->vat_percent     = 7;
                    $model->payment_term    = 0;
                    $model->include_vat     = 1;
                    
                    if($model->save()){
                        $status         = 200;
                        $UpdateSeries   = Generater::CreateNextNumber('vat_type', 'vat_value', '7', $model->no_);
                        $transaction->commit();
                    }else{
                        $transaction->rollBack();
                        $status         = 500;
                        $message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                    }
                } 

                
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                return $this->redirect(['index']);
                exit;
                //throw $e;
            }

            if($status == 200){               
                return $this->redirect(['update', 'id' => $model->id, 'no' => $model->no_]); 
            }else{
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $message]));
                return $this->redirect(['index']);
            }
            
        }



        // $Fnc                = new FunctionAccounting();
        // $SessID             = Yii::$app->session->getId();   
        // $model              = new SaleInvoiceHeader();
        // $searchModel        = new SaleinvlineSearch();
        // $dataProvider       = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['source_id' => '']);        
        // $NoSeries           = Generater::NextRuning('vat_type','vat_value','7',false);
        // if($NoSeries=='Error'){ return $this->redirect(['/install', 'id' => 1]); exit();}
        

        // if(Yii::$app->request->isAjax) {

        //     if(Yii::$app->request->post('hasEditable'))
        //     {                
        //         $InvLine    = SaleInvoiceLine::findOne(Yii::$app->request->post('editableKey'));
        //         $out        = json_encode(['output' => '', 'message' => '']);              
        //         $posted     = current($_POST['SaleInvoiceLine']);     
        //         $post       = ['SaleInvoiceLine' => $posted];
        //         if($InvLine->load($post))
        //         {
        //             $InvLine->save();                  
        //         }
        //         echo $out;                
        //         return;      
        //     }

        //     // 
        //     if($Fnc->validateBillCreate($_GET['cust'])==true)
        //     {
        //         $RcHeader = \common\models\RcInvoiceHeader::find()
        //         ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        //         ->andWhere(['cust_no_' => $_GET['cust']])
        //         ->andWhere(['order_id' => $_GET['id']]);
        //         if($RcHeader->exists())
        //         {
        //             $searchModel = new RcinvheaderSearch();
        //             $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //             $dataProvider->query->where(['cust_no_' => $_GET['cust']]);
        //             $dataProvider->query->andwhere(['order_id' => $_GET['id']]);
        //             return $this->renderAjax('_rc_render', [
        //                 'searchModel' => $searchModel,
        //                 'dataProvider' => $dataProvider,
        //             ]);                    
        //         }else {
        //             $searchModel = new SaleinvoiceSearch();
        //             $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //             $dataProvider->query->where(['cust_no_' => $_GET['cust']]);
        //             return $this->renderAjax('home', [
        //                 'searchModel' => $searchModel,
        //                 'dataProvider' => $dataProvider,
        //             ]);
        //         }               
        //         return $this->renderAjax('home', [
        //             'searchModel' => $searchModel,
        //             'dataProvider' => $dataProvider,
        //             'link' => $link,
        //         ]);
        //     }
        //     return $this->renderAjax('_form', [
        //             'model' => $model,
        //             'searchModel' => $searchModel,
        //             'dataProvider' => $dataProvider,
        //         ]);
        // }else {
        //     if ($model->load(Yii::$app->request->post())) {
        //         $model->status          = 'Open';
        //         $model->user_id         = Yii::$app->user->identity->id;
        //         $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
        //         $model->cust_no_        = $_POST['SaleInvoiceHeader']['cust_no_'];
        //         $model->session_id      = $SessID;
        //         $SALES                  = SalesPeople::findOne($_POST['SaleInvoiceHeader']['sale_id']);
        //         $model->sales_people    = $SALES->code;
        //         $model->sale_id         = $SALES->id;
        //         $model->doc_type        = $_POST['SaleInvoiceHeader']['doc_type'];
                
        //         if($model->doc_type=='Sale'){
        //             $UpdateSeries       = Generater::CreateNextNumber('vat_type','vat_value',$model->vat_percent,$model->no_);
        //         }
                
        //         if($model->save())
        //         {
        //             $Fnc->updateSaleInvoiceLine($model);
        //             if($model->doc_type=='Credit-Note'){
        //                 return $this->redirect(['update', 'id' => $model->id,'cn' => 'true']);     
        //             }else{
        //                 return $this->redirect(['update', 'id' => $model->id]); 
        //             }               
        //         }else {
        //             print_r($model->getErrors());
        //         }                
        //     } else {
        //         return $this->render('create', [
        //             'model' => $model,
        //             'searchModel' => $searchModel,
        //             'dataProvider' => $dataProvider,
        //         ]);
        //     }
        // }
    }

    /**
     * Updates an existing SaleInvoiceHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // $searchModel = new SaleinvlineSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['source_id' => $id]);

        $query   = SaleInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            
        ]);
        
        if(Yii::$app->request->isAjax) {

            // Percent Discount
            if(Yii::$app->request->post('percentDiscount') != ''){   
                $model->discount            =   $model->sumLine * (Yii::$app->request->post('percentDiscount') / 100);  
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

              
                $posted = current($_POST['SaleInvoiceLine']);
     
                $post   = ['SaleInvoiceLine' => $posted];

                if($InvLine->load($post))
                {

                    $InvLine->save();
                   

                }

                echo $out;
                
                return;

      
            }


            if ($model->load(Yii::$app->request->post())) {


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
                $model->cust_no_        = $_POST['SaleInvoiceHeader']['cust_no_'];
                $SALES                  = SalesPeople::findOne($_POST['SaleInvoiceHeader']['sale_id']);
                $model->sales_people    = $SALES->code;
                $model->sale_id         = $SALES->id;

                $GenSeries        = new Generater();
                $NoSeries         = $GenSeries->LastNumber('vat_type','vat_value',$model->vat_percent);

                 

                $UpdateSeries       = $GenSeries->CreateNextNumber('vat_type','vat_value',$model->vat_percent,$NoSeries);

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

    

    public function actionPrintInv($id)
    {
            
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        // $searchModel = new SaleinvlineSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider->query->where(['source_id' => $model->id]);
        //$dataProvider->pagination->page=1;
        //$dataProvider->pagination->pageSize=50;

        $query   = SaleInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => false,
            
        ]);
        $dataProvider->pagination->pageSize=10;
        

        $PageFooter = '';

         
        //$pdf->SetHTMLHeader('<img src="' . base_url() . 'custom/Hederinvoice.jpg"/>');

        //$pdf->SetHTMLFooter('xxxx'); 
    
    
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('__print_inv',[
                    'model' => $model,
                     
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

            'format' => Pdf::FORMAT_LETTER,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@admin/web/css/pdf.css',
            'cssFile' => 'css/pdf.css',
             
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css', 
            'filename' => 'Inv_'.$model->no_,
            // any css to be embedded if required
            'cssInline' => '@page {margin: 0;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'INVOICE : '.$model->no_.' '],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[''],
                //'SetFooter'=>['{PAGENO}'],
                //'SetHTMLFooter' => ''.$PageFooter.'',
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

    public function actionPrint($id)
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findModel($id);

        $query   = SaleInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            
        ]);

        $template   = \common\models\PrintPage::findOne(\common\models\PrintPage::findCustomPrint( $model->vat_percent > 0 ? 'invoice_vat' : 'invoice_novat' ));
  
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
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->order_date.' + 543 Years')),
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
            '{VALUE_REMARK}'        => wordwrap($model->remark, 45, "<br/>\n", false),
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
            ]
        ];

        
  
        
        return $pdf->render();
    }

    public function actionPrintInvPage($id)
    {
     
        $company    = Yii::$app->session->get('Rules')['comp_id'];
        $model      = $this->findModel($id);
        $query      = SaleInvoiceLine::find()->where(['source_id' => $model->id]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false            
        ]);

        $PageHeader = $this->renderPartial('__print_inv_header',[
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('__print_inv_page',[
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
        
        // setup kartik\mpdf\Pdf component
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
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css', 
            'filename' => $model->no_.'.pdf',
            // any css to be embedded if required @page {margin: 0; }
            'cssInline' => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'INVOICE : '.$model->no_.' '],
            // call mPDF methods on the fly
            'methods' => [
                'WriteHTML' => $PageHeader
            ]
        ]);

        if(isset($_GET['papersize'])){

            if($_GET['papersize']=='A3'){
                $pdf->format = Pdf::FORMAT_A3;
            }else if($_GET['papersize']=='A4'){
                $pdf->format = Pdf::FORMAT_A4;
            }else if($_GET['papersize']=='LETTER'){
                $pdf->format = Pdf::FORMAT_LETTER;
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
            
            //$text1.= 'การชำระค่าสินค้า กรุณาสั่งจ่ายในนาม บจก. จีโนลกรุ๊ป ซีที อิเล็กทริคฟิเคชั่น ธ.กสิกรไทย สาขาถนนเศรษฐกิจ 1'."<br>\r\n";
            //$text1.= 'เลขที่บัญชี 464-2-35454-7 (ในกรณีจ่ายเช็คกรุณาเขียนเช็คหรือโอนเงินเข้าบัญชีในนามบริษัทฯ'."<br>\r\n";
            //$text1.= 'การชำระค่าสินค้า กรุณาสั่งจ่ายในนาม MR. JINYU ZHAO ธ.กสิกรไทย/เชฟ-อี พุทธมลฑลสาย2'."<br>\r\n";
            //$text1.= 'เลขที่บัญชี 767-2-17421-4 (ในกรณีจ่ายเช็ค), กรณีโอนเงิน ให้โอนเข้าบัญชี MR. JINYU ZHAO ธ.ไทยพาณิชย์ '."<br>\r\n";
            //$text1.= 'สาขา ถนน.กาญจนาภิเษก(บางแวก) เลขที่บัญชี 131-222931-2 หรือตามที่ระบุไว้เท่านั้น ห้ามจ่ายเงินสดหรือเช็คให้พนักงานขาย'."<br>\r\n";
            // Print with form
            //$pdf->format = Pdf::FORMAT_A4;
            $pdf->cssInline = '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:13px;}';

            $pdf->content = $this->renderPartial('__print_inv_content_with_form',[
                                'model' => $model,
                                'dataProvider' => $dataProvider,
                                'text1' => $text1,                                 
            ]);
            
            $pdf->methods = [
                                'WriteHTML' => $this->renderPartial('__print_inv_header_with_form',[
                                                'model' => $model,
                                                'dataProvider' => $dataProvider,
                                                'text1' => $text1, 
                                            ]),

            ];
        }

        if(isset($_GET['download'])){
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
            ]
        ];

       
        return $pdf->render();
    }


    public function actionSaleInvoiceLine($id)
    {
        $searchModel    = new SaleinvlineSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['source_id' => $id]);
        $dataProvider->pagination=false;
        $status         = 200;
        $message        = 'Done';

        if(Yii::$app->request->isAjax){
           
            if(isset($_POST['key'])){
                
                $name   = '';
                $key    = 0;
                $value  = '';
                $model  = SaleInvoiceLine::findOne($_POST['key']);

                if(isset($_POST['name'])) $name   = $_POST['name'];

                switch ($name) {
                    case 'desc':
                        $model->code_desc_  = $_POST['val'];
                        break;
                    case 'qty':
                        $model->quantity    = $_POST['val'];
                        break;
                    case 'price':
                        $model->unit_price  = $_POST['val'];
                        break;
                    case 'line_discount':
                        $model->line_discount  = $_POST['val'] =='' ? 0 : $_POST['val'];
                        break;
                    case 'measure':
                        $model->measure  = $_POST['val'];
                        break;
                    default:
                        # code...
                        break;
                }

                if($model->save(false)){
                    $status     = 200;
                }else{
                    $status     = 500;
                    $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                }

                $header = SaleInvoiceHeader::findOne($model->source_id);
				$header->discount 			= $header->sumLine * ($header->percent_discount /100);			
				if(!$header->save()){
                    return json_encode([
                        'status' => 500,
                        'message' => json_encode($header->getErrors(),JSON_UNESCAPED_UNICODE)
                    ]);
                }

                return json_encode([
                    'status' => $status,
                    'data' => [
                        'discount' => ($model->source_id!=0)? $header->discount : 0
                    ],
                    'message' => $message,
                    'html' => $this->renderAjax('__invoice_line',['dataProvider' => $dataProvider])
                ]);
            }
            
            
        }

        return $this->renderAjax('__invoice_line',['dataProvider' => $dataProvider]);

    }

    
    /**
     * Deletes an existing SaleInvoiceHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        
        $model      = $this->findModel($id);
        $ivNo       = $model->no_;
        $balance    = $model->sumtotals->total;
        $custName   = $model->customer ? $model->customer->name : '';
        
        $transaction = Yii::$app->db->beginTransaction();
        try {

            // 1 Delete Line
            // REF http://www.yiiframework.com/doc-2.0/yii-db-activerecord.html#deleteAll()-detail
            $InvLine = SaleInvoiceLine::find()->where(['source_id' => $id])->all();
            foreach ($InvLine as $model) {
                $model->delete();
            } 

            // 2 Delete Header
            $model = $this->findModel($id);
            if ($model->shipments){                
                $wh = \common\models\WarehouseHeader::findOne($model->shipments->id);
                $wh->status = 'Shiped';
                if(!$wh->save()){
                    Yii::$app->session->setFlash('error', json_encode($wh->getErrors(),JSON_UNESCAPED_UNICODE));
                }
            }

            // ถ้าลบบิล ให้ไปแก้สถาณะใบงานเป็น ส่งของ(ยังไม่เปิดบิล)
            $Order  = SaleHeader::findOne($model->order_id);
            $OrderNo= '';
            if($Order != null){
                $Order->status  = 'Shiped';
                $OrderNo        = $Order->no;
                $Order->save();
            }

            
            
            try{                      
                // Line Notify
                $bot = \common\models\LineBot::findOne(1);
                $msg = 'Delete Invoice  (Not Post)'."\r\n";
                $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                $msg.= $custName."\r\n";
                $msg.= $ivNo."\r\n";
                $msg.= number_format($balance,2)." บาท \r\n\r\n";
                $msg.= $OrderNo."\r\n";
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

                $bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
            }

            $model->delete();

            $transaction->commit();

            Yii::$app->session->setFlash('success', Yii::t('common','Success'));
            
            if(Yii::$app->session->get('lasturl')){
                $url    = Yii::$app->session->get('lasturl');
                $id     = Yii::$app->session->get('lastid');

                Yii::$app->session->set('lasturl',NULL);
                Yii::$app->session->set('lastid',NULL);
                
                return $this->redirect([$url,'id' => $id]);
            }else{
                return $this->redirect(['index']);
            }


            
        } catch (\Exception $e) {            
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', json_encode(Yii::t('common','{:e}',[':e' => $e]),JSON_UNESCAPED_UNICODE));
            return $this->redirect(['update', 'id' => $model->id]);           
        }

        
    }

    public function actionCancelDocument(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $status = 200;
        $message = Yii::t('common','Success');
        
        $model = SaleInvoiceHeader::findOne($data->id);
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
    

    /**
     * Finds the SaleInvoiceHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleInvoiceHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function checkNumber($no){
        return ViewRcInvoice::find()->where(['no_' => $no, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists();
    }
}
