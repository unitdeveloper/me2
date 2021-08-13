<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemsHasProperty */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Items Has Property',
]) . $model->Items_No;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items Has Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Items_No, 'url' => ['view', 'Items_No' => $model->Items_No, 'property_id' => $model->property_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="items-has-property-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
