<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\AmphurSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="amphur-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'AMPHUR_ID') ?>

    <?= $form->field($model, 'AMPHUR_CODE') ?>

    <?= $form->field($model, 'AMPHUR_NAME') ?>

    <?= $form->field($model, 'GEO_ID') ?>

    <?= $form->field($model, 'PROVINCE_ID') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
