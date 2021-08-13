<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SaleHeader */

$this->title = Yii::t('common','Sale Quotation');

function readOnly($foo,$model)
{
	if($foo == true){
		// แก้ได้เฉพาะ Sale Admin
		//if(Yii::$app->session->get('Rules')['rules_id'] == 3)
		return Yii::$app->response->redirect(Url::to(['/SaleOrders/quotation/view', 'id' => $model->id]));
	}
} 

 
$myRule       = Yii::$app->session->get('Rules');
$SalePeople   = $myRule['sale_id'];

// Sales = 3
// ไม่อนุญาตให้ดูใบงานที่ไม่ใช่ของตัวเอง 
if(in_array($myRule['rules_id'],['3'])){
  if($model->sale_id!=$SalePeople) return Yii::$app->response->redirect(Url::to(['/SaleOrders/quotation/index']));
}

if($model->status == 'Shiped')
{
	readOnly(true,$model);	
}else if($model->status == 'Invoiced'){
	// ถ้า Invoiced แก้ได้เฉพาะ admin ระบบ
	if(Yii::$app->session->get('Rules')['rules_id'] > 2)	readOnly(true,$model);	
}else if($model->status == 'Checking'){

	if(Yii::$app->session->get('Rules')['rules_id'] == 3)	readOnly(true,$model);
}

// ​​Set Default สำหรับ Sales เพื่อให้เข้าสู่ระบบการเก็บ ​tracking
if(Yii::$app->session->get('Rules')['rules_id'] == 3) $model->status = 'Open';



?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-header-update" ng-init="Title='<?= Html::encode($this->title) ?>'">
 
	<?php
	echo $this->render('_form', [
		'model' => $model,
		'dataProvider' => $dataProvider,
 
	]); 
	 ?>

</div>


