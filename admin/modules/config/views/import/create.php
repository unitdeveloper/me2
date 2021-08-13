<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ImportFile */

$this->title = Yii::t('common', 'Create Import File');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Import Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-file-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
