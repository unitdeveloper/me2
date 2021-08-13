<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ViewInvoiceLine */

$this->title = Yii::t('common', 'Update View Invoice Line: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'View Invoice Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'posted' => $model->posted]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="view-invoice-line-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
