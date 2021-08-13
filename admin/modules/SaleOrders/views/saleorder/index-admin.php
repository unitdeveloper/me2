<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;
use yii\widgets\Pjax;
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
<style>
.content-wrapper{
    background-color: #ecf0f5 !important;
}
</style>

<div class="">
    <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
</div>

<div class="mb-5">
    <?=$this->render('../reserve/_script_inv_list')?>
     
</div>  
<div class="sale-header-index" ng-init="Title='<?=$this->title;?>'">
    <div style="position: absolute; right: 20px; top: 75px;">
         
        <div class="pull-right hidden-xs" >
        <span class="hidden"><?=Html::a('<i class="fas fa-truck"></i> '.Yii::t('common','Transport'),['/SaleOrders/report/sale-order'],['class' => 'btn btn-default-ew btn-truck', 'target' => '_blank', 'data-pjax' => "0"]);?></span>
        <?php 
            echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'showColumnSelector' => false,
                'target' => ExportMenu::TARGET_BLANK,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('common','Date'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            return date('d/m/Y',strtotime($model->order_date));
                        },
                    ],
                    'no',                     
                    'customer.name',                                      
                    [
                        'attribute' => 'salespeople.code',
                        'label'     => Yii::t('common','Sales'),
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->salespeople? (string)$model->salespeople->code : Yii::t('common','Not Set');
                        }
                    ],                   
                    [
                        'attribute' => 'sales_people',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->salespeople? $model->salespeople->name. ' '.$model->salespeople->surname : Yii::t('common','Not Set');
                        }
                    ],            
                    'balance'    
                ],
                'filename'        => Yii::t('app', 'SaleOrder'),
                'columnSelectorOptions'=>[
                    'label' => 'Columns',
                    'class' => 'btn btn-success-ew'
                ],

                'fontAwesome'       => true,
                'dropdownOptions'   => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary-ew'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_PDF => false,
                ]
            ]); 
            ?>
        </div>    
    </div>
    <?=$this->render('_shortcut');?>

    <div class="table-responsive" style="padding-bottom:220px;">
        <?php yii\widgets\Pjax::begin(['id' => 'grid-item-pjax','timeout'=>5000]) ?>
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
            //'options' => ['class' => 'bg-white'],
            //'rowOptions' => ['style' => 'height:58px;'],
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
                    'headerOptions' => ['class' => 'bg-info', 'style' => 'width:105px;'],
                    'filterOptions' => ['class' => 'bg-white'],
                    'contentOptions'=> ['class' => 'bg-white font-roboto'],
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
                    'headerOptions' => ['class' => 'bg-info'],
                    'contentOptions'=> ['class' => 'bg-white font-roboto'],
                    'filterOptions' => ['class' => 'bg-white', 'style' => 'width:120px;'],
                    'format'        => 'raw',
                    'value'         => function($model){
                        $color      = NULL;
                        $SumLine    = $model->balance_befor_vat;
                        if($model->balance != $SumLine){
                            $color = 'text-danger';
                        }
                            
                        $html = Html::a($model->no,['/SaleOrders/saleorder/view','id' => $model->id],['class' => ($model->vat_percent > 0 ? 'text-success order-no' : 'order-no'), 'target' => '_blank', 'data-pjax' => "0"]);
                        $html.= '<div class="'.$color.'">
                                    <small class="label label-default">'.number_format($model->balance,2).'</small>                                    
                                 </div>';

                        return $html;
                    } 
                ], 
                [
                    'attribute'         => 'search',
                    'label'             => Yii::t('common','Customer'),
                    'format'            => 'raw',
                    //'headerOptions'=> ['style' => 'width:250px;'],
                    'headerOptions'     => ['class' => 'bg-info'],
                    'filterOptions'     => ['class' => 'bg-white'],
                    'contentOptions'    => ['class' => 'bg-white', 'style' => 'white-space: initial; font-family: saraban;'],
                    'value'         => function($model){   
                        $html = '<div style="">'.($model->customer ?  Html::a($model->customer->code,['/customers/customer/view', 'id' => $model->customer->id],['target' => '_blank', 'data-pjax' => "0"])  : ' ').'</div>';              
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
                    'headerOptions'     => ['class' => 'bg-info', 'style' => 'width:135px;'],
                    'filterOptions'     => ['class' => 'bg-white'],
                    'contentOptions'    => ['class' => 'bg-white', 'style' => 'white-space: initial;'],
                    'value'             => function($model){
                        $html = '<div id="sale-name">'.($model->salespeople ? $model->salespeople->name : '').' '. ($model->salespeople ? $model->salespeople->surname : '').'</div>';  
                        $html.= '<div><small style="color:#ccc;">['.($model->salespeople ? $model->salespeople->code : '').']</small></div>';
                        return $html;
                    },
                ],

                [
                    'attribute'         => 'status',
                    'format'            => 'raw',
                    'headerOptions'     => ['class' => 'bg-info', 'style' => 'width:350px;'],   
                    'filterOptions'     => ['class' => 'bg-white'],
                    'contentOptions'    => ['class' => 'status-content bg-white'],                                     
                    'value'             => function($model){                       
                               
                         
                        $html_confirm   =  $model->status=='Checking'
                                                ? ($model->confirm * 1) > 0                                                      
                                                    ? '<button class="btn btn-success btn-sm" title="'.Yii::t('common','Confirmed').'" alt="'.Yii::t('common','Confirmed').'"><i class="fa fa-check"></i></button>' 
                                                    : ($model->live == 1 
                                                        ? '<button class="btn btn-info btn-sm" ><i class="fas fa-hourglass-half"></i></button>'
                                                        : '' )                                                    
                                                : '';   
                        $classModal     = $model->status=='Checking'
                                            ? ((int)$model->confirm * 1 > 0 
                                                ? 'click-modal-Confirmed' 
                                                : 'click-modal-Checking' 
                                                )
                                            : 'click-modal-Checking';                   

                        $html = '<div>                                     
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm hidden '.($model->print_ship ? 'btn-default-ew minus-from-ship' : 'btn-default-ew plus-to-ship').'"><i class="'.($model->print_ship ? 'fas fa-truck text-info' : 'fas fa-truck text-gray').'" style="min-width:15px;"></i></button>
                                        <button class="'.$classModal.' btn btn-sm btn-default-ew" title="'.Yii::t('common','Description').'"><i class="fas fa-columns"></i> </button>
                                        
                                        <div class="btn-group" role="group">
                                            '.Html::a('<i class="fa fa-pencil"></i>',['#'],[
                                                'class' => "btn btn-warning-ew btn-sm",
                                                'data-toggle' => 'dropdown',
                                                'aria-haspopup' => 'true',
                                                'aria-expanded' => 'false'
                                                ]).'
                                            <ul class="dropdown-menu">    
                                                <li class=" "><a href="#"   class="modify-sale-header"><i class="fas fa-clipboard-list mr-10 text-yellow"></i> '.Yii::t('common','Edit').' '.Yii::t('common','Heading').'</a></li>                                            
                                                <li class=" ">'.Html::a('<i class="fa fa-eye text-info mr-10"></i> '.Yii::t('common', 'View'), ['/SaleOrders/saleorder/view','id' => $model->id], ['class' => ' ', 'target' => '_blank', 'data-pjax' => "0"]).'</li>
                                                <li class=" ">'.Html::a('<i class="fa fa-pencil text-warning"></i> '.Yii::t('common','Edit'),['/SaleOrders/saleorder/update','id' => $model->id], ['class' => " ", 'target' => '_blank', 'data-pjax' => "0"]).'</li>
                                                <li class=" ">'.Html::a('<i class="far fa-trash-alt text-danger mr-10"></i> '.Yii::t('common', 'Delete'), 'javascript:void(0);', ['class' => 'delete-order']).'</li>
                                            </ul>
                                        </div>
                                        <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-default-ew dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:105px">
                                                '.$model->ordersStatus.'
                                                <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu option-list-menu">
                                                    <li class="change-order-status line"><a href="#" class="status-open" data-key="Open"><i class="fas fa-envelope-open text-aqua"></i> '.Yii::t('common','status-open').'</a></li>
                                                    <li class="change-order-status line"><a href="#" class="status-release" data-key="Release"><i class="fas fa-envelope text-red"></i> '.Yii::t('common','status-release').'</a></li>
                                                    <li class="change-order-status line"><a href="#" class="status-checking" data-key="Checking"><i class="fa fa-hourglass-half text-orange"></i> '.Yii::t('common','status-checking').'</a></li>
                                                    <li class="change-order-status line"><a href="#" class="status-invoiced" data-key="Invoiced"><i class="fa fa-file-text-o text-green"></i> '.Yii::t('common','status-invoiced').'</a></li>
                                                    <li class="change-order-status "><a href="#" class="status-shipped" data-key="Shiped"><i class="fa fa-truck text-info"></i> '.Yii::t('common','status-shipped').'</a></li>
                                                </ul>
                                        </div>
                                        
                                        
                                        '.($model->customer
                                            ? Html::a('<i class="fa fa-print"></i>',['/SaleOrders/saleorder/print', 'id'=> $model->id, 'footer' => 1],['class' => "btn btn-info-ew btn-sm", 'target' => '_blank', 'data-pjax' => "0"])
                                            : ''
                                        ).'

                                        

                                        '.($model->shipment
                                            ? Html::a('<i class="fas fa-cube"></i>',['/warehousemoving/shipment/print-ship', 'id'=> $model->shipment->id, 'footer' => 1],['class' => "btn btn-primary-ew btn-sm", 'target' => '_blank', 'data-pjax' => "0"])
                                            : '' 
                                        ).'
                                        
                                        '.($model->rcInvoiceHeader 
                                            ? Html::a('<i class="fas fa-file-invoice"></i>',['/accounting/posted/posted-invoice', 'id'=> base64_encode($model->rcInvoiceHeader->id), 'footer' => 1],['class' => "btn btn-danger-ew btn-sm", 'target' => '_blank', 'data-pjax' => "0"])
                                            : ($model->saleInvoiceHeader != null
                                                ? Html::a('<i class="fas fa-file-invoice text-yellow"></i>',['/accounting/saleinvoice/update', 'id'=> $model->saleInvoiceHeader->id, 'footer' => 1],['class' => "btn btn-danger-ew btn-sm", 'target' => '_blank', 'data-pjax' => "0"])
                                                : '')
                                        ).'
                                        
                                        '.$html_confirm.'
                                    </div>                                                                                                      
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
        <?php  yii\widgets\Pjax::end() ?>
    </div>
        
</div>

<?= $this->render('../modal/_tracking'); ?>
<?= $this->render('_index-script-admin'); ?>
 