<?php

namespace admin\controllers;
use yii\filters\VerbFilter;

class WebsiteController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        $allow = [
            'api',
            'product'
        ];
        if (in_array($action->id, $allow, true)) {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }

        return parent::beforeAction($action);
    }

    public static function allowedDomains() {
        return [
            // '*',                        // star allows all domains
            'http://127.0.0.1',
            'http://localhost',
            'http://127.0.0.1:8080',
            'http://localhost:8080',
            'http://192.168.1.24:8080'
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['index','product','api'],  // in a controller
                // if in a module, use the following IDs for user actions
                'formats' => [
                    //'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin'                            => static::allowedDomains(),
                    'Access-Control-Request-Method'     => ['GET', 'POST', 'PUT', 'OPTIONS'],
                    'Access-Control-Request-Headers'    => ['*'],
                    'Access-Control-Allow-Credentials'  => true,
                ],
            ],
            'authenticator' => [
                    'class' => \yii\filters\auth\HttpBearerAuth::className(),
                    'except' => ['index','product','api','item'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'product' => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionProduct()
    {
        $request_body   = file_get_contents('php://input');
        $data           = json_decode($request_body);

        return json_encode([
            'status' => 200,
            'raws' => [
                'id'=> 1,
                'name'=> 'test'
            ]
        ]);
    }

    public function actionApi(){
        return json_encode(['status' => 200]);
    }

    public function actionItem($id){
        return '1,2,3';
    }

}
