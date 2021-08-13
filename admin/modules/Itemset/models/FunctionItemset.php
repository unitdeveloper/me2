<?php

namespace admin\modules\Itemset\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

use common\models\Items;
use common\models\BomHeader;
use common\models\BomLine;
use common\models\WarehouseMoving;

use common\models\ItemsHasProperty;
use common\models\TmpMenuGroup;

use common\models\PropertyHasGroup;
 
/**
 * FunctionItemset represents the model behind the search form about `common\models\FunctionItemset`.
 */
class FunctionItemset extends Model
{
	// ต้องการหา จำนวนสินค้า
	// โดยการคำนวนความเป็นไปได้ ใน BOM

	public function calculateBom($item)
	{
		$model = Items::find()->where(['No' => $item])->one();

		$Bom = $this->ProMinBomLine($model->ProductionBom,0,0);
		return $Bom;

	}

	// มี Item อะไรบ้าง
	public function ProductionBomHeader($id)
	{
		$model = BomHeader::findOne($id);
		$Bom  = $this->ProductionBomLine($model->id);

		return $Bom;
	}



	public function ProductionBomLine($id)
	{
		$model = BomLine::find()->where(['bom_no' => $id])->all();
		$data = '';
		foreach ($model as $value) {
			$data += $this->itemInven($value['item_no']);
		}
		return $data;
	}

	public function countBomInItems($No,$Qty)
	{
		$model = Items::find()->where(['No' => $No])->one();
		if($model->ProductionBom!=''){
			$Bom  = self::ProMinBomLine($model->ProductionBom,0,$Qty);
			return $Bom;
		}else {			 
			return (Object)([
				'status' 	=> 200,
				'message' 	=> 'done',
				'value' 	=> $model != null ? $model->inven - $Qty : 0,
			]);
		}
	}
 

	static function ProMinBomLine($id,$loop,$Qty)
	{
		$data 	= BomLine::find()->where(['bom_no' => $id])->all();
		$xx 	= [];
		$max 	= 10;
		foreach ($data as $model) {
			$loop++;
			// มี BOM อีกหรือไม่
			if($model->items->ProductionBom!=''){
				// ถ้ามี BOM
				if($loop > $max){
					//-----Line notify-----
						// if($loop == $max){	
						// 	$cpuLoad = \common\models\Systeminfo::getServerLoad();										 
						// 	$bot     = \common\models\LineBot::findOne(2);
						// 	$message = "Error! Loop\r\n";
						// 	$message.= 'Warning : CPU '.$cpuLoad.' %'."\r\n";
						// 	$message.= "By : ".Yii::$app->user->identity->id.") ".Yii::$app->session->get('Rules')['name']."\r\n";
						// 	$message.= "Sale : ".Yii::$app->session->get('Rules')['sale_code']."\r\n";
						// 	$message.= "Module : Calculate minimum possible item, Bom : [".$id."]";
							 
						// 	$bot->notify_message($message);
						// }
					//----- /.Line notify-----
					
					return (Object)([
						'status' 	=> 500,
						'message' 	=> 'Error! Loop'.' ['.$loop.']',
						'value' 	=> 0
					]);
					break;
				}else {
					$xx[] = self::validateBom($model->items,$loop,$Qty);
				}
			}else {
				// เอาจำนวนสินค้าคงเหลือ ลบกับ จำนวนที่ใช้ ใน BOM
				$xx[] =  ($model->items->inven - $model->base_unit)  - $Qty ; // +1 เพื่อให้ค่าไม่เพี้ยน
			}
		}
		return (Object)([
			'status' 	=> 200,
			'message' 	=> 'done',
			'value' 	=> $xx ? (int)min($xx) +1 : 0,
			]);
	}
	static function validateBom($Items,$loop,$Qty)
	{
		//$Items = Items::find()->where(['No' => $No])->one();		
		if($Items->ProductionBom!='')
		{
			// ถ้ามี Bom
			// เอาจำนวนใน BOM รวมกับ ​Inventory
			$Bom = self::ProMinBomLine($Items->ProductionBom,$loop,$Qty);
			return ($Bom->value  +  $Items->inven ) ;
		}else {
			// ถ้าไม่มี BOM ส่งจำนวนใน Inventory กลับ
			return $Items->inven - $Qty;
		}
	}

	// หาผลรวมของสินค้าคงคลัง
	public function itemInven($No)
	{
		$Items = Items::find()->where(['No' => $No])->one();
		$Query = WarehouseMoving::find()->where(['ItemNo' => $No]);
		$Inven = $Query->sum('Quantity');
		return $Inven  + $Items->Inventory;
	}

	public function sumInven($No)
	{
		$Query = WarehouseMoving::find()->where(['ItemNo' => $No]);
		$Inven = $Query->sum('Quantity');
		return $Inven;
	}

	public function Inventory($No)
	{
		$Items = Items::find()->where(['No' => $No])->one();
		// ถ้ามี BOM
 		if($Items->ProductionBom!='')
 		{
 			return $Items->Inventory;
 		}else {
 			return 0;
 		}
	}

	public function ItemSet($items)
	{
		$model = ItemsHasProperty::find()
        ->where(['Items_No' => $items])
        ->all();
        foreach ($model as $value) {
			$tmp_menu = new TmpMenuGroup();
        	$tmp_menu->item 			= $value->Items_No;
        	$tmp_menu->property 		= $value->property->description;
        	$tmp_menu->property_id 		= $value->property->id;
        	$tmp_menu->property_value 	= $value->values;
        	$tmp_menu->user_id			= Yii::$app->user->identity->id;
        	$tmp_menu->session_id		= Yii::$app->session->getId();
        	$tmp_menu->itemset 			= $value->itemsNo->itemset;
			$tmp_menu->priority 		= $value->priority;
			$tmp_menu->item_group 		= $value->itemsNo->ItemGroup;
        	$tmp_menu->save(false);
        }
	}

	public function getItemSetLoad()
	{
		if(isset($_POST['param']['pset'])){
			$set = $_POST['param']['pset'];
		}else {
			$set = NULL;
		}

		// 
		$query = TmpMenuGroup::find()
		->select('property_id')
		->where(['session_id' => Yii::$app->session->getId()])
		->groupBy('property_id');


		$model = PropertyHasGroup::find()
		->where(['property' => $query])
		->andWhere(['itemgroup' => PropertyHasGroup::getGroup()])
		->orderBy(['priority' => SORT_ASC])
		->all();


		$div = '';
		foreach ($model as $value) {
			$div.= '<div class=" ">
						<label class="box-header">'.$value->propertytb->description.'</label>
						'.self::getPropertyListMe($value['property'],$set).'
					</div>';
		}

    	return $div;

	}

	static function getPropertyListMe($property_id,$set)
	{

		if(isset($_POST['param']['pval']))
		{
			$pval = $_POST['param']['pval'];
		}else {
			$pval = NULL;
		}
		if(isset($_POST['param']['pset'])){
			$set = $_POST['param']['pset'];
		}else {
			$set = NULL;
		}

		//$model = TmpMenuGroup::find()->where([''])->all();
		$sql = "SELECT property_value FROM tmp_menu_group
				WHERE session_id = '".Yii::$app->session->getId()."'
				AND property_id = ".$property_id."
				GROUP BY property_value
				order by property_value asc
			";

        $model = Yii::$app->db->createCommand($sql)->queryAll();
        $count = count($model);
        $div = '<div id="selector" class="btn-group-x" data-toggle="buttons">';

        foreach ($model as $value) {

        	if( $value['property_value'] == $pval)
        	{
        		$btn_style = "btn-info";
        	}else {
        		$btn_style = "btn-default";
        	}

        	// ถ้ามีลูก ให้ค้นต่อ ถ้าไม่มี ให้แสดง Link

        	if(TmpMenuGroup::find()->where(['property_value' => $value['property_value'],'session_id' => Yii::$app->session->getId() ])->count()>1)
        	{
        		//$div.= '<div class="row">';
    			$div.= '<a href="javascript:void(0);"  class="_radio btn '.$btn_style.' btn-lg btn-flat  ew-action-my-item" ew-radio-id="'.$property_id.'" ew-radio-val="'.$value['property_value'].'">
    								<input type="radio" name="itemno" style="width:5px;">
    								'.$value['property_value'].'
    							</a>';
    			//$div.= '</div>';
    		}else {
    			// SHow Link Item
    			$div.= self::checkChildMenu($value,$set,$property_id);
    		}



        }
        $div.= '</div>';
        return $div;

	}

	static function checkChildMenu($model,$set,$property_id)
	{
		// $sql = "select * from tmp_menu_group where item in
		// 		(

		// 		SELECT
		// 		      item

		// 		  FROM tmp_menu_group
		// 		  where  property_value = '".$model['property_value']."'
		// 		  AND itemset = '".$set."'
		// 		  AND session_id = '".Yii::$app->session->getId()."'
		// 		  )
		// 		AND itemset = '".$set."'
		// 		and property_id = '".$property_id."'
		// 		AND session_id = '".Yii::$app->session->getId()."'
		// 	";		 
        //$models = Yii::$app->db->createCommand($sql)->queryAll();
 

		$subQuery = TmpMenuGroup::find()
		->select('item')
		->where(['property_value' => $model['property_value']])
		->andWhere(['itemset' => $set])
		->andWhere(['session_id' => Yii::$app->session->getId()]);

		$models = TmpMenuGroup::find()
		->where(['item' => $subQuery])
		->andWhere(['itemset' => $set])
		->andWhere(['property_id' => $property_id])
		->andWhere(['session_id' => Yii::$app->session->getId()])
		->all();
		
		$div = '';
		foreach ($models as $value) {

			//$div.= '<div class="row">';
			$div.= '<a href="javascript:void(0);"  class="_radio btn btn-default btn-lg ew-action-item btn-flat" ew-radio-id="'.$property_id.'" ew-radio-item="'.$value['item'].'" ew-radio-val="'.$value['property_value'].'">
			    			<input type="radio" name="itemno" style="width:5px;">
			    			'.$value['property_value'].'
		    			</a>';
    		//$div.= '</div>';
		}
		return $div;
	}

	public function lastprice($item){
		$model = \common\models\SaleLine::find()
				->where(['user_id' => Yii::$app->user->identity->id])
				->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
				->andWhere(['item_no' => $item])
				->one();
		if($model){
			return $model;
		}else {
			return (Object)['unit_price' => 0];
		}

	}



	public function getItemSet()
	{
		//$model = TmpMenuGroup::find()->where([''])->all();
		if(isset($_POST['param']['pid']))
		{
			$pid = $_POST['param']['pid'];
		}else {
			$pid = NULL;
		}
		if(isset($_POST['param']['pset'])){
			$set = $_POST['param']['pset'];
		}else {
			$set = NULL;
		}
		$query = TmpMenuGroup::find()
		->select('property_id')
		->where(['session_id' => Yii::$app->session->getId()])
		->andWhere(['itemset' => $set])
		->groupBy('property_id');

		$model = PropertyHasGroup::find()
		->where(['property' => $query])
		->andWhere(['itemgroup' => PropertyHasGroup::getGroup()])
		->orderBy(['priority' => SORT_ASC])
		->all();


        $count = count($model);
        $div = '';
        $i = 0;
        foreach ($model as $value) {

        	$i++;
			$div.= '<div class=" "><label class="box-header">'.$value->propertytb->description.'</label>';
        	if($i == 1)
        	{
        		$div.= self::getPropertyListMe($value['property'],$set);

        	}else if($i == $count){
        			// Check Count > 0

        			// ค้นหา ว่ามีลูกเพ่ิมใหม?
        			// ถ้ามี ให้เข้าไปค้นอีกครั้ง
	        		if(self::getPropertyList($value['property'],$set)['count']>0)
	        		{
	        			// List Again
	        			$div.= self::getPropertyList($value['property'],$set)['div'];
	        		}else {
	        			// End
	        			$div.= self::getPropertyListMe($value['property'],$set);
	        		}
        	}else {

        		$div.= self::getPropertyListMe($value['property'],$set);
        	}
        	$div.= '</div>';
        }
        return $div;
	}



	static function getPropertyList($property_id,$set)
	{

		if(isset($_POST['param']['pval']))
		{
			$pval = $_POST['param']['pval'];
		}else {
			$pval = NULL;
		}
		//$model = TmpMenuGroup::find()->where([''])->all();
		$sql = "select * from tmp_menu_group where item in
				(

				SELECT
				      item

				  FROM tmp_menu_group
				  where  property_value = '".$pval."'
				  AND itemset = '".$set."'
				  AND session_id = '".Yii::$app->session->getId()."'
				  )
				AND itemset = '".$set."'
				and property_value != '".$pval."'
				and property_id = '".$property_id."'
				AND session_id = '".Yii::$app->session->getId()."'
			";
		//echo $sql;
        $model = Yii::$app->db->createCommand($sql)->queryAll();

        $count = count($model);

        if($count>0)
        {
        	// Add to Sale Line
        	$class= "ew-action-item";
        }else {
        	$class= "ew-action-my-item";
        	//	$class= "ew-action-item";
        }
        $div = '<div id="selector" class="btn-group-x" data-toggle="buttons">';
        foreach ($model as $value) {

			if( $value['property_value'] == $pval)
        	{
        		$btn_style = "btn-info";
        	}else {
        		$btn_style = "btn-default";
        	}

        	//$div.= '<div class="row">';
    		$div.= '<a href="javascript:void(0);" data-rippleria class="_radio btn btn-default btn-lg '.$btn_style.'  '.$class.' btn-flat"  ew-radio-id="'.$property_id.'" ew-radio-item="'.$value['item'].'" ew-radio-val="'.$value['property_value'].'">
						<input type="radio" name="itemno" style="width:5px;">
						'.$value['property_value'].'
    				</a>';
    		//$div.= '</div>';

        }
        $div.= '</div>';
        return ['div'=> $div,'count' =>$count];

	}
}
