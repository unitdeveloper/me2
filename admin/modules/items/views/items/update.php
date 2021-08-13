<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Items */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Items',
]) . $model->master_code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->No, 'url' => ['view', 'id' => $model->No]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<?= $this->render('_explorer_bar',['model' => $model]) ?>
 
<div class="row" ng-init="Title='<?=Yii::t('common','Product')?>'">	
	<div class="col-sm-12">
		<div class="items-create" >		     
		    <?= $this->render('_form', [
		        'model' => $model,
		        'Proper' => $Proper,		 
		    ]) ?>
		</div>
	</div>
</div>
 <?= $this->render('_script_js') ?>