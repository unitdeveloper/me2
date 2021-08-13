<?php

namespace admin\modules\Manufacturing\controllers;

use Yii;
use yii\helpers\Html;
use common\models\WarehouseHeader;
use common\models\WarehouseMoving;
use common\models\SaleLine;

use yii\web\Controller;


/**
 * Default controller for the `Manufacturing` module
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

    public function actionBomDetail(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body); 

        $status         = 200;
        $message        = Yii::t('common','Done');

        $model          = WarehouseHeader::findOne($data->id);        
        $line           = WarehouseMoving::find()->where(['source_id' => $model->id])->all();

        $raws           = [];
        foreach ($line as $key => $list) {
            $raws[] = [
                'id'        => $list->id,
                'date'      => $model->PostingDate,
                'item'      => $list->items->id,
                'name'      => $list->Description,
                'code'      => $list->items->master_code,
                'no'        => $model->DocumentNo,
                'qty'       => (float)$list->Quantity,
                'type'      => Yii::t('common',$list->TypeOfDocument),
                'remain'    => $list->qty_after * 1,
                'location'  => $list->location
            ];
        }


        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'no'        => $model->DocumentNo,
            'desc'      => $model->Description,
            'id'        => $model->id,
            'remark'    => $model->remark ? $model->remark : ' ',
            'type'      => $model->status,
            'raws'      => $raws
        ]);
    }


    public function actionBomRevert(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body); 

        $status         = 200;
        $message        = Yii::t('common','Done');

        $model          = WarehouseHeader::findOne($data->id);        
        $line           = WarehouseMoving::find()->where(['source_id' => $model->id])->all();


        $raws           = [];

        if($model->status != 'Undo-Produce'){
        
            foreach ($line as $key => $list) {
                $raws[] = $this->UndoProduction($model, $list);
            }

            if(isset($data->remark)){
                $model->remark = $data->remark;
            }
            $model->status = 'Undo-Produce';
            $model->save();

            try{ // Line Notify                                            
                                
                $bot =  \common\models\LineBot::findOne(2);
                $msg = 'UNDO'."\r\n";
                $msg.= Yii::t('common','Production')."\r\n";
                $msg.= $model->remark."\r\n\r\n";
                $msg.= $model->SourceDoc."\r\n";
                $msg.= $model->DocumentNo."\r\n";       
                $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                 
                $bot->notify_message($msg);					

            } catch (\Exception $e) {					 
                $status 		= 500;
                $message 		= Yii::t('common','{:e}',[':e' => $e]);	
            }	

        }else{
            $status = 403;
            $message = Yii::t('common','Already Undo');
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'no'        => $model->DocumentNo,
            'desc'      => $model->Description,
            'remark'    => $model->remark ? $model->remark : ' ',
            'raws'      => $raws
        ]);

    }

    public function actionUndoTransaction(){
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body); 

        $status         = 200;
        $message        = Yii::t('common','Done');

        $model          = WarehouseHeader::findOne($data->id);        
        $line           = WarehouseMoving::find()->where(['source_id' => $model->id])->all();


        $raws           = [];

        if(!in_array($model->status, ['Undo-Produce','Undo-Shiped','Undo'])){
        
            foreach ($line as $key => $list) {
                $raws[] = $this->UndoProduction($model, $list);
            }

            
            if(isset($data->remark)){
                $model->remark = $data->remark;
            }

            $model->status = 'Undo-'.$data->name;
            $model->save();


        }else{
            $status = 403;
            $message = Yii::t('common','Already Undo');
        }

        return json_encode([
            'status'    => $status,
            'message'   => $message,
            'no'        => $model->DocumentNo,
            'desc'      => "",
            'id'        => $model->id,
            'remark'    => "",
            'type'      => $model->status,
            'raws'      => $raws
        ]);

    }

    public static function undoShipMatching($id)
	{

		$matching 			= 0;
		$query 				= WarehouseMoving::find()->select('matching')->orderBy('matching DESC')->one();
		$matching 			= $query->matching + 1;

		$model 				= WarehouseMoving::findOne($id);
		$model->matching 	= $matching;
		$model->save(false);

		$SaleLine 					= SaleLine::findOne($model->SourceDoc);
		if($SaleLine != null){			
			$SaleLine->quantity_shipped	= $SaleLine->quantity_shipped - ($model->Quantity *-1);
			$SaleLine->save(false);
		}

		return $model;
	}


    protected function UndoProduction($Heading, $source){

        $status         = 200;
        $message        = Yii::t('common','Success');

        $model          = new WarehouseMoving();

        
		$transaction    = Yii::$app->db->beginTransaction();
        try {
			
			
			$model->source_id 		= $Heading->id;
			$model->DocumentNo		= $Heading->DocumentNo;
			$model->PostingDate 	= date('Y-m-d H:i:s');
			$model->TypeOfDocument 	= 'Undo-'.$source->TypeOfDocument;
			$model->SourceDoc 		= $source->SourceDoc;
			$model->SourceDocNo		= $source->SourceDocNo;
			$model->ItemNo 			= $source->ItemNo;
			$model->Description 	= $source->Description;
            $model->order_line_table= $source->order_line_table;
            $model->order_line_id   = $source->order_line_id;

			$model->item 			= $source->item;

			$model->DocumentDate 	= date('Y-m-d H:i:s');
			$model->user_id 		= Yii::$app->user->identity->id;
			$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
			$model->line_no 		= $source->source_id;
			$model->Quantity 		= $source->Quantity * -1;
			$model->QtyToMove 		= $source->items->inven;
			$model->QtyMoved 		= $source->items->inven + $model->Quantity;
			$model->apply_to 		= $source->id;
			$model->QtyOutstanding	= $source->QtyOutstanding;
			$matching 				= $this->undoShipMatching($source->id);
            $model->matching 		= $matching->matching;
            // SELECT * FROM `warehouse_moving` WHERE source_id not in (select id from warehouse_header)
            // UPDATE warehouse_moving SET header_id = source_id WHERE source_id not in (select id from warehouse_header)
            // UPDATE warehouse_moving SET source_id = 17552 WHERE source_id not in (select id from warehouse_header)


			$model->qty_per_unit	= $source->qty_per_unit;
            $model->unit_price		= $source->unit_price;
            
            $model->qty_before		= $source->items->inven;
			$model->qty_after		= $model->qty_before + $model->Quantity;

            $model->save();
                // update item 
            $model->items->updateQty;
                // $item  = \common\models\Items::findOne($model->item);
                // $item->last_stock = $model->qty_after;
                // $item->save(false);

                
            
            // if(!){
            //     $status     = 500;
            //     $message    =  json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            // }

            if($source->items->notify_stock == 1){

                try{ // Line Notify                                            
                                
                    $bot =  \common\models\LineBot::findOne(2);
                    $msg = 'Return Output'."\r\n\r\n";
                    $msg.= $source->items->master_code."\r\n";
                    $msg.= $model->Description."\r\n\r\n";
                    $msg.= Yii::t('common','Quantity').' : '.$model->Quantity."\r\n";
                    $msg.= Yii::t('common','Remain').' : '.$model->qty_after."\r\n\r\n";
                    $msg.= "User : [".Yii::$app->user->identity->id. "] " .Yii::$app->user->identity->username."\r\n";
                    
                    $bot->notify_message($msg);					

                } catch (\Exception $e) {					 
                     
                    //$message 		= Yii::t('common','{:e}',[':e' => $e]);	

                }	

            }

            $transaction->commit();
			
		} catch (\Exception $e) {
            $transaction->rollBack();
            $status     = 500;
            $message    = Yii::t('common','{:e}', [':e' => $e]);
            //throw $e;
           
        }

        return (Object)[
            'status' 	=> $status,
            'message'	=> $message,
            'no'        => $Heading->DocumentNo,
            'id' 	    => $model->item,
            'code'      => $model->items->master_code,
            'name' 	    => $model->Description,
            'qty' 	    => $model->Quantity,
            'type'      => $model->TypeOfDocument,
            'remain'    => $model->items->qtyAfter
        ];

	
    }

    public function actionValidateBom($word = 'CHONG-'){
        $query =  \common\models\BomHeader::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->andWhere(['like', 'name' , $word.'%', false])
        ->all();
        //use common\models\BomLine;

         
        $html = '<div class="row">';
        foreach ($query as $key => $model) {
            $line = \common\models\BomLine::find()
            ->where(['bom_no' => $model->id])
            ->all();
            $html.= '<div class="col-xs-3">';
            $html.= '<h3>'.($key + 1).' <a href="?r=Manufacturing%2Fprodbom%2Fview&id='.$model->id.'" target="_blank">'.$model->code.'</a></h3>';
            $html.= '<ul ><h4>'.$model->name.'</h4>';

            foreach ($line as $key => $value) {                  
                $html.= '<li style="margin-left:20px;">'.$value->name.'</li>';
            }

            $html.= '</ul>';
            $html.= '</div>';
        }
        $html.= '</div>';

        return $this->render('validate-bom', ['html' => $html]);
        
    }
}
