<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = $model->Description;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-header-view">

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
            //'DocumentDate',
            //'TypeOfDocument',
            //'SourceDocNo',
            //'DocumentNo',
            [
                'attribute' => 'customer_id',
                'label' => Yii::t('common','Customer'),
                'value' => function($model){
                    return $model->customer->name;
                }
            ],
            'SourceDoc',
            [
                'attribute' => 'Description',
                'label' => Yii::t('common','Transport'),
                'value' => function($model){
                    return $model->Description;
                }
            ],
            //'Quantity',
            'address',
            //'address2',
            'districttb.DISTRICT_NAME',
            'citytb.AMPHUR_NAME',
            'provincetb.PROVINCE_NAME',
            'postcode',
            'contact',
            'phone',
            //'gps:ntext',
            //'update_date',
            //'status',
            //'user_id',
            //'comp_id',
            //'ship_to',
            'ship_date',
            //'AdjustType',
        ],
    ]) ?>

</div>
