<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Isvat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="isvat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'TREC')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'VATTYP')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'RECTYP')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'VATPRD')->textInput() ?>

    <?= $form->field($model, 'LATE')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'VATDAT')->textInput() ?>

    <?= $form->field($model, 'DOCDAT')->textInput() ?>

    <?= $form->field($model, 'DOCNUM')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'REFNUM')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'NEWNUM')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DEPCOD')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DESCRP')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AMT01')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'VAT01')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AMT02')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'VAT02')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AMTRAT0')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'REMARK')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SELF_ADDED')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'HAD_MODIFY')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DOCSTAT')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TAXID')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ORGNUM')->textInput() ?>

    <?= $form->field($model, 'PRENAM')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
