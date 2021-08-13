<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\PromotionGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Promotions Item Groups');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="hidden-xs">
	<?=Breadcrumbs::widget([
		'itemTemplate' => "<i class=\"fas fa-home\"></i> <li><i>{link}</i></li>\n", // template for all links
		'links' => [
            [
                'label' => Yii::t('common','Promotions'),
                'url' => ['promotions/index', 'id' => 10],
                'template' => "<li><b>{link}</b></li>\n", // template for this link only
            ],
            //['label' => 'Sample Post', 'url' => ['post/edit', 'id' => 1]],
            $this->title,
            
		],
	]);?>
</div>

<div class="promotions-item-group-index" ng-init="Title='<?= Html::encode($this->title) ?>'">
 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'name',
                'label' => Yii::t('common','Name'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->name,['view-name','name' => $model->name]);
                }
            ],
            'description',
            // [
            //     'label' => Yii::t('common','Sale Amount'),
            //     'format' => 'raw',
            //     'headerOptions' => ['class' => 'text-right'],
            //     'contentOptions' => ['class' => 'text-right'],
            //     'value' => function($model){
            //         return ($model->promotions)? number_format($model->promotions->sale_amount): '';
            //     }
            // ],
            // [
            //     'label' => Yii::t('common','Discount'),
            //     'format' => 'raw',
            //     'headerOptions' => ['class' => 'text-right'],
            //     'contentOptions' => ['class' => 'text-right'],
            //     'value' => function($model){
            //         return ($model->promotions)? number_format($model->promotions->discount) : '';
            //     }
            // ],
             
            //'items',
            //'comp_id',

            //['class' => 'yii\grid\ActionColumn'],
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
                        return Html::a('<i class="fas fa-eye"></i> ',['/SaleOrders/promotions-item-group/view-name','name' => $model->name],['class'=>'btn btn-primary']);
                    },

                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',['/SaleOrders/promotions-item-group/delete','id' => $model->field->id],[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },

                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',['/SaleOrders/promotions-item-group/update','id' => $model->field->id],['class'=>'btn btn-success']);
                    }
        
                  ]
              ],
        ],
    ]); ?>
</div>
