<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\location\models\SearchLocation */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Locations');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="location-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?= Html::a(Yii::t('common', 'Create Location'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'code',
            'name',
            'description',
            
            [
                'attribute' => 'status',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value' => function($model){
                    if($model->status==0){
                        return Yii::t('common','Disable');
                    }else if($model->status==1){
                        return Yii::t('common','Enable');
                    
                    }else {
                        return Yii::t('common','Block');
                    }
                }
            ],
            [
                'attribute' => 'defaultlocation',
                'label' => Yii::t('common','Default'),
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value' => function($model){
                    if($model->defaultlocation==1){
                        return '<span class="text-success"><i class="far fa-check-square"></i></span>';
                    }else if($model->defaultlocation==0){
                        return '<span class="text-gray"><i class="far fa-square"></i></span>';
                    
                    }else {
                        return 'Not Set';
                    }
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
