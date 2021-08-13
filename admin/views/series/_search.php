<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\SeriesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="number-series-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'starting_no') ?>

    <?= $form->field($model, 'ending_no') ?>

    <?= $form->field($model, 'last_date') ?>

    <?php // echo $form->field($model, 'last_no') ?>

    <?php // echo $form->field($model, 'default_no') ?>

    <?php // echo $form->field($model, 'manual_nos') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
