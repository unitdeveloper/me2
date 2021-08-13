<?php

namespace admin\modules\Manufacturing\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;


use admin\modules\Manufacturing\models\KitbomHeader;
use admin\modules\Manufacturing\models\KitbomLine;

use common\models\Items;
use common\models\BomHeader;
use common\models\BomLine;
use common\models\ItemMystore as Itemmystore;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionManufac extends Model 
{

	public $BomNo;
	public $SetID;

	public function ValidateNewCreate($param)
	{
		$status 	= 200;
		$message 	= '';
		$id 		= 0;
		$transaction = Yii::$app->db->beginTransaction();
        try {

			// Create New Item
			$NewCode 		= $this->generateItemCode($param);
			

			$status 		= 200;
			$message		= [];
			
			$OldCode 		= $param['item'];
			$this->SetID 	= $param['id'];

			if($NewCode->status == 200){	
				$id 		= 	$NewCode->id;
				 
			}else{
				$status 	= $NewCode->status;				
				$message 	= $NewCode->message;
			}
			
			// Create & Update BOM
			$data 			= json_decode($param['post'], true);
			$message		= $this->CreateBom($data,$NewCode,$OldCode,$param);
			$transaction->commit();

        } catch (\Exception $e) {
			$transaction->rollBack();
			$message 	= Yii::t('common','{:e}',[':e' => $e]);
			$status 	= 500;
		}
		
		return json_encode([
			'status' 	=> $status,
			'message' 	=> $message,
			'bom' 		=> $this->BomNo,
			'item' 		=> $id
		]);
		 
	}

	public function generateItemCode($param)
	{
		$desc		= $param['desc'];
		$Header 	= KitbomHeader::findOne($param['group']);
		$BomLine 	= KitbomLine::findOne(['kitbom_no' => $Header->id]);
		$Price 		= $param['price'];
		$last 		= '';
		$NewCode 	= '';
		$CreateNew 	= '';

		if($Header != null){
			if($Header->format_gen == ''){					
				return (Object)[
					'status'	=> 500,
					'message'	=> $Header->id,
					'messages'	=> Yii::t('common','Error ! ไม่ได้ตั้งค่า การรันสินค้า'),
					'value'		=> ''
				];		
				exit();
			}
		 
			if($BomLine===null){
				return (Object)[
					'status'	=> 500,
					'message'	=> 'Error No bom line',
					'value'		=> $Header->id
				];
				exit();
			}
		

			// ดึงข้อมุล Item ที่อยู่ใน KitBom
			$Items = Items::findOne($BomLine->item);
			// $Items = Items::find()
			// ->where(['No' => $BomLine->item_no])
			// ->orderBy(['master_code' => SORT_ASC])
			// ->one();

			// ดึงรายการ ล่าสุด โดยเอา ​​Fortmat ในการสร้าง Item มาเที่ยบ
			// Update 20/09/2017
			$LastCode = Items::find()->select('master_code')
						->where(['like', 'master_code', $Header->format_gen])
						->orderBy(['id' => SORT_DESC])
						->one();

			// ตัดตัวอักษรตามจำจวนที่ตั้ง format_gen
				
			$new_code_ 	= 	$LastCode ? $LastCode->master_code : $Header->format_gen;

			// หาตัวเลขตัวสุดท้าย
			// แล้วจะได้ตัวเลข Runing นำมา + 1 เพื่อไปสร้าง Item ใหม่	
			$lastN 		= (int)substr($new_code_, strlen($Header->format_gen)) +1;
			// var_dump($new_code_);
			// var_dump($new_code_);
			// var_dump(substr($new_code_, strlen($Header->format_gen))); exit;

			// ใส่ 0 นำหน้าตัวเลข ตามจำนวนตัวเลขที่รัน
			$last 		= str_pad(($lastN == 0 ? 1 : $lastN),$Header->running_digit , "0", STR_PAD_LEFT);

			// ถ้าไม่ได้ตั้งรูป ให้ดึงจาก Item ต้นฉบับ
			$Header->photo = $Header->photo == '' 
								? ($Items ? $Items->Photo  : $Header->photo)
								: $Header->photo ;

			// ตรวจสอบเพื่อป้องกันความผิดพลาด
			// เพื่อหาว่า มี master_code นี้แล้วหรือยัง
			$NewCode 		= $Header->format_gen.$last;
			$CodeWithComp 	= str_pad(Yii::$app->session->get('Rules')['comp_id'],2,"0",STR_PAD_LEFT).'^'.$NewCode;

			if(!$this->validateItemCode($CodeWithComp,$desc)){

				$CreateNew  = [ 
						'master_code' 			=> $NewCode ,
						'category' 				=> $Items->category, 
						'Photo' 				=> $Header->photo,
						'Description'			=> $desc,
						'PriceStructure_ID'  	=> $Items->PriceStructure_ID,
						'UnitOfMeasure' 		=> $Items->UnitOfMeasure,
						'UnitCost' 				=> $Items->UnitCost,
						'ItemGroup' 			=> $Items->ItemGroup,
						'TypeOfProduct' 		=> $Items->TypeOfProduct,
						'CostingMethod' 		=> $Items->CostingMethod,
						'StandardCost' 			=> $Price,
						'ProductionBom' 		=> $NewCode,
						'unit_of_measure'		=> $Items->unit_of_measure,
						'quantity_per_unit'		=> $Items->quantity_per_unit,
						'No' 					=> str_pad(Yii::$app->session->get('Rules')['comp_id'],2,"0",STR_PAD_LEFT).'^'.$NewCode,
						'status' 				=> 200

						];
				return $this->CreateItem($CreateNew);
				
			}else {

				$Item 	= Items::findOne(['Description' => $desc]);
				$C 		= str_pad(Yii::$app->session->get('Rules')['comp_id'],2,"0",STR_PAD_LEFT).'^'.$NewCode;
				// $Item = Items::find()
				// 		->where(['or', 
				// 			['No' => $C],
				// 			['Description' => $desc],
				// 			['description_th' => $desc]
				// 		])
				// 		->one();

				$this->BomNo = $Item != null 
								? $Item->ProductionBom 
								: null;

				if($Item != null){

					$Item->status 	= 200;
					$Item->message 	= '';
					return $Item;

				}else{
				
					return (Object)[
						'status'	=> 404, 				
						'message'	=> [
										'code' 		=> $Item != null 
														? $Item->master_code 
														: null,
										'message' 	=> 'Exists  '. $C .' '. $desc,
										'id' 		=> $Item != null 
														? $Item->id 
														: null
									],
						'id' 		=> $Item != null 
										? $Item->id 
										: null
					];
				}

				exit;
				
			}

		}else{
			return (Object)[
				'status'	=> 500,
				'message'	=> 'Error No bom header',
				'value'		=> 0
			];
			exit(); 
		}
		
	}
	
	public function validateItemCode($No,$desc){

		return Items::find()
    			->where(['master_code' => $No])
    			->orWhere(['Description' => $desc])
    			->orWhere(['description_th' => $desc])
				->exists();
		 
	}


	public function CreateBom($Data,$NewCode,$OldCode,$param)
	{
		
		$this->BomNo = $OldCode;
		
		// ถ้า Item ถูกสร้างมาใหม่
		// New Item
		if($NewCode->status == 200){

			if(BomHeader::find()->where(['id' => $NewCode->ProductionBom])->exists()){
				$Bom = self::UpdateBomNo($NewCode);	
			}else {				
				$Bom = $this->CreateBomNo($NewCode);
				if($Bom){
					foreach ($Data as $key => $value) {
						$this->CreateBomLine($value);
					}
				}
			}

			// Update Items 
			$NewCode->UnitCost 			= $Bom->unitcost;
			$NewCode->StandardCost 		= $Bom->unitcost;
			$NewCode->CostGP 			= 0;
			$NewCode->ProductionBom 	= $Bom->id;
			// if($NewCode->StandardCost <= 0){
			// 	$NewCode->StandardCost 	= $param['price'];
			// } 
			
			$NewCode->save(false);

			Yii::$app->session->set('item_no',$NewCode->No);

			return (Object)([
				'status'	=> 200,
				'message'	=> 'create',
				'value'		=> Yii::$app->session->get('item_no'),
				'id'		=> $NewCode->id,
				'code' 		=> $NewCode->master_code
			]);
			 
		}else {

			// Already Item
			// ถ้า เป็น item ที่มีอยู่แล้ว
			// หาว่ามีใน Bom ไหม?
			$NewItem = Items::find()->where(['No' => $OldCode])->one();

			

			if(BomHeader::find()->where(['id'=> $NewItem != null ? $NewItem->ProductionBom : 0])->exists()){

				// ถ้ามี BOM อยู่แล้วให้ Update
				$Bom = self::UpdateBomNo($NewItem);
 				foreach ($Data as $key => $value){
					$this->UpdateBomLine($value);
 					// if(BomLine::find()->where(['bom_no' => $Bom->id,'item_no' => $value['c']])->exists()){
 					// 	$this->UpdateBomLine($value);
 					// }else {
 					// 	$this->CreateBomLine($value);
 					// }
				}

				// Update Items	
				$NewItem->UnitCost 			= $Bom->unitcost;			 
				$NewItem->StandardCost 		= $Bom->unitcost;
				$NewItem->CostGP 			= 0;
				$NewItem->ProductionBom 	= $Bom->id;
				$NewItem->save(false); 


			}else {
				
				// ถ้าไม่มี BOM ให้สร้างใหม่
				// โดยเอาข้อมูลจาก Item table มาสร้าง
				$Bom = $this->CreateBomNo($NewItem);	
				if($Bom){
					foreach ($Data as $key => $value) {
						$this->CreateBomLine($value);					
					}
				
					// Update Items	
					$NewItem->UnitCost 			= $Bom ? $Bom->unitcost : 0;			 
					$NewItem->StandardCost 		= $Bom ? $Bom->unitcost : 0;
					$NewItem->CostGP 			= 0;
					$NewItem->ProductionBom 	= $Bom ? $Bom->id : '';
					$NewItem->save(false); 
				}

			}

			Yii::$app->session->set('item_no',$NewItem ? $NewItem->No : '');

			return (Object)([
				'status' 	=> 200,
				'message' 	=> 'update',
				'value' 	=> $NewItem ? $NewItem->No : '',
				'id' 		=> $NewItem ? $NewItem->id: '',
				'code' 		=> $NewItem ? $NewItem->master_code : $OldCode,
				'bom' 		=> $Bom ? $Bom->id : '',
				'event' 	=> $NewCode
			]);
			 
		}

	}

	public function CreateBomNo($NewCode)
	{
		if($NewCode != null){
			$Header 				= new BomHeader();
			$Header->item 			= $NewCode->id;
			$Header->code 			= $NewCode->master_code;
			$Header->name 			= $NewCode->Description;
			$Header->description 	= $NewCode->Description;
			$Header->create_date 	= date('Y-m-d H:i:s');
			$Header->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
			$Header->user_id		= Yii::$app->user->identity->id;

			
			$Header->save(false);
			$this->BomNo = $Header->id;
			

			return $Header;
		}else{
			return false;
		}
	}

	protected function UpdateBomNo($item)
	{
		//$Header = BomHeader::find()->where(['code'=>$validate])->one();
		$Header 				= BomHeader::findOne($item->ProductionBom);

		$Header->item 			= $item->id;
		$Header->code 			= $item->master_code;
		$Header->name 			= $item->Description;
		$Header->description 	= $item->Description;
		$Header->modify_date 	= date('Y-m-d H:i:s');
		$Header->modify_by 		= Yii::$app->user->identity->id;
		
		
		$Header->save(false);
		$this->BomNo = $Header->id;
		
		

		return $Header;
	}

	public function CreateBomLine($value)
	{
		$status 	= 200;
		$message    = '';
		$Line 		= new BomLine();
		//$Items 	= Items::find()->where(['like', 'No', $value['c']])->one(); // Disabled 02/07/2020
		$Items 	= Items::findOne($value['id']); // Enabled 02/07/2020
		if($Items != null){		
			$Line->bom_no			= $this->BomNo;
			$Line->item 			= $Items->id;
			$Line->item_no 			= $Items->No;
			$Line->name 			= $Items->Description;
			$Line->description 		= $Items->Description;
			$Line->quantity 		= $value['q'];
			$Line->base_unit 		= $value['q'];
			$Line->measure 			= $Items->UnitOfMeasure;
			$Line->comp_id 			= Yii::$app->session->get('Rules')['comp_id'];
			$Line->user_id			= Yii::$app->user->identity->id;

			if(!$Line->save()){
				$status 	= 500;
				$message    = json_encode($Line->getErrors(),JSON_UNESCAPED_UNICODE);
			};
		}
		 
		return (Object)[
			'status' => $status,
			'message' => $message,
			'model' => $Line
		];
	}
	public function UpdateBomLine($value)
	{
		$Line 	= BomLine::find()->where(['bom_no' => $this->BomNo,'item_no' => $value['c']])->one();

		$Items 	= Items::find()->where(['No'=>$value['c']])->one();

		if($Items!=null){
		
			$Line->bom_no			= $this->BomNo;
			$Line->item 			= $Items->id;
			$Line->item_no 			= $Items->No;
			$Line->name 			= $Items->Description;
			$Line->description 		= $Items->Description;
			$Line->quantity 		= $value['q'];
			$Line->base_unit 		= $value['q'];
			$Line->measure 			= $Items->UnitOfMeasure;
			$Line->comp_id 			= Yii::$app->session->get('Rules')['comp_id'];
			$Line->user_id			= Yii::$app->user->identity->id;

			$Line->save(false);

		} 

	}

	public function CreateItem($Items)
	{
		$model = new Items();

		$model->No 					= $Items['No'];
		$model->Description 		= $Items['Description'];
		$model->description_th 		= $Items['Description'];
		$model->master_code 		= $Items['master_code'];
		$model->category 			= $Items['category'];
		$model->Photo 				= $Items['Photo'];
		$model->PriceStructure_ID  	= $Items['PriceStructure_ID'];
		$model->UnitOfMeasure 		= $Items['UnitOfMeasure'];
		$model->UnitCost 			= $Items['UnitCost'];
		$model->ItemGroup 			= $Items['ItemGroup'];
		$model->TypeOfProduct 		= $Items['TypeOfProduct'];
		$model->CostGP 				= $Items['StandardCost'];
		$model->CostingMethod 		= $Items['CostingMethod'];
		$model->StandardCost 		= $Items['StandardCost'];
		$model->ProductionBom 		= $this->BomNo;
		$model->date_added 			= date('Y-m-d H:i:s');
		$model->detail 				= '';
		$model->Status 				= '1';
		$model->product_group 		= 'FG';
		$model->replenishment 		= 'Produce';
		$model->company_id 			= Yii::$app->session->get('Rules')['comp_id'];
		$model->user_id 			= Yii::$app->user->identity->id;
		$model->unit_of_measure     = $Items['unit_of_measure'];
		$model->quantity_per_unit   = $Items['quantity_per_unit'];
		$model->interesting 		= 0;
		$model->create_by			= 'Automatic created';
		



		if(!$this->validateItemCode($Items['No'], $Items['Description'])){
			$model->save(false);
			$model->status 	= 200;
			$myStore 		= self::cloneItem($model,['clone'=>0]);

			return $model;
		}else {
			return (Object)[
				'status' => 500, 
				'detail' => 'Item code already exists.',
				'message' => 'Error : Item code already exists.'
			];
		}

		
	}

	protected function cloneItem($item,$params){
        $param      = self::array_to_object($params);
        $model      = new Itemmystore();

        $model->item              = $item->id;
        $model->item_no           = $item->No;
        $model->master_code       = $item->master_code;
        $model->barcode           = $item->barcode;
        $model->user_added        = Yii::$app->user->identity->id;
        $model->comp_id           = Yii::$app->session->get('Rules')['comp_id'];
        $model->name              = $item->description_th;
        $model->name_en           = $item->Description;
        $model->detail            = $item->detail;
        $model->date_added        = date('Y-m-d H:i:s');
        $model->unit_cost         = ($item->UnitCost)? $item->UnitCost : ($item->StandardCost)? $item->StandardCost : 0 ;
        $model->sale_price        = ($item->CostGP)? $item->CostGP : 0;
        $model->unit_of_measure   = $item->unit_of_measure;  
        $model->qty_per_unit      = $item->quantity_per_unit;
        $model->clone             = ($param->clone!==null)? $param->clone : 1;

        if($model->save()){
            return $model;
        }
	}
	
    protected function array_to_object($array) {
        return (object) $array;
    }
    protected function object_to_array($object) {
        return (array) $object;
	}
	
}