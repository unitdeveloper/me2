<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\ProdBomSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bom-header-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'item_set') ?>

    <?php // echo $form->field($model, 'max_val') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'multiple') ?>

    <?php // echo $form->field($model, 'format_gen') ?>

    <?php // echo $form->field($model, 'format_type') ?>

    <?php // echo $form->field($model, 'running_digit') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
