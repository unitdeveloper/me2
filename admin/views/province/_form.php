<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\models\Zone;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Province */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="province-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'PROVINCE_CODE')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
           <?= $form->field($model, 'PROVINCE_NAME')->textInput(['maxlength' => true]) ?> 
        </div>
    </div>

    

    <div class="row">
        <div class="col-sm-6">
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
        <div class="col-sm-6">
            <?= $form->field($model, 'GEO_SUB')->dropDownList([
                '0' => Yii::t('common','None') ,
                '1' => Yii::t('common','Upper _'),
                '2' => Yii::t('common','Central _'),
                '3' => Yii::t('common','Lower _')]) ?>

        </div>
    </div>

    

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
        </div>
         
    </div>
    <div class="row">
                            <div class="col-md-6">
                                 <?php
                                 if($model->country=='') $model->country = '213';

                                            // echo $form->field($model, 'country')->dropDownList(
                                            //     ArrayHelper::map(\common\models\Countries::find()->orderBy(['country_name' => SORT_ASC])->all(),
                                            //                                 'id',
                                            //                                 'country_name'),[
                                            //                                     'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','country')
                                                                             
                                            //                                 ] 
                                            // ) 
                                        ?>

                                <?= $form->field($model, 'country')->widget(Select2::classname(),
                                                [
                                                    'data'              => arrayHelper::map(\common\models\Countries::find()->all(),'id','country_name'),
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
    


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
