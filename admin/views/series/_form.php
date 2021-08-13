<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\ModuleApp;

use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NumberSeries */
/* @var $form yii\widgets\ActiveForm */
if(isset($_GET['series']['starChar']))      $model->starting_char   = $_GET['series']['starChar'];
if(isset($_GET['series']['separate']))      $model->separate        = $_GET['series']['separate'];
if(isset($_GET['series']['format_gen']))    $model->format_gen      = $_GET['series']['format_gen'];

    
?>
<style type="text/css">
    .nopadding {
       padding: 0 !important;
       margin: 0 !important;
    }
</style>
<div class="number-series-form">

    <?php $form = ActiveForm::begin([
    'id'=> 'NumberSeries',
    'options' => ['class' => 'NumberSeries'],
    ]); ?>

     

    <?php
     // $form->field($model,'name')->dropDownList([
     //                    'Sale Order' => 'Sale Order' , 
     //                    'Purchase Order' => 'Purchase Order',
     //                    'SaleInvoice' => 'Sale Invoice',
     //                ],
     //                    ['options' => ['1' => ['Selected'=>'selected']
     //                ]
     //    , 'prompt' => ' -- Select Module --']) 
 
        ?>
    <div class="row">
        

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                     <?= $form->field($model, 'name')->dropDownList(
                arrayHelper::map(ModuleApp::find()->all(),
                                'name','description'),
                                [
                                    'class'=>'form-control',                                     
                                    'options' => [                        
                                        @$_GET['series']['form'] => ['selected' => true]
                                    ],
                                ])->label(Yii::t('common','Document type'))?>
                </div>
                
            </div>
         
            <div class="row">
                <div class="col-md-12">

                    <?= $form->field($model, 'description')->textInput(['placeholder' => Yii::t('common','Explanation')]) ?>
                </div>
            </div>
            <div class="row">    
                <div class="col-md-12">

                    <div class="well">
                        <div class="row">
                        <div class="col-sm-6 nopadding">
                            <?= $form->field($model, 'starting_char')->textInput(['placeholder' => 'SO']) ?>
                        </div>
                        <div class="col-sm-2 col-xs-4 nopadding">

                            <?php if($model->separate=="") $model->format_gen = '-'; ?>
                            <?php 
                                list($y,$m,$d)  = explode("-",date('Y-m-d'));
                                $thY            = ($y+543);
                                $thy            = substr(($y+543),2);
                            ?>
                            <?= $form->field($model,'separate')->dropDownList([''=>Yii::t('common','NULL'),
                                '-' => '-',   
                                'YYYY' => date('Y'),    
                                'YYYY-' => date('Y').'-',                              
                                'YYMM' => date('ym'),
                                'YYMM-' => date('ym').'-',
                                'YY-MM' => date('y-m'),  
                                'YY-MM-' => date('y-m').'-', 
                                'YYYYTH' => $thY,
                                'YYYYTH-' => $thY.'-',
                                'YYMMTH' => $thy.date('m'),    
                                'YYMMTH-' => $thy.date('m').'-',                         
                                'YY-MM-TH' => $thy.'-'.date('m'), 
                                'YY-MM-TH-' => $thy.'-'.date('m').'-'                                
                                
                            ]) ?>

                        </div>
                        <div class="col-sm-4 col-xs-8 nopadding">

                            <?php if($model->format_gen=="") $model->format_gen = '000'; ?>

                            <?= $form->field($model,'format_gen')->dropDownList(['00' => '00','000' => '000','0000' => '0000','00000' => '00000'])->label(Yii::t('common','Runing Digit'));?>

                            

                        </div>
                        </div>
                    </div>
                </div>
            </div>

           
            
             

        </div>
        <div class="col-md-6">
             <div class="row">
                <div class="col-md-12" >
                    <?php 

                    if(isset($_GET['table'])){
                        //echo '<h4>Detail</h4>Name : '.$_GET['table']['name'].'<br> Field : '.$_GET['table']['field'].'<br> Condition : '.$_GET['table']['cond']; 

                        $model->table_name  = $_GET['table']['name'];
                        $model->field_name  = $_GET['table']['field'];
                        $model->cond        = $_GET['table']['cond'];
                    } 
                        
                    ?>
                </div>
            </div>
            <div class="well">
                <div class="row">
                    <div class="col-md-6">

                        <?= $form->field($model, 'table_name')->textInput([$model->isNewRecord ?: 'readonly' => true]) ?>

                        <?= $form->field($model, 'field_name')->textInput([$model->isNewRecord ?: 'readonly' => true]) ?>

                        <?= $form->field($model, 'cond')->textInput([$model->isNewRecord ?: 'readonly' => true]) ?>
                    </div> 
                    <div class="col-md-6">

                    

                        <?= $form->field($model,'format_type')->dropDownList(['12M' => '12M','1Y' => '1Y','ONCE' => 'ONCE']);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>

    
    

    

    

    

    

    
    

    

    <?= $form->field($model, 'comp_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create Now') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
