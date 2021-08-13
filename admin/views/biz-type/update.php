<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CommonBusinessType */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Common Business Type',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Common Business Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="common-business-type-update" ng-init="Title='<?=$this->title?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
