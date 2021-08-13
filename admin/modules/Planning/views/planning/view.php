<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMystore */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Item Mystores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="item-mystore-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'item',
            'item_no',
            'barcode',
            'master_code',
            'name',
            'name_en',
            'detail:ntext',
            'size',
            'Photo',
            'thumbnail1',
            'thumbnail2',
            'thumbnail3',
            'thumbnail4',
            'thumbnail5',
            'online',
            'user_modify',
            'user_added',
            'comp_id',
            'date_added',
            'date_modify',
            'unit_cost',
            'sale_price',
            'qty_per_unit',
            'unit_of_measure',
            'clone',
            'status',
            'count_stock',
            'safety_stock',
            'reorder_point',
            'minimum_stock',
            'stock_adjust',
        ],
    ]) ?>

</div>
