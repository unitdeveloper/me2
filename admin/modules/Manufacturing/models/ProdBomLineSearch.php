<?php

namespace admin\modules\Manufacturing\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BomLine;

/**
 * ProdBomLineSearch represents the model behind the search form about `common\models\BomLine`.
 */
class ProdBomLineSearch extends BomLine
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bom_no', 'comp_id', 'user_id'], 'integer'],
            [['item_no', 'name', 'description', 'color_style', 'measure'], 'safe'],
            [['quantity', 'base_unit'], 'number'],
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
        $query = BomLine::find();

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
            'bom_no' => $this->bom_no,
            'quantity' => $this->quantity,
            'comp_id' => $this->comp_id,
            'user_id' => $this->user_id,
            'base_unit' => $this->base_unit,
        ]);

        $query->andFilterWhere(['like', 'item_no', $this->item_no])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'color_style', $this->color_style])
            ->andFilterWhere(['like', 'measure', $this->measure]);

        return $dataProvider;
    }
}
