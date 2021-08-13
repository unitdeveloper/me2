<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\District */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'District',
]) . $model->DISTRICT_ID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Districts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->DISTRICT_ID, 'url' => ['view', 'id' => $model->DISTRICT_ID]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="district-update" ng-init="Title='<?=$this->title;?>'">
 

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
