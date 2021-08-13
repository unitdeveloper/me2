<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use dosamigos\chartjs\ChartJs;

$space = '50px';
$this->title 	= Yii::$app->session->get('brand')? Yii::$app->session->get('brand') : 'EWIN-LATEST';

?>


<div class="hidden-xs">
	<?=Breadcrumbs::widget([
		'itemTemplate' => "<i class=\"fas fa-home\"></i> <li><i>{link}</i></li>\n", // template for all links
		'links' => [
			Yii::t('common','Dashboard'),
		],
	]);?>
</div>
<?=$this->render('widget')?>
<?=$this->render('_index-widget',['space' => $space])?>


<style>
	.table-customer-due{
		overflow: auto;
		max-height:305px;
	}
	 
@media (max-width: 768px) {
  .table-customer-due {
	overflow-y: hidden;
	height:100%;
  }
}
</style>


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


<?=$this->render('index-script')?>

<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js',['depends' => [\admin\assets\ReactAsset::className()]]); ?>


<?php
	$Options =  ['depends' => [\admin\assets\ReactAsset::className()],'type'=>'text/jsx'];
 
	$this->registerJsFile('@web/js/site/index-dashboard-table-by-sales.jsx?v=4.04.29',$Options); 	
?>

