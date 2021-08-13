<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use dosamigos\chartjs\ChartJs;

$space 			= '50px';
$this->title 	= Yii::$app->session->get('brand')? Yii::$app->session->get('brand') : 'EWIN-LATEST';
?> 
<!-- <div class="hidden-xs">
	<?=Breadcrumbs::widget([
		'itemTemplate' => "<i class=\"fas fa-home\"></i> <li><i>{link}</i></li>\n", // template for all links
		'links' => [
			Yii::t('common','Dashboard')
		],
	]);?>
</div> -->

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-desktop text-aqua"></i> <?=Yii::t('common','Desktop')?></a>
		</li>				
		<li role="presentation">
			<a href="#Favorite" aria-controls="tab" role="tab" data-toggle="tab"><i class="far fa-star text-yellow"></i> <?=Yii::t('common','Favorite')?></a>
		</li>				
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="home">
			<div class="row">
				<div class="col-xs-12 mt-10">
					<?=$this->render('_index-widget',['space' => $space])?>
					<div id="new-customer"></div>
					<div class="modal fade" id="modal-line-chart">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title">Modal title</h4>
								</div>
								<div class="modal-body">
									
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-power-off"></i> <?=Yii::t('common','Close')?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="Favorite"><?=$this->render('widget',['space' => $space])?></div>
	</div>
</div>
<?=$this->render('index-script')?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js',['depends' => [\admin\assets\ReactAsset::className()]]); ?>
<?php
	$Options =  ['depends' => [\admin\assets\ReactAsset::className()],'type'=>'text/jsx'];
	$this->registerJsFile('@web/js/site/index-dashboard-table.jsx?v=4.01.31',$Options); 	
	$this->registerJsFile('@web/js/site/index-new-customer.jsx?v=4.01.31',$Options);
?>

 
