<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ChartOfAccount */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Chart Of Account',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Chart Of Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="chart-of-account-update" ng-init="Title='<?=$this->title?>'">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
