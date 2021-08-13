<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\unitofmeasure */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Unitofmeasure',
]) . $model->UnitCode;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Unitofmeasures'), 'url' => ['index']];
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="unitofmeasure-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
