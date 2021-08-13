<?php

namespace admin\modules\tracking\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OrderTracking;

/**
 * SaleTrackingSearch represents the model behind the search form about `common\models\OrderTracking`.
 */
class SaleTrackingSearch extends OrderTracking
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'doc_id', 'comp_id'], 'integer'],
            [['event_date', 'doc_type', 'doc_no', 'doc_status', 'remark', 'ip_address', 'lat_long', 'create_by'], 'safe'],
            [['amount'], 'number'],
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
        $query = OrderTracking::find();

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
            'event_date' => $this->event_date,
            'doc_id' => $this->doc_id,
            'amount' => $this->amount,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'doc_type', $this->doc_type])
            ->andFilterWhere(['like', 'doc_no', $this->doc_no])
            ->andFilterWhere(['like', 'doc_status', $this->doc_status])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'lat_long', $this->lat_long])
            ->andFilterWhere(['like', 'create_by', $this->create_by]);

        return $dataProvider;
    }
}
