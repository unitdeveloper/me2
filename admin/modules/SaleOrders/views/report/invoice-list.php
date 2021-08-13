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

use common\models\ViewRcInvoice;
use fedemotta\datatables\DataTables;

$this->title = Yii::t('common', 'Report Invoice').' '.Yii::t('common','For Years').' '.Yii::$app->session->get('workyears');
$this->params['breadcrumbs'][] = $this->title;

$column = [
    [
        'contentOptions'    => ['class' => 'font-roboto'],
        'class' => 'yii\grid\SerialColumn'
    ],
    [
        'attribute' => 'posting_date',
        'label' => Yii::t('common','Posting Date'),
        'format' => 'html',
        'headerOptions' => ['class' => ' ','style' => 'min-width:50px; max-width:75;'],
        'contentOptions' => ['class' => 'font-roboto'],
        //'filterOptions' => ['class' => ' '],
        'value' => function($model){
            return date('d/m/Y',strtotime(($model->posting_date)? $model->posting_date : ' '));
        },
        // 'filter' => DatePicker::widget([
        //     'model' => $searchModel,
        //     'attribute' => 'posting_date',
        //     'type' => DatePicker::TYPE_COMPONENT_PREPEND, 
        //     'removeButton' => false,
        //     'pluginOptions' => [
        //         'format' => 'mm/yyyy',
        //         'autoclose' => true,
        //         'minViewMode' => 1,
        //     ]
        // ]),
    ],
    [
        'label' => Yii::t('common','Sale Order'),
        //'headerOptions' => ['style' => 'min-width:50px; max-width:375;'],
        'contentOptions'    => ['class' => 'font-roboto'],
        'format' => 'raw',
        'value' => function($model){
            if ($model->saleorder->no=='0'){
                $html = '<i class="fas fa-exclamation-triangle text-orange"></i>';
            }else{
                $html = $model->saleorder->no;
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/SaleOrders/saleorder/view','id' => $model->saleorder->id],['target' => '_blank']);
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/SaleOrders/saleorder/print-page','id' => $model->saleorder->id,'footer' => 1],['target' => '_blank']);    
            }
            return $html;
        }
    ],
    [
        'attribute'         => 'no_',
        'label'             => Yii::t('common','Document No'),
        'contentOptions'    => ['class' => 'font-roboto'],
        'format'            => 'raw',
        'value'             => function($model){ 
            if($model->status=='Posted'){
                $html = '<span class="text-warning">'.$model->no_.'</span>';
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank','class' => 'text-info']);
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/posted/print-inv','id' => base64_encode($model->id),'footer' => 1],['target' => '_blank','class' => 'text-info']);
            }else{
                $html = '<span class="text-info">'.$model->no_.'</span>';
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/saleinvoice/update','id' => $model->id],['target' => '_blank','class' => 'text-info']);
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/saleinvoice/print-inv-page','id' => $model->id,'footer' => 1],['target' => '_blank','class' => 'text-info']);
            }
            return $html;
        },
    ],

    [
        'footerOptions'    => ['class' => 'text-right'],
        'label'     => Yii::t('common','Customer'),
        'value' => 'cust_name_',
        //'footer' => "<h5>".Yii::t('common','Total')." : </h5>"
    ],
    [
        'attribute'         => 'total',
        'format'            => 'raw',
        'headerOptions'     => ['class' => 'text-right'],
        'contentOptions'    => ['class' => 'text-right font-roboto'],
        'footerOptions'    => ['class' => 'text-right font-roboto'],
        'value'             => function($model){
            return '<span class="'.($model->sumtotals->total ===  $model->saleorder->total ? 'text-green' : 'text-red').'">'.number_format($model->sumtotals->total,2).'</span>';
        },
        //'footer' => "<h5>".number_format(ViewRcInvoice::getSumPage($dataProvider->models, 'total'),2)."</h5>",   
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

    
    <div class="row">
        <div class="col-sm-6"><h4><?=$this->title?></h4></div>
        <div class="col-sm-6 text-right">
 
            <?php /*ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $column,
                'columnSelectorOptions'=>[
                    'label' => Yii::t('common','Columns'),
                    'class' => 'btn btn-success-ew',
                    'title' => $this->title
                ],
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => Yii::t('common','Export All'),
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,                                
                ],
                'fontAwesome' => true,
                'selectedColumns'=> [2,3,4,5,6],
                // 'dropdownOptions' => [
                //     'label' => 'Export All',
                //     'class' => 'btn btn-primary'
                // ],
                'target' => ExportMenu::TARGET_BLANK,
                'filename' => Yii::t('common','Report Invoice'),
                
            ]); */
        ?>   
        </div>
    </div>
    <div class="table-responsive"  >
        <?= DataTables::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table  table-bordered', 'id' => 'export_table'],
            'clientOptions' => [
                "lengthMenu"=> [[20,-1], [20,Yii::t('common',"All")]],
                "info"=>true,
                "responsive"=>true, 
                //"dom"=> 'lfTrtip',
                "paging" => true,
                "tableTools"=>[
                    "aButtons"=> [  
                        // [
                        //     "sExtends"=> "copy",
                        //     "sButtonText"=> Yii::t('app',"Copy to clipboard")
                        // ],
                        // [
                        //     "sExtends"=> "csv",
                        //     "sButtonText"=> Yii::t('app',"Save to CSV")
                        // ],
                        // [
                        //     "sExtends"=> "xls",
                        //     "oSelectorOpts"=> ["page"=> 'current']
                        // ],
                        // [
                        //     "sExtends"=> "pdf",
                        //     "sButtonText"=> Yii::t('app',"Save to PDF")
                        // ],
                        // [
                        //     "sExtends"=> "print",
                        //     "sButtonText"=> Yii::t('app',"Print")
                        // ],                        
                    ]
                ]
            ],             
            //'showFooter' => true,
            'columns' => $column,
            // 'pager' => [
            //     'options'=>['class' => 'pagination'],// set clas name used in ui list of pagination
            //     'prevPageLabel'     => '«',         // Set the label for the "previous" page button
            //     'nextPageLabel'     => '»',         // Set the label for the "next" page button
            //     'firstPageLabel'    => Yii::t('common','page-first'),     // Set the label for the "first" page button
            //     'lastPageLabel'     => Yii::t('common','page-last'),      // Set the label for the "last" page button
            //     'nextPageCssClass'  => 'next',      // Set CSS class for the "next" page button
            //     'prevPageCssClass'  => 'prev',      // Set CSS class for the "previous" page button
            //     'firstPageCssClass' => 'first',     // Set CSS class for the "first" page button
            //     'lastPageCssClass'  => 'last',      // Set CSS class for the "last" page button
            //     'maxButtonCount'    => 5,           // Set maximum number of page buttons that can be displayed
            //     ],
        ]); ?>
    </div>
</div>



<?php
$Yii = 'Yii';
$js=<<<JS

// $('body').on('change','input.qty',function(){
//     let grandTotal = 0;
//     $.each( $('.react-bs-container-body').find('tr').find('div.sumLine'), function(indexInArray, e){ 
         
//        grandTotal += $(e).attr('data') * 1;					
        	 							
//         console.log($(e).attr('data'));
//     });
    
//     $('.total').text(grandTotal);
// });
$(document).ready(function(){
    setTimeout(() => {
        $('body')
            .find('input[type="search"]')
            .addClass('form-control')
            .closest('label')
            .prepend('<div>{$Yii::t("common","Search")}</div>');

    }, 1000);
    
    $("#export_table").tableExport({
        headings: true,                    // (Boolean), display table headings (th/td elements) in the <thead>
        footers: true,                     // (Boolean), display table footers (th/td elements) in the <tfoot>
        formats: ["xlsx"],    // (String[]), filetypes for the export ["xls", "csv", "txt"]
        fileName: "{$this->title}" ,         // (id, String), filename for the downloaded file
        bootstrap: true,                   // (Boolean), style buttons using bootstrap
        position: "top" ,                // (top, bottom), position of the caption element relative to table
        ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
        ignoreCols: null,                 // (Number, Number[]), column indices to exclude from the exported file
        ignoreCSS: ".tableexport-ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
    }); 
    
}) 

  
 
JS;

$this->registerJS($js,\yii\web\View::POS_END);
?>
 
<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 