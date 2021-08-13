<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleQuoteLine;

/**
 * OrderSearch represents the model behind the search form about `common\models\SaleQuoteLine`.
 */
class SaleQuoteLineSearch extends SaleQuoteLine
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'unit_price', 'line_amount', 'line_discount', 'quantity_to_ship', 'quantity_shipped', 'quantity_to_invoice', 'quantity_invoiced', 'user_id', 'comp_id'], 'integer'],
            [['order_no','order_date','shiped_date', 'type', 'item_no', 'description', 'unit_measure', 'need_ship_date', 'create_date', 'api_key'], 'safe'],
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
        $query = SaleQuoteLine::find();

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
            'order_no' => $this->order_no,
            'unit_price' => $this->unit_price,
            'item_no' => $this->item_no,
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'order_no', $this->order_no])

            ->andFilterWhere(['like', 'comp_id', $this->comp_id]);


        return $dataProvider;
    }
}
