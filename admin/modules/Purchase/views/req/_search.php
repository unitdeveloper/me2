<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\Purchase\models\ReqHeaderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-req-header-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'line_no_') ?>

    <?= $form->field($model, 'doc_no') ?>

    <?= $form->field($model, 'series_id') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?php // echo $form->field($model, 'vendor_name') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'address2') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'ext_document') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'taxid') ?>

    <?php // echo $form->field($model, 'address_id') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'percent_discount') ?>

    <?php // echo $form->field($model, 'vat_type') ?>

    <?php // echo $form->field($model, 'include_vat') ?>

    <?php // echo $form->field($model, 'vat_percent') ?>

    <?php // echo $form->field($model, 'payment_term') ?>

    <?php // echo $form->field($model, 'payment_due') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'project_name') ?>

    <?php // echo $form->field($model, 'session_id') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <?php // echo $form->field($model, 'ref_no') ?>

    <?php // echo $form->field($model, 'purchaser') ?>

    <?php // echo $form->field($model, 'delivery_date') ?>

    <?php // echo $form->field($model, 'withholdTaxSwitch') ?>

    <?php // echo $form->field($model, 'withholdTax') ?>

    <?php // echo $form->field($model, 'withholdAttach') ?>

    <?php // echo $form->field($model, 'project') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
