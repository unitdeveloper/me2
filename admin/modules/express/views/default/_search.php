<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\express\models\VatSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="isvat-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'TREC') ?>

    <?= $form->field($model, 'VATTYP') ?>

    <?= $form->field($model, 'RECTYP') ?>

    <?= $form->field($model, 'VATPRD') ?>

    <?php // echo $form->field($model, 'LATE') ?>

    <?php // echo $form->field($model, 'VATDAT') ?>

    <?php // echo $form->field($model, 'DOCDAT') ?>

    <?php // echo $form->field($model, 'DOCNUM') ?>

    <?php // echo $form->field($model, 'REFNUM') ?>

    <?php // echo $form->field($model, 'NEWNUM') ?>

    <?php // echo $form->field($model, 'DEPCOD') ?>

    <?php // echo $form->field($model, 'DESCRP') ?>

    <?php // echo $form->field($model, 'AMT01') ?>

    <?php // echo $form->field($model, 'VAT01') ?>

    <?php // echo $form->field($model, 'AMT02') ?>

    <?php // echo $form->field($model, 'VAT02') ?>

    <?php // echo $form->field($model, 'AMTRAT0') ?>

    <?php // echo $form->field($model, 'REMARK') ?>

    <?php // echo $form->field($model, 'SELF_ADDED') ?>

    <?php // echo $form->field($model, 'HAD_MODIFY') ?>

    <?php // echo $form->field($model, 'DOCSTAT') ?>

    <?php // echo $form->field($model, 'TAXID') ?>

    <?php // echo $form->field($model, 'ORGNUM') ?>

    <?php // echo $form->field($model, 'PRENAM') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
