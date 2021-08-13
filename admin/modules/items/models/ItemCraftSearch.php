<?php

namespace admin\modules\items\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ItemCraft;

/**
 * ItemCraftSearch represents the model behind the search form of `common\models\ItemCraft`.
 */
class ItemCraftSearch extends ItemCraft
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'item', 'code', 'quantity', 'measure', 'status', 'user_id', 'comp_id'], 'integer'],
            [['img', 'name'], 'safe'],
            [['cost'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = ItemCraft::find();

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
        $query->andFilterWhere([
            'id' => $this->id,
            'item' => $this->item,
            'code' => $this->code,
            'quantity' => $this->quantity,
            'cost' => $this->cost,
            'measure' => $this->measure,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
