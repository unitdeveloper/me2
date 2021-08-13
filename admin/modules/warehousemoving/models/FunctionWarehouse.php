<?php

namespace admin\modules\warehousemoving\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;
use admin\models\Generater;

use common\models\WarehouseMoving;
use common\models\WarehouseHeader;
use common\models\SaleHeader;
use common\models\SaleLine;
use common\models\Address;
use common\models\Customer;
use common\models\Items;
use common\models\TransportList;
use common\models\OrderTracking;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionWarehouse extends Model
{
	public function JsonPost($params)
	{
		$decode = json_encode($params, true);
		return $decode;

	}


	public function CreateShipMent($params)
	{
		$id 			= 0;
		$NoSeries 		= '';		
		$status 		= 200;
		$message 		= Yii::t('common','Success');

		foreach ($params['qtytoship'] as  $value) {			
			$SaleLine 	= SaleLine::findOne($value['name']);

			$remain 			= $SaleLine->items->liveInven * 1; // จำนวนสินค้าคงเหลือ 
			$reserveInSaleLine 	= $SaleLine->items->getReserveNotMe($SaleLine->id) * 1; // จำนวนที่จอง 
			
			$compare 			= $remain - $reserveInSaleLine;
			// ถ้าจำนวนสินค้าที่เหลือ หักติดจอง(-) น้อยกว่า จำนวนที่ต้องการ 
			// ไม่ให้ตัดสต๊อก
			if($compare < $value['value']){					 

				try{
					$status 		= 500;
					$message 		= Yii::t('common','There are not enough products.');

					// Line Notify
					$bot =  \common\models\LineBot::findOne(2);
					$msg = $message."\r\n\r\n";
					$msg.= "Sale Order : ".$SaleLine->saleHeader->no."\r\n";
					$msg.= $SaleLine->items->master_code."\r\n";
					$msg.= $SaleLine->items->description_th."\r\n\r\n";

					
					$msg.= Yii::t('common','Need').' : ' .$value['value']."\r\n";
					$msg.= Yii::t('common','Remain').' : ' .$remain."\r\n";
					$msg.= Yii::t('common','Missing quantity').' : ' .abs($compare - $value['value'])."\r\n";
					
					$msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

					 
					$bot->notify_message($msg);					

				} catch (\Exception $e) {					 
					$status 		= 500;
					$message 		= Yii::t('common','{:e}',[':e' => $e]);	
				}

				return json_encode([
					'no' 		=> $NoSeries,
					'docid' 	=> $id,
					'status' 	=> $status,
					'message' 	=> $message,
					'suggestion'=> $SaleLine->items->description_th .' '.Yii::t('common','Missing quantity').' : ' .abs($compare - $value['value'])
				]);

				exit();
			}
		}

		$WHeader 			= $this->genShipHeader($params);

		if($WHeader != null){
			$NoSeries 		= $WHeader->DocumentNo;

			$transaction 	= Yii::$app->db->beginTransaction();
			try{
		
				$i 			= 0;
				foreach ($params['qtytoship'] as  $value) {
					$i++;
					$Quantity 	= $value['value'];
					$Whsum 		= 0;

					$SaleLine 	= SaleLine::findOne($value['name']);
					$SaleLine->quantity_to_ship = $SaleLine->quantity;
					$SaleLine->stock_reserve 	= 0;
					$SaleLine->save(false);

					$query 		= WarehouseMoving::find()
									->where(['SourceDoc'			=> $SaleLine->id])
									->andWhere(['TypeOfDocument'	=> 'Sale'])
									->andWhere(['comp_id' 			=> Yii::$app->session->get('Rules')['comp_id']]);

					$Whsum 	 	= $query->sum('Quantity') * -1; // กลับค่า​ (-) <-> (+)

					if($Quantity == 0){
						// Do nothing.
					}else {
						// จำนวนต้องไม่เกิน Sale Order
						if($Quantity <= $SaleLine->quantity ){
							// ยัง ship ไม่ครบ ให้ ship ต่อได้
							// นำจำนวนที่ส่งมา (เทียบกับ Sale Order)
							if($Whsum < $SaleLine->quantity){								
								$Ship = $this->genShipment($WHeader, $NoSeries, $SaleLine, $Quantity, $params, $Whsum);								
								// กลับไปปรับตัวเลขที่ ​Sale Line
								// เพื่อตรวจสอบว่า Ship ครบหรือยัง
								$SaleLine->quantity_to_ship 	= $Ship->QtyOutstanding;
								$SaleLine->quantity_shipped 	= $query->sum('QtyMoved');
								$SaleLine->save(false);
							}else {								
								//ส่งค่ากลับ
								//$data.= '<br>ครบ '.$i.'. ['.$value['name'].'] < '.$Whsum.'<br>';
							}
						}else{

							// Undo Ship
							if($Quantity < 0){
								//$data.= '<br>'.$i.'. '.$SaleLine->quantity.'<= ['.$Whsum.'] < '.$Quantity.'<br>';
								$Ship =  $this->genShipment($WHeader, $NoSeries, $SaleLine, $Quantity, $params, $Whsum);
								// กลับไปปรับตัวเลขที่ ​Sale Line
								// เพื่อตรวจสอบว่า Ship ครบหรือยัง
								$SaleLine->quantity_to_ship 	= $Ship->QtyOutstanding;
								$SaleLine->quantity_shipped 	= $query->sum('Quantity') - $Quantity;
								$SaleLine->save(false);

							}
						}
					}
				}


				$this->OrderTrackingSave($WHeader,$params);	
				$SaleHeader 				= SaleHeader::findOne($params['id']);
				$SaleHeader->shiped_date 	= date('Y-m-d H:i:s');
				$SaleHeader->save(); 

				$id 						= $WHeader->id;

				$transaction->commit();		

			} catch (\Exception $e) {
				$transaction->rollBack();
				$status 	= 500;
				$message 	= Yii::t('common','{:e}',[':e' => $e]);	
			}
		}else{
			$status 	= 500;
			$message 	= 'Error';	
		}
			
		return json_encode([
			'no' 		=> $NoSeries,
			'docid' 	=> $id,
			'status' 	=> $status,
			'message' 	=> $message
		]);

	}

	public function OrderTrackingSave($WHeader,$params)
	{

		$tracking = new OrderTracking();

		$tracking->event_date 		= date('Y-m-d H:i:s');
		$tracking->doc_type 		= 'Sale-Ship';
		$tracking->doc_id 			= $WHeader->id;
		$tracking->doc_no 			= $WHeader->DocumentNo;
		$tracking->doc_status 		= 'Shiped';
		$tracking->amount 			= WarehouseMoving::find()->where(['source_id' => $WHeader->id])->sum('Quantity * unit_price');
		$tracking->remark 			= 'Shipto : '.$WHeader->ship_to.', By : '.$WHeader->Description;
		$tracking->ip_address 		= $_SERVER['REMOTE_ADDR'];
		$tracking->lat_long 		= '';
		$tracking->create_by 		= Yii::$app->user->identity->id;
		$tracking->comp_id 			= Yii::$app->session->get('Rules')['comp_id'];
		$tracking->track_for_table 	= 'sale_header';
		$tracking->track_for_id 	= $params['id'];

		if(!$tracking->save())
		{
			print_r($tracking->getErrors());
			exit();
		}
	}

	public function genShipHeader($params)
	{

		$GenSeries 				= new Generater();
		$model 					= new WarehouseHeader();

		$getTransport			= isset($params['transport']) ?: $params['transport'];
		
		$SaleHeader 			= SaleHeader::findOne($params['id']);
		$SaleHeader->ship_date	= $params['shipdate'];

		$model->PostingDate		= date('Y-m-d H:i:s');
		$model->ship_date		= $params['shipdate'];
		$model->DocumentDate 	= date('Y-m-d');
		$model->TypeOfDocument 	= 'Sale';

		$model->customer_id 	= $params['custid'];
		$model->SourceDocNo 	= $params['id'];
		$model->SourceDoc 		= $params['no'];
		$model->order_id 		= $SaleHeader->id;
		$model->ship_to 		= $SaleHeader->customer_id;

		// Update Customer
		// Cancel Update 06/12/62
		// $customer 				= Customer::findOne($SaleHeader->customer_id);
		// $customer->default_transport = $params['transport'];
		// $customer->save(false);

		$Address 				= $this->getAddress($params);
		$transport				= TransportList::findOne($getTransport);

		$model->Description 	= $transport ? $transport->name . ' ' .$transport->address . ' ' .$transport->phone : '';
		$model->transport_id 	= $getTransport;
		$model->address 		= $Address->address;
		$model->address2 		= $Address->address2;
		$model->district 		= $Address->district;
		$model->city 			= $Address->city;
		$model->province 		= $Address->province;
		$model->postcode 		= $Address->postcode;
		$model->status 			= 'Shiped';

		$model->user_id 		= Yii::$app->user->identity->id;
		$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
		$model->line_no 		= $model->comp_id.$model->user_id.$params['id'];

		// Return Error when already exists
		if($this->completeShip($params)==false){
			$model->DocumentNo 		= $GenSeries->GenerateNoseries('Shipment',true);
			$model->save(false);
			$SaleHeader->save(false);

		}else {

			// return json_encode([
			// 	'status' => 500,
			// 	'message' => Yii::t('common','This line shipped')
			// ]);
			throw new NotFoundHttpException(Yii::t('common','This line is shipped'));


			exit();

		}


		return $model;

	}

	public function getAddress($params)
	{


		if($params['addrid']==0)
		{
			// Default
			// Return address from customer
			$model = Customer::findOne($params['custid']);
		}else {

			// Custom
			// Return from address customs.
			$model = Address::findOne($params['addrid']);
		}


		return $model;
	}

	public function genShipment($WHeader,$NoSeries,$SaleLine,$quantity,$params, $Whsum)
	{
		$Item 					= Items::findOne($SaleLine->item);

		$transaction = Yii::$app->db->beginTransaction();
        try {	

			$model 					= new WarehouseMoving();			

			$model->source_id 		= $WHeader->id;
			$model->DocumentNo		= $WHeader->DocumentNo;
			$model->PostingDate 	= date('Y-m-d H:i:s');
			$model->TypeOfDocument 	= 'Sale';
			$model->SourceDoc 		= $SaleLine->id;
			$model->SourceDocNo		= $SaleLine->order_no;
			$model->item 			= $Item->id;
			$model->ItemNo 			= $SaleLine->item_no;
			
			// ถ้าไม่มี Descripton ให้ไปเอาใน item card.
			$model->Description 	= $SaleLine->description ==''
										? $Item->description_th
										: $SaleLine->description;

			// Sale Ship ต้องเป็นค่า (-) เสมอ
			$model->Quantity 		= $quantity * -1;
			$model->QtyToMove 		= $SaleLine->quantity;
			$model->QtyMoved 		= $quantity;

			// Oudstanding คือ จำนวนที่ค้าง Ship
			// เอาจำนวนที่ต้องส่งทังหมด (-)ลบด้วย ผลรวมของ item มารวมกับ จำนวนล่าสุด
			$model->QtyOutstanding	= $SaleLine->quantity - ($Whsum + $quantity);

			$model->unit_price 		= $SaleLine->unit_price;
			$model->qty_per_unit	= $Item->quantity_per_unit;
			$model->DocumentDate 	= date('Y-m-d H:i:s');
			$model->user_id 		= Yii::$app->user->identity->id;
			$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
			$model->line_no 		= $model->comp_id.$model->user_id.$WHeader->id;
			$Locations 				= \common\models\Location::find()
										->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
										->one();
			$model->location 		= $Locations != null 
										? $Locations->id 
										: 1;

			$model->qty_before		= $Item->liveInven;
			$model->qty_after		= $model->qty_before + $model->Quantity;

			if($model->save()){
				
				// update item 
				$model->items->updateQty;
				// $item  = Items::findOne($model->item);
				// $item->last_stock = $model->qty_after;
				// $item->save(false);

				if($Item->notify_stock == 1){

                    try{ // Line Notify                                            
                                    
                        $bot =  \common\models\LineBot::findOne(2);
                        $msg = 'Shipment'."\r\n\r\n";
                        $msg.= $Item->master_code."\r\n";
						$msg.= $model->Description."\r\n\r\n";
						
						$msg.= Yii::t('common','SO').' : '.($WHeader->saleOrder ? $WHeader->saleOrder->no : '')."\r\n";
                        $msg.= Yii::t('common','Quantity').' : '.$model->Quantity."\r\n";
                        $msg.= Yii::t('common','Remain').' : '.$model->qty_after."\r\n\r\n";
                        $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                        
                        $bot->notify_message($msg);					
    
                    } catch (\Exception $e) {					 
                         
                        //$message 		= Yii::t('common','{:e}',[':e' => $e]);	
    
                    }	
    
				}

				$transaction->commit();
				
				return $model;
			}else {
				$transaction->rollBack();
				return (Object)[
					'status' 	=> 500,
					'message' 	=> json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
				];
				exit();
			}

			
		} catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
				'status' => 500,
				'message' => Yii::t('common','{:e}', [':e' => $e])
			]); 	
            //throw $e;
		}
		
		
	}

	public function completeShip($params)
	{

		$model 		= SaleLine::find()->where(['sourcedoc' => $params['id']]);
		$ShipAll 	= $model->sum('quantity_shipped');
		$ShipReq 	= $model->sum('quantity');


		if($ShipAll == $ShipReq)
		{
			return true;
		}else {
			return false;
		}


	}

	public function getShipmentHeader($id)
	{
		$model = WarehouseHeader::find()->where(['SourceDocNo' => $id])->andWhere(['TypeOfDocument' => 'Sale'])->all();
		return $model;

	}

	public function getShipmentLine($id)
	{
		$query = WarehouseMoving::find()->where(['source_id' => $id]);

		$sumLine =$query->sum('Quantity');


		return $sumLine;

	}


	public static function undoShip($source,$id,$quantity)
	{

		$model 			= new WarehouseMoving();

		$transaction 	= Yii::$app->db->beginTransaction();

        try {

			$Heading 				= WarehouseMoving::find()->where(['source_id' => $source->id,'id' => $id])->one();
			//$Item 		= Items::find()->where(['No' => $Heading->ItemNo])->one();

			$model->source_id 		= $source->id;
			$model->DocumentNo		= $Heading->DocumentNo;
			$model->PostingDate 	= date('Y-m-d H:i:s');
			$model->TypeOfDocument 	= $Heading->TypeOfDocument;
			$model->SourceDoc 		= $Heading->SourceDoc;
			$model->SourceDocNo		= $Heading->SourceDocNo;
			$model->ItemNo 			= $Heading->ItemNo;
			$model->Description 	= $Heading->Description;

			$model->item 			= $Heading->items->id;

			$model->DocumentDate 	= date('Y-m-d H:i:s');
			$model->user_id 		= Yii::$app->user->identity->id;
			$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
			$model->line_no 		= $Heading->source_id;
			$model->Quantity 		= $quantity;
			$model->QtyToMove 		= $Heading->QtyToMove;
			$model->QtyMoved 		= $quantity *-1;
			$model->apply_to 		= $Heading->id;
			$model->QtyOutstanding	= $model->QtyMoved - $model->QtyToMove;
			$matching 				= self::undoShipMatching($id);
			$model->matching 		= $matching->matching;
			
			$model->qty_per_unit	= $Heading->qty_per_unit;
			$model->unit_price		= $Heading->unit_price;

			$model->qty_before		= $Heading->items->liveInven;
			$model->qty_after		= $model->qty_before + $model->Quantity;

			if($quantity != 0){

				$SaleHeader 			= SaleHeader::findOne($source->order_id);
				$SaleHeader->status		= 'Checking';
				//$SaleHeader->confirm 	= 0; // ให้ confirm ใหม่
				$SaleHeader->live 		= 1;
				$SaleHeader->save();
				// ให้ confirm ใหม่
				SaleLine::updateAll(['confirm' => 0], ['sourcedoc' => $SaleHeader->id]);
				

				if(!$model->save()){
					//print_r($tracking->getErrors());
					$transaction->rollBack();
					return (Object)[
						'status' 	=> 500,
						'message'	=> $model->getErrors(),
						'value' 	=> [
								'id' 	=> $model->id,
								'item' 	=> $model->items->master_code,
								'qty' 	=> $model->Quantity,
						],
					];

				}else {
					
					// update item 
					$model->items->updateQty;
					// $item  = Items::findOne($model->item);
					// $item->last_stock = $model->qty_after;
					// $item->save(false);

					if($model->items->notify_stock == 1){

						try{ // Line Notify                                            
										
							$bot =  \common\models\LineBot::findOne(2);
							$msg = 'Undo Ship'."\r\n\r\n";
							$msg.= $model->items->master_code."\r\n";
							$msg.= $model->Description."\r\n\r\n";
							
							$msg.= Yii::t('common','SO').' : '.($SaleHeader->no)."\r\n";
							$msg.= Yii::t('common','Quantity').' : '.($model->Quantity * 1)."\r\n";
							$msg.= Yii::t('common','Remain').' : '.($model->qty_after * 1)."\r\n\r\n";
							$msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
							
							$bot->notify_message($msg);					
		
						} catch (\Exception $e) {					 
							 
							//$message 		= Yii::t('common','{:e}',[':e' => $e]);	
		
						}	
		
					}

					$transaction->commit();

					return (Object)[
						'status' 	=> 200,
						'message'	=> 'undo',
						'value' 	=> [
								'id' 	=> $model->id,
								'item' 	=> $model->items->master_code,
								'qty' 	=> $model->Quantity,
						],
					];

				}
				
			}
			
		} catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode([
				'status' => 500,
				'suggestion' => Yii::t('common','{:e}',[':e' => $e])
            ]); 
            //throw $e;
           
        }

	}

	public static function undoShipMatching($id)
	{

		$matching 			= 0;
		$query 				= WarehouseMoving::find()->select('matching')->orderBy('matching DESC')->one();
		$matching 			= $query->matching + 1;

		$model 				= WarehouseMoving::findOne($id);
		$model->matching 	= $matching;
		$model->save(false);

		$SaleLine 					= SaleLine::findOne($model->SourceDoc);
		if($SaleLine != null){			
			$SaleLine->quantity_shipped	= $SaleLine->quantity_shipped - ($model->Quantity *-1);
			$SaleLine->save(false);
		}

		return $model;
	}

}
