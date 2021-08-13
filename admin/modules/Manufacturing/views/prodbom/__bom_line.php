<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
//use yii\grid\GridView;

?>
<?php $columns = [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'bg-dark'],
            ],
            [  
              'format' => 'raw',
              'headerOptions' => ['class' => 'bg-dark'],
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
                'headerOptions' => ['class' => 'bg-dark', 'style' => 'width:150px;'],
                'contentOptions' => ['class' => 'font-roboto'],
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
                'headerOptions' => ['class' => 'bg-dark'],
                'contentOptions' => ['class' => 'font-roboto'],
                'format' => 'html',
                'value' => function($model){
                    
                    // if($model->description==''){
                    //     $html = '<div>'.$model->items->description_th.'</div>';
                    // }else {
                    //     $html = '<div>'.$model->items->description_th.'</div>';
                    //     //$html.= '<div>'.$model->description.'</div>';
                         
                    // }

                    return $model->name;
                },
                'footer' => '<div class="ew-desc"><input type="text" name="InsertDesc" ew-item-code="eWinl" id="InsertDesc" class="form-control"></div>',
            ],  
            //'base_unit',
            
            [
                'label' => Yii::t('common','Stock'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-dark'],
                'contentOptions' => ['class' => 'text-right font-roboto bg-info'],
                'value' => function($model){
                    $stock = $model->items->ProductionBom > 0
                                ? $model->items->myItems->last_possible
                                : $model->items->myItems->last_stock;
                    return Html::a(number_format($stock), ['/warehousemoving/warehouse', 'WarehouseSearch[ItemId]' => base64_encode($model->item)], ['target' => '_blank']);
                },
                'footer' => '',
            ],

            [
                'label' => Yii::t('common','Quantity base'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-dark', 'style' => 'width:150px;'],
                'contentOptions' => ['class' => 'text-right font-roboto bg-yellow'],
                'value' => function($model){
                    return $model->base_unit *1;
                },
                'footer' => '<div class="ew-qty"><input type="number" name="InsertQty" id="InsertQty" class="form-control"></div>',
            ],
            
            
            // [
            //     'label' => Yii::t('common','Standard Cost'),
            //     'format' => 'raw',
            //     'headerOptions' => ['class' => 'text-right bg-dark'],
            //     'contentOptions' => ['class' => 'text-right bg-yellow'],
            //     'value' => function($model){
            //         return '<div class="consumption">'.$model->items->pricing->stdcost.'</div>';
            //     },
            //     'footer' => '<div class="sum-con"></div>',
            // ],

            [
                'label' => Yii::t('common','Real Cost'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-dark'],
                'contentOptions' => ['class' => 'text-right font-roboto bg-gray'],
                'value' => function($model){
                    return '<div class="consumption">'.(
                            $model->items->ProductionBom != '' 
                                ? $model->items->pricing->conCost 
                                : $model->items->pricing->stdcost
                            ).'</div>';
                },
                'footer' => '<div class="sum-con"></div>',
            ],


            

            // [
            //     'label' => Yii::t('common','Fix Inventory'),
            //     'format' => 'raw',
            //     'value' => function($model){
                   
            //         return $model->items->Inventory;
            //     },
            //     'footer' => '',
            // ],

            // [
            //     'label' => Yii::t('common','Min Bom'),
            //     'format' => 'raw',
            //     'value' => function($model){
            //         $Fnc = new admin\modules\Itemset\models\FunctionItemset;

            //         $CalInven = $Fnc->ProMinBomLine($model->bom_no,0,0);
                    
            //         $res = $CalInven;
                    

            //         if($res->status == 'error'){

            //             return '<label class="blink label-danger" style="padding:0px 5px 0px 5px;">Error !</label>';

            //         }else {

            //             $Min = $res->value - $model->items->Inventory;
            //             if($Min > 0 )
            //             {                        
            //               return $Min;
            //             }else {
            //               return '-';
            //             }

            //         }

                    
                    
            //     },
            //     'footer' => '',
            // ],

            // [
            //     'label' => Yii::t('common','Min available'),
            //     'format' => 'raw',
            //     'value' => function($model){

            //         // จำนวนที่สามารถผลิตได้

            //         $Fnc = new admin\modules\Itemset\models\FunctionItemset;

            //         $CalInven = $Fnc->ProMinBomLine($model->bom_no,0,0);
                   
            //         $res = $CalInven;

                    

            //         if($res->status == 'error'){
            //             return '<span class="text-danger">Some item Error Loop</span>';
            //         }else {
                         
            //             if($res->value > 0 )
            //             {                        
            //               return $res->value;
            //             }else {
            //               return '-';
            //             }
            //         }

                    
            //     },
            //     'footer' => '',
            // ],
            //'measure',
            [
                 'attribute' => 'measure',
                 'format' => 'raw',
                 'headerOptions' => ['class' => 'bg-dark'],
                 'value' => function($model){
                    return $model->measure;   
                 },
                 'footer' => '<div class="measure"></div>',
              ],
            [  
                                     

                  'format' => 'raw',                                       
                  'label' => Yii::t('common', 'Delete'),
                  'headerOptions' => ['class' => 'bg-dark text-right'],
                  'contentOptions' => ['class' => 'text-right'],
                  'value' => function($model){                     
                      return Html::a('<i class="glyphicon glyphicon-trash btn btn-danger-ew"></i>', '#'.$model->id,
                        [
                          'class'=>'RemoveBomLine',
                          'alt' => $model->items->Description,
                        ]);
                  },
                  'footer' => '<div class="ew-add"><input type="button" name="InsertAdd" class="btn btn-default" value="ADD"></div>',
              ],

             
           

             
        ]; ?>



<?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
        'columns' => $columns,
    ]); ?>