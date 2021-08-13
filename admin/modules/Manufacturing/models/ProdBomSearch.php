<?php

namespace admin\modules\Manufacturing\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BomHeader;

/**
 * ProdBomSearch represents the model behind the search form about `common\models\BomHeader`.
 */
class ProdBomSearch extends BomHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'max_val', 'priority', 'comp_id', 'user_id', 'multiple', 'running_digit'], 'integer'],
            [['code', 'name', 'description', 'item_set', 'format_gen', 'format_type'], 'safe'],
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
        $query = BomHeader::find();

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
            'max_val' => $this->max_val,
            'priority' => $this->priority,
            'comp_id' => $this->comp_id,
            'user_id' => $this->user_id,
            'multiple' => $this->multiple,
            'running_digit' => $this->running_digit,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'item_set', $this->item_set])
            ->andFilterWhere(['like', 'format_gen', $this->format_gen])
            ->andFilterWhere(['like', 'format_type', $this->format_type]);

        return $dataProvider;
    }
}
