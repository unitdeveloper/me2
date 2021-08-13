<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ImportFile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="import-file-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_qty')->textInput() ?>

    <?= $form->field($model, 'position_qty_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_discount')->textInput() ?>

    <?= $form->field($model, 'position_discount_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_total')->textInput() ?>

    <?= $form->field($model, 'position_total_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'keyword_po')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'auto_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'find_code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
