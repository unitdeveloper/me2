<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\customers\models\GroupsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Customer Groups');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="customer-groups-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            //'detail:ntext',
            [
                'label' => Yii::t('common','Customer').' ('.Yii::t('common','Quantity').')',
                'format' => 'html',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    return count($model->customers);
                }
            ],
            [
                'label' => Yii::t('common','Responsible'),
                'format' => 'html',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    return count($model->salepeople);
                }
            ],
           // 'comp_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:260px;'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'template'=>'<div class="btn-group btn-group text-center" role="group"> {view} {update} {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[

                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',['/customers/groups/view','id' => $model->id],['class'=>'btn btn-primary']);
                    },

                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',['/customers/groups/delete','id' => $model->id],[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },

                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',['/customers/groups/update','id' => $model->id],['class'=>'btn btn-success']);
                    }
        
                  ]
              ],
        ],
    ]); ?>
</div>
