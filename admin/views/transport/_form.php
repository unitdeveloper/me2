<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
/* @var $this yii\web\View */
/* @var $model common\models\TransportList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-list-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <div class="col-xs-6">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>            

            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>            

        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'nick_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'comp_id')->hiddenInput(['maxlength' => true])->label(false) ?>      
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> '.Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
