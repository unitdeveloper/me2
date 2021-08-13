<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SalesHasCustomer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-has-customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sale_id')->textInput() ?>

    <?= $form->field($model, 'cust_id')->textInput() ?>

    <?= $form->field($model, 'customer_group')->textInput() ?>

    <?= $form->field($model, 'comp_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
