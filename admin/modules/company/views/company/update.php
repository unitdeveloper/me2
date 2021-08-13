<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="company-update" ng-init="Title='<?= Html::encode($this->title) ?>'">
	

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

     

</div>
