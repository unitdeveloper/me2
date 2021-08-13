<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\tracking\models\SaleTrackingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Order Trackings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-tracking-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Order Tracking'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'event_date',
            'doc_type',
            'doc_id',
            'doc_no',
            // 'doc_status',
            // 'amount',
            // 'remark:ntext',
            // 'ip_address',
            // 'lat_long',
            // 'create_by',
            // 'comp_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
