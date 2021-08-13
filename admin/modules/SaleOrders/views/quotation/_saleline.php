<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;


use yii\grid\GridView;
//use kartik\grid\GridView;

use admin\modules\SaleOrders\models\FunctionSaleOrder;

$Fnc = new FunctionSaleOrder();
?>

<style>
.item-detail{
    width:100%;
    height:100%;
    position:fixed;
    background:#fff;
    top:0px;
    right:0px;
    z-index:3010;
    overflow:hidden;
    display:none;
}
/* .go-detail{
    margin:15px 0 15px 0;
}
.go-detail .row{
    margin-top:10px;
}  */
.text-customer-name{
    margin:-20px 0 20px 0;
    z-index:1020;
}
@media screen and (max-width: 767px){
    .table-responsive {
        overflow-x: hidden;
        border: none !important;
    }
    .SaleLine{
        /* margin:0 -15px 0 -15px !important; */
    }
    .rule-xs-mac{
        max-width:340px; 
        overflow-x:auto;
    }
    .add-product-service{
        color:#888;
    }
    input.no-border{
        background:none;
    }
    a#complete-btn:active{
        color:green;
    }
    .submit-btn-zone{
        position:fixed;
        bottom:0px;
        background-color: rgb(253,253,253);
        border-top:1px solid #eaeaea;
        padding:10px 0 10px 0;
        width:100%;
        z-index:1000;
    }
    #menuFilter{
        padding-bottom:50px;
    }
    .FilterResource{
        padding-bottom:50px;
    }
}
</style>
<div class="row">
      <div class="col-xs-12">
          <div class="table-responsive no-padding" >
          <?php
               $gridColumns = [
                                  //'order_no',
                                  [
                                    'class' => 'yii\grid\SerialColumn',                                    
                                    'headerOptions' => ['class' => 'bg-info text-right hidden-xs','style' => 'width:30px;'],
                                    'contentOptions'  => ['class' => 'bg-info hidden-xs','style' => 'vertical-align: middle;'],
                                    'footerOptions'   => ['class' => 'bg-info hidden-xs'],
                                    /*'footer' => '<div class="ew-fsize"><span><i class="fa fa-plus" aria-hidden="true"></i></span>
                                    </div>',*/
                                  ],
    

                                  [
                                    //'attribute' => 'item_no',
                                    'label' => Yii::t('common','Items'),
                                    'format' => 'html',
                                    'contentOptions' => ['class' => 'hidden-xs','style' => 'vertical-align: middle;'],
                                    'headerOptions' => ['class' => 'hidden-xs','style' => 'width:150px;'],
                                    'footerOptions' => ['style' => 'min-width:150px;'],
                                    'footerOptions' => ['class' => 'hidden-xs-cancel'],
                                    'value' => function($model){
                                      //return $model->items['master_code'];
                                      return $model->crossreference->no;
                                    },
                                    'footer' => '<div class="form-group has-feedback ew-item-insert">
                                                  <div class="form-group has-success">
                                                    <input type="text" name="InsertItem"  class="form-control InsertItem" placeholder="'.Yii::t('common','Search product').'">
                                                    <span class="form-control-feedback " aria-hidden=""><i class="glyphicon glyphicon-search"></i></span>
                                                  </div>
                                                </div>',
                                  ],
                                  //'items.Description',
                                  [
                                    //'attribute' => 'description',
                                    'label' => Yii::t('common','Description'),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'hidden-xs','style' => 'min-width:200px;'],
                                    'contentOptions' => ['class' => ' ','style' => 'vertical-align: middle;'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){

                                      if($model->description==''){
                                        $desc = $model->items->description_th;
                                      }else {
                                        $desc = $model->description;
                                      }

                                      //$html = '<input type="text" value="'.$desc.'" class="form-control text-line update-desc hidden-xs" name="description">';
                                      $html = '<div class="hidden-xs"><input type="text" value="'.$desc.'" class="form-control text-line update-desc" name="description"></div>';
                                      $html.= '<div class="hidden-sm hidden-md hidden-lg my-10">
                                                <div class="row">
                                                    <div class="col-xs-3">'.Html::img($model->items->picture,['class' => 'img-thumbnail go-detail', 'style' => 'max-width:80px;']).'</div>
                                                    <div class="col-xs-9">
                                                        <div class="row">
                                                            <div class="col-xs-4 text-left text-show-calulate font-roboto">
                                                                '.number_format($model->quantity).'<span class="text-yellow"> x </span>'.number_format($model->unit_price,2).'
                                                            </div>
                                                            <div class="col-xs-8 text-right text-info text-show-total font-roboto">
                                                            <input type="number" value="'.number_format($model->quantity * $model->unit_price,2).'" class="form-control no-border text-right" name="price">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-aqua my-5"><input type="text" value="'.$desc.'" class="form-control text-line update-desc" name="description"></div>
                                                
                                            </div>';
                                            
                                    //   $html.= '<div class="hidden-sm hidden-md hidden-lg ">
                                    //             <div class="text-aqua">'.$desc.'</div>
                                    //             <div class="row">
                                    //                 <div class="col-xs-8 text-left text-show-calulate">'.number_format($model->quantity).' x '.number_format($model->unit_price,2).'</div>
                                    //                 <div class="col-xs-4 text-right text-aqua text-show-total">'.number_format($model->quantity*$model->unit_price,2).'</div>
                                    //             </div>
                                    //             </div>';
                                                
                                      return $html;
                                    },
                                    'footer' => '<div class="ew-desc"><input type="text" name="InsertDesc" ew-item-code="eWinl" id="InsertDesc" class="form-control"></div>',
                                  ],
                                  [
                                     //'attribute' => 'quantity',
                                    'label' => Yii::t('common','Quantity'),
                                     'format' => 'raw',
                                     'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:120px; width:120px;'],
                                     'contentOptions' => ['class' => 'text-right hidden-xs'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        //return '<div id="ew-qty-edit" ew-line-no="'.$model->id.'">'.number_format($model->quantity).'</div>';
                                        return '<input type="number"  step=any onclick="$(this).val(\'\')"  value="'.number_format($model->quantity, 0, '.', '').'" class="form-control money text-right text-line update-quantity" name="quantity">';

                                     },
                                     'footer' => '<div class="ew-qty"><input type="number"  step=any  name="InsertQty" id="InsertQty" class="form-control money"></div>',
                                  ],
                                    //'quantity',

                                    //'items.UnitOfMeasure',
                                  [
                                    //'attribute' => 'unit_measure',
                                    'label' => Yii::t('common','Measure'),
                                    'headerOptions' => ['class' => 'hidden-xs' ,'style' => 'width:80px;'],
                                    'contentOptions' => ['class' => 'hidden-xs','style' => 'vertical-align: middle;'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => 'items.UnitOfMeasure',
                                  ],
                                    //'unit_measure',
                                    //'unit_price',
                                  [
                                     //'attribute' => 'unit_price',
                                    'label' => Yii::t('common','Unit Price'),
                                     'format' => 'raw',
                                     'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:120px; width:120px;'],
                                     'contentOptions' => ['class' => 'text-right hidden-xs'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        //return '<div id="ew-price-edit" ew-line-no="'.$model->id.'">'.number_format($model->unit_price,2).'</div>';
                                        return '<input type="text" value="'.number_format($model->unit_price, 2, '.', '').'" class="form-control text-right  text-line update-unit_price" name="unit_price">';
                                     },
                                     //'footer' => 'รวม',
                                     'footer' => '<div class="ew-price"><input type="number"  step=any   name="InsertPrice" id="InsertPrice" class="form-control money"></div>',
                                  ],
                                    //'line_amount',
                                  [
                                      //'attribute' => 'line_amount',
                                      'label' => Yii::t('common','Line Amount'),

                                      'format' => 'html',
                                      //'label' => Yii::t('common', 'Delete'),
                                      'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'min-width:102px; width:120px;'],
                                      'contentOptions' => ['class' => 'text-right hidden-xs line-amount','style' => 'vertical-align: middle;'],
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
                                      'contentOptions' => ['class' => 'hidden-xs'],
                                      'headerOptions' => ['class' => 'text-center hidden-xs','style' => 'width:50px;'],
                                      'footerOptions' => ['class' => 'hidden-xs'],
                                      'value' => function($model){
                                          return Html::a('<i class="fas fa-times btn btn-danger-ew btn-flat"></i>', '#'.$model->id,
                                            [
                                              'class'=>'RemoveSaleLine',
                                              'alt' => $model->items->Description,
                                              'qty' => $model->quantity,
                                              'price' => $model->unit_price,
                                            ]);
                                      },
                                      //'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
                                      'footer' => '<button type="button" title="'.Yii::t('common','Clear').'" id="clear-line" class="btn btn-default-ew  btn-flat"><i class="far fa-trash-alt text-red"></i></button>',
                                  ],


                              ];
              ?>
              <?=  GridView::widget([
                  'dataProvider'=> $dataProvider,                  
                  'showFooter' => true,
                  'headerRowOptions'=>['class'=>'bg-gray'],
                  'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                  'columns' => $gridColumns,
                  //'responsive'=>true,
                  //'hover'=>true,
                  'summary' => false,
                  'tableOptions' => [
                      'class' => 'table table-hover',
                  ]
              ]);
              ?>



       
          
        </div>
        <!-- /.box -->
      </div>
  </div>




<div class="item-detail">
    <div class=" ">
        <div class="content">

            <div class="row">
                <div class="col-xs-6"><a href="javascript:void(0);" id="back-btn"><i class="far fa-arrow-alt-circle-left fa-2x"></i></a></div>
                <div class="col-xs-6 text-right"><a href="javascript:void(0);" id="complete-btn" ><i class="fas fa-check-circle fa-2x text-green"></i></a></div>
            </div>
            <div class="row margin-top">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="col-xs-12">
                            <label><?=Yii::t('common','Name')?></label> <span class="item-name text-primary">ชื่อสินค้า</span>
                    </div>
                    <div class="col-xs-12 item-desc text-primary">รายละเอียด</div>
                </div>
            </div>
            <div class="well  margin-top">
                
                <div class=" ">
                    <div class="row" style="margin-top:5px;">
                        <div class="col-xs-6"><?=Yii::t('common','Quantity')?> </div>
                        <div class="col-xs-6 text-right text-aqua">
                            <input type="number" value="" name="quantity"  step=any onclick="$(this).select()" style="font-size:16px;" pattern="^(?:\(\d{3}\)|\d{3})[- . ]?\d{3}[- . ]?\d{4}$"   class="form-control money item-qty text-right update-field "  autocomplete="off">
                        </div>
                    </div>
                    <div class="row" style="margin-top:5px;">
                        <div class="col-xs-6"><?=Yii::t('common','Price')?> </div>
                        <div class="col-xs-6 text-right text-aqua">
                            <input type="number" value="" name="unit_price" step=any onclick="$(this).select()" style="font-size:16px;" pattern="^(?:\(\d{3}\)|\d{3})[- . ]?\d{3}[- . ]?\d{4}$"   class="form-control money item-price text-right update-field "   autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="row" style="margin-top:5px;">
                        <div class="col-xs-6"><?=Yii::t('common','Line Amount')?> </div>
                        <div class="col-xs-6 text-right text-aqua">
                            <input type="text" value="" style="font-size:16px;"  class="form-control item-line-amount text-right" readonly="readonly">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" >
                <div class="col-xs-12">
                    <a href="javascript:void(0);" class="delete-btn" style="position:fixed;bottom:10px;"><i class="far fa-times-circle fa-2x text-danger" title="<?=Yii::t('common','Delete')?>"></i> <?=Yii::t('common','Delete')?></a>
                </div>
            </div>
            
        </div>
    </div>
</div>
