<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RcInvoiceHeader;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class CreditnoteSearch extends RcInvoiceHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['no_', 'cust_no_', 'cust_name_', 'cust_address', 'cust_address2', 'posting_date', 'order_date', 'ship_date'], 'safe'],
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
        $query = RcInvoiceHeader::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        

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
            'posting_date' => $this->posting_date,

        ]);

        $query->andFilterWhere(['doc_type' => 'Credit-Note']);

        return $dataProvider;
    }
}
