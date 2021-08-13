<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

use common\models\Zone;
use kartik\select2\Select2;
use common\models\Province;
use common\models\Amphur;
/* @var $this yii\web\View */
/* @var $model common\models\District */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="district-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'DISTRICT_CODE')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'DISTRICT_NAME')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    

    <div class="row">
        <div class="col-sm-8">          
            <?= $form->field($model, 'AMPHUR_ID')->widget(Select2::classname(),
                [
                    'data'              => arrayHelper::map(Amphur::find()->all(),'AMPHUR_ID','AMPHUR_NAME'),
                    'language'          => 'th',
                    'options'           => ['placeholder' => Yii::t('common','Select')],
                    'pluginOptions'     => [
                        'allowClear'    => true
                    ],
                ]
                );
            ?>
        </div>
    </div>

     
    
    <div class="row">
        <div class="col-sm-8">
            <?= $form->field($model, 'PROVINCE_ID')->widget(Select2::classname(),
                [
                    'data'              => arrayHelper::map(Province::find()->all(),'PROVINCE_ID','PROVINCE_NAME'),
                    'language'          => 'th',
                    'options'           => ['placeholder' => Yii::t('common','Select')],
                    'pluginOptions'     => [
                        'allowClear'    => true
                    ],
                ]
                );
            ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'GEO_ID')->dropDownList(
                ArrayHelper::map(Zone::find()->all(),'id','name'),
                [

                    //'data-live-search'=> "true",
                    'class' => 'form-control',
                    //'prompt' => Yii::t('common','Zone'),
                    //'multiple'=>"multiple",
                    
                ] 
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
