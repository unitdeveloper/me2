<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Itemgroup */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Itemgroup',
]) . $model->GroupID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Itemgroups'), 'url' => ['index']];
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="itemgroup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
