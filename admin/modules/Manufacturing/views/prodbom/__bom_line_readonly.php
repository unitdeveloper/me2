<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
//use yii\grid\GridView;

?>
<?php $columns = [
            ['class' => 'yii\grid\SerialColumn'],
            [  
              'format' => 'raw',
              'contentOptions' => ['class' => 'relative'],
              'label' => Yii::t('common', 'Image'),
              'value' => function($model){  
                 return Html::a(Html::img($model->items->picture,['style'=>'width:50px;']),['/items/items/view', 'id' => $model->items->id],['target' => '_blank']);    
              }             
            ], 
            //'items.master_code',
            [
                'label' => Yii::t('common','Code'),
                'format' => 'html',
                'headerOptions' => ['style' => 'width:150px;'],
                'value' => function($model){
                    if($model->items->ProductionBom != ''){
                        $html = Html::a($model->items->master_code,['view', 'id' => $model->items->ProductionBom],['target' => '_blank']);
                    }else{
                        $html = $model->items->master_code;
                    }
                    return $html;
                },
                'footer' => '<div class="ew-item-insert">
                                    <input type="text" name="InsertItem"  class="form-control InsertItem"></div>',
            ],

            //'code',
            //'name',
            [
                'attribute' => 'name',
                'label' => Yii::t('common','Items'),
                'format' => 'html',
                'value' => function($model){
                    
                    if($model->description==''){
                        $html = '<div>'.$model->items->description_th.'</div>';
                    }else {
                        $html = '<div>'.$model->items->description_th.'</div>';
                        $html.= '<div>'.$model->description.'</div>';
                         
                    }

                    return $html;
                },
                'footer' => '<div class="ew-desc"><input type="text" name="InsertDesc" ew-item-code="eWinl" id="InsertDesc" class="form-control"></div>',
            ],  
            //'base_unit',
            [
                'label' => Yii::t('common','Quantity base'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right', 'style' => 'width:150px;'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->base_unit,2);
                },
                'footer' => '<div class="ew-qty"><input type="number" name="InsertQty" id="InsertQty" class="form-control"></div>',
            ],

            [
                'label' => Yii::t('common','Stock'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return $model->items->inven;
                },
                'footer' => '',
            ],
            [
                'label' => Yii::t('common','Standard Cost'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return '<div class="consumption">'.$model->items->pricing->stdcost.'</div>';
                },
                'footer' => '<div class="sum-con"></div>',
            ],


             
            [
                 'attribute' => 'measure',
                 'format' => 'raw',
                  
                 'value' => function($model){
                    return $model->measure;   
                 },
                 'footer' => '<div class="measure"></div>',
              ],
            
        ]; ?>



<?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => false,
        'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
        'columns' => $columns,
    ]); ?>