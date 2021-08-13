<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SaleReturnHeader */

$this->title = Yii::t('common', 'Create Sale Return Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Return Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-return-header-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
