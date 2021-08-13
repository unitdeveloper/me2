<?php

namespace admin\modules\users\controllers;

use Yii;
use common\models\Profile;
use admin\modules\users\models\SearchProfile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use \common\models\User;

//use cozumel\cropper\imagecreatefromjpeg;

class SettingsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = $this->findModel(Yii::$app->user->identity->id);

        if ($model->load(Yii::$app->request->post())) {

            $model->photo       = $model->upload($model,'photo');

            
            if($_POST['confirm_password'] != ''){
                //var_dump($_POST['confirm_password']);
                $user = User::findOne(Yii::$app->user->identity->id);
                $user->password_hash =  Yii::$app->security->generatePasswordHash($_POST['confirm_password'], Yii::$app->getModule('user')->cost);
               
                $user->save();
                //var_dump($user->password_hash);
                
                 
            }  
           
            if(!$model->save()) var_dump($model->getErrors());  
            
            
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account details have been updated'));
            
            return $this->render('index', [
                'model' => $model,
            ]);

        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
        
         
    }
    

     

    protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
