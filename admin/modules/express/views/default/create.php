<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Isvat */

$this->title = Yii::t('common', 'Create Isvat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Isvats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="isvat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
