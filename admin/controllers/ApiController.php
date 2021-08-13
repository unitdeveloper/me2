<?php
namespace admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

use common\models\AppsRules;
use common\models\Authentication;
use common\models\User;

/**
 * Site controller
 */
class ApiController extends Controller{

    /**
     * @inheritdoc
     */

    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            //...
            // 'auth' => [
            //     'class' => 'yii\authclient\AuthAction',
            //     'successCallback' => [$this, 'AuthSuccess'],
            // ],
            //...
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['api-login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['api-login'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actionApiLogin($token){
        // set post fields
       
        $response = $this->fetch([
            'host' => '127.0.0.1:3000/login/api/Y3Jvc3MvbG9naW4vZnJvbS9mcm9udGVuZA==/json',
            //'body' => json_encode(['id' => Yii::$app->request->get('id')]),
            'method' => 'POST',
            'header' => [
                'Content-type: application/json',
                'Authorization: '.$token,
            ]
        ]);
        $response = json_decode($response);
         

        if($response){
            // Session register
            $info       = $response->data->user;
            
            $user       = User::findOne($info->id);

            \Yii::$app->getUser()->login($user);
            $session = \Yii::$app->session;

            if(self::Permission()){   
                $Rules = [
                    'comp_id' => Yii::$app->request->get('comp_id'),
                    'rules_id' => ''
                ];
                $session->set('Rules', $Rules);       
                return $this->redirect(['install/default']);
                exit;
            }
            
            $AppsRules = AppsRules::find()
            ->where(['user_id' => $info->id])
            ->andWhere(['comp_id' => Yii::$app->request->get('comp_id')])
            ->one();

            if ($AppsRules != null) {
                
                $session->set('Rules', $AppsRules);            
                $session->set('company', $AppsRules->company->name);
                $session->set('brand', $AppsRules->company->brand);
            }

            return $this->goHome();
        }else{
            // logout
            Yii::$app->user->logout();

            return $this->goHome();
        }
        
    }
 

    protected function fetch($obj){
        $obj = (Object)$obj;
        $ewin_api = $obj->host;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$ewin_api);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_POST, 1);
        if (isset($obj->method)){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $obj->method);
        }else{
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        if (isset($obj->body))
        curl_setopt($ch, CURLOPT_POSTFIELDS,$obj->body);
        // follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if (isset($obj->header))
        curl_setopt($ch, CURLOPT_HTTPHEADER, $obj->header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute!
        $response = curl_exec($ch);

        // close the connection, release resources used
        curl_close($ch);

        // do anything you want with your response
        return $response;
    }


    static function Permission(){
        $rules      = \common\models\AppsRules::findOne([
                        'user_id' => Yii::$app->user->identity->id,
                        'comp_id' => Yii::$app->request->get('comp_id')
                    ]);
        $permission = \common\models\AuthAssignment::findOne(['user_id' => Yii::$app->user->identity->id]);

        if($permission ==null){          
            return true;
        }else if($rules ==null){          
            return true;
        }else {
            return false;
        }
    }

}
