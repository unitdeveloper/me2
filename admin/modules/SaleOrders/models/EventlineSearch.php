<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleEventLine;

/**
 * EventSearch represents the model behind the search form about `common\models\SaleEventHeader`.
 */
class EventLineSearch extends SaleEventLine
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
        $query = SaleEventLine::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 100]
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

        if(isset($this->order_date)) $this->order_date = date('Y-m-d',strtotime($this->order_date));

        $query->andFilterWhere(['like', 'order_no', $this->order_no])
            ->andFilterWhere(['like', 'order_date', $this->order_date]);

        return $dataProvider;
    }




}
