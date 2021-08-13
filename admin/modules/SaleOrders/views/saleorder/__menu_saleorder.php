<?php

use yii\helpers\Html;
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
    $printUrl = Url::toRoute(['print','footer' => '1','id' => $_GET['id']]);
    $btnSave  = 'onclick="$(\'form\').submit();"';
    $btnEdit  = 'style="visibility:hidden; display: none;"';
    $btnDel		= NULL;
    $btnPrint = 'style="visibility:hidden; display: none;"';
    break;
  case 'actionCreate':
    $editUrl  = '#';
    $printUrl = '#';
    $btnSave  = 'onclick="$(\'form\').submit();"';
    $btnEdit  = 'style="visibility:hidden;"';
    $btnDel		= NULL;
    $btnPrint = 'style="visibility:hidden; display: none;"';
    break;
  case 'actionView':
    $editUrl  = Url::toRoute(['update','id' => $_GET['id']]);
    $printUrl = Url::toRoute(['print','footer' => '1','id' => $_GET['id']]);
    $btnSave  = 'style="visibility:hidden; display: none;"';
    $btnEdit  = NULL;
    $btnDel		= NULL;
    $btnPrint = NULL;
    break;

  default:
    $editUrl  = '#';
    $printUrl = '#';
    $btnSave  = 'style="visibility:hidden; display: none;"';
    $btnEdit  = 'style="visibility:hidden; display: none;"';
    $btnDel		= 'style="visibility:hidden; display: none;"';
    $btnPrint = 'style="visibility:hidden; display: none;"';
    break;
}


?>

<div class="row menu-widget" style="background-color: #FFF; padding-top:15px; margin-top:-20px;">
  <div class="col-xs-8 " style="width: 100%;">
    <a class="btn btn-app" href="<?= Url::toRoute(['index']) ?>" data-rippleria>
      <i class="fa fa-home"></i><?= Yii::t('common', 'Over View') ?>
    </a>

    <!-- Edit Button -->
    <?php if(in_array($model->status, ['Open', 'Release', 'Reject', 'Checking', 'Cancel', 'Shiped'])): ?>
    <?php //if($model->status == 'Open' || $model->status == 'Release' || $model->status == 'Reject' || $model->status == 'Checking' || $model->status == 'Cancel'): ?>

      <?php if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))): ?>
      <?php //if(Yii::$app->session->get('Rules')['rules_id']==4): ?>

        <a class="btn btn-app" href="<?= $editUrl?>" <?=$btnEdit;?> data-rippleria>
          <i class="fa fa-edit text-warning"></i> <?= Yii::t('common', 'Edit') ?>
        </a>
        <?php else: ?>

        <?php if(in_array($model->status, ['Open','Release','Reject','Cancel','Shiped'])): ?>
          <a class="btn btn-app" href="<?= $editUrl?>" <?=$btnEdit;?> data-rippleria>
            <i class="fa fa-edit text-warning"></i> <?= Yii::t('common', 'Edit') ?>
          </a>
        <?php endif; ?>

      <?php endif; ?>


    <?php endif; ?>
    <!-- // END Edit Button -->

    <a class="btn btn-app" href="<?= Url::toRoute(['create']) ?>" data-rippleria>
      <span class="badge bg-green new-doc"></span>
      <i class="glyphicon glyphicon-plus text-success"></i> <?= Yii::t('common', 'Create Doc') ?>
    </a>

    <?= Html::a('<span class="badge"></span><i class="fa fa-trash" ></i> '.Yii::t('common', 'Delete'), 
                  'javascript::void(0)', [
                        'class' => 'btn btn-app ew-delete-order bg-red',                         
                        'data-rippleria' => true    ,
                        'data-id' => $model->id                   
                  ]) ?>







    <div class="pull-right ">
      <a class="btn btn-app  ew-save-common" href="#" <?=$btnSave;?> data-rippleria>
        <span class="badge bg-info"></span>
        <i class="fa fa-save text-primary" ></i> <?= Yii::t('common', 'Save') ?>
      </a>
 
      <!-- For Sales -->
      <?php /* if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalehearderSearch','view'))): ?>
        <?php if(in_array($model->status,['Release', 'Invoiced', 'Shiped', 'Checking'])): ?>
          <a class="btn btn-app  " href="<?= $printUrl?>" target='_blank' <?=$btnPrint;?> data-rippleria>
            <span class="badge bg-green"></span>
            <i class="fa fa-print text-aqua" ></i> <?= Yii::t('common', 'Print') ?>
          </a>
        <?php endif; ?>
      <?php endif; */ ?>
      <!-- /. For Sales -->

      <!-- For Sale Admin -->
      <?php if(in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))): ?>
      <?php //if(Yii::$app->session->get('Rules')['rules_id'] != 3 ): ?>

          <?php if($model->status != 'Open'): ?>
            <a href="index.php?r=SaleOrders/saleorder/print&id=<?=$_GET['id']?>&footer=1" target="_blank" class="btn btn-app bg-aqua" data-rippleria>
            <?= Icon::show('print') ?><?= Yii::t('common', 'Print Sale Order') ?>  </a>
          <?php endif; ?>


          <?php if(in_array($model->status,['Shiped','Checking','Invoiced', 'Credit-Note'])) : ?>
            <i class="fa fa-arrow-right" aria-hidden="true"></i>
            <a href="#" target="_blank" class="btn btn-app bg-blue ewSaleShipModal" data-toggle="modal" data-target="#ewSaleShipModal" data-rippleria>
              <?= Icon::show('cubes') ?><?= Yii::t('common', 'Packing') ?> </a>

            <!-- ewSaleShipModal -->
            <?php echo  $this->render('../modal/__modal_shipment',['dataProvider' => $dataProvider,'model' => $model]) ?>
          <?php endif; ?>


          <?php if(in_array($model->status,['Checking','Shiped','Invoiced', 'Credit-Note'])): ?>
            <i class="fa fa-arrow-right" aria-hidden="true"></i>
            <a href="#" class="btn btn-app bg-orange ewSaleInvoiceModal" data-toggle="modal" data-target="#ewSaleInvoiceModal" data-rippleria>
              <i class="fa fa-file-text-o" aria-hidden="true"></i>
              <?= Yii::t('common', 'Bill') ?>
            </a>
            <!-- ewSaleInvoiceModal -->
            <?php echo  $this->render('../modal/__modal_sale_invoice',['dataProvider' => $dataProvider,'model' => $model] ) ?>
          <?php endif; ?>

        <?php else: ?>

          <?php if(in_array($model->status,['Release', 'Invoiced', 'Shiped', 'Checking'])): ?>
            <a class="btn btn-app  " href="<?= $printUrl?>" target='_blank' <?=$btnPrint;?> data-rippleria>
              <span class="badge bg-green"></span>
              <i class="fa fa-print text-aqua" ></i> <?= Yii::t('common', 'Print') ?>
            </a>
          <?php endif; ?>

      <?php endif; ?>
      <!-- /. For Sale Admin -->



    </div>
  </div>
</div>
