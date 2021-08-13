<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;

use yii\db\Expression;
use kartik\daterange\DateRangePicker;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\apps_rules\models\SysRuleModels;

 
 
$this->title = Yii::t('common', 'Sale Order');
$this->params['breadcrumbs'][] = $this->title;

//นับจำนวนตัวอักษร ของข้อความ ภาษาไทย แบบ UTF-8
function utf8_strlen($string) {
    $c = strlen($string); $l = 0;
    for ($i = 0; $i < $c; ++$i)
    if ((ord($string[$i]) & 0xC0) != 0x80) ++$l;
    return $l;
}

$this->registerCssFile('css/sale-order.css?v=3.6.01',['rel' => 'stylesheet','type' => 'text/css']);
$this->registerCssFile('css/sales_order/index.css?v=4.10.23.1',['rel' => 'stylesheet','type' => 'text/css']);
?>
<div class="">
    <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
</div>

<div class="sale-header-index" ng-init="Title='<?=$this->title;?>'">
    

    <div class="table-responsive-">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-bordered table-hover'],
            'pager' => [
                'options'=>['class' => 'pagination'],   // set clas name used in ui list of pagination
                'prevPageLabel'     => '«',         // Set the label for the "previous" page button
                'nextPageLabel'     => '»',         // Set the label for the "next" page button
                'firstPageLabel'    => Yii::t('common','page-first'),     // Set the label for the "first" page button
                'lastPageLabel'     => Yii::t('common','page-last'),      // Set the label for the "last" page button
                'nextPageCssClass'  => 'next',      // Set CSS class for the "next" page button
                'prevPageCssClass'  => 'prev',      // Set CSS class for the "previous" page button
                'firstPageCssClass' => 'first',     // Set CSS class for the "first" page button
                'lastPageCssClass'  => 'last',      // Set CSS class for the "last" page button
                'maxButtonCount'    => 4,           // Set maximum number of page buttons that can be displayed
                ],

            'columns' => [
                // [
                //     'class'             => 'yii\grid\SerialColumn',
                //     'options'           => ['style' => 'width:50px;'],
                //     'headerOptions'     => ['class' => 'text-center'],
                //     //'filterOptions'     => ['class' => 'hidden-xs'],
                //     'contentOptions'    => ['class' => 'text-center show-doc']
                // ],            
                [
                    'attribute'     => 'order_date',
                    'label'         => Yii::t('common','Order Date'),
                    'format'        => 'raw',
                    'headerOptions' => ['style' => 'width:105px;'],
                    'contentOptions'=> ['class' => 'font-roboto'],
                    'value'         => function($model){
                        $html =  ($model->order_date)? $model->order_date : ' ';
                        //$html.=  '<span class="pull-right"><button class="btn '.($model->print_ship ? 'btn-default-ew minus-from-ship' : 'btn-default plus-to-ship').' btn-xs"><i class="'.($model->print_ship ? 'fa fa-minus' : 'fas fa-truck').'"></i></button></span>';
                        return $html;
                    },
                    'filter' => DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'order_date',  
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d'
                            ],                                
                        ],
                        
                    ]),
                ],
                 
                [
                    'attribute'     => 'no',
                    //'label'       => Yii::t('common','Customer'),
                    'contentOptions'=> ['class' => 'font-roboto'],
                    'filterOptions' => ['style' => 'width:120px;'],
                    'format'        => 'raw',
                    'value'         => function($model){
                        $color      = NULL;
                        $SumLine    = $model->balance_befor_vat;
                        if($model->balance != $SumLine){
                            $color = 'text-danger';
                        }
                            
                        $html = Html::a($model->no,['/SaleOrders/saleorder/view','id' => $model->id],['class' => ($model->vat_percent > 0 ? 'text-success order-no' : 'order-no'), 'target' => '_blank']);
                        $html.= '<div class="'.$color.'">
                                    <small class="label label-default">'.number_format($model->balance,2).'</small>                                    
                                 </div>';

                        return $html;
                    } 
                ], 
                [
                    'attribute'     => 'search',
                    'label'         => Yii::t('common','Customer'),
                    'format'        => 'raw',
                    //'headerOptions'=> ['style' => 'width:250px;'],
                    'contentOptions'    => ['style' => 'white-space: initial; font-family: saraban;'],
                    'value'         => function($model){   
                        $html = '<div style="">'.($model->customer ?  Html::a($model->customer->code,['/customers/customer/view', 'id' => $model->customer->id],['target' => '_blank'])  : ' ').'</div>';              
                        $html.= '<div>'.($model->customer
                                    ? $model->customer->nick_name
                                        ? $model->customer->nick_name
                                        : $model->customer->name 
                                    : ' ')
                                .'</div>';
                        
                        return $html;
                    }
                ],                 
                // [
                //     'attribute'         => 'sale_id',
                //     'label'             => Yii::t('common','Sale Person'),
                //     'format'            => 'raw',                
                //     'contentOptions'    => ['class' => 'show-doc'],
                //     'value'             => function($model){
                //         $html = '<div id="sale-name">'.($model->salespeople ? $model->salespeople->name : '').' '. ($model->salespeople ? $model->salespeople->surname : '').'</div>';
                //         $html.= '<small style="color:#ccc;">['.($model->salespeople ? $model->salespeople->code : '').']</small>';
                //         return $html;
                //     }
                // ],            
                [
                    //'attribute'         => 'balance',
                    'attribute'         => 'sale_id',
                    'label'             => Yii::t('common','Sale Person'),
                    'format'            => 'raw',
                    'headerOptions'     => ['style' => 'width:200px;'],
                    'contentOptions'    => ['style' => 'white-space: initial;'],
                    'value'             => function($model){
                        $html = '<div id="sale-name">'.($model->salespeople ? $model->salespeople->name : '').' '. ($model->salespeople ? $model->salespeople->surname : '').'</div>';  
                        $html.= '<div><small style="color:#ccc;">['.($model->salespeople ? $model->salespeople->code : '').']</small></div>';
                        return $html;
                    },
                ],

                [
                    'attribute'         => 'status',
                    'format'            => 'raw',
                    'headerOptions'     => ['style' => 'width:180px;'],   
                    'filterOptions'     => ['class' => ''],
                    'contentOptions'    => ['class' => 'status-content'],                                     
                    'value'             => function($model){    
                        $html = ' <div class="btn-group" role="group">                      
                                '.Html::a('<i class="fa fa-eye"></i>',['/SaleOrders/saleorder/view','id' => $model->id],['class' => "btn btn-default-ew btn-sm", 'target' => '_blank']).'
                                                
                                                
                                '.($model->customer
                                    ? Html::a('<i class="fa fa-print"></i>',['/SaleOrders/saleorder/print', 'id'=> $model->id, 'footer' => 1],['class' => "btn btn-info-ew btn-sm", 'target' => '_blank'])
                                    : ''
                                ).'

                               

                                '.($model->shipment
                                    ? Html::a('<i class="fas fa-file-alt"></i>',['/warehousemoving/shipment/print-ship', 'id'=> $model->shipment->id, 'footer' => 1],['class' => "btn btn-primary-ew btn-sm", 'target' => '_blank'])
                                    : '' 
                                ).'
                                
                                '.($model->rcInvoiceHeader 
                                    ? Html::a('<i class="fas fa-file-invoice"></i>',['/accounting/posted/posted-invoice', 'id'=> base64_encode($model->rcInvoiceHeader->id), 'footer' => 1],['class' => "btn btn-danger-ew btn-sm", 'target' => '_blank'])
                                    : ($model->saleInvoiceHeader != null
                                        ? Html::a('<i class="fas fa-file-invoice text-yellow"></i>',['/accounting/saleinvoice/update', 'id'=> $model->saleInvoiceHeader->id, 'footer' => 1],['class' => "btn btn-danger-ew btn-sm", 'target' => '_blank'])
                                        : '')
                                ).'
                                    </div>';
                                         
                        return $html;
                    },

                    'filter' => Html::activeDropDownList($searchModel,'status',
                        [
                            'Open'          => Yii::t('common','status-open'),
                            'Release'       => Yii::t('common','status-release'),
                            'Checking'      => Yii::t('common','status-checking'),
                            'Shiped'        => Yii::t('common','status-shipped'),
                            'Reject'        => Yii::t('common','status-reject'),
                            'Invoiced'      => Yii::t('common','status-invoiced'),
                            'Credit-Note'   => Yii::t('common','status-credit-note'),
                            'Pre-Cancel'    => Yii::t('common','status-cancel-req'),
                            'Cancel'        => Yii::t('common','Cancel'),
                        ],
                        [
                            'class'         => 'form-control',
                            'prompt'        => Yii::t('common','Show All'),
                        ]),
                ],


            ],
        ]); ?>
    </div>
        
</div>

<?= $this->render('../modal/_tracking'); ?>
<?= $this->render('_index-script-admin'); ?>
 