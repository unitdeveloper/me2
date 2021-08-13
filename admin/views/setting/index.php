<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Purchase\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Customize Print page');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="purchase-header-index" ng-init="Title='<?=$this->title?>'">
 
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table  table-bordered '],
         
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->name,['printer-editable', 'page' => $model->module]);
                }
            ],
          
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:320px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group"> {view}   {update}  {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    // 'receive' => function($url,$model,$key){
                       
                        
                    //     if(!$model->received){
                    //         if($model->countline > 0){
                    //             return Html::a('<i class="fas fa-hand-holding-heart"></i> '.Yii::t('common','Product Receive'),$url,['class'=>'btn btn-warning']);
                    //         }
                    //         //return Html::a('<i class="fas fa-hand-holding-heart"></i> '.Yii::t('common','Product Receive'),$url,['class'=>'btn btn-info']);
                    //     }
                        
                    // },
                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Print'),$url,['class'=>'btn btn-info','target'=>'_blank']);
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> '.Yii::t('common','View'),['printer-editable', 'page' => $model->module],['class'=>'btn btn-default']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> '.Yii::t('common','Update'),['printer-editable', 'page' => $model->module],['class'=>'btn btn-success']);
                    }

                  ]
              ],
        ],
    ]); ?>
</div>

 <?php 
$js=<<<JS

 

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');