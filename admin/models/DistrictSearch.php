<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\District;

/**
 * DistrictSearch represents the model behind the search form about `common\models\District`.
 */
class DistrictSearch extends District
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DISTRICT_ID', 'AMPHUR_ID', 'PROVINCE_ID', 'GEO_ID'], 'integer'],
            [['DISTRICT_CODE', 'DISTRICT_NAME'], 'safe'],
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
        $query = District::find();

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
            'DISTRICT_ID' => $this->DISTRICT_ID,
            'AMPHUR_ID' => $this->AMPHUR_ID,
            'PROVINCE_ID' => $this->PROVINCE_ID,
            'GEO_ID' => $this->GEO_ID,
        ]);

        $query->andFilterWhere(['like', 'DISTRICT_CODE', $this->DISTRICT_CODE])
            ->andFilterWhere(['like', 'DISTRICT_NAME', $this->DISTRICT_NAME]);

        return $dataProvider;
    }
}
