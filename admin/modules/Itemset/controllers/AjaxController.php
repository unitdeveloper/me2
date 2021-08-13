<?php

namespace admin\modules\Itemset\controllers;

use Yii;
use yii\db\Expression;

use admin\modules\Itemset\models\FunctionItemset;
use admin\modules\Itemset\models\ItemSearch;
use common\models\Items;
use common\models\ItemMystore;
use common\models\WarehouseMoving;

class AjaxController extends \yii\web\Controller
{

	public function actionPossibleCount()
	{
		$Fnc = new FunctionItemset();
		//return $_POST['post'];
		$JSON 			= json_decode($_POST['post'], true);
		$data = array();
		foreach ($JSON as $key => $value) {
			$res 	= json_decode($Fnc->countBomInItems($value['c'],$value['q']));
			 
			$data[]= $res->value;
		}
		//(Object)$data;
		//var_dump($data);
		return min($data);
	}

	public function actionItems()
    {
    	$company = Yii::$app->session->get('Rules')['comp_id'];
      
        $model = new ItemSearch();

        // ###################### (Fn 0) ######################
		$Iset = Items::find()->select('itemset')->GroupBY('itemset')->all();

        # Find Item Set
        $data = array();
        foreach ($Iset as $value) {
            
			# Get one item from itemset.  
			//$data[] = $value->No; 
			$getItem =  Items::find()
			->select('items.id')
			->rightJoin('item_mystore','item_mystore.item_no=items.No')
			->where(['items.itemset' => $value->itemset])
			->andWhere(['item_mystore.comp_id' => $company])
			->one();     
			$data[] = $getItem['id'];
		}
		//echo $Iset->createCommand()->getRawSql();
        //var_dump($data);
        
        // ###################### (End Fn 0) ######################



        // ###################### (1) ######################

        $dataProvider = $model->search(Yii::$app->request->queryParams);
         
        $dataProvider->query->where([
                'items.ItemGroup' => self::hrefVal($_POST['param']['href']),
				'items.id' => $data,
				'item_mystore.comp_id' => $company
                ]);

        $dataProvider->query->joinwith(['itemSet']);
        $dataProvider->query->orderBy(['itemset.name' => SORT_ASC]);        
        $dataProvider->pagination=false;
        
        //echo $dataProvider->query->createCommand()->getRawSql();
        // ###################### (End 1) ######################

        return $this->renderpartial('items',[     	
            'dataProvider' => $dataProvider,
            ]);
    }

	static function hrefVal($data)
    {
    	$data = explode('=',$data);
    	 
    	return $data['1'];
	}


	public function actionMenuRandom()
    {
        $company = Yii::$app->session->get('Rules')['comp_id']; 
      
        $model = new ItemSearch();

        // ###################### (Fn 0) ######################
        $Iset = Items::find()
        ->select('items.itemset')
        ->rightJoin('item_mystore','item_mystore.item_no=items.No')
        ->where(['<>','items.itemset',0])
        ->andWhere(['item_mystore.comp_id' => $company])
        ->GroupBY('items.itemset')
        ->orderBy(new Expression('rand()'))
        ->limit(8)
        ->all();

		$data = array();
        foreach ($Iset as $value) {
            
			# Get one item from itemset.  
			//$data[] = $value->No; 
			$getItem =  Items::find()
			->select('items.id')
			->rightJoin('item_mystore','item_mystore.item_no=items.No')
			->where(['items.itemset' => $value->itemset])
			->andWhere(['item_mystore.comp_id' => $company])
			->one();     
			$data[] = $getItem['id'];
		}
        
        
        // ###################### (End Fn 0) ######################



        // ###################### (1) ######################

        $dataProvider = $model->search(Yii::$app->request->queryParams);
        //'ItemGroup' => '8', 
        //->orderBy(new Expression('rand()'))
        $dataProvider->query->where(['items.id' => $data])
                            ->orderBy(['items.itemset' => SORT_ASC])
                            ->limit(4); 
        
         
        ###################### (End 1) ######################

        return $this->renderpartial('items',[       
            'dataProvider' => $dataProvider,
            ]);
    }
	
	
	static function getMyitem($company)
    {


        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item_no;
            }

            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionItemValidate()
    {
     
        echo FunctionItemset::getItemSet();
        
        
    }


    public function actionJsonFindItem()
    {
        $company = Yii::$app->session->get('Rules')['comp_id'];
        $Items = Items::find()
        ->where(['or',
            ['like','barcode'      , $_POST['param']['item']],
            ['like','master_code'  , $_POST['param']['item']]
        ])
        ->andWhere(['company_id' => $company]);

        if($Items->count() >0)
        {
            $model      = $Items->one();
            $Query      = WarehouseMoving::find()->where(['ItemNo' => $model->No]);
            $RealInven  = $Query->sum('Quantity');
            $Remaining  = $model->Inventory + $RealInven;
            $data       = [
                            'id'        => $model->id,
                            'item'      => $model->No,
                            'ig'        => $model->ItemGroup,
                            'Photo'     => $model->Photo,
                            'std'       => $model->StandardCost,
                            'desc'      => $model->description_th,
                            'code'      => $model->master_code,
                            'remain'    => $Remaining,
                            'all'       => 1
                        ];
            return json_encode($data);
        }else {
            $data       = [
                            'id'        => 0,
                            'item'      => 'eWinl',
                            'ig'        => 0,
                            'Photo'     => 0,
                            'std'       => 0,
                            'desc'      => 'ไม่มี Item นี้',
                            'code'      => 'eWinl',
                            'remain'    => 0,
                            'all'       => 0
                        ];
            return json_encode($data);
        }
        
    }

}
