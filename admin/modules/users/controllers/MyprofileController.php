<?php

namespace admin\modules\users\controllers;

use Yii;
use common\models\Profile;
use admin\modules\users\models\SearchProfile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class MyprofileController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'model' => $this->findModel(Yii::$app->user->identity->id),
        ]);
         
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
