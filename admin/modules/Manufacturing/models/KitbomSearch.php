<?php

namespace admin\modules\Manufacturing\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\modules\Manufacturing\models\KitbomLine;

/**
 * KitbomSearch represents the model behind the search form about `admin\modules\Manufacturing\models\KitbomLine`.
 */
class KitbomSearch extends KitbomLine
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'kitbom_no', 'comp_id', 'user_id'], 'integer'],
            [['item_no', 'name', 'description', 'color_style'], 'safe'],
            [['quantity'], 'number'],
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
        $query = KitbomLine::find();

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
            'kitbom_no' => $this->kitbom_no,
            'quantity' => $this->quantity,
            'comp_id' => $this->comp_id,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'item_no', $this->item_no])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'color_style', $this->color_style]);

        return $dataProvider;
    }
}
