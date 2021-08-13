<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApInvoiceHeader */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Ap Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ap-invoice-header-view">

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
            'posting_date',
            'no_',
            'ref_inv_header',
            'cust_no_',
            'cust_name_',
            'cust_address:ntext',
            'cust_address2:ntext',
            'order_date',
            'ship_date',
            'cust_code',
            'sales_people',
            'sale_id',
            'document_no_',
            'doc_type',
            'district',
            'city',
            'province',
            'postcode',
            'user_id',
            'comp_id',
            'contact',
            'phone',
            'discount',
            'percent_discount',
            'vat_percent',
            'payment_term',
            'paymentdue',
            'ext_document',
            'include_vat',
            'remark:ntext',
            'session_id',
            'order_id',
            'status',
            'taxid',
            'branch',
            'branch_name',
            'cn_reference',
            'revenue',
            'rf_revenue',
        ],
    ]) ?>

</div>
