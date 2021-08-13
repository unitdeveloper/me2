<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseMoving */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Warehouse Moving',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Movings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="warehouse-moving-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
