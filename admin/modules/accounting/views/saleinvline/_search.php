<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\accounting\models\SaleinvlineSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-invoice-line-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'doc_no_') ?>

    <?= $form->field($model, 'line_no_') ?>

    <?= $form->field($model, 'customer_no_') ?>

    <?= $form->field($model, 'code_no_') ?>

    <?php // echo $form->field($model, 'code_desc_') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'unit_price') ?>

    <?php // echo $form->field($model, 'vat_percent') ?>

    <?php // echo $form->field($model, 'line_discount') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
