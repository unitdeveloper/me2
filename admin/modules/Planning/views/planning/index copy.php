<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Planning\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Item Mystores');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-mystore-index font-roboto">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'item',
            'master_code',

            'name',

            [
                'attribute' => 'items.invenByCache',
                'label' => Yii::t('common','Stock'),
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->items->invenByCache, 2);
                }
            ],   

            [
                'attribute' => 'safety_stock',
                //'label' => Yii::t('common','Stock'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->safety_stock, 2);
                }
            ],  

            [
                'attribute' => 'reorder_point',
                //'label' => Yii::t('common','Stock'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->reorder_point, 2);
                }
            ],  

            [
                'attribute' => 'minimum_stock',
                //'label' => Yii::t('common','Stock'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->minimum_stock, 2);
                }
            ]       
            
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
