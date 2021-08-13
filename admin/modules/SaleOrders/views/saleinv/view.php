<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;


use yii\grid\GridView;
//use kartik\grid\GridView;

use admin\modules\SaleOrders\models\FunctionSaleOrder;

$Fnc = new FunctionSaleOrder();
?>
 
<div class="row">
      <div class="col-xs-12">
        <div class="box box-danger">
     
          <div class="box-body table-responsive no-padding">
          <?php 
               $gridColumns = [ 
                                  //'order_no',
                                  [
                                    'class' => 'yii\grid\SerialColumn',
                                    'footer' => '<div class="ew-fsize"><span><i class="fa fa-plus" aria-hidden="true"></i></span>
                                    </div>',
                                  ],
                                  //'type',                                     
                                  //  'itemstb.master_code',
                                  [
                                    'attribute' => 'type',
                                    'format' => 'html',
                                    'contentOptions' => ['class' => 'hidden-xs'],                
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                      return $model->type;
                                    },
                                    'footer' => '<div class="ew-type">
                                    <select class="form-control ew-type" name="InsertType" id="InsertType">
                                      <option value="Item">Item</option>
                                      <option value="G/L" =>G/L</option>
                                    </select></div>',
                                  ],
                                  [
                                    'attribute' => 'item_no',
                                    'format' => 'html',
                                    'contentOptions' => ['class' => 'hidden-xs'],                
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                      return $model->itemstb['master_code'];
                                    },
                                    'footer' => '<div class="ew-item-insert">
                                    <input type="text" name="InsertItem"  class="form-control InsertItem"></div>',
                                  ],
                                  //'itemstb.Description',
                                  [
                                    'attribute' => 'description',
                                    'format' => 'html',
                                    'value' => function($model){
                                      
                                      if($model->description==''){
                                        return $model->itemstb['Description'];
                                      }else {
                                        return $model->description;
                                      }
                                    },
                                    'footer' => '<div class="ew-desc"><input type="text" name="InsertDesc" ew-item-code="eWinl" id="InsertDesc" class="form-control"></div>',
                                  ],  
                                  [
                                     'attribute' => 'quantity',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'text-right'],
                                     'contentOptions' => ['class' => 'text-right'],
                                     'value' => function($model){
                                        return number_format($model->quantity);   
                                     },
                                     'footer' => '<div class="ew-qty"><input type="number" name="InsertQty" id="InsertQty" class="form-control"></div>',
                                  ],
                                    //'quantity',

                                    //'itemstb.UnitOfMeasure',
                                  [
                                    'attribute' => 'unit_measure',
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => 'itemstb.UnitOfMeasure',
                                  ],
                                    //'unit_measure',
                                    //'unit_price',
                                  [
                                     'attribute' => 'unit_price',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'text-right hidden-xs'],
                                     'contentOptions' => ['class' => 'text-right hidden-xs'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        return number_format($model->unit_price,2);   
                                     },
                                     //'footer' => 'รวม',
                                     'footer' => '<div class="ew-price"><input type="number" name="InsertPrice" id="InsertPrice" class="form-control"></div>',
                                  ],
                                    //'line_amount',
                                  [  
                                      'attribute' => 'line_amount',

                                      'format' => 'html',                                       
                                      //'label' => Yii::t('common', 'Delete'),
                                      'headerOptions' => ['class' => 'text-right'],
                                      'contentOptions' => ['class' => 'text-right'],
                                      'value' => function($model){                     
                                          return number_format($model->quantity * $model->unit_price,2);
                                      },
                                      'footer' => 'รวม  : '. number_format($Fnc->getTotalSaleOrder($dataProvider->models),2),
                                  ], 
                                   
                                  // [  
                                  //     //'attribute' => 'id',

                                  //     'format' => 'raw',                                       
                                  //     'label' => Yii::t('common', 'Delete'),
                                  //     //'contentOptions' => ['class' => ' '],
                                  //     'value' => function($model){                     
                                  //         return Html::a('<i class="glyphicon glyphicon-trash btn btn-default"></i>', '#'.$model->id,
                                  //           [
                                  //             'class'=>'RemoveSaleLine',
                                  //             'alt' => $model->itemstb['Description'],
                                  //           ]);
                                  //     },
                                  //     //'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
                                  // ],

                                   
                              ];
              ?>
              <?=  GridView::widget([
                  'dataProvider'=> $dataProvider,
                  //'filterModel' => $searchModel,
                  'showFooter' => true,
                  'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                  'columns' => $gridColumns,
                  //'responsive'=>true,
                  //'hover'=>true,
                  //'summary' => false,
              ]);
              ?>
          
          
          
        </div>  
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
  </div>
