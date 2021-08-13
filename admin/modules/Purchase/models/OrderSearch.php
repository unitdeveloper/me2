<?php

namespace admin\modules\Purchase\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PurchaseHeader;

/**
 * OrderSearch represents the model behind the search form about `common\models\PurchaseHeader`.
 */
class OrderSearch extends PurchaseHeader
{
    /**
     * @inheritdoc
     */
    public $search;
    public function rules()
    {
        return [
            [['id', 'line_no_', 'vendor_id', 'address_id', 'vat_type', 'payment_term', 'status', 'user_id', 'comp_id'], 'integer'],
            [['vendor_name', 'address', 'address2', 'phone', 'fax', 'contact', 'ext_document', 'detail', 'taxid', 'create_date', 'order_date', 
            'payment_due', 'email', 'project_name', 'session_id','doc_no','search'], 'safe'],
            [['balance', 'discount'], 'number'],
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
        $query = PurchaseHeader::find()
        ->joinWith(['vendors'])
        ->where(['purchase_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort'=> ['defaultOrder' => ['order_date'=>SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['vendors'] = [
            'asc' => ['vendors.name' => SORT_ASC],
            'desc' => ['vendors.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            // 'line_no_' => $this->line_no_,
            // 'vendor_id' => $this->vendor_id,
            // 'address_id' => $this->address_id,
            // 'create_date' => $this->create_date,
            // 'balance' => $this->balance,
            // 'discount' => $this->discount,
            // 'vat_type' => $this->vat_type,
            // 'payment_term' => $this->payment_term,
            // 'payment_due' => $this->payment_due,
            // 'status' => $this->status,
            // 'user_id' => $this->user_id,
            // 'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['or',
            ['like', 'vendors.name', $this->search],
            ['like', 'vendors.code', $this->search],
            ['like', 'purchase_header.doc_no', $this->search]
        ]);

        // $query->andFilterWhere(['like', 'purchase_header.address', $this->address])
        //     ->andFilterWhere(['like', 'purchase_header.address2', $this->address2])
        //     ->andFilterWhere(['like', 'purchase_header.phone', $this->phone])
        //     ->andFilterWhere(['like', 'purchase_header.fax', $this->fax])
        //     ->andFilterWhere(['like', 'purchase_header.contact', $this->contact])
        //     ->andFilterWhere(['like', 'purchase_header.ext_document', $this->ext_document])
        //     ->andFilterWhere(['like', 'purchase_header.detail', $this->detail])
        //     ->andFilterWhere(['like', 'purchase_header.taxid', $this->taxid])
        //     ->andFilterWhere(['like', 'purchase_header.email', $this->email])
        //     ->andFilterWhere(['like', 'purchase_header.project_name', $this->project_name])
        //     ->andFilterWhere(['like', 'purchase_header.doc_no', $this->doc_no]);


        if (!is_null($this->order_date) && 
            strpos($this->order_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->order_date);

            $query->andFilterWhere(['between', 'DATE(purchase_header.order_date)', $start_date, $end_date]);

        }

        return $dataProvider;
    }
}
