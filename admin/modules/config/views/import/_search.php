<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\config\model\ImportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="import-file-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'position_qty') ?>

    <?= $form->field($model, 'position_qty_num') ?>

    <?php // echo $form->field($model, 'position_discount') ?>

    <?php // echo $form->field($model, 'position_discount_num') ?>

    <?php // echo $form->field($model, 'position_total') ?>

    <?php // echo $form->field($model, 'position_total_num') ?>

    <?php // echo $form->field($model, 'keyword_po') ?>

    <?php // echo $form->field($model, 'auto_remark') ?>

    <?php // echo $form->field($model, 'find_code') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
