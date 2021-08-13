<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\District */

$this->title = $model->DISTRICT_ID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Districts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="district-view" ng-init="Title='<?=$this->title;?>'">

 
 
    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->DISTRICT_ID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->DISTRICT_ID], [
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
            'DISTRICT_ID',
            'DISTRICT_CODE',
            'DISTRICT_NAME',
            'city.AMPHUR_NAME',
            'province.PROVINCE_NAME',
            'zone.name',
        ],
    ]) ?>

</div>
