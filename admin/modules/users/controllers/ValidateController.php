<?php

namespace admin\modules\users\controllers;

use Yii;
use common\models\User;
use common\models\Profile;
use admin\modules\users\models\SearchUsers;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use yii\base\Model;



/**
 * UsersController implements the CRUD actions for User model.
 */
class ValidateController extends Controller
{
    // public function actions()
    // {
    //     $actions = parent::actions();
    //     unset($actions['index']);
    //     return $actions;
    // }
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $allow = [
            'api',
            'password-hash'
        ];
        if (in_array($action->id, $allow, true)) {
            $this->enableCsrfValidation = false; //ปิดการใช้งาน csrf
        }

        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['index','api','password-hash'],  // in a controller
                // if in a module, use the following IDs for user actions
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                ],
            ],
            'authenticator' => [
                    'class' => \yii\filters\auth\HttpBearerAuth::className(),
                    'except' => ['index','api','password-hash'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'api' => ['POST'],
                    'password-hash' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex(){         
        echo 'REST API';
        //echo \admin\components\GoogleURLShortner::widget(["url" => "http://www.google.com"]);
    }

    public function actionPasswordHash($password,$token){
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        $tokens = [
            'qhZQRcwYBVOMl-ShgzPsz6Vrj7-MxG6w',
            'qhZQRcwYBVOMl-ShzzPsz6Vrj7-MgG6w'
        ];
        if (in_array($token, $tokens, true)) {
            return (Object)([
                'status' => 200,
                'hash' => $hash,
                'auth_key' => Yii::$app->security->generateRandomString()
            ]);
        }else{
            return (Object)([
                'status' => 403
            ]);
        }
    }

 

    public function actionApi()
    {
        $body = json_decode(Yii::$app->request->getRawBody());

        $model = \common\models\User::findOne(['username' => $body->username]);
       
        if($model!=null){
            
            if (Yii::$app->getSecurity()->validatePassword($body->password, $model->password_hash)) {
                // all good, logging user in         
                return (Object)([
                    'status' => 200,
                    'id' => $model->id,
                    'username' => $model->username,
                    'email' => $model->email,
                    'image' => 'https://beta.ewinl.com'.$model->profile->picture
                ]);

            } else {
                // wrong password
                return (Object)([
                    'status' => 403,
                    'message' => Yii::t('common','Wrong Password')
                ]);
                
            }
        }else{
            return (Object)([
                'status' => 403,
                'message' => Yii::t('common','User Not found')
            ]);
        }
    }
}