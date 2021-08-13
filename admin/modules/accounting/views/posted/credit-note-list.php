<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;

use common\models\RcInvoiceLine;

use admin\modules\accounting\models\FunctionAccounting;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Invoice Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-invoice-header-index">
<?php 
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'no_',
            //'posting_date',
            [
                'attribute' => 'posting_date',
                'label' => Yii::t('common','Posting date'),
                //'format' => 'raw',
                // 'filterOptions' => [
                //     'options' => ['autocomplete' => 'off'],
                // ],
                'value' => function($model){
                    return date('Y-m-d',strtotime($model->posting_date));

                }

            ],
            [
                'attribute' => 'no_',
                'label' => Yii::t('common','Document No.'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->no_,['posted-invoice','id' => base64_encode($model->id)]);

                }

            ],
            //'cust_no_',
            [
                'attribute' => 'cust_name_',
                'label' => Yii::t('common','Customer'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->cust_name_,['posted-invoice','id' => base64_encode($model->id)]);

                }

            ],
            //'cust_name_',
            'cust_address:ntext',
            // 'cust_address2:ntext',
            // 'posting_date',
            // 'order_date',
            // 'ship_date',
            // 'cust_code',
            // 'sales_people',
            // 'document_no_', 
            [
                
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    // $invLine = RcInvoiceLine::find()->where(['source_id' => $model->id]);
                    // $sumLine = $invLine->sum('quantity * unit_price');

                    $sumLine = FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');

                    return number_format($sumLine,2);

                }

            ],
            

            //['class' => 'yii\grid\ActionColumn'],
        ] ?>
<?php
 echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => [               
                [
                    'attribute' => 'posting_date',
                    'label' => Yii::t('common','Posting date'),
                    //'format' => 'raw',
                    // 'filterOptions' => [
                    //     'options' => ['autocomplete' => 'off'],
                    // ],
                    'value' => function($model){
                        return date('Y-m-d',strtotime($model->posting_date));
    
                    }
    
                ],
                'no_',
                'cust_name_',
                
  
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
                'class' => 'btn btn-danger'
            ],
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Export All',
                'class' => 'btn btn-primary'
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                //ExportMenu::FORMAT_PDF => false,
             // ExportMenu::FORMAT_PDF => [
             //                 'label' => Yii::t('common', 'PDF'),
             //                 'icon' =>  'file-pdf-o',
             //                 'iconOptions' => ['class' => 'text-danger'],
             //                 //'linkOptions' => [],
             //                 'options' => ['title' => Yii::t('common', 'Portable Document Format')],
             //                 'alertMsg' => Yii::t('common', 'The PDF export file will be generated for download.'),
             //                 'mime' => 'application/pdf',
             //                 'extension' => 'pdf',
             //                 'writer' => 'PDF',
             //             ],
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
    $(document).ready(function(){  
        // Change Link Create
        $('a.ew-bt-app-new').attr('href','index.php?r=accounting%2Fsaleinvoice%2Fcreate&cn=true');
        $('a.ew-bt-app-home').attr('href','index.php?r=accounting%2Fposted%2Fcredit-note-list');
        var element = $('input[name="RcinvheaderSearch[posting_date]"]');
         element.parent('td').attr('class','input-group');
         
        element.parent('td').append('<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>');
        element.attr('style','width:200px;');
        // var template = '<div class="input-group date">'+
        //                     '<input type="text" class="form-control " name="RcinvheaderSearch[posting_date]">'+
        //                     '<div class="form-control-feedback">'+
        //                         '<span class="glyphicon glyphicon-th" for="date"></span>'+
        //                     '</div>'+
        //                 '</div>';
        // element.parent('td').html(template);
    })
    // $(function() {
    //     $('input[name="RcinvheaderSearch[posting_date]"]').daterangepicker({
    //         autoUpdateInput: false,
    //         locale: {
    //             cancelLabel: 'Clear'
    //         }
    //     });
    // });

    /* http://www.daterangepicker.com/ */

    $(document).ready(function(){
        $('input[name="RcinvheaderSearch[posting_date]"]').attr('autocomplete','off').attr('readonly','readonly').css('width:200px');
    })

    $(function() {
         
        $('input[name="RcinvheaderSearch[posting_date]"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                applyLabel: '{$Yii::t("common","Apply")}',
                cancelLabel: '{$Yii::t("common","Cancel")}',
                format: 'DD/MM/YYYY'
            }
        });

        $('input[name="RcinvheaderSearch[posting_date]"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            
        });

        $('input[name="RcinvheaderSearch[posting_date]"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });
    $('body').on('click','button.applyBtn',function(){
        $('input[name="RcinvheaderSearch[posting_date]"]').change();
    })

    // /.Date Fillter
JS;
$this->registerJS($js);
?>