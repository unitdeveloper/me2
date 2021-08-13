<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="location-view">

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
            'code',
            'name',
            'description',
            [
                'attribute' => 'defaultlocation',
                'label' => Yii::t('common','Default'),
                'format' => 'raw',
                'value' => function($model){
                    if($model->defaultlocation==1){
                        return '<span class="text-success"><i class="far fa-check-square"></i></span>';
                    }else if($model->defaultlocation==0){
                        return '<span class="text-gray"><i class="far fa-square"></i></span>';
                    
                    }else {
                        return 'Not Set';
                    }
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($model){
                    if($model->status==0){
                        return Yii::t('common','Disable');
                    }else if($model->status==1){
                        return Yii::t('common','Enable');
                    
                    }else {
                        return Yii::t('common','Block');
                    }
                }
            ],
        ],
    ]) ?>

</div>
