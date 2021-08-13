<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\Purchase\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-header-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'line_no_') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?= $form->field($model, 'vendor_name') ?>

    <?= $form->field($model, 'address') ?>

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

    <?php // echo $form->field($model, 'vat_type') ?>

    <?php // echo $form->field($model, 'payment_term') ?>

    <?php // echo $form->field($model, 'payment_due') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'project_name') ?>

    <?php // echo $form->field($model, 'session_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<link href="css/smart_wizard.css" rel="stylesheet" type="text/css">
<link href="css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css">

<div   class="sw-main sw-theme-arrows" role="tabpanel">
    <ul class="nav nav-tabs step-anchor" role="tablist">
        <li class="nav-item done" role="presentation" ><a href="#step1" aria-controls="step1" role="tab" data-toggle="tab">Upload PDF<br><small>เลือกไฟล์ PDF ที่ต้องการ</small></a></li>
        <li class="nav-item active" role="presentation"><a href="#step2" aria-controls="step2" role="tab" data-toggle="tab">Verify Your Data<br><small>ตรวจสอบข้อมูล และยืนยัน</small></a></li>
        <li class="nav-item" role="presentation"><a href="#step3" aria-controls="step2" role="tab" data-toggle="tab">Verify Your Data<br><small>ตรวจสอบข้อมูล และยืนยัน</small></a></li>        
    </ul>        
    <div class="sw-container tab-content" >
        <div id="step1" class="step-content tab-pane active"  role="tabpanel">        
            Setp 1         
        </div>
        <div id="step2" class="step-content tab-pane"  role="tabpanel">                
            Setp 2
        </div>  
        <div id="step3" class="step-content tab-pane"  role="tabpanel">                
            Setp 3
        </div>          
    </div>        
</div>