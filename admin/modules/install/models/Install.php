<?php

namespace admin\modules\install\models;

use Yii;
use \common\models\NumberSeries;
use admin\models\Generater;
use common\models\User;
use common\models\SalesPeople;
use common\models\Location;
use common\models\ProjectControl;
/**
 * This is the model class for table "sale_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $comp_id
 */
class Install extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function create($main){

        return (Object)[
            'status' => 500,
        ];
    }
    static function findSeriesModel($obj){
        $comp_id    = Yii::$app->session->get('Rules')['comp_id'];
        $model = NumberSeries::find()
                ->where(['comp_id' => $comp_id])
                ->andWhere(['name' => $obj->n])
                ->andWhere(['table_name' => $obj->t])
                ->andWhere(['field_name' => $obj->f])
                ->andWhere(['cond' => $obj->c]);
        
        return $model;
    }
    
    public static function createSeries($main){

        $comp_id    = Yii::$app->session->get('Rules')['comp_id'];

        if($comp_id==null){
            return (Object)['status' => 404,'message' => 'token not found.'];
        }

        // VENDOR
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'vendors',
            'field'     => 'vatbus_posting_group',
            'cond'      => '01',
            'separate'  => '-', 
            'char'      => 'VD',            
            'desc'      => 'เจ้าหนี้(ในประเทศ) Vendor Domestic',
            'format'    => 'ONCE'
        ]);

        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'vendors',
            'field'     => 'vatbus_posting_group',
            'cond'      => '02',
            'char'      => 'VO',
            'separate'  => '-',
            'format_gen'=> '000',
            'desc'      => 'เจ้าหนี้(ต่างประเทศ) Vendor Oversea',
            'format'    => 'ONCE'
        ]);

        // Purchase Request
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'purchase_req_header',
            'field'     => 'doc_no',
            'cond'      => 'all',
            'char'      => 'REQ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '0000',  
            'desc'      => 'ใบขอซื้อ',
            'format'    => '12M'
        ]);

        // Purchase
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'purchase_header',
            'field'     => 'vatbus_posting_group',
            'cond'      => '01',
            'char'      => 'PO',     
            'separate'  => 'YYMM-',      
            'desc'      => 'ใบสั่งซื้อ (ใน) ประเทศ',
            'format'    => '12M'
        ]);

        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'purchase_header',
            'field'     => 'vatbus_posting_group',
            'cond'      => '02',
            'char'      => 'POTD',            
            'desc'      => 'ใบสั่งซื้อ (ต่าง) ประเทศ',
            'format'    => '12M'
        ]);

        // Purchase Receive Vat
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'purchase_header',
            'field'     => 'include_vat',
            'cond'      => '1',
            'char'      => 'RC',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '0000',  
            'desc'      => 'ใบรับสินค้า Purchase Receive (Include Vat)',
            'format'    => '12M'
        ]);

        // Purchase Receive Novat
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Purchase',
            'table'     => 'purchase_header',
            'field'     => 'include_vat',
            'cond'      => '0',
            'char'      => 'RCE',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '0000',  
            'desc'      => 'ใบรับสินค้า Purchase Receive (Exclude Vat)',
            'format'    => '12M'
        ]);



        // POS
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleOrder',
            'table'     => 'sale_event_header',
            'field'     => 'vat_value',
            'cond'      => '1',
            'char'      => 'POS',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ระบบขายหน้าร้าน (Point Of Sale)',
            'format'    => '12M'
        ]);  


         // Sale Quotation

         $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleQuote',
            'table'     => 'sale_quote_header',
            'field'     => 'no',
            'cond'      => 'all',
            'char'      => 'SQ',            
            'desc'      => 'ใบเสนอราคาขาย',
            'format'    => '12M'
        ]);

        // Sale Order

        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleOrder',
            'table'     => 'sale_header',
            'field'     => 'no',
            'cond'      => 'all',
            'char'      => 'SO',            
            'desc'      => 'ใบสั่งขาย',
            'format'    => '12M'
        ]);


        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleOrder',
            'table'     => 'customer',
            'field'     => 'vatbus_postinggroup',
            'cond'      => '01',
            'separate'  => '-', 
            'char'      => 'CUD',            
            'desc'      => 'ลูกหนี้(ในประเทศ) Customer Domestic',
            'format'    => 'ONCE'
        ]);

        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleOrder',
            'table'     => 'customer',
            'field'     => 'vatbus_postinggroup',
            'cond'      => '02',
            'char'      => 'CUO',
            'separate'  => '-',
            'format_gen'=> '000',
            'desc'      => 'ลูกหนี้(ต่างประเทศ) Customer Oversea',
            'format'    => 'ONCE'
        ]);

        // Sale Return Order
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleReturnOrder',
            'table'     => 'sale_return_header',
            'field'     => 'no',
            'cond'      => 'all',
            'char'      => 'TR',
            'separate'  => '-',
            'format_gen'=> '000',
            'desc'      => 'ใบเบิกสินค้า',
            'format'    => '12M'
        ]);


        // Shipment
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Shipment',
            'table'     => 'warehouse_moving',
            'field'     => 'no',
            'cond'      => 'all',
            'char'      => 'SH',     
            'separate'  => 'YYMM-',      
            'desc'      => 'ใบส่งสินค้า',
            'format'    => '12M'
        ]);


        // Financial
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Financial',
            'table'     => 'billing_note',
            'field'     => 'vat_type',
            'cond'      => '0',
            'char'      => 'BI',     
            'separate'  => 'YYMMTH-',      
            'desc'      => 'ใบวางบิล Billing',
            'format'    => '12M'
        ]);


        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleInvoice',
            'table'     => 'vat_type',
            'field'     => 'vat_value',
            'cond'      => '0',
            'char'      => 'IV',     
            'separate'  => 'YYMMTH-',      
            'desc'      => 'ใบส่งสินค้าชั่วคราว/บิล ​No Vat',
            'format'    => '12M'
        ]);


        $series[] = self::createRuningSeries((Object)[
            'name'      => 'SaleInvoice',
            'table'     => 'vat_type',
            'field'     => 'vat_value',
            'cond'      => '7',
            'char'      => 'I',     
            'separate'  => 'YYMMTH-',      
            'desc'      => 'ใบกำกับภาษี/ใบแจ้งหนี้',
            'format'    => '12M'
        ]);

        $series[] = self::createRuningSeries((Object)[
            'name'      => 'CreditNote',
            'table'     => 'rc_invoice_header',
            'field'     => 'no_',
            'cond'      => 'all',
            'char'      => 'CN',
            'format_gen'=> '000',  
            'separate'  => 'YYMMTH-',      
            'desc'      => 'ใบลดหนี้/รับคืนสินค้า',
            'format'    => '12M'
        ]);



        // Item Reclass Journal
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Reclass',
            'table'     => 'item_reclass_journal',
            'field'     => 'DocumentNo',
            'cond'      => 'all',
            'char'      => 'TR',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'การโอนย้ายสินค้า ระหว่างคลัง',
            'format'    => '12M'
        ]);

        // Adjust Positive
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Adjust',
            'table'     => 'item_journal',
            'field'     => 'Adjust',
            'cond'      => '+',
            'char'      => 'PAJ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ปรับสต๊อก(เข้า) Positive Adjust (+)',
            'format'    => '12M'
        ]);

        // Adjust Output
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Adjust',
            'table'     => 'item_journal',
            'field'     => 'Output',
            'cond'      => '+',
            'char'      => 'OAJ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ปรับสต๊อก(เข้า) Output Direct (+)',
            'format'    => '12M'
        ]);

        // Adjust Purchase
        $series[]= self::createRuningSeries((Object)[
            'name'      => 'Adjust',
            'table'     => 'item_journal',
            'field'     => 'Purchase',
            'cond'      => '+',
            'char'      => 'RCAJ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ปรับสต๊อก(เข้า) Purchase Direct Receive (+)',
            'format'    => '12M'
        ]);


        // Adjust Negative
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Adjust',
            'table'     => 'item_journal',
            'field'     => 'Adjust',
            'cond'      => '-',
            'char'      => 'NAJ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ปรับสต๊อก(ออก) Negative Adjust (-)',
            'format'    => '12M'
        ]);


         // Adjust Shipment
         $series[] = self::createRuningSeries((Object)[
            'name'      => 'Adjust',
            'table'     => 'item_journal',
            'field'     => 'Sale',
            'cond'      => '-',
            'char'      => 'SAJ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ปรับสต๊อก(ออก) Product Shipment (-)',
            'format'    => '12M'
        ]);


        

        // Adjust Consumption
        $series[] = self::createRuningSeries((Object)[
            'name'      => 'Adjust',
            'table'     => 'item_journal',
            'field'     => 'Consumption',
            'cond'      => '-',
            'char'      => 'OAJ',     
            'separate'  => 'YYMM-',    
            'format_gen'=> '000',  
            'desc'      => 'ปรับสต๊อก(ออก) Consumption Direct (-)',
            'format'    => '12M'
        ]);

       


        
       
        
        return (Object)[
            'status'    => 200,
            'message'    => $series,
        ];
    }

    

    
    





    static function createRuningSeries($obj){

        $message    = $obj->desc;
        $model = self::findSeriesModel((Object)[
            'n' => $obj->name,
            't' => $obj->table,
            'f' => $obj->field,
            'c' => $obj->cond
            ])->one();


        if($model !== null){

            $status = 204;


            if($nos = \common\models\SetupNoSeries::findOne(['no_series' => $model->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]) === null){
                $setupnos = new \common\models\SetupNoSeries();
                $setupnos->form_id      = (isset($obj->name))?  $obj->name : 'Error';
                $setupnos->form_name    = (isset($obj->desc))?  $obj->desc : 'Error';
                $setupnos->no_series    = $model->id;
                $setupnos->comp_id      = Yii::$app->session->get('Rules')['comp_id'];
                $setupnos->save(false);  
            }
        }else {

            $No = new NumberSeries();
            $No->name           = ($obj->name)?             $obj->name : 'Error';
            $No->starting_no    = (isset($obj->starting))?  $obj->starting : NULL;
            $No->ending_no      = (isset($obj->ending))?    $obj->ending : NULL;
            $No->last_date      = (isset($obj->date))?      $obj->date : NULL;
            $No->last_no        = (isset($obj->last_no))?   $obj->last_no : NULL;
            $No->default_no     = (isset($obj->nos))?       $obj->nos : NULL;
            $No->manual_nos     = (isset($obj->nos))?       $obj->nos : NULL;
            $No->type           = (isset($obj->type))?      $obj->type : 'Item';
            $No->comp_id        = Yii::$app->session->get('Rules')['comp_id'];
            $No->table_name     = $obj->table;
            $No->field_name     = $obj->field;
            $No->cond           = $obj->cond;
            $No->starting_char  = $obj->char;
            $No->separate       = (isset($obj->separate))?  $obj->separate : 'YYMM-';
            $No->format_gen     = (isset($obj->format_gen))?$obj->format_gen : '0000';
            $No->description    = (isset($obj->desc))?      $obj->desc : 'Error';
            $No->format_type    = (isset($obj->format))?    $obj->format : '12M';
            if($No->save()){
                $status = 200;

                Generater::newGenerate($No->id);
            }else{
                $status     = 500; 
                $message    = json_encode($No->getErrors(),JSON_UNESCAPED_UNICODE);
            }

            if($nos = \common\models\SetupNoSeries::findOne(['no_series' => $No->id,'comp_id' => Yii::$app->session->get('Rules')['comp_id']]) === null){
                $setupnos = new \common\models\SetupNoSeries();
                $setupnos->form_id      = (isset($obj->name))?  $obj->name : 'Error';
                $setupnos->form_name    = (isset($obj->desc))?  $obj->desc : 'Error';
                $setupnos->no_series    = $No->id;
                $setupnos->comp_id      = Yii::$app->session->get('Rules')['comp_id'];
                $setupnos->save(false);  
            }

        } 

        return (Object)[
            'status'    => $status,
            'name'      => $obj->desc,
            'message'   => $message
        ];

    }







    public function createDefaultData(){

        
        
        $data[] = self::genSalePerson((Object)[
            'code'      => 'CENTER',
            'name'      => 'CENTER',
            'user_id'   => Yii::$app->user->identity->id,
            'rules_id'  => 1,
        ]);

        $data[] = self::createLocations((Object)[
            'code'      => 'WH-FG',
            'name'      => 'คลังสินค้าสำเร็จรูป',
            'default'   => 1
        ]);


        $data[] = self::createProject((Object)[
            'title'      => 'ไม่ระบุ',
            'name'      => 'ไม่ระบุ',
            'place'     => 'ไม่ระบุ',
            'budget'    => 0,
            'default'   => 1
        ]);
    
        
        
        return (Object)[
            'status'    => 200,
            'message'    => $data,
        ];
        
    }

    static function genSalePerson($obj){
        $user       = User::findOne(Yii::$app->user->identity->id);
        $model      = SalesPeople::findOne(['name' => $obj->name,'comp_id' =>  $user->register->comp_id]);
        $message    = $obj->name;

        if($model !==null){
            $status = 204;
        }else{
            $model = new SalesPeople();
            $model->code            = $obj->code;
            $model->name            = $obj->name;     
            $model->user_id         = $obj->user_id; 
            $model->comp_id         = $user->register->comp_id;
            $model->status          = '1';       

            if($model->save()){
                self::updateRules($model->id);
                $status = 200;                
            }else{
                $status = 500; 
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }
        return (Object)[
            'status'    => $status,
            'name'      => 'Sale -> '.$obj->name,
            'message'   => $message
        ];
    }

    static function updateRules($id){
        $model      = \common\models\AppsRules::findOne(['user_id' => Yii::$app->user->identity->id,'sales_id' => 'CENTER']);
        $model->sale_id     = $id;
        $model->users       = Yii::$app->user->identity->id;
        if(!$model->save()){
            return json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
        }
    }




    static function createLocations($obj){
 
        $model      = Location::findOne(['code' => $obj->code,'comp_id' =>  Yii::$app->session->get('Rules')['comp_id']]);
        $message    = $obj->name;

        if($model !==null){
            $status = 204;
        }else{
            $model = new Location();
            $model->code            = $obj->code;
            $model->name            = $obj->name;  
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->status          = '1';
            $model->defaultlocation = $obj->default;       

            if($model->save()){
                self::updateRules($model->id);
                $status = 200;                
            }else{
                $status = 500; 
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }
        return (Object)[
            'status'    => $status,
            'name'      => 'Location -> '.$obj->name,
            'message'   => $message
        ];
    }


    static function createProject($obj){
 
        $model      = ProjectControl::findOne(['title' => $obj->title,'comp_id' =>  Yii::$app->session->get('Rules')['comp_id']]);
        $message    = $obj->name;

        if($model !==null){
            $status = 204;
        }else{
            $model = new ProjectControl();
            $model->title           = $obj->title;
            $model->name            = $obj->name;  
            $model->place           = $obj->place;  
            $model->budget          = $obj->budget;  
            $model->create_date     = date('Y-m-d H:i:s');
            $model->comp_id         = Yii::$app->session->get('Rules')['comp_id'];
            $model->user_id         = Yii::$app->user->identity->id;
            $model->status          = 1;  


            if($model->save()){
                self::updateRules($model->id);
                $status = 200;                
            }else{
                $status = 500; 
                $message = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }
        return (Object)[
            'status'    => $status,
            'name'      => 'Project -> '.$obj->name,
            'message'   => $message
        ];
    }
}
