<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleInvoiceHeader;

/**
 * SaleinvoiceSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class SaleinvoiceSearch extends SaleInvoiceHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['no_', 'cust_no_', 'cust_name_', 'cust_address', 'cust_address2', 'posting_date', 'order_date', 
            'ship_date', 'cust_code', 'sales_people', 'document_no_', 'doc_type'], 'safe'],
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
        $query->joinWith('customer')
        ->where(['sale_invoice_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here
        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['no_'=>SORT_DESC,'posting_date' => SORT_DESC]],
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
            //'posting_date' => $this->posting_date,
            'order_date' => $this->order_date,
            'ship_date' => $this->ship_date,
            'doc_type' => 'Sale'
        ]);

        $query->andFilterWhere(['like', 'no_', explode(' ',$this->no_)])
            ->andFilterWhere(['or',
                ['like', 'cust_no_', explode(' ',$this->cust_no_)],
                ['like', 'customer.name',  explode(' ',$this->cust_no_)]
            ])
            ->andFilterWhere(['like', 'cust_address', explode(' ',$this->cust_address)]);

        if (!is_null($this->posting_date) &&
            strpos($this->posting_date, ' - ') !== false) {
            list($start_date, $end_date) = explode(' - ', $this->posting_date);

            $query->andFilterWhere(['between', 'DATE(posting_date)', $start_date, $end_date]);

        }


        return $dataProvider;
    }

}
