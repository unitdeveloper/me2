<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = $model->status;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    /* .content-wrapper{
        background: rgb(34, 45, 49) !important;
    } */
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="warehouse-header-view" ng-init="Title='<?=$this->title?>'">
    <div class="row">

        <div class="col-sm-12 my-5 text-right">
            <h1 style="margin-top:-5px;"><?= Html::encode($model->DocumentNo) ?></h1>
        </div>
        <div class="col-sm-12 mb-5 text-right">
            <?php 
                if(isset($_GET['po'])){
                    echo Html::a('<i class="fas fa-fast-backward"></i> '.Yii::t('common','Back'),
                            ['/Purchase/order'],['class' => 'btn bg-black mx-2']);  

                    // echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('common', 'Delete'), ['delete-rc', 'id' => $model->id], [
                    //     'class' => 'btn btn-danger mx-2',
                    //     'data' => [
                    //         'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                    //         'method' => 'post',
                    //     ],
                    // ]);
                }else{

                    // echo Yii::$app->user->identity->id==1 ? Html::a('<i class="fa fa-trash"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    //     'class' => 'btn btn-danger mx-2',
                    //     'data' => [
                    //         'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                    //         'method' => 'post',
                    //     ],
                    // ]) : '';


                    echo Html::a('<i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Delivery ticket'),
                    ['/warehousemoving/shipment/print-ship','id' => $model->id],['class' => 'btn btn-info mx-2','target'=>'_blank']);

                    echo Html::a('<i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Print'),
                    ['print','id' => $model->id],['class' => 'btn btn-success mx-2','target'=>'_blank']);
                }
            ?>
   
            
          

           
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 pull-right">
            <div class="box box-info">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'id',
                    //'line_no',
                    
                    //'DocumentDate',
                    //'TypeOfDocument',
                    //'SourceDocNo',
                    'DocumentNo',
                    [
                        'attribute' => 'SourceDoc',
                        'format' => 'raw',
                        'label' => Yii::t('common','Reference No.'),
                        'value' => function($model){                            
                            $doc =  $model->TypeOfDocument != 'Purchase' 
                                        ? $model->order != null 
                                            ? Html::a($model->saleOrder->no, ['/SaleOrders/saleorder/view','id' => $model->saleOrder->id],['target' => '_blank', 'data-pjax'=>"0"]) 
                                            : $model->SourceDoc
                                        : Html::a($model->SourceDoc, ['/Purchase/order/view','id' => $model->SourceDocNo],['target' => '_blank', 'data-pjax'=>"0"]) ;

                            $html = '<div><i class="fa fa-envelope-o"></i> '.$doc.'</div>';
                            return $html;
                        }
                    ],
                    'PostingDate',
                    [
                        'attribute' => 'customer_id',
                        'format' => 'html',
                        'label' => $model->TypeOfDocument == 'Purchase' ? Yii::t('common','Vendor') : Yii::t('common','Customer'),
                        'value' => function($model){                        
                            $html = '<i class="fa fa-address-card" aria-hidden="true"></i> '.$model->customer->name;
                            return $html;
                        }
                    ],
                    
                    
                    //'SourceDoc',
                    [
                        'attribute' => 'Description',
                        'label' => Yii::t('common','Transport'),
                        'value' => function($model){
                            return $model->Description;
                        }
                    ],
                    'ext_document'
                    //'Quantity',
                    //'address',
                    //'address2',
                    //'districttb.DISTRICT_NAME',
                    //'citytb.AMPHUR_NAME',
                    //'provincetb.PROVINCE_NAME',
                    //'postcode',
                    //'contact',
                    //'phone',
                    //'gps:ntext',
                    //'update_date',
                    //'status',
                    //'user_id',
                    //'comp_id',
                    //'ship_to',
                    //'ship_date',
                    //'AdjustType',
                ],
            ]) ?>
            </div>            
            
            
        </div>
        <div class="col-md-12">
            <div class="box box-success table-responsive">
            
            <?php $gridColumns = [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center']
                    ],

                    // 'id',
                    //'SourceDocNo',
         
                    //'DocumentNo',
                    
                    //'header.customer.name', 
                    
                    // 'PostingDate:date',
                    //'itemstb.master_code',
                    [
                        'format' => 'raw',
                        'label' => Yii::t('common','Images'),
                        //'contentOptions' => ['class' => 'text-right'],
                        'value' => function($model) {
                            $html =  Html::a(Html::img($model->items->picture,['class' => 'img-responsive','style' => 'max-width:50px;']), ['/items/items/view-only','id' => $model->items->id],['target' => '_blank', 'data-pjax'=>"0"]);
                            return $html;
                        }
                    ],
                     
                    [
                        'attribute' => 'ItemNo',
                        'format' => 'raw',
                        'label' => Yii::t('common','Items'),
                        //'contentOptions' => ['class' => 'text-right'],
                        'value' => function($model)
                        {
                            $html =  Html::a((($model->item==1414)
                                                ? $model->ItemNo 
                                                : $model->items->master_code
                                            ), ['/items/items/view-only','id' => $model->items->id],['target' => '_blank', 'data-pjax'=>"0"]);


                            return $html;
                        }
                    ],
                    [
                        'attribute' => 'Description',
                        'format' => 'raw',
                        'label' => Yii::t('common','Product Name'),
                        //'contentOptions' => ['class' => 'text-right'],
                        'value' => function($model)
                        {
                            // $html = '<div>'.$model->Description.' <span class="text-sm pull-right">(th)</span> </div>';
                            // $html.= '<div>'.$model->itemstb->Description.' <span class="text-sm pull-right">(en)</span> </div>';

                            return $model->Description;
                        }
                    ],
                     
                    // [
                    //     'attribute' => 'TypeOfDocument',
                    //     'label' => Yii::t('common','Type'),
                    //     'value' => 'TypeOfDocument'
                    // ],
                    //'Description',
                    // 'Quantity',
                    [
                        'attribute' => 'Quantity',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right font-roboto', 'style' => 'padding-right:15px;'],
                        'value' => function($model)
                        {
                            $html =  Html::a(($model->Quantity * 1), ['/warehousemoving/warehouse','WarehouseSearch[ItemId]' => base64_encode($model->items->id)],['target' => '_blank', 'data-pjax'=>"0"]);
                            return $html;
                        }
                    ],
                    // [
                    //     'attribute' => 'unit_price',
                    //     'format' => 'raw',
                    //     'headerOptions' => ['class' => 'text-right'],
                    //     'contentOptions' => ['class' => 'text-right'],
                    //     'value' => function($model)
                    //     {
                    //         return number_format($model->unit_price,2);
                    //     }
                    // ]
                ];
            ?> 

            <?= GridView::widget([
                'dataProvider' => $dataProvider,  
                'rowOptions' => function($model){
                    return ['class' => $model->Quantity < 0 ? 'text-warning' : 'text-success'];
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'summary' => false,       
                'columns' =>  $gridColumns,
            ]); ?>
             
            </div>
             
            
            
        </div>
    </div>
    

</div>
