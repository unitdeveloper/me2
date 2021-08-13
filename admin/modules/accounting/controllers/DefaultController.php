<?php

namespace admin\modules\accounting\controllers;
use Yii;
use yii\web\Controller;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use common\models\WithholdingTax;
use common\models\WithholdingTaxLine;

/**
 * Default controller for the `accounting` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function action50tw(){
        return $this->render('50tw');
    }

    public function actionListWithholdingTax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $raws           = [];
        
        $query          = WithholdingTax::find()
                        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->all();

        foreach ($query as $key => $model) {
            $raws[] = (Object)[
                'id'        => $model->id,
                'no'        => $model->no,
                'date'      => $model->wht_date,
                'name'      => $model->vendors ? $model->vendors->name : '',
                'address'   => $model->vendors ? $model->vendor_address : '',
                'vat_regis' => $model->vat_regis,
                'book_id'   => $model->book_id ? $model->book_id : '',
                'book_no'   => $model->book_no ? $model->book_no : ''
            ];
        }

        return json_encode([
            'status' => 200,
            'raws' => $raws
        ]);
    }

    public function actionGetWithholdingTax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $raws           = [];
        $lines          = [];
        $status         = 200;

        $model          = WithholdingTax::findOne($data->id);

        if($model != null){

            $query = WithholdingTaxLine::find()->where(['source_id' => $model->id])->all();
            foreach ($query as $key => $line) {
                $lines[] = (Object)[
                    'id' => $line->wht_id,
                    'amount' => $line->wht_amount *1,
                    'vat'   => $line->wht_vat_amount *1,
                    'total' => $line->wht_total *1,
                    'other' => $line->wht_other
                ];
            }

            $raws = (Object)[
                'header' => [
                            'vendId'    => (int)$model->vendor_id,
                            'id'        => (int)$model->id,
                            'no'        => $model->no,
                            'code'      => trim($model->vendors ? $model->vendors->code : ''),
                            'name'      => trim($model->vendors ? $model->vendors->name : ''),
                            'address'   => trim($model->vendors ? $model->vendor_address : ''),
                            'vat_regis' => $model->vat_regis,
                            'docType'   => explode(',',$model->choice_substitute),
                            'payer'     => explode(',',$model->choice_payer),
                            'user_name' => $model->user_name,
                            'date'      => $model->wht_date,
                            'book_id'   => $model->book_id,
                            'book_no'   => $model->book_no,
                            'other'     => explode(',',$model->other_choice)
                        ],               
                'line'  => $lines
            ];

        }else{
            $status = 404;
        }

        return json_encode([
            'status' => $status,
            'raws' => $raws
        ]);
    }
    
    public function createHeader($data){
        $model         = new WithholdingTax();
         
        $vendors    = \common\models\Vendors::findOne($data->vendor);

        if($vendors!=null){
            $model->vendor_id       = $vendors->id;
            $model->book_id         = $data->book_id;
            $model->book_no         = $data->book_no;
            $model->vendor_name     = $data->vendor_name;
            $model->vendor_address  = $data->vendor_address;
            $model->vat_regis       = $data->vendor_vat_regis;
            $model->no              = $data->no;
            $model->user_name       = $data->user_name;

            $substitute = [];
            foreach ($data->type as $key => $value) {
                $substitute[] = $value->value;
            }
            $model->choice_substitute =  implode(',',$substitute);

            $choice_payer = [];
            foreach ($data->payer as $key => $value) {
                $choice_payer[]= $value->value;
            }

            $model->choice_payer        = implode(',',$choice_payer);
            $model->wht_date            = date('Y-m-d', strtotime($data->date));

            $model->user_id             = Yii::$app->user->identity->id;
            $model->comp_id             = Yii::$app->session->get('Rules')['comp_id'];

            if($model->save()){
                return $model;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }

    public function createLine($data){

        $raws = [];

        foreach ($data->line as $key => $prop) {
            # code...
            // Update Or Delete

            $model = WithholdingTaxLine::findOne(['wht_id' => $prop->id, 'source_id' => $data->source]);
            if($model != null){  
                
                if($prop->value == 0){     // ถ้าไม่มีจำนวน ให้ลบทิ้ง
                    $model->delete();
                }else{                      // ถ้ามีจำนวนให้ Update

                    $model->source_id       = $data->source;
                    $model->wht_id          = $prop->id;
                    $model->wht_other       = $prop->other;
                    $model->wht_date        = $data->date;
                    $model->wht_amount      = $prop->value;
                    $model->wht_vat_amount  = $prop->vat;
                    $model->wht_total       = $prop->value - $prop->vat;
        
                    if(!$model->save()){
                        $status = 500;
                        $message= json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                    }else{
                        $id = $model->id;
                    }

                    $raws[] = (Object)[
                        'source'    => $data->source,
                        'id'        => $id,
                        'status'    => $status,
                        'message'   => $message
                    ];
                }
            

            }else{ // สร้างใหม่

                if($prop->value != 0){ 
                    $model                  = new WithholdingTaxLine();
                    $model->source_id       = $data->source;
                    $model->wht_id          = $prop->id;
                    $model->wht_other       = $prop->other;
                    $model->wht_date        = $data->date;
                    $model->wht_amount      = $prop->value;
                    $model->wht_vat_amount  = $prop->vat;
                    $model->wht_total       = $prop->value - $prop->vat;
                    
                    if(!$model->save()){
                        $status = 500;
                        $message= json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                    }else{
                        $id = $model->id;
                    }

                    $raws[] = (Object)[
                        'source'    => $data->source,
                        'id'        => $id,
                        'status'    => $status,
                        'message'   => $message
                    ];
                }
            }          

        }

        return $raws;
    }

    public function actionCreateWithholdingLine(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $status         = 200;
        $message        = Yii::t('common','Success');
        $id             = 0;
        $raws           = [];
        $newSource      = 0;
        // Header
        $header         = WithholdingTax::findOne($data->source);

        if($header != null){

        
            if($header->vendor_id != $data->vendor){
                $vendors   = \common\models\Vendors::findOne($data->vendor);
                
                $header->vendor_id        = $vendors->id;
                $header->vendor_address   = $vendors->address;
                $header->vat_regis        = $vendors->vat_regis;
            }else{
                $header->vendor_name        = $data->vendor_name;
                $header->vendor_address     = $data->vendor_address;
                $header->vat_regis          = $data->vendor_vat_regis;
            }

            $header->book_id            = $data->book_id;
            $header->book_no            = $data->book_no;
            $header->no                 = $data->no;
            $header->user_name          = $data->user_name;

            $substitute = [];
            foreach ($data->type as $key => $value) {
                $substitute[]= $value->value;
            }
            $header->choice_substitute  =  implode(',',$substitute);

            $choice_payer = [];
            foreach ($data->payer as $key => $value) {
                $choice_payer[]= $value->value;
            }

            $other_choice = [];
            foreach ($data->other as $key => $value) {
                $other_choice[]= $value->value;
            }
            $header->other_choice       = implode(',',$other_choice);
            $header->choice_payer       = implode(',',$choice_payer);
            $header->wht_date           = date('Y-m-d', strtotime($data->date));
            $header->save();
            
            foreach ($data->line as $key => $prop) {
                # code...
                // Update Or Delete

                $model = WithholdingTaxLine::findOne(['wht_id' => $prop->id, 'source_id' => $data->source]);
                if($model != null){  
                    
                    if($prop->value == 0){     // ถ้าไม่มีจำนวน ให้ลบทิ้ง
                        $model->delete();
                    }else{                      // ถ้ามีจำนวนให้ Update

                        $model->source_id       = $data->source;
                        $model->wht_id          = $prop->id;
                        $model->wht_other       = $prop->other;
                        $model->wht_date        = $data->date;
                        $model->wht_amount      = $prop->value;
                        $model->wht_vat_amount  = $prop->vat;
                        $model->wht_total       = $prop->value - $prop->vat;
            
                        if(!$model->save()){
                            $status = 500;
                            $message= json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                        }else{
                            $id = $model->id;
                        }

                        $raws[] = [
                            'source'    => $data->source,
                            'id'        => $id,
                            'status'    => $status,
                            'message'   => $message
                        ];
                    }
                

                }else{ // สร้างใหม่

                    if($prop->value != 0){ 
                        $model                  = new WithholdingTaxLine();
                        $model->source_id       = $data->source;
                        $model->wht_id          = $prop->id;
                        $model->wht_other       = $prop->other;
                        $model->wht_date        = $data->date;
                        $model->wht_amount      = $prop->value;
                        $model->wht_vat_amount  = $prop->vat;
                        $model->wht_total       = $prop->value - $prop->vat;
                        
                        if(!$model->save()){
                            $status = 500;
                            $message= json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
                        }else{
                            $id = $model->id;
                        }

                        $raws[] = [
                            'source'    => $data->source,
                            'id'        => $id,
                            'status'    => $status,
                            'message'   => $message
                        ];
                    }
                }          
    
            }

            $newSource = $header->id;
            
        }else{
            $head = $this->createHeader($data);
            if($head != null){
                $newSource = $head->id;
                $this->createLine($data);
            }
            
        }

        return json_encode([
            'status' => 200,
            'raws' => $raws,
            'source' => $newSource
        ]); 
    }


    public function actionGetVendor(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
        $raws           = [];
        $status         = 200;

        $model          = \common\models\Vendors::find()
                        ->where(['code' => $data->code])
                        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                        ->one();
        if($model != null){
            
            $raws = (Object)[
                'id' => $model->id,
                'name' => $model->name,
                'address' => $model->address,
                'vat_regis' => $model->vat_regis
            ];
        }else{
            $status = 404;
        }
         

        return json_encode([
            'status' => $status,
            'raws' => $raws
        ]);
    }

    public function actionPrint50tw($id){
 
        $model              = WithholdingTax::findOne($id);
                            
        $Company            = \common\models\Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one(); 
        
        
        $vat_regis          = $Company->vat_register != '' 
                                ? array_map('intval', str_split($Company->vat_register))
                                : [];

        $vat_regis_vendor   = $model->vat_regis != '' 
                                ? array_map('intval', str_split($model->vat_regis))
                                : [];
        
        $query              = WithholdingTaxLine::find()->where(['source_id' => $model->id])->all();

       
        $content                = $this->renderPartial('pdf_print',[
            'query'             => $query,
            'model'             => $model,
            'Company'           => $Company, 
            'vat_regis'         => $vat_regis,
            'vat_regis_vendor'  => $vat_regis_vendor
        ]);

        $pdf = new Pdf([
            'mode'          => Pdf::MODE_UTF8,
            'format'        => [148,210],
            'orientation'   => Pdf::ORIENT_PORTRAIT,
            'destination'   => Pdf::DEST_BROWSER,
            'content'       => $content,
            //'cssFile'       => '@admin/web/css/saraban.css',
            'cssInline'     => '@page {margin: 0; } body, table>tr>td {font-family: saraban, garuda; font-size: 11px; font-weight: 900; color:#000;}',
            'filename'      => '50tawi.pdf',
            'options'       => ['title' => '50 ทวิ'],
            'methods'       => []
        ]);

        
        /* Thai Font */
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf->options['fontDir'] = array_merge($fontDirs, [
            Yii::getAlias('@webroot').'/fonts'
        ]);

        $pdf->options['fontdata'] = $fontData + [
            'saraban' => [
                'R' => 'thsarabunnew-webfont.ttf',
            ],
            'freesiaupc' => [
                'R' => 'FreesiaUPC.ttf', 
            ]
        ];
        
        return $pdf->render();
    }



    public function actionDeleteWithholdingTax(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);
 
        $status         = 200;
        $message        = Yii::t('common','Success');         
        $model          = WithholdingTax::findOne($data->id);

 
        $transaction = Yii::$app->db->beginTransaction();
        try {

           
            WithholdingTaxLine::deleteAll(['source_id' => $model->id]);

            $model->delete();
            
            $transaction->commit();
        } catch (\Exception $e) {

            $transaction->rollBack();
            $status     = 500;
            $message    = json_encode($e,JSON_UNESCAPED_UNICODE);
          
        }
            

        return json_encode([
            'status'    => $status,
            'message'   => $message,
        ]);
    }

}
