<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleInvoiceLine;

/**
 * SaleinvlineSearch represents the model behind the search form about `common\models\SaleInvoiceLine`.
 */
class SaleinvlineSearch extends SaleInvoiceLine
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'line_no_'], 'integer'],
            [['doc_no_', 'customer_no_', 'code_no_', 'code_desc_'], 'safe'],
            [['quantity', 'unit_price', 'vat_percent', 'line_discount'], 'number'],
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
        $query = SaleInvoiceLine::find();

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
        // $query->andFilterWhere([
        //     'id' => $this->id,
        //     'line_no_' => $this->line_no_,
        //     'quantity' => $this->quantity,
        //     'unit_price' => $this->unit_price,
        //     'vat_percent' => $this->vat_percent,
        //     'line_discount' => $this->line_discount,
        // ]);

        // $query->andFilterWhere(['like', 'doc_no_', $this->doc_no_])
        //     ->andFilterWhere(['like', 'customer_no_', $this->customer_no_])
        //     ->andFilterWhere(['like', 'code_no_', $this->code_no_])
        //     ->andFilterWhere(['like', 'code_desc_', $this->code_desc_]);

        return $dataProvider;
    }
}
