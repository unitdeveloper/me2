<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SalesPeople */

$this->title = Yii::t('common', 'Update') .' '. $model->name.' '.$model->surname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales Peoples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sales-people-update" ng-init="Title='<?= Html::encode($this->title) ?>'">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
