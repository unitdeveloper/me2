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

$this->title = Yii::t('common', 'Sale Order').' '.Yii::t('common','For Years').' '.Yii::$app->session->get('workyears');
$this->params['breadcrumbs'][] = $this->title;

$column = [
    [
        'contentOptions'    => ['class' => 'font-roboto'],
        'class' => 'yii\grid\SerialColumn'
    ],
    [
        'attribute' => 'order_date',
        'label' => Yii::t('common','Order Date'),
        'format' => 'html',
        'headerOptions' => ['class' => 'hidden-xs '],
        'contentOptions' => ['class' => 'hidden-xs font-roboto'],
        'filterOptions'     => ['class' => 'hidden-xs','style' => 'width:150px;'],
        'value' => function($model){
            return ($model->order_date)? $model->order_date : ' ';
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
        'attribute'         => 'no',
        'label' => Yii::t('common','Sale Order'),
        'contentOptions'    => ['class' => 'font-roboto'],
        'format' => 'raw',
        'value' => function($model){
            $html = $model->no;
            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/SaleOrders/saleorder/view','id' => $model->id],['target' => '_blank']);
            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/SaleOrders/saleorder/print-page','id' => $model->id,'footer' => 1],['target' => '_blank']);

            return $html;
        }
    ],
 

    // [
    //     //'attribute'         => 'no',
    //     'label'             => Yii::t('common','Invoice No'),
    //     'contentOptions'    => ['class' => 'font-roboto'],
        
    //     'format'            => 'raw',
    //     'value'             => function($model){ 
            
    //         $invoice = $model->invoiced;

    //         if ($invoice->id!=null){
            
    //             $html = '<span class="text-info">'.$invoice->no_.'</span>';
    //             //$html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/saleinvoice/update','id' => $model->invoiced->id],['target' => '_blank','class' => 'text-info']);
    //             $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/saleinvoice/print-inv-page','id' => $invoice->id,'footer' => 1],['target' => '_blank','class' => 'text-info']);
    
    //             if($invoice->status=='Posted'){
    //                 $html = '<span class="text-warning">'.$invoice->no_.'</span>';
    //                 //$html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/posted/posted-invoice','id' => base64_encode($model->invoiced->id)],['target' => '_blank','class' => 'text-info']);
    //                 $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/posted/print-inv','id' => base64_encode($invoice->id),'footer' => 1],['target' => '_blank','class' => 'text-info']);
    
    //             }
    //         }else{
    //             $html = ' ';
    //         }

    //         return $html;
            
    //     },
        
         
    // ],
    
    [
        'footerOptions'    => ['class' => 'text-right'],
        'value' => 'customer.name',
        'footer' => "<h5>".Yii::t('common','Total')." : </h5>"
    ],
    //'cust_address',
    //'customer.vat_regis',
    [
        'attribute'         => 'balance',
        'headerOptions'     => ['class' => 'text-right'],
        'contentOptions'    => ['class' => 'text-right font-roboto'],
        'footerOptions'    => ['class' => 'text-right font-roboto'],
        'value'             => function($model){
            return number_format($model->total,2);
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
                'columns' => [
                    [
                        'contentOptions'    => ['class' => 'font-roboto'],
                        'class' => 'yii\grid\SerialColumn'
                    ],
                    [
                        'attribute' => 'order_date',
                        'label' => Yii::t('common','Order Date'),
                        'format' => 'html',
                        'headerOptions' => ['class' => ' ','style' => 'min-width:50px; max-width:175;'],
                        'contentOptions' => ['class' => 'font-roboto','style' => 'min-width:50px; max-width:175;'],
                        'filterOptions' => ['class' => ' ','style' => 'min-width:50px; max-width:175;'],
                        'value' => function($model){
                            return date('d/m/Y',strtotime(($model->order_date)? $model->order_date : ' '));
                        }                        
                    ],
                    [
                        'attribute'         => 'no',
                        'label' => Yii::t('common','Sale Order'),
                        'contentOptions'    => ['class' => 'font-roboto'],
                        'format' => 'raw',
                        'value' => function($model){
                            $html = $model->no;
                            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/SaleOrders/saleorder/view','id' => $model->id],['target' => '_blank']);
                            $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/SaleOrders/saleorder/print-page','id' => $model->id,'footer' => 1],['target' => '_blank']);
                
                            return $html;
                        }
                    ],
                 
                
                    // [
                    //     //'attribute'         => 'no',
                    //     'label'             => Yii::t('common','Invoice No'),
                    //     'contentOptions'    => ['class' => 'font-roboto'],
                        
                    //     'format'            => 'raw',
                    //     'value'             => function($model){ 
                            
                    //         $invoice = $model->invoiced;
                
                    //         if ($invoice->id!=null){
                            
                    //             $html = '<span class="text-info">'.$invoice->no_.'</span>';
                    //             //$html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/saleinvoice/update','id' => $model->invoiced->id],['target' => '_blank','class' => 'text-info']);
                    //             $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/saleinvoice/print-inv-page','id' => $invoice->id,'footer' => 1],['target' => '_blank','class' => 'text-info']);
                    
                    //             if($invoice->status=='Posted'){
                    //                 $html = '<span class="text-warning">'.$invoice->no_.'</span>';
                    //                 //$html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i></button>',['/accounting/posted/posted-invoice','id' => base64_encode($model->invoiced->id)],['target' => '_blank','class' => 'text-info']);
                    //                 $html.= Html::a(' <button type="button" class="btn btn-default btn-xs btn-flat"><i class="fas fa-print"></i></button>',['/accounting/posted/print-inv','id' => base64_encode($invoice->id),'footer' => 1],['target' => '_blank','class' => 'text-info']);
                    
                    //             }
                    //         }else{
                    //             $html = ' ';
                    //         }
                
                    //         return $html;
                            
                    //     },
                        
                         
                    // ],
                    
                    [
                        'footerOptions'    => ['class' => 'text-right'],
                        'value' => 'customer.name',
                        'footer' => "<h5>".Yii::t('common','Total')." : </h5>"
                    ],
 
                    [
                        'attribute'         => 'balance',
                        'headerOptions'     => ['class' => 'text-right'],
                        'contentOptions'    => ['class' => 'text-right font-roboto'],
                        'footerOptions'    => ['class' => 'text-right font-roboto'],
                        'value'             => function($model){
                            return $model->total;
                        },
                        'footer' => "<h5>".ViewRcInvoice::getSumPage($dataProvider->models, 'total')."</h5>",   
                        
                    ],                   
                     
                ],
                'columnSelectorOptions'=>[
                    'label' => Yii::t('common','Columns'),
                    'class' => 'btn btn-success-ew',
                    'title' => $this->title
                ],
                'dropdownOptions' => [
                    'label' => Yii::t('common','Export All'),
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,                                
                ],
                'fontAwesome' => true,
                //'selectedColumns'=> [2,3,4,5,6],
                'target' => ExportMenu::TARGET_BLANK,
                'filename' => (isset($_GET['page']))? Yii::t('common','Sale Order').'('.Yii::t('common','Page').' '.$_GET['page'].')' : Yii::t('common','Sale Order'),
                
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