<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\modules\vendors\models\VendorsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vendors-search" style="position: absolute; margin-top: -10px;">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php //$form->field($model, 'id') ?>

    <div class="row">
        <div class="col-xs-offset-6">
            <div class=" ">
                <div class="col-xs-12">
                    <?=$form->field($model, 'Search',[
                                            'addon' => ['append' => ['content'=>'<i class="fa fa-search"></i>']]
                                            ])->label(Yii::t('common','Search')) ?>
                </div>
            </div>
            
        </div>
    </div>
    

    <?php //$form->field($model, 'address') ?>

    <?php //$form->field($model, 'address2') ?>

    <?php //$form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'postcode') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'vendor_posting_group') ?>

    <?php // echo $form->field($model, 'batbus_posting_group') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'homepage') ?>

    <?php // echo $form->field($model, 'headoffice') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'comp_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary hidden']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default hidden']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
