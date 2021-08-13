<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ItemJournal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-journal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'line_no')->textInput() ?>

    <?= $form->field($model, 'PostingDate')->textInput() ?>

    <?= $form->field($model, 'DocumentDate')->textInput() ?>

    <?= $form->field($model, 'TypeOfDocument')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SourceDocNo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DocumentNo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'SourceDoc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Quantity')->textInput() ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'city')->textInput() ?>

    <?= $form->field($model, 'province')->textInput() ?>

    <?= $form->field($model, 'postcode')->textInput() ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gps')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'update_date')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'comp_id')->textInput() ?>

    <?= $form->field($model, 'ship_to')->textInput() ?>

    <?= $form->field($model, 'ship_date')->textInput() ?>

    <?= $form->field($model, 'AdjustType')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
