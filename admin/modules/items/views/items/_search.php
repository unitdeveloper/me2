<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\items\models\SearchItems */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="items-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-xs-offset-6">
            <div class=" ">
                <div class="col-xs-12">
                    <?=$form->field($model, 'description_th',[
                                            'addon' => ['append' => ['content'=>'<i class="fa fa-search"></i>']]
                                            ])->label(Yii::t('common','Search')) ?>
                </div>
            </div>

        </div>
    </div>
    <?php // $form->field($model, 'master_code') ?>

    <?php //  $form->field($model, 'Description') ?>

    <?php //  $form->field($model, 'UnitOfMeasure') ?>

    <?php // $form->field($model, 'Inventory') ?>

    <?php // $form->field($model, 'UnitCost') ?>

    <?php // echo $form->field($model, 'CostGP') ?>

    <?php // echo $form->field($model, 'ItemGroup') ?>

    <?php // echo $form->field($model, 'TypeOfProduct') ?>

    <?php // echo $form->field($model, 'CostingMethod') ?>

    <?php // echo $form->field($model, 'ProductionBom') ?>

    <?php // echo $form->field($model, 'StandardCost') ?>

    <?php // echo $form->field($model, 'PriceStructure_ID') ?>

    <!-- <div class="form-group">
        <?php // Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php // Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div> -->

    <?php ActiveForm::end(); ?>

</div>
