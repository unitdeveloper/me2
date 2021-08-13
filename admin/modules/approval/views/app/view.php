<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Approval */

$this->title = Yii::t('common','Approved');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Approvals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="approval-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>
        <a href="#" class="btn btn-primary update-btn" data="<?=$model->id?>"><?=Yii::t('common','Update')?></a>
        <a href="#" class="btn btn-danger delete-btn" data="<?=$model->id?>"><?=Yii::t('common','Delete')?></a>
    </p> -->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'table_name',
            //'field_name',
            //'field_data',
            [
                'label' => Yii::t('common','Data'),
                'value' => function($model){
                    return $model->field_data;
                }
            ],
            'source_id',
            'ip_address',
            'document_type',
            'sentby.name',
            'sent_time',
            'approve_date',
            'approveby.name',
            //'comp_id',
            //'approve_status',
            //'gps',
        ],
    ]) ?>

</div>