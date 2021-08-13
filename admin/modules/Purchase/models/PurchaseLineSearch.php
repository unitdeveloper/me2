<?php

namespace admin\modules\Purchase\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PurchaseLine;

/**
 * PurchaseLineSearch represents the model behind the search form about `common\models\PurchaseLine`.
 */
class PurchaseLineSearch extends PurchaseLine
{
    /**
     * @inheritdoc
     */
    public $doc_no;
    public $item_name;
    public $order_date;
    public $vendor_name;
    public function rules()
    {
        return [
            [['id', 'source_id'], 'integer'],
            [['source_no', 'type', 'items_no', 'description', 'location', 'measure', 'expected_date', 'planned_date', 'doc_no', 'item_name', 'order_date', 'vendor_name'], 'safe'],
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
        ->joinwith('items')
        ->joinwith('vendors')
        ->where(['purchase_line.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

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


        // $dataProvider->sort->attributes['vendor_name'] = [
        //     'asc' => ['purchase_header.vendors.name' => SORT_ASC],
        //     'desc' => ['purchase_header.vendors.name' => SORT_DESC],
        // ];

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
            'quantity' => $this->quantity,
            'unitcost' => $this->unitcost,
            'lineamount' => $this->lineamount,
            'linediscount' => $this->linediscount,
            //'expected_date' => $this->expected_date,
            'planned_date' => $this->planned_date,
        ]);

        $query->andFilterWhere(['like', 'purchase_header.doc_no', $this->doc_no])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'items_no', $this->items_no])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'measure', $this->unit_of_measure])
            ->andFilterWhere(['like', 'items.description_th', $this->item_name])
            ->andFilterWhere(['like', 'vendors.name', $this->vendor_name]);
        

        if (!is_null($this->expected_date) && 
            strpos($this->expected_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->expected_date);
            $query->andFilterWhere(['between', 'DATE(expected_date)', $start_date, $end_date]);
        }

        if (!is_null($this->order_date) && 
            strpos($this->order_date, ' - ') !== false ) {
            list($fdate, $tdate) = explode(' - ', $this->order_date);
            $query->andFilterWhere(['between', 'DATE(purchase_header.order_date)', $fdate, $tdate]);
        }
        return $dataProvider;
    }
}
