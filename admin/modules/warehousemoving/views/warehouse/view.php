<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseMoving */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Movings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-moving-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
         
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'line_no',
            'DocumentNo',
            'PostingDate',
            'TypeOfDocument',
            'SourceDoc',
            'SourceDocNo',
            'ItemNo',
            'Description',
            'Quantity',
            'QtyToMove',
            'QtyMoved',
            'QtyOutstanding',
            'DocumentDate',
        ],
    ]) ?>

</div>
