<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = $model->DocumentNo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="warehouse-header-view" ng-init="Title='<?= Html::encode($this->title) ?>'">
 

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
            //'TypeOfDocument',
            [
                'label' => Yii::t('common','Type Of Document'),
                'value' => function($model){
                    return $model->TypeOfDocument.' ('.$model->AdjustType.')';
                }
            ],
            'SourceDocNo',
            'DocumentNo',
            'customer_id',
            'SourceDoc',
            'Description',
            'Quantity',
            //'address',
            //'address2',
            //'district',
            //'city',
            //'province',
            //'postcode',
            //'contact',
            //'phone',
            //'gps:ntext',
            'update_date',
            //'status',
            //'user_id',
            //'comp_id',
            //'ship_to',
            //'ship_date',
        ],
    ]) ?>

</div>
