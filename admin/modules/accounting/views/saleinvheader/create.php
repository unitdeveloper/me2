<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */

$this->title = Yii::t('common', 'Create Sale Invoice Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-invoice-header-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
