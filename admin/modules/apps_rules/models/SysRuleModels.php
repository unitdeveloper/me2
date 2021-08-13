<?php

namespace admin\modules\apps_rules\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\Model;


use common\models\SetupSysMenu;
 

/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class SysRuleModels extends Model 
{


	public static function getPolicy($type,$modules,$controller,$models,$functionName)
	{
		$myPolicy = array();

		$Policy = SetupSysMenu::find()
			//->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
			->Where(['function_group_type' => $type])
			->andWhere(['function_modules' => $modules,'function_controllers' => $controller])
			->andWhere(['function_models' => $models,'function_name' => $functionName]);

		if($Policy->exists())
		{
			$getPolicy = $Policy->one();
			$myPolicy = explode(',',$getPolicy->rules_id);
		}

		

		return $myPolicy;
	}

}