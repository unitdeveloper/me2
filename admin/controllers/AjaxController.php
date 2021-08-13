<?php

namespace admin\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;



use common\models\Register;
use common\models\Items;
use common\models\Itemgroup;
use common\models\PropertyHasGroup;
use admin\models\FunctionCenter;

use yii\web\UploadedFile;
use common\models\Profile;
use common\models\SaleHeader;

use yii\helpers\Json;
use common\models\Province;
use common\models\Amphur;
use common\models\District;
use common\models\Zipcode;

use yii\helpers\ArrayHelper;

use kartik\widgets\DepDrop;

use admin\modules\Itemset\models\FunctionItemset;

use admin\modules\apps_rules\models\SysRuleModels;


class AjaxController extends \yii\web\Controller
{
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
                        'actions' => ['count-menu'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'change-theme' => ['POST'],
                    'change-sale-people' => ['POST'],
                    'set-favorite-menu' => ['POST'],
                    'get-province-list' => ['GET']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCropImg()
    {
        $model = Profile::find()->where(['user_id' => $_POST['param']['user_id']])->one();
        //var_dump($_POST['param']);
        $data = $_POST['param']['photo'];

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        if($type == 'data:image/jpeg'){
            $File_ext = 'jpg';
        }else if($type == 'data:image/png'){
            $File_ext = 'png';
        }else {
            $File_ext = 'jpg';
        }


        $fileName = $_POST['param']['user_id'].'.'.$File_ext;

        file_put_contents('uploads/'.$fileName, $data);

        $model->avatar = $fileName;
        $model->save();
        //  Upload FLie

        // $file = UploadedFile::getInstance($_POST['param'],'photo');
        // if($file!= NULL){
        //     if($file->size!=0){



        //         $new_file_path      = 'uploads';

        //         //create a new dir
        //         if (!file_exists($new_file_path)) {

        //             mkdir($new_file_path, 0777, true);
        //         }

        //         $file->saveAs($new_file_path.'/'.$_POST['param']['photo'].'.'.$file->extension);

        //     }

        // }
    }

    public function actionSwitch_menu()
    {

        $session = Yii::$app->session;
        //$session->set('collapse', 'sidebar-collapse sidebar-mini');
        $collapse = $session->get('collapse');

        if($collapse == 'sidebar-collapse'){
            $session->set('collapse', '');
        }else {
            $session->set('collapse', 'sidebar-collapse');
        }
        //echo $collapse;
    }

    public function actionSwitchOn()
    {

        $session = Yii::$app->session;

        $collapse = $session->get('collapse');

        if($collapse == '')
        {
            $session->set('collapse', 'sidebar-collapse');
        }
        //echo $collapse;
    }

    public function actionItemGroupChild()
    {
        $session = Yii::$app->session;
        $ew_attr = $session->get('ew-attr');
        // ถ้าสร้าง item ใหม่ ไม่ต้องแสดงปุ่ม Cancel
        if($ew_attr->itemno!=NULL)
        {
            $edit = '<a href="index.php?r=items/items/update&id='.$session->get('ew-attr')->itemno.'" class="btn btn-danger pull-right">Cancel</a>  ';
        }else {
            $edit = NULL;
        }
        $model = Itemgroup::find()->where(['Child' => $_POST['param']['id']])->all();
        $div = '<div class="panel panel-info ew-content">
                    '.$edit.'
                    <div class="panel-heading" id="ew-panel-header" ew-data="1">
                        '.Yii::t('common','Electronic Part').'
                    </div>
                    <div class="panel-body ew-panel-body">';
        $div.= '        <div data-toggle="">
                            <ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {
            $count = Itemgroup::find()->where(['Child' => $value->GroupID])->count();
            if($count>0){
                    $div.= '    <li class="col-sm-3">
                                    <label class="text-light-blue" style="display: block; width: 100%; text-align:left;">'.$value->Description_th.'</label>
                                    <ul class="col-sm-12">'.$this->FindChild($value->GroupID,$value->Description_th).'</ul>
                                </li>';
                }else {
                    $div.= '    <li class="col-sm-3">
                                    <label class="text-muted ew-radio ew-selected" ew-radio-data="'.$value->GroupID.'" ew-desc="' .Yii::t('common',trim($value->Description_th)). '">
                                        <input type="radio" name="ItemGroup" class="ew-radio" ew-radio-data="'.$value->GroupID.'"> '.Yii::t('common',trim($value->Description_th)).'
                                    </label>
                                </li>';
                }
        }
        $div.= '            </ul>
                        </div>
                    </div>
                </div>';
        return $div;
    }


    public function FindChild($id,$Desc)
    {
        $model = Itemgroup::find()->where(['Child' => $id])->all();

        $div = '<ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {
            $count = Itemgroup::find()->where(['Child' => $value->GroupID])->count();

            if($count>0){
                    $div.= '<li class="col-sm-12">
                                <label class="text-aqua" style="display: block; width: 100%; text-align:left;"><b> '.$value->Description_th.'</b></label>
                            ';
                    $div.= '<ul class="col-sm-12">'.$this->FindChild($value->GroupID,$Desc.' > '.$value->Description_th).'</ul></li>';
                }else {
                    $div.= '<li class="col-sm-12">
                                <label class="text-muted ew-radio ew-selected" ew-radio-data="'.$value->GroupID.'" ew-desc="'.$Desc. ' > '.$value->Description_th.'">
                                <input type="radio" name="ItemGroup" > '.$value->Description_th.'</label>
                            </li>';
                }
        }
        $div.= '</ul>';
        return $div;
    }


    public function actionItemGroupChildSub()
    {
        $model = Itemgroup::find()->where(['Child' => $_POST['param']['id']])->all();

        $div = '<div data-toggle="buttons">
                    <ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {

            $count = Itemgroup::find()->where(['Child' => $value->GroupID])->count();

            if($count>0){
                    $div.= '<li>
                                <label class="btn btn-primary" style="display: block; width: 100%; text-align:left;">'.$value->Description.'</label>
                            </li>';
                    $div.= '<li>'.$this->FindChildSub($value->GroupID).'</li>';
                }else {
                    $div.= '<li>
                        <button class="btn btn-default" style="display: block; width: 100%;">
                            <input type="radio" name="ItemGroup" class="ew-radio" ew-radio-data="'.$value->GroupID.'"> '.$value->Description.'</button>
                    </li>';
                }


        }
        $div.= '    </ul>
                </div>';
        return $div;
    }


    public function FindChildSub($id)
    {
        $model = Itemgroup::find()->where(['Child' => $id])->all();

        $div = '<ul class="ew-ul-itemgroup">';
        foreach ($model as $value) {
            $count = Itemgroup::find()->where(['Child' => $value->GroupID])->count();

            if($count>0){
                    $div.= '<li>
                                <label class="btn btn-primary" style="display: block; width: 100%; text-align:left;"><b>'.$value->Description.'</b></label>
                            </li>';
                    $div.= '<li>'.$this->FindChildSub($value->GroupID).'</li>';
                }else {
                    $div.= '<li>
                                <button class="btn btn-default ew-radio" ew-radio-data="'.$value->GroupID.'" style="display: block; width: 100%; text-align:left;">
                                <input type="radio" name="ItemGroup" > '.$value->Description.'</button>
                            </li>';
                }
        }
        $div.= '</ul>';
        return $div;
    }

    public function actionProperty()
    {
    	echo '0';
    }

    public function actionMycart()
    {
    	return NULL;
    }

    protected function getAlert($id)
    {
        // Count Register
        // $CountRegister =  Register::find()->where(['status' => 'pending'])->count();

        // if($CountRegister > 0){
        //     return $CountRegister;
        // }else {
        //     return NULL;
        // }
        $query 			= \common\models\ViewRcInvoice::find()
                        ->select('no_,count(no_) as dup')
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->andWhere(['between','DATE(posting_date)', date('Y-01-01'), date('Y-m-t')])
                        ->groupBy('no_')
                        ->having(['>','count(no_)', 1]);
        foreach ($query->all() as $key => $model) {
            $raws[] = (Object)[
                'no' 		=> $model->no_,
                'dup'		=> $model->dup,
            ];
        }

        return (Object)[
            'count' => $query->count(),
            'raws' => $raws
        ];
    }

    protected function getTask($id)
    {
        return NULL;
    }

    protected function getMessage($id)
    {
        

    }

    public function actionCheckbarcode($id)
    {
        $model = Items::find()->where(['barcode' => $id])->all();
        $count =  Items::find()->where(['barcode' => $id])->count();
        // if($count == 1)
        // {
        //     //return $this->redirect(['items/items/cupdate','id'=> Items::find()->where(['barcode' => $id])->one()->No]);
        // }else
        if($count >= 1){
            $data = '<div class="modal-header">Barcode นี้ มีอยู่แล้ว กรุณาเลือกรายการที่ต้องการบันทึก </div>';
            $data.= '<table class="table">';
            $data.=     '<tr>

                            <th>Barcode</th>
                            <th>Detail</th>
                            <th> </th>
                        </tr>';

            foreach ($model as $value) {
                $data.= '<tr>';
                $data.= '   <td>
                                <img src="'.$value->picture.'"
                                class="img-thumbnail" style="height:150px;">
                            </td>';
                $data.= '   <td >';
                $data.= '       <div>'.$value->barcode.'</div>
                                <a href="index.php?r=items/items/clone-item&id='.$value->id.'">'.$value->Description.'</a>';
                $data.= '   </td>'."\r\n";
                $data.= '<td class="text-right"><a href="index.php?r=items/items/clone-item&id='.$value->id.'"><span class="btn btn-info"> เลือก</span></a></td>';
                $data.= '</tr>';
            }
            $data.= '</table> ';
            $data.= '<div class="modal-footer">
                        <button type="button" class="btn btn-default-ew" data-dismiss="modal"><i class="fas fa-power-off"></i> '.Yii::t('common','Close').'</button>
                    </div>';

            return $data;
        }else {
            return '<div class="modal-header bg-aqua">
                        <div class="modal-title">Barcode "ว่าง!" ยังไม่ลงทะเบียนในระบบ </div>
                    </div>
                    <div class="modal-body">สามารถใช้ Barcode นี้ได้</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info-ew use-barcode" data-dismiss="modal"><i class="far fa-check-square"></i>  ใช้บาร์โค๊ดนี้</button>
                    </div>
                    ';
        }



    }

  
    
    // Sale Order [Alert]
    public function actionCountMenu(){
        $request_body   = file_get_contents('php://input');
        $body           = json_decode($request_body);

        $newJob         = 0;
        $data           = null;
        $raws 			= [];
        if(Yii::$app->session->get('Rules')['rules_id'] == 4 || Yii::$app->session->get('Rules')['rules_id'] == 1){ // 4=Sale admin
        
            $countModel = SaleHeader::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->andWhere(['status' => 'Release'])
            ->andWhere(['>','balance',0])
            ->count();
            if($countModel > 0){
                $data   = '<span class="label label-warning"><span class="blink">'.$countModel.'</span></span>';
                $newJob = $countModel;
            }else {
                $data   = NULL;
            }

           
        }else if(Yii::$app->session->get('Rules')['rules_id'] == 3 ){ // 3=Sale, 4=Sale admin

            $countReject = SaleHeader::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->andWhere(['status' => 'Reject'])
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->count();
            if($countReject > 0)
            {
                $data   = '<span class="label label-warning"><span class="blink">'.$countReject.'</span></span>';
                $newJob = $countReject;
            }else {
                $data   = NULL;
            }

        }else {
            $data = NULL;
        }

        $Approve = $this->getCountMenuApprove();


        $alert      = self::getAlert(0);
        
        return json_encode([
            //'saleorder' => $data,
            //'approve'   => $Approve->html,
            'appData'   => $Approve->data,
            'newjob'    => $newJob,
            'alert'     => $alert->count,
            'alertRaws' => $alert->raws,
            'task'      => self::getTask(0),
            'message'   => self::getMessage(0)       
        ]);
    }

    // Sale Order [Alert]
    public function getCountMenuApprove()
    {
        $data = null;
        $html = '';
        if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','Finance','Approve','Cheque','approve-click'))){

            $Approve   =  \common\models\Approval::find()->select('source_id')->where(['comp_id'=>Yii::$app->session->get('Rules')['comp_id']])->all();
            $appId  = array();
            foreach ($Approve as $key => $value) {
                $appId[] = ''.$value->source_id.'';
            }

            $countModel = \common\models\Cheque::find()
            ->select('source_id')
            ->where(['NOT IN','id',$appId])
            ->andWhere(['comp_id'=>Yii::$app->session->get('Rules')['comp_id']])
            ->groupBy('source_id')->count();

            if($countModel > 0){
                $html = '<span class="label label-danger"><span class="blink">'.$countModel.'</span></span>';
                $data = $countModel;
            }else {
                $data = NULL;
            }

        }else {
            $data = NULL;
        }
        return (Object)[
            'data' => $data,
            'html' => $html
        ];
    }

    public function actionPiccustomer()
    {
        $model = Customer::find()
                ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['id' => $_GET['model']['id']])
                ->one();
        return $model;
    }

    public function actionJsonLoadProperty()
    {
        $id     = $_GET['param']['id'];
        $Fnc    = new FunctionCenter();
        $data   = $Fnc->JsonLoadChild($id);
        $data.= $Fnc->JsonGetGroup($id);
        return $data;
    }

    /*
    * Load Item Property
    */
    public function actionJsonLoad()
    {
        $id = $_GET['param']['id'];
        $Fnc = new FunctionCenter();
        $data = $Fnc->JsonLoadChild($id);
        $data.= $Fnc->JsonGetGroup($id);
        return $data;
    }
//update items set Description = replace(Description, ' ', ' ');
//CHONG-2 2P63A 1P16A/2 20A/2 32A/2


    public function actionJsonValidateItem()
    {

        $data       = [];
        $company    = Yii::$app->session->get('Rules')['comp_id'];

        // Possible Count
        //$JSON 	    = json_decode(Yii::$app->request->post('param')['post'], true); // Disabled -> 29/06/2020
        $JSON 	    = json_decode(Yii::$app->request->post('param')['itemList'], true); // 29/06/2020
        $dataCount  = [];
        foreach ($JSON as $key => $data) {
            // $res 	= FunctionItemset::countBomInItems($data['c'],$data['q']);
            // $dataCount[]= $res->value;
            // $FindItem = Items::findOne(['like', 'No', $data['c']]);  // Disabled -> 29/06/2020
            $FindItem = Items::findOne($data['id']);     // 29/06/2020
            if($FindItem != NULL){
                //$dataCount[] = $FindItem->invenByCache - $data['qty'];    // Disabled -> 29/06/2020
                $dataCount[] = $FindItem->last_stock - $data['qty'];      // 29/06/2020
            }else{
                $dataCount[] = $data['qty'];
            }
        }


        // เทียบชื่อใน Item Table
        $model      = Items::find()
                    ->where(['or',
                        ['Description'  => trim(Yii::$app->request->post('param')['item'])],
                        ['description_th' => trim(Yii::$app->request->post('param')['item'])]
                    ])
                    ->orderBy(['id' => SORT_ASC])
                    ->one();
 
        if($model != null){

            //$model  = $items->orderBy(['id' => SORT_ASC])->one();


            $data = [
                    'item'      => $model->No,
                    'itemid'    => $model->id,
                    'ig'        => $model->ItemGroup,
                    'Photo'     => $model->picture,
                    'std'       => $model->lastPrice *1, // ไม่ให้เห็น StandardCost
                    'lastprice' => $model->lastPrice *1,
                    'desc'      => $model->Description,
                    'code'      => $model->master_code,
                    'sent'      => trim(Yii::$app->request->post('param')['item']),
                    'inv'       => $model->ProductionBom > 0 
                                        ? $model->invenByCache  //$model->invenByCache,
                                        : $model->last_stock,
                    'remain'    => min($dataCount)  < 0 ? Yii::t('common','OUT OF STOCK') : min($dataCount),             
                    'possible'  => min($dataCount)  < 0 ? Yii::t('common','OUT OF STOCK') : min($dataCount),
                    'text'      => [
                        'Inventory' => Yii::t('common','Remain'),
                        'LastPrice' => Yii::t('common','Last Price')
                    ],
                    'status'    => 200,
                ];
            
        }else {
            $data = [
                    'item'      => 'eWinl',
                    'itemid'    => 0,
                    'ig'        => 0,
                    'Photo'     => 0,
                    'std'       => 0,
                    'lastprice' => 0,
                    'desc'      => 'ยังไม่มี Item นี้',
                    'code'      => 'eWinl',
                    'sent'      => trim(Yii::$app->request->post('param')['item']),
                    'inv'       => '',
                    'remain'    => '',
                    'possible'  => min($dataCount) < 0 ? Yii::t('common','OUT OF STOCK') : min($dataCount),
                    'text'      => [
                        'Inventory' => Yii::t('common','Remain'),
                        'LastPrice' => Yii::t('common','Last Price')
                    ],
                    'status'    => 404,
                ];
             
        }

        return json_encode($data);

        // try{ 

        //     $bot =  \common\models\LineBot::findOne(5);
        //     $msg = 'TEST'."\r\n";                                         
                                                                  
        //     $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n\r\n";
             
            
        //     $bot->notify_message($msg);	

        // } catch (\Exception $e) {					 
        //      // Line Notify Error					 
            
        // }	

    }

    // -----------Address-----------

    public function actionPostcodeValidate($postcode) {


        $Zipcode      = Zipcode::find()->where(['ZIPCODE' => $postcode])->exists();

        if($Zipcode){
            return true;
        }else {
            return false;
        }

    }

    public function actionPostcodeFromDiscrict($discrict) {

        $Zipcode      = Zipcode::find()->where(['DISTRICT_ID' => $discrict])->exists();

        if($Zipcode){
             $Zipcode      = Zipcode::findOne(['DISTRICT_ID' => $discrict]);

            return  $Zipcode->ZIPCODE;

        }else {
            return false;
        }



    }

    public function actionPostcodeFromDiscrictList($discrict) {

        $Zipcode      = Zipcode::find()->where(['DISTRICT_ID' => $discrict])->exists();

        $data = [];
        if($Zipcode){
             $Zipcode      = Zipcode::find()->where(['DISTRICT_ID' => $discrict])->all();

            foreach ($Zipcode as $key => $model) {
                $data[]= [
                    'val' => $model->ZIPCODE,
                    'text' => $model->amphur->AMPHUR_NAME,
                    'selected' => NULL,
                ];
            }
            //return  $Zipcode->ZIPCODE;
           

        }else {
            return false;
        }   


        return Json::encode($data,true);


    }




    public function actionCityFromProvince($province) {


         $Amphur      = Amphur::find()->where(['PROVINCE_ID' => $province])
                        ->orderBy(['AMPHUR_NAME' => SORT_ASC])
                        ->all();

         $data[]= ['val' => '0','text' => '. . . '.Yii::t('common','City').' . . . ','selected' => 'selected="selected"'];
         foreach ($Amphur as $value) {

         $data[]= [
                    'val' => $value->AMPHUR_ID,
                    'text' => $value->AMPHUR_NAME,
                    'selected' => NULL,
                ];

         }


         return Json::encode($data,true);

    }

    public function actionCityFromZipcode($zipcode) {

        $Zipcode    = Zipcode::find()->where(['ZIPCODE' => $zipcode])->all();
        $AmphurID   = array();
        foreach ($Zipcode as $key => $zip) {
            $AmphurID[] = $zip['AMPHUR_ID'];
        }

        $Amphur      = Amphur::find()->where(['AMPHUR_ID' => $AmphurID])
                        ->orderBy(['AMPHUR_NAME' => SORT_ASC])
                        ->all();

         //$data[]= ['val' => '0','text' => '. . . '.Yii::t('common','City').' . . . ','selected' => 'selected="selected"'];

         foreach ($Amphur as $value) {

         $data[]= [
                    'val' => $value->AMPHUR_ID,
                    'text' => $value->AMPHUR_NAME,
                    'selected' => NULL,
                ];

         }


         return Json::encode($data,true);

    }

    public function actionGetDistrictZipcode($zipcode) {


        $Zipcode        = Zipcode::find()->where(['ZIPCODE' => $zipcode])->all();
        $AmphurList     = array();
        foreach ($Zipcode as $key => $zip) {
            $AmphurList[] = $zip['AMPHUR_ID'];
        }

        $Amphur      = Amphur::find()->where(['AMPHUR_ID' => $AmphurList])
                        ->orderBy(['AMPHUR_NAME' => SORT_ASC])
                        ->one();


        $distric      = District::find()->where(['AMPHUR_ID' => $Amphur->AMPHUR_ID])
                        ->orderBy(['DISTRICT_NAME' => SORT_ASC])
                        ->all();

         //$data[]= ['val' => '0','text' => '. . . '.Yii::t('common','District').' . . . ','selected' => 'selected="selected"'];
         foreach ($distric as $value) {

         $data[]= [
                    'val' => $value->DISTRICT_ID,
                    'text' => $value->DISTRICT_NAME,
                    'selected' => NULL,
                ];

         }


         return Json::encode($data,true);

    }

    public function actionGetProvince($postcode) {
        // if($postcode)
        //  if(!(Zipcode::findOne(['ZIPCODE' => $postcode])))
        //  {
        //     $data = [
        //         'val' => NULL,
        //         'text' => Yii::t('common','None'),
        //         ];
        //     return Json::encode($data,true);
        //     exit();
        //  }


        if(!isset($postcode))
        {


            $Allprovince   = Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all();

             $data[] =  [
                        'val' => '',
                        'text' => Yii::t('common','Province'),
                        'selected' => 'selected="selected"',
                        ];


             foreach ($Allprovince as $value) {

                $selected = NULL;

                 $data[] = [
                        'val' => $value->PROVINCE_ID,
                        'text' => $value->PROVINCE_NAME,
                        'selected' => $selected,
                        ];
            }

             return Json::encode($data,true);
             exit();

        }else {

            //$zipcode       = Zipcode::findOne(['ZIPCODE' => $postcode]);
             $zipcode       = $this->findZipcode($postcode);
             //$province      = Province::findOne(['PROVINCE_ID' => $zipcode->PROVINCE_ID]);
             $province      = $this->findProvince($zipcode->PROVINCE_ID);
             $Allprovince   = Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all();

             $data = array();
             foreach ($Allprovince as $value) {

                if($value->PROVINCE_ID == $province->PROVINCE_ID)
                 {
                    $selected = 'selected="selected"';
                 }else {
                    $selected = NULL;
                 }

                 $data[] = [
                        'val' => $value->PROVINCE_ID,
                        'text' => $value->PROVINCE_NAME,
                        'selected' => $selected,
                        ];
            }

             return Json::encode($data,true);
        }
    }

    public function findZipcode($id)
    {
        if (($model = Zipcode::findOne(['ZIPCODE' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Please enter zipcode.');
            //throw new  Exception('The requested page does not exist.');
        }
    }

    public function findProvince($id)
    {
        if (($model = Province::findOne(['PROVINCE_ID' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Please enter province.');

        }
    }

    public function actionGetProvinceList($postcode,$province) {

        $data   = [];     
        $keys   = 'province&province:'.$province;
        if(Yii::$app->cache->get($keys)){
			return Yii::$app->cache->get($keys);
		}else{
            if($postcode==''){
                $Allprovince   = Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all();
                $data[] =  [
                            'val' => '',
                            'text' => Yii::t('common','Province'),
                            'selected' => 'selected="selected"',
                ];

                foreach ($Allprovince as $value){
                    $selected = NULL;
                    $data[] = [
                            'val' => $value->PROVINCE_ID,
                            'text' => $value->PROVINCE_NAME,
                            'selected' => $selected,
                    ];
                }

                //return Json::encode($data,true);

            }else {

                $Allprovince   = Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all();

                $data = [];
                foreach ($Allprovince as $value) {

                    if($value->PROVINCE_ID == $province)
                    {
                        $selected = 'selected="selected"';
                    }else {
                        $selected = NULL;
                    }

                    $data[] = [
                            'val'       => $value->PROVINCE_ID,
                            'text'      => $value->PROVINCE_NAME,
                            'selected'  => $selected,
                    ];
                }

                
            }
        }

        Yii::$app->cache->set($keys, json_encode($data), 3600);

        return Yii::$app->cache->get($keys);
 
    }

    public function actionGetOneProvince($postcode) {

        if($postcode=='')
        {
            return 0;
        }else {
             $zipcode       = Zipcode::findOne(['ZIPCODE' => $postcode]);
             $province      = Province::findOne(['PROVINCE_ID' => $zipcode->PROVINCE_ID]);
             return $province->PROVINCE_ID;
        }


    }

    public function actionGetCity($postcode) {
         if(!(Zipcode::findOne(['ZIPCODE' => $postcode])))
         {
            $data[] = [
                'val' => NULL,
                'text' => Yii::t('common','None'),
                ];
            return Json::encode($data,true);
            exit();
         }

         $zipcode   = Zipcode::findOne(['ZIPCODE' => $postcode]);
         $province  = Province::findOne(['PROVINCE_ID' => $zipcode->PROVINCE_ID]);
         $city      = Amphur::find()->where(['PROVINCE_ID' => $province->PROVINCE_ID])->orderBy(['AMPHUR_NAME' => SORT_ASC])->all();

         $data[]= ['val' => '0','text' => Yii::t('common','Select').' '.Yii::t('common','Amphur'),'selected' => 'selected="selected"'];

         foreach ($city as $value) {
             $data[]= [
                'val' => $value->AMPHUR_ID,
                'text' => $value->AMPHUR_NAME,
                ];
         }


         return Json::encode($data,true);

    }
    public function actionGetCityDefault($city,$province) {

        if(!(Zipcode::findOne(['ZIPCODE' => $_GET['postcode'],'PROVINCE_ID'=> $province ])))
         {
            $data[] = [
                'val' => NULL,
                'text' => Yii::t('common','None'),
                ];
            return Json::encode($data,true);
            exit();
         }

         $zipcode   = Zipcode::findOne(['ZIPCODE' => $_GET['postcode'],'PROVINCE_ID'=> $province]);
         $Amphur      = Amphur::find()->where(['PROVINCE_ID' => $zipcode->PROVINCE_ID])->orderBy(['AMPHUR_NAME' => SORT_ASC])->all();

         $data = array();

         foreach ($Amphur as $value) {

             if($value->AMPHUR_ID == $city)
             {
                $selected = 'selected="selected"';
             }else {
                $selected = NULL;
             }
             $data[]= [
                'val' => $value->AMPHUR_ID,
                'text' => $value->AMPHUR_NAME,
                'selected' => $selected,
                ];
         }


         return Json::encode($data,true);

    }

    public function actionGetTumbol($postcode) {

        if(!(Zipcode::findOne(['ZIPCODE' => $postcode])))
         {
            $data[] = [
                'val' => NULL,
                'text' => Yii::t('common','None'),
                ];
            return Json::encode($data,true);
            exit();
         }

         $zipcode   = Zipcode::findOne(['ZIPCODE' => $postcode]);
         $province  = Province::findOne(['PROVINCE_ID' => $zipcode->PROVINCE_ID]);
         //$city      = Amphur::findOne(['PROVINCE_ID' => $province->PROVINCE_ID]);
         $distric   = District::find()->where(['PROVINCE_ID' => $province->PROVINCE_ID])->orderBy(['DISTRICT_NAME' => SORT_ASC])->all();

         $data[]= ['val' => '0','text' => Yii::t('common','Select').' '.Yii::t('common','District'),'selected' => 'selected="selected"'];

         foreach ($distric as $value) {
             $data[]= [
                'val' => $value->DISTRICT_ID,
                'text' => $value->DISTRICT_NAME,
                ];
         }


         return Json::encode($data,true);

    }

    public function actionGetDistrictCity($district) {

        if(!(District::findOne(['AMPHUR_ID' => $_GET['city']])))
         {
            $data[] = [
                'val' => 0,
                'text' => Yii::t('common','None'),
                'selected' => '',
                ];
            return Json::encode($data,true);
            exit();
         }

         //$zipcode   = Zipcode::findOne(['ZIPCODE' => $postcode]);
         //$province  = Province::findOne(['PROVINCE_ID' => $zipcode->PROVINCE_ID]);
         //$city      = Amphur::find()->where(['PROVINCE_ID' => $province->PROVINCE_ID])->all();
         $getDistric   = District::find()->where(['AMPHUR_ID' => $_GET['city']])->orderBy(['DISTRICT_NAME' => SORT_ASC])->all();



         $data[]= ['val' => '0','text' => '. . . '.Yii::t('common','District').' . . . ','selected' => 'selected="selected"'];


         foreach ($getDistric as $value) {

             if($value->DISTRICT_ID == $district)
             {
                $selected = 'selected="selected"';
             }else {
                $selected = NULL;
             }
             $data[]= [
                'val' => $value->DISTRICT_ID,
                'text' => $value->DISTRICT_NAME,
                'selected' => $selected,
                ];
         }

         return Json::encode($data,true);

    }
    // ------./ Address---------






    public function actionGetAmphur() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {

            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                 $province_id = $parents[0];
                 $out = $this->getAmphur($province_id);
                 echo Json::encode(['output'=>$out, 'selected'=>'']);
                 return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionGetDistrict() {
         $out = [];
         if (isset($_POST['depdrop_parents'])) {

             $ids = $_POST['depdrop_parents'];
             $province_id = empty($ids[0]) ? null : $ids[0];
             $amphur_id = empty($ids[1]) ? null : $ids[1];

             if ($province_id != null) {
                $data = $this->getDistrict($amphur_id);
                echo Json::encode(['output'=>$data, 'selected'=>'']);
                return;
             }
         }
         echo Json::encode(['output'=>'', 'selected'=>'']);
     }





    public function actionGetZipcode() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {

            $ids = $_POST['depdrop_parents'];
            $province_id = empty($ids[0]) ? null : $ids[0];
            $amphur_id = empty($ids[1]) ? null : $ids[1];
            $distric = empty($ids[2]) ? null : $ids[2];

            if ($province_id != null) {
                $data = $this->getZipcode($distric);
                echo Json::encode(['output'=>$data]);
                return;
            }
        }

        echo Json::encode(['output'=>'']);
    }


    protected function getAmphur($id){
        $datas = Amphur::find()->where(['PROVINCE_ID'=>$id])->orderBy(['AMPHUR_NAME' => SORT_ASC])->all();
        return $this->MapData($datas,'AMPHUR_ID','AMPHUR_NAME');
    }

    protected function getDistrict($id){
        $datas = District::find()->where(['AMPHUR_ID'=>$id])->orderBy(['DISTRICT_NAME' => SORT_ASC])->all();
        return $this->MapData($datas,'DISTRICT_ID','DISTRICT_NAME');
    }

    protected function getZipcode($id){
        $datas = Zipcode::find()->where(['DISTRICT_ID'=>$id])->orderBy(['DISTRICT_CODE' => SORT_ASC])->all();
        return $this->MapData($datas,'ZIPCODE','ZIPCODE');
    }

    protected function MapData($datas,$fieldId,$fieldName){
        $obj = [];

        foreach ($datas as $key => $value) {
            array_push($obj, ['id'=>$value->{$fieldId},'name'=>$value->{$fieldName}]);
        }
        return $obj;
    }


    public static function getTable($data){
        switch ($data->type) {
            case 'BillingNote':
                $table  = 'billing_note';
                $source = \common\models\BillingNote::find()->select('no_ as no')->where(['no_' => base64_decode($data->id)])->one();
                break;

            case 'Sale-Chat':
                $table  = 'sale_header';
                $source = \common\models\SaleHeader::findOne(base64_decode($data->id));
                break;
            
            default:
                $table  = 'billing_note';
                $source = \common\models\BillingNote::find()->select('no_ as no')->where(['no_' => base64_decode($data->id)])->one();
                break;
        }
        

        return (Object)[
            'table' => $table,
            'source' => $source
        ];
    }


    public function actionChatModule($id){

        //$source = \common\models\BillingNote::find()->where(['no_' => base64_decode($id)])->one();
        $data       = (Object)[
                        'type' => Yii::$app->request->get('typeofdoc'), 
                        'id' => $id
                    ];

        $source     = $this->getTable($data);

        $model = new \common\models\OrderTracking;
       
        if(Yii::$app->request->post()){
            $model->event_date      = date('Y-m-d H:i:s');
            $model->doc_type        = $data->type;
            $model->doc_id          = 0;
            $model->doc_no          = $source->source->no;
            $model->doc_status      = $_POST['OrderTracking']['doc_status'];
            $model->remark          = trim($_POST['OrderTracking']['remark']);
            $model->ip_address      = $_SERVER['REMOTE_ADDR'];
            $model->lat_long        = '';
            $model->create_by       = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->track_for_table = $source->table;
            $model->track_for_id    = $source->source->id;

            if(trim($_POST['OrderTracking']['remark']) != ''){

                if(!$model->save()){
                    print_r($model->getErrors());
                }else{

                    try{                     
                        // Line Notify
                        $bot =  \common\models\LineBot::findOne(5);
                        $msg = "\r\n\r\nMessage \r\n";
                        $msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
                        
                        $msg.= $model->doc_type."\r\n";	
                        $msg.= $model->doc_no."\r\n";
                        $msg.= $model->remark."\r\n\r\n";   

                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
            
                        $bot->notify_message($msg);					
            
                    } catch (\Exception $e) {					 
                        Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
                    }

                }

            }else {
                echo 'Error From : '.$_SERVER['REMOTE_ADDR'];
            }

        }


        $query = \common\models\OrderTracking::find()
        ->where(['doc_type' => $data->type])
        ->andWhere(['doc_no' => $source->source->no])
        ->all();


        return $this->renderAjax('_chat_box',[
            'model' => $model,
            'query' => $query
        ]);

    }

    public function actionSetWorkdate(){

        $setDate = Yii::$app->request->post('date');

        Yii::$app->session->set('workdate',$setDate);
        Yii::$app->session->set('workyears',explode('-',$setDate)[0]); 


        if (isset($_POST['y'])) {
            Yii::$app->session->set('workyears',$_POST['y']); 
            Yii::$app->session->set('workdate',$_POST['y'].date('-m-d'));
        }

        
        return json_encode([
            'status' => 200,
            'workdate' => Yii::$app->session->get('workdate'),
            'workyears' => Yii::$app->session->get('workyears')
        ]);
    }

    public function actionChangeTheme(){
        $theme = $_POST['theme'];
         
        $profile = Profile::findOne(Yii::$app->user->identity->id);
        $profile->theme = $theme;
        if($profile->save()){
            Yii::$app->session->set('theme',  $profile->theme);

            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => $theme
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'message' => 'error',
                'value' => $theme
            ]);
        }       
       
    }

    public function actionChangeThemeDemis($demis){

        $cookies = Yii::$app->response->cookies;   

        if($demis=='true'){
            $cookies->add(new \yii\web\Cookie([
                'name' => 'themeAlert',
                'value' => '0',
                'expire' => time() + 18000,
            ]));
            
            
        }       
       
    }


    public function actionChangeSalePeople(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);


        if(isset(Yii::$app->user->identity->id)){

            $AppsRules = \common\models\AppsRules::find()
            ->where(['user_id' => \Yii::$app->user->identity->id])
            ->one();

            if($AppsRules != null){

                $Sale = \common\models\SalesPeople::findOne($data->id);
                $AppsRules->sale_id = $data->id;
                $AppsRules->sale_code = $Sale->code;

                Yii::$app->session->set('Rules', $AppsRules);
    
                $status = 200;
            }else{
                $status = 404;
            }

            
        }else{
            $status = 403;
        }



        return $this->asJson([
            'status' => $status,
            'sale_id' => Yii::$app->session->get('Rules')
        ]);
    }

    public function actionChangeCompany(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        if(isset(Yii::$app->user->identity->id)){

            $AppsRules = \common\models\AppsRules::find()
            ->where(['user_id' => \Yii::$app->user->identity->id])
            ->one();

            if($AppsRules != null){
 
                $AppsRules->comp_id     = $data->comp;
                $AppsRules->sales_id    = 0;
                $AppsRules->sale_id     = null;
                $AppsRules->sale_code   = null;
                if(\Yii::$app->user->identity->id != 1){
                    $AppsRules->save(false);
                }
                

                Yii::$app->session->set('Rules', $AppsRules);
    
                $status = 200;
            }else{
                $status = 404;
            }
            
        }else{
            $status = 403;
        }

        return json_encode([
            'status' => $status,
            'sale_id' => Yii::$app->session->get('Rules')
        ]);
    }

    public function actionSetFavoriteMenu(){
        $request_body   = file_get_contents('php://input');
        $body           = json_decode($request_body);

        $message = 'on';
        $status  = 200;
        if($body->status== 'on'){
            $model = \common\models\FavoriteMenu::findOne(['url' => $body->url, 'user_id' => Yii::$app->user->identity->id]);
            if($model->delete()){
                $message = 'off';
            }else{
                $status  = 500;
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }else{
            $model          = new \common\models\FavoriteMenu();
            $model->name    = $body->name ? $body->name : NULL;
            $model->url     = $body->url;
            $model->user_id = Yii::$app->user->identity->id;
            if($model->save()){
                $message = 'on';
            }else{
                $status  = 500;  
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);               
            }
        }

        
        return json_encode([
            'status' => $status, 
            'message' => $message
        ]);
    }
}
