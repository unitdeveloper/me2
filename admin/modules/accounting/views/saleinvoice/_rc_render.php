<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sale Invoice Headers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-invoice-header-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'no_',
                'label' => Yii::t('common','Document No.'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->no_,['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank']);
                }
            ],
            [
                'attribute' => 'cust_no_',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->cust_name_,['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank']);
                }
            ],
            'cust_address:ntext',
            [
                
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function($model){
                    return number_format($model->sumtotals->total,2);
                }
            ],
        ],
    ]); ?>
</div>

 