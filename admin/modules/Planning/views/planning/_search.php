<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\Planning\models\ItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-mystore-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'item') ?>

    <?= $form->field($model, 'item_no') ?>

    <?= $form->field($model, 'barcode') ?>

    <?= $form->field($model, 'master_code') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'name_en') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'size') ?>

    <?php // echo $form->field($model, 'Photo') ?>

    <?php // echo $form->field($model, 'thumbnail1') ?>

    <?php // echo $form->field($model, 'thumbnail2') ?>

    <?php // echo $form->field($model, 'thumbnail3') ?>

    <?php // echo $form->field($model, 'thumbnail4') ?>

    <?php // echo $form->field($model, 'thumbnail5') ?>

    <?php // echo $form->field($model, 'online') ?>

    <?php // echo $form->field($model, 'user_modify') ?>

    <?php // echo $form->field($model, 'user_added') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <?php // echo $form->field($model, 'date_added') ?>

    <?php // echo $form->field($model, 'date_modify') ?>

    <?php // echo $form->field($model, 'unit_cost') ?>

    <?php // echo $form->field($model, 'sale_price') ?>

    <?php // echo $form->field($model, 'qty_per_unit') ?>

    <?php // echo $form->field($model, 'unit_of_measure') ?>

    <?php // echo $form->field($model, 'clone') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'count_stock') ?>

    <?php // echo $form->field($model, 'safety_stock') ?>

    <?php // echo $form->field($model, 'reorder_point') ?>

    <?php // echo $form->field($model, 'minimum_stock') ?>

    <?php // echo $form->field($model, 'stock_adjust') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
