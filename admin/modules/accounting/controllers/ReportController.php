<?php

namespace admin\modules\accounting\controllers;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Cheque;
use common\models\BankAccount;
use admin\modules\accounting\models\ChequeSearch;
use common\models\ViewRcInvoiceTotal;

 
class ReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new ChequeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]); 
    }

    public function actionMain(){
        return $this->render('main');
    }

    public function actionBankList(){   
        return $this->render('bank-list');
    }

    public function actionBankListAjax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $query = Cheque::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['tranfer_to' => $data->id])
        ->andWhere(['between','DATE(post_date_cheque)', $data->fdate, $data->tdate])
        ->all();
        $data = [];
        foreach ($query as $key => $model) {
            $data[] = [
                'id'        => $model->id,
                'type'      => $model->type,
                'bank'      => $model->banklist->name,
                'inv_id'    => $model->apply_to,
                'inv_no'    => $model->apply_to_no,
                'balance'   => (float)$model->balance,
                'date'      => $model->posting_date,
                'chequedate'=> $model->post_date_cheque,
                'cust'      => $model->customer->name
            ];
        }
        return json_encode([
            'status' => 200,
            'data'  => $data
        ]);
    }
    

    public function actionCustomerSale(){

        $invoice    = \common\models\RcInvoiceHeader::find()
                    ->select('cust_no_')
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->andWhere(['between','posting_date', date('Y').'-01-01', date('Y').'-12-31'])
                    ->groupBy(['cust_no_']);

        $query      = \common\models\Customer::find()
                    ->where(['status' => 1])
                    ->andWhere(['in','id',$invoice])
                    ->orderBy(['code' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $this->render('customer-sale', [              
            'dataProvider' => $dataProvider,
        ]);       

    }


    public function actionMonthly(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        $raw = [];

        if(isset($data->sale)){
            $query  = ViewRcInvoiceTotal::find()
                        ->select('DATE(posting_date) as posting_date, sale_id,  sum(total) as total')
                        ->Where(['IN', 'doc_type',$data->cr==1 ? ['Sale','Credit-Note']: ['Sale']])
                        ->andWhere(['between','DATE(posting_date)', 
                            date('Y-m-d 00:00:00', strtotime(date('Y').'-'.$data->m.'-01')), 
                            date('Y-m-t 23:59:59', strtotime(date('Y').'-'.$data->m.'-01'))])
                        ->groupby('DATE(posting_date), sale_id')
                        ->all(); 
            
            foreach ($query as $key => $model) {
                $raw[] = (Object)[
                    'days'      => (int)date('d', strtotime($model->posting_date)),
                    'date'      => $model->posting_date,
                    'sale_id'   => $model->sale_id,
                    'balance'   => $model->total * 1
                ];
            }

        }else{

            $query  = ViewRcInvoiceTotal::find()
                    ->select('DATE(posting_date) as posting_date, sum(total) as total')
                    ->Where(['IN', 'doc_type',$data->cr==1 ? ['Sale','Credit-Note']: ['Sale']])
                    ->andWhere(['between','DATE(posting_date)', 
                    date('Y-m-d 00:00:00', strtotime(date('Y').'-'.$data->m.'-01')), 
                    date('Y-m-t 23:59:59', strtotime(date('Y').'-'.$data->m.'-01'))])
                    ->groupby('DATE(posting_date)')
                    ->all();  

            foreach ($query as $key => $model) {
                $raw[] = (Object)[
                    'days'      => (int)date('d', strtotime($model->posting_date)),
                    'date'      => $model->posting_date,
                    'balance'   => $model->total * 1
                ];
            }
        }
         

        
        

        return json_encode([
            'data' => 'api',
            'status' => 200,
            'raw' => $raw
        ]);
    }

}
