<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SetupSysMenu */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Setup Sys Menu',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Setup Sys Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="setup-sys-menu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
