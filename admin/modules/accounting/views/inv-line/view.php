<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ViewInvoiceLine */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'View Invoice Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="view-invoice-line-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id, 'posted' => $model->posted], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id, 'posted' => $model->posted], [
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
            'type',
            'source_id',
            'item',
            'doc_no_',
            'line_no_',
            'customer_no_',
            'code_no_',
            'code_desc_',
            'quantity',
            'unit_price',
            'vat_percent',
            'line_discount',
            'order_id',
            'source_doc',
            'source_line',
            'status',
            'session_id',
            'cn_reference',
            'posted',
        ],
    ]) ?>

</div>
