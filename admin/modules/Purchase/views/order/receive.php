<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Purchase\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Confirm Receive');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="purchase-header-index" ng-init="Title='<?=$this->title?>'">
<h4 class="text-aqua"><i class="fas fa-check"></i> เลือกรายการสินค้าที่ต้องการรับ</h4>
<?php $form = ActiveForm::begin([
    'id' => 'purchase-order-receive',
    'enableClientValidation' => true,
    'enableAjaxValidation' => false,
    'options' => [
        'enctype' => 'multipart/form-data',
        'data-key' => $model->id,
    ]
]); ?>

<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-sm-4 pull-right">
        <?=$form->field($model,'doc_no')->textinput(['class' => 'text-right','readonly' => true]);?>
    </div>
</div>
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4"> 
        <?php $model->invoice_no = $model->invoice_no ?: $model->ref_no; ?>
        <?=$form->field($model,'invoice_no',['addon' =>
                              ['append' =>
                                [
                                'content'=>'<i class="fas fa-paperclip"></i>',
                                ]
                              ]
                            ])->textinput()->label(Yii::t('common','Reference No'));?>
    </div>
    <div class="col-sm-4">
        <?php /* $form->field($model,'delivery_date')->textInput(['value' => date('Y-m-d'),'readonly' => true,'placeholder' => Yii::t('common','Receive Date').'...',])->label(Yii::t('common','Receive Date')) */ ?>
        <?= $form->field($model, 'delivery_date')->widget(DateTimePicker::classname(), [
                                                 'options' => [
                                                     'placeholder' => Yii::t('common','Receive Date').'...',
                                                     'value' => date('Y-m-d H:i'),
                                                    
                                                ],
                                                 //'value' => date('Y-m-d'),
                                                 'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                                 'removeButton' => false,
                                                 'pluginOptions' => [
                                                     //'format' => 'dd/mm/yyyy',
                                                     'format' => 'yyyy-mm-dd hh:ii',
                                                     'autoclose'=>true,
                                                     'readonly' => true
                                                 ]
                                         ])->label(Yii::t('common','Receive Date')); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?=$model->vendor->name;?>
    </div>
</div>

<br>
<div class="table-responsive">

    <?= GridView::widget([
        'dataProvider'  => $dataProvider,
        //'filterModel' => $searchModel,
        'tableOptions'  => ['class' => 'table  table-bordered table-hover font-roboto'],
        'rowOptions'    => function($model){
            if($model->received->complete){
                return ['class' => 'bg-success'];
            }else{
                return ['class' => ' '];
            }
        },
        'columns'       => [
            [
                'class'    => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'bg-gray']
            ],
            [                
                'attribute' => 'items_no',
                'format' => 'raw',
                'headerOptions' => ['class' => 'bg-gray'],
                'contentOptions' => ['class' => ''],
                'value' => 'items_no'
            ],
            [                
                'attribute' => 'description',
                'format' => 'raw',
                'headerOptions' => ['class' => 'bg-gray'],
                'contentOptions' => ['class' => ''],
                'value' => 'description'
            ],
            // 'items_no',
            // 'description', 
            [   
                'class'     => 'yii\grid\DataColumn',             
                'label'     => Yii::t('common','Location Code'),
                'format'    => 'raw',
                'headerOptions'     => ['class' => 'text-right bg-gray','style' => 'width:150px;'],
                'contentOptions'    => ['class' => 'text-right'],
                'value' => function($model){
                    $disabled = null;
                    if($model->received->complete){
                        $disabled = 'disabled="disabled"';
                    }


                    $query = \common\models\Location::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->all();
                    $content = '<select '.$disabled.' name="location['.$model->id.']" class="form-control location-pick"  style="min-width:100px">';
                    foreach ($query as $key => $value) {
                        $selected = ($model->location==$value->id)? 'selected="selected"' : ' ';
                        $content.= '<option value="'.$value->id.'" '.$selected.'>'.$value->code.'</option>';
                    }
                    $content.= '</select>';                   
                    return $content;
                }
            ],    
            
            [                
                'label' => Yii::t('common','Quantity'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-gray','style' => 'width:100px;'],
                'contentOptions' => ['class' => 'text-right bg-gray'],
                'value' => function($model){
                    return '<span class="quantity" data-val="'.$model->quantity.'">'.number_format($model->quantity,2).'</span>';
                }
            ], 


           
            
            //'unitofmeasures.UnitCode',  
            [                
                'label' => Yii::t('common','Measure'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'bg-gray'],
                'contentOptions' => ['class' => ''],
                'value' => 'unitofmeasures.UnitCode'
            ],

            [                
                'label' => Yii::t('common','Received'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-gray'],
                'contentOptions' => ['class' => 'text-right qty-unit bg-info'],
                'value' => function($model){                    
                    return '<span class="received db-click" data-val="'.$model->received->receive.'">'.number_format($model->received->receive,2).'</span>';
                }
            ],

            [
                'class'             => 'yii\grid\CheckboxColumn',
                'name'              => 'receive',
                'headerOptions'     => ['class' => 'text-center bg-gray'],
                'contentOptions'    => ['class' => 'text-center checkbox_row'],
                'checkboxOptions'   => function ($model, $key, $index, $column) {
                    if($model->received->complete){
                        return [
                            'value'     => $model->id,
                            'disabled'  => true, 
                            'class'     => 'checkbox-row',
                            'data-val'  => $model->quantity
                        ];
                    }else{
                        return [
                            'value'     => $model->id,
                            'checked'   => false,
                            'class'     => 'checkbox-row',
                            'data-val'  => $model->quantity
                        ];
                    }                    
                }
            ],
 
            [                
                'label' => Yii::t('common','Quantity to receive'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right  bg-yellow','style' => 'width:150px;'],
                'contentOptions' => ['class' => 'text-right  bg-yellow'],
                'value' => function($model){
                    $disabled = null;
                    $quantity = 0;
                    if($model->received->complete){
                        $disabled = 'disabled="disabled"';
                        $quantity = $model->quantity - $model->received->receive;
                    }

                    $content = '<input type="number" '.$disabled.' step="any" name="quantity['.$model->id.']" class="form-control text-right qty-to-receive" value="'.$quantity.'" style="min-width:100px">
                                <input type="hidden" name="qty_per_unit" class="qty_per_unit" value="'.$model->quantity_per_unit.'">';
                    return $content;
                }
            ], 
        ],
        'summary' => false
    ]); ?>
    </div>
</div>


<div class="content-footer" style="
      position: fixed;
      bottom: -10px;
      border-top: 1px solid #ccc;
      width: 100%;
      background-color: rgba(239, 239, 239, 0.9);
      padding: 10px 10px 15px 10px;
      right: 0px;
      text-align: right;
      z-index:1000;
    ">
    <div class="row" >
        
    
        <div class="col-xs-6 col-sm-6 text-left">                       
            <?= Html::a('<i class="fas fa-chevron-left"></i> '.Yii::t('common', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-default ']) ?>   
        </div>

        <div class="col-sm-6 text-right">
            <?= Html::Button('<i class="fa fa-server" ></i> '. Yii::t('common', 'POST RECEIVE'), ['class' => 'btn btn-warning','onClick' => 'confirmSubmit()']) ?>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 150px;">
    <div class="col-sm-8">
        <?=$form->field($model,'remark')->textarea(['rows' => 6, 'class' => ' ', 'value' => strip_tags($model->remark)]);?>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php 
$js =<<<JS
    
    
$('body').on('change','input.qty-to-receive',function(){
    var qty_per = $(this).closest('td').find('input.qty_per_unit').val();
    $(this).closest('tr').find('td.qty-unit').html(Math.round(qty_per*$(this).val()));
})   

function confirmSubmit(){
    if($('input[name="receive[]"]:checkbox:checked').length > 0){
        
        if($('#purchaseheader-invoice_no').val() != ''){
            if(confirm('ยืนยันการรับสินค้า ?')){    
                $('form#purchase-order-receive').submit();
            }
        }else{
            $('#purchaseheader-invoice_no').focus();
            $('#purchaseheader-invoice_no').closest('.form-group').removeClass('has-success').addClass('has-warning');
            $('#purchaseheader-invoice_no').closest('.form-group').find('.help-block').html('<span class="text-red">Please Insert Reference</span>')
            return false;
        }
       
    }else{
        alert('Please select product.');
    }
}


$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');

    $('.checkbox_row').click(function(event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });    

    $('.checkbox-row').click(function(event) {
        let qty     = 0;
        let buy     = $(this).closest('tr').find('span.quantity').attr('data-val');
        let receive = $(this).closest('tr').find('span.received').attr('data-val');
        if($(this).is(':checked')){
            //let receive = $(this).attr('data-val') * 1;
                qty     = buy - receive;
        } 
 
        $(this).closest('tr').find('input.qty-to-receive').val(qty).attr('disabled',false);
    });   


    $('.select-on-check-all').click(function(event){
        $('.checkbox-row').trigger('click');
    });
    
})


JS;
$this->registerJS($js,\yii\web\View::POS_HEAD);
?>

 

<?php 
$jsn=<<<JS

  
$("table tr td.checkbox_row").dblclick(function(e) {
    alert('OK! Enable Receive');
    $(this).closest('tr').removeClass('bg-success');
    $(this).closest('tr').find('input[name="receive[]"]').attr('disabled', false);
    $(this).closest('tr').find('.qty-to-receive').attr('disabled', false);
    $(this).closest('tr').find('.location-pick').attr('disabled', false);
});

$('body').on('click',"input.qty-to-receive", function(){
    //$(this).closest('tr').find('.checkbox-row').prop( "checked", true );

})

$('body').on('keyup', 'input.qty-to-receive', function(e){
     
    let value   = $(this).val();
    let buy     = $(this).closest('tr').find('span.quantity').attr('data-val');
    let receive = $(this).closest('tr').find('span.received').attr('data-val');
    let total   = buy - receive;

    if(value > total){
        alert('ยอดรับเกินจำนวน');
    }

 
    if(value == 0){
        $(this).closest('tr').find('.checkbox-row').prop( "checked", false );
    }else{
        $(this).closest('tr').find('.checkbox-row').prop( "checked", true );
    }

    
    if (e.which == 13) {
        $(this).next('.qty-to-receive').focus();
    }
});
    
 
JS;
$this->registerJS($jsn,\yii\web\View::POS_END,'yiiOptions');