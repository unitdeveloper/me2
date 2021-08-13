<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectControl */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="project-control-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'name')->textInput() ?></div>
    </div>

    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'place')->textInput(['maxlength' => true]) ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'budget')->textInput(['type' => 'number' ,'step' => 'any']) ?></div>
    </div>
    

    <?= $form->field($model, 'description')->textarea(['rows' => '6']) ?>

    

    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'start_date')->textInput(['type' => 'date']) ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'end_date')->textInput(['type' => 'date']) ?></div>
    </div>
    

    <div class="row">
        <div class="col-xs-6"><?php //$form->field($model, 'create_date')->textInput(['type' => 'datetime-local']) ?></div>
    </div>

    

 
    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
