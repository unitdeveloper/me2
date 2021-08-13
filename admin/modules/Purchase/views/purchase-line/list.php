<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Purchase\models\PurchaseLineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Purchase Lines');
$this->params['breadcrumbs'][] = $this->title;

$runningtotal   = 0;
$groupItem      = '';
$docGroup       = 0;
$Remark         = 0;
$TotalCumelate  = 1;
$Cumelate       = 1;

$column = [
    [
        
        'filterOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'bg-gray'],
        'headerOptions' => ['class' => 'bg-primary'],
        'class' => 'yii\grid\SerialColumn'
    ],

    [
        'attribute' => 'order_date',
        'label' => Yii::t('common','Order Date'),
        'format' => 'html',
        'headerOptions' => ['class' => 'bg-dark','style' => 'width:60px; max-width:80;'],
        'contentOptions' => ['class' => ' ','style' => 'min-width:50px;max-width:80;'],
        'value' => function($model){
            return $model->header->order_date;
        },
        'filter' => DateRangePicker::widget([
            'model' => $searchModel,
            'attribute' => 'order_date',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d',
                ],                                
            ],
            
        ]),
    ],

    [
        'attribute' => 'doc_no',
        'label' => Yii::t('common','Purchase Order'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-dark','style' => 'width:130px;'],
        'contentOptions' => ['class' => ' '],
        'value' => function($model){
            return html::a($model->header->doc_no,['/Purchase/order/view','id' => $model->source_id], ['target' => '_blank']);
        }
    ],

    
    [
        'attribute' => 'expected_date',
        'label' => Yii::t('common','Expected Date'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-dark', 'style' => 'width:60px; max-width:80;'],
        'contentOptions' => ['class' => ' ', 'style' => 'min-width:50px;max-width:80;'],
        'value' => function($model){

            return '<input type="date" name="expected_date" value="'.($model->expected_date).'" class="  no-border" style="font-size:13px; background-color: transparent;" />';
            // return ($model->expected_date)
            //             ? '<input type="date" name="expected_date" value="'.$model->expected_date .'" class="form-control" />'
            //             : '<input type="date" name="expected_date"  class="form-control" />';
        },
        'filter' => DateRangePicker::widget([
            'model' => $searchModel,
            'attribute' => 'expected_date',
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d',
                ],                                
            ],
            
        ]),
    ],
    
    [
        'attribute' => 'item_name',
        'label' => Yii::t('common','Purchase Order'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-dark'],
        'contentOptions' => ['class' => ' '],
        'value' => function($model){
            if($model->description!=null){
                $html = $model->description;
            }else{
                $html = $model->items->description_th;
            }

            return $model->items->id != 1414 
                    ? Html::a($html, ['/items/items/view', 'id' => $model->items->id], ['target' => '_blank'])
                    : $html;
            
        }
    ],
    [
        'label' => Yii::t('common','Quantity to require'),
        'headerOptions' => ['class' => 'bg-dark text-right', 'style' => 'min-width:100px;'],
        'contentOptions' => ['class' => 'bg-yellow text-right'],
        'filterOptions' => ['class' => 'bg-gray'],
        'value' => function ($model) {
            return number_format($model->quantity,2);
            
        },                                               
    ],
/*
    [
        'label' => Yii::t('common','Quantity to require'),
        'headerOptions' => ['class' => 'bg-dark text-right', 'style' => 'min-width:100px;'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($model) use (&$groupItem) {
            //ถ้า item เดียวกัน ไม่ต้องแสดงค่าอีก
            if($groupItem == $model->items_no){                                
                return '';
            }else{
                
                $groupItem = $model->items_no;
                //return number_format($model->totalReceive->require) .' '. $model->unitofmeasures->UnitCode;
                return number_format($model->totalReceive->require);
            }
            
        },                                               
    ],
*/

    // [
    //     'label' => Yii::t('common','Quantity to receive'),
    //     'headerOptions' => ['class' => 'text-right','style' => 'min-width:100px;'],
    //     'contentOptions' => ['class' => 'text-right'],
    //     'value' => function($model){
    //         return number_format($model->received->receive,2);
    //     }
    // ],
   

    [
        'format' => 'raw',
        'label' => Yii::t('common','Received'),
        'headerOptions' => ['class' => 'bg-dark text-right','style' => 'min-width:100px;'],
        'filterOptions' => ['class' => 'bg-gray'],
        'contentOptions' => function($model){
            
            if($model->received->receive == $model->quantity){
                return ['class' => 'bg-green text-right'];
            }else if($model->received->receive < $model->quantity){
                if($model->received->receive != 0){
                    return ['class' => 'text-right bg-y-g','style' => 'background:linear-gradient(120deg, yellow , #00a65a); color:#fff;'];
                }else{
                    return ['class' => 'bg-info text-right'];
                }
                
            }else if($model->received->receive > $model->quantity){
                return ['class' => 'text-right bg-o-r','style' => 'background:linear-gradient(120deg, orange , red); color:#fff;'];
            }else{
                return ['class' => 'bg-orange-active text-right'];
            }
            
        },
        'value' => function($model){
            return number_format($model->received->receive,2);
        }
    ],                    
    // [
    //     'label' => Yii::t('common','Total Summary to receive'),
    //     'headerOptions' => ['class' => 'bg-dark text-right','style' => 'min-width:100px;'],
    //     'filterOptions' => ['class' => 'bg-gray'],
    //     'contentOptions' => function($model){
    //         if($model->totalReceive->require == $model->totalReceive->receive){
    //             return ['class' => 'bg-green text-right'];
    //         }else if($model->totalReceive->receive > $model->totalReceive->require){
    //             return ['class' => 'bg-aqua text-right'];
    //         }else {
    //             if($model->totalReceive->receive == 0){
    //                 return ['class' => 'bg-orange text-right'];
    //             }else{
    //                 return ['class' => 'text-right bg-or','style' => 'background-color:orange;color:#fff;'];
    //             }
    //         }                            
    //     },
    //     'value' => function ($model) use (&$docGroup,&$TotalCumelate) {
    //         //###แสดงในบรรทัดสุดท้าย (ของเอกสารเลขที่เดียวกัน)### 
    //         // ตรวจสอบเลขที่เอกสาร ว่ายังเป็นเลขที่เดิมอยู่หรือไม่ 
    //         // ถ้าเป็นเลขที่เดียวกัน ให้ Cumelate++ และเก็บเลขที่เอกสาร
    //         // ถ้าไม่ใช่ ให้ Cumelate = 1 เพื่อเคลียและนับใหม่ในรอบถัดไป
    //         if($docGroup == $model->item){  
                
    //             $TotalCumelate++;
        
    //             if($TotalCumelate == $model->countLineGroupByItem){
    //                 return number_format($model->totalReceive->receive, 2);
    //             }else{
    //                 return '';
    //             }                          
    //         }else{
                
    //             $TotalCumelate   = 1;
    //             $docGroup     = $model->item; //-->เก็บเลขที่เอกสารเพื่อตรวจสอบในรอบถัดไป

    //             if($TotalCumelate == $model->countLineGroupByItem){
    //                 return number_format($model->totalReceive->receive, 2);
    //             }else{
    //                 return '';
    //             }   
    //         }            
    //     },                                               
    // ],  
    [
        'label' => Yii::t('common','Measure'),
        'headerOptions' => ['class' => 'bg-dark ','style' => 'min-width:30px;'],
        'contentOptions' => [],
        'value' => 'unitofmeasures.UnitCode'
    ],
    [
        'label' => Yii::t('common','Stock'),
        'headerOptions' => ['class' => 'bg-dark text-right','style' => 'min-width:30px;'],
        'format' => 'decimal',
        'contentOptions' => ['class' => 'text-right'],
        'value' => 'items.qtyAfter'
    ]
];
?>
<style>

 
@media print { 
    .filters,footer{display:none;}
    .table .bg-green { 
        background:#00a65a!important;
    } 
    .table .bg-orange { 
        background:#ff851b!important;
    } 
    .table .bg-orange-active { 
        background:#ff7701!important;
    } 

    .table .bg-aqua { 
        background:#00c0ef!important;
    } 

    .table .bg-or { 
        background:orange!important;
        color:#fff!important;
    }
    .table .bg-y-g { 
        background:linear-gradient(120deg, yellow , #00a65a)!important;
        color:#fff!important;
    } 

    .table .bg-o-r { 
        background:linear-gradient(120deg, orange , red)!important;
        color:#fff!important;
    } 

    /* .table .bg-orange { 
        background:linear-gradient(120deg, orange , red)!important;
        color:#fff;
    }  */

}
@media (max-width: 768px){
    .daterangepicker{
        width:100%!important;
    }
}
 
</style>
<div class="purchase-line-index"  ng-init="Title='<?= Html::encode($this->title) ?>'" style="font-family: saraban; font-size:13px;">


 
    <div class="row">
        <div class="col-sm-6">
            <h4><?=Html::encode($this->title)?> </h4>
        </div>
        <div class="col-sm-6 text-right">
            <?=Html::a('<i class="fas fa-list"></i> '.Yii::t('common','Show All'),['list'],['class' => 'btn btn-info-ew'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 text-right">
            <?=ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $column,
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
                 
                'target' => ExportMenu::TARGET_BLANK,
            ]); 
        ?>   
        </div>
    </div>
   
    <div class="table-responsive"  >
           <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $column,
                'showFooter' => true
            ]); ?>
        
    </div>


    <div class="content-footer hidden-lg hidden-md hidden-sm" >
        <div class="row">
            <div class="col-xs-6 col-sm-6">                       
                <?= Html::a('<i class="fas fa-home"></i> '.Yii::t('common', 'Back'), ['/Purchase/order'], ['class' => 'btn btn-default ']) ?>   
            </div>
        </div>
    </div>


</div>


<?php 
$js=<<<JS

const changeDate = (obj, callback) => {
    fetch("?r=Purchase/purchase-line/change-date", {
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
      .catch(error => {
          console.log(error);
      });
}

$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');
 
    //$('input#purchaselinesearch-expected_date').attr('readonly','readonly');
});

$('body').on('change', 'input[name="expected_date"]', function(){
    let id      = $(this).closest('tr').attr('data-key');
    let date    = $(this).val();
    changeDate({id:id, date:date}, res => {

        if(res.status===200){
            $.notify({
            // options
            icon: "fas fa-check",
            message: res.message
            },{
                // settings
                placement: {
                from: "top",
                align: "right"
                },
                type: "success",
                delay: 4000,
                z_index: 3000
            });

        }else{
            $.notify({
            // options
            icon: "fas fa-times",
            message: res.message
            },{
                // settings
                placement: {
                from: "top",
                align: "center"
                },
                type: "error",
                delay: 4000,
                z_index: 3000
            });
        }

    });
});

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');