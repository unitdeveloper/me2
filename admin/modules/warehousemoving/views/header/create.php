<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = Yii::t('common', 'Create Warehouse Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="warehouse-header-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
