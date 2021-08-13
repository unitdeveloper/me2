<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SaleHeader */

$this->title = $model->no;

function readOnly($foo,$model)
{
	if($foo == true){

		// แก้ได้เฉพาะ Sale Admin
		//if(Yii::$app->session->get('Rules')['rules_id'] == 3)
		return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/view', 'id' => $model->id]));
	}
} 


$myCompany    = Yii::$app->session->get('Rules')['comp_id'];
$myRule       = Yii::$app->session->get('Rules');
$SalePeople   = $myRule['sale_id'];

// Sales = 3
// ไม่อนุญาตให้ดูใบงานที่ไม่ใช่ของตัวเอง 
if(in_array($myRule['rules_id'],['3'])){
  if($model->sale_id!=$SalePeople) return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/index']));
}



if($model->status == 'Shiped')
{

	if(Yii::$app->session->get('Rules')['rules_id'] > 3)	readOnly(true,$model);	

}else if($model->status == 'Invoiced'){

	// ถ้า Invoiced แก้ได้เฉพาะ admin ระบบ
	if(Yii::$app->session->get('Rules')['rules_id'] > 2)	readOnly(true,$model);	
	//return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/view', 'id' => $model->id]));

}else if($model->status == 'Checking'){

	if(Yii::$app->session->get('Rules')['rules_id'] == 3)	readOnly(true,$model);
}

// ​​Set Default สำหรับ Sales เพื่อให้เข้าสู่ระบบการเก็บ ​tracking
if(Yii::$app->session->get('Rules')['rules_id'] == 3) $model->status = 'Open';







?>

<style>
    
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-header-update" ng-init="Title='<?= Html::encode($this->title) ?>'">
<?php
	
	// if(Yii::$app->session->get('theme')==0):  
	// 	$cookies = Yii::$app->request->cookies;	
	// 	if ($cookies->has('themeAlert')):
	// 		$cookieValue = $cookies->getValue('themeAlert');
	// 		// Close button clicked.
	// 	else: 
	// 		// Alert again.
	// 	echo '<div class="alert alert-info alert-dismissible">
	// 			<button type="button" class="close demis-theme-change" data-dismiss="alert" aria-hidden="true">×</button>
	// 			<h4><i class="icon fa fa-info"></i>New Theme!</h4>
	// 			เรามี Themes ในเวอร์ชันใหม่ให้ลองใช้งาน ซึ่งเหมาะสำหรับการใช้งานบนโทรศัพท์มือถือ
	// 			<a href="javascript:void(0)" class="btn btn-success btn-lg" data-id="1" id="change-theme" style="text-decoration: none;"><i class="fas fa-retweet"></i> ใช้เลย</a>
	// 		</div>';
		 
	// 	 endif;   
		 
	//  endif; 
?>
    

	<?php

	echo $this->render('__form', [
		'model' => $model,
		//'dataProvider' => $dataProvider,

	]);

	// if(Yii::$app->session->get('theme')==1){
	// 	echo $this->render('__form', [
	// 		'model' => $model,
	// 		'dataProvider' => $dataProvider,
	 
	// 	]);
	// }else{
	// 	echo $this->render('_form', [
	// 		'model' => $model,
	// 		'dataProvider' => $dataProvider,
	 
	// 	]);                 
	// }
	 ?>

</div>


