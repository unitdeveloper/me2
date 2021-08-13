<?php

namespace admin\modules\SaleOrders\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleReturnHeader;

/**
 * SaleReturnSearch represents the model behind the search form of `common\models\SaleReturnHeader`.
 */
class SaleReturnSearch extends SaleReturnHeader
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'source_id', 'sale_id', 'vat_percent', 'vat_type', 'include_vat', 'confirm_by', 'user_id', 'comp_id'], 'integer'],
            [['no', 'source_type', 'sale_address', 'bill_address', 'ship_address', 'order_date', 'ship_date', 'update_status_date', 'create_date', 'paymentdue', 'sales_people', 'ext_document', 'payment_term', 'remark', 'transport', 'update_by', 'update_date', 'confirm', 'release_date', 'confirm_date', 'shiped_date', 'comments', 'status', 'session_id'], 'safe'],
            [['balance', 'balance_befor_vat', 'discount', 'percent_discount'], 'number'],
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
        $query = SaleReturnHeader::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'        => $this->id,
            'user_id'   => $this->user_id
        ]);
        
        $query->andFilterWhere(['like', 'no', $this->no]);
        
        
        return $dataProvider;
    }
}
