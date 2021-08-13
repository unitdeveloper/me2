<?php

namespace admin\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

use common\models\Itemset;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionItems extends Model 
{


	public function calulateSet($setname)
	{
		// ถ้ายังไม่มี Setname ให้เอาชื่อ Item มาตั้งเป็น Setname
		$ItemSet = new Itemset();

		$count = $ItemSet->find()->where(['name' => $setname])->count();
		

		if($count > 0)
		{

			// มีอยู่แล้ว
			// ส่งค่ากลับ
			$setname = Itemset::find()->where(['name' => $setname])->one();
			return $setname->name;

		}else {
			// สร้าง Item Set
			return $this->CreateItemSet($setname);
			 
		}
		
	}


	public function CreateItemSet($name)
	{
		$model = new Itemset();
		$model->name = $name;
		$model->comp_id = Yii::$app->session->get('Rules')['comp_id'];
		$model->user_id = Yii::$app->user->identity->id;
		if($model->save())
		{
			return $name;
		}else {
			return 'Error';
		}
	}
}