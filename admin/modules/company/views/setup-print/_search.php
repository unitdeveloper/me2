<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\company\models\PrintSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="print-page-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'logo') ?>

    <?= $form->field($model, 'header') ?>

    <?= $form->field($model, 'header_height') ?>

    <?php // echo $form->field($model, 'footer_height') ?>

    <?php // echo $form->field($model, 'body_height') ?>

    <?php // echo $form->field($model, 'footer') ?>

    <?php // echo $form->field($model, 'signature') ?>

    <?php // echo $form->field($model, 'pagination') ?>

    <?php // echo $form->field($model, 'paper_size') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
