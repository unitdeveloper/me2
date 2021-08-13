<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseMoving */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-moving-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'line_no')->textInput() ?>

    <?= $form->field($model, 'DocumentNo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PostingDate')->textInput() ?>

    <?= $form->field($model, 'TypeOfDocument')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SourceDoc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SourceDocNo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ItemNo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Quantity')->textInput() ?>

    <?= $form->field($model, 'QtyToMove')->textInput() ?>

    <?= $form->field($model, 'QtyMoved')->textInput() ?>

    <?= $form->field($model, 'QtyOutstanding')->textInput() ?>

    <?= $form->field($model, 'DocumentDate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
