<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\accounting\models\PayableHeaderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ap-invoice-header-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'posting_date') ?>

    <?= $form->field($model, 'no_') ?>

    <?= $form->field($model, 'ref_inv_header') ?>

    <?= $form->field($model, 'cust_no_') ?>

    <?php // echo $form->field($model, 'cust_name_') ?>

    <?php // echo $form->field($model, 'cust_address') ?>

    <?php // echo $form->field($model, 'cust_address2') ?>

    <?php // echo $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'ship_date') ?>

    <?php // echo $form->field($model, 'cust_code') ?>

    <?php // echo $form->field($model, 'sales_people') ?>

    <?php // echo $form->field($model, 'sale_id') ?>

    <?php // echo $form->field($model, 'document_no_') ?>

    <?php // echo $form->field($model, 'doc_type') ?>

    <?php // echo $form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'postcode') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <?php // echo $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'percent_discount') ?>

    <?php // echo $form->field($model, 'vat_percent') ?>

    <?php // echo $form->field($model, 'payment_term') ?>

    <?php // echo $form->field($model, 'paymentdue') ?>

    <?php // echo $form->field($model, 'ext_document') ?>

    <?php // echo $form->field($model, 'include_vat') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'session_id') ?>

    <?php // echo $form->field($model, 'order_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'taxid') ?>

    <?php // echo $form->field($model, 'branch') ?>

    <?php // echo $form->field($model, 'branch_name') ?>

    <?php // echo $form->field($model, 'cn_reference') ?>

    <?php // echo $form->field($model, 'revenue') ?>

    <?php // echo $form->field($model, 'rf_revenue') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
