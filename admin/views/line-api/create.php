<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LineBot */

$this->title = Yii::t('common', 'Create Line Bot');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Line Bots'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-bot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
