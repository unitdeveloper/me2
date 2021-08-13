<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
//use yii\widgets\ActiveForm;
use kartik\widgets\SwitchInput;

use yii\helpers\ArrayHelper;
use common\models\PriceStructure;
use common\models\Itemgroup;
use common\models\Unitofmeasure;


//use dosamigos\ckeditor\CKEditor;


use admin\modules\items\models\MultipleUploadForm;

use common\models\Property;
use common\models\Itemset;

//use common\models\ItemCategory;

use kartik\color\ColorInput;

$session = Yii::$app->session;
/* @var $this yii\web\View */
/* @var $model common\models\Items */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord){
    // init
    $model->quantity_per_unit   = 1;
    $model->cansale             = 1;
    $model->Status              = 1;
    $model->ItemGroup           = 79;
    $model->category            = 73;
}

$MultiUpload = new MultipleUploadForm();
 
function Hiddenbin($pic){

    if($pic !=''){
        return NULL;
    }else {
        return 'hidden';
    }
}

if(isset($model->No)){
    $No = ['value' => explode('^',$model->No)];
}else {
    $No = NULL;
}


?>
<?php $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>
<?php $this->registerJsFile('@web/js/manufacturing/itemController.js?v=5.09.21',['depends' => [\yii\web\JqueryAsset::className()]]);?>


<style type="text/css">
    .pick-img{
        position: absolute;

        top:20%;
        left: 30%;
    }
    .tab-content {
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        padding: 10px;
    }
    .product-name-popup{
        display:none;
    }
 
</style>
<div class="items-form " style="margin-top: 10px;" ng-controller="itemController" ng-init="description='<?=$model->Description?>';description_th='<?=$model->description_th?>'">

  <div class=" ">
  
      <?php $form = ActiveForm::begin([
          'id' => 'items-form',
          'enableAjaxValidation' => true,
          'options' => [
            'enctype' => 'multipart/form-data',
            'data-key' => $model->isNewRecord ? '' : $model->No,
            'data-id' => $model->isNewRecord ? '' : $model->id,
            'data-session' => Yii::$app->session->id,
          ],
      ]); ?>

        <input type="hidden" name="company" id="company" value="<?= Yii::$app->session->get('Rules')['comp_id'];?>">
         
        <div class="row"  >
            <div class="col-sm-12">
                <h4 ng-bind="description"></h4> 
                <small>
                <?=$model->description_th?> 
                <?=Yii::t('common','Create')?> : <?=date('r',strtotime(($model->isNewRecord) ? date('Y-m-d') :$model->date_added))?>
                <?=Yii::t('common','By')?> : 
                <?=$model->isNewRecord ? Yii::$app->user->identity->profile->firstname.' '.Yii::$app->user->identity->profile->lastname : $model->user->firstname. ' ' .$model->user->lastname;?>
                </small>
            </div>
        </div>
        <ul class="nav nav-tabs">
            <li class="active bg-success">
                <a href="#tab-basic" data-toggle="tab">
                <i class="fas fa-info-circle text-success"></i>  <?=Yii::t('common','General')?>
                </a>
            </li>
            <li class="bg-info <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?> <?=$model->isNewRecord ? 'hidden':' ';?>">
                <a href="#tab-Normal" data-toggle="tab">
                <i class="fas fa-cubes text-aqua"></i>  <?=Yii::t('common','Property')?>
                </a>
            </li>
            <li class="bg-warning <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?>">
                <a href="#tab-Advance" data-toggle="tab">
                <i class="fas fa-cogs text-orange"></i>  <?=Yii::t('common','Options')?>
                </a>
            </li>
            <li class="<?=$model->isNewRecord ?'hidden':' ';?>  <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?>"  style="background-color:#ddadf5;">
                <a href="#tab-Refrerence" data-toggle="tab">
                <i class="fas fa-link"></i>  <?=Yii::t('common','Refrerence')?>
                </a>
            </li>              
        </ul> 

            <div class="tab-content">
                <div class="tab-pane fade  active in" id="tab-basic">
                    <div class="row">
                        <div class="col-sm-4" ng-init="barcode='<?=$model->barcode?>'">
                             
                            <?= $form->field($model, 'barcode', [
                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-barcode"></i>']]
                                    ])->textInput([
                                        'class'         => 'form-control barcodeSearch',
                                        'autocomplete'  => 'off',
                                        'ng-model'      => 'barcode',
                                        'disabled'      => $model->id == 1414 ? true : false    
                                    ]) ?>

                            <?= $form->field($model, 'barcode_for_box', [
                                    'addon' => ['prepend' => ['content'=>'<i class="fas fa-box-open"></i>']]
                                    ])->textInput([
                                        'class'         => 'form-control',
                                        'autocomplete'  => 'off',
                                        'disabled'      => $model->id == 1414 ? true : false    
                                    ])?>


                            <?= $form->field($model, 'master_code', [
                                    'addon' => ['prepend' => ['content'=>'<i class="fas fa-terminal"></i>']]
                                    ])->textInput([
                                        'maxlength'     => true,
                                        'id'            => 'master_code' ,
                                        'autocomplete'  => 'off',   
                                        'data-org'      => $model->master_code,
                                        'disabled'      => $model->id == 1414 ? true : false                                     
                                    ])->label(Yii::t('common','Product code')) ?>
                           
                            <div class="row">
                                <div class="col-xs-6">
                                    <?= $form->field($model, 'brand', [
                                    'addon' => ['prepend' => ['content'=>'<i class="fab fa-shirtsinbulk"></i>']]
                                    ])->textInput([
                                        'disabled'      => $model->id == 1414 ? true : false
                                    ]) ?>   
                                </div>
                                <div class="col-xs-6">                            
                                <?= $form->field($model, 'name', [
                                    'addon' => ['prepend' => ['content'=>'<i class="fas fa-text-width text-warning"></i>']]
                                    ])->textInput([
                                        'disabled'      => $model->id == 1414 ? true : false
                                    ]) ?>
                                </div>
                            </div>                           
                        </div>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-12" style="position:relative;">

                                    <?= $form->field($model, 'description_th', [
                                        'addon' => ['prepend' => ['content'=>'<i class="fas fa-info"></i>']]
                                        ])->textInput([
                                            'maxlength'     => true,
                                            'ng-model'      => 'description_th',
                                            'ng-keyup'      => 'changeDiscriptionth(this)',
                                            'disabled'      => ($model->id == 1414 ? true : ($model->disabled)? true: false)
                                        ])->label(Yii::t('common','Name (Th)')) 
                                    ?>
                                    <?= $form->field($model, 'Description', [
                                        'addon' => ['prepend' => ['content'=>'<i class="fas fa-info"></i>']]
                                        ])->textInput([
                                            'maxlength'     => true,
                                            'ng-model'      => 'description',
                                            'ng-keyup'      => 'changeDiscription(this)',
                                            'autocomplete'  => 'off',
                                            'disabled'      => ($model->id == 1414 ? true : ($model->disabled)? true: false)
                                            ])->label(Yii::t('common','Name (En)')) 
                                    ?>
                                <div class="row">                                    
                                    <div class="col-xs-8">
                                        <?= $form->field($model, 'detail', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-align-left"></i>']]
                                            ])->textInput([
                                                'maxlength'     => true,
                                                'autocomplete'  => 'off',
                                                'disabled'      => $model->id == 1414 ? true : false
                                                ])->label(Yii::t('common','Item Detail')) 
                                        ?>
                                     </div>

                                     <div class="col-xs-4">                                
                                        <?= $form->field($model, 'size', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-ruler-combined"></i>']]
                                            ])->textInput([
                                                'maxlength'     => true,
                                                'autocomplete'  => 'off',
                                                'disabled'      => $model->id == 1414 ? true : false
                                                ])->label(Yii::t('common','Size')) 
                                        ?>
                                    </div>
                                </div>
                                    <div class="product-name-popup">
                                        <div class="product-popup">
                                            <a href="javascript:void(0);" class="product-popup-close text-orange"><i class="fas fa-times text-red"></i> <?=Yii::t('common','Close & Create new item')?></a>
                                            <h5 style="margin: 10px;">เลือกจากสินค้าที่มีอยู่แล้ว หรือ ปิดเพื่อสร้างใหม่</h5>
                                            <div class="product-popup-body">                                                
                                                <table class="table table-bordered table-hover">
                                                    <tr ng-repeat="model in itemList" ng-if="model.count>0" data-row="x">
                                                        <td style="max-width:50px;"><img src="{{model.img}}" class="img-responsive"></td>                                                         
                                                        <td class="text-primary">
                                                            <div ng-bind="model.desc_th" ng-if="model.desc_th != model.desc_en"></div>
                                                            <div ng-bind="model.desc_en" ></div>                                                            
                                                            <div ng-if="model.barcode">barcode : <span ng-bind="model.barcode"></span></div>
                                                            <div >
                                                                <a href="javascript:void(0);" ng-click="cloneItem(this)" class="btn btn-primary btn-xs btn-flat" ><i class="fas fa-plus"></i> <?=Yii::t('common','Clone Data')?></a>
                                                                <a href="javascript:void(0);" ng-click="choseItem(this)"  class="btn btn-warning btn-xs btn-flat" ><i class="far fa-copy"></i> <?=Yii::t('common','Copy Data')?></a>
                                                            </div>
                                                        </td>
                                                        
                                                    </tr>
                                                    <tr ng-repeat="data in itemList" ng-if="data.count<=0">
                                                        <td colspan=2 >No Data</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            
                                        </div>
                                        
                                    </div>

                                    
                                </div>
                            </div>                           
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($model,'StandardCost', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-dollar-sign text-red"></i>']]
                                            ])->textInput([
                                                'type'      => 'number',
                                                'step'      => 'any',
                                                'class'     => 'text-right',
                                                'disabled'  => $model->id == 1414 ? true : false,
                                                'value'     => $model->myItems ? $model->myItems->StandardCost * 1 : $model->StandardCost * 1 //$model->StandardCost * 1
                                            ])->label(Yii::t('common','Unit Cost'));?>                                   
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'CostGP', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-dollar-sign text-green"></i>']]
                                            ])->textInput([
                                                'maxlength'     => true,
                                                'type'          => 'number',
                                                'step'          => 'any',
                                                'class'         => 'text-right',
                                                'disabled'      => $model->id == 1414 ? true : false,
                                                'value'         => $model->myItems ? $model->myItems->CostGP * 1 : $model->CostGP * 1
                                            ])->label(Yii::t('common','Sale Price')) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'sale_price', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-dollar-sign text-yellow"></i>']]
                                            ])->textInput([
                                                'maxlength'     => true,
                                                'type'          => 'number',
                                                'step'          => 'any',
                                                'class'         => 'text-right',
                                                'disabled'      => $model->id == 1414 ? true : false,
                                                'value'         => $model->sale_price * 1
                                            ])->label(Yii::t('common','Express Price')) ?>
                                </div>
                            </div>    
                            
                                      
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6 <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?>">
                        
                            <div class="col-sm-2" >
                                <div class="row">

                                    <span class="btn btn-file">
                                        <span class="fileinput-new"><img src="<?= $MultiUpload->ImageIsset($model->ItemGroup,$model->thumbnail1); ?>"   class="img-thumbnail small1 thumbnail1"></span>
                                        <a href="#" data-id="thumbnail1" class="bin-remove <?=Hiddenbin($model->thumbnail1)?>" alt="ลบภาพ" title="ลบภาพ"><span ><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                        <span class="fileinput-exists"></span>
                                    <input type="file" name="Items[thumbnail1]" onchange="small1(this);"></span>

                                    <span class="btn btn-file">
                                        <span class="fileinput-new"><img src="<?= $MultiUpload->ImageIsset($model->ItemGroup,$model->thumbnail2); ?>"   class="img-thumbnail small2 thumbnail2"></span>
                                        <a href="#" data-id="thumbnail2" class="bin-remove <?=Hiddenbin($model->thumbnail2)?>" alt="ลบภาพ" title="ลบภาพ"><span><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                        <span class="fileinput-exists"></span>
                                    <input type="file" name="Items[thumbnail2]" onchange="small2(this);"></span>

                                    <span class="btn btn-file">
                                        <span class="fileinput-new"><img src="<?= $MultiUpload->ImageIsset($model->ItemGroup,$model->thumbnail3); ?>"   class="img-thumbnail small3 thumbnail3"></span>
                                        <a href="#" data-id="thumbnail3" class="bin-remove <?=Hiddenbin($model->thumbnail3)?>" alt="ลบภาพ" title="ลบภาพ"><span><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                        <span class="fileinput-exists"></span>
                                    <input type="file" name="Items[thumbnail3]" onchange="small3(this);"></span>

                                    <span class="btn btn-file">
                                        <span class="fileinput-new"><img src="<?= $MultiUpload->ImageIsset($model->ItemGroup,$model->thumbnail4); ?>"   class="img-thumbnail small4 thumbnail4"></span>
                                        <a href="#" data-id="thumbnail4" class="bin-remove <?=Hiddenbin($model->thumbnail4)?>" alt="ลบภาพ" title="ลบภาพ"><span><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                        <span class="fileinput-exists"></span>
                                    <input type="file" name="Items[thumbnail4]" onchange="small4(this);"></span>

                                    <span class="btn btn-file">
                                        <span class="fileinput-new"><img src="<?= $MultiUpload->ImageIsset($model->ItemGroup,$model->thumbnail5); ?>"   class="img-thumbnail small5 thumbnail5"></span>
                                        <a href="#" data-id="thumbnail5" class="bin-remove <?=Hiddenbin($model->thumbnail5)?>" alt="ลบภาพ" title="ลบภาพ"><span><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                        <span class="fileinput-exists"></span>
                                    <input type="file" name="Items[thumbnail5]" onchange="small5(this);"></span>
                                </div>
                                
                            </div>
                            <div class="col-sm-5" >
                                <div class="row">
                                    
                                    <span class="btn btn-file " >
                                        <span class="fileinput-new">
                                            <img class="img-added img-thumbnail" src="<?= $model->picture ?>" >
                                        </span>
                                        <span class="fileinput-exists"></span>
                                        <input type="file" name="Items[Item_Picture]" onchange="readURL(this);">
                                        
                                    </span>


                                    

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 <?=$model->isNewRecord ?'hidden':' ';?>">
                            <h4><i class="fas fa-boxes"></i> <?=Yii::t('common','Base Unit Of Measure')?></h4>
                            <div class="row" ng-repeat="list in mesurelist" ng-if="mesurelist.length>0">
                                <div class="col-xs-3" style="margin-top:25px;">
                                    <a href="javascript:void(0)" class="pull-left text-success" ng-click="setDefault(this)" ng-if="list.default==1"><i class="far fa-check-square"></i> <?=Yii::t('common','Default')?></a>
                                    <a href="javascript:void(0)" class="pull-left text-gray" ng-click="setDefault(this)" ng-if="list.default!=1"><i class="far fa-square"></i> <?=Yii::t('common','Default')?></a>
                                </div>
                                <div class="col-xs-4" ng-init="UnitOfMeasure=list.measure">
                                    <label for="Items-UnitOfMeasure"><?=Yii::t('common','Measure')?></label>
                                    <select name="UnitOfMeasure" ng-model="UnitOfMeasure" ng-change="changeMeasure(UnitOfMeasure,list)"  class="form-control" id="Items-UnitOfMeasure">  
                                        <option ng-repeat="option in unitofmeasure" value="{{option.id}}">{{option.name}}</option>
                                    </select>  
                                </div>
                                <div class="col-sm-4 col-xs-3">
                                    <div class="form-group field-items-quantity_per_unit">
                                        <label class="control-label" for="items-quantity_per_unit">ต่อหน่วย</label>
                                        <div class="input-group">
                                            <span class="input-group-addon hidden-sm hidden-xs"><i class="fas fa-prescription-bottle"></i></span>
                                            <input type="number" step=any id="items-quantity_per_unit" ng-blur="changeMeasureQty(this)" class="text-right form-control" name="quantity_per_unit" ng-model="list.qty">
                                        </div>
                                    </div>                                
                                </div>                                
                                <div class="col-sm-1 col-xs-2 no-padding" style="margin-top:27px;">                                     
                                    <a  href="javascript:void(0)" class="pull-right btn-delete-measure" 
                                    ng-click="removeRow($index,list)" 
                                    ng-if="mesurelist.length>1" style="margin-right: 15px; font-size:20px;"><i class="far fa-times-circle  text-red"></i></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="javascript:void(0)" id="add-measure" ng:click="addMeasure()">
                                        <i class="fas fa-plus"></i> <?=Yii::t('common','Add Unit Of Measure')?>
                                    </a>
                                </div>
                            </div>    
                            <hr>
                            <div class="row margin-top <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?>">
                                <div class="col-xs-4">                                       
                                      
                                    <?= $form->field($model, 'Status')->widget(SwitchInput::className(),[
                                                    'name' => 'Status',
                                                    'pluginOptions' => [
                                                        'onText' => 'On',
                                                        'offText' => 'Off',
                                                        'size' => 'mini',
                                                        'onColor' => 'success',
                                                        'offColor' => 'danger'
                                                    ]
                                                ])->label(Yii::t('common','Enable'));?>
                                </div>
                                <div class="col-sm-4">    
                                   

                                    <?= $form->field($model, 'cansale')->widget(SwitchInput::className(),[
                                        'name' => 'status_13',
                                        'pluginOptions' => [
                                            'onText' => 'On',
                                            'offText' => 'Off',
                                            'size' => 'mini'
                                        ]
                                    ])->label(Yii::t('common','Can-Sale'));?>

                                        
                                    
                                </div>
                                
                                <div class="col-xs-4">
                                    <?= $form->field($model, 'interesting')->widget(SwitchInput::className(),[
                                                'name' => 'interesting',
                                                'pluginOptions' => [
                                                    'onText' => 'On',
                                                    'offText' => 'Off',
                                                    'size' => 'mini'
                                                ]
                                            ])->label(Yii::t('common','Online-Sale'));?>
                        
                                </div> 
                            </div>  

                        </div>
                    </div>  
                   
                </div>
                <div class="tab-pane fade" id="tab-Normal">                     
                    <div class="row">
                                             
                            <div class="col-sm-4">
                                <div class="row">                                
                                    <div class="col-sm-12" style="margin-top:10px;">
                                        <?php

                                            $List = arrayHelper::map((Itemset::find()
                                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            ->orderBy(['name' => SORT_ASC])
                                            ->all()),'id','name');

                                            if($model->itemset=='') $model->itemset = $session->get('itemset');

                                        ?>

                                        <?= $form->field($model, 'itemset')->dropDownList($List,[

                                                                            'data-live-search'=> "true",
                                                                            'class' => 'selectpicker col-lg-12',
                                                                            'prompt'=>'-การจัดกลุ่ม สินค้า-'
                                                                        ])->label(false) ?>
                                    </div>
                                </div>
                                <div class="panel panel-info">
                                    <div class="panel-heading">Property <input type="hidden" name="" id="full_no" value="<?= $model->No ?>"></div>

                                        <div class="col-sm-12" >
                                            <ul class="property-sort ew-property-query" id="sortable">
                                                [Error] : [Onload Property Error.]
                                            </ul>

                                        </div>


                                        <hr>
                                        <div class="row">


                                                <!-- Disable This Function-->
                                                <div class="col-sm-12"  style=" display: none;">
                                                    <div class="property-info">
                                                        <?php # Modules -> ItemHasProperty?>

                                                        <?=  $this->render('category'); ?>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <?PHP $Proper->id = 0; ?>

                                                        <?= $form->field($Proper, 'id')->dropDownList(
                                                                    arrayHelper::map(Property::find()->all(),'id','description'),[

                                                                        'data-live-search'=> "true",
                                                                        'class' => 'selectpicker',
                                                                        'prompt'=>'เลือกคุณสมบัติ'
                                                                    ])->label(false);


                                                        ?>

                                                    </div>
                                                    <div class="col-sm-6">

                                                        <div class="">
                                                            <div class="col-sm-8">

                                                                <input type="text" class="form-control" id="property-value">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <button type="button" class="btn btn-default" id="addCat">ADD</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Disable -->


                                        </div>


                                </div>
                            </div>
                            <div class="col-sm-8">






                                <?= $form->field($model, 'TypeOfProduct')->dropDownList([ 'Vat', 'NoVat', ],['1'], ['prompt' => '']) ?>
                                <?= $form->field($model, 'CostingMethod')->dropDownList([ '0' => 'FIFO', '1'=>'Standard'],['1'], ['prompt' => '']) ?>
                                <?php  /* $form->field($model, 'detail')->widget(CKEditor::className(), [
                                    'options' => ['rows' => 2],
                                    'preset' => 'full', //basic,standard,full
                                ]) */  ?>


                            </div>
                        
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-Advance">
                    <div class="row">
                        <div class="col-sm-6">
                            

                            <div class="row">
                                <div class="col-sm-6">

                                    <?php if($model->replenishment=='') $model->replenishment = 'Purchase'; ?>

                                    <?= $form->field($model,'replenishment')->dropDownList([
                                        'Purchase'=>  Yii::t('common','Purchase.In'),
                                        'Produce'=>  Yii::t('common','Produce.In'),
                                        ]);?>       

                                </div>
                                <div class="col-sm-6">

                                    <?php if($model->product_group=='') $model->product_group = 'FG'; ?>

                                    <?= $form->field($model,'product_group')->dropDownList([
                                            'RM'=>'RM - ('. Yii::t('common','Raw material'). ')',
                                            'FG'=>'FG - ('. Yii::t('common','Finished Goods'). ')',
                                            'SM' => 'SM - ('. Yii::t('common','Semi Product'). ')'
                                            ]);?>

                                </div>
                            </div>

                            <?= $form->field($model, 'ProductionBom', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fas fa-cubes"></i>']]
                                            ])->textInput(['maxlength' => true]) ?>

                            

                            
                            <div class="row">
                                <div class="col-sm-8">
                                    <?= $form->field($model, 'PriceStructure_ID')->dropDownList(arrayHelper::map(PriceStructure::find()->all(),'ID','Name')) ?>
                                </div>
                            </div>

                            


                               

                              
                        </div>
                        <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-8">
                                    <?= $form->field($model, 'alias')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Alias name')])?>
                                    </div>
                                    <div class="col-sm-4">
                                    <?=$form->field($model, 'color')->widget(ColorInput::classname(), [
                                            'options' => ['placeholder' => Yii::t('common','Product color')],
                                        ]);
                                        ?>
                                    </div>
                                </div>    
                                <?php 
                                    $company = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
                                    if($model->brand =='' )     $model->brand       = $company->brand;
                                    if($model->brand_logo =='') $model->brand_logo  = $company->brand_logo;
                                ?>
                                    
                                
                                <?php echo $form->field($model, 'brand_logo', [
                                                'feedbackIcon' => ['default' => 'link']
                                            ])->textInput(['placeholder'=>'Enter url image logo'])->label(Yii::t('common','Url brand logo')); 
                                ?>
                                <div class="row">
                                    <div class="col-sm-4">
                                    <?php if($model->brand_logo!='') echo '<img src="'.$model->brand_logo.'" class="img-responsive brand-logo">'; ?>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade <?=$model->isNewRecord ?'hidden':' ';?>" id="tab-Refrerence">
                    <table class="table table-bordered">
                        <thead>
                            <tr>                        
                                <th style="width:100px;"><?=Yii::t('common','Type')?></th>
                                <th style="width:200px;"><?=Yii::t('common','Reference')?></th>
                                <th style="width:150px;"><?=Yii::t('common','Barcode')?></th>
                                <th style="width:150px;"><?=Yii::t('common','Code')?></th>                                
                                <th><?=Yii::t('common','Description')?></th>
                                
                                <!-- <th style="width:150px;"><?=Yii::t('common','Measure')?></th> -->
                                <th style="width:100px;" class="text-center"><?=Yii::t('common','Delete')?></th>
                            </tr>
                        </thead>
                        <tbody class="render-reference">
                            <tr>
                                <td colspan="6">Error 500</td>
                            </tr>
                        </tbody>
                        
                        <!-- <tr>
                            <td colspan="6"><button type="button" class="btn btn-success-ew"><i class="fa fa-plus"></i> ADD</button></td>
                        </tr> -->
                        
                    </table>                
                                    
                </div>

                <div class="row">
                    <div class="col-sm-12"><hr>
                        <div class="col-sm-12">
                            <div class="form-group text-right">
                                <?= $form->field($model, 'category')->hiddenInput()->label(false) ?>
                                <?= $form->field($model, 'ItemGroup')->hiddenInput()->label(false) ?>
                                <?= $form->field($model, 'CurrGroup')->hiddenInput(['value' => $model->ItemGroup])->label(false) ?>
                                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Save') : '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Save'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>                      

      <?php ActiveForm::end(); ?>
  </div>
</div>



<!-- Modal data-keyboard="false" data-backdrop="static"-->
<div id="myValidate" class="modal fade" role="dialog"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <div class="data-validate">
        <p>สินค้าไม่มี Barcode...</p>
      </div>

    </div>

  </div>
</div>


 
<div class="modal fade" id="source-picker-modal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Customer</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                    
                    <div class="input-group margin">
                        <input type="text" name="q" class="form-control" id="ew-search-cust-text" placeholder="ค้นหา...">
                        <span class="input-group-btn">
                        <button type="button" name="search" id="ew-search-cust-btn" class="btn btn-default-ew btn-flat"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                        
                    </div>
                </div>
                <div class="render-refernce"></div>                
            </div>
             
        </div>
    </div>
</div>


 
<div class="modal fade" id="measure" role="dialog" tabindex="-1" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Unit of measure')?></h4>
            </div>
            <div class="modal-body">
                
            </div>             
        </div>
    </div>
</div>



 