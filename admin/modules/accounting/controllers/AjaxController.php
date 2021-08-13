<?php

namespace admin\modules\accounting\controllers;

use Yii;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use admin\models\Generater;
use common\models\Items;
use common\models\SalesPeople;
use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use common\models\SaleInvoiceHeader;
use common\models\SaleInvoiceLine;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;
use common\models\ViewRcInvoice;
use admin\modules\warehousemoving\models\ShipmentHeaderSearch;

use admin\modules\accounting\models\FunctionAccounting;

 
class AjaxController extends \yii\web\Controller
{
	public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-inv-line' => ['POST'],
					'ajax-post' => ['POST'],
					'invoice-by-bom' => ['POST'],
					'invoice-by-inven' => ['POST'],
					'invoice-by-no' => ['POST'],
					'invoice-all' => ['POST'],
					'json-create-item-line' => ['POST'],
					'json-post-source' 	=> ['POST'],
					'create-empty-inv' => ['POST'],
					'invoice-locked' => ['POST'],
					'invoice-update-no' => ['POST'],
					'invoice-update-ref' => ['POST'],
					'locked-all' 		=> ['POST'],
					'no-exists'			=> ['POST'],
					'duplicate-bill' 	=> ['POST'],
					'show-duplicate-bill'	=> ['POST']
                ],
            ],
        ];
	}


	protected function isToday($date){
		if(date('Ymd') == date('Ymd', strtotime($date))){
			return true;
		}else{
			return false;
		}
	}
 

	protected function lineAlert($model){
		try{                     
			// Line Notify
			$bot =  \common\models\LineBot::findOne(1);
			$msg = "\r\n".$model->message."\r\n";
			$msg.= 'IP : '.Yii::$app->getRequest()->getUserIP()."\r\n\r\n";
            $msg.= $model->cust."\r\n";
			$msg.= $model->no."\r\n";
			$msg.= number_format($model->balance,2)." บาท\r\n";
			$msg.= $model->remark."\r\n\r\n";
			$msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";

			$bot->notify_message($msg);					

		} catch (\Exception $e) {					 
			Yii::$app->session->setFlash('error', Yii::t('common','{:e}',[':e' => $e]));
		}
	}
	
	public function actionDelete(){
		$request_body   		= file_get_contents('php://input');
		$data           		= json_decode($request_body);
		$status					= 200;
		$message				= Yii::t('common','Success');
		$suggestion 			= '';
		$model 					= new RcInvoiceHeader();

		if($data->status==='Posted'){
			// RC INV
			$ivStatus 	= 'Posted';	
			$RC 		= RcInvoiceHeader::findOne($data->id);
			$custName   = $RC->customer ? $RC->customer->name : '';

			if($RC != null){
			
				$no 		= $RC->no_;
				$balance 	= $RC->sumtotals->total;

				//ถ้าล๊อก ไม่อนุญาติให้ลบ 
				//ต้องปลดล๊อกก่อน
				if($RC->locked==0){ // Unlock
					RcInvoiceLine::deleteAll(['source_id' => $RC->id]);
					if($RC->delete()){						
						self::lineAlert((Object)[
							'message' 	=> 'Delete Invoice (Posted)',
							'no'		=> $no,
							'balance'	=> $balance,
							'remark'	=> $data->remark,
							'cust'		=> $custName
						]);
					}
				}else{ // Locked
					$status     = 500;
					$message    = Yii::t('common','Warning');
					$suggestion = Yii::t('common','Locked by : {:user}', [':user' => ($RC->lockby ? $RC->lockby->username : '')]);
				}

				
				
				// if(self::isToday($RC->create_date)){ // ถ้าอยู่ในวันนี้ ให้ลบได้

				// 	RcInvoiceLine::deleteAll(['source_id' => $RC->id]);
				// 	if($RC->delete()){
				// 		self::lineAlert((Object)[
				// 			'message' 	=> 'Delete Invoice (Posted)',
				// 			'no'		=> $no,
				// 			'balance'	=> $balance,
				// 			'remark'	=> $data->remark
				// 		]);
				// 	}

				// }else{// ถ้าไม่ได้อยู่ในวัน เช็คสิทธิ์ก่อน

					

				// 	if($model->deletePermission){
				// 		RcInvoiceLine::deleteAll(['source_id' => $RC->id]);
				// 		if($RC->delete()){						
				// 			self::lineAlert((Object)[
				// 				'message' 	=> 'Delete Invoice (Posted)',
				// 				'no'		=> $no,
				// 				'balance'	=> $balance,
				// 				'remark'	=> $data->remark
				// 			]);
				// 		}
						
				// 	}else{
				// 		$status     = 500;
				// 		$message    = Yii::t('common','Warning');
				// 		$suggestion = Yii::t('common','Permission Denine');
				// 	}	
				// }

			}else{
				$status     = 404;
				$message    = Yii::t('common','Warning');
				$suggestion = Yii::t('common','No data found');
			}
			
		}else{
			// SALE INV
			$ivStatus 	= 'Open';
			$INV  		= SaleInvoiceHeader::findOne($data->id);
			$custName   = $INV->customer ? $INV->customer->name : '';

			if($INV != null){
				$no 		= $INV->no_;
				$balance 	= $INV->sumtotals->total;

				SaleInvoiceLine::deleteAll(['source_id' => $INV->id]);
				$INV->delete();	
				self::lineAlert((Object)[
					'message' 	=> 'Delete Invoice (Not Poste)',
					'no'		=> $no,
					'balance'	=> $balance,
					'remark'	=> $data->remark,
					'cust' 		=> $custName
				]);		

				// if($INV->sumtotals->total <= 0){ // ถ้าไม่มียอดเงิน ลบได้เลย
				// 	SaleInvoiceLine::deleteAll(['source_id' => $INV->id]);
				// 	$INV->delete();	
				// 	self::lineAlert((Object)[
				// 		'message' 	=> 'Delete Invoice (Not Poste)',
				// 		'no'		=> $no,
				// 		'balance'	=> $balance,
				// 		'remark'	=> $data->remark
				// 	]);			
				// }else{		// ถ้ามียอดเงิน เช็คสิทธิ์การลบก่อน

				// 	if(self::isToday($INV->create_date)){ // ถ้าอยู่ในวันนี้ ให้ลบได้

				// 		SaleInvoiceLine::deleteAll(['source_id' => $INV->id]);
				// 		if($INV->delete()){
				// 			self::lineAlert((Object)[
				// 				'message' 	=> 'Delete Invoice (Not Poste)',
				// 				'no'		=> $no,
				// 				'balance'	=> $balance,
				// 				'remark'	=> $data->remark
				// 			]);	
				// 		}

				// 	}else{ // ถ้าไม่ได้อยู่ในวัน เช็คสิทธิ์ก่อน
				// 		if($model->deletePermission){				
				// 			SaleInvoiceLine::deleteAll(['source_id' => $INV->id]);
				// 			if($INV->delete()){
				// 				self::lineAlert((Object)[
				// 					'message' 	=> 'Delete Invoice (Not Poste)',
				// 					'no'		=> $no,
				// 					'balance'	=> $balance,
				// 					'remark'	=> $data->remark
				// 				]);	
				// 			}
				// 		}else{
				// 			$status     = 500;
				// 			$message    = Yii::t('common','Warning');
				// 			$suggestion = Yii::t('common','Permission Denine');
				// 		}
				// 	}	
				// }
			}else{
				$status     = 404;
				$message    = Yii::t('common','Warning');
				$suggestion = Yii::t('common','No data found');
			}
		}

		return json_encode([
			'status' 	=> $status,
			'iv'		=> $ivStatus,
			'message'	=> $message,
			'suggestion'=> $suggestion,
			$data
		]);
	}

	public function actionJsonGetSource()
	{
		$customer 	= '';
		$search 	= '';
		$SaleOrder	= '';

		if(isset($_POST['cust'])) 		$customer 	= $_POST['cust'];
		if(isset($_POST['search'])) 	$search 	= $_POST['search'];
		if(isset($_POST['SaleOrder'])) 	$SaleOrder 	= $_POST['SaleOrder'];
	
		$searchModel = new ShipmentHeaderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);     

        // ถ้าดึงรายการแล้ว ไม่ต้องแสดงตอน get source
        // ถ้า posted บิลแล้ว ไม่ต้องแสดง
        $InvLine = SaleInvoiceLine::find()
        ->where(['<>','source_doc',''])
        ->andwhere(['type' => 'Item'])
        //->groupBy(['source_doc'])
        ->all();

        $document = array();
        foreach ($InvLine as $value) {
        	$document[]  = $value->source_doc;
		}
		         
 		//var_dump($document)	;
        $dataProvider->query->andwhere(['not in','warehouse_header.DocumentNo',$document]);        

        if($customer!='')
		{			
			$dataProvider->query->andwhere(['warehouse_header.customer_id' => $customer]);		
			$dataProvider->query->andFilterWhere(['or',
				['like','warehouse_header.DocumentNo',$search],
				['like', 'customer.name', $search]]);  
			} 		

		if($search!='')
		{
			//$dataProvider->query->where(['not in','DocumentNo',$document]); 
			$dataProvider->query->andFilterWhere(['or',
            ['like','warehouse_header.DocumentNo',$search],
            ['like', 'customer.name', $search]]);
		}         

        if($SaleOrder!=''){
			$dataProvider->query->andWhere(['warehouse_header.SourceDocNo' => $SaleOrder]);	
		} 
		$dataProvider->query->andWhere(['warehouse_header.status' => 'Shiped']);			
		return $this->renderAjax('_json_get_source',[
					//'model' => $model,
					'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
		]);
		
	}

	public function actionJsonGetCustomer($id)
    {     
    	 
        $model = SaleInvoiceHeader::findOne($id);                
        if($model->province=='') $model->province = 'Province';
        if($model->city=='') $model->city = 'city';
        if($model->district=='') $model->district = 'district';
        //if($model->postcode=='') $model->postcode = '10150';        
        $data = [        		 
        		'address' => $model->cust_address,
        		'address2'	=> $model->cust_address2,
                'district'	=> $model->district,
                'city'	=> $model->city,
                'province'	=> $model->customer ? $model->customer->province : '59',
                'postcode'	=> $model->postcode,                 
		];
		
        return json_encode($data);       
        
    }

	public function actionJsonPostSource()
	{
		if(Yii::$app->request->post('so') != null){
			Yii::$app->session->set('lasturl','/SaleOrders/saleorder/view');
			Yii::$app->session->set('lastid',Yii::$app->request->post('so'));
		} 
		$Fnc = new FunctionAccounting();		

		$createInv = $Fnc->getFromInvoiceLine(Yii::$app->request->post());

		$res 	= json_decode($createInv);
		if($res->status == 200){
			return $this->redirect(['/accounting/saleinvoice/update', 'id' => $res->value->inv]);
		}

	}

	 

	public function actionAjaxPost()
	{
		$id 	= Yii::$app->request->post('id');
		$comp 	= Yii::$app->session->get('Rules')['comp_id'];
		$keys 	= 'posting&inv:'.$id."&comp".$comp;

		$Fnc 	= new FunctionAccounting();
		$source	= $this->findModel($id);		
		$ready 	= RcInvoiceHeader::find()->where(['no_' => $source->no_, 'comp_id' => $comp])->one();

		// ถ้ามีเลขที่อยู่แล้ว ไม่อนุญาตให้บันทึกซ้ำ
		if($ready != null){
			return json_encode([
				'status' => 403,
				'message' => base64_encode($id)
			]);
			exit();
		}

			
		// ถ้ากำลังทำงาน ไม่ให้ post ซ้ำ
		if(Yii::$app->cache->get($keys)){
			return json_encode([
				'status' => 202,
				'message' => Yii::t('common','working')
			]);
		}else{
			// บันทึกการทำงาน
			// ถ้าทำเสร็จให้ไปลบที่   
			Yii::$app->cache->set($keys, true, 60);

			$transaction = Yii::$app->db->beginTransaction();
			try {
	
									
				
				$posted = $Fnc->postInvoice($id,$source);		    	
				
				if($posted->status == 200){

					//---- Clear Sale Invoice --------

					// Delete SaleInvoicLine
					SaleInvoiceLine::deleteAll(['source_id' => $id]);
					// Delete SaleInvoiceHeader
					$source->delete(); 				

					//---- /. Clear Sale Invoice -----

					Yii::$app->cache->delete($keys);
					$transaction->commit();
					return json_encode([
						'status' 	=> 200,
						'id' 		=> base64_encode($posted->id),
						'message' 	=> $posted->message
					]);	 
				}else {		
					Yii::$app->cache->delete($keys);
					return json_encode([
						'status' 	=> $posted->status,
						'id' 		=> $id,
						'message' 	=> $posted->message
					]);	 				 
				}

				
				

			} catch (\Exception $e) {
				Yii::$app->cache->delete($keys);
				$transaction->rollBack();
				return json_encode([
					'status' => 500,
					'message' => Yii::t('common','{:e}',[':e' => $e])
				]);		 
			}
		}
		
	}

	public function actionAjaxChangeInvno()
	{
		$data = FunctionAccounting::changeInvoiceNo($_POST['id'],$_POST['val'],$_POST['old']);
		return $data;
	}


	public function actionDeleteInvLine($id)
	{
		$model = SaleInvoiceLine::find()->where(['source_id' => $id,'id' => $_POST['data']])->one();
		//$model->status = 'delete';
		if ($model->delete()) {

			if ($model->source_id != 0) {
				$header = SaleInvoiceHeader::findOne($id);
				$header->discount = $header->sumLine * ($header->percent_discount / 100);
				$header->save(false);
			}

			return json_encode([
				'status' => 200,
				'id' => $_POST['inv'],
				'data' => [
					'percent_discount' => ($model->source_id)? $header->percent_discount : 0,
					'discount' => ($model->source_id)? $header->discount : 0
				]
			]);

		} else {
			return $model->getErrors();
		}

	}

	public function actionRenderInvLine()
	{		 
		//$model = SaleInvoiceHeader::find()->where(['session_id' => Yii::$app->session->getId()])->one();
		if(isset($_POST['id'])) 
		{
			$query   = SaleInvoiceLine::find()->where(['source_id' => $_POST['id']]); 
		}else{
			$query   = SaleInvoiceLine::find()->where(['session_id' => Yii::$app->session->getId(),'source_id' => 0]); 
		}

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,	            
		]);

		return $this->renderPartial('../saleinvoice/__invoice_line', [	                     
					'dataProvider' => $dataProvider,
				]);
         
	}


	public function actionJsonCreateItemLine()
	{
		$item_id 	= '1^x';
		$item_code = '1414';
		
		$header 	= SaleInvoiceHeader::findOne(Yii::$app->request->post('id'));

		if(isset($_POST['item'])) 	$item_id	= $_POST['item'];
		if(isset($_POST['code'])) 	$item_code	= $_POST['code'];
		if($item_id=='eWinl') 		$item_id	= '1^x';

		$findItem  = Items::find()
		->where(['or',
			['No' => $item_code],
			['barcode' => $item_code]
		]);
 
		if($findItem->count() >= 1){
			$Item 	= $findItem->one();
		}else{
			
			$Item  	= Items::findOne(['No' => $item_id]);
		}
			 
			$model = new SaleInvoiceLine();
			
			$model->source_id 		= 0;
			$model->type 			= 'Item';
			$model->item 			= $Item->id;
			$model->code_no_ 		= $Item->No;
			$model->code_desc_ 		= $Item->description_th;			

			if(isset($_POST['desc'])) 	$model->code_desc_	= $_POST['desc'];
			if(isset($_POST['id'])) 	$model->source_id 	= $_POST['id'];
			if(isset($_POST['no'])) 	$model->doc_no_		= $_POST['no'];
			if(isset($_POST['type'])) 	$model->type		= $_POST['type'];
			if(isset($_POST['code'])) 	$model->code_no_	= $_POST['code'];	 			
			
			$model->quantity 		= $_POST['qty'];
			$model->unit_price 		= $_POST['price'];
			$model->line_discount	= 0;
			$model->vat_percent 	= 0;
			$model->session_id 		= Yii::$app->session->getId();
			$model->order_id 		= $header->order_id;
			$model->measure     	= $Item ? $Item->unit_of_measure : 1;
			$model->comp_id			= Yii::$app->session->get('Rules')['comp_id'];

			if ($model->save()) {

				if ($model->source_id != 0) {
					$header = SaleInvoiceHeader::findOne($model->source_id);
					$total 	= ($header->sumLine)? $header->sumLine : 1;
					$header->discount = $total  * ($header->percent_discount / 100);
					if (!$header->save()) {
						return json_encode([
							'status' => 500,
							'message' => json_encode($header->getErrors(), JSON_UNESCAPED_UNICODE),
						]);
					}
				}
				
				return json_encode([
					'id' 		=> $model->id,		
					'itemid' 	=> $Item->id,		
					'item' 		=> ($item_id=='1^x')? @$_POST['code'] : $model->items->master_code,
					'barcode' 	=> $model->items->barcode,
					'desc' 		=> $model->code_desc_,
					'qty' 		=> $model->quantity,
					'price' 	=> $model->unit_price,
					'data' 		=> [
						'percent_discount' 	=> ($model->source_id)? $header->percent_discount : 0,
						'discount'			=> ($model->source_id)? $header->discount : 0
					],
					'discount' 	=> $model->line_discount * 1
				]);

			}else {
				print_r($model->getErrors());
				exit();
			}
			
		//}

		 

	}

	protected function findModel($id)
    {
        if (($model = SaleInvoiceHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
	}
	

	public function actionJsonFindItem()
    {
        //return $_POST['param']['item'];
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $Items = Items::find()
        ->where(['or',
            ['like','barcode'      , $_POST['param']['item']],
            ['like','master_code'  , $_POST['param']['item']]
        ])
        ->andWhere(['company_id' => $company]);

        if($Items->count() >0)
        {
            //$model      = Items::find()->where(['master_code'=>$_POST['param']['item'],'company_id' => $company ])->one();
            $model      = $Items->one();

            $Query      = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
            $RealInven  = $Query->sum('Quantity');
            $Remaining  = $model->Inventory + $RealInven;

            $data = [
                    'id'        => $model->id,
                    'item'      => $model->No,
                    'ig'        => $model->ItemGroup,
                    'Photo'     => $model->Photo,
                    'std'       => $model->StandardCost,
                    'desc'      => $model->description_th,
                    'code'      => $model->master_code,
                    'remain'    => $Remaining,
                ];
            return json_encode($data);
        }else {
            $data = [
                    'id'        => '1414',
                    'item'      =>'1^x',
                    'ig'        => 0,
                    'Photo'     => 0,
                    'std'       => 0,
                    'desc'      => Yii::t('common','Text'),
                    'code'      => '1^x',
                    'remain'    => 0,
                ];
            return json_encode($data);
        }
        
    }


	public function actionGetSourceInvoice(){
		$models = \common\models\ViewRcInvoice::find()
		->joinWith('customer')
		->where(['view_rc_invoice.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
		->andWhere(['view_rc_invoice.doc_type' => 'Sale'])
		->andWhere(['view_rc_invoice.status' => 'Posted']);
	
		$dataProvider = new ActiveDataProvider([
			'query' => $models,  
			'pagination' => [
                'pageSize' => 10,
            ],
		]);		 

		if(isset($_GET['keyword'])){
			$dataProvider->query->andWhere(['or',
				['like', 'view_rc_invoice.no_', explode(' ',$_GET['keyword'])],
				['like', 'customer.name',  explode(' ',$_GET['keyword'])]
			]);
		}

		$dataProvider->query->orderBy(['posting_date' => SORT_DESC]);
		
		$data = [];
		// foreach ($models->all() as $key => $model) {
		// 	$data[] = (Object)[
		// 		'id' => $model->id,
		// 		'no' => $model->no_,
		// 		'total' => $model->sumtotals->total,
		// 		'status' => $model->status
		// 	];
		// }

		return json_encode([
			'status' => 200,
			'data' => $data,
			'html' => $this->renderAjax('_invoice_list',[
				//'model' => $model,
				'searchModel' => $models,
				'dataProvider' => $dataProvider,
				])
		]);
	}


	public function actionValidateDocumentExists($no){
		$model = \common\models\ViewRcInvoice::findOne(['no_' => $no,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
		if ($model!=null){
			return json_encode([
				'status' => 200,
				'data' => [
					'id' => $model->id,
					'no' => $model->no_
				]
			]);
		}else{
			return json_encode([
				'status' => 404,
				'data' => [
					'no' => $no
				]
			]);
		}
	}

	public function actionInvoiceByBom(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $comp  = Yii::$app->session->get('Rules')['comp_id'];
		$keys  = 'rc_invoice&id:'.$data->id.'comp:'.$comp;

		$model = RcInvoiceHeader::findOne($data->id);
		
		if(Yii::$app->cache->get($keys)){
			return json_encode([
				"source"    => 'cache',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);        			
		}else{
			$rawData = [];		 
			foreach (\common\models\RcInvoiceLine::find()->where(['source_id' => $model->id])->all() as $key => $IvLine) {
				$rawData[] = [
					'item'	=> $IvLine->item,
					'detail'=> $IvLine->items->bomLine,
					'code'	=> $IvLine->items->master_code,
					'name'	=> $IvLine->code_desc_,
					'qty' 	=> $IvLine->quantity,
					'price' => $IvLine->unit_price * 1,	
					'img'   => $IvLine->items->picture			
				];
			}

			$data = [
				'raw' => $rawData,
				'custName' => $model->customer->name
			];

			Yii::$app->cache->set($keys,$data,300);
			return json_encode([
				"source"    => 'api',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);
		}		 

	}
	

	public function actionInvoiceByInven(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $comp  = Yii::$app->session->get('Rules')['comp_id'];
		$keys  = 'InvoiceByInven&id:'.$data->id.'comp:'.$comp;

		$model = \common\models\ViewRcInvoice::findOne(['id' => $data->id, 'status' => $data->status]);
		
		if(Yii::$app->cache->get($keys)){
			return json_encode([
				"source"    => 'cache',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);        			
		}else{
			$rawData = [];	

			if($data->status == 'Open'){
				$Lines 	 = \common\models\SaleInvoiceLine::find()->where(['source_id' => $model->id])->all();
			}else{
				$Lines 	 = \common\models\RcInvoiceLine::find()->where(['source_id' => $model->id])->all();
			}

			foreach ($Lines as $key => $IvLine) {
				$rawData[] = [
					'item'	=> $IvLine->item,
					'detail'=> $IvLine->items->bomLine,
					'code'	=> $IvLine->items->master_code,
					'name'	=> $IvLine->code_desc_,
					'qty' 	=> $IvLine->quantity,
					'price' => $IvLine->unit_price * 1,	
					'img'   => $IvLine->items->picture,
					'return'=> $IvLine->return_receive * 1			
				];
			}

			$data = [
				'raw' => $rawData,
				'custName' => $model->customer->name,
				'status' => $data->status
			];

			Yii::$app->cache->set($keys,$data,10);
			return json_encode([
				"source"    => 'api',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);
		}		 

	}
	

	public function actionInvoiceByNo(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

		$comp  	= Yii::$app->session->get('Rules')['comp_id'];
		$fdate 	= $data->fdate;
		$tdate 	= $data->tdate;
		$vat	= isset($data->vat) 
					? ($data->vat == 'Vat'
						? 7
						: 0)
					: '';
		$keys  	= 'InvoiceByNo&comp:'.$comp.'&fdate:'.$fdate.'&tdate:'.$tdate.'&vat'.$data->vat;

		
		
		if(Yii::$app->cache->get($keys)){
			return json_encode([
				"source"    => 'cache',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);        			
		}else{
			$rawData = [];	
			$query = \common\models\ViewRcInvoice::find()
						->where(['between','DATE(posting_date)',$fdate,$tdate])
						->andWhere(['comp_id' => $comp])
						->andWhere(['doc_type' => 'Sale'])						
						->orderBy(['no_' => SORT_DESC]);

			if($data->vat != 0){
				$query->andWhere(['vat_percent' => $vat]);
			}

			$Invoice	= $query->all();

			foreach ($Invoice as $key => $model) {
				$rawData[] = [
					'id' 		=> $model->id,
					'custCode' 	=> $model->cust_code,
					'custName' 	=> mb_substr($model->cust_name_,0, 30). ' ' .(strlen($model->cust_name_) > 30 ? '...' : null),
					'no' 		=> $model->no_,
					'ref' 		=> $model->ext_document ? $model->ext_document : ' ',
					'orderId' 	=> $model->saleorder->id,
					'orderNo' 	=> $model->saleorder->no ? $model->saleorder->no : ' ',
					'due' 		=> date('Y-m-d', strtotime($model->paymentdue)),
					'date' 		=> date('Y-m-d', strtotime($model->posting_date)),
					'status' 	=> $model->status,
					'vat' 		=> $model->vat_percent,
					'balance' 	=> $model->totals,
					'locked'	=> $model->status == 'Posted' ?  $model->field->locked : 0,
					'transport' => $model->transports ? $model->transports->id : 0,
					'revenue'	=> $model->revenue
				]; 
			}

			$data = [
				'raw' 		=> $rawData,
				'status' 	=> 200
			];

			Yii::$app->cache->set($keys,$data,1);
			return json_encode([
				"source"    => 'api',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);
		}		 

	}
	
	public function actionCreateEmptyInv(){
		$request_body   		= file_get_contents('php://input');
		$data           		= json_decode($request_body);
		$status					= 200;
		$message				= Yii::t('common','Success');
		$id						= 0;
		$model 					= new SaleInvoiceHeader();

		if(self::checkNumber($data->no)){ // ถ้ามีอยู่แล้ว
			
			$status         	= 403;
			$message        	= Yii::t('common','Already exists').' : '.$data->no;
		 
		}else{
			

			$model->no_             = $data->no;
			$model->status          = 'Open';
			$model->user_id         = Yii::$app->user->identity->id;
			$model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
			$model->cust_name_      = 'จองโดย : '. Yii::$app->user->identity->username;
			$model->cust_address    = '';
			$model->cust_no_        = 909;
			$model->session_id      = Yii::$app->session->getId();

			$SALES                  = SalesPeople::findOne(Yii::$app->session->get('Rules')['sale_id']);

			$model->sales_people    = $SALES ? $SALES->code : '';
			$model->sale_id         = $SALES ? $SALES->id : '';
			$model->posting_date    = date('Y-m-d H:i:s');
			$model->order_date      = date('Y-m-d');
			$model->ship_date       = date('Y-m-d');
			$model->cust_code       = '999';
			$model->document_no_    = $data->no;
			$model->doc_type        = 'Sale';
			$model->city            = 814;
			$model->district        = 7352;
			$model->province        = 59;
			$model->postcode        = 74000;
			$model->status          = 'Open';
			$model->paymentdue      = date('Y-m-d');
			$model->discount        = 0;
			$model->percent_discount= 0;
			$model->vat_percent     = 7;
			$model->payment_term    = 0;
			$model->include_vat     = 1;
			$model->ext_document	= $data->ref;
			$model->reserved 		= 1; // Reserved no
			
			if($model->save()){
				$id 			= $model->id;
				$status         = 200;
				$UpdateSeries   = Generater::CreateNextNumber('vat_type', 'vat_value', '7', $model->no_);
			}else{
				$status         = 500;
				$message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
			}

		}

 

		return json_encode([
			'status' 	=> $status,
			'message'	=> $message,
			'id'		=> $id
		]);

	}

	public function actionInvoiceLocked(){
        $request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		$status			= 200;
		$message		= Yii::t('common','Success');
		$locked			= NULL;

		$model 			= RcInvoiceHeader::findOne($data->id);

		if($model->deletePermission){
			if($model != null){
				$model->locked 		= $data->status;
				$model->locked_by 	= Yii::$app->user->identity->id;
				if($model->save()){
					$locked			= $model->locked;
				}else{				
					$status         = 500;
					$message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
				}
			}else{
				$status		= 404;
				$message 	= Yii::t('common','Not found');
			}
		}else{
			$status     = 500;
			$message    = Yii::t('common','Permission Denine');
		}
		
		return json_encode([
			'status' 	=> $status,
			'message'	=> $message,
			'locked' 	=> $locked
		]);
	}

	public function actionInvoiceUpdateNo(){
        $request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		$status			= 200;
		$message		= Yii::t('common','Success');
	 
		$model 			= SaleInvoiceHeader::findOne($data->id);

		$oldNo 			= $model->no_;
		 
		if($model != null){
			$model->no_ 		= $data->no;
			

			if($model->save()){
				self::lineAlert((Object)[
					'message' 	=> 'Change Invoice No',
					'no'		=> $oldNo.' => '.$data->no,
					'balance'	=> $model->sumtotals->total,
					'cust'		=> $model->customer ? $model->customer->name : '',
					'remark'	=> $model->customer ? $model->customer->name : ''
				]);
			}else{
				$status         = 500;
				$message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
			}
		}else{
			$status		= 404;
			$message 	= Yii::t('common','Not found');
		}
		 
		
		return json_encode([
			'status' 	=> $status,
			'message'	=> $message
		]);
	}

	public function actionInvoiceUpdateRef(){
        $request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		$status			= 200;
		$message		= Yii::t('common','Success');
	 
		$model 			= SaleInvoiceHeader::findOne($data->id);

		
		 
		if($model != null){
			$oldRef			= $model->ext_document;

			$model->ext_document 		= $data->val;
			

			if($model->save()){
				self::lineAlert((Object)[
					'message' 	=> 'Change Reference No',
					'no'		=> $oldRef.' => '.$data->val,
					'balance'	=> $model->sumtotals->total,
					'cust'		=> $model->customer ? $model->customer->name : '',
					'remark'	=> $model->no_.' : '.$model->remark
				]);
			}else{
				$status         = 500;
				$message        = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
			}
		}else{
			$status		= 404;
			$message 	= Yii::t('common','Not found');
		}
		 
		
		return json_encode([
			'status' 	=> $status,
			'message'	=> $message
		]);
	}
	

	protected function checkNumber($no){
        return ViewRcInvoice::find()->where(['no_' => $no, 'comp_id' => Yii::$app->session->get('Rules')['comp_id']])->exists();
	}
	

	public function actionInvoiceAll(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

		$comp  	= Yii::$app->session->get('Rules')['comp_id'];
		$fdate 	= $data->fdate;
		$tdate 	= $data->tdate; 
		$keys  	= 'InvoiceAll&comp:'.$comp.'&fdate:'.$fdate.'&tdate:'.$tdate.'&vat'.$data->vat.'&credit:'.$data->cr;

		
		
		if(Yii::$app->cache->get($keys)){
			return json_encode([
				"source"    => 'cache',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);        			
		}else{
			$rawData = [];	
			$query 	= \common\models\ViewRcInvoice::find()
						->where(['between','DATE(posting_date)',$fdate,$tdate])
						->andWhere(['IN', 'doc_type',$data->cr==1 ? ['Sale','Credit-Note', NULL]: ['Sale']])
						->andWhere(['comp_id' => $comp])	
						->andWhere(['IN', 'vat_percent', $data->vat == 0 ? [0,7] : [0] ])				
						->orderBy(['no_' => SORT_DESC]);

			 

			$Invoice	= $query->all();

			foreach ($Invoice as $key => $model) {
				$rawData[] = [
					'id' 		=> $model->id,
					'custCode' 	=> $model->cust_code,
					'custName' 	=> mb_substr($model->cust_name_,0, 30). ' ' .(strlen($model->cust_name_) > 30 ? '...' : null),
					'no' 		=> $model->no_,
					'sale_id'	=> $model->sale_id,
					'sale_code'	=> $model->salesPeople ? $model->salesPeople->code : '',
					'sale_name'	=> $model->salesPeople ? $model->salesPeople->name : '',
					'ref' 		=> $model->ext_document ? $model->ext_document : ' ',
					'orderId' 	=> $model->saleorder->id,
					'orderNo' 	=> $model->saleorder->no ? $model->saleorder->no : ' ',
					'due' 		=> date('Y-m-d', strtotime($model->paymentdue)),
					'date' 		=> date('Y-m-d', strtotime($model->posting_date)),
					'status' 	=> $model->status,
					'vat' 		=> $model->vat_percent,
					'balance' 	=> $model->totals,
					'locked'	=> $model->status == 'Posted' ?  $model->field->locked : 0
				]; 
			}

			$data = [
				'raw' 		=> $rawData,
				'status' 	=> 200
			];

			Yii::$app->cache->set($keys,$data,60);
			
			return json_encode([
				"source"    => 'api',
				'status' 	=> 200,
				"data"      => Yii::$app->cache->get($keys)
			]);
		}		 

	}

	public function actionLockedAll(){
		$request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		
		$RC 			= new RcInvoiceHeader();
		$status 		= 200;
		$message 		= Yii::t('common','Success');

		$raw 			= [];
		if($RC->deletePermission){			 
		
			foreach ($data as $key => $value) {
				$model  = RcInvoiceHeader::findOne($value->id);
				$model->locked 		= 1;
				$model->locked_by 	= Yii::$app->user->identity->id;
				if($model->save()){
					$raw[] = [
						'status' => 200,
						"id" => $model->id
					];
				}else{
					$raw[] = [
						'status' => 500,
						"id" => $model->id
					];
				}
			}

		}else{
			$status     = 500;
			$message    = Yii::t('common','Permission Denine');
		}

		return json_encode([
			'status' 	=> $status,
			'message' 	=> $message,
			'raw' => $raw
		]);
	}

	public function actionNoExists(){
		$request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		
		$model = \common\models\ViewRcInvoice::findOne(['no_' => $data->no,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
		if ($model!=null){
			return json_encode([
				'status' => 200,
				'data' => [
					'id' => $model->id,
					'no' => $model->no_
				]
			]);
		}else{
			return json_encode([
				'status' => 404,
				'data' => [
					'no' => $data->no
				]
			]);
		}
	}
	 
	public function actionDuplicateBill(){
		$request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		
		$raws 			= [];
		$query 			= \common\models\ViewRcInvoice::find()
						->select('no_,count(no_) as dup')
						->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
						->groupBy('no_')
						->having(['>','count(no_)', 1])
						->all();
		foreach ($query as $key => $model) {
			$raws[] = (Object)[
				'no' 		=> $model->no_,
				'dup'		=> $model->dup,
			];
		}

		return json_encode([
			'status' => 200,
			'raws' => $raws
		]);
	}

	public function actionShowDuplicateBill(){
		$request_body   = file_get_contents('php://input');
		$data           = json_decode($request_body);
		
		$raws 			= [];
		$query 			= \common\models\ViewRcInvoice::find() 
						->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
						->andWhere(['no_' => $data->no])
						->all();
		foreach ($query as $key => $model) {
			$raws[] = (Object)[
				'id' 		=> $model->id,
				'no' 		=> $model->no_,
				'cust'		=> $model->customer ? $model->customer->name : '',
				'status'	=> $model->status,
			];
		}

		return json_encode([
			'status' => 200,
			'raws' => $raws
		]);
	}

}