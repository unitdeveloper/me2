<?php

use yii\helpers\Html;
use yii\grid\GridView;

use yii\widgets\Pjax;

use common\models\RcInvoiceHeader;
use common\models\BillingNote;
use common\models\Cheque;
use common\models\Approval;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\ChequeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

function fetchInvoice($models){


 

	$query = RcInvoiceHeader::find()
	->where(['cust_no_' => $models->cust_no_])
	->orderBy(['paymentdue' => SORT_DESC]);

	$data = '<div class="row">';
	$i = 0;
	$url = '';

	foreach ($query->all() as $key => $Inv) {
		$i++;


		// <!-- ถ้าออกใบวางบิลแล้ว จะเป็นสีฟ้า -->

		$Billing = BillingNote::find()->where(['inv_no' => $Inv->id]);

	 	$bgColor = NULL;

	 	if($Billing->exists()){
	 		$bgColor 	= 'bg-info';

	 		$Bill 		= $Billing->one();
	 		$url 		= 'index.php?r=accounting/billing/update&id='.base64_encode($Bill->no_);
	 		
	 	}

	 	// <!-- /.ถ้าออกใบวางบิลแล้ว จะเป็นสีฟ้า -->



	 	// <!-- ถ้ารับเงินแล้วจะเป็นสีเขียว -->
	 	$Recipt = Cheque::find()->where(['apply_to' => $Inv->id]);

	 	if($Recipt->exists()){

	 		$Approve 	= $Recipt->one();
	 		if($Approve->getComplete() > 0){

	 			$bgColor 	= 'bg-success';
	 			//$i--;

	 		}else {
	 			$bgColor 	= 'bg-warning';
	 		}
	 		
	 	}
	 	// <!-- /.ถ้ารับเงินแล้วจะเป็นสีเขียว -->


		$data.= '<div class="col-xs-5 '.$bgColor.' inv-row">'.$i.'. '.$Inv->no_.'</div>
				 <div class="col-xs-4 '.$bgColor.' inv-row"> '.$Inv->paymentdue.'</div>
				 <div class="col-xs-3 '.$bgColor.' inv-row text-right"> '.number_format($Inv->getSumTotal()).'</div>';
 

	}
	$data.= '</div>';

	return [
		'getData'=>$data,
		'count' => $query->count(),
		'url' => $url,
		];
} 


?>

<style type="text/css">
	.short-text{
		/*max-width:70px; 
		overflow: auto; 
		word-wrap: break-word;*/
	}
	.panel-body-style{
		height: 130px;
		overflow: auto;

	}
	.inv-row{
		min-height:30px;
	}
	.text-white{
		color: #fff;
	}
</style>
<div class="cheque-index" style="margin-top: 10px;">






<?php

	$CountBill 	= 0;
	

	$html = '<div class="row">';
	foreach ($dataProvider->models as $key => $model) {


		
		
		$PanelStyle 	= 'panel-default';

		$PanelHeading 	= NULL;

		$url		= 'index.php?r=accounting/billing/create&fdate=&tdate=&searchVat=&customer='.$model->cust_no_;



		// <!-- ถ้าออกใบวางบิล ครบตามInv จะเป็นสีน้ำเงิน -->
		$CountBill 	= CountBill($model);
		$CountModal = fetchInvoice($model)['count'];
		if($CountBill == $CountModal) {
			$PanelStyle = 'panel-primary ';
			$PanelHeading = 'text-white';
			$url		= fetchInvoice($model)['url'];
		}
		// <!-- /.ถ้าออกใบวางบิล ครบตามInv จะเป็นสีน้ำเงิน -->


		$thisYear  	= date('Y',strtotime(date('Y').' + 1 Years'));

		$Customer 	= '['.$model->customer->code.'] '.$model->customer->name;

		

		$html.= '<div class="col-lg-4 col-sm-6 col-xs-12">';
		$html.= '	<div class="panel '.$PanelStyle.'">';
		$html.= '		<div class="panel-heading panel-heading-style ">
							<a href="'.$url.'" class="'.$PanelHeading.'"><i class="fa fa-address-card-o" aria-hidden="true"></i> <span class="short-text">'.mb_substr($Customer, 0,50).' </span></a>
						</div>';

		$html.= '		<div class="panel-body panel-body-style">';
		$html.= 			fetchInvoice($model)['getData'];
		$html.= '		</div>
						<!--/.body-->';
		$html.= '	</div>
					<!--/.panel-->';
		$html.= '</div>';
	}

	$html.= '</div>';

	echo $html;


?>

 
</div>

<?php
	function CountBill($model){

		$BillingNote	= BillingNote::find()->where(['cust_no_' => $model->cust_no_]);
		$CountBill 		= $BillingNote->count();

		return $CountBill;

	}


?>



