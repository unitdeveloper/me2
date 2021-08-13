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
          <div class="  table-responsive no-padding" >
          <?php
               $gridColumns = [
                                  //'order_no',
                                  [
                                    'class' => 'yii\grid\SerialColumn',
                                    'headerOptions'   => ['class' => 'bg-info'],
                                    'contentOptions'  => ['class' => 'bg-info'],
                                    'footerOptions'   => ['class' => 'bg-info'],
                                    /*'footer' => '<div class="ew-fsize"><span><i class="fa fa-plus" aria-hidden="true"></i></span>
                                    </div>',*/
                                  ],
                                  //'type',
                                  //  'itemstb.master_code',

                                  // [
                                  //   //'attribute' => 'type',
                                  //   'label' => Yii::t('common','Type'),
                                  //   'format' => 'html',
                                  //   'contentOptions' => ['class' => 'hidden-xs'],
                                  //   'headerOptions' => ['class' => 'hidden-xs'],
                                  //   'footerOptions' => ['class' => 'hidden-xs'],
                                  //   'value' => function($model){
                                  //     return $model->type;
                                  //   },
                                  //   'footer' => '<div class="ew-type">
                                  //   <select class="form-control ew-type" name="InsertType" id="InsertType">
                                  //     <option value="Item">Item</option>
                                  //     <option value="G/L" =>G/L</option>
                                  //   </select></div>',
                                  // ],

                                  [
                                    //'attribute' => 'item_no',
                                    'label' => Yii::t('common','Items'),
                                    'format' => 'html',
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['style' => 'min-width:150px;'],
                                    //'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                      //return $model->itemstb['master_code'];
                                      return $model->crossreference->no;
                                    },
                                    'footer' => '<div class="form-group has-feedback ew-item-insert">
                                                  <div class="form-group has-success">
                                                    <input type="text" name="InsertItem"  class="form-control InsertItem" placeholder="'.Yii::t('common','Search product').'">
                                                    <span class="form-control-feedback " aria-hidden=""><i class="glyphicon glyphicon-search"></i></span>
                                                  </div>
                                                </div>',
                                  ],
                                  //'itemstb.Description',
                                  [
                                    //'attribute' => 'description',
                                    'label' => Yii::t('common','Description'),
                                    'format' => 'html',
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){

                                      if($model->description==''){
                                        return $model->itemstb['description_th'];
                                      }else {
                                        return $model->description;
                                      }
                                    },
                                    'footer' => '<div class="ew-desc"><input type="text" name="InsertDesc" ew-item-code="eWinl" id="InsertDesc" class="form-control"></div>',
                                  ],
                                  [
                                     //'attribute' => 'quantity',
                                    'label' => Yii::t('common','Quantity'),
                                     'format' => 'raw',
                                     'headerOptions' => ['class' => 'text-right'],
                                     'contentOptions' => ['class' => 'ew-sl-qty text-right'],
                                     'value' => function($model){
                                        return '<div id="ew-qty-edit" ew-line-no="'.$model->id.'">'.number_format($model->quantity).'</div>';
                                     },
                                     'footer' => '<div class="ew-qty"><input type="number" name="InsertQty" id="InsertQty" class="form-control"></div>',
                                  ],
                                    //'quantity',

                                    //'itemstb.UnitOfMeasure',
                                  [
                                    //'attribute' => 'unit_measure',
                                    'label' => Yii::t('common','Measure'),
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => 'itemstb.UnitOfMeasure',
                                  ],
                                    //'unit_measure',
                                    //'unit_price',
                                  [
                                     //'attribute' => 'unit_price',
                                    'label' => Yii::t('common','Unit Price'),
                                     'format' => 'raw',
                                     'headerOptions' => ['class' => 'text-right ','style' => 'min-width:100px;'],
                                     'contentOptions' => ['class' => 'ew-sl-price text-right '],
                                     //'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        return '<div id="ew-price-edit" ew-line-no="'.$model->id.'">'.number_format($model->unit_price,2).'</div>';
                                     },
                                     //'footer' => 'รวม',
                                     'footer' => '<div class="ew-price"><input type="number" name="InsertPrice" id="InsertPrice" class="form-control"></div>',
                                  ],
                                    //'line_amount',
                                  [
                                      //'attribute' => 'line_amount',
                                      'label' => Yii::t('common','Line Amount'),

                                      'format' => 'raw',
                                      //'label' => Yii::t('common', 'Delete'),
                                      'headerOptions' => ['class' => 'text-right hidden-xs'],
                                      'contentOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:100px;'],
                                      'footerOptions' => ['class' => 'hidden-xs'],
                                      'value' => function($model){
                                          return number_format($model->quantity * $model->unit_price,2);
                                      },
                                      'footer' => '<input type="hidden" name="item-id" id="item-id">',
                                      // 'footer' => 'รวม  : <div id="ew-line-total" data="'.$Fnc->getTotalSaleOrder($dataProvider->models).'">'. number_format($Fnc->getTotalSaleOrder($dataProvider->models),2).'</div>',
                                  ],

                                  [
                                      //'attribute' => 'id',

                                      'format' => 'raw',
                                      'label' => Yii::t('common', 'Delete'),
                                      //'contentOptions' => ['class' => ' '],
                                      'value' => function($model){
                                          return Html::a('<i class="glyphicon glyphicon-trash btn btn-default"></i>', '#'.$model->id,
                                            [
                                              'class'=>'RemoveSaleLine',
                                              'alt' => $model->itemstb['Description'],
                                              'qty' => $model->quantity,
                                              'price' => $model->unit_price,
                                            ]);
                                      },
                                      'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
                                  ],


                              ];
              ?>
              <?=  GridView::widget([
                  'dataProvider'=> $dataProvider,
                  //'filterModel' => $searchModel,
                  'rowOptions'=>function($model){
                          $query = \common\models\WarehouseMoving::find()
                          ->joinwith('header')
                          ->where(['warehouse_moving.SourceDoc' => $model->id])
                          ->andwhere(['<>','warehouse_header.status','Undo']);

                          if($query->exists()){

                              return ['class' => 'danger text-red'];

                          }


                          if(\common\models\WarehouseMoving::find()->where(['SourceDoc' => $model->id])->exists()){

                              return ['class' => 'success'];
                          }

                  },
                  'showFooter' => true,
                  'headerRowOptions'=>['class'=>'bg-gray'],
                  'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                  'columns' => $gridColumns,
                  //'responsive'=>true,
                  //'hover'=>true,
                  'summary' => false,
              ]);
              ?>



       
          
        </div>
        <!-- /.box -->
      </div>
  </div>
