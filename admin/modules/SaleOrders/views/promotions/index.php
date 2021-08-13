<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\PromotionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Promotions');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="promotions-index" ng-init="Title='<?= Html::encode($this->title) ?>'">

 
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'item_group',
            //'items',
             
            [
                'attribute' => 'sale_amount',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'value' => function($model){
                    return number_format($model->sale_amount,2);
                }
            ],
            [
                'attribute' => 'discount',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right font-roboto'],
                'value' => function($model){
                    return number_format($model->discount,2);
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('common','Status'),
                'format' => 'raw',
                'headerOptions' => ['style' => 'min-width:80px;'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    switch ($model->status) {
                        case 0:
                            $html = '<i class="far fa-circle"></i>';
                            break;
                        case 1:
                            $html = '<i class="fas fa-circle text-aqua"></i>';
                            break;
                        case 2:
                            $html = '<i class="fas fa-circle text-gray"></i>';
                            break;
                        case 3:
                            $html = '<i class="fas fa-circle text-warning"></i>';
                            break;
                        case 4:
                            $html = '<i class="fas fa-circle text-green"></i>';
                            break;
                        case 5:
                            $html = '<i class="fas fa-circle text-dark"></i>';
                            break;

                        default:
                            $html = '<i class="fas fa-circle"></i>';
                            break;
                    }
                    
                    
                    return $html;
                },
                'filter' => Html::activeDropDownList($searchModel,'status',
                [
                    '0' => Yii::t('common','Write'),
                    '1' => Yii::t('common','Send Approve'),
                    '2' => Yii::t('common','Closed'),
                    '3' => Yii::t('common','Reject'),
                    '4' => Yii::t('common','Release'),
                    '5' => Yii::t('common','Cancel')
                ],
                [                        
                    'class' => 'form-control',
                    'prompt' => Yii::t('common','All'),
                ]),
            ],
            //'create_by',
            //'create_date',
            //'approve_by',
            //'approve_date',
            //'status',
            //'comp_id',

           // ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:200px;'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'template'=>'<div class="btn-group btn-group text-center" role="group"> {view} {update} {delete} </div>',
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
                        return Html::a('<i class="fas fa-print"></i> ',$url,['class'=>'btn btn-info','target'=>'_blank']);
                    },
                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-success']);
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
                        return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-primary']);
                    }
        
                  ]
              ],
        ],
    ]); ?>
</div>
