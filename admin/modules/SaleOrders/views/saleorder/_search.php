<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\SaleOrders\models\SalehearderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-header-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Isearch') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'customer_id') ?>

    <?php // echo $form->field($model, 'sale_address') ?>

    <?php // echo $form->field($model, 'bill_address') ?>

    <?php // echo $form->field($model, 'ship_address') ?>

    <?php // echo $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'ship_date') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
