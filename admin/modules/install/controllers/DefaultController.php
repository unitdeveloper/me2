<?php

namespace admin\modules\install\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use admin\modules\install\models\Install;
use admin\modules\install\models\InstallItemgroup;
use admin\modules\install\models\InstallVatType;
use admin\modules\install\models\InstallRules;
use common\models\User;
use admin\modules\install\models\Uninstall;
/**
 * Default controller for the `install` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'install' => ['POST'],
                    'uninstall' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if(!\Yii::$app->user->identity){
            return $this->redirect('user/security/login');
        }
        
        //dump(Yii::$app->session->get('Rules')['comp_id']); 
        $user       = User::findOne(\Yii::$app->user->identity->id);
 
        if($user->owner){
            return $this->render('index');
        }else{
            return $this->render('alert_owner');
        }
        
    }

    public function actionInstall(){

        if(!\Yii::$app->user->identity){
            return $this->redirect('user/security/login');
            exit();
        }


        $module = Yii::$app->request->post('modules');
        
        switch ($module) {
            
            case 'rules':
                $model = InstallRules::createRules();
                break;

            case 'default':
                $model = Install::createDefaultData();
                break;

            case 'vat':
                $model = InstallVatType::createVat();
                break;

            case 'series':
                $model = Install::createSeries($module);
                break;

            case 'itemgroup':
                $model = InstallItemgroup::createItemGroups();
                break;
        
                 

            default:
                $model = Install::create($module);
                break;
        }
        

        
        return json_encode([
            'status'    => 200,
            'message'   => 'done',
            'value'     => [
                'id' => '1',
                'module' => $model
                // 'module' => [
                //     'message' => [
                //         ['name' => 'x','status' => 200],
                //         ['name' => 'y','status' => 500]
                //     ]
                // ]
            ]
        ]);
    }
    
    public function actionUninstall(){
        if(!\Yii::$app->user->identity){
            return $this->redirect('user/security/login');
            exit();
        }
        $module = Yii::$app->request->post('modules');
        
        switch ($module) {
            
            case 'series':
                $model = Uninstall::UninstallModule($module);
                break;             
                 

            default:
                $model = Uninstall::UninstallModule($module);
                break;
        }
        

        return json_encode([
            'status'    => 200,
            'message'   => 'done',
            'value'     => [
                'id' => '1',
                'module' => $model
            ]
        ]);

    }
}
