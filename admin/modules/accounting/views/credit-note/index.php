<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

use common\models\RcInvoiceLine;

use admin\modules\accounting\models\FunctionAccounting;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Credit Note Bill');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>

    .select2-container 
    .select2-selection--single 
    .select2-selection__rendered {
        padding-left: 0;
        margin-top: 0px;
    }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-invoice-header-index" style="font-family: saraban;" ng-init="Title='<?=$this->title?>'">
<?php 
        $column = [
            [
                'headerOptions' => ['class' => 'text-center bg-primary','style'=>'width:30px;'],
                'contentOptions' => ['class' => 'text-center bg-gray'],
                'filterOptions' => ['class' => 'bg-gray'],
                'class' => 'yii\grid\SerialColumn'
            ],
            
            [
                'label' => Yii::t('common','Status'),
                'format' => 'html',
                'headerOptions' => ['class' => 'text-center bg-gray','style'=>'min-width:50px;'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    if ($model->status=='Posted'){
                        return '<i class="fas fa-check-circle text-green"></i>';
                    }else{
                        return '<i class="fas fa-circle text-orange"></i>';
                    }
                },
                'filterWidgetOptions' => [
                        'size' => Select2::SMALL,
                        'options' => ['class' => 'hidden-xs hidden-sm', // control CSS for input like this here
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ],
                ],
                'filter' => Html::activeDropDownList($searchModel,'status',
                    [
                        'Posted'        => Yii::t('common','Posted'),
                        'Open'          => Yii::t('common','Not posted'),
                    ],
                    [
                        'class'         => 'form-control hidden-xs',
                        'prompt'        => Yii::t('common','Show All'),
                    ]),
                
            ],
 
            [
                'attribute' => 'posting_date',
                'label' => Yii::t('common','Posting Date'),
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs bg-gray','style' => 'min-width:50px; max-width:80;'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => ' ','style' => 'min-width:50px;max-width:80;'],
                'value' => function($model){
                    $date =  ($model->posting_date)? $model->posting_date : ' ';
                    return date('Y-m-d',strtotime($date));
                },                
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'posting_date',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                        ],
                    ],                    
                ]),
            ],

            [
                
                'attribute'         => 'vat_percent',
                'label'             => Yii::t('common','Tax Filter'),
                'format'            => 'html',
                'contentOptions'    => ['class' => ''],
                'headerOptions'     => ['class' => 'bg-gray','style' => 'min-width:100px;'],
                'filterOptions'     => ['class' => ''],
                'value'             => function($model){ return $model->vattypes->name; },
                'filter'            => \yii\helpers\ArrayHelper::map(common\models\VatType::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->orderBy('name')->asArray()->all(), 'vat_value', 'name'
                ),
 
            ],

            [
                
                'attribute'         => 'no_',
                'label'             => Yii::t('common','Document No'),
                'headerOptions'     => ['class' => 'bg-gray'],
                'contentOptions'    => ['class' => 'font-roboto'],
                'format'            => 'raw',
                'value'             => function($model){

                    $html = ($model->invfromCreditNote)? Html::a('<i class="fas fa-link text-green"></i> ',
                    ['/accounting/posted/posted-invoice','id' => base64_encode($model->cn_reference)],['target' => '_blank'])
                     : '<i class="fas fa-unlink text-red"></i> ';
                    $html.= Html::a($model->no_,['view','id' => base64_encode($model->id),'no' => $model->no_]);
                    
                    return $html;

                },
                

            ],
            
            [
                'attribute' => 'ext_document',
                'label' => Yii::t('common','External Document'),
                'headerOptions'     => ['class' => 'bg-gray'],
                'format' => 'raw',
                'value' => function($model){
                    return $model->ext_document;
                }

            ],

            [
                'attribute' => 'cust_no_',
                'label' => Yii::t('common','Customer'),
                'headerOptions'     => ['class' => 'bg-gray', 'style' => 'max-width:100px; word-wrap: break-word !important;'],
                'contentOptions'    => ['style' => ' '],
                'format' => 'raw',
                'value' => function($model){
                    $name = $model->customer->name;
                    return Html::a(mb_substr($name,0,30).'...',['view','id' => base64_encode($model->id),'no' => $model->no_], ['title' => $name]);
                }

            ],
      
            [
                'label'             => Yii::t('common','Amount'),
                'format'            => 'html',
                'headerOptions'     => ['class' => 'text-right bg-gray','style'=>'min-width:80px;'],
                'contentOptions'    => ['class' => 'text-right'],
                'value'             => function($model){
                    return number_format(abs($model->sumTotals->total),2);
                }  
            ],

            
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'headerOptions'     => ['class' => 'hidden-xs bg-gray','style'=>'min-width:250px;'],
                'contentOptions' => ['class' => 'hidden-xs text-right'],
                'filterOptions'     => ['class' => 'hidden-xs'],                
                'template'=>'<div class="btn-group btn-group text-center" role="group"> {print}  {update} {hidden}  {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Print'),
                        [
                            '/accounting/credit-note/print',
                            'id' => base64_encode($model->id),
                            'no' => $model->no_
                        ],
                        ['class'=>'btn btn-info-ew btn-sm','target'=>'_blank']);
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> '.Yii::t('common','View'),['/accounting/credit-note/view','id' => base64_encode($model->id),'no' => $model->no_],['class'=>'btn btn-default-ew btn-sm']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common','Delete'),['/accounting/credit-note/delete','id' => base64_encode($model->id),'no' => $model->no_],[
                            'class' => 'btn btn-danger-ew btn-sm',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        if ($model->status == 'Posted') {
                            return '<button type="button" class="btn btn-warning-ew btn-sm" disabled><i class="far fa-edit"></i> '.Yii::t('common','Update').'</button>';
                        }else{
                            return Html::a('<i class="far fa-edit"></i> '.Yii::t('common','Update'),['/accounting/credit-note/update','id' => base64_encode($model->id),'no' => $model->no_],['class'=>'btn btn-warning-ew btn-sm']);
                        }
                        
                    },

                    'hidden' => function($url,$model,$key){
                        if ($model->show_doc == '1') {
                            return '<button type="button" style="min-width: 70px;" class="btn btn-primary-ew btn-sm show-doc-disable" title="แสดงเอกสารในรายงาน"><i class="far fa-eye"></i> '.Yii::t('common','Show').'</button>';
                        }else{
                            return '<button type="button" style="min-width: 70px;" class="btn btn-default-ew btn-sm show-doc-enable" title="ไม่แสดงเอกสารในรายงาน"><i class="far fa-eye-slash"></i> '.Yii::t('common','Hide').'</button>';
                        }
                        
                    }

                  ]
              ],

        ] ?>


<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <i class="fas fa-circle text-orange"></i> Not Posted </br>
                <i class="fas fa-check-circle text-green"></i> Posted
            </div>
        </div>
    </div>
</div>

<div class="row">
        <div class="col-sm-6"><h4><?=$this->title?></h4></div>
        <div class="col-sm-6 text-right">
            <?=ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                     
  
                    [
                        'attribute' => 'posting_date',
                        'label' => Yii::t('common','Posting Date'),
                        'format' => 'html',
                        'headerOptions' => ['class' => 'hidden-xs bg-gray','style' => 'min-width:50px; max-width:80;'],
                        'filterOptions' => ['class' => 'hidden-xs'],
                        'contentOptions' => ['class' => ' ','style' => 'min-width:50px;max-width:80;'],
                        'value' => function($model){
                            $date =  ($model->posting_date)? $model->posting_date : ' ';
                            return date('Y-m-d',strtotime($date));
                        }
                    ],

                    [
                        
                        'attribute'         => 'vat_percent',
                        'label'             => Yii::t('common','Tax Filter'),
                        'format'            => 'html',
                        'contentOptions'    => ['class' => ''],
                        'headerOptions'     => ['class' => 'bg-gray','style' => 'min-width:100px;'],
                        'filterOptions'     => ['class' => ''],
                        'value'             => function($model){ return $model->vattypes->name; }                    
                    ],

                    [
                        
                        'attribute'         => 'no_',
                        'label'             => Yii::t('common','Document No'),
                        'headerOptions'     => ['class' => 'bg-gray'],
                        'contentOptions'    => ['class' => 'font-roboto'],
                        'format'            => 'raw',
                        'value'             => function($model){
        
                            $html = ($model->invfromCreditNote)? Html::a('<i class="fas fa-link text-green"></i> ',
                            ['/accounting/posted/posted-invoice','id' => base64_encode($model->cn_reference)],['target' => '_blank'])
                             : '<i class="fas fa-unlink text-red"></i> ';
                            $html.= Html::a($model->no_,['view','id' => base64_encode($model->id),'no' => $model->no_]);
                            
                            return $html;
        
                        },
                        
        
                    ],

                    [
                        'attribute' => 'ext_document',
                        'label' => Yii::t('common','External Document'),
                        'headerOptions'     => ['class' => 'bg-gray'],
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->ext_document;
                        }
        
                    ],
                    
                    [
                        'attribute' => 'cust_no_',
                        'label' => Yii::t('common','Customer'),
                        'headerOptions'     => ['class' => 'bg-gray'],
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->customer->name;
                        }
        
                    ],

                    [
                        'label'             => Yii::t('common','Amount'),
                        'format'            => 'html',
                        'headerOptions'     => ['class' => 'text-right bg-gray','style'=>'min-width:80px;'],
                        'contentOptions'    => ['class' => 'text-right'],
                        'value'             => function($model){
                            return abs($model->sumTotals->total);
                        }  
                    ],
                    [
                        'label'             => Yii::t('common','Before Vat'),
                        'format'            => 'html',
                        'headerOptions'     => ['class' => 'text-right hidden bg-gray'],
                        'filterOptions'     => ['class' => 'hidden'],
                        'contentOptions'    => ['class' => 'text-right hidden'],
                        'value'             => function($model){
                            return abs($model->sumTotals->before);
                        }  
                    ],
                
                    [
                        'label'             => Yii::t('common','Vat'),
                        'headerOptions'     => ['class' => 'text-right  bg-gray'],
                        'contentOptions'    => ['class' => 'text-right '],
                        'value'             => function($model){
                            return abs($model->sumtotals->incvat);
                        }  
                    ],
        
                    [
                        'label'             => Yii::t('common','Vat Type'),
                        'headerOptions'     => ['class' => 'text-right   bg-gray'],
                        'contentOptions'    => ['class' => 'text-right  '],
                        'value'             => function($model){
                            return $model->include_vat == 1 
                                        ? Yii::t('common','Exclude Vat')
                                        : Yii::t('common','Include Vat');
                        }  
                    ],
                    
         
                     
                ],
                'columnSelectorOptions'=>[
                    'label' => 'Columns',
                    'class' => 'btn btn-success-ew',
                    'title' => $this->title
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,                                
                ],
                'fontAwesome' => true,
                //'selectedColumns'=> [1,3,4,5,7,8],
                // 'dropdownOptions' => [
                //     'label' => 'Export All',
                //     'class' => 'btn btn-primary'
                // ],
                'target' => ExportMenu::TARGET_BLANK,
                'filename' => 'Invoice',
                
            ]); 
        ?>   
        </div>
    </div>
     
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $column,        
        'responsive' => true,
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>'First',   // Set the label for the "first" page button
            'lastPageLabel'=>'Last',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
    
 </div>
<style>
.input-group i{     
    position: absolute;
    top: 16px;
    right: 15px;
    z-index: 3;
}
 
</style>
<?php
    $options = ['depends' => [\yii\web\JqueryAsset::className()]];

 
    $this->registerJsFile('//cdn.jsdelivr.net/momentjs/latest/moment.min.js',$options);
    $this->registerJsFile('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js',$options);
    
    $this->registerCssFile('//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css'); 
?>




<?php
$Yii = 'Yii';
$js =<<<JS
     
    const showDoc = (obj, callback) =>{
        fetch("?r=accounting/credit-note/change-show-doc", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
            callback(res);
        })
        .catch(error => {
            console.log(error);
        });
    }
    
    $('body').on('click', '.show-doc-disable', function(){
        let el      = $(this);
        let keys    = JSON.parse($(this).closest('tr').attr('data-key'));        

                    // เปลี่ยนค่าก่อน
                    el.removeClass('btn-primary-ew').removeClass('show-doc-disable');
                    el.addClass('btn-default-ew').addClass('show-doc-enable');
                    el.html('<i class="far fa-eye-slash"></i> {$Yii::t("common","Hide")}');

        showDoc({id:keys.id, no_:keys.no_, sw: 0}, res =>{
                if(res.status!=200){ // ถ้าเปลี่ยนไม่สำเร็จ ให้กลับมาค่าเดิม
                    el.addClass('btn-primary-ew').addClass('show-doc-disable');
                    el.removeClass('btn-default-ew').removeClass('show-doc-enable');
                    el.html('<i class="far fa-eye"></i> {$Yii::t("common","Show")}');
                }
        })
        
        
    });

    $('body').on('click', '.show-doc-enable', function(){
        let el      = $(this);
        let keys    = JSON.parse($(this).closest('tr').attr('data-key'));        
         
                    // เปลี่ยนค่าก่อน
                    el.addClass('btn-primary-ew').addClass('show-doc-disable');
                    el.removeClass('btn-default-ew').removeClass('show-doc-enable');
                    el.html('<i class="far fa-eye"></i> {$Yii::t("common","Show")}');
        showDoc({id:keys.id, no_:keys.no_, sw: 1}, res =>{
            if(res.status!=200){ // ถ้าเปลี่ยนไม่สำเร็จ ให้กลับมาค่าเดิม
                el.removeClass('btn-primary-ew').removeClass('show-doc-disable');
                el.addClass('btn-default-ew').addClass('show-doc-enable');
                el.html('<i class="far fa-eye-slash"></i> {$Yii::t("common","Hide")}');
            }
        })
        
        
    });

JS;
$this->registerJS($js);
?>