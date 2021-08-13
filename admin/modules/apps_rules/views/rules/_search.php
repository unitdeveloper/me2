<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\apps_rules\models\SearchRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="apps-rules-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'permission_id') ?>

    <?= $form->field($model, 'comp_id') ?>

    <?= $form->field($model, 'date_created') ?>

    <?php // echo $form->field($model, 'sales_people') ?>

    <?php // echo $form->field($model, 'rules_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
