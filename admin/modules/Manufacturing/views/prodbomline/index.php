<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Manufacturing\models\ProdBomLineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Bom Lines');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bom-line-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Bom Line'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'bom_no',
            //'header.name',
            [
                'attribute' => 'header.name',
                'format' => 'raw',
                'label' => Yii::t('common','Bom name'),
                'value' => function($model){
                    return Html::a('<div class="text-warning">'.$model->header->name.'</div>',['/Manufacturing/prodbom/view', 'id' => $model->id],['target' => '_blank']);
                }
            ],
            //'item_no',
            [
                'attribute' => 'item_no',
                'format' => 'raw',
                'label' => Yii::t('common','Items no'),
                'value' => function($model){
                    return Html::a('<div class="text-primary">'.$model->items->master_code.'</div>',['view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'label' => Yii::t('common','Name'),
                'value' => function($model){
                    return Html::a('<div class="text-primary">'.$model->items->Description.'</div>',['view', 'id' => $model->id]);
                }
            ],
            //'name',
            //'description:ntext',
             'quantity',
            // 'color_style',
            // 'comp_id',
            // 'user_id',
            // 'base_unit',
            // 'measure',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
