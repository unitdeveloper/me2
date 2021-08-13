<?php

namespace admin\modules\Management\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Approval;

/**
 * ApprovalSearch represents the model behind the search form of `common\models\Approval`.
 */
class ApprovalSearch extends Approval
{
    /**
     * {@inheritdoc}
     */
    public $search;
    public function rules()
    {
        return [
            [['id', 'source_id', 'sent_by', 'approve_by', 'comp_id'], 'integer'],
            [['table_name', 'field_name', 'field_data', 'ip_address', 'document_type', 'sent_time', 'approve_date', 'approve_status', 'gps','detail','search'], 'safe'],
            [['balance'], 'number'],
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
        $query = Approval::find()
        ->where(['approve_status' => 0]);

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
        ]);

        $query->andFilterWhere(['or',
        ['like', 'detail', $this->search],
        ['like', 'document_type', $this->search],
        ]);

        return $dataProvider;
    }
}
