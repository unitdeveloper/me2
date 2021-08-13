<?php

namespace admin\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;

use common\models\Customer;
/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class FunctionCustomer extends Model 
{

	public function CheckCustomer($id)
	{
		$model = $this->findModel($id);	

		if($model->id == "")
		{
			return $val;
		}
		
	}


	protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}