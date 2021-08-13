 <?php
use yii\widgets\Breadcrumbs;
use dosamigos\chartjs\ChartJs;


$space = '50px';
?>
<style>
	h3{
		font-family: "Roboto","Times New Roman", Times, serif !important;
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




<div class="row" style="margin-bottom: <?=$space?>;">
	<div class="col-md-3">        
		<div class="panel panel-danger" id="daily-panel">
			  <div class="panel-heading bg-red" id="daily-panel-bg-head">
					 <h3 class="panel-title"></h3>
			  </div>
			  <div class="panel-body text-center">
			  	<div style="position:absolute; right:25px;" id="daily-yesterday"></div>
			  	<h3>Daily</h3>
				<i class="fas fa-chevron-circle-down fa-4x text-red" id="daily-icon"></i>
				<div class="row text-left margin-top">
 					<div class="col-xs-12">
						<span id="daily-currency"></span>
						<span id="daily-amount"></span>
						<span id="daily-percent"></span> 
						
					</div>
				</div>
				<div class="row text-left">
 					<div class="col-xs-12 text-gray"><small>Lastmonth : <span id="daily-lastmonth"></span>  </small></div>
				</div>
				
			  </div>
		</div>        
	</div>
	<div class="col-md-3">        
		<div class="panel panel-success" id="weekly-panel">
			  <div class="panel-heading bg-green" id="weekly-panel-bg-head">
					 <h3 class="panel-title"></h3>
			  </div>
			  <div class="panel-body text-center">
 				<h3>Weekly</h3>
				<i class="fas fa-chevron-circle-up fa-4x text-green" id="weekly-icon"></i>
				<div class="row text-left margin-top">
 					<div class="col-xs-12">
						<span id="weekly-currency"></span>
						<span id="weekly-amount"></span> 
						<span id="weekly-percent"></span> 						
					</div>
				</div>
				<div class="row text-left">
 					<div class="col-xs-12 text-gray"><small>Lastmonth : <span id="weekly-lastmonth"></span> </small></div>
				</div>
			  </div>
		</div>        
	</div>
	<div class="col-md-6">        
		<div class="panel panel-default">
			  <div class="panel-heading">
					 <h3 class="panel-title text-center"><i class="fas fa-chart-bar"></i> Sale Summary</h3>
			  </div>
			  <table class="table">
				<tbody>	
 					<tr>
 						<td style="height:60px;">SALE ORDER </td>
						<td class="text-right sale-order"><i class="fas fa-sync-alt fa-spin"></i></td>
					 </tr>
					 <tr>
 						<td style="height:60px;">INVOICE </td>
						<td class="text-right sale-invoice"><i class="fas fa-sync-alt fa-spin"></i></td>
					 </tr>
					 <tr>
 						<td style="height:60px;">NOT RECEIPT </td>
						<td class="text-right not-receipt"><i class="fas fa-sync-alt fa-spin"></i></td>
					 </tr>
				</tbody>
			  </table>
		</div>        
	</div>
</div>



<div class="row" style="margin-bottom: <?=$space?>;">
	<div class="col-md-12">
	
		<div class="panel panel-success">
			<div class="panel-body row">	 
				<div class="col-sm-8">		  
					<?= ChartJs::widget([
						'type' => 'line',
						'options' => [
							'height' => 150,
							'width' => 400
						],
						'data' => [
							'labels' => ["January", "February", "March", "April", "May", "June", "July"],
							'datasets' => [
								[
									'label' => "My First dataset",
									'backgroundColor' => "rgba(179,181,198,0.2)",
									'borderColor' => "rgba(179,181,198,1)",
									'pointBackgroundColor' => "rgba(179,181,198,1)",
									'pointBorderColor' => "#fff",
									'pointHoverBackgroundColor' => "#fff",
									'pointHoverBorderColor' => "rgba(179,181,198,1)",
									'data' => [65, 59, 90, 81, 56, 55, 40]
								],
								[
									'label' => "My Second dataset",
									'backgroundColor' => "rgba(255,99,132,0.2)",
									'borderColor' => "rgba(255,99,132,1)",
									'pointBackgroundColor' => "rgba(255,99,132,1)",
									'pointBorderColor' => "#fff",
									'pointHoverBackgroundColor' => "#fff",
									'pointHoverBorderColor' => "rgba(255,99,132,1)",
									'data' => [28, 48, 40, 19, 96, 27, 100]
								]
							]
						]
					]);
				?>
				</div>
				<div class="col-sm-4">
				<?= ChartJs::widget([
						'type' => 'doughnut',
						'options' => [
								'height' => 245,
								'width' => 'auto'
							],
						'data' => [
							'labels' => ['Red','Yellow','Blue'],
							'datasets' => [
								[				
									'data' => [10, 20, 30],
									'backgroundColor' => ['#ff6384','#36a2eb','#cc65fe']
										
								],
							],
							
						],
						
					]);
					?>
					</div>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-sm-8 hidden-xs">
					Panel footer
					</div>
					<div class="col-sm-4 col-xs-12">
					Panel footer
					</div>
					
				</div>
			</div>
		</div>
	
	</div>
 
	
</div>




<div class="row">
	<div class="col-md-8">						
		<div class="panel panel-default">
				<table class="table   ">
						<tbody>
							<tr>
								<td>img</td>
								<td>Pornprapa</td>
								<td>Janocha</td>
								<td>100,000</td>
								<td>
								<div>90,000</div>
								<small class="text-gray">Profit</small>
								</td>
								<td class="text-right hidden-xs" style="width:210px;">
									<button class="btn btn-sm btn-default btn-flat"><i class="far fa-edit"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-cog"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="far fa-envelope"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-sync-alt"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-file"></i></button>
									
								</td>
								<td class="text-center">
									<i class="fas fa-circle text-green"></i>
								</td>
							</tr>
							<tr>
								<td>img</td>
								<td>Pornprapa</td>
								<td>Janocha</td>
								<td>100,000</td>
								<td>
								<div>90,000</div>
								<small class="text-gray">Profit</small>
								</td>
								<td class="text-right hidden-xs" style="width:210px;">
									<button class="btn btn-sm btn-default btn-flat"><i class="far fa-edit"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-cog"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="far fa-envelope"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-sync-alt"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-file"></i></button>
									
								</td>
								<td class="text-center">
									<i class="fas fa-circle text-green"></i>
								</td>
							</tr>
							<tr>
								<td>img</td>
								<td>Pornprapa</td>
								<td>Janocha</td>
								<td>100,000</td>
								<td>
								<div>90,000</div>
								<small class="text-gray">Profit</small>
								</td>
								<td class="text-right hidden-xs" style="width:210px;">
									<button class="btn btn-sm btn-default btn-flat"><i class="far fa-edit"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-cog"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="far fa-envelope"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-sync-alt"></i></button>
									<button class="btn btn-sm btn-default btn-flat"><i class="fas fa-file"></i></button>
									
								</td>
								<td class="text-center">
									<i class="fas fa-circle text-green"></i>
								</td>
							</tr>
						</tbody>
				</table>
				<div class="panel-footer">
					Panel footer
				</div>
		</div>						
	</div>
	<div class="col-md-4">
		<div class="box box-info">

			<?php
			use \common\models\ViewRcInvoice;

				$saleInvoice = ViewRcInvoice::find()
				->select(['sale_id'])
				->where(['between','posting_date',date('Y').'-01-01',date('Y-m-d')])
				->andWhere(['<>','sale_id',''])
				->groupBy(['sale_id']);

				$sales	= [];
				$data 	= [];
				foreach ($saleInvoice->all() as $key => $sale) {
					
					$total = 0;
					foreach (ViewRcInvoice::find()->where(['sale_id' => $sale->sale_id])->all() as $key => $model) {
						 $total += $model->total;
					}

					//$sales[] 	= $sale->salesPeople->name;


					$data[] = (Object)[
						'id' => $sale->sale_id,
						'name' => $sale->salesPeople->name,
						'total' => $total
					];
					
				}

				
				// $models = ViewRcInvoice::find()
				// ->where(['sale_id' => $sales->id]);

				// //echo $models->createCommand()->rawSql;	
				// $data = [];								
				// foreach ($models->all() as $key => $model) {
				// 	$data[] = (Object)[
				// 		'labels' => ''
				// 	];
				// }
				function getData($data){

					$total 	= [];
					$name 	= [];
					$id 	= [];
					foreach ($data as $key => $value) {
						
						$total[$key] =  $value->total;

				 
					}

					array_multisort($total, SORT_DESC, $data);

					return $data;
				}
				
				$DataSort = getData($data);

				foreach ($DataSort as $key => $value) {
					$sales[] 	= $value->name;
				}

				$dataList = [];
				foreach ($DataSort as $key => $value) {
					$dataList[] 	= round($value->total/1000000,1);
				}

			?>
			<?= ChartJs::widget([
							'type' => 'horizontalBar',
							'options' => [
								'height' => 550,
								'width' => 'auto',
							],
							'data' => [
								'labels' => $sales,
								'datasets' => [
									[
										'label' => "Top Sale",							 
										'borderColor' => "rgba(255,99,132,1)",
										'pointBackgroundColor' => "rgba(255,99,132,1)",
										'pointBorderColor' => "#fff",
										'pointHoverBackgroundColor' => "#fff",
										'pointHoverBorderColor' => "rgba(255,99,132,1)",
										'backgroundColor' => ['#ff6384','#36a2eb','#cc65fe'],
										'data' => $dataList
									]
								]
							]
						]);
					?>
		</div>
	</div>
</div>

<?=$this->render('index-script')?>

