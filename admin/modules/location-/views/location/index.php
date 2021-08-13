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
            //'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    if($model->status==0){
                        return 'Disable';
                    }else if($model->status==1){
                        return 'Enable';
                    
                    }else {
                        return 'Block';
                    }
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
