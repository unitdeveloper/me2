<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = $model->PROVINCE_NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Provinces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="province-view" ng-init="Title='<?=$this->title;?>'">


    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->PROVINCE_ID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->PROVINCE_ID], [
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
            'PROVINCE_ID',
            'PROVINCE_CODE',
            'PROVINCE_NAME',
            'zone.name',
            'subzone.zone',
            'latitude',
            'longitude',
            'countries.country_name',
        ],
    ]) ?>

</div>
