<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\runingnoseries\models\SearchRunnose */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="runing-noseries-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'no_series') ?>

    <?= $form->field($model, 'start_date') ?>

    <?= $form->field($model, 'start_no') ?>

    <?= $form->field($model, 'last_no') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
