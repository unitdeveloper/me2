<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseLine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-line-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'source_id')->textInput() ?>

    <?= $form->field($model, 'source_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'items_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quantity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'measure')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unitcost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lineamount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'linediscount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'expeted_date')->textInput() ?>

    <?= $form->field($model, 'planned_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
