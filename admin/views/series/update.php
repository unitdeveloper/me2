<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NumberSeries */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Number Series',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Number Series'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="number-series-update" ng-init="Title='<?=$this->title?>'">

   

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
