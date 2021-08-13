<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\WithholdingTax */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Withholding Taxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="withholding-tax-view">

    <h1><?=Yii::t('common','Number')?>  <?= Html::encode($model->no) ?></h1>

    <p>
        <?= Html::a('Home', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
        
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'customer_id',
            'customer_address',
            'vat_regis',
            'comp_id',
            'comp_address',
            'user_id',
            'user_name',
            'choice_substitute',
        ],
    ]) ?>

</div>
