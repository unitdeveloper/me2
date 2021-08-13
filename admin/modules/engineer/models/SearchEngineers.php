<?php

namespace admin\modules\engineer\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Engineer;

/**
 * SearchEngineers represents the model behind the search form about `common\models\Engineer`.
 */
class SearchEngineers extends Engineer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'EngineerType_id'], 'integer'],
            [['user_id', 'name', 'surname'], 'safe'],
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
        $query = Engineer::find();

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
            'EngineerType_id' => $this->EngineerType_id,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname]);

        return $dataProvider;
    }
}
