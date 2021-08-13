<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;

use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

use yii\helpers\ArrayHelper;
use common\models\Province;
use common\models\District;
use common\models\Amphur;
use common\models\Zipcode;

use yii\helpers\Url;
use kartik\widgets\DepDrop;

 
use admin\models\Generater;


app\assets\SweetalertAsset::register($this);

$model->user_id = Yii::$app->user->identity->id;
?>
<?php $form = ActiveForm::begin([
    'id'=>'form-vendors',
    'enableAjaxValidation' => false,
    'options' => ['enctype' => 'multipart/form-data','modules' => 'Purchase'],
    'fieldConfig' => [
        'options' => [
                //'tag' => false,
            ],
    ],
]); ?>
 
<style type="text/css">
    .status-cust{
        margin-top: 34px;
        width: 100%;
    }
    .branch-section, 
    .vat-registration{
        /* display: none; */
    }
</style>
<div class="" style="font-family: saraban;">
<div class="panel panel-info">
    <div class="panel-heading"><?= Icon::show('user-circle-o') ?> <?=Yii::t('common','Vendors')?></div>
    <div class="panel-body">
        <div class="vendors-form">
            <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
            <li class="active">
            <a href="#General" data-toggle="tab" aria-expanded="true"> 
                <?= Icon::show('user', [], Icon::BSG) ?>
                <?=Yii::t('common','Gen<span class="hidden-xs">eral</span>'); ?> </a>
            </li>
            <li class=""><a href="#Invoicing" data-toggle="tab" aria-expanded="false">
                <?= Icon::show('barcode', [], Icon::BSG) ?>
                <?=Yii::t('common','Inv<span class="hidden-xs">oicing</span>'); ?> </a></li>
             
             
             
        </ul>
                

                <div class="tab-content">

                    <div class="tab-pane  active" id="General">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="row text-center">
                                    <div class="col-xs-12">
                                        <?= Html::img($model->getPhotoViewer('logo'),['style'=>'max-width:150px;','class'=>'img-rounded ','id' => 'img-preview-logo']); ?>
                                        <?= $form->field($model, 'logo')->fileInput() ?>
                                    </div>
                                    <div class="col-xs-12">
                                        <?= Html::img($model->getPhotoViewer('photo'),['style'=>'max-width:150px;','class'=>'img-rounded ','id' => 'img-preview-photo']); ?>
                                        <?= $form->field($model, 'photo')->fileInput() ?>
                                    </div>
                                </div>
                                 
                                <div class=" status-cust">
                                    <?php if($model->status=='') $model->status = 1; ?>
                                    <?= $form->field($model, 'status')->checkBox(['class'=>'input-md','data-toggle'=>"toggle", 
                                     'data-style'=>"android", 'data-onstyle'=>"info"]) ?> 
                                </div>
                            </div>



                            
                            <div class="col-md-10">
                              <div class="panel panel-default">
                                <div class="panel-heading"><?=Yii::t('common','Information')?></div>
                                <div class="class-panel-body">

                                    <div class="margin">

                                        <div class="row">
                                            <div class="col-sm-4 col-xs-8">
                                                <?php

                                                    // $Generater  = new Generater();

                                                    // if($model->code=='') $model->code = $Generater->getRuning('vendors','vatbus_posting_group',1);

                                                ?>
                                                <?= $form->field($model, 'code', [
                                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-barcode"></i>']]
                                                ])->textInput(['class'=>'input-md','data' => $model->id]) ?>
                                            </div>
                                            <div class="col-sm-4 col-xs-8">
                                                 



                                                <?= $form->field($model, 'vender_type')->dropDownList(
                                                                        ArrayHelper::map(\common\models\CommonBusinessType::find()->all(),'id',function($model){
                                                                            return Yii::t('common',$model->name);
                                                                        }),
                                                                        [

                                                                          
                                                                            'class' => 'selectpicker form-control ',
                                                                            //'prompt' => Yii::t('common','Type'),
                                                                     
                                                                             
                                                                        ] 
                                                ) ?>
                                            </div>

                                        </div>
                                       


                                        <div class="row">
                                           <div class="col-xs-8">
                                        
                                            <?= $form->field($model, 'name', [
                                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-address-book"></i>']]
                                                ])->textInput(['class'=>'input-md'])
                                            ?>

                                            </div>
                                            <div class="col-xs-4">

                                               <?= $form->field($model, 'headoffice')->dropDownList([ '1'=> Yii::t('common','Head Office'), '0' => Yii::t('common','Branch') ]) ?>
                                            </div>
                                       </div>  

                        

                                        <div class="row branch-section">
                                           
                                           
                                           <div class="col-xs-8">
                                               <?php if($model->branch == NULL) $model->branch = '0000'; ?>
                                               <?= $form->field($model, 'branch_name', [
                                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-map-signs"></i>']]
                                                ])->textInput()->label(Yii::t('common','Branch name')) ?>
                                           </div>

                                           

                                           <div class="col-xs-4">
                                               <?php if($model->branch == NULL) $model->branch = '0000'; ?>
                                               <?= $form->field($model, 'branch')->textInput()->label(Yii::t('common','Branch no.')) ?>
                                           </div>
                                           
                                       </div>  

                                        
                                                          
                                       

                                        <?= $form->field($model, 'address', [
                                            'addon' => ['prepend' => ['content'=>'<i class="fa fa-address-card"></i>']]
                                            ])->textInput(['class'=>'input-md']) ?>

                                        <div class="row">
                                            <div class="col-xs-12" >
                                                <a href="#location-section"  data-toggle="collapse" data-target="#vendor-address2" class="pull-right" style="margin-right: 10px;">
                                                   <i class="fa fa-plus" aria-hidden="true"></i> 
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row collapse" id="vendor-address2">                                   
                                           
                                           <div class="col-xs-12">

                                            <?= $form->field($model, 'address2', [
                                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-address-card-o"></i>']]
                                                ])->textInput(['class'=>'input-md']) ?>
                                            </div>
                                        </div>


                                         <div class="row">
                                            <div class="col-xs-12" style="margin: 20px 0px 10px 0px;">
                                                <a href="#location-section"  data-toggle="collapse" data-target="#vendor-contact">
                                                   <i class="fa fa-address-book-o" aria-hidden="true"></i> <?=Yii::t('common','Contact')?>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="vendor-contact" class="collapse">   
                    

                                  
                                             

                                            <div class="row">
                                                <div class="col-sm-6">

                                                <?= $form->field($model, 'contact', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-id-card-o"></i>']]
                                                    ])->textInput(['class'=>'input-md']) ?>

                                                </div>
                                                    <div class="col-sm-6">    

                                                <?= $form->field($model, 'phone', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-phone"></i>']]
                                                    ])->textInput(['class'=>'input-md']) ?>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <?= $form->field($model, 'fax', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-fax"></i>']]
                                                    ])->textInput(['class'=>'input-md']) ?>
                                                    
                                                </div>
                                                <div class="col-sm-6">
                                                    <?= $form->field($model, 'email', [
                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-envelope"></i>']]
                                                    ])->textInput(['class'=>'input-md']); ?>
                                                </div>
                                            </div>
                                             
                                        </div>
                                        



                                        <div class="row">
                                            <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
                                                <a href="#location-section"  data-toggle="collapse" data-target="#vendor-payment">
                                                    <i class="fa fa-map" aria-hidden="true"></i> <?=Yii::t('common','Location')?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="well collapse" id="vendor-payment">
                                            <style type="text/css">

                                                .maps-render{
                                                    
                                                    widows: 100%;
                                                    height: 50px;
                                                    background-color: #fff;
                                                    background-image: url('images/google-maps-icon.jpg');
                                                    background-repeat: no-repeat;
                                                    background-size:  100px auto;
                                                    border:1px solid #ccc;
                                                    border-radius: 5px 5px 5px 5px;
                                                    margin-top: 25px;
                                                     
                                                     
                                                }
                                            </style>
                                            <div class="row " style="margin: 10px 0px 10px 0px;">
                                                 
                                                    <div class="col-xs-6">
                                                        <?php
                                                             if($model->country=='') $model->country = '213';

                                                            echo $form->field($model, 'country')->dropDownList(
                                                                ArrayHelper::map(\common\models\Countries::find()->orderBy(['country_name' => SORT_ASC])->all(),
                                                                                            'id',
                                                                                            'country_name'),[

                                                                                            'data-live-search'=> "true",
                                                                                            'class' => 'selectpicker',
                                                                                            'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','country')
                                                                                             
                                                                                        ] 
                                                            ) 
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <?= $form->field($model, 'latitude', [
                                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-map-marker"></i>']]
                                                                    ])->textInput(['maxlength' => true,'placeholder' => '13.736717']) ?>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <?= $form->field($model, 'longitude', [
                                                                    'addon' => ['prepend' => ['content'=>'<i class="fa fa-map-marker"></i>']]
                                                                    ])->textInput(['maxlength' => true,'placeholder' => '100.523186']) ?>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="maps-render"></div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="col-xs-6">
                                                        <div class="col-xs-12">

                                                       

                                                            <?php
                                                                echo $form->field($model, 'province')->textInput()
                                                            ?>

                                                        </div>
                                                        <div class="col-xs-12">

                                                            <?php
                                                            # $form->field($model, 'city')->dropDownList(['placeholder' => Yii::t('common','City')]) ?>
                                                           <?= $form->field($model, 'city')->textInput() ?> 
                                                        </div>
                                                    

                                                        <div class="col-xs-12">

                                                            <?php # $form->field($model, 'district')->dropDownList(['placeholder' => Yii::t('common','District')]) ?>

                                                           <?= $form->field($model, 'district')->textInput() ?>

                                                        </div>
                                                        <div class="col-sm-6 col-xs-12">
                                                            <?= $form->field($model, 'postcode')->textInput() ?>
                                                        </div>
                                                    </div>

                                                 

                                            </div>
                                        </div>    
                                


                 

               
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="Invoicing">
                        <div class="row vat-registration"> 
                            <div class="col-xs-6">
                                <?= $form->field($model, 'vat_regis', [
                                'addon' => ['prepend' => ['content'=>'<i class="fa fa-tag"></i>']]
                                ])->textInput(['class'=>'input-md']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'vatbus_posting_group')->dropDownList(['01' => Yii::t('common','Domestic'),'02'=> Yii::t('common','Foreign Trade')]); ?>
                            </div>
                        </div>
                            <!-- The timeline -->
                        <div class="row">
                            
                            <div class="col-sm-6">
                                <?= $form->field($model, 'vendor_posting_group')->dropDownList(['01' => Yii::t('common','General'),'02'=> Yii::t('common','Modern Trade')]); ?>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">

                                        <?= $form->field($model, 'payment_term')->dropDownList([
                                            '0' => Yii::t('common','Cash'),
                                            '7' => '7 '.Yii::t('common','Day'),
                                            '15' => '15 '.Yii::t('common','Day'),
                                            '30' => '30 '.Yii::t('common','Day'),
                                            '45' => '45 '.Yii::t('common','Day'),
                                            '60' => '60 '.Yii::t('common','Day'),
                                            '90' => '90 '.Yii::t('common','Day'),
                                         ]) ?>  

                                    </div>
                                    <div class="col-md-6">
                                        
                                    <?= $form->field($model, 'credit_limit', [
                                        'addon' => ['append' => ['content'=>'บาท']]
                                        ])->textInput(); ?>  

                                    </div>
                                </div>

                                

                                
                            </div>

                            <div class="col-md-6">

                                 
                                <div style="padding-top: 15px;">
                                
                              

             
                                
                                </div>
 
                                 
    
                            
                            </div>
                        </div>
                        

                    </div>
                    <!-- /.tab-pane -->

                   


                    
                    <!-- /.tab-pane -->
                 </div>
                 <!-- /.tab-content -->
             </div>

            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'create_date')->hiddenInput(['class'=>'input-md'])->label(false) ?>
                                 <?= $form->field($model, 'user_id')->hiddenInput(['class'=>'input-md'])->label(false) ?>
                </div>
            </div>

     





             <div class="form-group pull-right">
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>


        </div>
    </div>
</div>
</div>
 <?php ActiveForm::end(); ?>


<?= $this->render('../../../../views/setupnos/__modal'); ?>

<?php $this->registerJsFile('js/no.series.js?update=101017');?>


<?php 
$isNewRecord = $model->isNewRecord;
$js=<<<JS

var form = '#vendors-';
var formid = '#form-vendors';

$(document).ready(function(){

    //validateVendorType($('select#vendors-vender_type'));
    validateHeadOffice($('select#vendors-headoffice'));

    if('{$isNewRecord}'){
        genNumBerSeries('VD&dash=-');
    }
});

$('body').on('keydown','input#vendors-code',function(e){
    if(e.which ===13){
        genNumBerSeries($(this).val());
    }
});

function genNumBerSeries(str){
    $.ajax({
        url:'index.php?r=series%2Fajax-get-vendor-no&str='+str+'&count=03',
        type:'GET',
        async:false,
        success:function(getData){
            var obj = jQuery.parseJSON(getData);
            $('input#vendors-code').val(obj.code);
        },
    });
}

$('body').on('change','#vendors-vatbus_posting_group',function(){
    var field   = 'vatbus_posting_group';
    var cond    = $(this).val();
    ValidateSeries('Vendors','5','vendors',field,cond,'#vendors-code',true);
    $('div.field-vendors-code').find('i').attr('class','fa fa-check text-success');
    $('div.field-vendors-code').find('input').attr('class','input-md form-control text-green');
});


// Create Number Series
$('body').on('click','.ew-save-modal-common',function(){
    SeriesFormPost($('#numberseries-table_name').val(),
        $('#numberseries-field_name').val(),
        $('#numberseries-cond').val());
});

function readURL(input,div) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(div)
            .fadeOut(400, function() {
                $(div).attr('src', e.target.result);
            })
            .fadeIn(400);
        }
        reader.readAsDataURL(input.files[0]);
    }
}


function validateVendorType(data){
    if(data.val()!=4){
        $('.vat-registration').slideDown('fast');
    }else{
        $('.vat-registration').slideUp('fast');
    }
}

$('body').on('change','select#vendors-vender_type',function(){

    //validateVendorType($(this));

});


function validateHeadOffice(data){
    if(data.val()==0){
        $('.branch-section').slideDown('fast');
    }else{
        $('.branch-section').slideUp('fast');
    }
}

$('body').on('change','select#vendors-headoffice',function(){
    validateHeadOffice($(this));
});

$("#vendors-logo").change(function(){
    readURL(this,'#img-preview-logo');
});

$("#vendors-photo").change(function(){
    readURL(this,'#img-preview-photo');
});


JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');
