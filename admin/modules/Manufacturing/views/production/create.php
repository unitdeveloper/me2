<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductionOrder */

$this->title = Yii::t('common', 'Create Production Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Production Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
