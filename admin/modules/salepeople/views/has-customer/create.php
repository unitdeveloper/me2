<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SalesHasCustomer */

$this->title = Yii::t('common', 'Create Sales Has Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales Has Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-has-customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
