<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Manufacturing\models\ProdBomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Bom Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bom-header-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           //   [
           //    'class' => 'yii\grid\ActionColumn',
           //    'options'=>['style'=>'width:150px;'],
           //    'buttonOptions'=>['class'=>'btn btn-default'],
           //    'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
           // ],

            //'id',
            //'code',
            [
                'attribute' => 'code',
                'format' => 'raw',
                'label' => Yii::t('common','Code'),
                'value' => function($model){
                    return Html::a('<div class="text-primary">'.$model->code.'</div>',['view', 'id' => $model->id]);
                }
            ],
            //'name',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'label' => Yii::t('common','Name'),
                'value' => function($model){
                    return Html::a('<div class="text-primary">'.$model->name.'</div>',['view', 'id' => $model->id]);
                }
            ],
            'description:ntext',
            'item_set:ntext',
            [
                 
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'label' => Yii::t('common','Unit Cost'),
                'value' => function($model){
                    return $model->unitcost;
                }
            ],
            // 'max_val',
            // 'priority',
            // 'comp_id',
            // 'user_id',
            // 'multiple',
            // 'format_gen',
            // 'format_type',
            // 'running_digit',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
