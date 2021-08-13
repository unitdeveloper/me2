<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Approval */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Approvals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="approval-view">

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
            'table_name',
            'field_name',
            'field_data',
            'source_id',
            'ip_address',
            'document_type',
            'sent_by',
            'sent_time',
            'approve_date',
            'approve_by',
            'comp_id',
            'approve_status',
            'gps',
            'balance',
        ],
    ]) ?>

</div>
