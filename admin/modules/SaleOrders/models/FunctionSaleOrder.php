<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

use common\models\Items;
use common\models\ItemsHasProperty;
use common\models\Property;
use common\models\Itemset;
use common\models\Itemgroup;

use common\models\TmpMenuGroup;
use common\models\PropertyHasGroup;
use common\models\SaleLine;
use common\models\SaleHeader;
use common\models\VatType;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionSaleOrder extends Model
{
	public static function ItemSet($items)
	{
		//$model = new Itemset();

		$model = ItemsHasProperty::find()
        ->where(['item' => $items])
        ->all();

        foreach ($model as $value) {

			$tmp_menu = new TmpMenuGroup();
			$tmp_menu->item 			= $value->Items_No;
			$tmp_menu->item_id 			= $value->item;
			$tmp_menu->property 		= $value->property->description;
			$tmp_menu->property_id 		= $value->property->id;
			$tmp_menu->property_value 	= $value->values;
			$tmp_menu->user_id			= Yii::$app->user->identity->id;
			$tmp_menu->session_id		= Yii::$app->session->getId();
			$tmp_menu->itemset 			= $value->items != null 
											? $value->items->itemset
											: 0;
			$tmp_menu->priority 		= $value->priority;
			$tmp_menu->item_group 		= $value->items != null 
											? $value->items->ItemGroup
											: 0;

        	$tmp_menu->save(false);


        }

	}

	public static function getItemSetLoad() {
		$param	= Yii::$app->request->post('param');
		$keys	= 'itemSetLoad&set:'.$param['pset'].'&sid:'.Yii::$app->session->getId();
        $cache	= Yii::$app->cache;

		if(!$cache->get($keys)){

			$query = TmpMenuGroup::find()
			->select('property_id')
			->where(['session_id' => Yii::$app->session->getId()])
			->groupBy('property_id');

			$model = PropertyHasGroup::find()
			->where(['property' => $query])
			->andWhere(['itemgroup' => PropertyHasGroup::getGroup()])
			->orderBy(['priority' => SORT_ASC])
			->all();

			$div 	= '';
			$i 		= 0;
			foreach ($model as $value) {
				$i++;				
				if($i==1)
				$div.= '<div class=" ">
							<label class="box-header">'.$value->propertytb->description.'</label>';							
				$div.= 		self::getPropertyListMe($value['property'],$param['pset'],(Object)['i' => $i,'count' => count($model)]);
				$div.= '</div>';				
			}

			$cache->set($keys, $div, 60);
		}

		return $cache->get($keys);
	}


	public static function getPropertyListMe($property_id,$set,$data)
	{

		if(isset($_POST['param']['pval']))
		{
			$pval = $_POST['param']['pval'];
		}else {
			$pval = NULL;
		}
		 

		$model = TmpMenuGroup::find()
		->select('property_value')
		->where(['session_id' => Yii::$app->session->getId()])
		->andWhere(['property_id' => $property_id])
		->groupBy('property_value')
		->orderBy(['property_value' => SORT_ASC])
		->all();

		$count = count($model);

		
        $div = '<div id="selector" class="btn-group-x" data-toggle="buttons">';
		
        foreach ($model as $value) {

        	if( $value['property_value'] == $pval)
        	{
				$btn_style 	= "btn-info";
				$btn_icon 	= '<i class="far fa-check-square"></i>';
        	}else {
				$btn_style 	= "btn-default";
				$btn_icon 	= '<i class="far fa-square"></i>';
			}
			if($data->i == 1) {

				$countAll = TmpMenuGroup::find()
				->where(['property_value' => $value['property_value']])
				->where(['session_id' => Yii::$app->session->getId()])
				->count();
 
				if ($data->count <=1) {
					$div.= self::checkChildMenu($value,$set,$property_id);
				}else {

					if ($countAll > 1 ) {
						// NEXT ->>  
						$div.= '<a data-key="'.$value['item_id'].'" href="javascript:void(0);" data-rippleria class="_radio btn btn-flat '.$btn_style.' btn-lg  ew-action-my-item" ew-radio-id="'.$property_id.'" ew-radio-val="'.$value['property_value'].'">
									<input type="radio" name="itemno" style="width:5px;"> '.$btn_icon.'
									'.$value['property_value'].'
								</a>';							
						
					}else if ($countAll > 0 ) {
						// NEXT ->>  
						$div.= '<a data-key="'.$value['item_id'].'" href="javascript:void(0);" data-rippleria class="_radio btn btn-flat '.$btn_style.' btn-lg  ew-action-my-item" ew-radio-id="'.$property_id.'" ew-radio-val="'.$value['property_value'].'">
									<input type="radio" name="itemno" style="width:5px;"> '.$btn_icon.'
									'.$value['property_value'].'
								</a>';							
						
					}else {
						// STOP ->|
						// Show Link Item 
						$div.= self::checkChildMenu($value,$set,$property_id);
					}
				}
			}

		}
		
		$div.= '</div>';
		
        return $div;

	}

	public static function getItemSet()
	{
		$param	= Yii::$app->request->post('param');
		$keys	= 'getItemSet&id:'.$param['pid'].'&set:'.$param['pset'].'&val:'.$param['pval'].'&user:'.Yii::$app->user->identity->id;
		$cache  = Yii::$app->cache;

		if(!$cache->get($keys)){
		 
			// WHEN CLICK
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
			->groupBy('property_id')
			->orderBy(['priority' => SORT_ASC]);

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

				$div.= '<div class=" ">
							<label class="box-header"> '.$value->propertytb->description.' </label>';

				if ($count<=1) {

					$div.= self::getPropertyListMe($value['property'],$set,(Object)['i' => $i,'count' => $count]);

				}else {

					if ($i == 1) {
						// Hender Heading
						$div.= self::getPropertyListMe($value['property'],$set,(Object)['i' => $i,'count' => $count]);
						$div.= '<br /><hr />';

					}else {

						if ($i == $count) {
							// Check Count > 0
							// ค้นหา ว่ามีลูกเพ่ิมใหม?
							// ถ้ามี ให้เข้าไปค้นอีกครั้ง
							if (self::getPropertyList($value['property'],$set)['count'] > 0) {

								// List Again
								$div.= self::getPropertyList($value['property'],$set)['div'];

							}else {

								// End
								//$div.= 'Fuck off : '.$i.' = '.$count;
								$div.= self::getPropertyListMe($value['property'],$set,(Object)['i' => $i,'count' => $count]);
								
							}
							
							
						}else{
							$div.= self::getPropertyList($value['property'],$set)['div'];
						}
						$div.= '<br /><hr />';
					}
					
				}
				
				$div.= '</div>';
			}
			$cache->set($keys, $div, 300);
		}

        return $cache->get($keys);


	}

	public static function getPropertyList($property_id,$set)
	{

		if(isset($_POST['param']['pval']))
		{
			$pval = $_POST['param']['pval'];
		}
		
		$query = TmpMenuGroup::find()
			->select('item')
			->where(['session_id' => Yii::$app->session->getId()])
			->andWhere(['property_value' => $pval])
			->andWhere(['itemset' => $set])
			->groupBy('item');

		$model = TmpMenuGroup::find()
			->where(['item' => $query])
			->andWhere(['session_id' => Yii::$app->session->getId()])
			->andWhere(['itemset' => $set])
			->andWhere(['<>','property_value',$pval])
			->andWhere(['property_id' => $property_id])
			->orderBy(['property_value' => SORT_ASC])
			->all();


        $count = count($model);

        if($count>0)
        {
        	// Add to Sale Line
        	$class= "ew-action-item";
        }else {
        	$class= "ew-action-my-item";
    
        }
		$div = '<div id="selector" class="btn-group-x" data-toggle="buttons">';
		
        foreach ($model as $value) {


    		$div.= '<a data-key="'.$value['item_id'].'" href="javascript:void(0);" data-rippleria class="_radio btn btn-default btn-lg btn-flat '.$class.'"  ew-radio-id="'.$property_id.'" ew-radio-item="'.$value['item'].'" ew-radio-val="'.$value['property_value'].'">
						<input type="radio" name="itemno" style="width:5px;"> <i class="far fa-square"></i>
						'.$value['property_value'].'
    				</a>';


		}
		
		$div.= '</div>';
		
        return [
			'div'=> $div,
			'count' =>$count
		];

	}


	public static function checkChildMenu($model,$set,$property_id)
	{
		
		$query = TmpMenuGroup::find()
		->select('item')
		->where(['session_id' => Yii::$app->session->getId()])
		->andWhere(['property_value' => $model['property_value']])
		->andWhere(['itemset' => $set])
		->orderBy('priority');

		$model = TmpMenuGroup::find()
		->where(['item' => $query])
		->andWhere(['session_id' => Yii::$app->session->getId()])
		->andWhere(['itemset' => $set])
		->andWhere(['property_id' => $property_id])
		->orderBy(['priority' => SORT_ASC])
		->all();

	 
		$div = '';
		foreach ($model as $value) {

			$div.= '<a data-key="'.$value['item_id'].'" href="javascript:void(0);" data-rippleria class="_radio btn btn-default btn-lg btn-flat ew-action-item" ew-radio-id="'.$property_id.'" ew-radio-item="'.$value['item'].'" ew-radio-val="'.$value['property_value'].'">
						<input type="radio" name="itemno" style="width:5px;"> <i class="far fa-square"></i>
						'.$value['property_value'].'
					</a>';
     
		}
		return $div;
	}

	public static function getTotal($provider, $fieldName)
	{
	    $total = 0;

	    foreach ($provider as $item) {
	        $total += $item[$fieldName];
	    }

	    return $total;
	}

	public static function getTotalSaleOrder($provider)
	{
	    $total = 0;

	    foreach ($provider as $item) {
	        $total += ($item['quantity'] * $item['unit_price']) -  (($item['quantity'] * $item['unit_price']) * ($item['line_discount'] /100));
	    }

	    return $total;
	}


	public static function getTotalSaleOrderExvat($provider,$typevat)
	{
	    $total = 0;

	    foreach ($provider as $item) {
	    	if($typevat==1) // Include Vat.
	    	{
	    		$total += $item['quantity'] * $item['unit_price'];
	    	}else  {		// Exclude Vat.
	    		$total += $item['quantity'] * $item['unit_price_exvat'] ;
	    	}

	    }

	    return $total;
	}
	public static function getTotalSaleLineExvat($order_no,$typevat)
	{
		$commpany = Yii::$app->session->get('Rules')['comp_id'];

		$model = SaleLine::find()->where(['order_no' => $order_no,'comp_id' => $commpany])->all();

	    $total = 0;

	    foreach ($model as $value) {
	    	if($typevat==1) // Include Vat.
	    	{
	        	$total += $value['quantity'] * $value['unit_price'] ;
	        }else  {
	        	$total += $value['quantity'] * $value['unit_price_exvat'] ;
	        }
	    }

	    return $total;
	}

	public static function getTotalSaleLine($order_no)
	{
		$commpany = Yii::$app->session->get('Rules')['comp_id'];

		$model = SaleLine::find()->where(['order_no' => $order_no,'comp_id' => $commpany])->all();

	    $total = 0;

	    foreach ($model as $value) {
	        $total += $value['quantity'] * $value['unit_price'] ;
	    }

	    return $total;
	}


    public static function OrderStatus($model)
    {
    	$icon 				= '<i class="fa fa-square"></i>';
    	$icon_hold 			= '<i class="fa fa-info-circle"></i>';
    	$icon_cancel		= '<i class="fa fa-ban"></i>';
    	$icon_truck			= '<i class="fa fa-truck"></i>';
    	$icon_bill			= '<i class="fa fa-file-text-o"></i>';
    	$icon_cubes 		= '<i class="fa fa-cubes"></i>';
    	$icon_hourglass 	= '<i class="fa fa-hourglass-half"></i>';
    	$icon_desktop 		= '<i class="fa fa-desktop"></i>';
    	$icon_envelope		= '<i class="fas fa-envelope"></i>';
		$icon_folder 		= '<i class="fa fa-folder-o"></i>';
		$icon_doc 			= '<i class="fas fa-envelope-open"></i>';
		
		


    	if($model->status == 'Open')
        {
            $JobStatus = '<span class="label label-info"><span class="ew-icon-status">'.$icon_doc.'</span> <span class="hidden-sm">'.Yii::t('common','status-open').'<span></span>';
        }else if($model->status == 'Release'){
            $JobStatus = '<span class="label label-danger"><span class="ew-icon-status">'.$icon_envelope.'</span> <span class="hidden-sm">'.Yii::t('common','status-release').'<span></span>';
        }else if($model->status == 'Checking'){
            $JobStatus = '<span class="label label-warning"><span class="ew-icon-status">'.$icon_hourglass.'</span> <span class="hidden-sm">'.Yii::t('common','status-checking').'<span></span>';
        }else if($model->status == 'Shiped'){
            $JobStatus = '<span class="label label-primary"><span class="ew-icon-status">'.$icon_truck.'</span> <span class="hidden-sm">'.Yii::t('common','status-shipped').'<span></span>';
        }else if($model->status == 'Reject'){
            $JobStatus = '<span class="label label-warning"><span class="ew-icon-status">'.$icon_hold.'</span> <span class="hidden-sm">'.Yii::t('common','status-reject').'<span></span>';
            $JobStatus.= '<div>('.$model->reason_reject.')</div>';
        }else if($model->status == 'Invoiced'){
            $JobStatus = '<span class="label label-success"><span class="ew-icon-status">'.$icon_bill.'</span> <span class="hidden-sm">'.Yii::t('common','status-invoiced').'<span></span>';
        }else if($model->status == 'Cancel'){
            $JobStatus = '<span class="label label-default"><span class="ew-icon-status">'.$icon_cancel.'</span> <span class="hidden-sm">'.Yii::t('common','Cancel').'<span></span>';
        }else if($model->status == 'Close'){
            $JobStatus = '<span class="label label-warning"><span class="ew-icon-status">'.$icon_folder.'</span> <span class="hidden-sm">'.Yii::t('common','status-close').'<span></span>';
        }else if($model->status == 'Hold'){
            $JobStatus = '<span class="label label-warning"><span class="ew-icon-status">'.$icon_hold.'</span> <span class="hidden-sm">'.Yii::t('common','status-hold').'<span></span>';
        }else if($model->status == 'Pre-Cancel'){
            $JobStatus = '<span class="label label-danger"><span class="ew-icon-status blink">'.$icon_hold.'</span> <span class="hidden-sm">'.Yii::t('common','status-cancel-req').'<span></span>';
        }else if($model->status == 'Credit-Note'){
            $JobStatus = '<span class="label label-danger"><span class="ew-icon-status"><i class="fas fa-level-down-alt"></i></span> <span class="hidden-sm">'.Yii::t('common','status-credit-note').'<span></span>';
        }else {
            $JobStatus = '<span class="label label-info"><span class="ew-icon-status">'.$icon.'</span> <span class="hidden-sm">'.Yii::t('common',$model->status).'<span></span>';
        }

        return $JobStatus;

    }




	public static function getBrowser()
	{
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $ub = 'Unknown';
	    $version= "";

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }

	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('/Firefox/i',$u_agent))
	    {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('/Chrome/i',$u_agent))
	    {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('/Safari/i',$u_agent))
	    {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('/Netscape/i',$u_agent))
	    {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }

	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }

	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }

	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}

	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	}


	public static function findEmpty()
	{
		return SaleHeader::find()
		->where(['balance' => 0])
		->andWHere(['user_id' => Yii::$app->user->identity->id])
		->andWHere(['sale_id' => Yii::$app->session->get('Rules')['sale_id']])
		->andWHere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
		->andWhere(['YEAR(create_date)' => date('Y')])
		->andWhere(['MONTH(create_date)' => date('m')])
		->andWhere(['status' => 'Open'])
		->one();
		// ->andWhere("YEAR(create_date) = '".date('Y')."' ")
		// ->andWhere("MONTH(create_date) = '".date('m')."' ")
		

		//return $model;

	}

	public static function GrandTotalSaleOrder($model)
	{



		$vat_type     = $model->vat_type;

		$vat          = $model->vat_percent;
		if($vat_type == 2) $vat = 0;

		// $saleLine       = SaleLine::find()->where(['order_no' => $model->no])->all();		

		//$BeforeDisc   = FunctionSaleOrder::getTotalSaleOrder($saleLine);

		$BeforeDisc		= $model->sumLine;

		//$model->discount = $model->percent_discount * $BeforeDisc /100;
	 

		$Discount     = $model->discount;

		// หักส่วนลด (ก่อน vat)
		$subtotal     = $BeforeDisc - $Discount ;


		if($model->include_vat == 1){

		    // Vat นอก


		    $InCVat   = ($subtotal * $vat )/ 100;

		    $total    = ($InCVat + $subtotal);
		    }else {

		    // Vat ใน



		    $InCVat   = $subtotal - ($subtotal / 1.07);

		    $total    = $subtotal;
		}

		return $total;
	}

	public static function getTotalBalance($model){

		$vat          = $model->vat_percent;

		$saleLine     = SaleLine::find()->where(['order_no' => $model->no])->all();
		$BeforeDisc   = FunctionSaleOrder::getTotalSaleOrder($saleLine);

		if($model->percent_discount > 0) $model->discount 	= $model->percent_discount * $BeforeDisc /100;

		$Discount     = $model->discount;

		// หักส่วนลด (ก่อน vat)
		$subtotal     = $BeforeDisc - $Discount ;


		if($model->include_vat == 1){

		    // Vat นอก


		    $InCVat   = ($subtotal * $vat )/ 100;

		    $total    = ($InCVat + $subtotal);
		    }else {

		    // Vat ใน



		    $InCVat   = $subtotal - ($subtotal / 1.07);

		    $total    = $subtotal;


		}


		    if($BeforeDisc==0){
		      $PercentDiscount = 0;
		    }else {
		      $PercentDiscount = $Discount/$BeforeDisc*100;
		    }

		return $total;
	}

	public static function getItemFromBarcode($barcode){
		$model = Items::find()->where(['barcode' => $barcode])->one();
		if($model){
			return $model;
		}
	}

	public static function lastprice($item){
		$model = SaleLine::find()
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

}
