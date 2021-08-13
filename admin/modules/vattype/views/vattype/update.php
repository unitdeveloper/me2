<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VatType */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Vat Type',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Vat Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="vat-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
