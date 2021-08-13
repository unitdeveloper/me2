<?php

namespace admin\modules\items\controllers;

use Yii;
use admin\modules\items\models\SearchItemLow;
use common\models\Items;

class ReportController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGroup()
    {
        return $this->render('group');
    }


    public function actionJsonGroup(){
        
        return json_encode([
            'status' => 200
        ]);
    }


    public function actionLow(){
        return $this->render('item-low');  
    }

    public function actionLowAjax(){
        $keys   = 'ItemLowAjax&comp:'.Yii::$app->session->get('Rules')['comp_id'];
        $query  = Items::find()->all();
        if(Yii::$app->cache->get($keys)){
            return Yii::$app->cache->get($keys);
        }else{
            $data = [];
            foreach ($query as $key => $model) {
                $data[] = [
                    'id'        => $model->id,
                    'name'      => $model->description_th,
                    'code'      => $model->master_code,
                    'barcode'   => $model->barcode,
                    'photo'     => $model->picture,
                    'stock'     => $model->inven,                     
                    'bom'       => $model->ProductionBom
                ];
            }
            Yii::$app->cache->set($keys, json_encode($data), 10);
            return Yii::$app->cache->get($keys);
        }               
    }

}
