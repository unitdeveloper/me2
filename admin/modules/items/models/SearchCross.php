<?php

namespace admin\modules\items\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ItemCrossReference;

/**
 * SearchItems represents the model behind the search form about `common\models\Items`.
 */
class SearchCross extends ItemCrossReference
{
    /**
     * @inheritdoc
     */
    public $customer;
    public function rules()
    {
        return [
            [['reference_type', 'reference_no', 'item'], 'integer'],
            [['item_no', 'unit_of_measure', 'barcode', 'customer'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
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
        $query = ItemCrossReference::find();      
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
         
            return $dataProvider;
        }

        $query->andFilterWhere([
            'item' => $this->item,       
            'reference_no' => $this->customer   
        ]);

        $query->andFilterWhere(['like', 'barcode', explode(' ',trim($this->barcode))])
        ->andFilterWhere(['like', 'item_no', $this->item_no])
        ->andFilterWhere(['like', 'description', explode(' ',trim($this->description))]);


        return $dataProvider;
    }
}
