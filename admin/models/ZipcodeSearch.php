<?php

namespace admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Zipcode;

/**
 * ZipcodeSearch represents the model behind the search form about `common\models\Zipcode`.
 */
class ZipcodeSearch extends Zipcode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ZIPCODE_ID'], 'integer'],
            [['DISTRICT_CODE', 'PROVINCE_ID', 'AMPHUR_ID', 'DISTRICT_ID', 'ZIPCODE', 'latitude', 'longitude'], 'safe'],
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
        $query = Zipcode::find();
        $query->joinWith('province');
        $query->joinWith('amphur');
        $query->joinWith('district');

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
            'ZIPCODE_ID' => $this->ZIPCODE_ID,
        ]);

        $query->andFilterWhere(['like', 'DISTRICT_CODE', $this->DISTRICT_CODE])
            ->andFilterWhere(['like', 'province.PROVINCE_NAME', $this->PROVINCE_ID])
            ->andFilterWhere(['like', 'amphur.AMPHUR_NAME', $this->AMPHUR_ID])
            ->andFilterWhere(['like', 'district.DISTRICT_NAME', $this->DISTRICT_ID])
            ->andFilterWhere(['like', 'ZIPCODE', $this->ZIPCODE])
            ->andFilterWhere(['like', 'zipcode.latitude', $this->latitude])
            ->andFilterWhere(['like', 'zipcode.longitude', $this->longitude]);

        return $dataProvider;
    }
}
