<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

use common\models\RcInvoiceLine;

use admin\modules\accounting\models\FunctionAccounting;

use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;
 
$this->title = Yii::t('common', 'Sale Invoice Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-invoice-header-index font-roboto">
<h3>ใบกำกับภาษี (Post แล้วเท่านั้น)</h3>    
<?php 
        $gridColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'font-roboto'],
                'headerOptions' => ['style' => 'width:30px;']
            ],
            [
                'attribute' => 'posting_date',
                'label' => Yii::t('common','Posting Date'),
                'format' => 'html',
                'headerOptions' => ['class' => 'hidden-xs','style' => 'width:80px;'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'font-roboto'],
                'value' => function($model){
                    return ($model->posting_date)? date('Y-m-d',strtotime($model->posting_date)) : ' ';
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
                'format'            => 'raw',
                'contentOptions'    => ['class' => 'font-roboto'],
                'headerOptions'     => ['class' => '','style' => 'min-width:100px;'],
                'filterOptions'     => ['class' => ''],
                'value'             => function($model){ return ($model->vat_percent > 0)? 'Vat': 'No Vat'; },
                'filter'            => Html::activeDropDownList($searchModel, 'vat_percent',
                    [
                        '7'         => 'Vat',
                        '0'         => 'No Vat',
                    ],                   
                    [
                        'class'     => 'form-control',
                        'prompt'    => Yii::t('common','Show All'),
                    ]),
            ],
        
            [
                'attribute' => 'no_',
                'label' => Yii::t('common','Document No.'),
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:90px;'],
                'contentOptions' => ['class' => 'font-roboto'],
                'value' => function($model){
                    return Html::a($model->no_,['posted-invoice','id' => base64_encode($model->id)], ['class' => $model->doc_type == 'Sale' ?: 'text-red']);

                }

            ],
            //'cust_no_',
            [
                'attribute' => 'cust_name_',
                'label' => Yii::t('common','Customer'),
                'format' => 'raw',
                'value' => function($model){                 
                    if(preg_match('/\p{Thai}/u', $model->cust_name_) === 1){
                        return Html::a(mb_substr($model->cust_name_, 0, 30),['posted-invoice','id' => base64_encode($model->id)]);
                    }else{
                        return Html::a(substr($model->cust_name_, 0, 20),['posted-invoice','id' => base64_encode($model->id)]);
                    }
                }

            ], 
            [
                'attribute'         => 'postinggroup',
                'label'             => Yii::t('common','Customer Group'),
                'format'            => 'raw',
                'contentOptions'    => ['class' => ''],
                'headerOptions'     => ['class' => '','style' => 'width:100px;'],
                'filterOptions'     => ['class' => ''],
                'value'             => function($model){ 
                    //return ($model->customer->postingGroup->name == '01')? Yii::t('common','General'): Yii::t('common','Modern Trade'); 
                    return Yii::t('common',$model->customer->postingGroup->name);
                },
                'filter' => Html::activeDropDownList($searchModel,'postinggroup',
                    // [
                    //     '01' => Yii::t('common','General'),
                    //     '02' => Yii::t('common','Modern Trade')
                    // ],
                    ArrayHelper::map(\common\models\CommonBusinessType::find()->orderBy(['name' => SORT_ASC ])->all(),'id',function($model){
                        return Yii::t('common',$model->name);
                    }),
                    [
                        'class'     => 'form-control',
                        'prompt'    => Yii::t('common','Show All'),
                    ]),
            ],    
            [
                
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    // $invLine = RcInvoiceLine::find()->where(['source_id' => $model->id]);
                    // $sumLine = $invLine->sum('quantity * unit_price');

                    //$sumLine = FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');

                    return '<div class="'.($model->doc_type == 'Sale' ?: 'text-red').'">'.number_format($model->sumtotals->total,2).'</div>';

                }

            ],
            
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:300px;'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{print-receipt}  {print} {print-form}   </div>',
                'options'=> ['style'=>'width:250px;'],
                'buttons'=>[

                    'print-receipt' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Receipt'),['/accounting/posted/print-receipt','id'=> base64_encode($model->id), 'footer' => 1],
                        ['class'=>'btn btn-default-ew','target'=>'_blank']);
                    },
                    
                    'print-new' => function($url,$model,$key){      
                        // ยังไม่เสร็จ 14/01/2020                
                        return Html::a('<i class="fas fa-print"></i> Letter',['/accounting/posted/print','id'=> base64_encode($model->id), 'footer' => 1, 'paper' => 'Letter', 'head' => 'false'],
                        ['class'=>'btn btn-info-ew','target'=>'_blank']);
                    },

                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> Letter',['/accounting/posted/print-inv','id'=> base64_encode($model->id), 'footer' => 1],
                        ['class'=>'btn btn-info-ew','target'=>'_blank']);
                    },

                    'print-form' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> Form',['/accounting/posted/print','id'=> base64_encode($model->id), 'footer' => 1],
                        ['class'=>'btn btn-warning-ew','target'=>'_blank']);
                    },
                    

                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-default']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success']);
                    }
                ]
            ]

            //['class' => 'yii\grid\ActionColumn'],
        ] ?>

    <div class="text-right">
        <?php
        echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [               
                        [
                            'attribute' => 'posting_date',
                            'label' => Yii::t('common','Posting date'),
                            'value' => function($model){
                                return date('Y-m-d',strtotime($model->posting_date));
                            }
                        ],
                        'no_',
                        [
                            'attribute' =>  'cust_name_',
                            'value' => function($model){
                                return mb_substr($model->cust_name_,0, 20);
                            }
                        ],               
                        'cust_address:ntext',
                        [                    
                            'label' => Yii::t('common','Balance'),
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function($model){
                                $sumLine = FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');    
                                return number_format($sumLine,2);    
                            }
            
                        ],               
                    ],
                    'columnSelectorOptions'=>[
                        'label' => 'Columns',
                        'class' => 'btn btn-danger-ew'
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
                                'family' => ['thaimono','saraban'],
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

    <div class="table-responsive"  >
   
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,
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
    // Date Fillter 

    // $(document).ready(function(){  
    //     var element = $('input[name="RcinvheaderSearch[posting_date]"]');
    //      element.parent('td').attr('class','input-group');
         
    //     element.parent('td').append('<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>');
    //     element.attr('style','width:200px;');

    // })
 

    /* http://www.daterangepicker.com/ */

    // $(document).ready(function(){
    //     $('input[name="RcinvheaderSearch[posting_date]"]').attr('autocomplete','off').attr('readonly','readonly').css('width:200px');
    // })

    // $(function() {
         
    //     $('input[name="RcinvheaderSearch[posting_date]"]').daterangepicker({
    //         autoUpdateInput: false,
    //         locale: {
    //             applyLabel: '{$Yii::t("common","Apply")}',
    //             cancelLabel: '{$Yii::t("common","Cancel")}',
    //             format: 'DD/MM/YYYY'
    //         }
    //     });

    //     $('input[name="RcinvheaderSearch[posting_date]"]').on('apply.daterangepicker', function(ev, picker) {
    //         $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            
    //     });

    //     $('input[name="RcinvheaderSearch[posting_date]"]').on('cancel.daterangepicker', function(ev, picker) {
    //         $(this).val('');
    //     });

    // });
    // $('body').on('click','button.applyBtn',function(){
    //     $('input[name="RcinvheaderSearch[posting_date]"]').change();
    // })

    // /.Date Fillter
JS;
$this->registerJS($js);
?>