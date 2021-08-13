<?php
use kartik\icons\Icon;

use yii\helpers\Url;
use admin\modules\apps_rules\models\SysRuleModels;

$session = Yii::$app->session;
$Actions = Yii::$app->controller->action->actionMethod;
$session->set('Method',$Actions);


# actionIndex,actionView, actionUpdate, actionCreate
switch ($Actions) {
  case 'actionUpdate':
  $editUrl  = Url::toRoute(['update','id' => $_GET['id']]);
  $printUrl  = Url::toRoute(['print-page','footer' => '1','id' => $_GET['id']]);
  $btnSave = 'onclick="$(\'form\').submit();"';
  $btnEdit = 'style="visibility:hidden; display: none;"';
  $btnPrint = 'style="visibility:hidden; display: none;"';
  break;
  case 'actionCreate':
  $editUrl = '#';
  $printUrl = '#';
  $btnSave = 'onclick="$(\'form\').submit();"';
  $btnEdit = 'style="visibility:hidden;"';
  $btnPrint = 'style="visibility:hidden; display: none;"';
  break;
  case 'actionView':
  $editUrl  = Url::toRoute(['update','id' => $_GET['id']]);
  $printUrl  = Url::toRoute(['print-page','footer' => '1','id' => $_GET['id']]);
  $btnSave = 'style="visibility:hidden; display: none;"';
  $btnEdit = NULL;
  $btnPrint = NULL;
  break;

  default:
  $editUrl = '#';
  $printUrl = '#';
  $btnSave = 'style="visibility:hidden; display: none;"';
  $btnEdit = 'style="visibility:hidden; display: none;"';
  $btnPrint = 'style="visibility:hidden; display: none;"';
  break;
}

?>

<div class="row menu-widget" style="background-color: #FFF; padding-top:15px; margin-top:-20px;">
  <div class="col-xs-8 " style="width: 100%;">
    <a class="btn btn-app" href="<?= Url::toRoute(['index']) ?>">
      <i class="fa fa-home"></i><?= Yii::t('common', 'Over View') ?>
    </a>

    <div class="pull-right ">
      <a href="#" target="_blank" class="btn btn-app btn-info ewSaleShipModal" data-toggle="modal" data-target="#ewSaleShipModal">
        <?= Icon::show('cubes') ?><?= Yii::t('common', 'Packing') ?>
      </a>
      <!-- ewSaleShipModal -->
      <?php echo  $this->render('../../../SaleOrders/views/modal/__modal_shipment',['dataProvider' => $dataProvider,'model' => $model]) ?>
    </div>
  </div>
</div>
