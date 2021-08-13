<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;
use kartik\widgets\DatePicker;

use kartik\grid\GridView;
//use yii\grid\GridView;

use common\models\Items;

$Bahttext = new FunctionBahttext();
$Fnc = new FunctionSaleOrder();
/* @var $this yii\web\View */
/* @var $model common\models\rcinvoiceheader */

$this->title = $model->no_;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_, 'url' => ['view', 'id' => $model->no_]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');


?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="sale-invoice-header-update" ng-init="Title='<?=$model->no_?>'">
     
<div class="panel panel-danger">

    <!-- Invoice Header  -->
    <div class="panel-heading">
        <div class="row">
                <div class="col-xs-12" >
                <div class="pull-right">
                <?php if($model->no_ == ''): ?>
                    <a href="#" id="ew-get-source" class="btn btn-primary"  data-toggle="modal" data-target="#ew-modal-source"><i class="fa fa-search-plus" aria-hidden="true"></i> <?=Yii::t('common','Get source document') ?></a>
                <?php else:  ?>
                    
                   
                    <h4> เลขที่ :  <input class="text-right" id="doc-no" type="text" name="no_" value="<?=$model->no_?>" />  </h4>
                    
                <?php endif; ?>
                </div>
                <div><h4><i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i> ใบลดหนี้ / ส่งคืนสินค้า</h4></div>
            </div>

        </div>
    </div>
    <div class="panel-body">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-4">
             
        </div>
    </div>

    <div class="row">
        <?php if($model->invfromCreditNote){ ?>
            <div class="col-sm-12 text-right" style="margin-bottom:20px;">         
                        <?= Html::a('<i class="fab fa-centercode fa-spin text-red"></i> ใบกำกับภาษี เลขที่ : '.$model->invfromCreditNote->no_ , ['/accounting/posted/posted-invoice', 'id' => base64_encode($model->invfromCreditNote->id)], [
                            'class' => 'btn btn-warning-ew btn-flat',                            
                        ]) ?>   
            </div>             
        <?php } ?>
        <div class="col-sm-4">
            <div class="row">
                
                <div class="col-sm-4">
                    <?= $form->field($model, 'cust_code')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                </div>
                <div class="col-sm-8">
                    <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                </div>
            </div>

            <?= $form->field($model, 'cust_address')->textInput(['readonly' => 'readonly']) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'district')->textInput(['disabled' => true,'value' => $model->customer->locations->tumbol]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'city')->textInput(['disabled' => true,'value' => $model->customer->locations->amphur]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'province')->textInput(['disabled' => true,'value' => $model->customer->locations->province]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'postcode')->textInput(['disabled' => true,'value' => $model->customer->locations->zipcode]) ?>
                </div>
            </div>
 
        </div>   
        <div class="col-sm-4">                       
            <?= $form->field($model, 'sales_people')->textInput(['maxlength' => true,'readonly' => 'readonly','value' => $model->sales->name]) ?>
            
        </div> 

        <div class="col-sm-2">
             
            <?= $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                          'options' => ['placeholder' => Yii::t('common','Posting Date').'...',],
                          'value' => date('Y-m-d',strtotime($model->posting_date)),
                          'type' => DatePicker::TYPE_COMPONENT_APPEND,
                          'removeButton' => false,
                          'pluginOptions'   => [
                              'format'      => 'yyyy-mm-dd',
                              'autoclose'   => true
                          ]
                      ]); ?> 
            <?= $form->field($model, 'order_date')->textInput(['readonly' => 'readonly']) ?>
            <?= $form->field($model, 'ship_date')->textInput(['readonly' => 'readonly']) ?>            
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'ext_document')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
            <?= $form->field($model, 'doc_type')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
            <?= $form->field($model, 'other_ref')->textInput() ?>
            <?= $model->users->username;?>
        </div>
    </div>

    <!-- /. Invoice Header  -->

    <!--  Invoice Line  -->
    <!-- <div class="row">
        <hr class="style2">  
    </div> --> 

    <div class="table font-roboto"  >

        <?php 
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('common','Item'),
                'format' => 'raw',
                'value' => function($model){
                    if($model->type=='Item')
                    {
                        $code = $model->crossreference->no;
                    }else {
                        $code = 'G/L Number';
                    }                        
                    return Html::a($code, ['/items/items/view-only', 'id' => $model->item],['target' => '_blank']);
                },
                
            ],
            [
                'label' => Yii::t('common','Name'),
                'value' => function($model){
                    return $model->code_desc_;
                },                     
            ],
            [
                'label' => Yii::t('common','Quantity'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    $html =  number_format($model->quantity,2);
                    return Html::a($html, ['/warehousemoving/warehouse', 'WarehouseSearch[ItemId]' => base64_encode($model->item)],['target' => '_blank']);
                },                     
            ],
            [
                'label' => Yii::t('common','Unit Price'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format(abs($model->unit_price),2);
                },                     
            ], 

            // [
            //     'label' => Yii::t('common','Discount'),
            //     'headerOptions' => ['class' => 'text-right'],
            //     'contentOptions' => ['class' => 'text-right'],
            //     'value' => function($model){
            //         return number_format(abs($model->line_discount),2);
            //     },                     
            // ], 

            [
                'label' => Yii::t('common','Amount'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format(abs(($model->quantity * $model->unit_price)- $model->line_discount),2);
                },
                 
            ], 
            ];
        ?>
        <?=  GridView::widget([
              'dataProvider'=> $dataProvider,
              //'filterModel' => $searchModel,
              'summary' => false,
              //'showFooter' => true,
              'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
              'columns' => $gridColumns,
              //'responsive'=>true,
              //'hover'=>true,
              //'summary' => false,
              'responsiveWrap' => false,
          ]);
        ?>
         
    </div>


    
    <!--  /. Invoice Line  -->

    <div class="row" style="margin-bottom:50px;">

        <div class="col-md-7">
            <p class="text-muted well well-sm no-shadow"> <?php echo  $Bahttext->ThaiBaht(abs($model->sumtotals->total)) ?>   </p>
            
             
            <div class="panel panel-default">
                <div class="panel-body">
                <?= $form->field($model, 'remark')->textarea(['rows'=>'7','placeholder' => '. . .'])->label(Yii::t('common','Remark')) ?>
                </div>
            </div>
            
            
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">                               
                    <table class="table font-roboto">     
                        <tr class="bg-gray">
                            <th style="width:70%"><?=Yii::t('common','มูลค่าของสินค้าตามใบกำกับภาษีเดิม')?></th>
                            <td align="right" class="font-roboto">
                            <?=($model->invfromCreditNote)?  number_format($model->invfromCreditNote->sumtotals->sumline,2) : '' ; ?>
                            </td>
                        </tr>
                        <tr class="text-info">
                            <td><?=Yii::t('common','Total')?> </td>
                            <td class="text-right"><?=number_format(abs($model->sumtotals->sumline),2);?></td>
                        </tr>
                        <tr class="text-info">
                            <td><?=Yii::t('common','Discount')?> <?=$model->percent_discount?> % </td>
                            <td class="text-right"><?=number_format(abs($model->discount),2);?></td>
                        </tr>
                        <tr class="text-info">
                            <td><?=Yii::t('common','After discount')?> </td>
                            <?php $afterDiscount = abs($model->sumtotals->sumline) - abs($model->discount); ?>
                            <td class="text-right"><?=number_format($afterDiscount,2);?></td>
                        </tr>
                        <tr>
                            <th style="width:50%"><?=Yii::t('common','มูลค่าของสินค้าที่ถูกต้อง')?></th>
                            <td align="right" class="font-roboto">
                            <?php $real_value = (abs(
                                    ($model->invfromCreditNote)
                                        ? $model->invfromCreditNote->sumtotals->sumline 
                                        : 0
                                    )) - abs($model->sumtotals->subtotal);

                                    echo number_format(($real_value * 7 / 100) + $real_value, 2);
                            ?></td>
                        </tr>  
                        
                        <tr class="hidden"><td><?= $form->field($model, 'discount')->textInput(['readonly' => 'readonly']) ?></td></tr>
                        <tr>
                            <th style="width:50%" ><?=Yii::t('common','ผลต่าง')?></th>
                            <td align="right" class="font-roboto"><?= number_format(abs($model->sumtotals->subtotal),2) ?></td>
                        </tr>
                        <?php if($model->include_vat == 0): // Vat ใน ?>
                        <tr>
                            <th> ก่อนรวมภาษี </th> 
                            <td align="right" class="font-roboto"><?= number_format(abs($model->sumtotals->subtotal) - abs($model->sumtotals->incvat),2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th ><?=Yii::t('common','Vat')?> <?=$model->sumtotals->vat?> %</th> 
                            <td  align="right" class="font-roboto"><?= number_format(abs($model->sumtotals->incvat),2) ?></td>
                        </tr>
                        <tr class="bg-dark">
                            <th><?=Yii::t('common','Grand total')?>:</th>
                            <td align="right" class="font-roboto"><?= number_format(abs($model->sumtotals->total),2) ?> </td>
                        </tr>
                    </table>
                
            </div>
        </div>
    </div>
    <div style="position: fixed;
                    bottom: -20px;
                    border-top: 1px solid #ccc;
                    
                    background-color: rgba(239, 239, 239, 0.9);
                    padding: 10px 10px 15px 10px;
                    right: 0px;
                    text-align: right;
                    z-index:1000;
                    left: 0px;">
        <div class="form-group pull-left">    
            <?= Html::a('<i class="fas fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => base64_encode($model->id),'no' => $model->no_ , 'status' => $model->status], [
                                'class' => 'btn btn-danger-ew btn-flat',                             
                                'data' => [
                                    'confirm' => Yii::t('common', 'Are you sure you want to delete this document?'),
                                    'method' => 'post',
                                ],
                            ]) ?>    
        </div>
        <div class="form-group pull-right">     
                            
            <?=Html::a('<i class="fa fa-pencil"></i> '.Yii::t('common','Edit'),['/accounting/credit-note/update', 'id' => base64_encode($model->id), 'no' => $model->no_],
                [
                    'class' => 'btn btn-warning-ew ' .($model->status == 'Open' ? ' ' : 'hidden'),
                    
                ]) ?> 

            <?=Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common','Print'),['/accounting/credit-note/print', 'id' => base64_encode($model->id), 'no' => $model->no_],
                [
                    'class' => 'btn btn-success',
                    'target' => '_blank',
                ]) ?>   

              
        </div>

    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
 

</div>

 
 <?php
$id = base64_encode($model->id);
$js =<<<JS
 $(document).ready(function(){
     $('a.btn-app-print').attr('href','index.php?r=accounting%2Fcredit-note%2Fprint&id={$id}&no={$model->no_}');
     if ('{$model->status}'=='Posted') {
        $('a.ew-bt-app-edit').attr('style','display:none');
     }     
 
 })

 const changeField = (obj, callback) => {
    fetch("?r=accounting/credit-note/update-field", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => {        
        callback(response);
    })
    .catch(e => {
        swal(
        "Fail!",
        "Something Wrong. "+ e.message + "  " + new Date().toTimeString().slice(0, 8),
        "error"
        );
    });
 }

 $('body').on('change', '#rcinvoiceheader-other_ref', function(){
 
    let field   = 'other_ref';
    let value   = $(this).val();
    changeField({id:'{$id}', field:field, value:value, status:'{$model->status}'}, res => {
        if(res.status===200){
            swal("Success", "Done", "success");
        }else{
            swal("Fail!", "Something Wrong. "+ res.message + new Date().toTimeString().slice(0, 8), "error");
        }
    })
    
 })

$('body').on('change', 'input#doc-no', function(){

    let field   = 'no_';
    let value   = $(this).val();
    changeField({id:'{$id}', field:field, value:value, status:'{$model->status}'}, res => {
        if(res.status===200){
            swal("Success", "Done", "success");
            window.location = "?r=accounting%2Fcredit-note%2Fview&id={$id}&no=" + res.data; 
        }else{
            swal("Fail!", "Something Wrong. " + res.message, "error");
        }
    })

})

$('body').on('change', '#rcinvoiceheader-remark', function(){

    let field   = 'remark';
    let value   = $(this).val();
    changeField({id:'{$id}', field:field, value:value, status:'{$model->status}'}, res => {
        if(res.status===200){
            swal("Success", "Done", "success");
        }else{
            swal("Fail!", "Something Wrong. " + res.message, "error");
        }
    })

})



//From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
Date.prototype.toInputFormat = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    return (
    yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0])
    ); // padding
};

$("#rcinvoiceheader-posting_date").on("change", function() {
    var today   = $('#rcinvoiceheader-posting_date').val();
    var date    = new Date(today);
    let field   = 'posting_date';

    if (!isNaN(date.getTime())) {
        changeField({id:'{$id}', field:field, value:date.toInputFormat(), status:'{$model->status}'}, res => {
            if(res.status===200){
                swal("Success", "Done", "success");
            }else{
                swal("Fail!", "Something Wrong. " + res.message, "warning");
            }
        });
    } else {
        alert("Invalid Date");
    }
});



JS;
$this->registerJS($js);

?>