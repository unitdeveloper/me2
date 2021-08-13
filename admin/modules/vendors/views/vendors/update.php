<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Vendors */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => ' ',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="vendors-update" ng-init="Title='<?=$this->title?>'">
  <?php if(Yii::$app->session->hasFlash('alert')):?>
      <?= \yii\bootstrap\Alert::widget([
      'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
      'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
      ])?>
  <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
