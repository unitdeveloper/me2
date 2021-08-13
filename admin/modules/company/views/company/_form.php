<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="row">

            <div class="col-md-2">

                <div class="row text-center">

                    <div class="col-xs-6 col-sm-12">
                        <h4><?= Yii::t('common','Icon')?></h4>  
                        <?= $form->field($model, 'logo',['options' => ['class' => 'btn btn-file company-logo']])->fileInput(['id' => 'company-logo'])->label(false) ?>

                    </div>

                    <div class="col-xs-6 col-sm-12">    
                        <h4><?= Yii::t('common','Brand')?></h4>
                        <?= $form->field($model, 'brand_logo',['options' => ['class' => 'btn btn-file company-brand']])->fileInput(['id' => 'company-brand'])->label(false) ?>

                    </div>

                    <div class="col-xs-6 col-sm-12">            
                        <h4><?= Yii::t('common','Maps')?></h4>
                        <?= $form->field($model, 'maps',['options' => ['class' => 'btn btn-file company-maps']])->fileInput(['id' => 'company-maps'])->label(false) ?>

                    </div>

                </div>

            </div>

            <div class="col-md-10">
            
                <div class="box box-info content">
                    
                    <div class="row">

                        <div class="col-sm-6">

                            <?= $form->field($model, 'name')->textInput() ?>

                            <?= $form->field($model, 'name_en')->textInput() ?>

                            <?= $form->field($model, 'address_en')->textInput() ?>
                            
                            <?= $form->field($model, 'address')->textInput() ?>

                            <?= $form->field($model, 'address2')->textInput() ?>

                            <?= $form->field($model, 'city')->textInput() ?>

                            <?= $form->field($model, 'location')->textInput() ?>

                            <?= $form->field($model, 'postcode')->textInput() ?>

                            <?= $form->field($model, 'country')->textInput() ?>

                            <?= $form->field($model, 'mobile')->textInput() ?>

                            <?= $form->field($model, 'phone')->textInput() ?>

                            <?= $form->field($model, 'fax')->textInput() ?>
                            
                            <?= $form->field($model, 'vat_address')->textInput() ?>

                            <?= $form->field($model, 'vat_city')->textInput() ?>

                            <?= $form->field($model, 'vat_location')->textInput() ?>

                        </div>

                        <div class="col-sm-6">
                            <?= $form->field($model, 'acronym')->textInput() ?>
                            
                            <?= $form->field($model, 'vat_register')->textInput() ?>

                            <?= $form->field($model, 'headoffice')->dropDownList(['0' => Yii::t('common','Branch'), '1' => Yii::t('common','Head Office')]) ?>

                            <?= $form->field($model, 'brand')->textInput()->label(Yii::t('common','Brand Name')) ?>

                            <div class="form-group text-right">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>

                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>



<?PHP 
$Yii = 'Yii';
$js=<<<JS


    $(document).ready(function(){

        $('div.company-logo').fadeOut(500, function() {
        $('div.company-logo').prepend('<img class=\"img-responsive img-rounded img-thumbnail\" src="{$model->logoViewer}" id=\"img-preview-logo\">');    
        }).fadeIn(500);  

        $('div.company-brand').fadeOut(800, function() {
        $('div.company-brand').prepend('<img class=\"img-responsive img-rounded img-thumbnail\" src="{$model->brandViewer}" id=\"img-preview-brand\">');    
        }).fadeIn(800);  

        $('div.company-maps').fadeOut(800, function() {
        $('div.company-maps').prepend('<img class=\"img-responsive img-rounded img-thumbnail\" src="{$model->mapsViewer}" id=\"img-preview-maps\">');    
        }).fadeIn(800);  

    });

    const readURL = (input,div) => {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) { $(div).fadeOut(400, function() { $(div).attr('src', e.target.result); }).fadeIn(400); }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#company-logo").change(function(){
        readURL(this,'#img-preview-logo');
    });

    $("#company-brand").change(function(){
        readURL(this,'#img-preview-brand');
    });

    $("#company-maps").change(function(){
        readURL(this,'#img-preview-maps');
    });

JS;

$this->registerJS($js,\yii\web\View::POS_END);

 ?>