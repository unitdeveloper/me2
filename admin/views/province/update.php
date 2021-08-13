<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => '',
]) . $model->PROVINCE_NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Provinces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->PROVINCE_ID, 'url' => ['view', 'id' => $model->PROVINCE_ID]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="province-update" ng-init="Title='<?=$this->title;?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
