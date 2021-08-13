<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BankAccount */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Bank Account',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bank Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bank-account-update" ng-init="Title='<?=$this->title?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
