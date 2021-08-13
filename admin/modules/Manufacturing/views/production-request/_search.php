<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\PdrSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'create_date') ?>

    <?= $form->field($model, 'item') ?>

    <?= $form->field($model, 'quantity') ?>

    <?= $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'no') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <?php // echo $form->field($model, 'posting_date') ?>

    <?php // echo $form->field($model, 'request_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
