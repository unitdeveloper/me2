<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SaleEventHeader */

$this->title = $model->no;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Event Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-event-header-view" ng-init="Title='<?=$this->title?>'">

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'no',
            'customer_id',
            'sale_address',
            'bill_address',
            'ship_address',
            'order_date',
            'ship_date',
            'balance',
            'balance_befor_vat',
            'discount',
            'percent_discount',
            'status',
            'create_date',
            'user_id',
            'comp_id',
            'paymentdue',
            'sales_people',
            'sale_id',
            'vat_percent',
            'ext_document',
            'payment_term',
            'vat_type',
            'remark:ntext',
            'transport',
            'reason_reject:ntext',
            'update_by',
            'update_date',
            'include_vat',
            'sourcedoc',
            'completeship',
        ],
    ]) ?>

</div>
