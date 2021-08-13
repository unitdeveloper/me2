<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TransportList */

$this->title = Yii::t('common', 'Create Transport List');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Transport Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-list-create">
<div class="">
    <?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
</div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
