<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Purchase\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Project Controls');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="project-control-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

     
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'name',
            //'budget:decimal',
            [
                'label' => Yii::t('common','Day'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    $datetime1 = new DateTime($model->start_date);
                    $datetime2 = new DateTIme($model->end_date);
                    $interval = $datetime1->diff($datetime2);
                    return $interval->format('%R%a');
                }
            ],
            
            [
                'attribute' => 'budget',
                'label' => Yii::t('common','Budget'),
                'format'=>['decimal',0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return $model->budget;
                }
            ],
            [
                'attribute' => 'remaining',
                'label' => Yii::t('common','Remaining'),
                'format'=>'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return $model->percent->progress2;                   
                }
            ],
            //'start_date',
            //'end_date',
            
            //'create_date',
            //'user_id',
            //'comp_id',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:320px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{view} {update} {delete}</div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> '.Yii::t('common','View'),$url,['class'=>'btn btn-info']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common','Delete'),$url,[
                            'class' => 'btn btn-warning',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> '.Yii::t('common','Update'),$url,['class'=>'btn btn-success']);
                    }

                  ]
              ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
 