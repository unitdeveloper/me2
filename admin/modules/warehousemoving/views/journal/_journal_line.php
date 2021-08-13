<?php

use yii\helpers\Html;
//use kartik\grid\GridView;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Items;

use common\models\WarehouseMoving;
use common\models\Itemunitofmeasure;
?>

<div class="table">




    <?php
    
    $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('common','Items'),
                'headerOptions' => ['style' => 'width:200px;'],
                'format' => 'raw',
                'value' => function($model){
                    $color = '';
                    return '<div style="color:'.$color.'">'.$model->items->master_code.'</div>';
                },    
                'footer' => '<div class="form-group has-feedback ew-item-insert">
                              <div class="form-group has-success">
                                <input type="text" name="InsertItem"  class="form-control InsertItem ew-InsertItems" placeholder="'.Yii::t('common','Search product').'">
                                <span class="form-control-feedback " aria-hidden=""><i class="glyphicon glyphicon-search"></i></span>
                              </div>
                            </div>',
            ],
             
            [
                'format' => 'raw',
                'label' => Yii::t('common','Description'),
                'contentOptions' => [
                        'style'=>'max-width:300px; min-height:100px; overflow: auto; word-wrap: break-word;'
                    ],
                'value' => function($model){
                    return $model->Description;
                },
                'footer' => '<div class="">
                                    <input type="text" class="form-control ew-InsertDesc" ew-item-code="eWinl" readonly="readonly"></div>',
            ],
            [
                'format'=>'raw',
                'label' => Yii::t('common','Current'),
                'headerOptions' => ['class' => 'text-right  hidden','style' => 'width:110px;'],
                'contentOptions' => ['class' => 'text-right hidden'],
                'footerOptions' => ['class' => 'hidden'],
                'value' => function($model){
                    if($model->source_id!='')
                    {
                        $Header         = \common\models\ItemJournal::findOne($model->source_id);
                        $PostingDate    = $Header->PostingDate;
                    }else {
                        $PostingDate    = date('Y-m-d H:i:s');
                    }
                    $font = 'text-success';
                    $Remaining  = $model->items->Inventory + $model->getStockOnHand($PostingDate); 
                    $D          = NULL;
                    $DiffQty    = '';
                    $datetime1 = new DateTime($PostingDate);
                    $datetime2 = new DateTime(date('Y-m-d H:i:s'));
                    $interval = $datetime1->diff($datetime2);
                    if($interval->format('%a')>0){
                        $AlInven = $model->getSumQty();
                        $DiffQty = $model->items->Inventory + $AlInven;
                        $D = "<div class='text-warning'>All : ".number_format($DiffQty,2)."</div>";
                        $D.= "<div class='text-warning'>D : ".number_format($DiffQty - $Remaining,2)."</div>";
                    }
                    if($Remaining==$DiffQty) $D = NULL;
                    return '<div  >'.number_format($Remaining,2).'</div>'.$D;
                },
            ],
            [
                'label' => Yii::t('common','Inventory'),
                'headerOptions' => ['class' => 'text-right ','style' => 'width:110px;'],
                'contentOptions' => ['class' => 'text-right inventory'],
                'value' => function($model){
                    return $model->items->invenByLocation($model->location);
                }  
            ],
            [
                //'attribute'=>'Quantity',
                'format'=>'raw',
                'label' => Yii::t('common','Quantity'),
                'headerOptions' => ['class' => 'text-right','style' => 'min-width:90px;'],
                'contentOptions' => ['class' => ' ','align' => 'right'],
                'footerOptions' => ['align' => 'right'],
                'value' => function($model){
                    return Html::textInput('ItemJournalLine[Quantity]['.$model->id.']',$model->Quantity * 1,['type' => 'number','class' => 'form-control text-right journal-qty','style' => 'width:90px;']);
                },
                //'pageSummary'=>true,
                'footer' => '<div class="" style="width:90px; ">
                                    <input type="text" class="form-control ew-direct-qty text-right"></div>',
            ],

            
            [
                'label' => Yii::t('common','Measure'),
                'format' => 'raw',                 
                'value' => function($model){
                    
                    return Html::activeDropDownList($model, 'unit_of_measure['.$model->id.']', 
                            ArrayHelper::map($model->measureList, 'measures.id', 'measures.UnitCode'),
                            ['class'=>'form-control','value' => $model->unit_of_measure,]
                    );
                },
                'footer' => ' ',
            ],

            [

                //'attribute'=>'Quantity',
                'format'=>'raw',
                'label' => Yii::t('common','After'),
                'headerOptions' => ['class' => 'text-right  hidden','style' => 'width:110px;'],
                'contentOptions' => ['class' => 'text-right hidden ew-adj-qty-after'],
                'footerOptions' => ['class' => 'hidden'],
                'value' => function($model){
                    $Quantity   = 0;
                    if($model->source_id!='')
                    {
                        $Header     = \common\models\ItemJournal::findOne($model->source_id);
                        if($Header->AdjustType == '+') $Quantity = abs($model->Quantity);
                        if($Header->AdjustType == '-') $Quantity = abs($model->Quantity) * -1;
                        $PostingDate    = $Header->PostingDate;
                    }else {
                        $Quantity       = abs($model->Quantity);
                        $PostingDate    = date('Y-m-d H:i:s');
                    }
                    $font       = 'text-success';
                    // $Query      = WarehouseMoving::find()->where(['ItemNo' => $model->ItemNo])->andWhere(['between', 'PostingDate', '0000-00-00',$PostingDate]);
                    // $RealInven  = $Query->sum('Quantity');
                    // $item       = Items::find()->where(['No' => $model->ItemNo])->one();
                    $Remaining  = $model->items->Inventory + $model->getStockOnHand($PostingDate);
                    $Remaining  = $Quantity + $Remaining;
                    if($Remaining < 0)
                    {
                        $font = 'text-danger';
                    }
                    $D          = NULL;
                    $DiffQty    = '';
                    $datetime1 = new DateTime($PostingDate);
                    $datetime2 = new DateTime(date('Y-m-d H:i:s'));
                    $interval = $datetime1->diff($datetime2);
                    if($interval->format('%a')>0){
                        $AlInven = $model->getSumQty();
                        $DiffQty = $Quantity + ($model->items->Inventory + $AlInven);
                        $D = "<div class='text-warning'>All : ".number_format($DiffQty,2)."</div>";
                        $D.= "<div class='text-warning'>D : ".number_format($DiffQty - $Remaining,2)."</div>";
                    }
                    if($Remaining==$DiffQty) $D = NULL;
                    return '<div class="'.$font.' " >'.number_format($Remaining ,2).'</div>'.$D;
                },
            ],
            [
                'format' => 'raw',
                'label' => Yii::t('common','Unit Price'),
                'headerOptions' => ['class' => 'text-right hidden','style' => 'width:90px;'],
                'contentOptions' => ['class' => 'text-right hidden'],
                'footerOptions' => ['align' => 'right','class' => 'hidden'],
                'value' => function($model){
                    //return $model->unit_price;
                    return Html::textInput('ItemJournalLine[unit_price]['.$model->id.']',$model->unit_price * 1,['type' => 'number','class' => 'form-control text-right','style' => 'width:120px;']);
                },
                'footer' => '<div class="">
                                    <input type="text" class="form-control ew-direct-price text-right"></div>',
            ],
            [
                'label' => Yii::t('common','Total'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right hidden','style' => 'width:90px;'],
                'contentOptions' => ['class' => 'text-right hidden'],
                'footerOptions' => ['align' => 'right','class' => 'hidden'],
                'value' => function($model){
                    $LineTotal = $model->Quantity * $model->unit_price;
                    //return number_format(($model->quantity * $model->unit_price)- $model->line_discount);
                    return '<div class="ew-line-total" data="'.$LineTotal.'">'.number_format(abs($LineTotal),2).'</div>';
                },
                //'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
                'footer' => ' ',
            ],
             
            [
                'label' => Yii::t('common','Store'),
                'format' => 'raw',                 
                'value' => function($model){

                    $query = \common\models\Location::find()
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->all();

                    return Html::activeDropDownList($model, 'location['.$model->id.']',
                            ArrayHelper::map($model->locationsList, 'id', 'code'),
                            [
                                'class'=>'form-control locations',
                                'value' => $model->location,
                            ]
                    );
                    
                    
                },
                'footer' => ' ',
            ],
            [
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right adjust-deleteBtn','style' => 'width:10px;'],
                'contentOptions' => ['class' => 'text-right adjust-deleteBtn'],
                'value' => function($model){
                if($model->status!='Posted')
                    return '<button class="btn btn-danger-ew btn-flat ew-delete-adj-line" data="'.$model->id.'"><i class="fa fa-times" aria-hidden="true"></i></button>';
                else
                    return '';
                },
                'footer' => '<div class="ew-add-to-adj-line"><input type="button" name="ew-InsertAdd" class="btn btn-default" value="ADD"></div>',
            ],
        ];

    ?>


    <?=  GridView::widget([
          'id' => 'ew-grid-adjust-line',
          'dataProvider'=> $dataProvider,
          //'summary' => true,
          'showFooter' => true,
          //'headerRowOptions' => ['class' => 'bg-dark'],
          'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
          'columns' => $gridColumns,
          'tableOptions' => ['id'=>'Item_Adjust_Line','class' =>'table table-bordered table-striped  '],
          //'pjax'=>true,
          //'responsive'=>true,
          //'hover'=>true,
          'summary' => false,
      ]);
      ?>

</div>
