<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Warehouse Header',
]) . $model->DocumentNo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="warehouse-header-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
