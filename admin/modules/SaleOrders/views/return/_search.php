<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\SaleOrders\models\SaleReturnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-return-header-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'no') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'source_id') ?>

    <?= $form->field($model, 'source_type') ?>

    <?php // echo $form->field($model, 'sale_address') ?>

    <?php // echo $form->field($model, 'bill_address') ?>

    <?php // echo $form->field($model, 'ship_address') ?>

    <?php // echo $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'ship_date') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'balance_befor_vat') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'percent_discount') ?>

    <?php // echo $form->field($model, 'update_status_date') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'paymentdue') ?>

    <?php // echo $form->field($model, 'sale_id') ?>

    <?php // echo $form->field($model, 'sales_people') ?>

    <?php // echo $form->field($model, 'vat_percent') ?>

    <?php // echo $form->field($model, 'ext_document') ?>

    <?php // echo $form->field($model, 'payment_term') ?>

    <?php // echo $form->field($model, 'vat_type') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'transport') ?>

    <?php // echo $form->field($model, 'update_by') ?>

    <?php // echo $form->field($model, 'update_date') ?>

    <?php // echo $form->field($model, 'include_vat') ?>

    <?php // echo $form->field($model, 'confirm') ?>

    <?php // echo $form->field($model, 'confirm_by') ?>

    <?php // echo $form->field($model, 'release_date') ?>

    <?php // echo $form->field($model, 'confirm_date') ?>

    <?php // echo $form->field($model, 'shiped_date') ?>

    <?php // echo $form->field($model, 'comments') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'session_id') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
