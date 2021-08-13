<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\warehousemoving\models\ShipmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-moving-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'line_no') ?>

    <?= $form->field($model, 'DocumentNo') ?>

    <?= $form->field($model, 'PostingDate') ?>

    <?= $form->field($model, 'TypeOfDocument') ?>

    <?php // echo $form->field($model, 'SourceDoc') ?>

    <?php // echo $form->field($model, 'SourceDocNo') ?>

    <?php // echo $form->field($model, 'ItemNo') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <?php // echo $form->field($model, 'Quantity') ?>

    <?php // echo $form->field($model, 'QtyToMove') ?>

    <?php // echo $form->field($model, 'QtyMoved') ?>

    <?php // echo $form->field($model, 'QtyOutstanding') ?>

    <?php // echo $form->field($model, 'DocumentDate') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
