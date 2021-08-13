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
                'format' => 'dd/mm/yyyy',
                'autoclose' => true,
                //'minViewMode' => 1,
            ]
            
        ]),
    ],
    [
        'label' => Yii::t('common','Sale Order'),
        'contentOptions'    => ['class' => 'font-roboto'],
        'format' => 'raw',
        'value' => function($model){
            $html = $model->saleorder->no;
            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/SaleOrders/saleorder/view','id' => $model->saleorder->id],['target' => '_blank']);
            //$html.= ' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>';
            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/SaleOrders/saleorder/print-page','id' => $model->saleorder->id,'footer' => 1],['target' => '_blank']);
 
            if ($model->saleorder->no=='0'){
                $html = '<i class="fas fa-exclamation-triangle text-orange"></i>';
            }
            return $html;
        }
    ],
    // [
    //     'attribute'         => 'vat_percent',
    //     'label'             => Yii::t('common','Tax Filter'),
    //     'format'            => 'raw',
    //     'contentOptions'    => ['class' => ''],
    //     'headerOptions'     => ['class' => '','style' => 'min-width:100px;'],
    //     'filterOptions'     => ['class' => ''],
    //     'value'             => function($model){ return ($model->vat_percent > 0)? 'Vat': 'No Vat'; },
    //     'filter' => Html::activeDropDownList($searchModel,'vat_percent',
    //         [
    //             '7'         => 'Vat',
    //             '0'         => 'No Vat',
    //         ],
    //         [
    //             'class'     => 'form-control',
    //             'prompt'    => Yii::t('common','Show All'),
    //         ]),
    // ],
    // [
    //     'attribute'         => 'postinggroup',
    //     'label'             => Yii::t('common','Customer Group'),
    //     'format'            => 'raw',
    //     'contentOptions'    => ['class' => ''],
    //     'headerOptions'     => ['class' => '','style' => 'min-width:150px;'],
    //     'filterOptions'     => ['class' => ''],
    //     'value'             => function($model){ return ($model->customer->genbus_postinggroup == '01')? Yii::t('common','General'): Yii::t('common','Modern Trade'); },
    //     'filter' => Html::activeDropDownList($searchModel,'postinggroup',
    //         [
    //             '01' => Yii::t('common','General'),
    //             '02' => Yii::t('common','Modern Trade')
    //         ],
    //         [
    //             'class'     => 'form-control',
    //             'prompt'    => Yii::t('common','Show All'),
    //         ]),
    // ],  

    [
        'attribute'         => 'no_',
        'label'             => Yii::t('common','Document No'),
        'contentOptions'    => ['class' => 'font-roboto'],
        
        'format'            => 'raw',
        'value'             => function($model){ 
            
            $html = '<span class="text-info">'.$model->no_.'</span>';
            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/saleinvoice/update','id' => $model->id],['target' => '_blank','class' => 'text-info']);
            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/saleinvoice/print-inv-page','id' => $model->id,'footer' => 1],['target' => '_blank','class' => 'text-info']);
 
            if($model->status=='Posted'){
                $html = '<span class="text-warning">'.$model->no_.'</span>';
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank','class' => 'text-info']);
                $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/posted/print-inv','id' => base64_encode($model->id),'footer' => 1],['target' => '_blank','class' => 'text-info']);
 
            }

            return $html;
            
        },
        
         
    ],
    
    [
        'footerOptions'    => ['class' => 'text-right'],
        'value' => 'cust_name_',
        'footer' => "<h5>".Yii::t('common','Total')." : </h5>"
    ],
    //'cust_address',
    //'customer.vat_regis',
    [
        'attribute'         => 'total',
        'format'            => 'raw',
        'headerOptions'     => ['class' => 'text-right'],
        'contentOptions'    => ['class' => 'text-right font-roboto'],
        'footerOptions'    => ['class' => 'text-right font-roboto'],
        'value'             => function($model){

            if ($model->total === $model->saleorder->total){            
                $html =  '<span class="text-green">'.number_format($model->total,2).'</span>';
            }else{
                $html =  '<span class="text-red">'.number_format($model->total,2).'</span>';
            }
            return $html;
        },
        'footer' => "<h5>".number_format(ViewRcInvoice::getSumPage($dataProvider->models, 'total'),2)."</h5>",   
        
    ],
    // [
    //     'label'             => Yii::t('common','Before Vat'),
    //     'headerOptions'     => ['class' => 'text-right'],
    //     'contentOptions'    => ['class' => 'text-right'],
    //     'value'             => function($model){
    //         return $model->beforeVat;
    //     }  
    // ],

    // [
    //     'label'             => Yii::t('common','Vat'),
    //     'headerOptions'     => ['class' => 'text-right'],
    //     'contentOptions'    => ['class' => 'text-right'],
    //     'value'             => function($model){
    //         return (($model->beforeVat) * $model->vat_percent)/ 100;
    //     }  
    // ],
     
   
     
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
            <?=ExportMenu::widget([
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
                'filename' => Yii::t('common','Invoice'),
                
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
            'showFooter' => true,
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