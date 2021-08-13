<?php

namespace admin\modules\runingnoseries\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RuningNoseries;

/**
 * SearchRunnose represents the model behind the search form about `common\models\RuningNoseries`.
 */
class SearchRunnose extends RuningNoseries
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'no_series', 'comp_id'], 'integer'],
            [['start_date', 'start_no', 'last_no'], 'safe'],
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
        $query = RuningNoseries::find();

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
            'no_series' => $this->no_series,
            'start_date' => $this->start_date,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'start_no', $this->start_no])
            ->andFilterWhere(['like', 'last_no', $this->last_no]);

        return $dataProvider;
    }
}
