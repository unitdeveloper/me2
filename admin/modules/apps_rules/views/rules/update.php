<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AppsRules */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Apps Rules',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Apps Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="apps-rules-update">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
