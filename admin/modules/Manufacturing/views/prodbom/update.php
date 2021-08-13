<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BomHeader */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Bom Header',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bom Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bom-header-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
