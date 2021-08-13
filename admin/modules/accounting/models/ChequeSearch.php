<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Cheque;

/**
 * ChequeSearch represents the model behind the search form about `common\models\Cheque`.
 */
class ChequeSearch extends Cheque
{
    /**
     * @inheritdoc
     */
    public $fdate;
    public $tdate;

    public function rules()
    {
        return [
            [['id', 'tranfer_to'], 'integer'],
            [['bank', 'bank_account', 'bank_branch', 'bank_id', 'create_date', 'posting_date', 'post_date_cheque','cust_no_','cust_name_','fdate','tdate'], 'safe'],
            [['balance'], 'number'],
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
        $query = Cheque::find();
        $query->joinwith('customer');
        $query->joinwith('banklist');
        $query->where(["cheque.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
             'cheque.id' => $this->id,
             'customer.id' => $this->cust_no_,
            // 'create_date' => $this->create_date,
            // 'posting_date' => $this->posting_date,
            // 'tranfer_to' => $this->tranfer_to,
            // 'balance' => $this->balance,
            // 'post_date_cheque' => $this->post_date_cheque,
            //'cheque.cust_no_' => $this->cust_no_,
        ]);

        $query->andFilterWhere(['like', 'bank_list.name', explode(' ',$this->bank)])
            //->andFilterWhere(['like', 'customer.code', explode(' ',$this->cust_no_)])
            ->andFilterWhere(['or',
                ['like', 'customer.name', explode(' ',$this->cust_name_)],                
                //['like', 'cheque.posting_date', explode(' ',$this->posting_date)]
            ])
            ->andFilterWhere(['like', 'cheque.balance', $this->balance]);

        //--- Date Filter ---
        $LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-').date('m-').'01  00:00:0000';

        $todate     = date('Y-').date('m-').$LastDay.' 23:59:59.9999';

        if($this->fdate!='') $formdate     = date('Y-m-d 00:00:0000',strtotime($this->fdate));

        if($this->tdate!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime($this->tdate));

        $query->andFilterWhere(['between', 'date(posting_date)', $formdate,$todate]);
        //--- /. Date Filter ---

        return $dataProvider;
    }


    public function getMyCustomer(){

        $myRule = Yii::$app->session->get('Rules');

        $query  = Customer::find() 
                ->where(['comp_id' => $myRule['comp_id']])
                ->andWhere(new Expression('FIND_IN_SET(:owner_sales, owner_sales)'))
                ->addParams([':owner_sales' => $myRule['sales_id']])
                ->all();

        $myCus = array();
        foreach ($query as $value) {
            $myCus[] =  $value['id'];
        }        
        
        return $myCus;
    }
}
