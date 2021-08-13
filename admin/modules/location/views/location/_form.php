<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\location */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput() ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?php $model->defaultlocation == 1 ? true : false ; ?>
    <?= $form->field($model, 'defaultlocation')->checkbox(['label' => Yii::t('common','Default')]) ?>

    <?php if($model->status=='') $model->status = 1; ?>
    <?= $form->field($model, 'status')->dropDownList([ '0' => 'Disable', '1'=>'Enable', '2'=>'Block', ], ['prompt' => '']) ?>
    

    <div class="form-group text-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : '<i class="fas fa-save"></i> '.Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
