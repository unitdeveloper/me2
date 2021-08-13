<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\accounting\models\AccountChart */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chart-of-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'AccNo') ?>

    <?= $form->field($model, 'AccName') ?>

    <?= $form->field($model, 'AccDesc') ?>

    <?= $form->field($model, 'Incom_Balance') ?>

    <?php // echo $form->field($model, 'AccType') ?>

    <?php // echo $form->field($model, 'Totaling') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
