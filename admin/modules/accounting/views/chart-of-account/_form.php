<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ChartOfAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chart-of-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'AccNo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AccName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AccDesc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Incom_Balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AccType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Totaling')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
