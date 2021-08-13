<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RuningNoseries */

$this->title = Yii::t('common', 'Create Runing Noseries');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Runing Noseries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="runing-noseries-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
