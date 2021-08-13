<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WarehouseMoving */

$this->title = Yii::t('common', 'Create Warehouse Moving');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Movings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-moving-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
