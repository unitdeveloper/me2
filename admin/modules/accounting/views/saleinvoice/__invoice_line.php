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

                    if($model->type=='Item')
                    {
                        // $item = Items::find()->where(['No' => $model->code_no_])->one();
                        // $code = $item->master_code;
                       
                        $code =  $model->crossreference->no;
                       
                    }else {
                        // ค้นจาก GL Account
                        //
                        //$code = 'G/L Number';
                         
                        $code = '';
                    }
                    $color = '';
                    if($model->status=='delete') $color = 'text-red';
                    if($model->item=='1414') $color = 'text-orange';

                    $html = '<div class="'.$color.'">'.Html::a($code,['/items/items/view', 'id' => $model->item],['target' => '_blank', 'data-pjax' => "0"]).'</div>';

                    // ถ้าเป็นข้อความ
                    // 
                    if($model->item=='1414'){
                        if($model->code_no_=='1^x'){
                            $html = '<div class="'.$color.'"> </div>';
                        }else if($model->code_no_==' '){
                            $html = '<div class="'.$color.'"> </div>';
                        }else {
                            $html = '<div class="'.$color.'">'.$model->code_no_.'</div>';
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
                    $color = '';
                    if($model->item=='1414') $color = 'text-orange';
                    return '<input type="text" value="'.$model->crossreference->desc.'" class="form-control '.$color.' text-line next" name="desc" autocomplete="off">';
                },

                'footer' => '<div class="ew-type"><input type="text" class="form-control ew-InsertDesc"></div>',
            ],
            [
                //'attribute'=>'quantity',
                'format'=>'raw',
                'label' => Yii::t('common','Quantity'),
                'headerOptions' => ['class' => 'text-right','style' => 'width:100px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                    return '<input type="number" value="'.($model->quantity * 1).'" class="form-control text-right text-line next" name="qty" autocomplete="off">';

                },

                'footer' => '<div class="ew-type">
                                    <input type="text" class="form-control text-right ew-direct-qty"></div>',
            ],

            [
                'label' => Yii::t('common','Measure'),
                'headerOptions' => ['class' => 'bg-gray','style' => 'width:120px', 'title' => 'หากไม่มีให้เลือก ต้องไปเพิ่มหน่วยในสินค้าตัวนั้นๆ'],
                'contentOptions' => ['class' => ' '],
                'format' => 'raw',
                'value' => function($model){
                    $html = '<select class="form-control measure-change" name="measure">';
                    //$html.= '<option value="0">'.Yii::t('common','(not set)').'</option>';
                    foreach ($model->itemMeasures as $key => $value) {
                        $html.= '<option value="'.$value->measures->id.'" '.($model->measure == $value->measure ? 'selected' : '').'>'.$value->measures->UnitCode.'</option>';
                    }
                    $html.= '</select>';
    
                    return $html;
                },                     
            ],

            [
                'format'=>'raw',
                'label' => Yii::t('common','Unit Price'),
                'headerOptions' => ['class' => 'text-right','style' => 'width:120px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                    return '<input type="number" value="'.($model->unit_price * 1).'" class="form-control text-right text-line next" name="price" autocomplete="off">';

                },
                'footer' => '<div class="ew-type"><input type="text" class="form-control text-right ew-direct-price"></div>',

            ],
            [
                'format'=>'raw',
                'label' => Yii::t('common','Discount').'(%)',
                'headerOptions' => ['class' => 'text-right','style' => 'width:90px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){

                    return '<input type="number" value="'.($model->line_discount * 1).'" class="form-control text-right text-line next" name="line_discount" autocomplete="off">';

                },
                'footer' => '<div class="ew-type"><input type="text" class="form-control text-right ew-line-discount"></div>',

            ],
            [
                'label' => Yii::t('common','Line Amount'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right','style' => 'width:102px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    $total      = $model->quantity * $model->unit_price;
                    $discount   = ($model->line_discount / 100) * $total;
                    $LineTotal  = ($total) - $discount;
                    //return number_format(($model->quantity * $model->unit_price)- $model->line_discount);
                    return '<div class="ew-line-total" data="'.$LineTotal.'">'.number_format($LineTotal,2).'</div>';
                },
                //'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
                'footer' => ' ',
            ],
            [
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right', 'style' => 'width:70px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                   return '<div class="btn btn-danger ew-delete-inv-line" data="'.$model->id.'"><i class="fa fa-trash-o" aria-hidden="true"></i></div>';
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
