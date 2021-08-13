<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NumberSeries;

/**
 * SeriesSearch represents the model behind the search form about `common\models\NumberSeries`.
 */
class SeriesSearch extends NumberSeries
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'manual_nos', 'comp_id'], 'integer'],
            [['name', 'starting_no', 'ending_no', 'last_date', 'last_no', 'default_no', 'type'], 'safe'],
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
        $query = NumberSeries::find();

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
            'manual_nos' => $this->manual_nos,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'starting_no', $this->starting_no])
            ->andFilterWhere(['like', 'ending_no', $this->ending_no])
            ->andFilterWhere(['like', 'last_date', $this->last_date])
            ->andFilterWhere(['like', 'last_no', $this->last_no])
            ->andFilterWhere(['like', 'default_no', $this->default_no])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
