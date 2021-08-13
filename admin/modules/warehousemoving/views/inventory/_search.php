<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\export\ExportMenu;
use kartik\widgets\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model admin\modules\warehousemoving\models\InventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="view-inventory-search">

    <div class="box-body">
        
        <p>Filter</p>
    
        <div class="row">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
            ]); ?>
            <div class="col-md-3">                 
                <?php
                    echo $form->field($model, 'dateRang', [
                        'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                        'options'=>['class'=>'drp-container form-group']
                    ])->widget(DateRangePicker::classname(), [
                        'useWithAddon'=>true
                    ])->label(false);

                ?>
            </div> 
            <div class="col-sm-3">
                <?php /* $form->field($model, 'TypeOfDocument')->dropDownList([
                    ''              => '-- '.Yii::t('common','All Activity').' --',
                    'Sale'          => Yii::t('common','Sale'),
                    'Invoice'       => Yii::t('common','Invoice'),
                    'Adjust'        => Yii::t('common','Adjust'),     
                    'Consumption'   => Yii::t('common','Consumption'),   
                    'Output'        => Yii::t('common','Output')                           
                    ])->label(false) */ ?>

                <?= $form->field($model, 'type')->widget(Select2::classname(),
                [
                    'data' => [                         
                        'Sale'          => Yii::t('common','Sale'),
                        'Invoice'       => Yii::t('common','Invoice'),
                        'Adjust'        => Yii::t('common','Adjust'),     
                        'Consumption'   => Yii::t('common','Consumption'),   
                        'Output'        => Yii::t('common','Output')                           
                    ],
                    'options' => [
                        'multiple'=>'multiple',                        
                        'placeholder' => '-- '.Yii::t('common','All Activity').' --',        
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,
                    ],                     
                ] )->label(false) ?>
            </div>  
            <div class="col-sm-2">
            <?= $form->field($model, 'vat_type')->dropDownList([
                                    ''  => '-- '.Yii::t('common','All Type').' --',
                                    '1' => 'Vat',
                                    '2' => 'No Vat',                                 
                                    ])->label(false) ?>
                
            </div>                 
            <div class="col-sm-1">
                <?= Html::submitButton('<i class="fas fa-search"></i> '.Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
                
            </div>
            <?php ActiveForm::end(); ?>
            <div class="col-md-3">
                <div class="text-right">
                     
                </div>
            </div>            
        </div>
        
    </div>

</div>
