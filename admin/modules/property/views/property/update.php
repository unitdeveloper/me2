<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Property */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Property',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Properties'), 'url' => ['index']];
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="property-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
