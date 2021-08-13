<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-invoice-header-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'no_') ?>

    <?= $form->field($model, 'cust_no_') ?>

    <?= $form->field($model, 'cust_name_') ?>

    <?= $form->field($model, 'cust_address') ?>

    <?php // echo $form->field($model, 'cust_address2') ?>

    <?php // echo $form->field($model, 'posting_date') ?>

    <?php // echo $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'ship_date') ?>

    <?php // echo $form->field($model, 'cust_code') ?>

    <?php // echo $form->field($model, 'sales_people') ?>

    <?php // echo $form->field($model, 'document_no_') ?>

    <?php // echo $form->field($model, 'doc_type') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
