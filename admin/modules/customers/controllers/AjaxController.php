<?php

namespace admin\modules\customers\controllers;

use Yii;
use yii\db\Expression;
use common\models\Customer;
use common\models\SalesHasCustomer;
use common\models\Address;
use yii\filters\VerbFilter;
use common\models\CustomerGroups;

class AjaxController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update-policy' => ['POST'],
                    'update-status' => ['POST'],
                    'find-cust' => ['POST'],
                    'find-customer-group' => ['POST'],
                    'duedate-for-discount' => ['POST'],
                    'update-nick-name' => ['POST'],
                    'save-promotion' => ['POST'],
                    'delete-attach' => ['POST']
                ],
            ],
        ];
    }

	public function actionJsonGetCustomer($id=1)
    {


        $model = Customer::findOne($id);

        if($model->transport=='')   $model->transport   = $model->name;
        if($model->province=='')    $model->province    = 'Province';
        if($model->city=='')        $model->city        = 'city';
        if($model->district=='')    $model->district    = 'district';
        //if($model->postcode=='') $model->postcode = '10150';

        $sales  = @explode(',',$model->owner_sales);

        // Default Bangkok
        //if($model->postcode=='') $model->postcode = '10100';district

        $data = [
            'name'                  => $model->name,
            'address'               => $model->address,
            'address2'              => $model->address2,
            'city_code'             => $model->city,
            'district_code'         => $model->district,
            'province_code'         => $model->province,
            'district'              => $model->locations->tumbol,
            'city'                  => $model->locations->amphur,
            'province'              => $model->locations->province,
            'postcode'              => $model->postcode,
            'transport'             => $model->transport,
            'code'                  => $model->code,
            'vatregis'              => $model->vat_regis,
            'branch'                => $model->branch,
            'branch_name'           => $model->branch_name,
            'payment_term'          => $model->payment_term,
            'contact'               => $model->contact,
            'phone'                 => $model->phone,
            'fax'                   => $model->fax,
            'email'                 => $model->email,
            'owner_sales'           => $sales[0],
            'vatbus_postinggroup'   => $model->vatbus_postinggroup,
            'fulladdress'           => $model->locations->address,
            'credit_limit'          => (float)$model->credit_limit,
            'credit_remain'         => (float)$model->credit->CreditRemaining,
            'credit_available'      => (float)$model->credit->CreditAvailable
        ];


        return json_encode($data);


    }

    public function actionFindCustomer()
    {
        $words = explode(" ",Yii::$app->request->post('word'));


        $query = Customer::find()->where(
            ['or',
                ['like','name', $words],
                ['like','code', $words],
                ['like','vat_regis', $words],
                ['like','phone', $words]
            ])
            ->andWhere(['status' => 1])
            ->andWhere(['suspend' => 0])
            ->andWHere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->limit(10)->all();

        $data = [];

        if($query!=null){
            foreach ($query as $key => $model) {
                $data[] = (Object)[
                    'id' => $model->id,
                    'name' => $model->name,
                    'code' => $model->code,
                    'province' => $model->locations->state
                ];
            }
        }
        

        return json_encode($data);
        
    }

    public function actionFindCustomerGroup(){

        $words = explode(" ",Yii::$app->request->post('word'));


        $query = CustomerGroups::find()->where(
            ['or',
                ['like','name', $words],
                ['like','detail', $words]
            ])
            ->andWHere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->all();

        $data = [];

        if($query!=null){
            foreach ($query as $key => $model) {
                $data[] = (Object)[
                    'id' => $model->id,
                    'name' => $model->name,
                    'detail' => $model->detail,
                    'count' => count($model->customers)
                ];
            }
        }
        

        return json_encode($data);
    }

    public function actionJsonGetAddress($id)
    {
        //return $_POST['param']['item'];


        $model = Address::findOne($id);

        if($model->transport=='') $model->transport = $model->name;
        $data = [
                'name'      => $model->source_name,
                'address'   => $model->address,
                'address2'  => $model->address2,
                'district'  => $model->district,
                'city'      => $model->city,
                'province'  => $model->province,
                'postcode'  => $model->postcode,
                'transport' => $model->transport,
								'remark' 		=> $model->remark,
								'comment' 	=> $model->comment
        ];


        return json_encode($data);

    }


    public function actionFindCust(){

        $words = explode(" ",$_POST['word']);


        $query = Customer::find()->where(['or',
            ['like','Description', $words],
            ['like','description_th', $words],
            ['like','master_code', $words],
            ['like','barcode', $words]
        ])->orderBy(['StandardCost' => SORT_DESC]);

        // '.wordwrap($value->description_th, 20, "<br/>\r\n").'
        // '.mb_substr($value->Description, 0,40).'
        $html = '';

        if($query->exists()){

            foreach ($query->all() as $model) {




                $html.= '<a href="#true" itemno="'.$model->No.'" desc="'.$model->description_th.'" price="'.$model->StandardCost.'"  class="pick-item-to-createline" >';
                $html.= '<div class="panel panel-info">';
                $html.= '   <div class="panel-body">';


                $html.= '       <div class="row">';

                $html.= '           <div class="col-md-1 col-sm-2">'.Html::img($model->getPicture(),['class' => 'img-responsive','style' => 'max-width:100px; margin-bottom:20px;']).'</div>';

                $html.= '           <div class="col-md-11 col-sm-10">';

                // $html.= '           <div class="row">';
                // $html.= '               <div class="col-xs-12 text-right"><span class="find-price">
                //                             <p class="price">'.Yii::t('common','Price').'</p>'.number_format($model->StandardCost,2).'</span>
                //                         </div>';
                // $html.= '           </div>';

                $html.= '           <div class="row">';
                $html.= '               <div class="col-md-10 col-xs-8">'.$model->description_th.'</div>';
                $html.= '               <div class="col-md-2 col-xs-4 text-right">
                                            <span class="find-price"><p class="price">'.Yii::t('common','Price').'</p>';

                $html.= '                       '.number_format($model->StandardCost,2);
                //$html.= '                       <input type="text" class="form-control text-right no-border" value="'.number_format($model->StandardCost,2).'"></span>';

                $html.= '                   </span>
                                        </div>';
                $html.= '           </div>';

                $html.= '           <div class="row">';
                $html.= '               <div class="col-xs-12"><span class="text-sm text-gray">'.$model->Description.'</span></div>';
                $html.= '               <div class="col-xs-12"><label class="text-black">'.Yii::t('common','Code').' : '.$model->master_code.'</label></div>';
                $html.= '           </div>';

                $html.= '           <div class="row">';
                $html.= '               <div class="col-xs-8"><label>'.Yii::t('common','Stock').'</label></div>
                                        <div class="col-xs-4 text-right"><span class="text-gray">'.number_format($model->getInven($model),2).'</span></div>';
                $html.= '           </div>';
                $html.= '           </div>';

                $html.= '       </div>';

                $html.= '   </div>';
                $html.= '</div>';
                $html.= '</a>';
            }
        }else {
            $html.='<div><i class="fa fa-search fa-4x text-warning" aria-hidden="true"></i> Sory! No results found.</div>';

        }

        return $html;
    }

    public function actionUpdatePolicy($id){
        $model = $this->findModel($id);
        $model->show_item_code = $_POST['val'];
        if($model->save(false)){
            return json_encode([
                'status' => 200,
                'message' => 'done',
                'value' => [
                    'id' => $id,
                    'value' => $_POST['val']
                ]
            ]);
        }else{
            return json_encode([
                'status' => 500,
                'message' => json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE),
                'value' => [
                    'id' => $id,
                    'value' => $_POST['val']
                ]
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateStatus($id)
    {
         
        $model = $this->findModel($id);


        if($_POST['param']['val']=='true')
        {
            $model->status = 1;
            $model->save(false);
        }else {
            $model->status = 0;
            $model->save(false);
        }

        return json_encode([
                'status' => 200,
                'name' => $model->name,
                'message' => 'Saved',
        ]);
         
    	 
    }

    public function actionVatAvalibleBusinessType($id){
        $model = \common\models\CommonBusinessType::findOne($id);
        if ($model != null){
            return json_encode([
                'status' => 200,
                'data' => [
                    'id' => $model->id,
                    'allow_vat' => $model->allow_vat,
                    'name ' => Yii::t('common',$model->name)
                ]
            ]);
        }else{
            return json_encode([
                'status' => 404,
            ]);
        }
    }

    public function actionDuedateForDiscount(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $keys           = 'DuedateForDiscount&comp'.Yii::$app->session->get('Rules')['comp_id'].'&sale'.Yii::$app->session->get('Rules')['sale_id'];
        $data           = Yii::$app->cache->get($keys);
        if($data){
            return $data;
        }else{
            
            $query = SalesHasCustomer::find()
            ->joinwith('customer')
            ->select('customer.id, customer.name as name, customer.code as code, customer.payment_due as duedate')
            ->where(['sales_has_customer.sale_id' => Yii::$app->session->get('Rules')['sale_id']])
            ->andWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
            ->andWhere(['<>','customer.payment_due','' ])
            ->all();

            $data = [];
            foreach ($query as $key => $model) {
                $data[] =  [
                    'id' => $model->id,
                    'code' => $model->code,
                    'name' => $model->name,
                    'duedate' => $model->duedate
                ];
            }
            //return $this->asJson($data);
            Yii::$app->cache->set($keys,json_encode($data),(60 * 60 * 8));
            return Yii::$app->cache->get($keys);
        }

    }

    public function actionUpdateNickName(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;  
        $message        = Yii::t('common','{:e}',[':e' => 'Success']);

        $transaction    = Yii::$app->db->beginTransaction();
        try {
             
            $model      = Customer::findOne($data->id);
            $model->nick_name  = $data->name;
            if(!$model->save()){
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
            

            $transaction->commit();  
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }


    public function actionSavePromotion(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;  
        $message        = Yii::t('common','{:e}',[':e' => 'Success']);

        $transaction    = Yii::$app->db->beginTransaction();
        try {
             
            $model      = new \common\models\AttachData();
            $model->table_name  = 'customer';
            $model->ref_id      = $data->id;
            $model->data_file   = '';
            $model->title       = $data->title;
            $model->create_date = $data->date;
            $model->exp_date    = date('Y-m-d');
            $model->user_id     = Yii::$app->user->identity->id;
            $model->remark      = $data->remark;

            
            
            if(!$model->save()){
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }else{

                // Save file --->
                if($data->file){
                    $img = $data->file;

                    list($type, $img)   = explode(';', $img);
                    list(, $img)        = explode(',', $img);
                    $sourceImage        = base64_decode($img);

                    $fileName           = base64_encode($model->ref_id.'_'.$data->title.'_'.date('y_m_d_H_i_s')).self::fileType($type);

                    file_put_contents(Yii::getAlias('@webroot').'/'.'uploads/'.$fileName, $sourceImage);
                    $model->data_file = $fileName;

                    $model->save();
                }
                // <--- Save file 

            }
            

            $transaction->commit();  
        } catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}',[':e' => $e]);
        }

        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

    protected static function fileType($type){
        switch ($type) {
            case 'data:image/jpeg':
                return '.jpg';
                break;

            case 'data:image/png':
                return '.png';
                break;

            case 'data:application/pdf':
                return '.pdf';
                break;     

            default:
                return '.jpg';
                break;
        } 
    }

    public function actionDeletePromotion(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $model          = \common\models\AttachData::findOne($data->id);
        $status         = 200;
        $message        = 'Done';


        try{ // Line Notify                                            
                
            $bot =  \common\models\LineBot::findOne(5);
            $msg = "\r\nPromotion\r\n\r\n";
            $msg.= $model->title."\r\n";
            $msg.= $model->remark."\r\n\r\n";           
            $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
            
            $bot->notify_message($msg);					

        } catch (\Exception $e) {					 
            
        }

        if($model->delete()){
           
        }else{
            $status     = 500;
        }

        return json_encode([
            'status' => $status
        ]);


    }

}
