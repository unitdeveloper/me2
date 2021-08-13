<?php

namespace admin\modules\items\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Items;

/**
 * SearchItems represents the model behind the search form about `common\models\Items`.
 */
class SearchItems extends Items
{
    /**
     * @inheritdoc
     */
    public $groups;
    public function rules()
    {
        return [
            [['No','master_code','Description','description_th','ItemGroup'],'string'],
            // [['Description', 'UnitOfMeasure', 'TypeOfProduct', 'CostingMethod', 'ProductionBom','master_code'], 'safe'],
            // [['Inventory', 'UnitCost', 'CostGP', 'StandardCost'], 'number'],
             [['groups', 'PriceStructure_ID'], 'integer'],
            // [['master_code'], 'string'],
             [['barcode'],'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Items::find();      
        

        if(Yii::$app->user->identity->id!=1){
            $query->rightJoin('item_mystore',"item_mystore.item=items.id AND item_mystore.comp_id='".Yii::$app->session->get('Rules')['comp_id']."'");
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
         
            return $dataProvider;
        }

        $query->andFilterWhere([
            //'ItemGroup' => $this->groups,          
        ]);

        $query->andFilterWhere(['or',
                ['like', 'items.description_th', explode(' ',trim($this->description_th))],
                ['like', 'items.Description', explode(' ',trim($this->description_th))]
            ])
            ->andFilterWhere(['like', 'items.No', trim($this->No)])
            
            ->andFilterWhere(['like', 'items.CostingMethod', $this->CostingMethod])
            ->andFilterWhere(['or',
                              ['like', 'items.master_code', trim($this->master_code)],
                              ['like', 'items.barcode', trim($this->barcode)]
                              ])
           ->andFilterWhere(['like', 'items.ProductionBom', $this->ProductionBom]);

        if($this->ItemGroup){
            $query->joinWith('itemGroups');
            $query->andFilterWhere(['like', 'itemgroup.Description', explode(' ',trim($this->ItemGroup))]);
        }
        


        return $dataProvider;
    }
}
