<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMystore */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-mystore-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item')->textInput() ?>

    <?= $form->field($model, 'item_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'master_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'size')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Photo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail4')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thumbnail5')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'online')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_modify')->textInput() ?>

    <?= $form->field($model, 'user_added')->textInput() ?>

    <?= $form->field($model, 'comp_id')->textInput() ?>

    <?= $form->field($model, 'date_added')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_modify')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit_cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sale_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit_of_measure')->textInput() ?>

    <?= $form->field($model, 'clone')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
