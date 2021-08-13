<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ViewInvoiceLine */

$this->title = Yii::t('common', 'Create View Invoice Line');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'View Invoice Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="view-invoice-line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
