<?php

use yii\helpers\Html;
use kartik\grid\GridView;
  
$this->title = Yii::t('app', 'Sale Order');
$this->params['breadcrumbs'][] = $this->title;
 

?>
 <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
<div class="row" ng-init="Title='<?= Html::encode($this->title) ?>'">
    <?=$this->render('_order_header',['model' => $searchModel])?>
     

    <div class="col-xs-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'order_date',
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        return date('d-m-Y',strtotime($model->order_date));
                    },
                ],
                [
                    'attribute' => 'no',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        return Html::a($model->no,['/SaleOrders/saleorder/view', 'id' => $model->id],['target' => '_blank']);
                    }
                ],
                [
                    'attribute' => 'Shipment',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        if($model->warehouseHeader!==null){
                            return Html::a($model->warehouseHeader->DocumentNo,['/warehousemoving/header/view', 'id' => $model->warehouseHeader->id],['target' => '_blank']);
                        }else{
                            return '';
                        }
                         
                    }
                ],
                [
                    'attribute' => 'Invoice',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        if($model->rcInvoiceHeader!==null){
                            return Html::a($model->rcInvoiceHeader->no_,['/accounting/posted/posted-invoice', 'id' => base64_encode($model->rcInvoiceHeader->id)],['target' => '_blank']);
                        }else{
                            return '';
                        }
                         
                    }
                ],
                
                [
                    'attribute' => 'customer_name',                     
                    'value' => 'customer.name',
                ],
                [
                    'attribute' => 'balance',    
                    'headerOptions' => ['class' => 'text-right'],  
                    'contentOptions' => ['class' => 'text-right'],               
                    'value' => function($model){
                        return number_format($model->balance,2);
                    },
                ],
                 
                [
                
                    'class' => 'yii\grid\ActionColumn',
                    'buttonOptions'=>['class'=>'btn btn-default'],
                    'contentOptions' => ['class' => 'text-right','style'=>'min-width:50px;'],
                    'template'=>'<div class="btn-group btn-group text-center" role="group">  {delete} </div>',
                    'options'=> ['style'=>'width:50px;'],
                    'buttons'=>[
                            
                        'delete' => function($url,$model,$key){
                            $inv = 0;
                            if($model->rcInvoiceHeader!==null){
                                $inv=  $model->rcInvoiceHeader->id;
                            }
                            return Html::a('<i class="far fa-trash-alt"></i> ',['delete','order' => $model->id, 'inv' => $inv],[
                                'class' => 'btn btn-danger-ew btn-xs',
                                'data' => [
                                    'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]);
                        },
                            
                        ]
                ],
                
            
            ],
            'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
            ],
            'options' => [
                'class' => 'table ',                
            ],
            
            //'pjax'=>true,   
            //'responsive' => false, 
            'responsiveWrap' => false, // Disable Mobile responsive    
                
        ]); ?>
    </div>
</div>