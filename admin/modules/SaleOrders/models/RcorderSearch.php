<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RcInvoiceHeader;
use common\models\RcInvoiceLine;



class RcorderSearch extends RcInvoiceHeader
{
    public $total;
    public $nbProd;
    public $client;
    public $custinfo;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total', 'nbProd', 'client','custinfo'], 'safe'],
            [['nbProd'], 'number'],
            [['client'], 'string'],
        ];
    }
    public function search($params)
    {
        $query = RcInvoiceHeader::find();
        $query->joinWith(['sales']);
        $query->joinWith(['customer']);
        
        $subQuery = RcInvoiceLine::find()
        ->select('source_id, SUM(quantity*unit_price) as total, count(code_no_) as nbProd')
        ->groupBy('source_id');

        $query->leftJoin(['orderSum' => $subQuery], 'orderSum.source_id = rc_invoice_header.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['client'] = [
            'asc' => ['sales.sales_people' => SORT_ASC],
            'desc' => ['sales.sales_people' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['custinfo'] = [
            'asc' => ['customer.cust_no_' => SORT_ASC],
            'desc' => ['customer.cust_no_' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nbProd'] = [
            'asc' => ['orderSum.nbProd' => SORT_ASC],
            'desc' => ['orderSum.nbProd' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['total'] = [
            'asc' => ['orderSum.total' => SORT_ASC],
            'desc' => ['orderSum.total' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'orderSum.total' => $this->total,
            'orderSum.nbProd' => $this->nbProd,
        ]);

        $query->andFilterWhere(['like', 'sales.name', $this->client])
            ->andFilterWhere(['like', 'customer.name', $this->custinfo]);

        return $dataProvider;
    }
}