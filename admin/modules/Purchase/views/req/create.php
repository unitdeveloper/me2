<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PurchaseReqHeader */

$this->title = Yii::t('common', 'Create Purchase Req Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Purchase Req Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-req-header-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
