<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ItemJournal */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Item Journals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="item-journal-view">

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
            'line_no',
            'PostingDate',
            'DocumentDate',
            'TypeOfDocument',
            'SourceDocNo',
            'DocumentNo',
            'customer_id',
            'SourceDoc',
            'Description',
            'Quantity',
            'address',
            'address2',
            'district',
            'city',
            'province',
            'postcode',
            'contact',
            'phone',
            'gps:ntext',
            'update_date',
            'status',
            'user_id',
            'comp_id',
            'ship_to',
            'ship_date',
            'AdjustType',
            'ext_document',
        ],
    ]) ?>

</div>
