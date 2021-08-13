<?php

namespace admin\modules\Purchase\controllers;

use Yii;
use common\models\PurchaseReqHeader;
use admin\modules\Purchase\models\ReqHeaderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use admin\models\Generater;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use common\models\Items;
use common\models\PurchaseReqLine;
use admin\modules\Purchase\models\PurchaseReqLineRender;
use admin\models\FunctionCenter;
use common\models\Itemunitofmeasure;
use common\models\PrintPage;
/**
 * ReqController implements the CRUD actions for PurchaseReqHeader model.
 */
class ReqController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'json-create-item-line' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all PurchaseReqHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReqHeaderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PurchaseReqHeader model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PurchaseReqHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $Free   = $this->findEmpty();
        if(isset($Free->id))
        {
            return $this->redirect(['update', 'id' => $Free->id]);
        }

        $model = new PurchaseReqHeader();
        
        $model->vendor_id       = 1;
        $model->vendor_name     = 'เงินสด';
        $model->doc_no          = Generater::getRuning('purchase_req_header','doc_no','all');
        $model->order_date      = date('Y-m-d');
        $model->create_date     = date('Y-m-d H:i:s');
        $model->delivery_date   = date('Y-m-d');
        $model->percent_discount= '';
        $model->include_vat     = '1';
        $model->session_id      = Yii::$app->session->getId();
        $model->user_id         = Yii::$app->user->identity->id;
        $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

        if($model->save()){
            Generater::UpdateSeries('purchase_req_header','doc_no','all',$model->doc_no);
            return $this->redirect(['update', 'id' => $model->id]);             
        }else{
            
            Yii::$app->getSession()->setFlash('alert',[
                'body'=>'<i class="fa fa-times-circle text-red"></i> '.json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                'options'=>['class'=>'bg-danger']
            ]);
            return $this->redirect(['index', 'id' => $model->id]);   
        }

    }

    /**
     * Updates an existing PurchaseReqHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchModel = new PurchaseReqLineRender();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andwhere(['source_id' => $id]);
        $dataProvider->query->orderBy(['priority' => SORT_ASC]);

        if(Yii::$app->request->post('ajax')){
            /**
            * Update Purchase Line
            *
            */
            $data   = $_POST['data'];
            $field  = (string)$_POST['name'];

            $Line   = PurchaseReqLine::findOne($_POST['key']);
            $Line->$field       = $data;
            $Line->source_id    = $id;

            
            if($_POST['name']=='unit_of_measure'){
                $measure = Itemunitofmeasure::findOne(['measure' => $_POST['data'],'item' => $Line->item,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                $Line->quantity_per_unit = $measure->qty_per_unit;
            }

            if($Line->save()){
                return json_encode([
                    'status' => 200,
                    'message' => 'done'
                ]);
            }else {
                return json_encode([
                    'status' => 500,
                    'message' => 'error'
                ]);
            }


        }


        if ($model->load(Yii::$app->request->post())) {



            /*
            * Validate new vendor.
            * Automatic create customer when get from customer table.
            * Validate by refer_id
            */
            if($model->vendor_id == 0){

              $model->vendor_id = $model->createVendor();
            }

            $model->session_id  = Yii::$app->session->getId();
            $model->user_id     = Yii::$app->user->identity->id;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            $model->balance     = $model->total->total;

            if(!$model->save()){

              Yii::$app->getSession()->setFlash('alert',[
                  'body'=>'<i class="fa fa-times-circle text-red"></i> '.json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                  'options'=>['class'=>'bg-danger']
              ]);

              return $this->render('update', [
                  'model' => $model,
                  'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
              ]);

            };
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing PurchaseReqHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(in_array($model->status,['0', '10'])){
            # Allow Only status Open
            # 1. Delete Purchase Line
            # 2. Delete Purchase Header
            $transaction = Yii::$app->db->beginTransaction();
		    try {
                if(PurchaseReqLine::find()->where(['source_id' => $id])->exists()){
                    # Exists Purchase Line
                    if(PurchaseReqLine::deleteAll(['source_id' => $id])){
                        # Delete Purchase Line
                        if($model->delete()){
                            # Delete Purchase Header
                            $transaction->commit();                              
                        }else {
                            # Error Delete Purchase Header
                            $transaction->rollBack();
                        }
                    }else {
                        # Error Delete Purchase Line
                        $transaction->rollBack();
                    }
                }else {
                    // Empty Purchase Line
                    if($model->delete()){
                        # Delete Purchase Header
                        $transaction->commit();
                    }else {
                        # Error Delete Sale Header
                        $transaction->rollBack();
                    }
                }                                   
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }else{           
            Yii::$app->getSession()->addFlash('warning','You can\'t not remove this document');
        }
        
        return $this->redirect(['index']);
    }



    public function actionAjaxTotal($id){

        $model = PurchaseReqHeader::findOne($id);
  
        $JSON = [
          'total'             => '',
          'discount'          => '',
          'percentDiscount'   => '',
          'subtotal'          => '',
          'beforeVat'         => 0,
          'vat'               => '0',
          'vat_type'          => '0',
          'include_vat'       => '1',
          'grandtotal'        => '',
          'withholdTax'       => '0'
        ];
  
        if ($model !== null) {
  
            $JSON = [
              'total'           => $model->total->beforediscount,
              'discount'        => $model->total->discount,
              'percentDiscount' => $model->percent_discount,
              'subtotal'        => $model->total->subtotal,
              'beforeVat'       => $model->total->beforeVat,
              'vat'             => (string)abs($model->vat_percent),
              'vat_type'        => (string)abs($model->vat_type),
              'include_vat'     => (string)$model->include_vat,
              'grandtotal'      => $model->total->total,
              'withholdTax'     => $model->withholdTax,
            ];
  
        }
  
        return json_encode($JSON);
      }
  
    public function actionJsonCreateItemLine()
    {

        $Item  = Items::findOne(['id' => $_POST['item']]);
        if($Item==null){
            $Item  = Items::findOne(['barcode' => $_POST['item']]);
            if($Item==null){
                $Item  = Items::findOne(['No' => $_POST['item']]);
            }
        }
        
        $model = new PurchaseReqLine();


        if(isset($_POST['code'])) 	{
            $Item       = Items::findOne(['id' => $_POST['code']]);
            $Item->master_code   = $_POST['item_no'];
        }
        

        $model->source_id   = 0;
        $model->source_no   = 0;
        $model->type        = 'Item';
        $model->item        = $Item->id;
        $model->items_no    = ($Item->barcode)? $Item->barcode : $Item->master_code;
        $model->description = $Item->description_th;
        $model->location    = $model->defaultlocation;
        $model->quantity    = 0;
        $model->unitcost    = 0;


        if(isset($_POST['desc']))   $model->description = $_POST['desc'];
        if(isset($_POST['id']))     $model->source_id   = $_POST['id'];
        if(isset($_POST['no']))     $model->source_no   = $_POST['no'];
        if(isset($_POST['type']))   $model->type        = $_POST['type'];
        if(isset($_POST['qty']))    $model->quantity    = ($_POST['qty'])? $_POST['qty'] : 0;
        if(isset($_POST['price']))  $model->unitcost    = ($_POST['price'])? $_POST['price'] : 0;

        
        
        $model->unit_of_measure     = $Item->unit_of_measure;
        $model->quantity_per_unit   = $Item->quantity_per_unit;
        $model->lineamount          = $model->quantity * $model->unitcost;
        $Priority                   = PurchaseReqLine::find()->select('max(priority) as priority')->where(['source_id' => $_POST['id']])->one();
        $model->priority            = $Priority->priority +1;

        $model->user_id             = Yii::$app->user->identity->id;
        $model->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

        $measureList = [];
        foreach ($Item->itemunitofmeasures as $key => $value) {
            $measureList[] = [
                'id' => (string)$value->measure,
                'name' => $value->measures->UnitCode,
                'qty_per' => $value->qty_per_unit * 1,
            ];
            
        }

        if($model->save(false))
        {
            return json_encode([
                'id'        => $model->id,
                'item'      => $model->item,
                'item_no'   => (string)$model->items_no,
                'desc'      => $model->description,
                'qty'       => $model->quantity,
                'price'     => $model->unitcost,
                'location'  => $model->location,
                'measure'   => (string)$model->unit_of_measure,
                'unitofmeasure' => $measureList,
                'qty_per_unit'  => $model->items->quantity_per_unit
            ]);

        }else {

            print_r($model->getErrors());
            exit();

        }



    }

    public function actionAngularGet($id){
        $query = PurchaseReqLine::find()->where(['source_id' => $id])->all();
        
        $JSON = [];
        $i    = 0;
        foreach ($query as $key => $model) {
            $i++;

            $measureList = [];
            if($model->items){            
                foreach ($model->items->itemunitofmeasures as $key => $value) {
                    $measureList[] = [
                        'id' => (string)$value->measure,
                        'name' => $value->measures->UnitCode,
                        'qty_per' => $value->qty_per_unit  * 1,
                    ];                    
                }
            }

            $JSON[]= [
                'i'             => $i,
                'id'            => $model->id,
                'source_no'     => $model->source_no,
                'type'          => $model->type,
                'item'          => $model->item,
                'item_no'       => (string)$model->items_no,
                'description'   => $model->description,
                'quantity'      => $model->quantity,
                'unitcost'      => $model->unitcost,
                'lineamount'    => $model->lineamount,
                'linediscount'  => $model->linediscount,
                'priority'      => $model->priority,
                'expected_date' => $model->expected_date,
                'measure'       => (string)$model->unit_of_measure,
                'unitofmeasure' => $measureList,
                'qty_per_unit'  => $model->items ? $model->items->quantity_per_unit : '',
                'genpo'         => $model->convertpo
            ];
        }


        return json_encode($JSON);
    }

    public function actionAjaxDeletePurLine(){
        $model = PurchaseReqLine::find()
        ->where(['source_id' => $_POST['pur']])
        ->andWhere(['id' => $_POST['data']])
        ->one();

        if($model){
          if(!$model->delete())
          {
            Yii::$app->getSession()->setFlash('alert',[
                'body'=>'<i class="fa fa-times-circle text-red"></i> '.json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                'options'=>['class'=>'bg-danger']
            ]);
            return $this->redirect('update', [
                'id' => $_POST['pur'],
            ]);

          }
        }else {
          Yii::$app->getSession()->setFlash('alert',[
              'body'=>'<i class="fa fa-times-circle text-red"></i> '.json_encode($model,JSON_UNESCAPED_UNICODE),
              'options'=>['class'=>'bg-danger']
          ]);
          return $this->redirect(['update','id' => $_POST['pur']]);
        }


    }


    public function actionViewLine($id)
    {
      if(Yii::$app->request->isAjax) {

        if(Yii::$app->request->post('ids')){
          $array = $_POST['ids'];
          $prop = '';
          foreach ($array as $key => $value) {
            $prop = $this->findModelLine($value['id']);

            if($prop){
              $prop->priority = $key;
              $prop->save(false);
            }

          }

        }


      }else {
        return $this->render('view', [
            'model' => $this->findModelLine($id),
        ]);
      }

    }


    public function actionPrint($id){

        $model      = $this->findModel($id);
        $query      = PurchaseReqLine::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['priority'=>SORT_ASC]],
        ]);

        $template      = PrintPage::findOne(PrintPage::findCustomPrint('purchase_req'));  
        $Company  = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();
        $header = (Object)[
            'height'    => $template->header_height,
            'top'       => $template->margin_top,
            'fontsize'  => $template->font_size,
            'title'     => $model->doc_no,
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
            '{COMPANY_LOGO}'        => '<img src="'.$Company->logoViewer.'" style="width: 110px;">',
            '{COMPANY_NAME}'        => $Company->name,
            '{COMPANY_NAME_EN}'     => $Company->name_en,
            '{COMPANY_ADDRESS}'     => $Company->vat_address.' อ.'.$Company->vat_city.' จ.'.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_ADDRESS_EN}'  => $Company->vat_address.' '.$Company->vat_city.' '.$Company->vat_location.' '.$Company->postcode,
            '{COMPANY_PHONE}'       => $Company->phone,
            '{COMPANY_FAX}'         => $Company->fax,
            '{COMPANY_MOBILE}'      => $Company->mobile,
            '{COMPANY_EMAIL}'       => $Company->email,
            '{DOCUMENT_NO}'         => $model->doc_no,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->order_date)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->order_date.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,
        
            
            '{APPLY_FOR}'           => $model->detail,
            '{CREATER}'             => $model->purchaser,
        
            '{VENDOR_CODE}'         => $model->vendor->code,
            '{VENDOR_NAME}'         => $model->vendor->name,
            '{VENDOR_ADDRESS}'      => $model->vendor->address,
            '{VENDOR_TAX}'          => $model->vendor->vat_regis,
        
            '{REF_NO}'              => $model->ref_no,
            '{REF_TO}'              => $model->ext_document,
            '{PURCHASE_REQUEST}'    => $model->purchaser,
            '{REF_TERM_OF_PAYMENT}' => $model->payment_term,
            '{DELIVERY_SCHEDULE}'   => $model->delivery_date,
        
            '{PROJECT_NAME}'        => $model->projects != null ? $model->projects->name : null,
            '{PROJECT_PLACE}'       => $model->projects != null ? $model->projects->place : null,
            '{DEPARTMENT}'          => $model->department,
            '{ATTENTION}'           => $model->contact,
         
            '{LABEL_REMARK}'        => 'หมายเหตุ : ',
            '{VALUE_REMARK}'        => wordwrap($model->remark, 250, "<br/>\n", false),
            '{LABEL_TOTAL}'         => 'รวมเป็นเงิน',
            '{VALUE_TOTAL}'         => number_format($model->total->beforediscount,2),
            '{LABEL_DISCOUNT}'      => 'ส่วนลด',
            '{VALUE_DISCOUNT}'      => number_format($model->total->discount,2),
            '{LABEL_SUBTOTAL}'      => Yii::t('common','Total after discount'),
            '{VALUE_SUBTOTAL}'      => number_format($model->total->subtotal,2),
            '{LABEL_VAT}'           => 'ภาษีมูลค่าเพิ่ม VAT',
            '{VALUE_VAT}'           => $model->vat_percent.' %',
            '{VALUE_INCLUDEVAT}'    => number_format($model->total->includevat,2),
            '{LABEL_GRANDTOTAL}'    => Yii::t('common','Grand total'),
            '{VALUE_GRANDTOTAL}'    => number_format($model->total->total,2),
            '{VALUE_BAHTTEXT}'      => $Bahttext->ThaiBaht($model->total->total - (($model->withholdTax * $model->total->subtotal)/100)),
        
            '<!--IF_WHT-->'         => $model->withholdTaxSwitch == 1 ? ' ': '<!--',
            '<!--IF_WHT_END-->'     => $model->withholdTaxSwitch == 1 ? ' ': '-->',
            '<!--IF_NOT_WHT-->'     => $model->withholdTax <= 0 ? ' ': '<!--',
            '<!--IF_NOT_WHT_END-->' => $model->withholdTax <= 0 ? ' ': '-->',
        
            '{LABEL_WHT_PERCENT}'   => 'หักภาษี ณ ที่จ่าย',
            '{VALUE_WHT_PERCENT}'   => $model->withholdTax,
            '{VALUE_WHT}'           => ($model->withholdTax * $model->total->subtotal)/100,
            '{LABEL_TOTAL_WHT}'     => 'ยอดชำระ',
            '{VALUE_TOTAL_WHT}'     => number_format($model->total->total - (($model->withholdTax * $model->total->subtotal)/100), 2)
        ];

        $PrintTemplate = $this->renderPartial('@admin/views/setting/printer-template',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'print' => $template,
            'Company' => $Company,
            'header' => $header,
            'body'  => $body,
            'defineHeader' => $defineHeader
        ]);

        // get your HTML raw content without any layouts or scripts
        $views = $template->paper_orientation == 'P' ? '_print_body_portrait' : '_print_body' ;
        $content = $this->renderPartial($views,[
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'header'        => $header,
            'print'         => $template,
            'body'          => $body
        ]);
  
  

        $customFontsConfig = Yii::$app->params['mpdfCustomFontsPath'];
        $customFonts = Yii::$app->params['mpdfCustomFonts'];
        define("_MPDF_SYSTEM_TTFONTS_CONFIG", $customFontsConfig);
        define("_MPDF_SYSTEM_TTFONTS", $customFonts);
        
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
            'filename'      => $model->doc_no.'.pdf',
            // any css to be embedded if required @page {margin: 0; }
            'cssInline'     => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px; }',
            // set mPDF properties on the fly
            'options'       => [
                'title' => 'PR : '.$model->doc_no.' ',
            ],
            // call mPDF methods on the fly
            'methods'       => [
                //'WriteHTML' => $PrintTemplate,
                 
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

        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->WriteHtml($PrintTemplate);
        

        return $pdf->render();
  
    }



    public function actionConvert($id){

        //ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $reqHead    = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $header     = new \common\models\PurchaseHeader();
            $header->vendor_id          = $reqHead->vendor_id;
            $header->vendor_name        = $reqHead->vendor_name;
            $header->doc_no             = Generater::getRuning('purchase_header','vatbus_posting_group',$reqHead->vendor->vatbus_posting_group);
            $header->order_date         = date('Y-m-d');
            $header->create_date        = date('Y-m-d H:i:s');    
            $header->delivery_date      = $reqHead->delivery_date;    
            $header->balance            = $reqHead->balance;
            $header->discount           = $reqHead->discount;
            $header->percent_discount   = $reqHead->percent_discount;
            $header->vat_type           = $reqHead->vat_type;
            $header->include_vat        = $reqHead->include_vat;
            $header->vat_percent        = $reqHead->vat_percent;
            $header->payment_term       = $reqHead->payment_term;
            $header->payment_due        = $reqHead->payment_due;
            $header->remark             = $reqHead->remark;
            $header->ext_document       = $reqHead->ext_document;

            $header->ref_no             = $reqHead->doc_no; //Get from (PR)

            $header->session_id         = Yii::$app->session->getId();
            $header->status             = 10;
            
            $header->withholdTaxSwitch  = $reqHead->withholdTaxSwitch;
            $header->withholdTax        = $reqHead->withholdTax;
            $header->withholdAttach     = $reqHead->withholdAttach;
            $header->project            = $reqHead->project;
            $header->user_id            = Yii::$app->user->identity->id;
            $header->comp_id            = Yii::$app->session->get('Rules')['comp_id'];

           
            if($header->save()){
                Generater::UpdateSeries('purchase_header','vatbus_posting_group',$reqHead->vendor->vatbus_posting_group,$header->doc_no);
            }else{
                Yii::$app->getSession()->addFlash('danger',json_encode($header->getErrors(),JSON_UNESCAPED_UNICODE)); 
                $transaction->rollBack();
                return $this->redirect(['view','id' => $id]);
            }

            // Update Status Purchase Request
            $reqHead->status            = 1;
            $reqHead->update();


            $ReqLine    = PurchaseReqLine::find()->where(['source_id' => $reqHead->id])->all();
            foreach ($ReqLine as $key => $line) {
                //echo $line->description;
                $model = new \common\models\PurchaseLine();
                $model->source_id       = $header->id;
                $model->source_no       = $header->doc_no;
                $model->type            = 'item';
                $model->item            = $line->item;
                $model->items_no        = $line->items_no;
                $model->description     = $line->description;
                $model->location        = $line->location;
                $model->quantity        = $line->quantity;
                $model->unit_of_measure = $line->unit_of_measure;
                $model->quantity_per_unit= $line->quantity_per_unit;
                $model->unitcost        = $line->unitcost;
                $model->lineamount      = $line->lineamount;
                $model->linediscount    = $line->linediscount;
                $model->expected_date   = $header->delivery_date;
                $model->planned_date    = $line->planned_date;
                $model->priority        = $line->priority;
                $model->ref_line        = $line->id;
                $model->user_id         = Yii::$app->user->identity->id;
                $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];

                if(!$model->save()){
                    var_dump($model->getErrors());
                    $transaction->rollBack();
                    exit();
                }
            }
            $transaction->commit();
            return $this->redirect(['/Purchase/order/update','id' => $header->id]);
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;

            return $this->redirect(['view','id' => $id]);
        }
        
    }


    public function actionSourceRequest(){
        $query = PurchaseReqHeader::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
        
        $JSON = [];
        
        foreach ($query as $key => $model) {        
            $JSON[]= [
 
                'id'            => $model->id,
                'no'            => $model->doc_no,
                'project'       => $model->projects ? $model->projects->name : '',
                'apply'         => $model->remark,
                'attn'          => $model->contact,
                'depart'        => $model->department,
                'owner'         => $model->user_id,
                'owner_name'    => $model->purchaser
                //'owner_name'    => $model->user->firstname.' '.$model->user->lastname               
            ];
        }


        return json_encode($JSON);
    }

    public function actionSourceRequestList(){
        $query = PurchaseReqLine::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
        
        $JSON = [];
        
        foreach ($query as $key => $model) {        
            $JSON[]= [
 
                'id'            => $model->id,
                'po'            => $model->source_id,
                'source'        => $model->source_no,
                'type'          => $model->type,
                'item'          => $model->item,
                'code'          => $model->items ? $model->items->master_code : $model->items_no,
                'desc'          => $model->description,
                'location'      => $model->locations->name,
                'qty'           => $model->quantity,
                'measure'       => $model->unitofmeasures->UnitCode,
                'qty_per'       => $model->quantity_per_unit,
                'cost'          => $model->unitcost,  
                'genpo'         => $model->convertpo           
            ];
        }


        return json_encode($JSON);
    }
    /**
     * Finds the PurchaseReqHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchaseReqHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchaseReqHeader::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }

    protected function findModelLine($id)
    {
        if (($model = PurchaseReqLine::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    protected function findEmpty()
	{
		$model = PurchaseReqHeader::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->andWhere(['balance' => 0])
		->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
		->andWhere("MONTH(create_date) = '".date('m')."' ")
		->One();
		return $model;
    }
    

    public function actionFindAuto(){
        $model = PurchaseReqHeader::find()
        ->where(['doc_no' => '']);

        return json_encode([
            'data' => 'TEST'
        ]);
    }
}
