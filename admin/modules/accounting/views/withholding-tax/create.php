<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WithholdingTax */

$this->title = 'Create Withholding Tax';
$this->params['breadcrumbs'][] = ['label' => 'Withholding Taxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withholding-tax-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
