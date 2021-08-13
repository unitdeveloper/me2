<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;


use common\models\Province;
use common\models\District;
use common\models\Amphur;
use common\models\Zipcode;
/* @var $this yii\web\View */
/* @var $model common\models\Zipcode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="zipcode-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'DISTRICT_CODE')->textInput(['maxlength' => true]) ?>

    <div class="row">
        
        <div class="col-sm-4">
            <?= $form->field($model, 'PROVINCE_ID')->widget(Select2::classname(),
                [
                    'data'              => arrayHelper::map(Province::find()->all(),'PROVINCE_ID','PROVINCE_NAME'),
                    'language'          => 'th',
                    'options'           => ['placeholder' => 'Select a state ...','id' => 'ddl-province'],
                    'pluginOptions'     => [
                        'allowClear'    => true
                    ],
                ]
                );
            ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'AMPHUR_ID')->widget(Select2::classname(),
                    [ 
                        'data'              => arrayHelper::map(Amphur::find()->where(['PROVINCE_ID' => $model->PROVINCE_ID])->all(),'AMPHUR_ID','AMPHUR_NAME'),
                        'language'          => 'th',
                        'options'           => ['placeholder' => 'Select a state ...','id' => 'ddl-amphur'],
                        'pluginOptions'     => [
                            'allowClear'    => true
                        ],
                    ]
                );
            ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'DISTRICT_ID')->widget(Select2::classname(),
                [
                    'data'              => arrayHelper::map(District::find()
                                            ->where(['AMPHUR_ID' => $model->AMPHUR_ID])
                                            ->andWhere(['PROVINCE_ID' => $model->PROVINCE_ID])
                                            ->all(),'DISTRICT_ID','DISTRICT_NAME'),
                    'language'          => 'th',
                    'options'           => ['placeholder' => 'Select a state ...','id' => 'ddl-district'],
                    'pluginOptions'     => [
                        'allowClear'    => true
                    ],
                ]
            );
            ?>
        </div>
        
    </div>
    


    <div class="row">
        <div class="col-sm-4">
            <?php // $form->field($model, 'PROVINCE_ID')->textInput(['maxlength' => true]) ?>

            <?php // $form->field($model, 'AMPHUR_ID')->textInput(['maxlength' => true]) ?>

            <?php // $form->field($model, 'DISTRICT_ID')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'ZIPCODE')->textInput(['maxlength' => true]) ?>

            

            
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

 

    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJS("
    $('body').on('change','#ddl-province',function(){
        getCityFromProvince($(this).val());
    }); 
    $('body').on('change','#ddl-amphur',function(){
        getDistrictFromCity($(this).val());
    }); 

    function getCityFromProvince(province)
    {

        $.ajax({

            url:'index.php?r=ajax/city-from-province&province='+province,
            type: 'POST',
            data: {province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#ddl-amphur').html('');
                $.each( obj, function( key, value ) {

                    $('#ddl-amphur').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));



                });

            }

        });

    }

    function getDistrictFromCity(city)
    {
        $.ajax({

            url:\"index.php?r=ajax/get-district-city&district=\"+city+\"&city=\"+city,
            type: 'GET',
            data: {city:city},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#ddl-district').html('');
                $.each( obj, function( key, value ) {

                   $('#ddl-district').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                });



            }

        });
    }

");

?>