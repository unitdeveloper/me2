<?php

use yii\helpers\Html;
//use kartik\grid\GridView;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\models\Items;

?>
<style type="text/css">
    .item-code{
        width: 100px;
    }
</style>
<div class="table">




    <?php
    $gridColumns = [
            ['class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width:80px;'],
                'footer' => '<div class="ew-type">
                                    <select class="form-control ew-type" name="InsertType" id="InsertType">
                                      <option value="Item">'.Yii::t('common','Items').'</option>
                                      <option value="G/L">'.Yii::t('common','G/L').'</option>
                                    </select>
                            </div>'
            ],

            [
                'label' => Yii::t('common','Items'),
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:190px;'],
                'contentOptions' => ['class' => 'item-code'],              
                'value' => function($model){

                    if($model->type=='Item'){
                        $code =  $model->crossreference->no;                       
                    }else {
                        // ค้นจาก GL Account
                        //
                        //$code = 'G/L Number';                         
                        $code = '';
                    }
                    $color  = $model->status=='delete' ? 'red' : '';
                    $html   = '<div style="color:'.$color.'">'.Html::a($code,['/items/items/view', 'id' => $model->item],['target' => '_blank']).'</div>';

                    if($model->item=='1414'){
                        if($model->code_no_=='1^x'){
                            $html = '<div style="color:'.$color.'"> </div>';
                        }else{
                            $html = '<div style="color:'.$color.'">'.Html::a($model->code_no_,['/items/items/view', 'id' => $model->item],['target' => '_blank']).'</div>';
                        }                        
                    }
                    return $html;
                },
                'footer' => '<div class="ew-type input-group">
                                <input type="text" class="form-control ew-InsertItems next">
                                <span class="btn-info input-group-addon ew-pick-item-modal" style="cursor:pointer;"><i class="fa fa-caret-square-o-up" aria-hidden="true"></i></span>
                             </div>',
            ],

            [
                //'attribute'=>'code_desc_',
                'label' => Yii::t('common','Product Name'),
                'format' => 'raw',
                'value' => function($model){

                    //$html = '<div class="ew-description input-group">';
                    $html = '   <input type="text" value="'.$model->crossreference->desc.'" class="form-control text-line next" name="desc">';
                    //$html.= '   <span class="btn-info input-group-addon ew-add-comment" style="cursor:pointer;"><i class="fa fa-plus" ></i></span>';
                    //$html.= '</div>';

                    return $html;
                },

                'footer' => '<div class="ew-type"><input type="text" class="form-control ew-InsertDesc"></div>',
            ],

            
            [
                //'attribute'=>'quantity',
                'format'=>'raw',
                'label' => Yii::t('common','Quantity'),
                'headerOptions' => ['class' => 'text-right','style' => 'width:80px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                    return '<input type="text" value="'.(abs($model->quantity) * 1).'" class="form-control text-right text-line next" name="qty" autocomplete="off">';

                },
                'footer' => '<div class="ew-type">
                                    <input type="text" class="form-control text-right ew-direct-qty"></div>',
            ],

            [
                'format'=>'raw',
                'label' => Yii::t('common','Unit Price'),
                'headerOptions' => ['class' => 'text-right','style' => 'width:120px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                    return '<input type="text" value="'.(abs($model->unit_price) * 1).'" class="form-control text-right text-line next" name="price" autocomplete="off">';

                },
                'footer' => '<div class="ew-type"><input type="text" class="form-control text-right ew-direct-price"></div>',

            ],

            [
                'format'=>'raw',
                'headerOptions' => ['class' => 'text-center','style' => 'width:80px;'],
                'contentOptions' => ['class' => 'text-center'],
                'footerOptions' => ['class' => 'text-center'],
                'value' => function($model){                    
                    return '<input type="checkbox" '.($model->return_receive > 0 ? "checked" : "").' data-key="'.$model->id.'" name="receive" value="'.$model->return_receive.'" '.($model->item == 1414 ? 'disabled' : '').' />';
                },
                'header' => '<input type="checkbox" id="receive-all"  /> <label class="pointer" for="receive-all">'.Yii::t('common','All').' </label>',
                'footer' => Yii::t('common','Return')
            ],

            [
                'label' => Yii::t('common','Line Amount'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right','style' => 'width:102px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    $LineTotal = ($model->quantity * $model->unit_price)- $model->line_discount;
                    //return number_format(($model->quantity * $model->unit_price)- $model->line_discount);
                    return '<div class="ew-line-total" data="'.abs($LineTotal).'">'.number_format(abs($LineTotal),2).'</div>';
                },
                //'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
                'footer' => ' ',
            ],

            [
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right', 'style' => 'width:70px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                   return '<div class="btn btn-danger-ew ew-delete-inv-line" data="'.$model->id.'"><i class="fa fa-trash-o" aria-hidden="true"></i></div>';
                },
                'footer' => '<div class="ew-add-to-inv-line"><input type="button" name="ew-InsertAdd" class="btn btn-default" value="ADD"></div>',
            ],
        ];

    ?>

    <?php Pjax::begin(['id' => 'pjax-inv-line',
                                    'timeout'=>false,
                                    'enablePushState' => false,
                                    'enableReplaceState' => true,
                                    'clientOptions' => ['method' => 'POST']
                                    ]); ?>
    <?=  GridView::widget([
          'id' => 'ew-grid-saleInv',
          'dataProvider'=> $dataProvider,
          //'summary' => true,
          'showFooter' => true,
          'tableOptions' => ['id'=>'Sale_Invoice_Line','class' => 'table bg-warning table-striped table-bordered' ],
          'headerRowOptions'=>['class'=>'bg-gray'],
          'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
          'columns' => $gridColumns,          
          'rowOptions' => function($model){
            return [
                'data-source'       => $model->source_line,
                'data-source-no'    => $model->source_doc,                
                'date-order-id'     => $model->order_id,
            ];
        },
          //'pjax'=>true,
          //'responsive'=>true,
          //'hover'=>true,
          'summary' => false,
      ]);
      ?>
    <?php Pjax::end(); ?>
    <div class="row">

        <div class="col-sm-12">
            <!-- <a href="#" id="ew-add-new-line" class="btn btn-success-ew"  ><i class="fa fa-plus"></i> <?=Yii::t('common','Get source document')?></a>                            -->
        </div>
    </div>
</div>
