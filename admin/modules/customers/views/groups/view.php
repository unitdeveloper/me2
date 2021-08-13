<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerGroups */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Customer Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="customer-groups-view" ng-init="Title='<?= Html::encode($this->title) ?>'">

    

    <div class="row">
        <div class="col-sm-4">
            <h1><?= Html::encode($this->title) ?></h1>

            
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'detail:ntext',
                    'comp.name',
                    
                ],
            ]) ?>

            
         
            <?php if(count($model->salepeople) > 0) : ?>
            <hr />
            <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=Yii::t('common','Sale People')?></h3>
                    </div>
                    <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProviders,
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                            ],
                            'salespeople.code',
                            [
                                'attribute' => 'salespeople.name',
                                'format' => 'html',
                                'value' => function($model){
                                    return Html::a($model->salespeople->name,['/customers/responsible/view','id' => $model->salespeople->id]);
                                }
                            ]
                            
                        ],
                    ]); ?>         
                    </div>
            </div>
            <?php endif?>
            

            <p>
                <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
        <div class="col-sm-8"> 
            <h3><?=Yii::t('common','Customer')?></h3>               
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'customer.code',
                        'value' => 'customer.code',
                    ],
                    [
                        'attribute' => 'customer.name',
                        'format' => 'html',
                        'value' => function($model){
                            return Html::a($model->customer->name,['/customers/customer/view','id' => $model->customer->id]);
                        }
                    ],
                    [
                        'attribute' => 'customer.locations.province',
                        'value' => 'customer.locations.province'
                    ],
                     
                ],
            ]); ?>            
        </div>
    </div>
    

</div>
