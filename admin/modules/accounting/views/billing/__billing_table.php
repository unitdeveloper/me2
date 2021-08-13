<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\models\Customer;
use common\models\Company;
use common\models\SalesPeople;
 
 

$Actions	= Yii::$app->controller->action->actionMethod; 


// Set line amount per page.
$PerPage	= 10;
 
 
function getHeader($page,$lastPage,$dataProvider,$customer){


	$Actions 		= Yii::$app->controller->action->actionMethod; 

	$GenSeries		= new \admin\models\Generater();
	$NoSeries 		= $GenSeries->NextRuning('billing_note','vat_type','0',false); 

	$DocNo 			= '';

	$custId 		= 0;

	$TEXT_COMMENT 	= 'ได้รับบิลเงินเชื่อหรือเงินสดไว้ เพื่อตรวจสอบและพร้อมจะชำระเงินให้ตามบิลดังต่อไปนี';
	 
 

	if($Actions!='actionRenderTable'){
		foreach ($dataProvider->models as $model) {
		 	$DocNo 	= $model->no_;
		 	$custId = $model->cust_no_;
		 }
		
		 
	}else  {
		$DocNo 		= substr($NoSeries, 0,-3).'XXX';
	}




	if($Actions=='actionUpdate'){

		foreach ($dataProvider->models as $model) {
		 	$DocNo 			= $model->no_;
		 	// Search in update page.
		 	if(@$_GET['getView'] == 'false'){
				$DocNo = substr($NoSeries, 0,-3).'XXX';
			}

		 	$TEXT_COMMENT 	= $model->text_comment; 
		 	 

		 }

	}

	//if(Yii::$app->session->get('billingNo')==NULL) $DocumentNo = substr($NoSeries, 0,-3).'XXX';

	$comp 		= Company::find()
				->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
				->one();
	$Customer 	= Customer::findOne(isset($customer) ? $customer : $custId);



	if(isset($customer)){
		if($customer!=''){

			$cust = $Customer->fullAddress;

		}else {
			$cust = NULL;
		}
	}else {
		$cust = [
			'address' => NULL,
			'code' => NULL,
			'name' => NULL,
			'id' => NULL,
			'phone' => NULL,
			'fax' => NULL
		];
	}

	if($Actions=='actionUpdate')
	{
		$cust = $Customer->fullAddress;
		
	}




$Yii 		= 'Yii';
$date 		= 'date';
$margintop 	= $page > 1 ? 'margin-top:20px;' : '';
$firstpage 	= $page > 1 ? 'other-page' : 'first-page';
$html =<<<HTML

<style>
.text-white{
	text-shadow: none;
}

 @media print{
     .no-print, .minus-line {
		display:none;
	 }

 }
</style>
<div class="row body-content">
	
	<div class="body-print" >
		<div class="print-header">
			<div class="row">

				<div class="profile-header {$firstpage}" style="{$margintop}">
					<div class="col-xs-7">
						<div class="row">
							<div class="col-xs-12"><p class="comp-info">{$comp->name}</p></div>
							<div class="col-xs-12">
								<p class="addr">
									{$comp->vat_address}   
									อ.{$comp->vat_city}    
									จ.{$comp->vat_location} {$comp->postcode}
								</p>
							</div>
							<div class="col-xs-12">
								<p class="addr">{$comp->phone} {$comp->fax}</p>
							</div>
						</div>


						<div class="row">
							<div class="customer-info">
								<div class="col-xs-12"><span class="customer-billing" data="{$cust['id']}">ลูกค้า  {$cust['code']}</span></div>
								<div class="col-xs-12">{$cust['name']}</div>
								<div class="col-xs-12">{$cust['address']}</div>
								
							</div>
						</div>

					</div>

					<div class="col-xs-5 text-right" >
							
						<div class="row">

							<div class="col-xs-12">หน้าที่  {$page} / {$lastPage}</div>

							<div class="col-xs-12">
								<p class="text-name" id="bill-box">ใบวางบิล/ใบแจ้งหนี้</p>
							</div>
							
						</div>

						<div class="row " >
							<div class="col-xs-12 ">

								<div class="col-xs-6">{$Yii::t('common','No')} ใบวางบิล : </div>
								<div class="col-xs-6">{$DocNo}</div>

								<div class="col-xs-6">{$Yii::t('common','Date')} : </div>
								<div class="col-xs-6">
									{$date('d/m/y',strtotime($model->create_date.'+543 Years'))} 
								</div>
							</div>
						</div>

					</div>

					<div class="col-xs-12" style="margin-top:-25px;">
						<div class="">{$Yii::t('common','Phone')} : {$cust['phone']}, {$Yii::t('common','Fax')} : {$cust['phone']}</div>
					</div>

					<div class="col-xs-12">									
						<p class="text-comment edit-comment" id="edit-comment" data-text="{$TEXT_COMMENT}">{$TEXT_COMMENT}</p>
					</div>

				</div><!-- /.profile-header -->
			</div><!-- /. row -->

		</div><!-- /. print-header -->

	<div class="page-body">
	<table class="page-body-table" width="100%">
		<thead>
			<tr valign='top'>
				<td class=' ' >เลขที่</td>
				<td class='' style="max-width:150px !mportant;" >เลขที่ใบกำกับ</td>
				<td class='' >วันที่</td>
				<td class='' >ครบกำหนด</td>
				<td class='text-right'>จำนวนเงิน</td>
				<td class='text-right'>ชำระแล้ว</td>
				<td class='text-right'>เงินคงค้าง</td>				
			</tr>
		</thead>
HTML;

	return $html;
}




function footer($SumRemaining,$page,$topPage,$dataProvider){

	$Actions 		= Yii::$app->controller->action->actionMethod; 


				
	$comp 		= Company::find()
				->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
				->one();
	$Bahttext 	= new \admin\models\FunctionBahttext;

	$textBaht 	= $Bahttext->ThaiBaht($SumRemaining);

	$SumTotal 	= number_format($SumRemaining,2);



	$TEXT_REMARK 	= '';
	$TEXT_LECTURE 	= 'ในนาม '.$comp->name;

	if($page !=  $topPage ){
		$textBaht = NULL;
		$SumTotal = NULL;
	}


	if($Actions=='actionUpdate'){

		foreach ($dataProvider->models as $model) {



		 	$DocNo 			= $model->no_;			 	

		 	$TEXT_REMARK 	= $model->text_remark;
			$TEXT_LECTURE 	= $model->text_lecture;

		 }

	}

	
	$html = '<table class=" footer-summary" width="100%">
				<tr>
					<td class="text-baht">'.$textBaht.'</td>
					<td width="200px;" class="text-sum">รวมเป็นเงิน </td>
					<td width="100px;" class="number-summary"><b>'.$SumTotal.'</b></td>
				</tr>

			</table>';
	

	 
	$html.= '<div class="footer-page">
				<div class="footer-border">
	 				<div class="row ">
				
						<div class="col-xs-12 " style="height:50px;">
							หมายเหตุ <span class="text-remark">'.$TEXT_REMARK.'</span>
						</div>

						<div class="col-xs-offset-6" >
							<div class="col-xs-12 " style="height:50px;">
								<span class="text-lecture">'.$TEXT_LECTURE.'</span>
							</div>
						</div>
					
						<div class="col-xs-6">
							<p>ผู้รับวางบิล _____________________________</p>
							<p>วันที่รับวางบิล ________/_________/ __________</p>
							<p>วันที่นัดรับเช็ค ________/_________/ __________</p>
						</div>

						<div class="col-xs-6">
							<p>ผู้วางบิล _____________ ______/______/______</p>
							<p style="height:15px;">  </p>
							<p>ผู้รับเช็ค _____________ ______/______/______</p>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div style="margin-top:5px; " class="text-left font-roboto">
							<small style="padding-right:20px;">
								กรณีชำระด้วยเช็คโปรดสั่งจ่ายในนาม บริษัท จีโนล กรุ๊ป ซีที อิเล็คทริคฟิเคชั่น จำกัด เท่านั้น
							</small>
							<br /> 
							<small style="padding-right:20px;">
								กรณีโอนเงิน โอนในนาม บริษัท จีโนล กรุ๊ป ซีที อิเล็คทริคฟิเคชั่น จำกัด ธนาคารกสิกรไทย สาขาถนนเศรษฐกิจ 1 เลขที่บัญชี <b>464-1-02799-0</b>
							</small> 
							<br />
							<small style="padding-right:20px;">
								และ ในนาม  MR.ZHAO JINYU ธ.กรุงศรีอยุธยา เซ็นทรัลพลาซ่า มหาชัย เลขที่บัญชี <b>800-9-07376-9</b>
							</small>
						</div>
					</div>
				</div>
			</div>';	

	if($Actions=='actionUpdate'){
		$html.= '<div class="text-right mt-5 no-print">  
					<button type="button" class="btn btn-warning-ew btn-sm btn-add-new-line"><i class="fa fa-plus"></i> '.Yii::t('common','Insert-line').'</button>
				</div>';
	}

	$html.= '</div><!-- /.page-body -->';
	
 	$html.= '</div>	<!--/.body-print-->					  
		</div><!-- /.body-content -->';


	return $html;

}


function NoData(){
	return '<div class="row body-content">
			 <div class="body-print">
		        <div class="print-header">
					<div class="row">

				        <div class="profile-header">
							<div class="no-data">
							<p class="text-center"> <i class="fa fa-bolt fa-5x" aria-hidden="true"></i> </p>
				            <p class="text-center"> No Data </p>
				            </div>
				            

				        </div><!-- /.profile-header -->
					</div><!-- /. row -->

				</div><!-- /. print-header -->

			 </div>';
}


?>





 
 
 
<?php
	$AllData = $dataProvider->getTotalCount();


	$data = $dataProvider->models;
	$data = array_chunk($data, $PerPage);

	if($AllData <= 0 ){
		echo NoData();
	}

	$ix 			= 0;
	$totalRemain 	= 0;
	$Inv 			= null;

	for ($i=0; $i < count($data); $i++) { 
	        

		$html = getHeader($i+1,count($data),$dataProvider,$customer); 
		$html.= '<tbody>
					';
		foreach ($data[$i] as $key => $model) {
			 
			$ix++;

			$PanelStyle    = '';
			if($Actions!='actionUpdate'){
				$model->type_of_document = 1 ;
			}


			if(isset($_GET['searchVat'])){

				
				// Search
				// Model 	= RcInvoiceHeader
				
				$No 		= $model->no_;

				$id 		= $model->id;

				$PostDate 	= date('d/m/Y',strtotime($model->posting_date.' + 543 Years'));

				$DueDate	= date('d/m/Y',strtotime($model->paymentdue.' + 543 Years'));

				$total 		= $model->getSumTotal();

				$Payment 	= $model->getPayment();
				

				 
				$SumRemaining = $total - $Payment;

				 

				$totalRemain += $SumRemaining;
				 

			}else {


				// Update
				// Model 	= BIllingNote
				// Inv 		= RcInvoiceHeader

				$Inv 	= \common\models\RcInvoiceHeader::find()->where(['id' => ($Actions=='actionUpdate') ? $model->inv_no : ''])->one();

				$No 	=  ($Actions=='actionUpdate') 
								? ($Inv != null 
									? $Inv->no_
									: $model->description)
								: $model->no_;

				$id 	= ($Actions=='actionUpdate') 
							? ($Inv != null 
								? $Inv->id
								: Yii::t('common','Deleted') )
							: $model->id;

				\Yii::$app->session->set('BillingUpdate',$No);

				$PostDate 	= $model->type_of_document == 1 
								?(($Actions=='actionUpdate') 
									? ($Inv != null  
										? date('d/m/Y',strtotime($Inv->posting_date.' + 543 Years')) 
										: date('d/m/Y',strtotime($model->posting_date.' + 543 Years')))
									: date('d/m/Y',strtotime($model->posting_date.' + 543 Years')))
								: '';

				$DueDate	= $model->type_of_document == 1
								? (($Actions=='actionUpdate') 
									? ($Inv != null  
										? date('d/m/Y',strtotime($Inv->paymentdue.' + 543 Years')) 
										: Yii::t('common','Deleted') )
									: date('d/m/Y',strtotime($model->paymentdue.' + 543 Years')))
								: '';

				$total 		= ($Actions=='actionUpdate') 								 
								? ($Inv != null 
									? $Inv->sumTotal 
									: $model->balance)
								: $model->sumTotal;

				$Payment 	= ($Actions=='actionUpdate') 
								? ($Inv != null 
									? $Inv->getPayment()
									: 0) 
								: $model->getPayment();

				 
				$SumRemaining = $total - $Payment;

				 
				//var_dump($Inv->id);

				$totalRemain += $SumRemaining;

				// <!-- ถ้าเปิดใบวางบิลแล้วจะเป็นสีแดง -->
				if($Actions!='actionUpdate') {

					if($Inv != null){					
						$Billing = common\models\BillingNote::find()->where(['inv_no' => ($Actions=='actionUpdate') ? $Inv->id : $model->id]);
						if($Billing->exists()){
							$PanelStyle    = 'background-color: rgba(255, 0, 0, 0.4); color: #fff;';                     
						}
						
						// <!-- ถ้ารับเงินแล้วจะเป็นสีเขียว -->
						$Recipt = \common\models\Cheque::find()->where(['apply_to' => ($Actions=='actionUpdate') ? $Inv->id : $model->id ]);

						if($Recipt->exists()){

							$Approve    = $Recipt->one();

							if($Approve->getComplete() > 0){

								// Green
								// Approved						
								$PanelStyle    = 'background-color: #dff0d8; color:#000;';                           

							}else {

								// Orange
								// Not yet approve
								$PanelStyle    = 'background-color: #f0ad4e; color:#fff;'; 

							}

						}
						// <!-- /.ถ้ารับเงินแล้วจะเป็นสีเขียว -->
					} 
				}
			}

		 
			if($Inv != null){
				$RowsStyle = $PanelStyle;
			}else{
				if($model->type_of_document == 2){
					$RowsStyle = ' ';
				}else{
					$RowsStyle = 'background-color: black; color:red;';
				}
				
			}


			$html.= '<tr style="'.$RowsStyle.'" data-key="'.$model->id.'" class="row-invoice">';					 
			$html.= "	<td class='key-index'>{$ix}</td>";
			
			
			
			if($model->type_of_document==2){
				//$html.= "	<td><span class='iv-no text-white no-print' data='{$id}'> </span></td>";
				$html.= '	<td colspan="5" class="text-right" style="border-top:1px solid #000;">
								<input type="text" class="form-control text-right no-border discount-text" value="'.$model->description.'"  style="padding: 0px; font-family: saraban; font-size: 14px;"/>
							</td>';
				 
				$html.= ' 	<td class="text-right sum-line"  data="'.$SumRemaining.'"  style="position: relative;">
								<input type="text" class="form-control text-right no-border discount-line" style="padding: 0px; font-size: 14px; font-family: saraban;" value="'.number_format($SumRemaining,2).'"/>
									<div style="position:absolute;right: -15px; top: 18px;"><i class="fa fa-minus pointer text-red minus-line"></i></div>
							
							</td>';
			}else{
				$html.= "	<td><span class='iv-no ".($model->type_of_document == 1 ? '' : 'text-white no-print')."' data='{$id}'>{$No}</span></td>";
				$html.= "	<td>{$PostDate}</td>";
				$html.= "	<td >{$DueDate}</td>";
				$html.= "	<td class='text-right' width='100px'>".($model->type_of_document == 1 ? number_format($total,2) : '')."</td>";
				$html.= "	<td class='text-right' width='100px'>".number_format($Payment,2)."</td>";
				$html.= "	<td class='text-right sum-line' width='100px' data='{$SumRemaining}'>".number_format($SumRemaining,2)."</td>";
			}
			
			$html.= '</tr>';
		}
		$html.= '</tbody>';

		$html.= '</table>';
		$html.= footer($totalRemain,$i+1,count($data),$dataProvider);


		echo $html;
	}
			
?>
 
 
 

 