<?php

namespace admin\modules\items\controllers;

use Yii;
use yii\filters\VerbFilter;
use admin\models\FunctionCenter;
use common\models\ItemCrossReference;
use admin\modules\items\models\SearchCross;

class CrossController extends \yii\web\Controller
{
    public function behaviors()
    {
        $Fnc = new FunctionCenter();
        $Fnc->RegisterRule();
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SearchCross();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        return $this->render('index');
    }

}
