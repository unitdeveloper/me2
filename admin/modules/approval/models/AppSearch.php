<?php

namespace admin\modules\approval\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Approval;

/**
 * AppSearch represents the model behind the search form about `common\models\Approval`.
 */
class AppSearch extends Approval
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source_id', 'sent_by', 'approve_by', 'comp_id'], 'integer'],
            [['table_name', 'field_name', 'field_data', 'ip_address', 'document_type', 'sent_time', 'approve_date', 'approve_status', 'gps'], 'safe'],
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
        $query = Approval::find();

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
            'source_id' => $this->source_id,
            'sent_by' => $this->sent_by,
            'sent_time' => $this->sent_time,
            'approve_date' => $this->approve_date,
            'approve_by' => $this->approve_by,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'table_name', $this->table_name])
            ->andFilterWhere(['like', 'field_name', $this->field_name])
            ->andFilterWhere(['like', 'field_data', $this->field_data])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'document_type', $this->document_type])
            ->andFilterWhere(['like', 'approve_status', $this->approve_status])
            ->andFilterWhere(['like', 'gps', $this->gps]);

        return $dataProvider;
    }
}
