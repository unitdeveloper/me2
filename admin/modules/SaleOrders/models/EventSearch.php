<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleEventHeader;

/**
 * EventSearch represents the model behind the search form about `common\models\SaleEventHeader`.
 */
class EventSearch extends SaleEventHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'comp_id', 'sale_id', 'vat_percent', 'vat_type', 'include_vat', 'sourcedoc', 'completeship'], 'integer'],
            [['no', 'customer_id', 'sale_address', 'bill_address', 'ship_address', 'order_date', 'ship_date', 'status', 'create_date', 'paymentdue', 'sales_people', 'ext_document', 'payment_term', 'remark', 'transport', 'reason_reject', 'update_by', 'update_date'], 'safe'],
            [['balance', 'balance_befor_vat', 'discount', 'percent_discount'], 'number'],
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
        $query = SaleEventHeader::find();

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
            'order_date' => $this->order_date,
            'ship_date' => $this->ship_date,
            'balance' => $this->balance,
            'balance_befor_vat' => $this->balance_befor_vat,
            'discount' => $this->discount,
            'percent_discount' => $this->percent_discount,
            'create_date' => $this->create_date,
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
            'paymentdue' => $this->paymentdue,
            'sale_id' => $this->sale_id,
            'vat_percent' => $this->vat_percent,
            'vat_type' => $this->vat_type,
            'update_date' => $this->update_date,
            'include_vat' => $this->include_vat,
            'sourcedoc' => $this->sourcedoc,
            'completeship' => $this->completeship,
        ]);

        $query->andFilterWhere(['like', 'no', $this->no])
            ->andFilterWhere(['like', 'customer_id', $this->customer_id])
            ->andFilterWhere(['like', 'sale_address', $this->sale_address])
            ->andFilterWhere(['like', 'bill_address', $this->bill_address])
            ->andFilterWhere(['like', 'ship_address', $this->ship_address])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'sales_people', $this->sales_people])
            ->andFilterWhere(['like', 'ext_document', $this->ext_document])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'transport', $this->transport])
            ->andFilterWhere(['like', 'reason_reject', $this->reason_reject])
            ->andFilterWhere(['like', 'update_by', $this->update_by]);

        return $dataProvider;
    }




}
