<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PrintPage */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Print Page',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Print Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="print-page-update" ng-init="Title='<?=$this->title?>'">

     

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
