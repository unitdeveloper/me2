<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SetupNoSeries;

/**
 * SetupNosSearch represents the model behind the search form about `common\models\SetupNoSeries`.
 */
class SetupNosSearch extends SetupNoSeries
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'no_series', 'comp_id'], 'integer'],
            [['form_id', 'form_name'], 'safe'],
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
        $query = SetupNoSeries::find();

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
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'form_id', $this->form_id])
            ->andFilterWhere(['like', 'form_name', $this->form_name]);

        return $dataProvider;
    }
}
