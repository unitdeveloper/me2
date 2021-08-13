<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use common\models\ItemJournal;
use common\models\ItemJournalLine;
use admin\modules\warehousemoving\models\journalSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

use admin\models\Generater;
use common\models\Items;
use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use common\models\PrintPage;
/**
 * JournalController implements the CRUD actions for ItemJournal model.
 */
class JournalController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ItemJournal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new journalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemJournal model.
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
     * Creates a new ItemJournal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemJournal();

        if ($model->load(Yii::$app->request->post())) {

            $model->PostingDate     = $model->PostingDate.' '.date('H:i:s');
            $model->user_id     = Yii::$app->user->identity->id;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            $UpdateSeries       = Generater::CreateNextNumber('item_journal',$model->TypeOfDocument,$model->AdjustType,$model->DocumentNo);

            if($model->save(false))
            {

                if(isset($_POST['ItemJournalLine'])){
                    $lines = self::loadLine($_POST['ItemJournalLine']); 
                
                    foreach ($lines as $key => $line) {

                        $AdjLine = ItemJournalLine::findOne(['id' => $line->id]);

                        $AdjLine->source_id         = $model->id;
                        $AdjLine->DocumentNo        = $model->DocumentNo;                
                        $AdjLine->SourceDoc         = $model->id;
                        $AdjLine->SourceDocNo       = $model->DocumentNo;
                        $AdjLine->unit_price        = $line->data->unit_price;

                        //$Quantity                   = ($model->AdjustType=='-')? abs($line->data->Quantity) * -1 : abs($line->data->Quantity);
                        $Quantity                   = $line->data->Quantity;
                        
                        $AdjLine->Quantity          = $Quantity;
                        $AdjLine->QtyToMove         = abs($Quantity);
                        $AdjLine->QtyMoved          = 0;
                        $AdjLine->QtyOutstanding    = abs($Quantity);
                        $AdjLine->TypeOfDocument    = $model->TypeOfDocument;
                        $AdjLine->location          = ($line->data->location)? $line->data->location : $AdjLine->validateLocations();
                        $AdjLine->unit_of_measure   = $line->data->unit_of_measure;

                        $AdjLine->qty_per_unit      = $AdjLine->qtyperunit;

                        if($AdjLine->location=='Nil'){                        
                            Yii::$app->session->setFlash('error', Yii::t('common','Please create location code.'));
                            return $this->redirect(['/location/location']);
                            exit();
                        }

                        if($AdjLine->save())
                        {                 
                            Yii::$app->getSession()->addFlash('success','<i class="far fa-save"></i> '.Yii::t('common','Saved'));                    
                        }else{
                            Yii::$app->getSession()->addFlash('warning',json_encode($AdjLine->getErrors(),JSON_UNESCAPED_UNICODE));            
                        }
                    }

                        
                }


                
                //return $this->redirect(['index']);
                return $this->redirect(['update', 'id' => $model->id]);

            }else {

                Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
               

            }


        } 
        // Init
        $model->AdjustType                  = '-';
        $model->TypeOfDocument              = 'Adjust';
        if($model->isNewRecord) $model->id  = 0; 
        if($model->DocumentDate=='')$model->DocumentDate = date('Y-m-d');

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ItemJournal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    static function loadLine($params){
        $data = [];
        foreach ($params as $attribute => $line) {
            foreach ($line as $id => $value) {
                $data[$id][$attribute] = $value;
            }
        }

        $list = [];
        foreach ($data as $key => $value) {
            $list[] = (Object)[
                'id' => $key,
                'data' => (Object)$value
            ];
        }
        return $list;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            

            $model->PostingDate     = $model->PostingDate.' '.date('H:i:s');
            $model->user_id     = Yii::$app->user->identity->id;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];

            if(!$model->save())
            {
                print_r($model->getErrors());
                exit();
            }else {

                if(isset($_POST['ItemJournalLine'])){
                    $lines = self::loadLine($_POST['ItemJournalLine']); 
                
                    foreach ($lines as $key => $line) {

                        $AdjLine = ItemJournalLine::findOne(['id' => $line->id]);

                        if(!isset($line->data->unit_of_measure)){
                            Yii::$app->session->setFlash('error', Yii::t('common','Some items don\'t have units.'));
                            return $this->redirect(['update', 'id' => $model->id]);
                        }

                        $AdjLine->source_id         = $model->id;
                        $AdjLine->DocumentNo        = $model->DocumentNo;                
                        $AdjLine->SourceDoc         = $model->id;
                        $AdjLine->SourceDocNo       = $model->DocumentNo;
                        $AdjLine->unit_price        = $line->data->unit_price;

                        //$Quantity                   = ($model->AdjustType=='-')? abs($line->data->Quantity) * -1 : abs($line->data->Quantity);
                        $Quantity                   = $line->data->Quantity;
                        
                        $AdjLine->Quantity          = $Quantity;
                        $AdjLine->QtyToMove         = abs($Quantity);
                        $AdjLine->QtyMoved          = 0;
                        $AdjLine->QtyOutstanding    = abs($Quantity);
                        $AdjLine->TypeOfDocument    = $model->TypeOfDocument;
                        $AdjLine->location          = ($line->data->location)? $line->data->location : $AdjLine->validateLocations();
                        $AdjLine->unit_of_measure   = $line->data->unit_of_measure;

                        $AdjLine->qty_per_unit      = $AdjLine->qtyperunit;

                        if($AdjLine->location=='Nil'){                        
                            Yii::$app->session->setFlash('error', Yii::t('common','Please create location code.'));
                            return $this->redirect(['/location/location']);
                            exit();
                        }

                        if($AdjLine->save())
                        {                 
                            Yii::$app->getSession()->addFlash('success','<i class="far fa-save"></i> '.Yii::t('common','Saved'));                    
                        }else{
                            Yii::$app->getSession()->addFlash('warning',json_encode($AdjLine->getErrors(),JSON_UNESCAPED_UNICODE));            
                        }
                    }
                }
                //return $this->redirect(['index']);
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } 

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ItemJournal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(in_array($model->status,[null,'0', '10'])){
            # Allow Only status Open
            # 1. Delete Journal Line
            # 2. Delete Journal Header
            $transaction = Yii::$app->db->beginTransaction();
		    try {
                if(ItemJournalLine::find()->where(['source_id' => $id])->exists()){
                    # Exists Journal Line
                    if(ItemJournalLine::deleteAll(['source_id' => $id])){
                        # Delete Journal Line
                        if($model->delete()){
                            # Delete Journal Header
                            $transaction->commit();                              
                        }else {
                            # Error Delete Journal Header
                            $transaction->rollBack();
                        }
                    }else {
                        # Error Delete Journal Line
                        $transaction->rollBack();
                    }
                }else {
                    // Empty Journal Line
                    if($model->delete()){
                        # Delete Journal Header
                        $transaction->commit();
                    }else {
                        # Error Delete Journal Header
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

    /**
     * Finds the ItemJournal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemJournal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemJournal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }





    public function actionPrint($id){

        

        $model      = $this->findModel($id);

        $query      = ItemJournalLine::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $template      = PrintPage::findOne(PrintPage::findCustomPrint('item_journal'));
  
        $Company  = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();

        $header = (Object)[
            'height'    => $template->header_height,
            'title'     => $model->Description,
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
            '{DOCUMENT_NO}'         => $model->DocumentNo,
            '{CREATER}'             => $model->users->profile->name,
            '{ORDER_DATE}'          => date('d/m/y',strtotime($model->PostingDate)),
            '{ORDER_DATE_EN}'       => date('d/m/y',strtotime($model->PostingDate)),
            '{ORDER_DATE_TH}'       => date('d/m/y',strtotime($model->PostingDate.' + 543 Years')),
            '{COMPANY_TAX}'         => $Company->vat_register,
            '{COMPANY_HEAD}'        => $Company->headofficetb->data_char,
            '{APPLY_FOR}'           => $model->Description,        
            '{CREATOR}'             => $model->users->profile->name,        
            '{REF_TO}'              => $model->ext_document,                     
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
  
        $content = $this->renderPartial('print_content',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'header' => $header,
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
            'filename'      => $model->DocumentNo.'.pdf',
            // any css to be embedded if required @page {margin: 0; }
            'cssInline'     => '@page {margin: 0; } body{font-family: saraban, sans-serif; font-size:11px; }',
            // set mPDF properties on the fly
            'options'       => ['title' => 'PR : '.$model->DocumentNo.' ',],
            // call mPDF methods on the fly
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

    public function actionPostJournal()
    {
        $sourceId = '';

        if(isset($_POST['sourceId']))
        {
            $sourceId = $_POST['sourceId'];
        }else {
            Yii::$app->getSession()->addFlash('warning','Error : '.$sourceId.'');            
        }

        $query      = ItemJournalLine::find()->where(['source_id' => $sourceId])->all();
        $journal    = ItemJournal::findOne($sourceId);


        $transaction = Yii::$app->db->beginTransaction();

        try{

            $head 			    = new WarehouseHeader();	
           
            $head->PostingDate  = date('Y-m-d H:i:s');
            $head->DocumentDate = date('Y-m-d');
            $head->TypeOfDocument 	= 'Adjust';            
            $head->SourceDocNo 	= $journal->id;
            $head->SourceDoc    = $journal->DocumentNo;
            $head->DocumentNo   = $journal->DocumentNo;
            $head->ship_to     = 0;
    
            $head->status       = 'Transfer';

            $head->user_id 		= Yii::$app->user->identity->id;
            $head->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
            $head->line_no 		= $journal->id;

            if($head->save()){    
            
                $i = 0;
                $len = count($query);
                foreach ($query as $source) {
                    $model  = new WarehouseMoving();
                    $model->PostingDate     = $source->PostingDate;
                    if(isset($_POST['postDate'])) $model->PostingDate =  $_POST['postDate'].' '.date('H:i:s');                     
                    if($_POST['adjType']=='-')
                    {
                        //--ปรับให้เป็น (-) 
                        $source->Quantity = $source->Quantity * -1;
                    }else {
                        //--ปรับให้เป็น (+) 
                        $source->Quantity = $source->Quantity;
                    }                    
                    $model->line_no         = $source->line_no;
                    $model->source_id       = $head->id;
                    $model->DocumentNo      = $source->DocumentNo;
                    $model->TypeOfDocument  = $source->TypeOfDocument;
                    $model->item            = $source->item;
                    $model->ItemNo          = $source->ItemNo;
                    $model->Description     = $source->Description;
                    $model->SourceDocNo     = $source->DocumentNo;
                    $model->SourceDoc       = $source->id;
                    $model->Quantity        = $source->Quantity;
                    $model->QtyToMove       = abs($source->QtyToMove);
                    $model->QtyMoved        = abs($source->QtyMoved);
                    $model->QtyOutstanding  = 0;
                    $model->DocumentDate    = $source->DocumentDate;
                    $model->qty_per_unit    = $source->qty_per_unit;
                    $model->unit_price      = $source->unit_price;
                    $model->user_id         = Yii::$app->user->identity->id;
                    $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
                    $model->session_id      = Yii::$app->session->getId();
                    $model->location        = $source->location;

                    $model->qty_before		    = $source->items->liveInven;
                    $model->qty_after		    = $model->qty_before + $model->Quantity;
            
                    
                    if($model->save(false)){

                        // update item 
                        $model->items->updateQty;
                        // $item  = Items::findOne($model->item);
                        // $item->last_stock = $model->qty_after;
                        // $item->save(false);

                        $PostLine = ItemJournalLine::findOne($source->id);
                        //$PostLine->status = 'Posted';
                        if($PostLine->delete())
                        {
                            if ($i == $len - 1) {

                                // Remove Header At last record
                                if(ItemJournal::findOne($sourceId)->delete()){                                             
                                    
                                    Yii::$app->getSession()->addFlash('info','<i class="far fa-save"></i> '.Yii::t('common','Posted'));
                                    $transaction->commit();
                                    return json_encode([
                                        'status' => 200,
                                        'message' => 'done',
                                        'value' => [
                                            'id' => $model->id,
                                            'doc' => $model->DocumentNo
                                        ]
                                    ]);

                                }else {
                                    
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->addFlash('warning','Clear header Error...');
                                    return $this->redirect(['update', 'id' => $model->id]);
                                }

                            }
                        }else {
                            $transaction->rollBack();
                            Yii::$app->getSession()->addFlash('warning','Clear line Error...');
                            return $this->redirect(['update', 'id' => $model->id]);  
                        }

                    }else {
                        Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));    
                        $transaction->rollBack();
                        return $this->redirect(['update', 'id' => $model->id]);

                    }
                    $i++;

                }
            }

            $transaction->commit();
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }


    }



    public function actionAjaxLine($id)
    {
        if(isset($_GET['id']))
        {
            $query   = ItemJournalLine::find()->where(['source_id' => $_GET['id']])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        }else{
            $query   = ItemJournalLine::find()->where(['session_id' => Yii::$app->session->getId(), 'source_id' => 0])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        if(Yii::$app->request->isAjax){

            if(Yii::$app->request->post()){


                $name   = '';
                $key    = 0;
                $value  = '';

                if(isset($_POST['key'])) $key   = $_POST['key'];

                $model  = ItemJournalLine::findOne($key);

                if(isset($_POST['name'])) $name   = $_POST['name'];

                switch ($name) {
                    case 'desc':
                        $model->Description  = $_POST['val'];
                        break;
                    case 'qty':
                        $model->Quantity    = $_POST['val'];
                        break;
                    case 'price':
                        $model->unit_price  = $_POST['val'];
                        break;
                    case 'location':
                        $model->location  = $_POST['val'];
                        break;
                    default:
                        # code...
                        break;

                }

                $model->save();
            }

            return $this->renderAjax('_journal_line',['dataProvider' => $dataProvider]);
        }

        return $this->render('_journal_line', [
                    'dataProvider' => $dataProvider,
                ]);



    }

    public function actionJsonCreateItemLine()
    {
        $Item       = Items::findOne($_POST['item']);
        $model      = new ItemJournalLine();
        //ถ้าราคาเท่ากัน ให้เพิ่มจำนวน
        $price      = (@$_POST['price'])? $_POST['price'] : 1;
        $oldData    = ItemJournalLine::find()->where([
                        'item'          => $Item->id,
                        'source_id'     => (@$_POST['id'])? $_POST['id'] : 0                    
                    ])
                    ->andWhere(['unit_price' => $price])->one();

        // Measure List          
        //var_dump($Item->unitofmeasures); exit();
        $measureList = [];
        foreach ($Item->itemunitofmeasures as $key => $value) {
          
            $measureList[] = [
                'id' => (string)$value->measure,
                'name' => $value->measures->UnitCode,
                'qty_per' => $value->qty_per_unit * 1,
            ];
            
        }

        if($oldData){
            $model  = ItemJournalLine::findOne($oldData->id);
            $model->Quantity    = $oldData->Quantity + 1;
            $model->save();

            return json_encode([
                'id'        => $model->id,
                'item'      => $Item->master_code,
                'desc'      => $model->Description,
                'remain'    => $Item->inven,
                'qty'       => $model->Quantity,
                'price'     => $model->unit_price,
                'status'    => 201,
                'measure'   => (string)$model->unit_of_measure,
                'unitofmeasure'   => $measureList
            ]);
            exit();

        }else{            
            $model->line_no         = '';
            $model->source_id       = 0;
            $model->DocumentNo      = '-';
            $model->PostingDate     = date('Y-m-d H:i:s');
            $model->TypeOfDocument  = '';
            $model->SourceDoc       = '0';
            $model->item            = $Item->id;
            $model->ItemNo          = $Item->No;
            $model->Description     = $Item->Description;
            $Quantity               = $_POST['qty'];
            if(isset($_POST['desc']))       $model->Description         = $_POST['desc'];
            if(isset($_POST['id']))         $model->source_id           = $_POST['id'];
            if(isset($_POST['docNo']))      $model->DocumentNo          = $_POST['docNo'];
            if(isset($_POST['typeDoc']))    $model->TypeOfDocument      = $_POST['typeDoc'];
            if(isset($_POST['adjType'])){
                // if($model->source_id!=0){
                //     $Header     = ItemJournal::findOne($model->source_id);
                //     if($Header->AdjustType == '+') $Quantity = abs($Quantity);
                //     if($Header->AdjustType == '-') $Quantity = abs($Quantity) * -1;
                // }else {

                    
                    
                //     if($_POST['adjType']=='-')
                //     {
                //         //--ปรับให้เป็น (-) เสมอ--
                //         //$Quantity = abs($Quantity) * -1;
                //     }else {
                //         //--ปรับให้เป็น (+) เสมอ--
                //         //$Quantity = abs($Quantity);
                        
                //     }
                    
                    
                // }
            }
            $model->SourceDocNo     = $model->DocumentNo;
            $model->Quantity        = $Quantity;
            $model->QtyToMove       = $Quantity;
            $model->QtyMoved        = $Quantity;
            $model->QtyOutstanding  = 0;
            $model->DocumentDate    = date('Y-m-d');
            $model->qty_per_unit    = 1;
            $model->unit_price      = $_POST['price'];
            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->session_id      = Yii::$app->session->getId();
            $model->location        = $model->validateLocations();

            if($model->location=='Nil'){                        
                Yii::$app->session->setFlash('error', Yii::t('common','Please create location code.'));
                return $this->redirect(['/location/location']);
                exit();
            }

            if($model->save())
            {   
                // หากมีการสร้างใหม่ จะไม่ดึง location ไปแสดง
                // เนื่องจากปรับปรุงระบบให้มีการสร้าง Location เองแล้ว
                // ดังนั้น จึงต้องส่ง location list ไปทุกครั้งที่มีการเพิ่มรายการ 
                $locate = \common\models\Location::find()
                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->all();
                $data = [];
                foreach ($locate as $key => $value) {
                    $data[] = [
                        'id'    => $value->id,
                        'code'  => $value->code,
                        'name'  => $value->name
                    ];
                }

                

                return json_encode([
                    'id'        => $model->id,
                    'item'      => $Item->master_code,
                    'desc'      => $model->Description,
                    'remain'    => $Item->inven,
                    'qty'       => $model->Quantity,
                    'price'     => $model->unit_price,
                    'status'    => 200,
                    'location'  => $data,
                    'measure'   => (string)$model->unit_of_measure,
                    'unitofmeasure'   => $measureList
                ]);

            }else {

                print_r($model->getErrors());
                exit();

            }
        }
    }


    public function actionDeleteAdjLine()
    {
        $model = ItemJournalLine::findOne($_POST['id']);

        if($model->delete())
        {
            return true;
        }else {
            var_dump($_POST['id']);
            print_r($model->getErrors());
            exit();
        }
    }


    public function actionGetInventoryByLocation(){
        $model = ItemJournalLine::findOne($_POST['key']);
        $location = (int)$_POST['locator'];
        if($model !==null){
            return json_encode([
                'status' => 200,
                'id' => $model->id,
                'item' => $model->item,
                'location' => $location,
                'inven' => $model->items->invenByLocation($location)
            ]);
        }

        return json_encode([
            'status' => 404
        ]);
    }


    public function actionJournalLineList(){
        return $this->render('journal_line_list');
    }
}
