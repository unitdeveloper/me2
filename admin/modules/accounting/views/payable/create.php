<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ApInvoiceHeader */

$this->title = Yii::t('common', 'Create Ap Invoice Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Ap Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ap-invoice-header-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
