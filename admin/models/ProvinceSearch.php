<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Province;

/**
 * ProvinceSearch represents the model behind the search form about `common\models\Province`.
 */
class ProvinceSearch extends Province
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PROVINCE_ID',  'GEO_SUB'], 'integer'],
            [['PROVINCE_CODE', 'GEO_ID','PROVINCE_NAME', 'latitude', 'longitude'], 'safe'],
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
        $query = Province::find();
        $query->joinWith('zone');

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
            'PROVINCE_ID' => $this->PROVINCE_ID,
        ]);

        $query->andFilterWhere(['like', 'PROVINCE_CODE', $this->PROVINCE_CODE])
            ->andFilterWhere(['like', 'PROVINCE_NAME', $this->PROVINCE_NAME])
            ->andFilterWhere(['like', 'zone.name', $this->GEO_ID])
            ->andFilterWhere(['like', 'GEO_SUB', $this->GEO_SUB])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude]);

        return $dataProvider;
    }
}
