<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;
?>
<style>
        label#file-input {
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            background-color: #e66a6a;
            color: #fff;
        }

        label#file-input > i {
            margin-right: 5px;
        }

        input#file {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }
    </style>
    

<div class="row">
    <div class="col-xs-12">
        <?php $form = ActiveForm::begin([
        'id' => 'import-file',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'data' => ['pjax' => true],
            'enctype' => 'multipart/form-data',
            'data-key' => $model->id,
            //'name' => 'FormSaleorder'
            ]]); 
        ?>
        <div class="row">
            <div class="col-sm-6">
                <h4 class="cust-name"></h4>
            </div>
            <div class="col-sm-6 text-right">
                <h3 class="file-of-company" style="color:#ccc;"></h3>
            </div>
        </div>
 
        <div class="row mt-5">

            <div class="col-sm-3">
                <?= $form->field($model, 'ext_document')->textInput(['class' => 'bg-info','style' => 'background: #e5e5ff;'])->label(Yii::t('common','P/O of customer.')); ?>
            </div> 
            <div class="col-sm-3">
                <?= $form->field($model,'invoice_no',[
                        'addon' => [
                            'append'   => [
                                'content'=> Html::button('<i class="fas fa-caret-up"></i>', ['class'=>'btn btn-default show-series-list', 'data-serie' => 'IV']),
                                'asButton'  => true
                            ]                            
                        ]
                    ])->textInput(['placeholder' => 'ถ้ายังไม่รู้ ให้ว่างไว้ก่อน', 'style' => 'background:#f7ffd7'])?>
            </div>       
            <div class="col-sm-3">
                <?= $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => Yii::t('common','Order Date'),
                                            'autocomplete' => 'off'
                                        ],
                                        'value' => $model->order_date,
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'pluginOptions' => [
                                            'todayHighlight' => true,
                                            'todayBtn' => true,
                                            'format' => 'yyyy-mm-dd',
                                            'autoclose'=>true
                                        ],
                                        'pluginEvents' => [
                                            'changeDate' => 'function(e) {
                                                let header = JSON.parse(localStorage.getItem("sale-header"));
                                                    header = Object.assign({},header,{ 
                                                        vat:    $("#saleheader-vat_percent").val(), 
                                                        incvat: $("#saleheader-include_vat").val(),
                                                        date:   $("body").find("#saleheader-order_date").val(), 
                                                        inv:    $("body").find("#saleheader-invoice_no").val(),
                                                        po:     $("body").find("#saleheader-ext_document").val(),
                                                        remark: $("body").find("#saleheader-remark").val()
                                                    });
                                                localStorage.setItem("sale-header",JSON.stringify(header));
                                                let data = localStorage.getItem("new-sale-line")? JSON.parse(localStorage.getItem("new-sale-line")) : [];
                                                renderTable(data);
                                            }'
                                        ]
                                    ])->label(Yii::t('common','Invoice Date')); ?>
            </div>    
            <div class="col-sm-3 text-right">
                <label id="file-input" for="file" style="margin-top: 15px;"><i class="fas fa-file-upload"></i> เลือกไฟล์ PDF</label>
                <?= $form->field($model,'pdfFile')->fileInput(['class' => 'file form-control','accept' => ".pdf", 'id'=>'file'])->label(false)?>
                <div class="btn-group navbar-btn sw-btn-group" role="group">       
                    <?= Html::submitButton('<i class="fa fa-upload" ></i> '.Yii::t('common', 'Upload') ,['class' => 'btn btn-primary hidden']) ?>                            
                </div>
            </div>
        </div>
             
        <div class="renders"></div>
        <div class="row" >
            <div class="col-sm-8">
                <?= $form->field($model,'remark')->textArea()?>   
            </div>
            <div class="col-sm-4 col-xs-12" id="summation-table" >
                <div>
                    <div class="pull-left" style="margin-right:30px;">
                        <?= $form->field($model,'vat_percent')->dropDownList(
                                arrayHelper::map(
                                        \common\models\VatType::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all(),
                                        'vat_value', 'name')
                                    ,['style' => 'max-width:180px;'])->label(Yii::t('common','Vat')); ?>
                    </div>
                    <div>
                        <?= $form->field($model,'include_vat')
                            ->dropdownList(['0' => Yii::t('common','Include Vat'), '1' => Yii::t('common','Exclude Vat')],
                                ['style' => 'max-width:215px;']
                                )?>   
                    </div>
                </div>
                <table class="table table-bordered" style="margin-bottom:150px;">
                    
                    <tr>
                        <td class="text-right"><?=Yii::t('common','Sum total')?></td>
                        <td id="get-sum-total" class="text-right">0</td>
                    </tr>
                    <tr>
                        <td class="text-right"><?=Yii::t('common','Vat')?> 7%</td>
                        <td id="get-sum-vat" class="text-right">0</td>
                    </tr>                    
                    <tr class="bg-gray">
                        <td class="text-right"><?=Yii::t('common','Grand total')?></td>
                        <td id="get-grand-total" class="text-right">0</td>
                    </tr>
                </table>
            </div>
        </div> 
        <div class="<?=$text ? 'row' : 'hidden' ?>">
            <div class="col-sm-12 text-left"  style="margin-bottom:150px;">     
                <a href="#show-raw-data" class=" " data-toggle="collapse"><small ><i class="fas fa-file-pdf text-red"></i> Raw</small></a>
                <div id="show-raw-data" class="collapse">                 
                    <div  id="pdf-content" style="border:1px solid #ccc;">
                        <?=$text;?>                            
                    </div>
                </div>
            </div>
        </div>       
          
        <?php ActiveForm::end(); ?> 
    </div>
</div>
 
 