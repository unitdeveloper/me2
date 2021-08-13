<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApInvoiceHeader */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ap-invoice-header-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'posting_date')->textInput() ?>

    <?= $form->field($model, 'no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ref_inv_header')->textInput() ?>

    <?= $form->field($model, 'cust_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cust_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cust_address2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'order_date')->textInput() ?>

    <?= $form->field($model, 'ship_date')->textInput() ?>

    <?= $form->field($model, 'cust_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sales_people')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sale_id')->textInput() ?>

    <?= $form->field($model, 'document_no_')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'doc_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'city')->textInput() ?>

    <?= $form->field($model, 'province')->textInput() ?>

    <?= $form->field($model, 'postcode')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'comp_id')->textInput() ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'percent_discount')->textInput() ?>

    <?= $form->field($model, 'vat_percent')->textInput() ?>

    <?= $form->field($model, 'payment_term')->textInput() ?>

    <?= $form->field($model, 'paymentdue')->textInput() ?>

    <?= $form->field($model, 'ext_document')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'include_vat')->textInput() ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'session_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'taxid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch_name')->textInput() ?>

    <?= $form->field($model, 'cn_reference')->textInput() ?>

    <?= $form->field($model, 'revenue')->textInput() ?>

    <?= $form->field($model, 'rf_revenue')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
