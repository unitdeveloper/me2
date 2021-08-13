<?php

namespace admin\modules\Manufacturing\controllers;
use Yii;
use yii\db\Expression;

use common\models\Items;
use admin\modules\Manufacturing\models\FunctionManufac;
use admin\modules\Manufacturing\models\KitbomHeader;

class AjaxController extends \yii\web\Controller
{
    public function actionJsonFindItem()
    {
        //return $_POST['param']['item'];
        $company = Yii::$app->session->get('Rules')['comp_id'];

        $Items = Items::find()
        ->joinWith('itemmystore')
        ->where(['items.master_code'=>$_POST['param']['item']])
        ->andWhere(['item_mystore.comp_id' => $company]);

        if($Items->exists())
        {
            //$model = Items::find()->where(['master_code'=>$_POST['param']['item']])->one();
            $model = $Items->one();
            $data = [
                    'id'    => $model->id,
                    'item'  => $model->No,
                    'ig'    => $model->ItemGroup,
                    'Photo' => $model->Photo,
                    'std'   => $model->StandardCost,
                    'desc'  => $model->Description,
                    'descTh'  => $model->description_th,
                    'code'  => $model->master_code,
                    'measure' => $model->UnitOfMeasure,
                ];
            return json_encode($data);
        }else {
            $data = [
                    'id'    => 0,
                    'item'  =>'eWinl',
                    'ig'    => 0,
                    'Photo' => 0,
                    'std'   => 0,
                    'desc'  => 'ไม่มี Item นี้',
                    'descTh' => '',
                    'code'  => 'eWinl',
                    'measure' => '-',
                ];
            return json_encode($data);
        }
        
    }
    public function actionIndex()
    {
        echo 'test';
    }

    public function actionCheckItem()
    {
        return $this->render('check-item');
    }

    public function actionInsertItem()
    {
        return $this->render('insert-item');
    }

    public function actionCreateBom()
    {
    	$Fnc = new FunctionManufac();

    	return $Fnc->ValidateNewCreate(Yii::$app->request->post('param'));


        //return $this->render('create-bom');
    }


    public function actionUpdateStatus()
    {
         
        $model = $this->findModelKitBom($_POST['param']['id']);


        if($_POST['param']['val']=='true')
        {
            $model->status = 1;
            $model->save(false);
        }else {
            $model->status = 0;
            $model->save(false);
        }

        return $model->name.' Saved..';
         
         
    }

    public function actionUpdateMultiple()
    {
         
        if(isset(Yii::$app->request->post('param')['id'])){
        
            $model = $this->findModelKitBom(Yii::$app->request->post('param')['id']);

            if(Yii::$app->request->post('param')['val']=='true'){
                $model->multiple = 1;
            }else {
                $model->multiple = 0;
            }

            if($model->save(false)){
                $status     = 200;
                $message    = $model->multiple;
            }else{
                $status     = 500;
                $message    = json_encode($model->getErrors(),JSON_UNESCAPED_UNICODE);
            }
        }else{
            $status     = 200;
            $message    = Yii::$app->request->post('param')['val']=='true' ? 1 : 0;
        }
 
        return $this->asJson([
            'status' => $status,
            'message' => $message
        ]);
         
         
    }

    protected function findModelKitBom($id)
    {
        if (($model = KitbomHeader::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
