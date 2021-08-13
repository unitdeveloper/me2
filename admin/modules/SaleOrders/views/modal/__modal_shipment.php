<?php
use kartik\icons\Icon;
?>
<style type="text/css">
/*  .modal-dialog {
  width: 101%;
  height: 100%;
  margin: 0;
  padding: 0;
}

.modal-content {
  height: auto;
  min-height: 100%;
  border-radius: 0;
}*/
.loading{
  position: relative;
  background-image: url(images/icon/mini-loader.gif);
  float: left;
  margin: 3px;
  height: 12px;
  width: 12px;
}
.ew-undo-ship{
  color: red;
}
.ew-refresh-ship{
  color: green;
}
.ew-gen-ship{
  color: #00c0ef;
}
.datepicker
{
  border: 1px solid #ccc;
}
.datepicker-switch
{
  background-color: #ccc;
}
</style>
<!-- data-backdrop="static" -->
<div id="ewSaleShipModal" class="modal  modal-full fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Sale Shipment')?></h4>
      </div>
      <div class="Smooth-Ajax">
      <div class="modal-body" >
      <div class="SaleLine">
          <div class="row">
          <div class="col-sm-12 ew-ship-menu">
            <a href="javascript:void(0);" class="btn btn-app  pull-right ew-gen-ship" ew-order-no="<?=$model->no ?>" data-rippleria>
            <?= Icon::show('cube') ?> <?=Yii::t('common','Ship-packing')?></a>
            <?php //endif; ?>
            <div class="ew-inv-header">
              <div><?=Yii::t('common','Order No.')?> : <?=$model->no ?></div>
              <div class="ew-ship-renders" data-key="<?=$model->id?>" data-status="<?=$model->status?>"></div>
              <ew id="orderid" data="<?=$model->no ?>"></ew>
              <ew id="customerid" data="<?=$model->customer_id ?>"></ew>
              <ew id="commonAddress" data="0"></ew>  
            </div>
          </div>
        </div>
        <!-- Nav-->
        <div>
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
           <!--  <li role="presentation">
              <a href="#ShipGeneral" aria-controls="ShipGeneral" role="tab" data-toggle="tab">
              <?= Icon::show('file-o') ?>
              <?=Yii::t('common','General')?></a>
            </li> -->
            <!-- <li role="presentation"  class="active">
              <a href="#Shipment" aria-controls="Shipment" role="tab" data-toggle="tab">
              <?= Icon::show('cube') ?>
              <?=Yii::t('common','Packing')?></a>
            </li> -->
<!--
            <li role="presentation"><a href="#shipped" aria-controls="shipped" role="tab" data-toggle="tab"><?=Yii::t('common','Shipped')?></a></li>

            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
 -->
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <!-- <div role="tabpanel" class="tab-pane fade" id="ShipGeneral">
              <?php //echo $this->render('../saleinv/_transport',['dataProvider' => $dataProvider,'model' => $model]); ?>
            </div> -->
            <div role="tabpanel" class="tab-pane fade in active" id="Shipment">
              <div class="ew-ship-pagerender">
                <div class="loading"></div> Shipment Loading.....
              </div>
            </div>
          </div>
        </div>
        <!-- ./ Nav tabs -->
      </div>
      </div>
      <div class="modal-footer" style="z-index:2;">
        <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"  data-rippleria>
        <i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>
      </div>
      </div>
    </div>
  </div>
</div>

<!-- data-backdrop="static" -->
<div id="ew-modal-WarehouseHeader" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg" >
    <!-- Modal content-->
    <div class="modal-content"  style="background-color: rgb(250,250,250);">
      <div class="modal-header bg-green">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-truck" aria-hidden="true"></i> <?=Yii::t('common','Sale Shipment')?></h4>
      </div>
      <div class="modal-body" >
        <div class="ew-body-wh">
          {T-T}
        </div>
      </div>
      <div class="modal-footer" >
        <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal">
        <i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>
        <button type="button" name="Select" class="btn btn-warning-ew EditTransport">
        <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Edit')?></button>
      </div>
    </div>
  </div>
</div>



<!-- Right Click -->
<style type="text/css">
  #contextMenu {
    position: absolute;
    display:none;
    z-index: 3000;
  }
  .dropdown-menu{
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    background-color: rgb(250,250,250);
  }

  /*body.modal-open {
      overflow: hidden;
  }*/
</style>
<div id="contextMenu" class="dropdown clearfix" style="">
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px; ">
    <li>
      <a tabindex="-1" href="#" class="ew-inv-re-no" id="ew-rename-btn"><i class="fa fa-pencil text-warning" aria-hidden="true"></i> <?=Yii::t('common','Test Ship')?></a>
    </li>
    <li class="divider"></li>
    <li>
      <a tabindex="-1" href="index.php?r=accounting/saleinvoice/update&id=<?=$_GET['id']?>"><i class="fa fa-refresh text-success" aria-hidden="true"></i> <?=Yii::t('common','Refresh')?></a>
    </li>
  </ul>
</div>
