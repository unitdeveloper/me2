<?php
ini_set('max_execution_time', 300);
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;
use kartik\export\ExportMenu;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

use common\models\SalesPeople;

$this->title = Yii::t('common', 'Report Invoice');
$this->params['breadcrumbs'][] = $this->title;

$column = [
    //['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'posting_date',
        'label' => Yii::t('common','Posting Date'),
        'format' => 'html',
        'headerOptions' => ['class' => ' ','style' => 'min-width:50px; max-width:175;'],
        'contentOptions' => ['class' => 'font-roboto','style' => 'min-width:50px; max-width:175;'],
        'filterOptions' => ['class' => ' ','style' => 'min-width:50px; max-width:175;'],
        'value' => function($model){
            return date('d/m/Y',strtotime(($model->posting_date)? $model->posting_date : ' '));
        },
        'filter' => DatePicker::widget([
            'model' => $searchModel,
            'attribute' => 'posting_date',
            'type' => DatePicker::TYPE_COMPONENT_PREPEND, 
            'removeButton' => false,
            'pluginOptions' => [
                'format' => 'mm/yyyy',
                'autoclose' => true,
                'minViewMode' => 1,
            ]
            
        ]),
    ],

    [
        'label'             => Yii::t('common','Posted'),
        'format'            => 'raw',
        'headerOptions'     => ['class' => ' '],
        'contentOptions'    => ['class' => 'text-center font-roboto'],
        'value'             => function($model){
            return $model->status == 'Posted'
                    ? '<i class="fas fa-check text-green"></i>'
                    : '<i class="fas fa-times" style="color:#f7f7f7;"></i>';
        }  
    ],


    [
        'attribute'         => 'vat_percent',
        'label'             => Yii::t('common','Tax Filter'),
        'format'            => 'raw',
        'contentOptions'    => ['class' => 'font-roboto'],
        'headerOptions'     => ['class' => '','style' => 'min-width:100px;'],
        'filterOptions'     => ['class' => ''],
        'value'             => function($model){ return ($model->vat_percent > 0)? 'Vat': 'No Vat'; },
        'filter'            => Html::activeDropDownList($searchModel,'vat_percent',
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
        'attribute'         => 'no_',
        'label'             => Yii::t('common','Document No'),
        'format'            => 'raw',
        'contentOptions'    => ['class' => 'font-roboto'],
        'value'             => function($model){ 
            if($model->status=='Posted'){
                return Html::a($model->no_,['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank','class' => 'text-warning']);
            }else{
                return Html::a($model->no_,['/accounting/saleinvoice/update','id' => $model->id],['target' => '_blank','class' => 'text-info']);
            }
            
        },
         
    ],

    [
        //'attribute'         => 'no_',
        'label'             => Yii::t('common','Excel'),
        'format'            => 'raw',
        'contentOptions'    => ['class' => 'font-roboto'],
        'value'             => function($model){ 
            return Html::a('<i class="fas fa-file-excel text-green"></i>',
            ['/accounting/print/export','id' => $model->id, 'vat' => $model->vat_percent, 'status' => $model->status],
            ['target' => '_blank', 'data-pjax' => '0', 'class' => 'text-warning']);
        },
         
    ],
    'cust_name_',
    //'customer.vat_regis',

    [
        'label'             => Yii::t('common','Before Vat'),
        'headerOptions'     => ['class' => 'text-right'],
        'contentOptions'    => ['class' => 'text-right font-roboto'],
        'value'             => function($model){
            return number_format($model->beforeVat,2);
        }  
    ],

    [
        'label'             => Yii::t('common','Vat'),
        'headerOptions'     => ['class' => 'text-right'],
        'contentOptions'    => ['class' => 'text-right font-roboto'],
        'value'             => function($model){
            return number_format((($model->beforeVat) * $model->vat_percent)/ 100, 2);
        }  
    ],
    [
  
        'label'             => Yii::t('common','To Vat'),
        'format'            => 'raw',
        'headerOptions'     => ['class' => 'text-center'],
        'contentOptions'    => ['class' => 'text-center'],
        'value'             => function($model){ 

            
            if($model->vat_percent <=0){
                if($model->status=='Posted'){

                    // REVENUE
                        // 0 นับยอดขาย
                        // 1 ไม่นับยอดขาย (นำส่งสรรพากร)

                    if($model->field->revenue==0){ 
                        // ถ้าเลขที่ใน field rf_revenue มีใน rc_invoice_header.id 
                        if($model->field->refRevenue){
                            // แสดง link ที่สร้างโดยใบนั้นๆ
                            $html = '<div class="btn-group dropup" >                                   
                                        <button type="button" class="btn btn-success-ew dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-check-square"></i> '.Yii::t('common','To Vat').'  
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" style="border:1px solid #ccc; box-shadow: 0 6px 12px rgba(0,0,0,.175);">                                 
                                            <li>
                                                '.Html::a('<i class="fas fa-exchange-alt"></i> '.Yii::t('common','To Vat'),
                                                'javascript:void(0)',
                                                ['class' => 'modal-invoice-convert','data-key'=> $model->id]).'
                                            </li>  
                                            <li role="separator" class="divider"></li>
                                            <li>
                                                '.Html::a('<i class="fas fa-check-square"></i> '.Yii::t('common','View'),
                                                ['/accounting/posted/posted-invoice','id' => base64_encode($model->field->rf_revenue)],
                                                ['target' => '_blank','class' => ' ']).'
                                            </li>
                                        </ul>
                                    </div>';

                            return $html;

                            // return Html::a('<i class="fas fa-check-square"></i> '.Yii::t('common','Convert'),
                            // ['/accounting/posted/posted-invoice','id' => base64_encode($model->field->rf_revenue)],
                            // ['target' => '_blank','class' => 'btn btn-success-ew']);

                        }else{
                            // แสดงปุ่ม convert
                            if($model->total > 0){
                                return Html::a('<i class="fas fa-exchange-alt"></i> '.Yii::t('common','To Vat'),
                                'javascript:void(0)',
                                ['class' => 'btn btn-info-ew modal-invoice-convert','data-key'=> $model->id]); 
                            }else{
                                return '';
                            }

                        }
                        

                    }else{

                        if($model->total > 0){
                            return Html::a('<i class="fas fa-exchange-alt"></i> '.Yii::t('common','To Vat'),
                            'javascript:void(0)',
                            ['class' => 'btn btn-info-ew modal-invoice-convert','data-key'=> $model->id]); 
                        }else{
                            return '';
                        }
                        
                    }
                }else{
                    // return Html::a('<i class="fas fa-ban"></i> '.Yii::t('common','Convert'),'javascript:void(0)',['class' => 'btn btn-default-ew','disabled'=> true]); 
                    return '';
                }
                
            }else{
                //return Html::a('<i class="fas fa-ban"></i> '.Yii::t('common','Convert'),'javascript:void(0)',['class' => 'btn btn-default-ew','disabled'=> true]); 
                return '';
            }
            
        },
         
    ],
   
     
    ];
?>
<style>
.grandTotal,
.grandTotalText{
    margin-top:18px;
    color: white;
    background-color: #082104;
    padding: 5px;
    font-size: 17px;
}
.vatTotal{
    margin-top:15px;
}
.current-document{
    color:#ccc;
}
</style>
<?php 

/**
 * 
 * 
 * START PAGE
 * 
 * 
 */
?>
<div class="invoice-header-index" ng-init="Title='<?=$this->title?>'">

<h3>ใบกำกับภาษี(ทั้งหมด)</h3>    
    <div class="row">
        <div class="col-sm-6"><h4><?=$this->title?></h4></div>
        <div class="col-sm-6 text-right">
            <?=ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $column,
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
                'selectedColumns'=> [2,3,5,6,7],
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
    <div class="table-responsive"  >
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table  table-bordered '],
            'rowOptions' => function($model){
               
            },
            'columns' => $column,
            'pager' => [
                'options'=>['class' => 'pagination'],// set clas name used in ui list of pagination
                'prevPageLabel'     => '«',         // Set the label for the "previous" page button
                'nextPageLabel'     => '»',         // Set the label for the "next" page button
                'firstPageLabel'    => Yii::t('common','page-first'),     // Set the label for the "first" page button
                'lastPageLabel'     => Yii::t('common','page-last'),      // Set the label for the "last" page button
                'nextPageCssClass'  => 'next',      // Set CSS class for the "next" page button
                'prevPageCssClass'  => 'prev',      // Set CSS class for the "previous" page button
                'firstPageCssClass' => 'first',     // Set CSS class for the "first" page button
                'lastPageCssClass'  => 'last',      // Set CSS class for the "last" page button
                'maxButtonCount'    => 5,           // Set maximum number of page buttons that can be displayed
                ],
        ]); ?>
    </div>
</div>

 
 
<div class="modal fade modal-full" id="modal-invoice-convert" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog"  >
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                    'id' => 'Form-Convert-Invoice',
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'options' => [
                        'data' => ['pjax' => true],
                        //'enctype' => 'multipart/form-data',
                        'data-key' => $model->id,

                        ]]); 
            ?>
            <div class="modal-header bg-orange">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fas fa-exchange-alt"></i> <?=Yii::t('common','Convert')?></h4>
            </div>
            <div class="modal-body">
                
                <div class="row">
                
                    <div class="col-md-4 pull-right">
                        <input type="hidden" name="id" >
                        <input type="hidden" name="status">
                        <?=$form->field($model,'no_')->textInput(['style' => 'background-color: #ef8333;color: #fff;font-size: 15px;'])->label(Yii::t('common','Document No'))?>
                    </div>                       
                    <div class="col-md-4 pull-right text-right current-document">
                        <label><?=Yii::t('common','Current No')?></label>
                        <div style="margin-top: 7px;"><span id="document_no">{DOCUMENT_NO}</span> <i class="fas fa-angle-double-right text-orange"></i></div>
                         
                    </div>            
                </div>

                <div class="row">
                    <div class="col-md-4 pull-right">                          
                    </div>                     
                </div>
                
                <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-2"><?=$form->field($model,'cust_no_')->textInput(['readonly' => true])?></div>
                        <div class="col-sm-3"><?=$form->field($model,'cust_code')->textInput(['readonly' => true])?></div>
                        <div class="col-sm-7"><?=$form->field($model,'cust_name_')?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10">
                            <?= $form->field($model, 'cust_address')->textarea(['rows' => 1]) ?> 
                        </div>
                        <div class="col-sm-2">
                        <?php
                            $Sales = SalesPeople::find()
                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->andWhere(['status' => 1])
                            ->orderBy(['code' => SORT_ASC])
                            ->all();

                            $salespeople = arrayHelper::map($Sales,'id', function ($element) {
                                            return '['.$element['code'] .']  ' .$element['name'];
                                        });

                            echo $form->field($model, 'sale_id') ->dropDownList($salespeople,
                                        [
                                            'class' => 'sale_id',
                                            'prompt'=> Yii::t('common','Not Defined')
                                        ]
                                    )->label(Yii::t('common','Sales'));

                            // "disabled"=> ($rules_id != 7 && $rules_id != 4 && $rules_id != 1 ),
                                    ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 ">   
                        <?php
                            if($model->order_date=='') {
                                $model->order_date = date('Y-m-d');
                            }else {
                                $model->order_date = date('Y-m-d',strtotime($model->order_date));
                            }
                            echo $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                                    'options'       => ['placeholder' => Yii::t('common','Order date').'...'],
                                    'value'         => date('Y-m-d',strtotime($model->order_date)),
                                    'type'          => DatePicker::TYPE_COMPONENT_APPEND,
                                    'removeButton'  => false,
                                    'pluginOptions' => [
                                        'format'    => 'yyyy-mm-dd',
                                        'autoclose' =>true
                                    ]
                            ])->label(Yii::t('common','Order date'));
                            ?>
                        <?php
                            if($model->posting_date=='') {
                                $model->posting_date = date('Y-m-d');
                            }else {
                                $model->posting_date = date('Y-m-d',strtotime($model->posting_date));
                            }
                            echo $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                                    'options'   => ['placeholder' => Yii::t('common','Posting date').'...','style' => 'background-color:#e2f3fb;'],
                                    'value'     => date('Y-m-d',strtotime($model->order_date)),
                                    'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                    'removeButton'  => false,
                                    'pluginOptions' => [
                                        'format'    => 'yyyy-mm-dd',
                                        'autoclose' => true
                                    ]
                            ])->label(Yii::t('common','Posting date'));
                            ?>
                    </div>  
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                       <div id="render-invoice"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common', 'Close')?></button>                
                <?= Html::submitButton($model->isNewRecord 
                        ? '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Save') 
                        : '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Save'),
                            [
                                'class' => 'btn btn-success ',
                                'data-rippleria' => true,
                            ]
                ) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?=$this->render('index-react') ?>



<?php
$js=<<<JS

// $('body').on('change','input.qty',function(){
//     let grandTotal = 0;
//     $.each( $('.react-bs-container-body').find('tr').find('div.sumLine'), function(indexInArray, e){ 
         
//        grandTotal += $(e).attr('data') * 1;					
        	 							
//         console.log($(e).attr('data'));
//     });
    
//     $('.total').text(grandTotal);
// });
 
JS;

$this->registerJS($js);