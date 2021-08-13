<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\SaleOrders\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-line-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_no') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'item_no') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'unit_measure') ?>

    <?php // echo $form->field($model, 'unit_price') ?>

    <?php // echo $form->field($model, 'line_amount') ?>

    <?php // echo $form->field($model, 'line_discount') ?>

    <?php // echo $form->field($model, 'need_ship_date') ?>

    <?php // echo $form->field($model, 'quantity_to_ship') ?>

    <?php // echo $form->field($model, 'quantity_shipped') ?>

    <?php // echo $form->field($model, 'quantity_to_invoice') ?>

    <?php // echo $form->field($model, 'quantity_invoiced') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <?php // echo $form->field($model, 'api_key') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
