<?php

namespace admin\modules\salepeople\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SalesHasCustomer;

/**
 * HasCustomerSearch represents the model behind the search form of `common\models\SalesHasCustomer`.
 */
class HasCustomerSearch extends SalesHasCustomer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sale_id', 'cust_id', 'customer_group', 'comp_id'], 'integer'],
            [['type_of'], 'safe'],
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
        $query = SalesHasCustomer::find();

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
            'sale_id' => $this->sale_id,
            'cust_id' => $this->cust_id,
            'customer_group' => $this->customer_group,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'type_of', $this->type_of]);

        return $dataProvider;
    }
}
