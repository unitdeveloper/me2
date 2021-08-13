<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleInvoiceHeader;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class SaleinvheaderSearch extends SaleInvoiceHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['no_', 'cust_no_', 'cust_name_', 'cust_address', 'cust_address2', 'posting_date', 'order_date', 'ship_date'], 'safe'],
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
        $query = SaleInvoiceHeader::find();
        

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
            'posting_date' => $this->posting_date,
            'order_date' => $this->order_date,
            'ship_date' => $this->ship_date,
        ]);

        $query->andFilterWhere(['like', 'no_', $this->no_])
            ->andFilterWhere(['like', 'cust_no_', $this->cust_no_])
            ->andFilterWhere(['like', 'cust_name_', $this->cust_name_])
            ->andFilterWhere(['like', 'cust_address', $this->cust_address])
            ->andFilterWhere(['like', 'cust_address2', $this->cust_address2]);

        return $dataProvider;
    }
}
