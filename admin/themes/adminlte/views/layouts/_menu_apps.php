<?php
use yii\helpers\Url;
use admin\modules\apps_rules\models\SysRuleModels;
$session = Yii::$app->session;
$Actions = Yii::$app->controller->action->actionMethod;
$session->set('Method',$Actions);
# actionIndex,actionView, actionUpdate, actionCreate
switch ($Actions) {
	case 'actionUpdate':
		$editUrl	= Url::toRoute(['update','id' => $_GET['id']]);
		$printUrl	= Url::toRoute(['print','id' => $_GET['id']]);
		$btnSave	= 'onclick="$(\'form\').submit();"';
		$btnEdit	= 'style="visibility:hidden; display: none;"';
		$btnDel		= NULL;
		$btnPrint	= 'style="visibility:hidden; display: none;"';
		break;
	case 'actionCreate':
		$editUrl 	= '#';
		$printUrl 	= '#';
		$btnSave	= 'onclick="$(\'form\').submit();"';
		$btnEdit	= 'style="visibility:hidden;"';
		$btnDel		= NULL;
		$btnPrint	= 'style="visibility:hidden; display: none;"';
		break;
	case 'actionView':
		$editUrl	= Url::toRoute(['update','id' => $_GET['id']]);
		$printUrl	= Url::toRoute(['print','id' => $_GET['id']]);
		$btnSave	= 'style="visibility:hidden; display: none;"';
		$btnEdit	= NULL;
		$btnDel		= NULL;
		$btnPrint	= NULL;
		break;
	default:
		$editUrl	= '#';
		$printUrl	= '#';
		$btnSave	= 'style="visibility:hidden; display: none;"';
		$btnEdit	= 'style="visibility:hidden; display: none;"';
		$btnDel		= 'style="visibility:hidden; display: none;"';
		$btnPrint	= 'style="visibility:hidden; display: none;"';
		break;
}
// var_dump(Yii::$app->controller->Route);
// var_dump(Yii::$app->user->can('admin','author'));
// var_dump(Yii::$app->user->can('customers/customer/index'));
// var_dump(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
// echo '<hr>';
// var_dump(\Yii::$app->authManager->getAssignments(Yii::$app->user->getId()));
// echo '<hr>';
$Rules = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
$ThisRule = array();
foreach ($Rules as $key => $value) {
	$ThisRule[] = $value;
}

function validateRoute(){
	$myRoute = \Yii::$app->controller->Route;
	$R = explode('/', $myRoute);
	$data[0] = '/'.$R[0].'/'.$R[1].'/'.'update';
	$data[1] = '/'.$R[0].'/'.$R[1].'/'.'*';
	//$data[2] = '/'.$R[0].'/'.'*';
	return $data;
}
// if($models->)
$Permission = \common\models\AuthItemChild::find()->where(['child' => validateRoute()])->orderBy(['child' =>SORT_ASC])->all();
foreach ($Permission as $key => $route) {
	if(in_array($route->parent, $ThisRule)){
		//var_dump('Allow Edit');
	}else {
		// var_dump('Not Allow Edit => '.$route->parent);
		// var_dump(validateRoute());
	}
	//echo $route->parent.'<br>';
}

?>
<div class="row menu-widget">
	<div class="col-xs-9 col-sm-6 left-menu-widget">
		<a class="btn btn-app ew-bt-app-home" href="<?= Url::toRoute(['index']) ?>" data-rippleria>
			<i class="fa fa-home"></i><?= Yii::t('common', 'Over View') ?>
		</a>
		<?php
			//
			// Permission & Rules
			//
		$QUERY_STRING  = substr("$_SERVER[QUERY_STRING]",2);

		?>
			<a class="btn btn-app ew-bt-app-new" href="<?= Url::toRoute(['create']) ?>" data-rippleria>
				<span class="badge bg-green new-doc"></span>
				<i class="glyphicon glyphicon-plus text-success"></i> <?= Yii::t('common', 'Create Doc') ?>
			</a>
		<?php  // <-- End ---- Permission & Rules ---- ?>
		<?php if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionCreate','Create-Edit-Customer'))): ?>
		<?php //if(Yii::$app->session->get('Rules')['rules_id'] == 1 || Yii::$app->session->get('Rules')['rules_id'] == 2 || Yii::$app->session->get('Rules')['rules_id'] == 4): ?>
			<a class="btn btn-app ew-bt-app-edit" href="<?= $editUrl?>" <?=$btnEdit;?>  data-rippleria>
				<i class="fa fa-edit text-warning"></i> <?= Yii::t('common', 'Edit') ?>
			</a>
			<a class="btn btn-app  ew-delete-common" style="display:none;" href="#"  data-rippleria>
				<span class="badge bg-orange"></span>
				<i class="fa fa-trash text-danger" ></i> <?= Yii::t('common', 'Delete') ?>
			</a>
		<?php endif; ?>
		
	</div>
	<div class="col-xs-3 col-sm-6 right-menu-widget">
		<div class="box-tools pull-right ">
			<a class="btn btn-app  ew-save-common" href="#"  style="display:none;" data-rippleria>
				<span class="badge bg-info"></span>
				<i class="fa fa-save text-primary" ></i> <?= Yii::t('common', 'Save') ?>
			</a>			
			<?php if(Yii::$app->session->get('Rules')['rules_id']!=4): ?>
			<a class="btn btn-app  btn-app-print" href="<?= $printUrl?>" target='_blank' <?=$btnPrint;?> data-rippleria>
				<span class="badge bg-green"></span>
				<i class="fa fa-print text-aqua" ></i> <?= Yii::t('common', 'Print') ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</div>
