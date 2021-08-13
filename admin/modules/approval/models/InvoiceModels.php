<?php

namespace admin\modules\approval\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

use common\models\SaleHeader;
use common\models\SaleLine;
use common\models\SaleInvoiceHeader;
use common\models\SaleInvoiceLine;

use admin\modules\approval\models\FunctionModels;
use admin\models\Generater;

/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class InvoiceModels extends Model 
{
	public function CreateInvoice($id)
	{
		$Fnc = new FunctionModels();


		$SaleHeader = SaleHeader::find()->where(['no' => $_POST['param']['no']])->one();
		$SaleLine 	= SaleLine::find()->where(['order_no' => $_POST['param']['no']])->all();

		$model = new SaleInvoiceHeader();


		


		$NoSeries = new Generater();
        
        $dateNow 				= date('Y-m-d H:i:s');

        $model->no_         	= $NoSeries->GenerateNoseries('SaleInvoice',true);		 
		$model->cust_no_		= $SaleHeader->customer_id;
		$model->cust_code 		= $SaleHeader->customer->code;		
		$model->cust_name_ 		= $SaleHeader->customer->name;
		$model->sales_people 	= $SaleHeader->sales_people;
		$model->posting_date	= $dateNow;
		$model->order_date 		= date('Y-m-d',strtotime($dateNow));
		$model->document_no_	= $SaleHeader->no;
		$model->doc_type		= 'SaleOrder';




        // Render ..........
		//echo $Fnc->consoleRender();

		if($model->save())
		{
			 
			$SaleHeader->status  = 'Invoiced';
			$SaleHeader->save(false);

			foreach ($SaleLine as $key => $value) {
				# code...
				return $value->item_no.'<br>';
			}

			
			return '<br>Create';



			
		}else {
			//$Fnc->UpdateSaleOrder($SaleHeader->id,'Shiped');
			$SaleHeader->status  = 'Shiped';
			$SaleHeader->save(false);
			return '<br><span style="color:red;">Fail . . . . . . . . . . . . . . . </span>';
			
		}
		
	}


	public function GenerateInvoice()
	{

	}

}