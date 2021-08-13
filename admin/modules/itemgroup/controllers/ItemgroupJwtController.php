<?php

namespace admin\modules\itemgroup\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\Itemgroupjwt;

use yii\filters\auth\HttpBearerAuth;

class ItemgroupJwtController extends \yii\rest\ActiveController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'bearerAuth' => [
                'class' => HttpBearerAuth::className()
            ]
        ]);
    }

    public function actionGetItemGroup(){

 
        $model = Itemgroupjwt::find()->where([
            "Child"     => '00',
            'Status'    => "1", 
            'comp_id'   => Yii::$app->session->get('Rules')->comp_id
        ])->all();

        return $this->asJson($model); 
    }
}