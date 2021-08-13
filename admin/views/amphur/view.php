<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Amphur */

$this->title = $model->AMPHUR_NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Amphurs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="amphur-view" ng-init="Title='<?=$this->title;?>'">
 
 
    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->AMPHUR_ID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->AMPHUR_ID], [
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
            'AMPHUR_ID',
            'AMPHUR_CODE',
            'AMPHUR_NAME',
            'province.PROVINCE_NAME',
            'zone.name',
            
        ],
    ]) ?>

</div>
