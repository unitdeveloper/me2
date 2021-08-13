<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleLine;

/**
 * OrderSearch represents the model behind the search form about `common\models\SaleLine`.
 */
class OrderSearch extends SaleLine
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'unit_price', 'line_amount', 'line_discount', 'quantity_to_ship', 'quantity_shipped', 'quantity_to_invoice', 'quantity_invoiced', 'user_id', 'comp_id','sourcedoc'], 'integer'],
            [['order_no','order_date','shiped_date', 'type', 'item_no', 'item', 'description', 'unit_measure', 'need_ship_date', 'create_date', 'api_key'], 'safe'],
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
        $query = SaleLine::find();

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
            //'id' => $this->id,
            'sourcedoc' => $this->sourcedoc,
            //'order_no' => $this->order_no,
            // 'quantity' => $this->quantity,
            // 'unit_price' => $this->unit_price,
            // 'line_amount' => $this->line_amount,
            // 'line_discount' => $this->line_discount,
            // 'need_ship_date' => $this->need_ship_date,
            // 'quantity_to_ship' => $this->quantity_to_ship,
            // 'quantity_shipped' => $this->quantity_shipped,
            // 'quantity_to_invoice' => $this->quantity_to_invoice,
            // 'quantity_invoiced' => $this->quantity_invoiced,
            // 'create_date' => $this->create_date,
            'unit_price' => $this->unit_price,
            'item_no' => $this->item_no,
            'item'      => $this->item,
            'user_id' => $this->user_id
        ]);

        $query->andFilterWhere(['like', 'order_no', $this->order_no]);
            // ->andFilterWhere(['like', 'type', $this->type])
            // ->andFilterWhere(['like', 'item_no', $this->item_no])
            // ->andFilterWhere(['like', 'description', $this->description])
            // ->andFilterWhere(['like', 'unit_measure', $this->unit_measure])
            //->andFilterWhere(['like', 'comp_id', $this->comp_id]);
            //->andFilterWhere(['like', 'api_key', $this->api_key]);

        return $dataProvider;
    }
}
