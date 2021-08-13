<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WithholdingList */

$this->title = 'Create Withholding List';
$this->params['breadcrumbs'][] = ['label' => 'Withholding Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withholding-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
