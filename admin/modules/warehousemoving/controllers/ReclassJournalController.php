<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use common\models\ItemReclassJournal;
use common\models\ItemReclassJournalLine;
use admin\modules\warehousemoving\models\reclassJournalSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use admin\models\Generater;
use common\models\Items;
use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use common\models\PrintPage;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

/**
 * ReclassJournalController implements the CRUD actions for ItemReclassJournal model.
 */
class ReclassJournalController extends Controller
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
     * Lists all ItemReclassJournal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new reclassJournalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemReclassJournal model.
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
     * Creates a new ItemReclassJournal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemReclassJournal();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();

            try{
                $model->PostingDate     = $model->PostingDate.' '.date('H:i:s');
                $model->user_id     = Yii::$app->user->identity->id;
                $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
                

                if($model->save())
                {

                    if(isset($_POST['ItemReclassJournalLine'])){
                        $lines = self::loadLine($_POST['ItemReclassJournalLine']); 
                    
                        foreach ($lines as $key => $line) {

                            $AdjLine = ItemReclassJournalLine::findOne(['id' => $line->id]);

                            $AdjLine->source_id         = $model->id;
                            $AdjLine->DocumentNo        = $model->DocumentNo;                
                            $AdjLine->SourceDoc         = $model->DocumentNo;  
                            $AdjLine->SourceDocNo       = $model->DocumentNo;
                            $AdjLine->unit_price        = ($line->data->unit_price)? $line->data->unit_price : 0;

                            $Quantity                   = $line->data->Quantity;
                            
                            $AdjLine->Quantity          = $Quantity;
                            $AdjLine->QtyToMove         = abs($Quantity);
                            $AdjLine->QtyMoved          = 0;
                            $AdjLine->QtyOutstanding    = abs($Quantity);
                            $AdjLine->TypeOfDocument    = 'Journal';
                            $AdjLine->location          = ($line->data->location)? $line->data->location : $AdjLine->validateLocations();
                            $AdjLine->to_location       = $line->data->to_location;
                            $AdjLine->unit_of_measure   = $line->data->unit_of_measure;

                            $AdjLine->qty_per_unit      = $AdjLine->qtyperunit;

                            if($AdjLine->location=='Nil'){                        
                                Yii::$app->session->addFlash('error', Yii::t('common','Please create location code.'));
                                $transaction->rollBack(); 
                                return $this->redirect(['/location/location']);
                                exit();
                            }

                            if(!$AdjLine->save())
                            {
                                Yii::$app->getSession()->addFlash('warning',json_encode($AdjLine->getErrors(),JSON_UNESCAPED_UNICODE));  
                                $transaction->rollBack(); 
                                return $this->redirect(['index']);     
                            }
                        }
                            
                    }

                    $UpdateSeries       = Generater::CreateNextNumber('item_reclass_journal','DocumentNo','all',$model->DocumentNo);

                    Yii::$app->session->addFlash('success', '<i class="far fa-save"></i> '.Yii::t('common','Saved'));
                    $transaction->commit();
                    return $this->redirect(['update','id' => $model->id]);

                }else {

                    Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
                    $transaction->rollBack();
                
                }
                
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }

        } 
            
        // Init
        $model->AdjustType                  = '-';
        $model->TypeOfDocument              = 'Journal';
        if($model->isNewRecord) $model->id  = 0; 
        if($model->DocumentDate=='')$model->DocumentDate = date('Y-m-d');

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ItemReclassJournal model.
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

            
            $transaction = Yii::$app->db->beginTransaction();

            try{

                $model->PostingDate     = $model->PostingDate.' '.date('H:i:s');
                $model->user_id     = Yii::$app->user->identity->id;
                $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];

                if($model->save())
                {

                    if(isset($_POST['ItemReclassJournalLine'])){
                        $lines = self::loadLine($_POST['ItemReclassJournalLine']); 
                    
                        foreach ($lines as $key => $line) {

                            $AdjLine = ItemReclassJournalLine::findOne(['id' => $line->id]);

                            if(!isset($line->data->unit_of_measure)){
                                Yii::$app->session->setFlash('error', Yii::t('common','Some items don\'t have units.'));
                                return $this->redirect(['update', 'id' => $model->id]);
                            }

                            $AdjLine->source_id         = $model->id;
                            $AdjLine->DocumentNo        = $model->DocumentNo;                
                            $AdjLine->SourceDoc         = $model->DocumentNo; 
                            $AdjLine->SourceDocNo       = $model->DocumentNo;
                            $AdjLine->unit_price        = ($line->data->unit_price)? $line->data->unit_price : 0;

                            $Quantity                   = $line->data->Quantity;
                            
                            $AdjLine->Quantity          = $Quantity;
                            $AdjLine->QtyToMove         = abs($Quantity);
                            $AdjLine->QtyMoved          = 0;
                            $AdjLine->QtyOutstanding    = abs($Quantity);
                            $AdjLine->TypeOfDocument    = 'Journal';
                            $AdjLine->location          = ($line->data->location)? $line->data->location : $AdjLine->validateLocations();
                            $AdjLine->to_location       = $line->data->to_location;
                            $AdjLine->unit_of_measure   = $line->data->unit_of_measure;

                            $AdjLine->qty_per_unit      = $AdjLine->qtyperunit;

                            if($AdjLine->location=='Nil'){                        
                                Yii::$app->session->addFlash('error', Yii::t('common','Please create location code.'));
                                $transaction->rollBack();  
                                return $this->redirect(['/location/location']);
                                exit();
                            }

                            if(!$AdjLine->save())
                            {
                                Yii::$app->getSession()->addFlash('warning',json_encode($AdjLine->getErrors(),JSON_UNESCAPED_UNICODE));          
                                $transaction->rollBack();  
                                return $this->redirect(['index']);
                            }
                        }

                       
                    }
                    Yii::$app->session->addFlash('success', '<i class="far fa-save"></i> '.Yii::t('common','Saved'));
                    $transaction->commit();
                    return $this->redirect(['update','id' => $model->id]);

                }else{
                    Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)); 
                    $transaction->rollBack();
                }

            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } 

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ItemReclassJournal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ItemReclassJournal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemReclassJournal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemReclassJournal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }



    public function actionPrint($id){

        

        $model      = $this->findModel($id);

        $query      = ItemReclassJournalLine::find()
        ->where(['source_id' => $model->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $template      = PrintPage::findOne(PrintPage::findCustomPrint('item_reclass_journal'));
  
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
 
        return $pdf->render();
  
    }

    public function actionPostJournal($id)
    {
         
       

       // var_dump($_POST); exit();
        $transaction = Yii::$app->db->beginTransaction();

        try{
            //Create Header
            
            $sourceHeader 			= $this->findModel(base64_decode($id));
            $model 					= new WarehouseHeader();	
           
            $model->PostingDate		= date('Y-m-d H:i:s');
            $model->DocumentDate 	= date('Y-m-d');
            $model->TypeOfDocument 	= 'Journal';            
            $model->SourceDocNo 	= $sourceHeader->id;
            $model->SourceDoc 		= $sourceHeader->DocumentNo;
            $model->DocumentNo      = $sourceHeader->DocumentNo;

    
            $model->status 			= 'Transfer';

            $model->user_id 		= Yii::$app->user->identity->id;
            $model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
            $model->line_no 		= $model->comp_id.$model->user_id.$sourceHeader->id;

            if($model->save()){    

                $i = 0;
                $query = ItemReclassJournalLine::find()->where(['source_id' => base64_decode($id)])->all();                
                $len = count($query);
                foreach ($query as $source) {
                    
                    if(isset($_POST['postDate'])) $source->PostingDate =  $_POST['postDate'].' '.date('H:i:s');  
                
                    $location_source        = self::createWarehouseLine($source,$model,'source');
                    $location_destination   = self::createWarehouseLine($source,$model,'destination');                    


                    $PostLine = ItemReclassJournalLine::findOne($source->id);
                    
                    if($PostLine->delete()){
                        if ($i == $len - 1) {
                            // Remove Header At last record
                            if(ItemReclassJournal::findOne(base64_decode($id))->delete()){                                                  
                                
                                Yii::$app->getSession()->addFlash('info','<i class="far fa-save"></i> '.Yii::t('common','Posted'));
                                $transaction->commit();
                                return json_encode([
                                    'status' => 200,
                                    'message' => 'done',
                                    'value' => [
                                        'id'    => $location_destination->id,
                                        'doc'   => $location_destination->DocumentNo
                                    ]
                                ]);

                            }else {
                                
                                $transaction->rollBack();
                                Yii::$app->getSession()->addFlash('warning','Clear header Error...');
                                return $this->redirect(['update', 'id' => base64_decode($id)]);
                            }

                        }
                    }else {
                        $transaction->rollBack();
                        Yii::$app->getSession()->addFlash('warning','Clear line Error...');
                        return $this->redirect(['update', 'id' => base64_decode($id)]);  
                    }
                    
                    $i++;
                }


            }else{
                Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));    
                $transaction->rollBack();
                return $this->redirect(['update', 'id' => base64_decode($id)]);
            }


            $transaction->commit();
            Yii::$app->getSession()->addFlash('success',Yii::t('common','Posted'));    
            return $this->redirect(['index']);
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }


    }

    static function createWarehouseLine($source,$head,$cond){

        $model                  = new WarehouseMoving();
        $transaction = Yii::$app->db->beginTransaction();

        try{
            $model->PostingDate     = $source->PostingDate;
            $model->line_no         = $source->line_no;
            $model->source_id       = $head->id;
            $model->DocumentNo      = $source->DocumentNo;
            $model->TypeOfDocument  = $source->TypeOfDocument;
            $model->item            = $source->item;
            $model->ItemNo          = $source->ItemNo;
            $model->Description     = $source->Description;
            $model->SourceDocNo     = $source->DocumentNo;
            $model->SourceDoc       = $source->id;        
            $model->Quantity        = $source->Quantity * -1;                                                    
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

            $model->qty_before      = $source->items->liveInven;
            $model->qty_after       = $model->qty_before + $model->Quantity;
            

            if($cond === 'destination'){ // location destination
                $model->Quantity        = abs($source->Quantity);
                $model->location        = $source->to_location;
            }


            if(!$model->save()){
                Yii::$app->getSession()->addFlash('warning',json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));    
                $transaction->rollBack();
                return self::redirect(['update', 'id' => base64_decode($id)]);
            }else{
                // update item 
                $model->items->updateQty;
                // $item  = Items::findOne($model->item);
                // $item->last_stock = $model->qty_after;
                // $item->save(false);
            }
        
            $transaction->commit();
            return $model;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        

    }



    public function actionAjaxLine($id)
    {
        if(isset($_GET['id']))
        {
            $query   = ItemReclassJournalLine::find()->where(['source_id' => $_GET['id']]);

        }else{
            $query   = ItemReclassJournalLine::find()->where(['session_id' => Yii::$app->session->getId(),'source_id' => 0]);

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

                $model  = ItemReclassJournalLine::findOne($key);

                if(isset($_POST['name'])) $name   = $_POST['name'];
                
                $model->$name  = $_POST['val'];
  

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
        $Item       = Items::findOne(['id' => $_POST['item']]);
        $model      = new ItemReclassJournalLine();
        //ถ้าราคาเท่ากัน ให้เพิ่มจำนวน
        $price      = (@$_POST['price'])? $_POST['price'] : 1;
        $oldData    = ItemReclassJournalLine::find()->where([
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
            $model  = ItemReclassJournalLine::findOne($oldData->id);
            $model->Quantity    = $oldData->Quantity + 1;
            $model->save();

            return json_encode([
                'id'        => $model->id,
                'item'      => $Item->master_code,
                'desc'      => $model->Description,
                'inven'     => $Item->inven,
                'remain'    => $model->items->invenByLocation($model->location),
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
        $model = ItemReclassJournalLine::findOne($_POST['id']);

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
        $model = ItemReclassJournalLine::findOne($_POST['key']);
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
