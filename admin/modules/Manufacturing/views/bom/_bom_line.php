<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\KitbomLine */
// $this->registerCssFile('@vendor/almasaeed2010/adminlte/plugins/colorpicker/bootstrap-colorpicker.min.css', 
//     ['depends' => [\yii\web\JqueryAsset::className()]]);

// $this->registerJsFile('@vendor/almasaeed2010/adminlte/plugins/colorpicker/bootstrap-colorpicker.min.js', [
//     'depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Kitbom Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kitbom-line-view">
<?php 
        $gridColumns = [
            ['class' => 'kartik\grid\SerialColumn'],
            
            //'code',
             
            //'name',
            [  
                //'attribute' => 'Photo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],
                'label' => Yii::t('common', 'Image'),
                'value' => function($model){  
                    if($model->items->Photo==""){
                        return Html::a(Html::img('images/nopic.png',['style'=>'width:50px;']));
                    }else{
                        return Html::a(Html::img('images/product/'.$model->items->ItemGroup.'/'.$model->items->Photo,['style'=>'width:50px;']));
                        // return Html::a(Html::img(Yii::$app->urlManagerFrontend->baseUrl.'/images/product/'.$model->ItemGroup.'/'.$model->Photo,
                        // ['style'=>'width:50px;']) .favolite($model), ['items/view','id'=>$model->No]);
                    }                   
                    
                }
            ],
            
            [
                'attribute' => 'items.master_code',
                'format' => 'raw',
                'value' => function($model)
                {
                    return Html::a($model->items->master_code,['/items/items/view','id' => $model->item],['target' => '_blank']);
                },
                'footer' => '<div class="ew-item-insert"><input type="text" name="master_code"  class="form-control master_code" placeholder="'.Yii::t('common','Product Code').'"></div>',
            ],
            
            //'items.master_code',
            //'items.Description',
            [
                'attribute' => 'items.Description',
                'format' => 'raw',
                'value' => function($model)
                {
                    return $model->items->Description;
                },
                'footer' => '<div id="InsertDesc"></div>',
            ],
            //'item_set'
            //'quantity',
            [
                'label' => Yii::t('common','The name that will be created'),
                'value' => function($model)
                {
                    return $model->name;
                },
                'footer' => '<div class="ew-item-insert"><input type="text" name="name"  class="form-control" placeholder="'.Yii::t('common','For generate Code').'"></div>',
            ],
            [
                'label' => Yii::t('common','Default quantity'),
                'value' => function($model)
                {
                    return $model->quantity;
                },
                'footer' => '<div class="ew-item-insert"><input type="text" name="quantity"  class="form-control" placeholder="'.Yii::t('common','Default quantity').'"></div>',
            ],
            [
                'attribute' => 'color_style',
                'value' => function($model)
                {
                    return $model->color_style;
                },
                'footer' => '<div class="ew-item-insert"><input type="text" name="color_style"  class="form-control ew-color" placeholder="red, gree, pink..."></div>',
            ],
            //'color_style',
            // 'comp_id',
            // 'user_id',

            [
              'class' => 'yii\grid\ActionColumn',
              'options'=>['style'=>'width:150px;'],
              'buttonOptions'=>['class'=>'btn btn-default','title' => ''],
              'template'=>'<div class="btn-group btn-group-sm text-center pull-right" role="group">  {delete}  </div>',
              'footer' => '<div class=""><button class="btn btn-default col-xs-12 ew-btn-insert"><i class="fa fa-arrow-right"></i> '.Yii::t('common','Add').'</button></div>',
            ],
        ]; ?>
<?php
 // ExportMenu::widget([
 //            'dataProvider' => $dataProvider,
 //            'columns' => $gridColumns,
 //            'columnSelectorOptions'=>[
 //                'label' => 'Columns',
 //                'class' => 'btn btn-danger'
 //            ],
 //            'fontAwesome' => true,
 //            'dropdownOptions' => [
 //                'label' => 'Export All',
 //                'class' => 'btn btn-primary'
 //            ]
 //        ]); 
?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,

        'showFooter' => true,
        'columns' => $gridColumns,

         
    ]); ?>

</div>
 