<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $model admin\modules\accounting\models\InvLineSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .head-search{
        margin:-15px -15px 15px -15px;
        border-bottom:1px solid rgba(204, 204, 204, 0.39);
        /* background: #fbfafa; */
        padding:15px 15px 15px 15px;
    }

    .head-search input,
    .head-search select,
    .head-search .input-group-addon{
        background: #fcfeff;
    }
</style>
<div class="row head-search">
    <div class="row">
        <div class="col-sm-6 mb-10">
            <h1 style="margin-bottom: 0px;"><?= Html::a($this->title,['index']); ?></h1>
            <small>รายงานการขายสินค้า</small>
        </div>
        <div class="col-sm-6 mb-10 text-right">
                 
        </div>
    </div>
    <?php $form = ActiveForm::begin([
        'tooltipStyleFeedback' => true,
        'action' => ['index'],
        'method' => 'get',
        'id' => 'form-search'
    ]); ?>
    <div class="row">
        
        <div class="col-sm-5 col-md-6 mb-10">
                    
        <?php



$FromDate   = Yii::t('common','From Date');
$ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
    <span class="input-group-addon">$FromDate</span>
    {input1}{separator} 
    <span class="input-group-addon">$ToDate</span>
    {input2}
    <span class="input-group-addon kv-date-remove">
        <i class="glyphicon glyphicon-remove"></i>
    </span>
HTML;

            echo DatePicker::widget([
                'type'      => DatePicker::TYPE_RANGE,
                'name'      => 'InvLineSearch[fdate]',
                'value'     => $model->fdate ? $model->fdate : '',					
                'name2'     => 'InvLineSearch[tdate]',
                'value2'    => $model->tdate ? $model->tdate : '',                  
                'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                'layout'    => $layout,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'yyyy-mm-dd'
                ],
                'options'   => ['autocomplete' => 'off'],
                'options2'  => ['autocomplete' => 'off'],
            ]);

            ?>
 
        </div>
        <div class="col-sm-3 col-md-2 col-xs-4">
            <?= $form->field($model,'vat_percent')->dropDownList(['1' => 'Vat','2' => 'No Vat'],['prompt' => Yii::t('common','All')])->label(false)?>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?= $form->field($model, 'items', [
                'addon' => [
                            'append' => [
                                'content' => Html::submitButton('<i class="fa fa-search"></i>', ['class'=>'btn btn-primary search-btn']),
                                'asButton' => true
                            ],
                        ]
                ])->textInput(['class'=>'input-md','placeholder' => Yii::t('common','Search')])->label(false) ?>
              
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
// $js =<<<JS
//     // $(document).ready(function(){
//     //     $('a.ew-bt-app-new').hide();
//     //     $('#w1-togdata-page').attr('title',' ');
//     // });
//     const submitForm = () => {
//         console.log('submit');
//         $('#form-search').submit();
//     }
//     $('body').on('click','.search-btn',function(){
//         submitForm();
//     })

//     $('body').on('keydown','#invlinesearch-items',function(e){
//         if (e.which == 13) {
//             submitForm();
//         }
//     })
// JS;
// $this->registerJs($js);
?>