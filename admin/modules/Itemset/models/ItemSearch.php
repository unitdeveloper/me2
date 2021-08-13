<?php

namespace admin\modules\Itemset\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Items;

/**
 * SearchItems represents the model behind the search form about `common\models\Itemset`.
 */
class ItemSearch extends Items
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['No','master_code','Description'],'string'],            
            // [['Description', 'UnitOfMeasure', 'TypeOfProduct', 'CostingMethod', 'ProductionBom','master_code'], 'safe'],
            // [['Inventory', 'UnitCost', 'CostGP', 'StandardCost'], 'number'],
             [['ItemGroup', 'PriceStructure_ID'], 'integer'],
            // [['master_code'], 'string'],
             [['barcode'],'integer'],
             [['Isearch'],'safe'],
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
        $query = Items::find()
        ->rightJoin('item_mystore','item_mystore.item=items.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        
        // $query->andFilterWhere([
        //     //'Inventory' => $this->Inventory,
        //     //'UnitCost' => $this->UnitCost,
        //     //'CostGP' => $this->CostGP,
        //     //'ItemGroup' => $this->ItemGroup,
        //     //'StandardCost' => $this->StandardCost,
        //     //'PriceStructure_ID' => $this->PriceStructure_ID,
        //     //'master_code' => $this->No,
        //     //'Description' => $this->Description,
             
        // ]);

        $query
            ->andFilterWhere(['like', 'items.Description', $this->Isearch])
            //->andFilterWhere(['like', 'No', $this->No])
            //->andFilterWhere(['like', 'ItemGroup', $this->ItemGroup])
            //->andFilterWhere(['like', 'UnitOfMeasure', $this->UnitOfMeasure])
            ->andFilterWhere(['like', 'items.barcode', $this->Isearch])
            //->andFilterWhere(['like', 'CostingMethod', $this->CostingMethod])
            ->andFilterWhere(['like', 'items.master_code', $this->Isearch])

           //->andFilterWhere(['like', 'ProductionBom', $this->ProductionBom]);

           ->andFilterWhere(['like', 'items.No', $this->Isearch]);
           

        return $dataProvider;
    }
}
