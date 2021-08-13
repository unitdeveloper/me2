<?php

 
namespace admin\controllers;
use common\models\LineBot;
use yii\filters\VerbFilter;

use yii\helpers\Url;

class LineBotController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'robot' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'robot') {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }
    
        return parent::beforeAction($action);
    }

    public function actionCurl()
    {
        $bot     =  LineBot::findOne(2);

        $message = 'สวัสดีครับ '.date('H:i:s');
        //$message =  "message=".$message."&imageThumbnail=https://ginolr.ewinl.com/images/ginolrlogo.png&imageFullsize=https://ginolr.ewinl.com/images/ginolrlogo.png";

        $res = $bot->notify_message($message);
        //$res = $bot->line($message);
        //$res = $bot->bot('ไทย');

        //$res = $bot->weather('กรุงเทพมหานคร');

        var_dump($res);



    }

    public function actionRobot()
    {

        if(\Yii::$app->request->get('token')=="4573"){

            $bot    =  LineBot::findOne(2);
            $msg    = \Yii::$app->request->get('message');
            $url    = \Yii::$app->request->get('url');
        
            $message = "\r\n".'Attack from : '.\Yii::$app->request->get('ip'). "\r\n".date('H:i:s'). "\r\n".$msg."\r\n\r\n";      
            $message.= 'By Url : '.$url;

            $res = $bot->notify_message($message);

            return json_encode([
                "response" => $res
            ]);

        }else{

            return json_encode([
                "status" => 403
            ]);

        }

    }
 
     
}
?>
