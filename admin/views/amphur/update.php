<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Amphur */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Amphur',
]) . $model->AMPHUR_NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Amphurs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->AMPHUR_ID, 'url' => ['view', 'id' => $model->AMPHUR_ID]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="amphur-update" ng-init="Title='<?=$this->title;?>'">
 

     
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
