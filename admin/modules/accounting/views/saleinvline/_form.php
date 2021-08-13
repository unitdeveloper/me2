<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceLine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-invoice-line-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'doc_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'line_no_')->textInput() ?>

    <?= $form->field($model, 'customer_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code_desc_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vat_percent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'line_discount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
