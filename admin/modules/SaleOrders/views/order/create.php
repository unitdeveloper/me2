<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SaleLine */

$this->title = Yii::t('app', 'Create Sale Order');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
        <div class="panel-body">
			<div class="sale-line-create">

			     
			    <?= $this->render('_form', [
			        'model' => $model,
			        'SaleHeader' => $SaleHeader,
	                'searchModel' => $searchModel,
	                'dataProvider' => $dataProvider,
			    ]) ?>

			</div>
		</div>
	</div>
</div>
