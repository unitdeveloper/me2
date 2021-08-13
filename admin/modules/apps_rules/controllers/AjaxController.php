<?php

namespace admin\modules\apps_rules\controllers;

use Yii;
use common\models\AppsRules;
use yii\web\NotFoundHttpException;
 
class AjaxController extends \yii\web\Controller
{
	 
    public function actionUpdateStatus()
    {
         
        $model = $this->findModel($_POST['param']['id']);


        if($_POST['param']['val']=='true')
        {
            $model->status = 1;
            $model->save(false);
        }else {
            $model->status = 0;
            $model->save(false);
        }

        return json_encode([
                'status' => 200,
                'name' => $model->name,
                'message' => 'Saved',
        ]);
         
    	 
    }

    protected function findModel($id)
    {
        if (($model = AppsRules::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}