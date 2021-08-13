<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\runingnoseries\models\SearchRunnose */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Runing Noseries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="runing-noseries-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Runing Noseries'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'no_series',
            'start_date',
            'start_no',
            'last_no',
            // 'comp_id',

            //['class' => 'yii\grid\ActionColumn'],
            [
              'class' => 'yii\grid\ActionColumn',
              'options'=>['style'=>'width:150px;'],
              'buttonOptions'=>['class'=>'btn btn-default','title' => ' '],
              'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
           ],
        ],
    ]); ?>
</div>
