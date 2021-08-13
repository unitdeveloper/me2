<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\Purchase\models\PurchaseLineSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-line-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'source_id') ?>

    <?= $form->field($model, 'source_no') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'items_no') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'measure') ?>

    <?php // echo $form->field($model, 'unitcost') ?>

    <?php // echo $form->field($model, 'lineamount') ?>

    <?php // echo $form->field($model, 'linediscount') ?>

    <?php // echo $form->field($model, 'expeted_date') ?>

    <?php // echo $form->field($model, 'planned_date') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
