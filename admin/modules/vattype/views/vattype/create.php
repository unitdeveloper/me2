<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VatType */

$this->title = Yii::t('common', 'Create Vat Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Vat Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vat-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
