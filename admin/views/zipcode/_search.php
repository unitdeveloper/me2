<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\ZipcodeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="zipcode-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ZIPCODE_ID') ?>

    <?= $form->field($model, 'DISTRICT_CODE') ?>

    <?= $form->field($model, 'PROVINCE_ID') ?>

    <?= $form->field($model, 'AMPHUR_ID') ?>

    <?= $form->field($model, 'DISTRICT_ID') ?>

    <?php // echo $form->field($model, 'ZIPCODE') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
