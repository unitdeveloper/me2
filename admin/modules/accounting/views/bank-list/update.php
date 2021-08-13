<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BankList */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Bank List',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bank Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bank-list-update" ng-init="Title='<?=$this->title?>'">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
