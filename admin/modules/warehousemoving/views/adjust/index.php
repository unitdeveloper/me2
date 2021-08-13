<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\AdjustSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Item Adjust');
//$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="warehouse-header-index" ng-init="Title='<?= Html::encode($this->title) ?>'">



<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
            return ['class' => 'update pointer'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],


            'DocumentDate',

             [
                'attribute' => 'DocumentNo',
                'format' => 'html',
                'contentOptions' => ['class' => 'relative'],

                'value' => function($model){

                    return Html::a($model->DocumentNo,['adjust/update','id'=>$model->id]);

                }
            ],
  
             'SourceDoc',

 
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:260px;'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">  {print}   {update} {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Print'),$url,['class'=>'btn btn-info','target'=>'_blank']);
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> '.Yii::t('common','View'),$url,['class'=>'btn btn-default']);
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
                        return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success']);
                    }
        
                  ]
              ],
        ],
    ]); ?>
</div>

 