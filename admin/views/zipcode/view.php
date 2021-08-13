<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Zipcode */

$this->title = $model->ZIPCODE_ID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Zipcodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="zipcode-view" ng-init="Title='<?=$this->title;?>'">
 

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->ZIPCODE_ID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->ZIPCODE_ID], [
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
            'ZIPCODE_ID',
            'DISTRICT_CODE',
            'PROVINCE_ID',
            'AMPHUR_ID',
            'DISTRICT_ID',
            'ZIPCODE',
            'latitude',
            'longitude',
        ],
    ]) ?>

</div>
