<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cheque */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Cheque',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Cheques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="cheque-update" ng-init="Title='<?=$this->title?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
