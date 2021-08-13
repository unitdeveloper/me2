<?php

namespace admin\models;

use Yii;
use yii\base\Model;



use yii\helpers\Json;


use yii\helpers\ArrayHelper;




class AlertException extends Model 
{


    public function Error($message)
    {

    	return Exception($message->msg);
    }


    public static function DocStatus($status){
		switch ($status) {
			case '0':
				$NewStatus = '<i class="fa fa-commenting-o" aria-hidden="true"></i>';
				break;
			
			case '1':
				$NewStatus = '<i class="fa fa-credit-card" aria-hidden="true"></i>';
				break;
			
			case '2':
				$NewStatus = '<i class="fa fa-exchange" aria-hidden="true"></i>';
				break;
			
			case '3':
				$NewStatus = '<i class="fa fa-question" aria-hidden="true"></i>';
				break;
			
			case '4':
				$NewStatus = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
				break;

			case '5':
				$NewStatus = '<i class="fa fa-info-circle" aria-hidden="true"></i>';
				break;

			case '6':
				$NewStatus = '<i class="fa fa-hourglass-half" aria-hidden="true"></i>';
				break;	

			
			default:
				$NewStatus = '<i class="fa fa-comment-o" aria-hidden="true"></i>';
				break;
		}

		return $NewStatus;
	}


}