<?php

namespace admin\modules\Manufacturing\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProductionRequest;

/**
 * PdrSearch represents the model behind the search form of `common\models\ProductionRequest`.
 */
class PdrSearch extends ProductionRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'item', 'user_id', 'comp_id'], 'integer'],
            [['create_date', 'remark', 'no', 'posting_date', 'request_date'], 'safe'],
            [['quantity'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = ProductionRequest::find();

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
            'create_date' => $this->create_date,
            'item' => $this->item,
            'quantity' => $this->quantity,
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
            'posting_date' => $this->posting_date,
            'request_date' => $this->request_date,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'no', $this->no]);

        return $dataProvider;
    }
}
