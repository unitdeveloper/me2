<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductionOrderLine */

$this->title = Yii::t('common', 'Create Production Order Line');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Production Order Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-order-line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
