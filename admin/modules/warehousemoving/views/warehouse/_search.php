<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $model admin\modules\warehousemoving\models\WarehouseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-moving-search">

    <div class="box-body">
        
        <p>Filter</p>
    
        <div class="row">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
            ]); ?>
            <div class="col-md-4">                 
                <?php
                    $startDate  = '';
                    $endDate    = '';
                    if(isset($_GET['fil_from_date']))    
                    {
                        if($_GET['fil_from_date']!='') $startDate   = date('d-m-Y',strtotime($_GET['fil_from_date']));
                    }
                    if(isset($_GET['fil_to_date'])){      
                        if($_GET['fil_to_date']!='') $endDate     = date('d-m-Y',strtotime($_GET['fil_to_date']));
                    }

                    echo DatePicker::widget([
                        'name' => 'fil_from_date',
                        'value' => $startDate,
                        'type' => DatePicker::TYPE_RANGE,
                        'name2' => 'fil_to_date',
                        'value2' => $endDate,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd'
                        ],
                        'options' => [
                            'autocomplete' => 'off'
                        ],
                        'options2' => [
                            'autocomplete' => 'off'
                        ],
                        'pluginEvents' => [
                            //"changeDate" => "function(e) { ReloadSearch(); }",
                        ],
                    ]);

                ?>
            </div>
            <div class="col-sm-3">
                <select name="Filter" class="form-control" id="wh-filter-change">
                    <option value='SV' <?=(@$_GET['Filter']=='SV')? 'selected': '';?>>Sale Shipment (Vat)</option>
                    <option value='SN' <?=(@$_GET['Filter']=='SN')? 'selected': '';?>>Sale Shipment (No Vat)</option>
                    <option value='OP' <?=(@$_GET['Filter']=='OP')? 'selected': '';?>>Output</option>
                    <option value='CO' <?=(@$_GET['Filter']=='CO')? 'selected': '';?>>Consumption</option>
                </select>     
            </div>                 
            <div class="col-sm-2">
                <?= Html::submitButton('<i class="fas fa-search"></i> '.Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?php // Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="col-md-3">
                <div class="text-right">
                <?php
                    echo ExportMenu::widget([
                                'dataProvider' => $dataProvider,
                                'columns' => [                    
                                    'PostingDate',                    
                                    'SourceDocNo', 
                                    [
                                        'label' => Yii::t('common','DocumentNo'),
                                        'format' => 'raw',
                                        //'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model){                               
                                            return $model->DocumentNo;        
                                        }
                                    ],                   
                                    [
                                        'attribute' => 'ItemNo',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model)
                                        {
                                            if($model->item!=''){
                                                return $model->itemstb->master_code;
                                            }
                                            
                                        }
                                    ],
                                    'itemstb.description_th',
                                    // 'Quantity',
                                    [
                                        'attribute' => 'Quantity',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model)
                                        {
                                            //return $model->Quantity * (int)$model->qty_per_unit;
                                            $qty_per = ($model->qty_per_unit)? $model->qty_per_unit : 1;
                                            return (int)$model->Quantity * $qty_per;
                                        }
                                    ],
                                    'locations.name',
                                    [
                                        'attribute' => 'unit_price',
                                        'format' => 'raw',
                                        'contentOptions' => ['class' => 'text-right'],
                                        'value' => function($model)
                                        {
                                            return $model->unit_price;
                                        }
                                    ]
                                ],
                                'columnSelectorOptions'=>[
                                    'label' => 'Columns',
                                    'class' => 'btn btn-success-ew'
                                ],
                                'fontAwesome' => true,
                                'dropdownOptions' => [
                                    'label' => 'Export All',
                                    'class' => 'btn btn-primary-ew'
                                ],
                                'exportConfig' => [
                                    ExportMenu::FORMAT_HTML => false,
                                ],
                                'styleOptions' => [
                                    ExportMenu::FORMAT_PDF => [
                                        'font' => [
                                            'family' => ['THSarabunNew','garuda'],
                                                'bold' => true,
                                                'color' => [
                                                    'argb' => 'FFFFFFFF',
                                            ],
                                        ],
                                    ],
                                ],
                                'target' => ExportMenu::TARGET_BLANK,
                            ]); 
                    ?>          
                </div>
            </div>            
        </div>
        
    </div>
</div>
