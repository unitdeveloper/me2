<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ModuleApp */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Module App',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Module Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="module-app-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
