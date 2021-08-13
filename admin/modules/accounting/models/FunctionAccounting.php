<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\db\Expression;
use yii\base\Model;
use admin\models\Generater;


use common\models\WarehouseHeader;
use common\models\WarehouseMoving;

use common\models\SaleInvoiceHeader;
use common\models\SaleInvoiceLine;


use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;


use common\models\SaleLine;
use common\models\SaleHeader;

use common\models\OrderTracking;

use admin\modules\tracking\models\FunctionTracking;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionAccounting extends Model 
{
	public function getFromInvoiceLine($data)
	{
 
		// If document are created.
		// Do create only line. 
		if(isset($data['id']))
		{
			
			$InvHeader = SaleInvoiceHeader::findOne($data['id']);

			// Create only invoice line.
			foreach ($data['ship'] as $value) {			 	
				$this->createInvoiceLine($value,$InvHeader);			 
			}

			$this->OrderTrackingSaveOnlyLine($InvHeader,$data['ship']);

			 

			return json_encode([
				'status' => 200,
				'message' => 'done',
				'value' => [
					'inv' => $data['id'],
					'data' => $data
					
				]
			]);

			
			 
			

		}else {

			// Create new invoice.		 
			
			$resData = $this->createInvoice($data);

			$SaleHeader = SaleHeader::findOne($resData->value->order);			
			$SaleHeader->status  = 'Invoiced';
			if($SaleHeader->save(false)){
				$this->OrderTrackingSave($resData->value->id);		

				return json_encode([
					'status' 	=> 200,
					'message' 	=> 'done',
					'value' 	=> [
						'inv' 	=> $resData->value->id,
						'data' 	=> $resData						
					]
				]);
			}

		}
 

	}
	public static function getTotalBalance($model,$table)
	{
		$vat 			= $model->vat_percent; 


		$BeforeDisc 	= 0;

		if($table == 'RcInvoiceLine'){
			//RcInvoiceLine
			$BeforeDisc   = RcInvoiceLine::find()->where(['source_id' => $model->id])->sum('quantity * unit_price');

		}else {
			//SaleInvoiceLine
			$BeforeDisc   = SaleInvoiceLine::find()->where(['source_id' => $model->id])->sum('quantity * unit_price');
		}

		

		

		$Discount 		= $model->discount;

		// หักส่วนลด (ก่อน vat)
		$subtotal  		= $BeforeDisc - $Discount ;


		if($model->include_vat == 1){ 

			// Vat นอก

			$InCVat   	= ($subtotal * $vat )/ 100;

			$total    	= ($InCVat + $subtotal);

		}else {

			// Vat ใน

			// 1.07 = 7%
			$vat_revert = ($vat/100) + 1;

			$InCVat   	= $subtotal - ($subtotal / $vat_revert);

		  	$total    	= $subtotal;

		}

		return $total;
           
	}

	public function TotalBalance($model,$table)
	{
		$vat 			= $model->vat_percent; 


		$BeforeDisc 	= 0;

		if($table == 'RcInvoiceLine'){
			//RcInvoiceLine
			$model->id = $model->ref_inv_header; 

		}else {
			//SaleInvoiceLine
			$model->id = $model->id;
			
		}

		$BeforeDisc   = SaleInvoiceLine::find()->where(['source_id' => $model->id])->sum('quantity * unit_price');

		

		$Discount 		= $model->discount;

		// หักส่วนลด (ก่อน vat)
		$subtotal  		= $BeforeDisc - $Discount ;


		if($model->include_vat == 1){ 

			// Vat นอก

			$InCVat   	= ($subtotal * $vat )/ 100;

			$total    	= ($InCVat + $subtotal);

		}else {

			// Vat ใน

			// 1.07 = 7%
			$vat_revert = ($vat/100) + 1;

			$InCVat   	= $subtotal - ($subtotal / $vat_revert);

		  	$total    	= $subtotal;

		}

		return $total;
           
	}

	public function OrderTrackingSave($inv)
	{

		 

		$InvHeader 	= SaleInvoiceHeader::findOne($inv);

		$WhHeader 	= WarehouseHeader::find()->where(['DocumentNo' => $InvHeader->document_no_])->one();

		$SaleOrder 	= SaleHeader::find()->where(['no'=>$WhHeader->SourceDoc])->one();

		$tracking 	= new OrderTracking();

		

		$tracking->event_date 		= date('Y-m-d H:i:s');
		$tracking->doc_type 		= 'Sale-Inv';
		$tracking->doc_id 			= $inv;
		$tracking->doc_no 			= $InvHeader->no_;
		$tracking->doc_status 		= 'Open';
		$tracking->amount 			= $this->TotalBalance($InvHeader,'SaleInvoiceLine');
		$tracking->remark 			= $InvHeader->document_no_; // source doc..
		$tracking->ip_address 		= $_SERVER['REMOTE_ADDR'];
		$tracking->lat_long 		= '';
		$tracking->create_by 		= Yii::$app->user->identity->id;
		$tracking->comp_id 			= Yii::$app->session->get('Rules')['comp_id'];
		$tracking->track_for_table 	= 'sale_header';
		$tracking->track_for_id 	= $SaleOrder->id;


		if(!$tracking->save())
		{
			print_r($tracking->getErrors());
			exit();
		}

		 
		
	}


	public function OrderTrackingSaveOnlyLine($InvHeader,$data)
	{

		foreach ($data as $value) {
			 	
			$WhHeader 	= WarehouseHeader::findOne($value);

			$SaleOrder 	= SaleHeader::findOne($WhHeader->SourceDocNo);

			$tracking 	= new OrderTracking();

			$tracking->event_date 		= date('Y-m-d H:i:s');
			$tracking->doc_type 		= 'Sale-Inv';
			$tracking->doc_id 			= $InvHeader->id;
			$tracking->doc_no 			= $InvHeader->no_;
			$tracking->doc_status 		= 'Open';
			$tracking->amount 			= $this->TotalBalance($InvHeader,'SaleInvoiceLine');
			$tracking->remark 			= $WhHeader->DocumentNo;
			$tracking->ip_address 		= $_SERVER['REMOTE_ADDR'];
			$tracking->lat_long 		= '';
			$tracking->create_by 		= Yii::$app->user->identity->id;
			$tracking->comp_id 			= Yii::$app->session->get('Rules')['comp_id'];
			$tracking->track_for_table 	= 'sale_header';
			$tracking->track_for_id 	= $SaleOrder->id;


			if(!$tracking->save(false))
			{
				print_r($tracking->getErrors());
				exit();
			}


		} 
		
	}

	 
	public function createInvoice($data)
	{
		 
		// First Ship Document => $data['ship'][0]['value'].
		$transaction = Yii::$app->db->beginTransaction();

		try {

			$WHeader 	= WarehouseHeader::findOne($data['ship'][0]['value']);
			$InvHeader 	= $this->createInvoiceHeader($WHeader);

			
			if(isset($InvHeader['Error'])){
				if($InvHeader['Error']==true){ 

					$CusLink = 'index.php?r=customers/customer/update&id='.$InvHeader['cust_id'].'&m='.$InvHeader['message'];

					echo "<script>
							swal(
								'กรุณา ตรวจสอบรายละเอียดลูกค้า (".$InvHeader['cust_name'].")',
								'Error : (<a href=\'".$CusLink."\' target=\'_blank\'>".$InvHeader['cust_code']."</a>)',
								'warning'
							);
						</script>"; 

					$transaction->rollBack();
					exit();
				}
			}
			
			$val = '';

			foreach ($data['ship'] as $value) {

				$val = $this->createInvoiceLine($value['value'],$InvHeader);

			}

			$WHeader->status = 'Invoiced';
			$WHeader->save();

			$transaction->commit();
			// Return one document id.
			return (object)[
				'status' => 200,
				'message' => 'Invoice created',
				'value' => (Object)[
					'id' => $InvHeader->id,
					'order' => $InvHeader->order_id,
					'line' => $val
				]
			];

		} catch (\Exception $e) {

			$transaction->rollBack();
			throw $e;

		}
	}

    public function createInvoiceLine($line,$InvHeader)
    {
		
		$transaction = Yii::$app->db->beginTransaction();

		try {

			$WHLine 	= WarehouseMoving::find()
			->where(['source_id' => $line])
			->andWhere(['TypeOfDocument' => 'Sale'])
			->andWhere(['<>','source_id','0'])
			->all();

			foreach ($WHLine as $value) {
				
				$model = new SaleInvoiceLine();

				$SaleLine = SaleLine::findOne($value['SourceDoc']);
				
				$model->type 			= 'Item';
				$model->doc_no_ 		= $InvHeader->no_;
				$model->item 			= $SaleLine->item;
				$model->source_id 		= $InvHeader->id;
				$model->line_no_ 		= $SaleLine->id;
				$model->customer_no_	= $InvHeader->cust_no_;
				$model->code_no_		= $value['ItemNo'];
				$model->code_desc_		= $value['Description'];
				$model->quantity 		= $value['Quantity'] * -1;
				$model->unit_price 		= $SaleLine->unit_price;
				$model->vat_percent 	= $SaleLine->vat_percent;
				$model->line_discount 	= $SaleLine->line_discount;
				$model->order_id 		= $SaleLine->sourcedoc;
				$model->source_doc		= $value['DocumentNo'];
				$model->source_line		= $value['id'];
				$model->session_id 		= Yii::$app->session->getId();
				$model->comp_id			= Yii::$app->session->get('Rules')['comp_id'];

				if(!$model->save(false))
				{
					$transaction->rollBack();
					return $model->getErrors();
				} 

			}
			
			$transaction->commit();
			// return id
			return $InvHeader->id;

			

		} catch (\Exception $e) {

			$transaction->rollBack();
			throw $e;

		}


    }


    public function createInvoiceHeader($WHeader)
    {
		$model 					= new SaleInvoiceHeader();
		
		$transaction = Yii::$app->db->beginTransaction();

		try {

			$SaleHeader 			= SaleHeader::findOne($WHeader->SourceDocNo);


			$GenSeries 				= new Generater(); //
			$NoSeries 				= $GenSeries->GenNumber('vat_type','vat_value', ($SaleHeader->vat_percent ? $SaleHeader->vat_percent : 0), false);

			// Auto Create
			//$NoSeries           	= $model->getAutoNumber(0);  
			 

			$model->no_ 			= $NoSeries;
			$model->cust_no_ 		= $WHeader->customer_id;
			$model->cust_name_		= $SaleHeader->customer->name;
			$model->cust_address 	= $SaleHeader->customer->address;
			$model->cust_address2 	= $SaleHeader->customer->address2;
			$model->taxid 	 		= $SaleHeader->customer->vat_regis;
			$model->branch 			= $SaleHeader->customer->branch;

			$model->contact 		= $SaleHeader->customer->contact;
			$model->phone 			= $SaleHeader->customer->phone;

			// Address
			$model->district 		= ($SaleHeader->customer->district) ? $SaleHeader->customer->district : NULL;
			$model->city 			= ($SaleHeader->customer->city)? $SaleHeader->customer->city: NULL;
			$model->province 		= ($SaleHeader->customer->province)? $SaleHeader->customer->province : NULL;
			$model->postcode 		= ($SaleHeader->customer->postcode)? $SaleHeader->customer->postcode : NULL;


			$model->document_no_	= $WHeader->DocumentNo;
			$model->posting_date 	= date('Y-m-d H:i:s');
			$model->doc_type 		= 'Sale';
			$model->order_id 		= $SaleHeader->id;
			$model->sales_people 	= $SaleHeader->sales_people;
			$model->sale_id 		= $SaleHeader->sale_id;
			$model->cust_code 		= $WHeader->customer->code;
			$model->order_date	 	= $SaleHeader->order_date;
			$model->ship_date 		= $WHeader->ship_date;


			$model->vat_percent 	= ($SaleHeader->vat_percent)? $SaleHeader->vat_percent : 0 ;
			$model->include_vat		= $SaleHeader->include_vat;
			$model->paymentdue		= $SaleHeader->paymentdue;
			$model->payment_term 	= $SaleHeader->payment_term;
			
			$model->percent_discount= $SaleHeader->percent_discount;
			$model->discount 		= $SaleHeader->discount;

			
			$model->ext_document	= $SaleHeader->no;
			$model->remark 			= $SaleHeader->remark;
			$model->status 			= 'Open';
			$model->session_id 		= Yii::$app->session->getId();

			$model->user_id 		= Yii::$app->user->identity->id;
			$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];


			if($model->save()){
				$transaction->commit();
				return $model;
			}else {
				$transaction->rollBack();
				return [
						'Error' => true,
						'cust_name' => $SaleHeader->customer->name,
						'cust_id' => $SaleHeader->customer->id,
						'cust_code' => $SaleHeader->customer->code,
						'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
					];
			}			

		} catch (\Exception $e) {

			$transaction->rollBack();
			throw $e;

		}
    }

    // Inventory Adjustment
    public function invenAdjust($getSource,$Header,$WhHeader){
			// ต้อง Add ship เข้าไปด้วย 
			
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$model 					= new WarehouseMoving();   	
				$model->line_no 		= $Header->id;
				$model->source_id 		= $Header->saleOrder
											? ($Header->saleOrder->shipment 
												? $Header->saleOrder->shipment->id
												: $WhHeader->id)
											: $WhHeader->id;

				$model->DocumentNo		= $getSource->doc_no_;
				$model->PostingDate 	= date('Y-m-d H:i:s');
				$model->TypeOfDocument	= 'Sale';
				$model->SourceDoc 		= $getSource->order_id;
				$model->SourceDocNo		= $getSource->doc_no_;
				$model->item 			= $getSource->item;
				$model->ItemNo 			= $getSource->code_no_;		 
				$model->Description 	= $getSource->code_desc_;
				// Sale Ship ต้องเป็นค่า (-) เสมอ
				$model->Quantity 		= $getSource->quantity * -1;
				$model->QtyToMove 		= $getSource->quantity;
				$model->QtyMoved 		= $getSource->quantity;
				$model->QtyOutstanding	= 0;
				$model->unit_price 		= $getSource->unit_price;
				$model->DocumentDate 	= date('Y-m-d H:i:s');
				$model->session_id 		= Yii::$app->session->getId();
				$model->user_id 		= Yii::$app->user->identity->id;
				$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
				
				//$model->createCommand()->sql;
				//echo $model->$connection->createCommand()->getRawSql();
				$model->save(false);
				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				throw $e;
			}
    }

    public function postInvoice($id,$source)
    {
			$comp 	= Yii::$app->session->get('Rules')['comp_id'];
			$keys 	= 'posting&inv:'.$id."&comp".$comp;

			$transaction = Yii::$app->db->beginTransaction();
			try {
				//------- Header ----------	
				$model					= new RcInvoiceHeader();
				$model->no_				= $source->no_;
				$model->cust_no_		= $source->cust_no_;
				$model->cust_name_		= $source->cust_name_;
				$model->document_no_	= $source->document_no_;
				$model->posting_date 	= $source->posting_date;
				$model->doc_type 		= $source->doc_type;
				$model->order_id		= $source->order_id;
				$model->sales_people 	= $source->sales_people;
				$model->sale_id			= $source->sale_id;
				$model->cust_code 		= $source->cust_code;
				$model->order_date	 	= $source->order_date;
				$model->ship_date 		= $source->ship_date;
				$model->cust_address 	= $source->cust_address;
				$model->cust_address2	= $source->cust_address2;
				$model->contact			= $source->contact;
				$model->phone			= $source->phone;
				$model->district 		= $source->district;
				$model->city			= $source->city;
				$model->province		= $source->province;
				$model->postcode		= $source->postcode;
				$model->discount		= $source->discount;
				$model->percent_discount= $source->percent_discount;
				$model->vat_percent 	= $source->vat_percent;
				$model->include_vat		= $source->include_vat;
				$model->paymentdue		= $source->paymentdue;
				$model->payment_term 	= $source->payment_term;
				$model->ext_document	= $source->ext_document;
				$model->other_ref		= $source->other_ref;
				$model->remark			= $source->remark;
				$model->comments		= $source->comments;
				$model->status			= 'Posted';
				$model->ref_inv_header	= $source->id;
				$model->session_id 		= Yii::$app->session->getId();
				$model->user_id			= Yii::$app->user->identity->id;
				$model->comp_id			= $source->comp_id;
				$model->cn_reference	= $source->cn_reference;
				$model->host			= gethostname();

				if($model->save()){
					$rc_id = $model->id;
				}else{
					$rc_id = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
				}
				//------- /. Header ----------
					
				//------- Line -------------------	
				// ดึงรายการจาก Sale_Invoice_Line ไปใส่ใน Rc_Invoice_Line
				// เก็บ ประวัติ หลัง post invoice
				// Update Status Sale Order เป็น Shipped
				$Line = $this->postInvoiceLine($model,$id);
				//------- /. Line ----------------	


				//------- Tracking -------------------	
				// ถ้าดึงมาจากใบ SO 
				// ให้เก็บประวัติ
				if($source->order_id != ''){
					$SaleHeader = SaleHeader::findOne($model->order_id);
					if($SaleHeader !== null) {						
						FunctionTracking::CreateTracking(
									[
										'doc_type'          => 'Sale-Order',
										'doc_id'            => $SaleHeader->id,
										'doc_no'            => $SaleHeader->no,
										'doc_status'        => 'Shiped',
										'amount'            => $SaleHeader->balance,
										'remark'            => 'Current : '.$SaleHeader->status.', Status : Shiped',
										'track_for_table'   => 'sale_header',
										'track_for_id'      => $SaleHeader->id,
									]);
						$SaleHeader->status = 'Shiped';
						$SaleHeader->save();
					}
				}
				//------- /. Tracking ----------------	


				//----------- Stock --------------	 
				// ถ้ามี item คีย์ตรง		
				// ให้ไปตัด Stock ด้วย 
				$findInvLine    = SaleInvoiceLine::find()->where(['source_id' => $id])->andWhere(['source_line' => NULL])->all();
				if($findInvLine != null){

					$WhHeader                  	= new WarehouseHeader();
		
					$WhHeader->PostingDate		= $model->posting_date;  
					$WhHeader->ship_date       	= $model->ship_date;
					$WhHeader->DocumentDate 	= date('Y-m-d');
					$WhHeader->TypeOfDocument 	= 'Invoice';
					$WhHeader->Description     	= '';
					$WhHeader->SourceDocNo     	= $model->id;
					$WhHeader->DocumentNo 	    = $model->no_;					
					
					$WhHeader->customer_id     	= $model->cust_no_;
					$WhHeader->ship_to 			= 0;
					$WhHeader->status 			= 'Shiped';
					$WhHeader->remark          	= '';
					$WhHeader->contact         	= '';
					$WhHeader->user_id 			= Yii::$app->user->identity->id;
					$WhHeader->comp_id 			= Yii::$app->session->get('Rules')['comp_id'];
					$WhHeader->line_no 			= $model->id;

					
					if($WhHeader->save()){

						foreach ($findInvLine as $InvLine) {
							// ไม่ต้องตัด 31/09/19
							//$this->invenAdjust($InvLine,$model,$WhHeader);
						}
					}
				}		
				//----------- /.Stock --------------

				//----------- Receipt Check --------------	 
				// ตรวจสอบว่า มีการจ่ายชะระเงินแล้วหรือยัง
				// ถ้ามีแล้ว ให้ไป Update Invoice (Apply_to,Status)
				$this->validateReceipt($model);
				//----------- /.Receipt Check --------------	

				$transaction->commit();

				return (Object)[
					'status' 	=> 200,
					'id' 			=> $rc_id,
					'message' => 'done'
				];
			} catch (\Exception $e) {
				$transaction->rollBack();
				return (Object)[
					'status' 	=> 500,
					'model' 	=> 0,
					'message' => Yii::t('common','{:e}',[':e' => $e])
				];
			}
    }

    public function validateReceipt($data){
    	$query = \common\models\Cheque::find()
							->where(['apply_to_no' => $data->no_])
							->andWhere(['cust_no_' => $data->cust_no_])
							->andWhere(['apply_to_status' => 'Open'])
							->all();
    	if($query != null){
    		foreach ($query as $key => $value) {
    			$models 									= \common\models\Cheque::findOne($value->id);
    			$models->apply_to 				= $data->id;
					$models->apply_to_status 	= $data->status;
					$models->save(false);
    		};
		}
    }

   
	public function OrderTrackingRcSave($RcHeader,$SaleShiped)
	{

		// บันทึกประวัติ
		// บันทึกใน Shipment Status = Invoiced

	 	$remark 	= '';
		$table 		= 'customer';
		$forid 		= $RcHeader->cust_no_;
		$Shiped		= WarehouseHeader::find()->where(['DocumentNo'=>$SaleShiped])->one();

		if($Shiped != null){

			$remark 	= 'Ship : '.$Shiped->DocumentNo.', ';
			$table 		= 'sale_header';
			$forid 		= $Shiped->SourceDocNo;

			$Shiped->status = 'Invoiced';
			if($Shiped->save(false)){	

				$tracking 					= new OrderTracking();
				$tracking->event_date 		= date('Y-m-d H:i:s');
				$tracking->doc_type 		= 'Rc-Inv';
				$tracking->doc_id 			= $RcHeader->id;
				$tracking->doc_no 			= $RcHeader->no_;
				$tracking->doc_status 		= 'Posted';
				$tracking->amount			= $this->TotalBalance($RcHeader,'RcInvoiceLine');
				$tracking->remark			= $remark.'Customer : '.$RcHeader->cust_no_;  
				$tracking->ip_address		= $_SERVER['REMOTE_ADDR'];
				$tracking->lat_long			= '';
				$tracking->create_by		= Yii::$app->user->identity->id;
				$tracking->comp_id			= Yii::$app->session->get('Rules')['comp_id'];
				$tracking->track_for_table	= $table;
				$tracking->track_for_id			= $forid;

				if(!$tracking->save())
				{
					print_r($tracking->getErrors());
					exit();
				}
			}

		} 
		
	}




    public function postInvoiceLine($header,$id)
    {
		$logs 		= [];
		$transaction = Yii::$app->db->beginTransaction();
		try {
			// ดึงรายการจาก Sale_Invoice_Line ไปใส่ใน Rc_Invoice_Line
			$source 	= SaleInvoiceLine::find()->where(['source_id' => $id])->all();			
			foreach ($source as $value) {				
				$model 					= new RcInvoiceLine();				
				$model->type 			= $value->type;
				$model->item		 	= $value->item;
				$model->doc_no_			= $header->no_;
				$model->line_no_		= $value->id;
				$model->source_id		= $header->id;
				$model->posting_date 	= $header->posting_date;
				$model->customer_no_	= $header->cust_no_;
				$model->code_no_		= $value->code_no_;
				$model->code_desc_		= $value->code_desc_;
				$model->quantity 		= $value->quantity;
				$model->unit_price 		= $value->unit_price;
				$model->vat_percent 	= $value->vat_percent;
				$model->line_discount	= $value->line_discount;
				$model->order_id 		= $value->order_id;
				$model->source_doc		= $value->source_doc;
				$model->source_line		= $value->source_line;
				$model->session_id 		= Yii::$app->session->getId();
				$model->measure     	= $value->measure;
				$model->comp_id			= Yii::$app->session->get('Rules')['comp_id'];

				if($model->save()){
					$logs[] = [
						'status' 	=> 200,
						'id'		=> $model->id,
						'message'	=> 'done'
					];						
				}else{
					$transaction->rollBack();
					$logs[] = [
						'status'	=> 500,
						'id'		=> 0,
						'message'	=> json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
					];
				}

			}


			
			//นับดูว่า มี sale Order อยู่กี่ใบ
			$SINLine  	= SaleInvoiceLine::find()->select('source_doc as SaleShiped')->where(['source_id' => $id])->groupBy('source_doc')->all();

			if($SINLine != null){				 
				foreach ($SINLine as $key => $modal) {
					// บันทึกประวัติ ลงไปทีละใบ
					$this->OrderTrackingRcSave($header,$modal->SaleShiped);

				}
			}
			
			$transaction->commit();
			
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}

		return $logs;
	}
		


    public static function changeInvoiceNo($id,$val,$old)
    {

    
    	if(RcInvoiceHeader::find()->where(['no_' => $val])->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists())
    	{

    		#throw new NotFoundHttpException(Yii::t('common','Already exists.'));
    		
    			
    		// return "<script>swal(
	     //          '".Yii::t('common','Already exists.')." !',
	     //          '".Yii::t('common','Please try again......')."',
	     //          'error'
	     //        )</script>";
	        return $old;


    	}else {

    		$model = SaleInvoiceHeader::findOne($id);
    		$model->no_ = $val;
	    	$model->save(false);

	    	return $model->no_;
    	}	
    	

    }

    protected function findSaleHeader($id)
    {
        if (($model = SaleInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    public function ewRefresh($url,$time)
    {
    	echo '<script>setTimeout(function() {
                  window.location.href = "index.php?r='.$url.'";
                }, '.$time.');
                  </script>';

        echo "<script>swal(
              '".Yii::t('common','Success !')."',
              '".Yii::t('common','Refresh......')."',
              'success'
            )</script>";
    }
    

    public static function validateBillCreate($cust)
    {
    	 

    	$exists = SaleInvoiceHeader::find()
	    	->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
	    	->andWhere(['cust_no_' => $cust])
	    	->andWhere(['order_id' => $_GET['id']])
	    	->exists();

	    $RcHeader = RcInvoiceHeader::find()
	    	->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
	    	->andWhere(['cust_no_' => $cust])
	    	->andWhere(['order_id' => $_GET['id']])
	    	->exists();

    	if($exists)
    	{

    		return true;
    		
    	}else {

    		if($RcHeader){

    			return true;

    		}else {

    			return false;

    		}

    	}

    }


    public function updateSaleInvoiceLine($header)
    {
    	if(SaleInvoiceLine::find()->where(['session_id' => Yii::$app->session->getId()])->exists())
    	{

	    	$model = SaleInvoiceLine::updateAll([
				'source_id' => $header->id,
				'doc_no_' => $header->no_,
				'customer_no_' => $header->cust_no_,
				],['session_id' => Yii::$app->session->getId(),'source_id' => 0]);

    	}
    	//Yii::$app->db->createCommand('UPDATE post SET status=1 WHERE id=1')->execute();
    }
}