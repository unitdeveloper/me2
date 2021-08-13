<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceLine */

$this->title = Yii::t('common', 'Create Sale Invoice Line');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-invoice-line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
