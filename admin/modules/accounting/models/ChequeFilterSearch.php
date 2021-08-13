<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Cheque;

/**
 * ChequeSearch represents the model behind the search form about `common\models\Cheque`.
 */
class ChequeFilterSearch extends Cheque
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
        ]);

        $query->andFilterWhere(['like', 'bank_list.name', explode(' ',$this->bank)])
            ->andFilterWhere(['or',
                ['like', 'customer.name', explode(' ',$this->cust_name_)],                
            ])
            ->andFilterWhere(['like', 'cheque.balance', $this->balance]);


        if (!is_null($this->posting_date) &&
            strpos($this->posting_date, ' - ') !== false) {
            list($start_date, $end_date) = explode(' - ', $this->posting_date);

            $query->andFilterWhere(['between', 'DATE(posting_date)', $start_date, $end_date]);

        }

        if (!is_null($this->post_date_cheque) &&
            strpos($this->post_date_cheque, ' - ') !== false) {
            list($fdate, $tdate) = explode(' - ', $this->post_date_cheque);

            $query->andFilterWhere(['between', 'DATE(post_date_cheque)', $fdate, $tdate]);

        }

        

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
