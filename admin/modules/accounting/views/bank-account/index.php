<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\BankAccount */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Bank Accounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bank-account-index" ng-init="Title='<?=$this->title?>'">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'banklist.name',
            'name',
            'branch',
            'bank_no',
            //'create_date',
            // 'user_id',
            // 'comp_id',
            [
                'attribute' => 'bank_type',
                'value' => function($model){
                    
                    if($model->bank_type==1) $model->bank_type = Yii::t('common','บัญชีออมทรัพย์');
                    if($model->bank_type==2) $model->bank_type = Yii::t('common','กระแสรายวัน');

                    return $model->bank_type;
                }
            ],

            [
                'contentOptions' => ['class' => 'text-right'],
                'class' => 'yii\grid\ActionColumn',
                'options'=>['style'=>'width:150px;'],
                'buttonOptions'=>['class'=>'btn btn-default'],
                'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
            ],

        ],
    ]); ?>
</div>

<a class="btn btn-info-ew" href="index.php?r=accounting/bank-list/index"><i class="fa fa-book" aria-hidden="true"></i> <?=Yii::t('common','Bank List');?></a>