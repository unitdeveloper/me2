<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SetupNoSeries */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Setup No Series',
]) . $model->id;
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="setup-no-series-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
