<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductionRequest */

$this->title = 'Update Production Request: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Production Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="production-request-update">

    <?= $this->render('_form_request', [
        'model' => $model,
        'id'    => $id, 
        'item'  => $item, 
        'logo'  => $logo,
        'qty'   => $qty,
        'no'    => $no
    ]); ?>

</div>
