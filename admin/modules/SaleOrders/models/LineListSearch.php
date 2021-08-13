<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleLine;

/**
 * OrderSearch represents the model behind the search form about `common\models\SaleLine`.
 */
class LineListSearch extends SaleLine
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
        $query = SaleLine::find()
                    ->joinWith('saleHeader');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'sale_line.sourcedoc' => $this->sourcedoc,
            'sale_line.unit_price' => $this->unit_price,
            'sale_line.item_no' => $this->item_no,
            'sale_line.item'      => $this->item,

        ]);

        $query->andFilterWhere(['like', 'sale_line.order_no', $this->order_no]);

        return $dataProvider;
    }
}
