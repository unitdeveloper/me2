<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Itemset */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="itemset-form">

    <div class="row">
        <div class="col-xs-6">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'detail')->textInput() ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'priority')->textInput(['type' => 'number']) ?>
            <?= $form->field($model, 'status')->dropDownList([ '0' => 'Disable', '1'=> 'Enable' ],['prompt' => '', 'value' => '1']) ?>   
        </div>
    </div>
    

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'comp_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
