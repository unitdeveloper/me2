<?php

namespace admin\modules\tracking\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;



use common\models\OrderTracking;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionTracking extends Model 
{

	public static function CreateTracking($models)
	{

		
		
		$model = new OrderTracking();

		$model->event_date 		= date('Y-m-d H:i:s');
		$model->doc_type 		= $models['doc_type'];
		$model->doc_id 			= $models['doc_id'];
		$model->doc_no 			= $models['doc_no'];
		$model->doc_status 		= $models['doc_status'];
		$model->amount 			= $models['amount'];
		$model->remark 			= $models['remark'];
		$model->ip_address 		= $_SERVER['REMOTE_ADDR'];
		$model->lat_long 		= '';
		$model->create_by 		= Yii::$app->user->identity->id;
		$model->comp_id 		= Yii::$app->session->get('Rules')['comp_id'];
		$model->track_for_table = $models['track_for_table'];
		$model->track_for_id 	= $models['track_for_id'];

		

		if(!$model->save())
		{
			print_r($model->getErrors());
			exit();
		}

		//---LINE NOTIFY---
		$cpuLoad = \common\models\Systeminfo::getServerLoad();
		if($cpuLoad>=80){					 
			$bot     =  \common\models\LineBot::findOne(2);
			$message = 'Warning : CPU '.$cpuLoad.' %'."\r\n";
			$message.= "By : [".Yii::$app->user->identity->id."] ".Yii::$app->session->get('Rules')['name']."\r\n";
			$message.= "Sale : ".Yii::$app->session->get('Rules')['sale_code']."\r\n";
			$message.= "Module : Saleorder [Update : ".$model->id."].".$model->doc_no;
			$bot->notify_message($message);
		}
		//--- /.LINE NOTIFY---     

		 
		
	}

}