<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WithholdingTax */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withholding-tax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'vendor_id')->textInput() ?>

    <?= $form->field($model, 'vendor_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vat_regis')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comp_id')->textInput() ?>

    <?= $form->field($model, 'comp_address')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'user_name')->textInput() ?>

    <?= $form->field($model, 'choice_substitute')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
