<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Manufacturing\models\PdrSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Production Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-request-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Production Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'create_date',
            'item',
            'quantity',
            'remark',
            //'no',
            //'user_id',
            //'comp_id',
            //'posting_date',
            //'request_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
