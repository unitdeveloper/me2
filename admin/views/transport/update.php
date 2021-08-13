<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TransportList */

$this->title = Yii::t('common', 'Update Transport List: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Transport Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="transport-list-update">
<div class="">
    <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
</div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
