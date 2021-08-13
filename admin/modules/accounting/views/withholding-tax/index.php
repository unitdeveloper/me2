<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\WithholdingTaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Withholding Taxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withholding-tax-index">

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

            'id',
            'customer_id',
            'customer_address',
            'vat_regis',
            'comp_id',
            //'comp_address',
            //'user_id',
            //'user_name',
            //'choice_substitute',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
