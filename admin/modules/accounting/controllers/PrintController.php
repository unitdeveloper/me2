<?php

namespace admin\modules\accounting\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

 
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;

use common\models\SaleInvoiceHeader;
use common\models\SaleInvoiceLine;

 
 
class PrintController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                                        'export',
                                        'get-invoice'
                                    ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-invoice' => ['post']
                ],
            ],
        ];
    }

    
    public function actionExport($vat = null){
		if($vat > 0){
			return $this->render('export_vat');
		}else{
			return $this->render('export');
		}
        
    }

 
    
	public function actionGetInvoice(){
		$request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		
		$raws 			= [];
		$header 		= [];

		if($data->status == 'Posted'){
			$query		= RcInvoiceHeader::findOne($data->id);
			$line 		= RcInvoiceLine::find()->where(['source_id' => $query->id])->all();
			
		}else{
			$query		= SaleInvoiceHeader::findOne($data->id);
			$line 		= SaleInvoiceLine::find()->where(['source_id' => $query->id])->all();
		}
		
		$Bahttext   = new \admin\models\FunctionBahttext();

		if($query != null){
			$header = (Object)[
				'id' 			=> $query->id,
				'no' 			=> $query->no_,
				'cust_id' 		=> $query->customer->id,
				'cust_code' 	=> $query->customer->code,
				'cust_name' 	=> $query->customer->name,
				'cust_address' 	=> $query->customer->fullAddress['address'],
				'cust_tell'		=> $query->customer->phone,
				'cust_fax' 		=> $query->customer->fax,
				'cust_vat' 		=> $query->customer->vat_regis,
				'cust_head' 	=> $query->customer->headoffice == 1
									? Yii::t('common','Head Office') 
				 					: Yii::t('common','Branch') .' '.$query->customer->branch,
				'ext_doc' 		=> $query->saleOrder ? $query->saleOrder->no : $query->ext_document,
				'sale_code' 	=> $query->salesPeople->code,
				'sale_name' 	=> $query->salesPeople->name,
				'sale_surname' 	=> $query->salesPeople->surname,
				'transport_by' 	=> $query->saleOrder
									? ($query->saleOrder->transportList
										? $query->saleOrder->transportList->name
										: $query->saleOrder->transport) 
									: $query->customer->transport,
				'posting_date' 	=> date('Y-m-d',strtotime($query->posting_date)),
				'payment_term' 	=> $query->payment_term,
				'payment_date' 	=> $query->payment_term == '0' 
										? Yii::t('common','Cash') 
									  	: $query->payment_term.' '.Yii::t('common','Days'),
				'payment_due'	=> $query->paymentdue,
				'remark'		=> $query->remark,
				'total' 		=> $query->sumtotals,
				'thaibaht'		=> $Bahttext->ThaiBaht($query->sumtotals->total)
			];
		}

		foreach ($line as $key => $model) {
			$raws[] = (Object)[
				'id' 		=> $model->id,
				'code' 		=> $model->items->master_code,
				'item' 		=> $model->item,
				'name' 		=> $model->items->Description,
				'qty' 		=> $model->quantity * 1,
				'measure' 	=> $model->unitofmeasures ? $model->unitofmeasures->UnitCode : 'PCS',
				'price' 	=> $model->unit_price * 1,
				'discount' 	=> $model->line_discount * 1,
				'total' 	=> (($model->unit_price * $model->quantity) - (($model->unit_price * $model->quantity) * ($model->line_discount / 100))) * 1
			];
		}


	 
		return json_encode([
			'status' => 200,
			'header' => $header,
			'raws' 	 => $raws
		]);
	 
	}
}
