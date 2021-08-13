<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model common\models\Cheque */

$this->title = $model->banklist->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Cheques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="cheque-view" ng-init="Title='<?=$this->title?>'">

<div class="panel panel-success">
    <div class="panel-heading">
        <span class="pull-right">
            <?= Html::a('<i class="fa fa-print"></i> '.Yii::t('common', 'Print'), '#', ['class' => 'btn btn-warning-ew btn-xs', 'style' => 'margin:-5px -8px 0px 0px;']) ?>
            <?php /* Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs', 'style' => 'margin:-5px -8px 0px 0px;'])*/ ?>
        </span>
        <h3 class="panel-title"><?= $model->banklist->name ?></h3>         
    </div>
    <div class="panel-body">
        <div><h4>ได้รับเงินจาก : <?=$model->customer->name;?></h4></div>
        <div></div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],             
                [
                    'label' => Yii::t('common','List'),
                    'contentOptions' => ['class' => 'font-roboto'],   
                    'headerOptions' => ['class' => ' '],   
                    'format' => 'raw',
                    'value' => function($model){
                        return Html::a($model->no_,['/accounting/posted/read-only', 'id' => $model->id],['target' => '_blank']);
                    }
                ],
                [
                    'label' => Yii::t('common','Balance'),
                    'contentOptions' => ['class' => 'text-right'],   
                    'headerOptions' => ['class' => 'text-right'],   
                    'value' => function($model){
                        return number_format($model->sumtotals->total,2);
                    }
                ]          
            ],
        ]); ?>
    </div>
    <div class="panel-footer">
        <?=Yii::t('common','Remark')?>: <?= $model->remark;?>
    </div>
</div>

 
<?php Pjax::begin(['id' => 'pjax-container']) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'bank',
            'bank_account',
            'bank_branch',
            'bank_id',
            'create_date',
            'posting_date',
            'tranfer_to',

            'balance',
            'post_date_cheque',
            'apply_to',
            'transfer_time',
        ],
    ]) ?>
<?php Pjax::end() ?>


</div>
​