<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\config\model\ImportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Import Files');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-file-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('common', 'Create Import File'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'description',
            'position_qty',
            'position_qty_num',
            //'position_discount',
            //'position_discount_num',
            //'position_total',
            //'position_total_num',
            //'keyword_po',
            //'auto_remark',
            //'find_code',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
