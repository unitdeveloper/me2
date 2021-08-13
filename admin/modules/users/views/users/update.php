<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'User',
]) .($model->profile ? $model->profile->name : '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="user-update" ng-init="Title='<?= Html::encode($this->title) ?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
        'profile' => $profile,
        'appRule' => $appRule
    ]) ?>

</div>
