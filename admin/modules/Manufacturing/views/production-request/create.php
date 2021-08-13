<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductionRequest */

$this->title = 'Create Production Request';
$this->params['breadcrumbs'][] = ['label' => 'Production Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-request-create">

    <?= $this->render('_form_request', [
        'model' => $model,
        'id'    => $id, 
        'item'  => $item, 
        'logo'  => $logo,
        'qty'   => $qty,
        'no'    => $no
    ]); ?>

</div>
