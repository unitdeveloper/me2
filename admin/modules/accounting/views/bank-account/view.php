<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BankAccount */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bank Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bank-account-view" ng-init="Title='<?=$this->title?>'">
 

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
            //'banklist.name',
            [
                'attribute' => 'bank',
                'value' => function($model){
                    return $model->banklist->name .' สาขา '.$model->branch;
                }
            ],
            'name',
            //'branch',
            'bank_no',
            
            [
                'attribute' => 'bank_type',
                'value' => function($model){
                    
                    if($model->bank_type==1) $model->bank_type = Yii::t('common','บัญชีออมทรัพย์');
                    if($model->bank_type==2) $model->bank_type = Yii::t('common','กระแสรายวัน');

                    return $model->bank_type;
                }
            ],
            'create_date:datetime',
            //'user_id',
            //'comp_id',
        ],
    ]) ?>

</div>
