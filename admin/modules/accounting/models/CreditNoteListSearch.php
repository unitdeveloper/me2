<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewRcInvoice;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class CreditNoteListSearch extends ViewRcInvoice
{
    /**
     * @inheritdoc
     */
    public $vat_regis;
    public function rules()
    {
        return [
            [['id', 'sale_id', 'user_id', 'comp_id', 'vat_percent', 'include_vat', 'order_id'], 'integer'],
            [['posting_date', 'order_date','status'], 'safe'],
            [['total', 'discount', 'percent_discount','grandtotal'], 'number'],
            [['no_'], 'string', 'max' => 100],
            [['cust_no_', 'status','doc_type','vat_regis'], 'string', 'max' => 50],
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
        $query = ViewRcInvoice::find();
        $query->joinWith('customer')
        ->where(['view_rc_invoice.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            //'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
            'sort'=> ['defaultOrder' => [
                   
                    'posting_date' => SORT_DESC,
                    'no_'=>SORT_DESC,
                ]
            ],
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

        $query->andFilterWhere(['like', 'no_', explode(' ',$this->no_)])
        ->andFilterWhere(['or',
            ['like', 'cust_no_', explode(' ',$this->cust_no_)],
            ['like', 'customer.name',  explode(' ',$this->cust_no_)]
        ])
        ->andFilterWhere(['like','customer.vat_regis', explode(' ',$this->vat_regis)])        
        ->andFilterWhere(['doc_type' => 'Credit-Note'])
        ->andFilterWhere(['view_rc_invoice.status' => $this->status])
        ->andFilterWhere(['vat_percent' => $this->vat_percent]);
        

        if (!is_null($this->posting_date) &&
            strpos($this->posting_date, ' - ') !== false) {
            list($start_date, $end_date) = explode(' - ', $this->posting_date);

            $query->andFilterWhere(['between', 'DATE(posting_date)', $start_date, $end_date]);

        }

        return $dataProvider;
    }
}
