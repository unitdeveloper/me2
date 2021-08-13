<?php

namespace admin\modules\accounting\controllers;

use Yii;
use common\models\Payment;
use admin\modules\accounting\models\PaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
 
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl; 
use admin\models\Generater; 
use common\models\ApInvoiceLine;
use common\models\ApInvoiceHeader;
use common\models\Vendors;
use common\models\Items;
use common\models\PurchaseHeader;
use common\models\PurchaseLine;
use common\models\WarehouseHeader;
use common\models\WarehouseMoving;

/**
 * PayableController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['create', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['angular',
                                        'index',
                                        'create',
                                        'update',
                                        'view',
                                        'delete',
                                        'print',
                                        'line-ajax',
                                        'find-vendors',
                                        'find-items',
                                        'get-purchase-list',
                                        'push-purchase-line',
                                        'push-received-line',
                                        'create-payment'
                                    ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'line-ajax' => ['POST'],
                    'find-vendors' => ['POST'],
                    'find-items' => ['POST'],
                    'get-purchase-list' => ['POST'],
                    'push-purchase-line' => ['POST'],
                    'push-received-line' => ['POST'],
                    'create-payment' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all ApInvoiceHeader models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ApInvoiceHeader model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Payment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ApInvoiceHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ApInvoiceHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ApInvoiceHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ApInvoiceHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApInvoiceHeader::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }


    public function actionLineAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $raws   = [];
        $query  = ApInvoiceLine::find()->where(['source_id' => $data->id])->all();

        foreach ($query as $key => $model) {
            $raws[] = [
                'id' => $model->id,
                'code' => $model->items->master_code,
                'code'  => '',
                'name'  => '',
                'qty'   => '',
                'unit'  => '',
                'price' => ''
            ];
        }

        return json_encode([
            'status' => 200,
            'raw' => $raws
        ]);
    }


    public function actionFindVendors(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->search) >= 3 ? '' : 5;
         
        $query = Vendors::find()
        ->where(['or',
            ['like','name',explode(' ',trim($data->search))],
            ['like','name_en',explode(' ',trim($data->search))],
            ['like','code',explode(' ',trim($data->search))]
        ])        
        ->andWhere(['status' => 1])
        ->limit($limit)
        ->all();

        $obj = [];
        foreach ($query as $key => $model) {
            $obj[] = (Object)[
                'id'        => $model->id,
                'name'      => $model->name,
                'name_en'   => $model->name_en,
                'code'      => $model->code,
                'address'   => $model->address,
                'head'      => (int)$model->headoffice,
                'term'      => $model->payment_term ? $model->payment_term : 0
            ];
        }

        return $this->asJson([
            'limit'     => $limit ? $limit : 'unlimited',
            'data'      => $obj,
            'search'    => $data->search
        ]);
    }

    public function actionFindItems(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $limit          = strlen($data->text) >= 3 ? '' : 5;

        $query          = Items::find()
                            ->where(['or',
                                ['like', 'master_code', trim($data->text)],
                                ['like', 'barcode', trim($data->text)],
                                ['like', 'description_th', trim($data->text)]
                            ])
                            ->andWhere(['status' => 1]);

        $items          = [];
        foreach ($query->limit($limit)->all() as $key => $model) {

            // ราคา ล่าสุดของลูกค้า
            $lastPrice  = ApInvoiceLine::find()
                        ->joinWith('header')
                        ->select('ap_invoice_line.unit_price')
                        ->where(['ap_invoice_header.vendor_id'  => $data->vendor])
                        ->andWhere(['ap_invoice_line.item'  => $model->id])
                        ->orderBy(['ap_invoice_line.id'     => SORT_DESC])
                        ->limit(1)
                        ->one();
            $measureList = [];
            foreach ($model->itemunitofmeasures as $key => $value) {
                $measureList[] = [
                    'id'        => (string)$value->measure,
                    'name'      => $value->measures->UnitCode,
                    'qty_per'   => $value->qty_per_unit * 1,
                    'selected'  => $model->unit_of_measure == (string)$value->measure ? true : false
                ];           
            }

            $items[]= [
                'id'        => $model->id,
                'alias'     => $model->alias,
                'name'      => $model->description_th,
                'name_en'   => $model->Description,
                'code'      => $model->master_code,
                'barcode'   => $model->barcode,
                'unit'      => $measureList,
                'pic'       => $model->picture,
                'lastprice' => $lastPrice !== null ? $lastPrice->unit_price : 0
            ];
        }

        return json_encode([
            'status'    => $query->count() > 0 ? 200 : 404,
            'data'      => $data,
            'items'     => $items,
            'message'   => $query->count() > 0 ? Yii::t('common','Done') : Yii::t('common','Not found'),
        ]);
    }

    public function actionGetPurchaseList(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $poList = [];
        $query = PurchaseHeader::find()->where(['vendor_id' => $data->vendor])->all();
        foreach ($query as $key => $model) {

            $rc         = [];
            $rcHeader   = WarehouseHeader::find()->where(['TypeOfDocument' => 'Purchase', 'SourceDocNo' => $model->id])->all();
            foreach ($rcHeader as $key => $header) {
                $rc[] = [
                    'id' => $header->id,
                    'no' => $header->DocumentNo,
                    'date' => date('Y-m-d',strtotime($header->PostingDate)),
                    'recive_time' => date('H:i:s',strtotime($header->PostingDate)),
                    'count' => $header->lineInfo->count
                ];
            }
            $poList[] = [
                'id'            => $model->id,
                'no'            => $model->doc_no,
                'date'          => $model->order_date,
                'term'          => $model->payment_term,
                'due'           => $model->payment_due ? $model->payment_due : date('Y-m-d', strtotime($model->order_date. "+".$model->payment_term." days")),
                'vendor_name'   => $model->vendor_name,
                'vendor_id'     => $model->vendor_id,
                'vendor_code'   => $model->vendor->code,
                'amount'        => $model->total->total,
                'received'      => $rc
            ];
        }

        return json_encode([
            'status' => 200,
            'raw' => $poList
        ]);
    }

    public function actionPushPurchaseLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $purLine        = [];
        $query = PurchaseLine::find()->where(['source_id' => $data->id])->all();

        foreach ($query as $key => $model) {

            $measureList = [];
            foreach ($model->items->itemunitofmeasures as $key => $value) {
                $measureList[] = [
                    'id'        => (string)$value->measure,
                    'name'      => $value->measures->UnitCode,
                    'qty_per'   => $value->qty_per_unit * 1,
                    'selected'  => $model->unit_of_measure == (string)$value->measure ? true : false
                ];           
            }

            $purLine[]  = [
                'id'        => $model->id,
                'type'      => $model->type,
                'item'      => $model->item,
                'code'      => $model->items_no,
                'name'      => $model->description,
                'qty'       => $model->quantity,
                'unit'      => $model->unit_of_measure,
                'price'     => $model->unitcost,
                'measure'   => $measureList
            ];
            
        }

        $po     = PurchaseHeader::findOne($data->id);

        return json_encode([
            'status' => 200,
            'raw' => $purLine,
            'po' => [
                'id' => $po->id,
                'no' => $po->doc_no,
                'balance' => $po->total
            ]
        ]);
    }


    public function actionPushReceivedLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $purLine        = [];
        $query          = WarehouseMoving::find()->where(['source_id' => $data->id])->all();

        foreach ($query as $key => $model) {

            $measureList = [];
            foreach ($model->items->itemunitofmeasures as $key => $value) {
                $measureList[] = [
                    'id'        => (string)$value->measure,
                    'name'      => $value->measures->UnitCode,
                    'qty_per'   => $value->qty_per_unit * 1,
                    'selected'  => $model->unit_of_measure == (string)$value->measure ? true : false
                ];           
            }

            $purLine[]  = [
                'id'        => $model->id,
                'item'      => $model->item,
                'code'      => $model->ItemNo,
                'name'      => $model->Description,
                'qty'       => $model->Quantity,
                'unit'      => $model->unit_of_measure,
                'price'     => $model->unit_price,
                'measure'   => $measureList
            ];
            
        }

        $po     = PurchaseHeader::findOne($data->po);

        return json_encode([
            'status' => 200,
            'raw' => $purLine,
            'po' => [
                'id' => $po->id,
                'no' => $po->doc_no,
                'balance' => $po->total
            ]
        ]);
    }


    public function actionCreatePayment(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $source_head    = $data->header;
        $source_vendor  = $data->vendors;
        $source_payment = $data->payment;
        $source_line    = $data->line;

        //-----------------------------------------|
        // 1. Create Payable Invoice               |
        //      1.1 Create Payment Transection     |
        // 2. Create Payment                       |
        // 3. Render Result to show on page        |
        //-----------------------------------------|

        $transaction = Yii::$app->db->beginTransaction();
        try {   

            $model                  = new ApInvoiceHeader(); // ตั้งหนี้

            $model->no              = Generater::getRuning('ap_invoice_header','vat_percent',$source_head->vat);
            $model->ext_document    = $source_head->ext_doc;
            $model->ref_inv_header  = $source_head->inv_no;
            $model->order_date      = $source_head->inv_date; 
            $model->posting_date    = $source_head->inv_date;
            $model->paymentdue      = date('Y-m-d',strtotime($source_head->inv_date . "+ ".$source_vendor->term." days"));
            $model->vendor_id       = $source_vendor->id;
            $model->vendor_name     = $source_vendor->name;
            $model->percent_discount= $source_head->percent_discount;
            $model->discount        = $source_head->discount;
            $model->vat_percent     = $source_head->vat;
            $model->payment_term    = $source_vendor->term;
            $model->include_vat     = $source_head->include_vat;
            $model->status          = '';
            $model->remark          = $source_head->remark;
            $model->user_id         = Yii::$app->user->identity->id;
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
         

            if($model->save()){
                Generater::UpdateSeries('ap_invoice_header','vat_percent',$source_head->vat,$model->no);
                $ApLine = new ApInvoiceLine();      // รายการตั้งหนี้

                if($ApLine->save()){
                    $pay    = new Payment();        // ทำจ่าย

                    $pay->save();
                }

                $transaction->commit(); 
                return json_encode([
                    'status' => 200,   
                    'body' => $data,                     
                ]);   
            }else{
                $transaction->rollBack();
                return json_encode([
                    'status' => 200,   
                    'body' => $data,
                    'message'  => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE)
                ]);               
            }


            
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        

        // return json_encode([
        //     'status' => 200,   
        //     'body' => $data
        // ]);
    }

}
