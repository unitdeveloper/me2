<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Manufacturing\models\ProductionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Production Orders');
$this->params['breadcrumbs'][] = $this->title; 
?>
<div class="production-order-index" ng-init="Title='<?= Html::encode($this->title) ?>'">
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
     
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'attribute' => 'order_date',
                'label'     => 'Date',
                'format'    => 'text',
                'contentOptions' => ['style' => 'width:150px;'],
                'filter'=> '<div class="drp-container input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>'.
                        DateRangePicker::widget([
                            'name'              => 'ProductionSearch[order_date]',
                            //'value'             => @$_GET['ProductionSearch']['order_date'],
                            'pluginOptions'     => [ 
                                'locale'    => [
                                    'separator' => ' - ',
                                ],
                            'opens'             => 'right'
                            ] 
                        ]) . '</div>',
                'content'=>function($data){
                    return Yii::$app->formatter->asDatetime($data['order_date'], "php:d-M-Y");
                }
            ],

            // [
            //     'attribute' => 'order_date',
            //    'value' => 'order_date',
            // ],
            //'id',
            [
                'attribute' => 'order_id',
                'value'     => 'order.no'
            ],
            'no',
            //'create_date',
           
            
            //'status',
            //'comp_id',
            //'user_id',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'headerOptions'     => ['class' => 'hidden-xs','style'=>'min-width:250px;'],
                'contentOptions' => ['class' => 'hidden-xs text-right'],
                'filterOptions'     => ['class' => 'hidden-xs'],                
                'template'=>'<div class="btn-group btn-group text-center" role="group">{view} {print}  {update}  {delete} </div>',
                'options'=> ['style'=>'width:350px;'],
                'buttons'=>[
                    'print' => function($url,$model,$key){                      
                        return Html::a('<i class="fas fa-print"></i> '.Yii::t('common','Print'),
                        [
                            'print',
                            'id' => $model->id,
                            'no' => $model->no
                        ],
                        ['class'=>'btn btn-info-ew','target'=>'_blank']);
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> '.Yii::t('common','View'),['view','id' => $model->id,'no' => $model->no],['class'=>'btn btn-default-ew']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common','Delete'),['delete','id' => $model->id,'no' => $model->no],[
                            'class' => 'btn btn-danger-ew',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        if ($model->status == 'Posted') {
                            return '<button type="button" class="btn btn-warning-ew" disabled><i class="far fa-edit"></i> '.Yii::t('common','Update').'</button>';
                        }else{
                            return Html::a('<i class="far fa-edit"></i> '.Yii::t('common','Update'),['update','id' => $model->id,'no' => $model->no],['class'=>'btn btn-warning-ew']);
                        }
                        
                    }

                  ]
              ],
        ],
        'options' => [
            'class' => 'table font-roboto',                
        ], 
    ]); ?>
</div>
