<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SaleGroup */

$this->title = Yii::t('common', 'Create Sale Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
