<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = Yii::t('common', 'Item Journal {modelClass}: ', [
    'modelClass' => '',
]) . $model->DocumentNo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Adjust');
?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
<div class="warehouse-header-update" ng-init="Title='<?= Html::encode($this->title) ?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
