<?php
use yii\widgets\Breadcrumbs;
use dosamigos\chartjs\ChartJs;

$space = '50px';
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
</style>

<div class="hidden-xs">
	<?=Breadcrumbs::widget([
		'itemTemplate' => "<i class=\"fas fa-home\"></i> <li><i>{link}</i></li>\n", // template for all links
		'links' => [
			'Dashboard',
		],
	]);?>
</div>


 <!-- Small boxes (Stat box) -->
<div class="row" ng-init="Title='<?=Yii::t('common','Welcome')?>'" style="margin-bottom: <?=$space?>;">

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




<div class="row" style="margin-bottom: <?=$space?>; font-family:Roboto !important;" >
	<div class="col-md-3">        
		<div class="panel panel-info" id="daily-panel">
			  <div class="panel-heading bg-info" id="daily-panel-bg-head">
					 <h3 class="panel-title"></h3>
			  </div>
			  <div class="panel-body text-center">
			  	<div style="position:absolute; right:25px; top:25px;" id="daily-yesterday"></div>
			  	<h3><?=Yii::t('common','Daily')?></h3>
				<i class="fas fa-chevron-circle-down fa-4x fa-spin" id="daily-icon"></i>
				<div class="row text-left margin-top">
 					<div class="col-xs-12">
						<span id="daily-currency"></span> 
						<span id="daily-amount"></span> 
						<span id="daily-color"><span id="daily-sign"></span> <span id="daily-percent"></span></span>				
					</div>
				</div>
				<div class="row text-left">
 					<div class="col-xs-12 text-gray"><small><?=Yii::t('common','Today in the last month')?> : <span id="daily-lastmonth"></span>  </small></div>
				</div>
				
			  </div>
		</div>        
	</div>
	<div class="col-md-3">        
		<div class="panel panel-info" id="weekly-panel">
			  <div class="panel-heading bg-info" id="weekly-panel-bg-head">
					 <h3 class="panel-title"></h3>
			  </div>
			  <div class="panel-body text-center">
				<div style="position:absolute; right:25px; top:25px;" id="weekly-lastweek"></div>
 				<h3><?=Yii::t('common','Weekly')?></h3>
				<i class="fas fa-chevron-circle-up fa-4x fa-spin" id="weekly-icon"></i>
				<div class="row text-left margin-top">
 					<div class="col-xs-12">
						<span id="weekly-currency"></span>
						<span id="weekly-amount"></span> 
						<span id="weekly-color"><span id="weekly-sign"></span> <span id="weekly-percent"></span></span>									
					</div>
				</div>
				<div class="row text-left">
 					<div class="col-xs-12 text-gray"><small><?=Yii::t('common','This week in last month')?> : <span id="weekly-lastmonth"></span> </small></div>
				</div>
			  </div>
		</div>        
	</div>
	<div class="col-md-6">        
		<div class="panel panel-default" >
			  <div class="panel-heading">
					 <h3 class="panel-title text-center"><i class="fas fa-chart-bar"></i> <?=Yii::t('common','Sale Summary')?></h3>
					 <div class="pull-right text-gray" style="margin-top:-20px;"><small><i class="far fa-clock"></i> <?=Yii::t('common','Update every {h} Hour',['h' => 1])?></small></div>
			  </div>
			  <table class="table">
				<tbody>	
 					<tr>
 						<td style="height:60px;"><?=Yii::t('common','SALE ORDER')?> </td>
						<td class="text-right text-green sale-order"><i class="fas fa-sync-alt fa-spin"></i></td>
					 </tr>
					 <tr>
 						<td style="height:60px;"><?=Yii::t('common','INVOICE')?> </td>
						<td class="text-right text-green sale-invoice"><i class="fas fa-sync-alt fa-spin"></i></td>
					 </tr>
					 <tr>
					 	<td style="height:60px;"><a href="?r=SaleOrders/saleorder/not-invoice" target="_blank"><?=Yii::t('common','NOT INVOICE')?> </a></td>
						<td class="text-right "><a href="?r=SaleOrders/saleorder/not-invoice" class="not-receipt" target="_blank"><i class="fas fa-sync-alt fa-spin"></i></a></td>
					 </tr>
				</tbody>
			  </table>
		</div>        
	</div>
</div>


<div id="saleTable"></div>



<div id="Sec3"></div>


<?=$this->render('index-script')?>

<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js',['depends' => [\admin\assets\ReactAsset::className()]]); ?>


<?php
	$Options =  ['depends' => [\admin\assets\ReactAsset::className()],'type'=>'text/jsx'];
	$this->registerJsFile('@web/js/site/index.jsx?v=3.06.26.1', $Options);
	$this->registerJsFile('@web/js/site/index-dashboard-table.jsx?v=3.06.26',$Options); 	
?>

