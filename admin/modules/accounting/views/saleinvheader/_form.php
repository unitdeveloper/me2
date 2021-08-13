<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-invoice-header-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cust_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cust_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cust_address2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'posting_date')->textInput() ?>

    <?= $form->field($model, 'order_date')->textInput() ?>

    <?= $form->field($model, 'ship_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
