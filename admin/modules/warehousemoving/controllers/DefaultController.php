<?php

namespace admin\modules\warehousemoving\controllers;

use Yii;
use yii\web\Controller;
use admin\modules\warehousemoving\models\SaleOrderSearch;
use admin\modules\SaleOrders\models\OrderSearch;
use admin\models\FunctionCenter;

use common\models\SaleHeader;
use common\models\SaleLine;
/**
 * Default controller for the `warehousemoving` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->session->get('Rules')['rules_id'] == ''){
            return $this->redirect(['/site/index']);
        }

        if(\common\models\Options::getSystemStatus()){   
            $Fnc = new FunctionCenter();
            $Fnc->RegisterRule();


            if(Yii::$app->user->identity->id != 1){
                $session = \Yii::$app->session;
                $session->set('workdate', date('Y-m-d'));
            }
            

            $searchModel = new SaleOrderSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['sale_header.status' => 'Checking']);
            $dataProvider->query->andWhere(['<>','sale_header.customer_id','']);
            $dataProvider->query->andWHere(['sale_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            $dataProvider->query->andWHere(['sale_header.extra' => NULL]);
            //$dataProvider->query->andWHere(['sale_header.live' => 1]);
            
            $dataProvider->pagination=false;
            //$dataProvider->pagination->pageSize=50;

            // $modernTrade = new SaleOrderSearch();
            // $modernTrade = $searchModel->search(Yii::$app->request->queryParams);
            // $modernTrade->query->andWhere(['sale_header.status' => 'Checking']);
            // $modernTrade->query->andWhere(['<>','sale_header.customer_id','']);
            // $modernTrade->query->andWHere(['sale_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            // $modernTrade->query->andWHere(['IN', 'sale_header.extra',['reserve','wizard']]);
            // $modernTrade->query->andWhere(["YEAR(sale_header.order_date)" => Yii::$app->request->get('year') ? Yii::$app->request->get('year') : date('Y')]);
            // $modernTrade->pagination=false;

            if(Yii::$app->request->get('month')){
                $dataProvider->query->andWhere(["MONTH(sale_header.order_date)" => Yii::$app->request->get('month')]);
            }

            if(Yii::$app->request->get('years')){
                $dataProvider->query->andWhere(["YEAR(sale_header.order_date)" => Yii::$app->request->get('years')]);
            }else{
                $dataProvider->query->andWhere(["YEAR(sale_header.order_date)" => Yii::$app->request->get('year') ? Yii::$app->request->get('year') : date('Y')]);
            }


            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                //'moderntrade' => $modernTrade
            ]);
        }else{
            return $this->renderpartial('@admin/views/site/system-off');
        }
    }

    public function actionCountJob(){

        if(\common\models\Options::getSystemStatus()){   

            $searchModel = new SaleOrderSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['sale_header.status' => 'Checking']);
            $dataProvider->query->andWhere(['<>','sale_header.customer_id','']);
            $dataProvider->query->andWHere(['sale_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
            $dataProvider->query->andWHere(['sale_header.extra' => NULL]);
            $dataProvider->query->andWHere(['<=', 'sale_header.confirm', 0]);
            $dataProvider->query->andWhere(["YEAR(sale_header.order_date)" => date('Y')]);
 

            return json_encode([
                'status'    => 200,
                'qty'       => $dataProvider->getTotalCount()
            ]);

        }else{
            return json_encode([
                'status'    => 403,
                'qty'       => 0,
                'message'   => 'Frobbiden'
            ]);
        }      
        
    }

    public function actionView($id)
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $model = $this->findSaleHeaderModel($id);

        if($model->balance <= 0){

          return $this->redirect(['/SaleOrders/saleorder/update', 'id' => $model->id]);
        }

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->where(['order_no' => $model->no]);
        $dataProvider->query->andwhere(['comp_id' => $company]);
        $dataProvider->pagination->pageSize=100;



        //var_dump($searchModel);
        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findSaleHeaderModel($id)
    {
        if (($model = SaleHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionConfirm(){        
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = Yii::t('common','Success');
        $suggestion     = Yii::t('common','Done');

        $raws           = [];
        $totalConfirm   = 0;

        $model          = SaleHeader::findOne($data->id);
        $transaction    = Yii::$app->db->beginTransaction();
        try {

            SaleLine::updateAll(['quantity_to_invoice' => 0], ['sourcedoc' => $model->id]);

            foreach ($data->raw as $source) {

                $UpdateLine                         = SaleLine::findOne($source->id);
                
                $UpdateLine->quantity               = $source->qty;
                $UpdateLine->quantity_to_invoice    = $source->qty;
                
                if($UpdateLine->save()){
                    $raws[] = [
                        'id'        => $UpdateLine->id,
                        'qty'       => $UpdateLine->quantity,
                        'confirm'   => $UpdateLine->quantity_to_invoice,
                        'status'    => 200
                    ];
                }else{
                    $raws[] = [
                        'id'        => $UpdateLine->id,
                        'qty'       => $UpdateLine->quantity,
                        'confirm'   => $UpdateLine->quantity_to_invoice,
                        'status'    => 500
                    ];
                }
                $totalConfirm+= $UpdateLine->quantity_to_invoice;
            }

            $model->confirm         = $totalConfirm;
            $model->confirm_date    = date('Y-m-d H:i:s');
            $model->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','Error');
            $suggestion = Yii::t('common','{:e}',[':e' => $e]);
        }

        return json_encode([
            'status'    => $status,
            'raws'      => $raws,
            'message'   => $message,
            'suggestion'=> $suggestion
        ]);

    }

}
