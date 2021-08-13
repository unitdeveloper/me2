<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BomLine */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Bom Line',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bom Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="bom-line-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
