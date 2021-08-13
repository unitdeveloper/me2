<?php

namespace admin\modules\approval\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;


use common\models\WarehouseMoving;
use common\models\SaleHeader;



/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionModels extends Model 
{

	public function UpdateSaleOrder($id,$status)
	{
		$model = SaleHeader::findOne($id);

		if($status=='Checking'){
			$model->confirm_date = date('Y-m-d H:i:s');
		}
		
		$model->status 	= $status;

		if($status=='Confirm'){
			//$dosomething 	=  $this->CreateShipment();
			$dosomething 	=  '';

			

		}else {
			$dosomething 	=  '';
		}
		



		if($model->save())
		{
			$dosomething.= '<br>root@ewinl:/# Updated Sale Order';
		}else {
			$dosomething.= '<br>root@ewinl:/# <span class="text-red">Update Fail . . . !Error 500</span><span class="blink">_</span>';
			exit();
		}
		

		return $dosomething;
		//exit();
	}

	public function CreateShipment()
	{
		$model = new WarehouseMoving();


		$model->DocumentNo 		= '';
        $model->PostingDate 	= '';
        $model->TypeOfDocument 	= '';
        $model->SourceDoc 		= '';
        $model->SourceDocNo 	= '';
        $model->ItemNo 			= '';
        $model->Description 	= '';
        $model->Quantity 		= '';
        $model->QtyToMove 		= '';
        $model->QtyMoved 		= '';
        $model->QtyOutstanding 	= '';
        $model->DocumentDate 	= '';

        if($model->save())	
        {
        	return 'root@ewinl:/# Created ship';
        }else{
        	return 'root@ewinl:/# <span class="text-red">Create Shipment Fail . . . </span><span class="blink">_</span>';
        }
		
	}


	public function consoleRender()
	{
		echo "<script>swal(
              '".Yii::t('common','Success !')."',
              '".Yii::t('common','Updated')."',
              'success'
            )</script>";

		// $console = 'root@ewinl:/# apt-get update . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .<br>'."\r\n";
  //       $console.= '. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . <br>'."\r\n";
  //       $console.= '<img src="images/icon/mini-loader.gif"><br>'."\r\n";

  //       return $console;

	}
}