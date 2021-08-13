<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ViewInvoiceLine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="view-invoice-line-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_id')->textInput() ?>

    <?= $form->field($model, 'item')->textInput() ?>

    <?= $form->field($model, 'doc_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'line_no_')->textInput() ?>

    <?= $form->field($model, 'customer_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code_desc_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vat_percent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'line_discount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'source_doc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_line')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'session_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cn_reference')->textInput() ?>

    <?= $form->field($model, 'posted')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
