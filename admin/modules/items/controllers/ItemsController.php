<?php

namespace admin\modules\items\controllers;

use Yii;
use common\models\Items;
use admin\modules\items\models\SearchItems;
use admin\modules\items\models\InStockSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\ItemPic;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use admin\modules\ItemHasProperty\models\SearchItemhas;
use admin\modules\property\models\SearchProperty;


use common\models\Property;


use yii\web\Response;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

use common\models\Unitofmeasure;
use common\models\Company;
use common\models\ItemMystore;
use admin\models\FunctionCenter;
use admin\models\FunctionItems;

use yii\imagine\Image; 
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use WebPConvert\WebPConvert;


/**
 * ItemsController implements the CRUD actions for Items model.
 */
class ItemsController extends Controller
{
    /**
     * @inheritdoc
     */
    public $company;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'delete',
                            'delete-ajax',
                            'create',
                            'create-ajax',
                            'create-ajax-multiple',
                            'delpic',
                            'update-ajax',
                            'update-alias',
                            'barcode-ajax',
                            'count-stock-ajax'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-ajax' => ['POST'],
                    'create-ajax' => ['POST'],
                    'create-ajax-multiple' => ['POST'],
                    'delpic' => ['GET'],
                    'update-ajax' => ['POST'],
                    'update-alias' => ['POST'],
                    'barcode-ajax'  => ['POST'],
                    'count-stock-ajax' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Items models.
     * @return mixed
     */
    public function actionIndexs(){
        return $this->render('_index');
    }

    public function actionIndex()
    {
        $searchModel = new SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if(isset($_GET['SearchItems'])){ // ถ้าเริ่มค้นหา
            $dataProvider->pagination->pageSize=50;
        }else{
            $dataProvider->pagination->pageSize=20;
        }
        

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReadOnly()
    {
        $searchModel = new SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['items.Status' => 1]);
        
        
        return $this->render('read-only', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProduction()
    {
        $searchModel = new SearchItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        $dataProvider->query->rightJoin('item_mystore',"item_mystore.item=items.id AND item_mystore.comp_id='".$this->company."'");
        $dataProvider->query->andWhere(['ProductionBom' => 'NULL']);

        

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewModal($id)
    {
        return $this->renderpartial('_modal_view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Displays a single Items model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViewOnly($id)
    {
        
        return $this->render('view-only', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Creates a new Items model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate()
    {
        $model                      = new Items();
        $model->No                  = Yii::$app->session->get('Rules')['comp_id'].'^';
        $model->company_id          = Yii::$app->session->get('Rules')['comp_id'];
        $model->user_id             = Yii::$app->user->identity->id;
        $model->PriceStructure_ID   = 1;
        $model->ItemGroup           = 0;
        $model->category            = 1;
        $model->product_group       = 'FG';
        $model->master_code         = '0000';
        $model->description_th      = 'NEW';
        $model->date_added          = date('Y-m-d H:i:s');
        $model->Status              = 1;
        $model->unit_of_measure     = 1; 
        $model->UnitOfMeasure       = 'PCS';

        if($model->save(false)){

            $ItemMystore                    = new ItemMystore();
            $ItemMystore->item              = $model->id;
            $ItemMystore->item_no           = $model->No;
            $ItemMystore->master_code       = $model->master_code;
            $ItemMystore->barcode           = $model->barcode;
            $ItemMystore->user_added        = Yii::$app->user->identity->id;
            $ItemMystore->comp_id           = Yii::$app->session->get('Rules')['comp_id'];
            $ItemMystore->name              = $model->description_th;
            $ItemMystore->name_en           = $model->Description;
            $ItemMystore->detail            = $model->detail;
            $ItemMystore->date_modify       = date('Y-m-d H:i:s');
            $ItemMystore->unit_cost         = ($model->UnitCost)? $model->UnitCost : $model->StandardCost;
            $ItemMystore->sale_price        = $model->CostGP;
            $ItemMystore->unit_of_measure   = $model->unit_of_measure;  
            $ItemMystore->qty_per_unit      = $model->quantity_per_unit;
            $ItemMystore->clone             = 0;

            $ItemMystore->save();

            // create measure
            self::createMeasure($model);    

            return $this->redirect(['update', 'id' => $model->id]);
        }else{
            Yii::$app->session->setFlash('warning', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
            return $this->redirect(['index']);
        }

        
    }
    public function actionCreateX()
    {

        $session = Yii::$app->session;
        $session->set('ew-attr', (object)['itemno' => NULL ]);
        $model              = new Items();
        $model->scenario    = 'create';
        $Proper             = new Property();
        $ItemMystore        = new ItemMystore();


        // Ajax
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // Post
        if ($model->load(Yii::$app->request->post())) {


            // ถ้ามี Description ตรงกับ Item ที่มีอยู่แล้ว ให้ Clone Item ไปใช้เลย
            // (จะไม่ Clone สินค้าที่มีอยู่แล้ว)
            if($clone = self::DuplicateItemCheck($model)){
                Yii::$app->session->setFlash('warning', Yii::t('common','Already exists.'));
                return $this->redirect(['view', 'id' => $clone->item]);
            }

            // ถ้าไม่ได้เลือก Item Set
            // ทำการสร้ง Item set ขึ้นมาใหม่ โดยใช้ ชื่อ item เป็น item set
            if($model->itemset == '')
            {
                $FncItems = new FunctionItems();
                $FncItems->calulateSet($model->Description);
                $session->set('itemset', $model->Description);
            }
            //  Upload FLie
            $file = UploadedFile::getInstance($model,'Item_Picture');
            if($file!= NULL){
                $model->Photo    = $model->upload($model,'Item_Picture');
                // if($file->size!=0){
                //     // $file->saveAs($new_file_path.$model->master_code.'.'.$file->extension);
                //     $new_file_path      = '../../../app-assets/images/product/'. $_POST['Items']['ItemGroup']. '/';
                //     $thumb_name         = md5($model->master_code).'_thumb.'.$file->extension;
                //     //create a new dir
                //     if (!file_exists($new_file_path)) {
                //         mkdir($new_file_path, 0775, true);
                //     }
                //     $temp_file          = $new_file_path.$model->master_code.'.'.$file->extension;
                //     if($file->saveAs($temp_file)){

                //         $imagineObj     = new Image();
                //         $imageObj       = $imagineObj->getImagine()->open($temp_file);
                //         $imageObj
                //         ->resize($imageObj->getSize()->widen(500))
                //         ->save($new_file_path.$thumb_name);
                //         $model->Photo   = $thumb_name;
                //     }                  
                // }
            }

            $company                    = Yii::$app->session->get('Rules')['comp_id'];
            $model->No                  = $company.'^'.$_POST['Items']['master_code'];
            $model->company_id          = $company;
            $model->user_id             = Yii::$app->user->identity->id;
            $model->date_added          = date('Y-m-d H:i:s');
             
            // Save
            $model->save();

            $ItemMystore->item              = $model->id;
            $ItemMystore->item_no           = $model->No;
            $ItemMystore->master_code       = $this->company.''.$model->master_code;
            $ItemMystore->barcode           = $model->barcode;
            $ItemMystore->user_added        = Yii::$app->user->identity->id;
            $ItemMystore->comp_id           = Yii::$app->session->get('Rules')['comp_id'];
            $ItemMystore->name              = $model->description_th;
            $ItemMystore->name_en           = $model->Description;
            $ItemMystore->detail            = $model->detail;
            $ItemMystore->date_modify       = date('Y-m-d H:i:s');
            $ItemMystore->unit_cost         = ($model->UnitCost)? $model->UnitCost : $model->StandardCost;
            $ItemMystore->sale_price        = $model->CostGP;
            $ItemMystore->unit_of_measure   = $model->unit_of_measure;  
            $ItemMystore->qty_per_unit      = $model->quantity_per_unit;
            $ItemMystore->clone             = 0;

            $ItemMystore->save();

            $thumbnail_name = $model->ItemGroup.'-'.$model->master_code;
            $this->UploadMultiple($model->id,'thumbnail1',UploadedFile::getInstance($model,'thumbnail1'),'t1-'.$thumbnail_name);
            $this->UploadMultiple($model->id,'thumbnail2',UploadedFile::getInstance($model,'thumbnail2'),'t2-'.$thumbnail_name);
            $this->UploadMultiple($model->id,'thumbnail3',UploadedFile::getInstance($model,'thumbnail3'),'t3-'.$thumbnail_name);
            $this->UploadMultiple($model->id,'thumbnail4',UploadedFile::getInstance($model,'thumbnail4'),'t4-'.$thumbnail_name);
            $this->UploadMultiple($model->id,'thumbnail5',UploadedFile::getInstance($model,'thumbnail5'),'t5-'.$thumbnail_name);


            //Create Measure (Dummy)
            self::createMeasure($model);    

            return $this->redirect(['view', 'id' => $model->id]);
           } else {

                     
            // Update Measure to this item
            self::updateMeasure($model);  
            
           }

        return $this->render('create', [
            'model' => $model,
            'Proper' => $Proper,
        ]);
        
    }
    
    protected function DuplicateItemCheck($model){
        
        $item = Items::find()
        ->where(['LOWER(Description)' => strtolower($model->Description)])
        ->andWhere(['LOWER(description_th)' => strtolower($model->description_th)])
        ->one();

        if($item !== null){
            // ถ้ามีอยู่แล้ว ไม่ต้องสร้างเพิ่ม
            $myItem     = ItemMystore::findOne(['item' => $item->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            if($myItem===null){
                if($item !== null){
                    $clone = self::cloneItem($item->id,['clone' => 2]);
                    
                    if($clone !== null){
                        return $clone;
                    }else{
                        return false;
                    }
                }
            }else{
                return $myItem;
            }
        }else{
            return false;
        }
       
    }

    protected function createMeasure($item){
        $measure = \common\models\Itemunitofmeasure::find()
        ->where(['session_id' => Yii::$app->session->id])
        ->andWhere(['item' => $item->id])
        ->one();
        if($measure !== null){

             // Do nothing
        }else{      

            $model =  new \common\models\Itemunitofmeasure();
            $model->measure     = 1;
            $model->qty_per_unit= 1;
            $model->item        = $item->id;
            $model->description = 'On Create';
            $model->unit_base   = 1;
            $model->session_id  = Yii::$app->session->id;     
            $model->user_id     = Yii::$app->user->identity->getId();
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            
            $model->save();

            $myItem = ItemMystore::findOne(['item' => $item->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            if($myItem===null){
                $myItem =  self::cloneItem($item->id,['clone' => 0]);
            }
            $myItem->unit_of_measure    = $model->measures->id;
            $myItem->qty_per_unit       = $model->qty_per_unit;
            $myItem->save(false);  

            if($item->owner==true){         
                $item->unit_of_measure      = $model->measures->id;
                $item->UnitOfMeasure        = $model->measures->UnitCode;
                $item->quantity_per_unit    = $model->qty_per_unit?: 1;
                $item->update(false);  
            }   
            
        }
    }

    protected function updateMeasure($item){
        
        $models = \common\models\Itemunitofmeasure::find()
                    ->where(['session_id' => Yii::$app->session->id])
                    ->andWhere(['item' => $item->id])
                    ->all();
        
        foreach ($models as $key => $model) {

            $model->item        = $item->id;
            $model->description = 'On Create';
            $model->user_id     = Yii::$app->user->identity->getId();
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            
            
            $model->save();

            // Update Item of measure
            self::updateDefaultMeasure($item);
            
        }
        
    }

  
    protected function updateDefaultMeasure($item){
        
        $model = \common\models\Itemunitofmeasure::find()
        ->where(['item' => $item->id])
        ->andWhere(['unit_base' => 1])
        ->one();

        if($model !== null){
            //$item = Items::findOne($items->id);

            $myItem = ItemMystore::findOne(['item' => $item->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            if($myItem===null){
                $myItem =  self::cloneItem($item->id,['clone' => 0]);
            }
            $myItem->unit_of_measure    = $model->measures->id;
            $myItem->qty_per_unit       = $model->qty_per_unit;
            $myItem->save();  

            if($item->owner==true){         
                $item->unit_of_measure      = $model->measures->id;
                $item->UnitOfMeasure        = $model->measures->UnitCode;
                $item->quantity_per_unit    = $model->qty_per_unit?: 1;
                $item->save();  
            }              
        } 

    }

    protected function createWhenEmptyMeasure($item){
        
        $measure    = \common\models\Itemunitofmeasure::find()
        ->where(['item' => $item->id])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if($measure->count()<=0){

            $model =  new \common\models\Itemunitofmeasure();
            $model->measure     = $item->unitofmeasures ? $item->unitofmeasures->id : 0;
            $model->qty_per_unit= $item->quantity_per_unit;
            $model->item        = $item->id;
            $model->description = 'On Update';
            $model->unit_base   = 1;
            $model->session_id  = Yii::$app->session->id;     
            $model->user_id     = Yii::$app->user->identity->getId();
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            
            if($model->save()){
                return $model;
            }else{
                return false;
            }
            
        } 
    }






    public function actionUpdate($id)
    {
        
        $session    = Yii::$app->session;
        $session->set('ew-attr', (object)['itemno' => $id ]);
        $Fnc        = new FunctionCenter();
        $model      = $this->findModel($id);
        $Proper     = new Property();
        $MyItem     = ItemMystore::find()->where(['comp_id' => $session->get('Rules')['comp_id']])->andWhere(['item' => $id]);
         
        // New module
        // Generate Unit Of Measure
        // Upload to table when update
        // 09 Apr 2018
        self::createWhenEmptyMeasure($model);


        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $myItem = $MyItem->one();
            if($myItem != null){
                $myItem->status         = $model->Status;
                $myItem->StandardCost   = $model->StandardCost;
                $myItem->CostGP         = $model->CostGP;
                $myItem->sale_price     = $model->sale_price;
                $myItem->save();
            }else{
                $myItem->clone($model,['clone' => 1]);
            }
            
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

         

        if ($model->load(Yii::$app->request->post())) {

           

            if(Yii::$app->user->identity->getId() != 1){
                if($id==1414){
                    Yii::$app->getSession()->addFlash('warning','<i class="fas fa-key"></i> '.Yii::t('common','This modification is not allowed because this item is locked')); 
                    return $this->redirect(['view', 'id' => $id]);
                    exit;
                }
            }
            // ถ้าเป็นเจ้าของ Items (สามารถแก้ไข Item กลางได้)
            if($model->owner==true){
                $model->disabled        = false;
                $file                   = UploadedFile::getInstance($model,'Item_Picture');
                // Change Group
                if(Yii::$app->request->post('Items')['CurrGroup'] != $model->ItemGroup) {
                    // Has file
                    if($file!= NULL){
                        
                        //@unlink($model->uploadPath.'/'.$model->Photo); // ยกเลิก 27/12/2019
                        $model->Photo    = $model->upload($model,'Item_Picture');
                    }else {
                        // No FIle
                        // Move File to new Group Folder
                        $old_Path = $model->upload_path.$model->CurrGroup;
                        $new_Path = $model->upload_path.$model->ItemGroup;
                        $Fnc->moveImage($model->Photo,$old_Path,$new_Path); // 24/03/2020

                        // ยกเลิก 27/12/2019 เนื่องจากลบไม่ถูกที่ เพราะไฟล์ถูกย้ายไปที่อื่น
                        // $Fnc->moveImage($model->Photo,$old_Path,$new_Path);
                        // $Fnc->moveImage($model->thumbnail1,$old_Path.'/thumbnail',$new_Path.'/thumbnail');
                        // $Fnc->moveImage($model->thumbnail2,$old_Path.'/thumbnail',$new_Path.'/thumbnail');
                        // $Fnc->moveImage($model->thumbnail3,$old_Path.'/thumbnail',$new_Path.'/thumbnail');
                        // $Fnc->moveImage($model->thumbnail4,$old_Path.'/thumbnail',$new_Path.'/thumbnail');
                        // $Fnc->moveImage($model->thumbnail5,$old_Path.'/thumbnail',$new_Path.'/thumbnail');
                    }
                }else {
                    // Not Change Group
                    // Has File
                    if($file!= NULL){
                        //@unlink($model->uploadPath.'/'.$model->Photo); // ยกเลิก 27/12/2019
                        $model->Photo    = $model->upload($model,'Item_Picture');
                    }
                    // No FIle
                    // Do nothing.
                }
           
                           
                $model->user_modify = Yii::$app->user->identity->id;
                $model->date_modify = date('Y-m-d H:i:s');
                $thumbnail_name = $model->ItemGroup.'-'.$model->master_code;
                $this->UploadMultiple($model->id,'thumbnail1',UploadedFile::getInstance($model,'thumbnail1'),'t1-'.$thumbnail_name);
                $this->UploadMultiple($model->id,'thumbnail2',UploadedFile::getInstance($model,'thumbnail2'),'t2-'.$thumbnail_name);
                $this->UploadMultiple($model->id,'thumbnail3',UploadedFile::getInstance($model,'thumbnail3'),'t3-'.$thumbnail_name);
                $this->UploadMultiple($model->id,'thumbnail4',UploadedFile::getInstance($model,'thumbnail4'),'t4-'.$thumbnail_name);
                $this->UploadMultiple($model->id,'thumbnail5',UploadedFile::getInstance($model,'thumbnail5'),'t5-'.$thumbnail_name);

                // delete from tmp_menu_group before change code
                // 
                // \common\models\Itemunitofmeasure::deleteAll(['item' => $id]);                       
                // \common\models\ItemsHasGroups::deleteAll(['item_id' => $id]);                       
                // \common\models\ItemCrossReference::deleteAll(['item' => $id]);  
                \common\models\TmpMenuGroup::deleteAll(['item' => $model->No]);  
                $model->No          = str_pad(Yii::$app->session->get('Rules')['comp_id'], 2, "0", STR_PAD_LEFT).'^'.$model->master_code;

                if(!$model->save()){
                    Yii::$app->session->setFlash('warning', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                }

 
                
            }

            if($MyItem->exists()){
                    
                $MyItemInStore                    = $MyItem->one();
                $MyItemInStore->item              = $model->id;
                $MyItemInStore->item_no           = $model->No;
                $MyItemInStore->master_code       = $model->master_code;
                $MyItemInStore->barcode           = $model->barcode;
                $MyItemInStore->user_added        = Yii::$app->user->identity->id;
                //$MyItemInStore->comp_id           = Yii::$app->session->get('Rules')['comp_id'];
                $MyItemInStore->name              = $model->description_th;
                $MyItemInStore->name_en           = $model->Description;
                $MyItemInStore->detail            = $model->detail;
                $MyItemInStore->date_modify       = date('Y-m-d H:i:s');
                $MyItemInStore->unit_cost         = ($model->UnitCost)? $model->UnitCost : $model->StandardCost;
                $MyItemInStore->sale_price        = $model->sale_price;
                $MyItemInStore->unit_of_measure   = $model->unit_of_measure;  
                $MyItemInStore->qty_per_unit      = $model->quantity_per_unit;
                $MyItemInStore->status            = $model->Status;

                if(!$MyItemInStore->update()){
                    Yii::$app->session->setFlash('warning', json_encode($MyItemInStore->getErrors(),JSON_UNESCAPED_UNICODE));
                    exit();
                }
                
            } 
            
            self::updateDefaultMeasure($model);

            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            if($MyItem->exists()){
                $MyItemInStore          = $MyItem->one();
                
                $model->Description     = ($MyItemInStore->name_en)? $MyItemInStore->name_en : $model->Description;
                $model->description_th  = ($MyItemInStore->name)? $MyItemInStore->name : $model->description_th;
                //$model->StandardCost    = ($MyItemInStore->unit_cost)? $MyItemInStore->unit_cost * 1 : $model->StandardCost * 1;
                //$model->CostGP          = ($MyItemInStore->sale_price)? $MyItemInStore->sale_price  * 1 : $model->CostGP * 1 ;

                switch ($MyItemInStore->clone) {
                    case '0': // Create / Owner
                        $model->disabled        = false;
                        break;
                    case '1': // Clone Full & Barcode
                        $model->disabled        = true;
                        break;
                    case '2': // Clone without Barcode (name only)
                        $model->disabled        = false;
                        break;
                    default:
                        $model->disabled        = true;
                        break;
                }
                
            }
            

            return $this->render('update', [
                'model' => $model,
                'Proper' => $Proper,
            ]);
        }
    }


    public function actionCloneItem($id){

        $item       = $this->findModel($id);
        $model = self::cloneItem($id,['clone' => 1]);

        if($model){
            return $this->redirect(['view', 'id' => $item->id]);
        }else{
            var_dump($model->getErrors());
        }
    }

    public function actionCloneItemJson($id){
        
        $item   = $this->findModel($id);
        
        // ถ้ามีอยู่แล้ว ไม่ต้องสร้างใหม่
        if($item->exists==true){
            return json_encode([
                'status' => 201,
                'message' => 'Already exists',                 
            ]);
        }else{
            $model  = self::cloneItem($id,['clone' => 1]);
            if($model){
                return json_encode([
                    'status' => 200,
                    'value' => [                         
                        'id' => $model->item                 
                    ]
                ]);
            }else{
                return json_encode([
                    'status' => 500,
                    'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                ]);
            }
        }
    }

    protected function cloneItem($id,$params){
        $param      = self::array_to_object($params);
        $item       = $this->findModel($id);
        $model      = new ItemMystore();

        $model->item              = $item->id;
        $model->item_no           = $item->No;
        $model->master_code       = $item->master_code;
        $model->barcode           = $item->barcode;
        $model->user_added        = Yii::$app->user->identity->id;
        $model->comp_id           = Yii::$app->session->get('Rules')['comp_id'];
        $model->name              = $item->description_th;
        $model->name_en           = $item->Description;
        $model->detail            = $item->detail;
        $model->date_added        = date('Y-m-d H:i:s');
        $model->unit_cost         = ($item->UnitCost)? $item->UnitCost : ($item->StandardCost)? $item->StandardCost : 0 ;
        $model->sale_price        = ($item->CostGP)? $item->CostGP : 0;
        $model->unit_of_measure   = $item->unit_of_measure;  
        $model->qty_per_unit      = $item->quantity_per_unit;
        $model->clone             = ($param->clone!==null)? $param->clone : 1;
        $model->size              = $item->size;

        if($model->save()){
            return $model;
        }else {
            return false;
        }
    }

    protected function array_to_object($array) {
        return (object) $array;
    }
    protected function object_to_array($object) {
        return (array) $object;
    }

    /**
     * Deletes an existing Items model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if($id==1414){
            Yii::$app->getSession()->addFlash('warning','<i class="fas fa-key"></i> '.Yii::t('common','This deletion is not allowed because this item is locked')); 
            return $this->redirect(['view', 'id' => $id]);
        }

        // if(Yii::$app->user->identity->id==1){
        //     $model = $this->findModel($id);
        //     // ถ้ามี Transection แล้วไม่ให้ลบ 
        //     if($model->transection->status){

        //         Yii::$app->session->setFlash('warning', "<i class='far fa-frown'></i> You can\'t delete this item,  <br>Because it\'s used in {$item->transection->table}");
        //         return $this->redirect(['view','id' => $id]);
                
        //     }else {

        //         $transaction = Yii::$app->db->beginTransaction();

        //         try {
        //             $Fnc            = new FunctionCenter();
        //             $ImagePath      = $model->ItemGroup;
        //             $ImageThumPath  = $model->ItemGroup.'/thumbnail';

        //             $Fnc->removeImage($ImagePath,$model->Photo);

        //             $Fnc->removeImage($ImageThumPath,$model->thumbnail1);
        //             $Fnc->removeImage($ImageThumPath,$model->thumbnail2);
        //             $Fnc->removeImage($ImageThumPath,$model->thumbnail3);
        //             $Fnc->removeImage($ImageThumPath,$model->thumbnail4);
        //             $Fnc->removeImage($ImageThumPath,$model->thumbnail5);

        //             // ลบหน่วยนับด้วย
        //             $measures = \common\models\Itemunitofmeasure::find()->where(['item' => $model->id])->all();
        //             foreach ($measures as $key => $measure) {
        //                 $measure->delete();
        //             }

        //             $model->delete();

        //             $transaction->commit(); 

        //         } catch (\Exception $e) {
        //             $transaction->rollBack();
        //             throw $e;
        //         }
        //     }
        // }else{       

            $transaction = Yii::$app->db->beginTransaction();

            try {
                // ถ้ามี Transection แล้วไม่ให้ลบ 
                $model = $this->findModel($id);
                
                    
                

                   // ถ้ามี Transection แล้วไม่ให้ลบ 
                    if($model->transection->status){

                        Yii::$app->session->setFlash('warning', "<i class='far fa-frown'></i> You can't delete this item,  <br>Because it used in {$model->transection->table}");
                        return $this->redirect(['view','id' => $id]);
                        
                    }else {
                        
                        // ลบสินค้าของตนเอง (แต่ยังไม่ลบสินค้าจากส่วนกลาง)
                        // ถ้าเป็นสินค้าที่สร้างขึ้นเอง และ ยังไม่มีการดึงไปใช้งาน ให้ลบจากส่วนกลางด้วย
                        ItemMystore::deleteAll(['item' => $id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                        $Usage    = ItemMystore::find()->where(['item'=> $id])->count(); // นับดูว่ายังมีเหลืออยู่ไหม

                        // ถ้าไม่มี(แสดงว่า ไม่มีใครดึงไปใช้)  ให้ลบได้ 
                        if($Usage <= 0){        
                            
                            if(\common\models\BomHeader::find()->where(['item' => $model->id])->exists()){
                                Yii::$app->session->setFlash('warning', Yii::t('common','Please delete Bom before delete this item.'));
                                $transaction->rollBack();
                                return $this->redirect(['view', 'id' => $model->id]);
                            }else{
                                
                                // ลบหน่วยนับด้วย
                                \common\models\Itemunitofmeasure::deleteAll(['item' => $id]);                       
                                \common\models\ItemsHasGroups::deleteAll(['item_id' => $id]);                       
                                \common\models\ItemCrossReference::deleteAll(['item' => $id]);  
                                \common\models\TmpMenuGroup::deleteAll(['item' => $model->No]);  
                                //\common\models\KitbomLine::deleteAll(['item_no' => $model->No]);  ยังลบไม่ได้ เพราะจะทำให้ลบสินค้าที่ code เดียวกันด้วย เนื่องจากไม่ได้ใช้ ID                       
                                
                                
                                if(!$model->delete()){
                                    Yii::$app->session->setFlash('warning', json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE));
                                    $transaction->rollBack();
                                    return $this->redirect(['view', 'id' => $model->id]);
                                }else{
                                    @unlink($model->uploadPath.'/'.$model->Photo);
                                };
                            }
                             
                            Yii::$app->session->setFlash('success', '<i class="fas fa-thumbs-up"></i> '.Yii::t('common','Success'));
                            $transaction->commit();   
                            return $this->redirect(['index']);
                        }else{
                            Yii::$app->session->setFlash('warning', '<i class="far fa-frown"></i> เฉพาะเจ้าของสินค้าที่สามารถลบได้');
                            $transaction->commit();   
                            return $this->redirect(['index']);
                        }
                     

                }
               
                

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('warning', Yii::t('common','{:e}', [':e' => $e]));
                throw $e;
            }
        //}
        return $this->redirect(['index']);
    }


    public function actionDeleteAjax()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $id             = $data->id;

        if($id==1414){
            $message    = Yii::t('common','This deletion is not allowed because this item is locked');
            $status     = 304;
        }

        $model          = $this->findModel($id);
        // ถ้ามี Transection แล้วไม่ให้ลบ 
        if($model->transection->status){

            $message    = Yii::t('common',"You can't delete this item, Because it used in {:table}",[':table' => $model->transection->table]);
            $status     = 500;   
                
        }else {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                
                
                //$item = $this->findModel($id);

                    $ItemMystore = ItemMystore::find()
                    ->where(['item' => $model->id])
                    ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->one();

                    if($ItemMystore !== null){                    
                        // ลบสินค้าของตนเอง (แต่ยังไม่ลบสินค้าจากส่วนกลาง)
                        ItemMystore::deleteAll(['item' => $model->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
                        // ถ้าเป็นสินค้าที่สร้างขึ้นเอง และ ยังไม่มีการดึงไปใช้งาน ให้ลบจากส่วนกลางด้วย
                        $Usage    = ItemMystore::find()->where(['item'=> $model->id])->count(); // นับดูว่ายังมีเหลืออยู่ไหม
                        // ถ้าไม่มี(แสดงว่า ไม่มีใครดึงไปใช้)  ให้ลบได้ 
                        if($Usage <= 0){                                    
                            // ถ้ามี Transection แล้วไม่ให้ลบ 
                            if($model->transection->status){
                                $message    = Yii::t('common',"You can't delete this item, Because it used in {:table}",[':table' => $model->transection->table]);
                                $status     = 500;                                
                            }else {
                                // ตรวจดูว่าเป็นสินค้าที่สร้างขึ้นเองหรือไม่ 
                                $model      = Items::find()
                                ->where(['id' => $id])
                                ->andWhere(['company_id' => Yii::$app->session->get('Rules')['comp_id']])
                                ->one();
                                if($model !== null){
                                    @unlink($model->uploadPath.'/'.$model->Photo);
                                    // ลบหน่วยนับด้วย
                                    $measures = \common\models\Itemunitofmeasure::find()
                                    ->where(['item' => $model->id])
                                    ->all();
                                    foreach ($measures as $key => $measure) {
                                        $measure->delete(false);
                                    }                                    
                                    if(!$model->delete()){
                                        $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                                        $status     = 500;
                                        $transaction->rollBack();
                                    };
                                }                            
                            }
                            $message = Yii::t('common','Success');
                            $status  = 200;
                        }else{
                            $message = Yii::t('common','Success');
                            $status  = 200;    
                        }
                        $transaction->commit();                                       
                    }else{
                        $message = Yii::t('common','เฉพาะเจ้าของสินค้าที่สามารถลบได้');
                        $status  = 403;
                        $transaction->rollBack();
                    }            

            } catch (\Exception $e) {
                $message = json_encode($e,JSON_UNESCAPED_UNICODE);
                $status  = 500;
                $transaction->rollBack();            
                throw $e;
            }   
        }     

        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }



    public function actionDelpic($id)
    {

        $model = $this->findModel($id);

        $picname = $_GET['pic'];

        $model->$picname = '';

        $model->save();

    }

    /**
     * Finds the Items model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Items the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = Items::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        
    }


    public function UploadMultiple($id,$fieldPic,$file,$Name){

        if($file!= NULL){
            if($file->size!=0){
                $new_file_path      = '../../../app-assets/images/product/'. $_POST['Items']['ItemGroup']. '/thumbnail/';
                //create a new dir
                if (!file_exists($new_file_path)) {
                    mkdir($new_file_path, 0775, true);
                }

                if($file->saveAs($new_file_path.$Name.'.'.$file->extension)){
                    $update_qty = "UPDATE items SET $fieldPic = '".$Name.".".$file->extension."' WHERE id ='".$id."' ";
                    Yii::$app->db->createCommand($update_qty)->execute();
                }
            }
            //return $Name.'.'.$file->extension;
        }

    }

    public function getMyitem($company)
    {


        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item_no;
            }

            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionStock(){
        $searchModel = new InStockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=120;


        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        if(Yii::$app->user->identity->id!=1){
            //$dataProvider->query->andWhere(['No'=> $this->getMyitem($this->company)]);
            $dataProvider->query->rightJoin('item_mystore',"item_mystore.item=items.id AND item_mystore.comp_id='".$this->company."'");
        }

        return $this->render('stock', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
  
    }

    public function actionList(){

        return $this->render('list');
    }

    public function actionCountStockAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);


        $query  = Items::find()->where(['<>','id','1414'])->all();
        $raws   = []; 

        foreach ($query as $key => $model) {
            $raws[]   = [
                'id' => $model->id,
                'stock' => $model->countStock->last_stock,
            ]; 
        }

        return json_encode([
            'status' => 200,
            'raws'      => $raws
        ]);
    }
    public function actionListAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);


        $query  = ItemMystore::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['<>','item','1414'])
                    ->andWhere(['status' => 1])
                    ->limit(isset($data->limit) ? $data->limit : null)
                    ->offset(isset($data->offset) ? $data->offset : null)
                    ->orderBy(['name' => SORT_ASC])
                    ->all();
        $raws   = []; 

        if($query != null){
            foreach ($query as $key => $model) {
                $raws[] = [
                    'id'    => $model->items->id,
                    'name'  => $model->name,
                    'detail'=> $model->detail,
                    'code'  => $model->items->master_code,
                    'img'   => $model->picture,
                    //'stock' => $model->items->qtyAfter,
                    'stock' => $model->items->ProductionBom > 0
                                ? $model->items->myItems->last_stock
                                : $model->items->myItems->last_possible,
                    'size'  => $model->size,
                    'unit'  => $model->measures ? $model->measures->id  : ' ',
                    'measure' => $model->measures ? $model->measures->UnitCode  : ' '
                ];
            }
        }
        return json_encode([
            'raw'       => $raws,
            'status'    => 200
        ]);
    }

    public function actionUpdateAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp           = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $status         = 200;
        $message        = 'Done';

        $measure        = Unitofmeasure::findOne($data->unit);
        if($measure != null){
            $data->unit = $measure->UnitCode;
        }else{
            $measure    = Unitofmeasure::find()->where(['UnitCode' => $data->unit])->one();
            if($measure != null){
                $data->unit = $measure->UnitCode;
            }else{
                $measure                = new Unitofmeasure();
                $measure->UnitCode      = $data->unit;
                $measure->Description   = $data->unit;
                $measure->comp_id       = $comp->id;
                $measure->save();
            }
            $data->unit = $measure->UnitCode;
        }  
        
        $model                              = Items::findOne($data->id);
        $MyItemInStore                      = ItemMystore::find()->where(['item' => $model->id, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->one();

        if($MyItemInStore != null){ // ถ้ามีใน Store แล้ว

            $MyItemInStore->item            = $data->id;
            //$MyItemInStore->item_no         = $model->No;
            $MyItemInStore->master_code     = $data->code;
            $MyItemInStore->barcode         = $model->barcode;
            $MyItemInStore->user_added      = Yii::$app->user->identity->id;
            $MyItemInStore->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $MyItemInStore->name            = $data->name;
            $MyItemInStore->name_en         = $model->Description;
            $MyItemInStore->detail          = $data->detail;
            $MyItemInStore->date_modify     = date('Y-m-d H:i:s');
            $MyItemInStore->unit_of_measure = $measure->id;
            $MyItemInStore->qty_per_unit    = $model->quantity_per_unit;
            $MyItemInStore->size            = $data->size;

             // Save file --->
             if($data->imgchange){
                 if(isset($data->img)){
                    $img = $data->img;

                    list($type, $img)   = explode(';', $img);
                    list(, $img)        = explode(',', $img);
                    $sourceImage        = base64_decode($img);
                    $name               = $MyItemInStore->master_code.'-'.date('y-m-d_h_i_s');

                    file_put_contents('../../../app-assets/images/product/'.$name.self::fileType($type), $sourceImage);
                    $MyItemInStore->Photo = $name.self::fileType($type);
                }
            }
            // <--- Save file         

            $MyItemInStore->update();
            
            
            // $model->No                  = $company.'^'.$data->code;
            $model->Description         = $data->name;
            $model->description_th      = $data->name;
            $model->detail              = $data->detail;
            $model->unit_of_measure     = $measure->id;
            $model->UnitOfMeasure       = $measure->UnitCode;
            $model->master_code         = $data->code; 
            $model->size                = $data->size;  
            
            if($model->save()){

                $data->id   = $model->id;
                $data->code = $model->master_code;
                $data->stock= $model->inven;   
                $data->size = $MyItemInStore->size;            
                
                   

                 
                $status = 200;                  
                
            
                
            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
            
        }else {

                
        }

        
        $data->img      = $model->picture;

        return json_encode([
            'status'    => $status,
            'raw'       => $data,
            'message'   => $message
        ]);
    }

    public function actionCreateAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $comp           = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $status         = 200;
        $message        = 'Done';

        $isExists       = Items::findOne(['master_code' => $data->code, 'company_id' => Yii::$app->session->get('Rules')['comp_id']]);
        if($isExists != null){
            return json_encode([
                'status'    => 403,                
                'message'   => Yii::t('common','Code "{name}" already exists.', ['name' => $data->code])
            ]);
            exit;
        }

        $measure        = Unitofmeasure::findOne($data->unit);
        if($measure != null){
            $data->unit     = $measure->UnitCode;
        }else{
            $measure        = Unitofmeasure::find()->where(['UnitCode' => $data->unit])->one();
            if($measure != null){
                $data->unit     = $measure->UnitCode;
            }else{
                $measure                = new Unitofmeasure();
                $measure->UnitCode      = $data->unit;
                $measure->Description   = $data->unit;
                $measure->comp_id       = $comp->id;
                $measure->save();
            }
            $data->unit     = $measure->UnitCode;
        }
        
        
        $genCode                    = $data->code 
                                        ? $data->code 
                                        : str_pad($comp->id, 2, "0", STR_PAD_LEFT).'-'.$comp->acronym.'-'.self::lastRuning(str_pad($comp->id, 2, "0", STR_PAD_LEFT).'-'.$comp->acronym);

        $model                      = new Items();
        
        $model->No                  = $company.'^'.$genCode;
        $model->company_id          = $comp->id;
        $model->user_id             = Yii::$app->user->identity->id;
        $model->date_added          = date('Y-m-d H:i:s');
        $model->Description         = $data->detail;
        $model->description_th      = $data->name;
        $model->unit_of_measure     = $measure->id;
        $model->UnitOfMeasure       = $measure->UnitCode;
        $model->master_code         = $genCode;
        $model->ItemGroup           = 1;
        $model->category            = 1;
        $model->PriceStructure_ID   = 1;
        $model->product_group       = 'FG';
        $model->interesting         = 0;
        $model->Status              = 1;
        $model->replenishment       = 'Purchase';       
        $model->size                = $data->size;  
        $model->name                = $data->size;
        $model->quantity_per_unit   = 1;
         
        if($model->save()){

            $data->id   = $model->id;
            $data->code = $model->master_code;
            $data->stock= 0;

            $clone = self::cloneItem($model->id,['clone' => 2]);

            if($clone){
                // Save file --->
                if($data->img){
                    $img = $data->img;

                    list($type, $img)   = explode(';', $img);
                    list(, $img)        = explode(',', $img);
                    $sourceImage        = base64_decode($img);

                    file_put_contents('../../../app-assets/images/product/1/'.$model->master_code.self::fileType($type), $sourceImage);
                    $model->Photo = $model->master_code.self::fileType($type);

                    $model->save();
                }
                // <--- Save file 

                

                if(self::createWhenEmptyMeasure($model)){
                    $status = 200;                  
                }else{
                    $status = 500;
                }
            }else{
                $status = 500;
            }
            
        }else{
            $status     = 500;
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }
        

        
        $data->img      = $model->picture;

        return json_encode([
            'status'    => $status,
            'raw'       => $data,
            'message'   => $message
        ]);
    }

    public function actionCreateAjaxMultiple(){
        $request_body   = file_get_contents('php://input');
        $source         = json_decode($request_body);
        $comp           = Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
        $company        = Yii::$app->session->get('Rules')['comp_id'];
        $status         = 200;
        $message        = 'Done';
        $data           = [];
        $raw            = [];

        foreach ($source as $key => $data) {

            $measure        = Unitofmeasure::findOne($data->unit);

            if($measure == null){ 
                $measure        = Unitofmeasure::find()->where(['UnitCode' => $data->unit])->one();
            }

            $data->unit     = $measure->UnitCode;
            $data->measure  = $measure->id;
                     
            $genCode            = $data->code 
                                    ? $data->code 
                                    : str_pad($comp->id, 2, "0", STR_PAD_LEFT).'-'.$comp->acronym.'-'.self::lastRuning(str_pad($comp->id, 2, "0", STR_PAD_LEFT).'-'.$comp->acronym);

            // Check already in my store
            $InStore    = ItemMystore::find()->where(['name' => $data->name])->andWhere(['detail' => $data->detail])->andWhere(['size' => $data->size])->one();
            if($InStore != null){

                $raw[]      = [
                    'code'      => $InStore->master_code,
                    'name'      => $data->name,
                    'detail'    => $data->detail,
                    'size'      => $data->size,
                    'id'        => $InStore->item,
                    'img'       => $InStore->items->picture,
                    'stock'     => $InStore->items->inven,
                    'unit'      => $data->unit,
                    'measure'   => $data->measure,
                    'already'   => true
                ];

            }else {

                // Check already in center item
                $model   = Items::find()
                                    ->where(['description_th' => $data->name])
                                    ->andWhere(['detail' => $data->detail])
                                    ->andWhere(['size' => $data->size])
                                    ->one();

                if($model != null){

                    $clone = self::cloneItem($model->id,['clone' => 1]);

                }else {

                    $model                      = new Items();
                    
                    $model->No                  = $company.'^'.$genCode;
                    $model->company_id          = $comp->id;
                    $model->user_id             = Yii::$app->user->identity->id;
                    $model->date_added          = date('Y-m-d H:i:s');
                    $model->Description         = '';
                    $model->description_th      = $data->name;
                    $model->detail              = $data->detail;
                    $model->size                = $data->size;
                    $model->unit_of_measure     = $measure->id;
                    $model->UnitOfMeasure       = $measure->UnitCode;
                    $model->master_code         = $genCode;
                    $model->ItemGroup           = 1;
                    $model->category            = 1;
                    $model->PriceStructure_ID   = 1;
                    $model->product_group       = 'FG';
                    $model->interesting         = 0;
                    $model->Status              = 1;
                    $model->replenishment       = 'Purchase';
                    
                    
                    if($model->save()){

                        $data->id   = $model->id;
                        $data->code = $model->master_code;
                        $data->stock= 0;

                        $clone = self::cloneItem($model->id,['clone' => 2]);

                        if($clone){
                            if(self::createWhenEmptyMeasure($model)){
                                $status = 200;                  
                            }else{
                                $status = 500;
                            }
                        }else{
                            $status = 500;
                        }
                        
                    }else{
                        $status     = 500;
                        $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                    }
                    
                    $data->img      = $model->picture;
                }

                $raw[]      = [
                    'code'      => $model->master_code,
                    'name'      => $data->name,
                    'detail'    => $data->detail,
                    'size'      => $data->size,
                    'id'        => $model->id,
                    'img'       => $model->picture,
                    'stock'     => $model->inven,
                    'unit'      => $data->unit,
                    'measure'   => $data->measure,
                    'already'   => false
                ];
            } 

        }

        return json_encode([
            'status'    => $status,
            'data'      => $data,
            'message'   => $message,
            'raw'       => $raw
        ]);
    }

    static function fileType($type){
        switch ($type) {
            case 'data:image/jpeg':
                return '.jpg';
                break;

            case 'data:image/png':
                return '.png';
                break;
            
            default:
                return '.jpg';
                break;
        } 
    }

    static function lastRuning($text){        
       
        $model  = ItemMystore::find()                
                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['like', 'master_code', $text])
                ->orderBy(['master_code' => SORT_DESC])
                ->one();

        $digit =  str_pad(1, 4, "0", STR_PAD_LEFT);

        if($model != null){
            $str    = explode('-', $model->master_code);
            $arr    = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$str[2]);

            $digit =  str_pad($arr[0] + 1, 4, "0", STR_PAD_LEFT);
             
        } 

        return $digit;
    }

    public function actionNew(){
        return $this->render('new');
    }


    public function actionUpdateAlias(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = 'Done';

        $Item   = Items::findOne($data->id);
        $field = $data->field;
        $Item->$field = $data->val;

        if(!$Item->save()){
            $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            $status     = 500; 
        }
        // $myItem = ItemMystore::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->andWhere(['id' => $Item->id])->one();

        // if($myItem!=null){
        //     $myItem->name = $data->val;
        //     $myItem->update();
        // }
        

        return json_encode([
            'status'    => $status,
            'message'   => $message
        ]);
    }


    public function actionBarcodePrint(){

        return $this->render('barcode-print');
    }

    public function actionBarcodePrintAll(){

        return $this->render('barcode-print-all');
        /*
        $query = \common\models\ViewBarcodeRef::find();
        
        $this->company = Yii::$app->session->get('Rules')['comp_id'];
        
            
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,

        ]);

        return $this->render('barcode-print-all', [
            //'searchModel' => $query,
            'dataProvider' => $dataProvider,
        ]);
        */
    }

    public function actionBarcodeAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = 'Done';
        $raws           = [];

        $query = \common\models\ViewBarcodeRef::find();
        
        $code           = isset($data->code) ? strtolower($data->code) : '';

        if($code != ''){
            $query->where(['or', 
                    ['like','barcode',explode(" ",$code)],
                    ['like','ref_barcode',explode(" ",$code)],
                    ['like','ref_code',explode(" ",$code)],
                    ['like','ref_name',explode(" ",$code)],
                    ['like','master_code',explode(" ",$code)],
                    ['like','description_th',explode(" ",$code)]
            ]);
        }else{
            $query->limit(20);
        }
         
        foreach ($query->all() as $key => $model) {
            $raws[] = (Object)[
                'id'        => $model->id,
                'code'      => $model->master_code,
                'barcode'   => $model->barcode ? $model->barcode : '',
                'barcode_ref' => $model->ref_barcode,
                'article'   => $model->ref_code ? $model->ref_code : '',
                'name'      => $model->description_th,
                'name_ref'  => $model->ref_name ? $model->ref_name : '',
                'photo'     => isset($data->code) ? $model->picture : '',
                'stock'     => $model->ProductionBom > 0 
                                    ? ($model->myItems ? $model->myItems->last_possible * 1 : 0)
                                    : ($model->myItems ? $model->myItems->last_stock * 1 : 0),
                'cust'      => $model->customer_id,
                'cust_name' => $model->customers 
                                    ? $model->customers->name
                                    : ''
            ];
        }
        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'raws'      => $raws
        ]);

    }
}
