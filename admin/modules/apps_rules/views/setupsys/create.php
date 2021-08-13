<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SetupSysMenu */

$this->title = Yii::t('common', 'Create Setup Sys Menu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Setup Sys Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setup-sys-menu-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
