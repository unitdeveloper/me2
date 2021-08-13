<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RuningNoseries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="runing-noseries-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'no_series')->textInput() ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'start_no')->textInput() ?>

    <?= $form->field($model, 'last_no')->textInput() ?>

    <?= $form->field($model, 'comp_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
