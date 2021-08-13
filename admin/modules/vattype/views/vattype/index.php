<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\vattype\models\SearchVatType */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Vat Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="vat-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

     
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
              'class' => 'yii\grid\ActionColumn',
              'options'=>['style'=>'width:150px;'],
              'buttonOptions'=>['class'=>'btn btn-default'],
              'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update}  </div>'
           ],
            //'id',
            //'name',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->name,['view', 'id' => $model->id]);     
                },
            ],
            'description',
            'vat_value',
            //'comp_id',
            'company.name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
