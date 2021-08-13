<?php

namespace admin\modules\Management\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\db\Expression;
use yii\base\Model;
use admin\models\Generater;


// use common\models\WarehouseHeader;
// use common\models\WarehouseMoving;

// use common\models\SaleInvoiceHeader;
// use common\models\SaleInvoiceLine;


use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;



use common\models\Itemgroup;
use common\models\Items;

// use common\models\SaleLine;
// use common\models\SaleHeader;

// use common\models\OrderTracking;

// use admin\modules\tracking\models\FunctionTracking;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionManagement extends Model 
{
	public function getTotalBalance($model,$itemGroup,$func)
	{
		$vat 			= $model->vat_percent; 

		//var_dump($itemGroup);
		$BeforeDisc 	= 0;

		if($func == 'Excepted')
		{

			$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
				->where(['source_id' => $model->id])
				->andWhere(['NOT IN','items.ItemGroup',$itemGroup])
				->sum('quantity * unit_price');

		}else if($func == 'Equal'){

			if($itemGroup=='All')
			{
				$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
				->where(['source_id' => $model->id])
				->sum('quantity * unit_price');

			}else {

				$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
				->where(['source_id' => $model->id])
				->andWhere(['IN','items.ItemGroup',$itemGroup])
				->sum('quantity * unit_price');
			}

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


		if($total > 0){
			return $total;
		} else {
			return 0;
		}

           
	}


	public static function validateCheque($model,$cond){


		if($cond == 'Cash')
		{

			$QCheque = \common\models\Cheque::find()
			            ->joinwith('banklist')
			            ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
						->andWhere(['type' => ['Cash','ATM']])
						->andWhere(['apply_to_status' => $model->status])
			            ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
			            ->addParams([':apply_to' => $model->id]);

        }else{

        	$QCheque = \common\models\Cheque::find()
			            ->joinwith('banklist')
			            ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
						->andWhere(['type' => 'Cheque'])
						->andWhere(['apply_to_status' => $model->status])
			            ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
			            ->addParams([':apply_to' => $model->id]);
        }
		

        if($QCheque->exists()){

            $Cheque = $QCheque->all();

            $sumBalance = 0;
            foreach ($Cheque as $key => $model) {
            	$sumBalance += $model->balance;
            }

            return $sumBalance;

        }else {
        	return 0;
        }    
	}


	public function getFooterRowTotal($dataProvider,$itemGroup,$func)
	{
		$total = 0;
		foreach ($dataProvider as $model) {

			$vat 			= $model->vat_percent; 


			$BeforeDisc 	= 0;

			if($func == 'Excepted')
			{

				$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
					->where(['source_id' => $model->id])
					->andWhere(['NOT IN','items.ItemGroup',$itemGroup])
					->sum('quantity * unit_price');

			}else if($func == 'Equal'){

				if($itemGroup=='All')
				{
					$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
					->where(['source_id' => $model->id])
					->sum('quantity * unit_price');

				}else {

					$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
					->where(['source_id' => $model->id])
					->andWhere(['IN','items.ItemGroup',$itemGroup])
					->sum('quantity * unit_price');
				}

			}
			
			

			

			$Discount 		= $model->discount;

			// หักส่วนลด (ก่อน vat)
			$subtotal  		= $BeforeDisc - $Discount ;


			if($model->include_vat == 1){ 

				// Vat นอก

				$InCVat   	= ($subtotal * $vat )/ 100;

				$total    	+= ($InCVat + $subtotal);

			}else {

				// Vat ใน

				// 1.07 = 7%
				$vat_revert = ($vat/100) + 1;

				$InCVat   	= $subtotal - ($subtotal / $vat_revert);

			  	$total    	+= $subtotal;

			}


			 
		}

		if($total > 0){
			return $total;
		} else {
			return 0;
		}
		 
           
	}



	public function getSumFooter($dataProvider)
	{

		$Sumtotal = 0;
		foreach ($dataProvider as $key => $model) {
			 
		 
			$vat 			= $model->vat_percent; 


			$BeforeDisc 	= 0;

			$BeforeDisc   = RcInvoiceLine::find()->joinWith('itemstb')
					->where(['source_id' => $model->id])
					->sum('quantity * unit_price');
			
			

			

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


			$Sumtotal += $total;

		}


		if($Sumtotal > 0){
			return $Sumtotal;
		} else {
			return 0;
		}


	}


	public static function findItemInGroup($category)
    {
         
    		
        $model = Itemgroup::find()->where(['Child' => $category])->all();
        
         

        $div = array();
        foreach ($model as $value) {
            
            $count = Itemgroup::find()->where(['Child' => $value->GroupID])->count();

            if($count>0){

                    $div[]= FunctionManagement::findItemInGroup($value->GroupID);
                }else {
                	$div[]= $value->GroupID;
                } 
            
            
        }
         
        $data = implode(',', $div);

         
        
	     

	    return $data;
    }

     

     

    protected function findItems($id)
    {
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}