<?php
use yii\helpers\Html;
 
function getYearsList() {
	$currentYear = date('Y');
	$yearFrom = 2017;
	$yearsRange = range($yearFrom, $currentYear);
	return array_combine($yearsRange, $yearsRange);
}
 

?>
<style>
	h3{
		font-family: "Roboto","Times New Roman", Times, serif !important;
	}
	#weekly-sign,
	#daily-sign{
		margin-right: -5px;
		margin-left: 5px;
	}


	.lds-ellipsis {
		display: inline-block;
		position: relative;
		width: 64px;
		height: 64px;
	}
	.lds-ellipsis div {
		position: absolute;
		top: 27px;
		width: 11px;
		height: 11px;
		border-radius: 50%;
		background: #ccc;
		animation-timing-function: cubic-bezier(0, 1, 1, 0);
	}
	.lds-ellipsis div:nth-child(1) {
		left: 6px;
		animation: lds-ellipsis1 0.6s infinite;
	}
	.lds-ellipsis div:nth-child(2) {
		left: 6px;
		animation: lds-ellipsis2 0.6s infinite;
	}
	.lds-ellipsis div:nth-child(3) {
		left: 26px;
		animation: lds-ellipsis2 0.6s infinite;
	}
	.lds-ellipsis div:nth-child(4) {
		left: 45px;
		animation: lds-ellipsis3 0.6s infinite;
	}
	@keyframes lds-ellipsis1 {
		0% {
			transform: scale(0);
		}
		100% {
			transform: scale(1);
		}
	}
	@keyframes lds-ellipsis3 {
		0% {
			transform: scale(1);
		}
		100% {
			transform: scale(0);
		}
	}
	@keyframes lds-ellipsis2 {
		0% {
			transform: translate(0, 0);
		}
		100% {
			transform: translate(19px, 0);
		}
	}
</style>


<!-- Small boxes (Stat box) -->
<div class="row" ng-init="Title='<?=Yii::$app->session->get('brand')? Yii::$app->session->get('brand') : Yii::$app->session->get('company')?>'" style="margin-bottom: <?=$space?>;">

  <a href="index.php?SalehearderSearch[no]=&SalehearderSearch[status]=Release&r=SaleOrders/saleorder" >
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box box-ewRed">
        <div class="inner ">
          <h3 class="ew-new-orders-count"><i class="fas fa-sync-alt fa-spin" aria-hidden="true"></i> </h3>
          <p><?=Yii::t('common','Waiting Process') ?></p>
        </p>
        </div>
        <div class="icon">
          <i class="fas fa-coffee"  style="font-size:70px;"></i>
        </div>
        <div class="small-box-footer"><?=Yii::t('common','Detail') ?>  <i class="fa fa-arrow-circle-right"></i></div>
      </div>
    </div>
  </a>
  <!-- ./col -->

  <a href="index.php?SalehearderSearch[no]=&SalehearderSearch[status]=Checking&r=SaleOrders/saleorder">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box box-ewYellow">
        <div class="inner">
          <h3 class="ew-checking-count"><i class="fas fa-sync-alt fa-spin" aria-hidden="true"></i></h3>
          <p><?=yii::t('common','Checking') ?></p>
        </div>
        <div class="icon">
          <i class="fas fa-box-open" style="font-size:70px;"></i>
        </div>
        <div class="small-box-footer"><?=Yii::t('common','Detail') ?>  <i class="fa fa-arrow-circle-right"></i></div>
      </div>
    </div>
  </a>
  <!-- ./col -->

  <a href="index.php?SalehearderSearch[no]=&SalehearderSearch[status]=Shiped&r=SaleOrders/saleorder" >
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box box-ewBlue">
        <div class="inner">
          <h3 class="ew-shipped-count"><i class="fas fa-sync-alt fa-spin" aria-hidden="true"></i><sup style="font-size: 20px"></sup></h3>
          <p><?=Yii::t('common','Shipped') ?></p>
        </div>
        <div class="icon">
          <i class="fa fa-truck" aria-hidden="true" style="font-size:80px;"></i>
        </div>
        <div class="small-box-footer"><?=Yii::t('common','Detail') ?>  <i class="fa fa-arrow-circle-right"></i></div>
      </div>
    </div>
  </a>
  <!-- ./col -->

  <a href="index.php?SalehearderSearch[no]=&SalehearderSearch[status]=Invoiced&r=SaleOrders/saleorder">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box box-ewGreen">
        <div class="inner">
          <h3 class="ew-invoice-count"><i class="fas fa-sync-alt fa-spin" aria-hidden="true"></i></h3>
          <p><?=Yii::t('common','status-invoiced') ?></p>
        </div>
        <div class="icon">
          <i class="fas fa-dollar-sign" style="font-size:70px;"></i>
        </div>
        <div class="small-box-footer"><?=Yii::t('common','Detail') ?> <i class="fa fa-arrow-circle-right"></i> </div>
      </div>
    </div>
  </a>
  <!-- ./col -->
</div>
<!-- /.row -->


<!-- 
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title"><?=Yii::t('common','Monthly Recap Report')?></h3>
				<div class="col-xs-6 col-sm-4 pull-right" style="margin-right:-15px;">
					<div class="row">
						<div class="col-sm-4 hidden-xs text-right" style="padding-top: 6px; padding-right: 0px;"><span ><?=Yii::t('common','Years')?></span> :</div>
						<div class="col-xs-12 col-sm-8"><?= Html::dropDownList('years',['value' => date('Y')],getYearsList(),['class' => 'form-control']);?></div>
					</div>
				</div>
				 
			</div>

			 
		</div>
	</div>
</div>
-->
<div class="row" style="margin-bottom: <?=$space?>; font-family:Roboto !important;" >
  
  <div class="col-md-3 col-xs-6">        
    <div class="panel panel-info" id="daily-panel">
      <div class="panel-heading bg-info" id="daily-panel-bg-head">
          <h3 class="panel-title"></h3>
      </div>
      <div class="panel-body text-center">
          <h3><?=Yii::t('common','Daily')?></h3>			
          <div style="font-size:37px; height: 56px;">
              <?=Html::a('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>',
              ['/SaleOrders/report/invoice-list-per-day','ViewRcInvoiceDateSearch[posting_date]' => date('d/m/Y')],
              ['id' => 'daily-amount','target' => '_blank'])
              ?>
                
          </div>	
          <div class="row text-left margin-top">
              <div class="col-xs-12">						
                  <i class="fas fa-chevron-circle-down fa-spin" id="daily-icon"></i>
                  <span id="daily-color"><span id="daily-sign"></span> <span id="daily-percent"></span></span>				
              </div>
          </div>
          <div class="row text-left">
              <div class="col-xs-12 text-gray"><small><span id="daily-yesterday"><?=Yii::t('common','Yesterday')?> : </span>  </small></div>
          </div>				
      </div>
    </div>        
  </div>

  <div class="col-md-3 col-xs-6">        
    <div class="panel panel-info" id="monthly-panel">
        <div class="panel-heading bg-info" id="monthly-panel-bg-head">
          <h3 class="panel-title"></h3>
        </div>
        <div class="panel-body text-center">
          <h3><?=Yii::t('common','Monthly')?></h3>	
          <div style="font-size:37px; height: 56px;">			
              <?=Html::a('<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>',['/SaleOrders/report/invoice-list',
              'ViewRcInvoiceSearch[posting_date]' => date('m/Y')],
              ['id' => 'monthly-amount','target' => '_blank'])
              ?>
          </div>
          <div class="row text-left margin-top">
              <div class="col-xs-12">
              <i class="fas fa-chevron-circle-up fa-spin" id="monthly-icon"></i>
                  <span id="monthly-color"><span id="monthly-sign"></span> <span id="monthly-percent"></span></span>									
              </div>
          </div>
          <div class="row text-left">
              <div class="col-xs-12 text-gray"><small><?=Yii::t('common','Last month')?> : <span id="monthly-lastmonth"></span> </small></div>
          </div>
        </div>
    </div>        
  </div>
  <div class="col-md-6 col-xs-12">        
    <div class="panel panel-default" >
      <div class="panel-heading">
        <?= Html::dropDownList('years',['value' => Yii::$app->session->get('workyears')],getYearsList(),['class' => 'pull-right on-years-change','style' => 'margin-right: -10px; margin-top: -5px;']);?>
        <h3 class="panel-title text-center"><i class="fas fa-chart-bar"></i> <?=Yii::t('common','Sale Summary')?></h3>
      </div>
      <table class="table">
        <tbody>	
          <tr>
              <td style="height:60px;">
                <?=Html::a(Yii::t('common','SALE ORDER'),['SaleOrders/report/sale-cash-no-detail'],['target' => '_blank'])?>	                
              </td>
              <td class="text-right text-green">
                <?=Html::a('<i class="fas fa-sync-alt fa-spin"></i>',['SaleOrders/report/sale-cash-no-detail'],['target' => '_blank','class' => 'sale-order'])?>
              </td>                            						
          </tr>
          <tr>
              <td style="height:60px;"><?=Html::a(Yii::t('common','INVOICE'),['SaleOrders/report/invoice-list'],['target' => '_blank'])?> </td>
              <td class="text-right text-green">
                <?=Html::a('<i class="fas fa-sync-alt fa-spin"></i>',['SaleOrders/report/invoice-list'],['target' => '_blank','class' => 'sale-invoice'])?>                
              </td>
          </tr>
          <tr>
            <td style="height:60px;">
              <a href="?r=SaleOrders/saleorder/not-invoice" target="_blank"><?=Yii::t('common','NOT INVOICE')?> </a>
              <div class=" text-gray text-right" style="margin-top:0px;"><small><i class="far fa-clock"></i> <?=Yii::t('common','Update every {h} Hour',['h' => 1])?></small></div> 
            </td>
            <td class="text-right ">
              <a href="?r=SaleOrders/saleorder/not-invoice" class="not-receipt" target="_blank"><i class="fas fa-sync-alt fa-spin"></i></a>             
            </td>
            
          </tr>
        </tbody>
      </table>
    </div>        
  </div>
</div>



<div id="saleTable"></div>