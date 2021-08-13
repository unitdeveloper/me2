<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvheaderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Invoice Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-invoice-header-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Sale Invoice Header'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'no_',
            'cust_no_',
            'cust_name_',
            'cust_address:ntext',
            // 'cust_address2:ntext',
            // 'posting_date',
            // 'order_date',
            // 'ship_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
