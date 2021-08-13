<?php

namespace admin\modules\Purchase\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PurchaseLine;

/**
 * PurchaseLineSearch represents the model behind the search form about `common\models\PurchaseLine`.
 */
class LineListSearch extends PurchaseLine
{
    /**
     * @inheritdoc
     */
    public $doc_no;
    public $item_name;
    public $order_date;
    public function rules()
    {
        return [
            [['id', 'source_id'], 'integer'],
            [['source_no', 'type', 'items_no', 'description', 'location', 'measure', 'expected_date', 'planned_date','doc_no','item_name','order_date','item'], 'safe'],
            [['quantity', 'unitcost', 'lineamount', 'linediscount'], 'number'],
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
        $query = PurchaseLine::find()
        ->joinwith('header')
        ->joinwith('items');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                        'source_id' => SORT_DESC,
                        //'item'      => SORT_ASC
                    ]
                ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'purchase_line.id' => $this->id,
            'purchase_line.source_id' => $this->source_id,
            'purchase_line.quantity' => $this->quantity,
            'purchase_line.item'    => $this->item,

        ]);

        $query->andFilterWhere(['like', 'purchase_header.doc_no', $this->doc_no]);

        if (!is_null($this->expected_date) && 
            strpos($this->expected_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->expected_date);

            $query->andFilterWhere(['between', 'DATE(purchase_line.expected_date)', $start_date, $end_date]);

        }

        if (!is_null($this->order_date) && 
            strpos($this->order_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->order_date);

            $query->andFilterWhere(['between', 'DATE(purchase_header.order_date)', $start_date, $end_date]);

        }
            
        return $dataProvider;
    }
}
