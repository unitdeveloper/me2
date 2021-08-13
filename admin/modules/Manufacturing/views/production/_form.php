<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductionOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-order-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-6">

            <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>

            <div class="row">
                <div class="col-xs-6">
                    <?= $form->field($model, 'create_date')->textInput() ?>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'order_date')->textInput() ?>
                </div>
            </div>

            
    
            
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'order_id')->textInput() ?>
        </div>
        
    </div>
    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="form-group" style="
                position: fixed;
                bottom: -15px;
                border-top: 1px solid #ccc;
                width: 100%;
                background-color: rgba(239, 239, 239, 0.9);
                padding: 10px 10px 15px 10px;
                right: 0px;
                text-align: right;
                z-index:2000;
                ">
                <?= Html::submitButton('<i class="fa fa-save"></i> '.Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
