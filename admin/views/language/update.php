<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SourceMessage */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Source Message',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Source Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="source-message-update" ng-init="Title='<?= Html::encode($this->title) ?>'">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
