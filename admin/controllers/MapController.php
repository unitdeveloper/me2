<?php

namespace admin\controllers;
use Yii;
use common\models\Customer;
use common\models\Zone;

class MapController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionMap()
    {

        $direct     = '';
        $directSub  = 0;
        if(isset($_POST['direct']))     $direct     = $_POST['direct'];
        if(isset($_POST['directsub']))  $directSub  = $_POST['directsub'];


        if($direct!='All'){



            $Direction = Zone::find()->where(['description' => $direct])->one();

            
            if($directSub>0) {

                $contacts = Customer::find()->joinWith('zipcode')->joinWith('provincetb')
                ->where(['<>','postcode',""])
                ->andWhere(['<>','zipcode.latitude',""])
                ->andWhere(['province.GEO_ID' => $Direction->id])
                ->andWhere(['province.GEO_SUB' => $directSub])
                ->andWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['status'=>'1'])->limit(999)->all();

            }else {

                $contacts = Customer::find()->joinWith('zipcode')->joinWith('provincetb')
                ->where(['<>','postcode',""])
                ->andWhere(['<>','zipcode.latitude',""])
                ->andWhere(['province.GEO_ID' => $Direction->id])
                ->andWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['status'=>'1'])->limit(999)->all();

            }

        }else {

            $contacts = Customer::find()->joinWith('zipcode')->joinWith('provincetb')
                ->where(['<>','postcode',""])
                ->andWhere(['<>','zipcode.latitude',""])
                ->andWhere(['customer.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['status'=>'1'])->limit(999)->all();

        }

        
        

   

        if(Yii::$app->request->isAjax) {
    		return $this->renderAjax('map',['contacts'=>$contacts]);
        }else {
            return $this->render('map',['contacts'=>$contacts]);
        }
    }

}
