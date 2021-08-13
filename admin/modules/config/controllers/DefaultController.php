<?php

namespace admin\modules\config\controllers;

use Yii;
use yii\web\Controller;
use common\models\SaleHeader;
use common\models\SaleInvoiceHeader;
use common\models\RcInvoiceHeader;
use common\models\Options;
/**
 * Default controller for the `config` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $stock         = Options::find()
                            ->where(['table_name'       => 'warehouse_moving'])
                            ->andWhere(['table_case'    => 'on_sale_order'])
                            ->andWhere(['comp_id'       => Yii::$app->session->get('Rules')['comp_id']])
                            ->one();
        if($stock == null){
            $stock             = new Options;
            $stock->table_name = 'warehouse_moving';
            $stock->table_case = 'on_sale_order';
            $stock->enabled    = 0;
            $stock->create_date = date('Y-m-d H:i:s');
            $stock->modify_date = date('Y-m-d H:i:s');
            $stock->user_id     = Yii::$app->user->identity->id;
            $stock->comp_id    = Yii::$app->session->get('Rules')['comp_id'];
            if($model->save()){
                $status = 200;
            }else{
                $status = 500;
            }
        }       

        return $this->render('index', [
            'stock' => $stock,
            'system' => Options::find()
            ->where(['table_name' => 'options'])
            ->andWhere(['table_case' => 'system_status'])
            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->one()
        ]);
    }
    public function actionSaleHeaderCheck(){
        // $query = SaleHeader::find()
        // ->joinwith('sales')
        // ->where(['<>','sales_people.id','sale_header.sale_id']);
        $sql = "SELECT 
                    h.order_date,
                    h.id,
                    h.no,
                    h.sales_people,
                    h.sale_id,
                    h.discount,
                    h.percent_discount,
                    h.include_vat,
                    s.id as sale,
                    s.name
                FROM `sale_header` as h INNER JOIN sales_people as s 
                ON h.sales_people = s.code AND h.comp_id = s.comp_id           
                WHERE s.id <> h.sale_id";

        $query = \Yii::$app->db->createCommand($sql)->queryAll();

        return $this->render('_sale_header_check',[
            'query' => $query,   
        ]);

    }

    public function actionSaleHeaderFixed(){
        // $query = SaleHeader::find()
        // ->joinwith('sales')
        // ->where(['<>','sales_people.id','sale_header.sale_id']);
        $sql = "SELECT 
                    h.order_date,
                    h.id,
                    h.no,
                    h.sales_people,
                    h.sale_id,
                    h.discount,
                    h.percent_discount,
                    h.include_vat,
                    s.id as sale,
                    s.name
                FROM `sale_header` as h INNER JOIN sales_people as s 
                ON h.sales_people = s.code AND h.comp_id = s.comp_id  
                WHERE s.id <> h.sale_id";

        $query = \Yii::$app->db->createCommand($sql)->queryAll();

        foreach($query as $key => $model){
            $Header             = SaleHeader::findOne($model['id']);
            $Header->sale_id    = $model['sale'];            
            if($Header->save()){
                $query = \Yii::$app->db->createCommand($sql)->queryAll();
            }else {
                \Yii::$app->session->setFlash('error', json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE));
            }
        }

        
        
        return $this->render('_sale_header_check',[
            'query' => $query,   
        ]);

    }



    public function actionSaleInvoiceCheck(){

        $sql = "SELECT 
                    h.posting_date,
                    h.id,
                    h.no_ as no,
                    h.sales_people,
                    h.sale_id,
                    h.discount,
                    h.percent_discount,
                    h.include_vat,
                    s.id as sale,
                    s.name
                FROM `sale_invoice_header` as h INNER JOIN sales_people as s 
                ON h.sales_people = s.code   AND h.comp_id = s.comp_id                
                WHERE s.id <> h.sale_id";

        $query = \Yii::$app->db->createCommand($sql)->queryAll();
        if(isset($_GET['action'])){
            if(count($query)>0){
                foreach($query as $key => $model){
                    $Header             = SaleInvoiceHeader::findOne($model['id']);
                    $Header->sale_id    = $model['sale'];            
                    if($Header->update(false)){
                        $query = \Yii::$app->db->createCommand($sql)->queryAll();
                        \Yii::$app->session->setFlash('success', 'Complete');
                    }else {
                        \Yii::$app->session->setFlash('error', json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE));
                    }
                }
            }else{
                \Yii::$app->session->setFlash('info', 'Nothing');
            }
            
        }

        return $this->render('sale_invoice_check',[
            'query' => $query,   
        ]);

    }

    public function actionPostedInvoiceCheck(){

        $sql = "SELECT 
                    h.posting_date,
                    h.id,
                    h.no_ as no,
                    h.sales_people,
                    h.sale_id,
                    h.discount,
                    h.percent_discount,
                    h.include_vat,
                    s.id as sale,
                    s.name
                FROM `rc_invoice_header` as h INNER JOIN sales_people as s 
                ON h.sales_people = s.code   AND h.comp_id = s.comp_id                
                WHERE s.id <> h.sale_id";

        $query = \Yii::$app->db->createCommand($sql)->queryAll();
        if(isset($_GET['action'])){
            if(count($query)>0){
                foreach($query as $key => $model){
                    $Header                 = RcInvoiceHeader::findOne($model['id']); 
                    $Header->sale_id        = $model['sale'];            
                    if($Header->update(false)){
                        $query = \Yii::$app->db->createCommand($sql)->queryAll();
                        \Yii::$app->session->setFlash('success', 'Complete');
                    }else {
                        \Yii::$app->session->setFlash('error', json_encode($Header->getErrors(),JSON_UNESCAPED_UNICODE));
                    }
                }
            }else{
                \Yii::$app->session->setFlash('info', 'Nothing');
            }
            
            
            
        }

        return $this->render('posted_invoice_check',[
            'query' => $query,   
        ]);

    }


    public function actionStockLocker(){
        $request_body   = file_get_contents('php://input');
        $body           = json_decode($request_body);
        $status         = 200;
        $message        = 'done';
        $model          = Options::find()
                            ->where(['table_name'       => 'warehouse_moving'])
                            ->andWhere(['table_case'    => 'on_sale_order'])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->one();
        if($model != null){
            $model->enabled     = $body->enabled;
            $model->user_id     = Yii::$app->user->identity->id;
            $model->modify_date = date('Y-m-d H:i:s');
            if($model->save()){
                $status     = 200;
            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }else {
            $model              = new Options;
            $model->table_name  = 'warehouse_moving';
            $model->table_case  = 'on_sale_order';
            $model->enabled     = 0;
            $model->create_date = date('Y-m-d H:i:s');
            $model->modify_date = date('Y-m-d H:i:s');
            $model->user_id     = Yii::$app->user->identity->id;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            if($model->save()){
                $status     = 200;
            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }       

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => $body
        ]);
    }


    public function actionStartSystem(){
        $request_body   = file_get_contents('php://input');
        $body           = json_decode($request_body);
        $status         = 200;
        $message        = 'done';
        $model          = Options::find()
                            ->where(['table_name'       => 'options'])
                            ->andWhere(['table_case'    => 'system_status'])
                            ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->one();
        if($model != null){
            $model->enabled     = $body->enabled;
            $model->user_id     = Yii::$app->user->identity->id;
            $model->modify_date = date('Y-m-d H:i:s');
            if($model->save()){
                $status     = 200;
            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }else {
            $model              = new Options;
            $model->table_name  = 'options';
            $model->table_case  = 'system_status';
            $model->enabled     = 1;
            $model->create_date = date('Y-m-d H:i:s');
            $model->modify_date = date('Y-m-d H:i:s');
            $model->name        = 'ปิด-เปิดระบบ';
            $model->detail      = 'ปิดการทำงานหลักของการซื้อ-ขาย';
            $model->user_id     = Yii::$app->user->identity->id;
            $model->comp_id     = Yii::$app->session->get('Rules')['comp_id'];
            if($model->save()){
                $status = 200;
            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }       

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'data'      => $body
        ]);
    }

    public function actionListFile(){
        $files=\yii\helpers\FileHelper::findFiles('../../../app-assets/images/product');
        $raws = [];
        foreach ($files as $key => $value) {
            $raws[] = [
                'file' => str_replace("../../../app-assets/images/product","//assets.ewinl.com/images/product",$value),
                'path' => str_replace("../../../app-assets/images/product","",$value),
                'name' => basename($value)
            ];
        }
        return json_encode($raws);
    }

    public function actionListImage(){
        return $this->render('list_image');
 
    }

    public function actionRemoveFile(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;

        if(!unlink('../../../app-assets/images/product'.$data->src)){
            $status = 500;
        }

        return json_encode([
            'status' => $status    
        ]);
    }
}
