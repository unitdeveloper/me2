<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BillingNote;

use common\models\Cheque;
use common\models\Approval;
/**
 * BillingNoteSearch represents the model behind the search form about `common\models\BillingNote`.
 */
class BillingNoteSearch extends BillingNote
{
    /**
     * @inheritdoc
     */
    public $customer;
    public function rules()
    {
        return [
            [['payment'], 'safe'],
            [['id', 'cust_no_', 'vat_type', 'inv_no', 'user_id', 'comp_id'], 'integer'],
            [['no_', 'description', 'inv_date', 'paymentdue', 'create_date', 'posting_date', 'customer'], 'safe'],
            [['amount', 'paid','balance'], 'number'],
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
        //$query = BillingNote::find()->select('no_,max(create_date) as create_date')->groupBy('no_');

        $query = BillingNote::find();
        $query->joinWith('customer');

        $subQuery = Cheque::find()
            ->select('source_id,max(apply_to) as apply_to, sum(balance) as sum_balance')
            ->where(['cheque.id' => Approval::find()->select('source_id')->where(['comp_id'=>Yii::$app->request->get('comp_id')])])
            ->groupBy('source_id');

        $query->leftJoin(['chequeSum' => $subQuery], 'chequeSum.apply_to = billing_note.inv_no')
            ->where(['billing_note.id' => BillingNote::find()->select('max(id) as id')->groupBy('billing_note.no_')]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'pagination' => [
            //     'pageSize' => 10,
            // ],
            'sort'=> ['defaultOrder' => ['create_date' => SORT_DESC]],
        ]);



        // $dataProvider->setSort([
        //     'attributes' => [
        //         'no_',
        //         'create_date',
        //         'payment' => [
        //             'asc' => ['chequeSum.sum_balance' => SORT_ASC],
        //             'desc' => ['chequeSum.sum_balance' => SORT_DESC],
        //             'label' => 'Payment'
        //         ]
        //     ]
        // ]);        
     
        
        // $dataProvider->setSort([
        //     'attributes' => [
        //         'id',
        //         'name',
        //         'balance' => [
        //             'asc' => ['amount' => SORT_ASC],
        //             'desc' => ['amount' => SORT_DESC],
        //             'label' => 'Order Name'
        //         ]
        //     ]
        // ]); 




        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            // 'billing_note.id' => $this->id,
            // 'billing_note.cust_no_' => $this->cust_no_,
            // 'billing_note.vat_type' => $this->vat_type,
            // 'billing_note.inv_no' => $this->inv_no,
            // 'billing_note.inv_date' => $this->inv_date,
            // 'billing_note.paymentdue' => $this->paymentdue,
            // 'billing_note.amount' => $this->amount,
            // 'billing_note.paid' => $this->paid,
            // 'billing_note.balance' => $this->balance,
            // 'billing_note.create_date' => $this->create_date,
            // 'billing_note.posting_date' => $this->posting_date,
            // 'billing_note.user_id' => $this->user_id,
            //'billing_note.comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'billing_note.no_', $this->no_])
            ->andFilterWhere(['like', 'billing_note.description', $this->description])
            ->andFilterWhere(['or',
                ['like', 'customer.name', $this->customer],
                ['like', 'customer.code', $this->customer]
            ]);



        return $dataProvider;
    }
}
