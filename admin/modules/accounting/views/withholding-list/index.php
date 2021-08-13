<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\WithholdingListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Withholding Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withholding-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'create_date',
            'update_date',
           // 'user_id_create',
            //'user_id_update',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
