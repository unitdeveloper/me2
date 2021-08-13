<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SaleEventHeader */

$this->title = Yii::t('common', 'Create Sale Event Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Event Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-event-header-create" ng-init="Title='<?=$this->title?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
