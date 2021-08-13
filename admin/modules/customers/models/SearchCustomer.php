<?php

namespace admin\modules\customers\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use common\models\Customer;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SearchCustomer represents the model behind the search form about `common\models\customer`.
 */
class SearchCustomer extends Customer
{
    /**
     * @inheritdoc
     */
    public $balance;
    public $region;

    public function rules()
    {
        return [
             [['id', 'user_id','payment_term','status','genbus_postinggroup','suspend'], 'integer'],
            // [['name', 'address', 'address2', 'city', 'district', 'province', 'postcode', 'country', 'vatbus_postinggroup', 'genbus_postinggroup', 'status', 'vat_regis', 'headoffice', 'create_date'], 'safe'],
            // [['code'],'string'],
            [['address','code','name','owner_sales','transport','province','district','city','region'],'string'],
            [['blance','credit_limit','balance'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
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
        $myRule = Yii::$app->session->get('Rules');

        $query = customer::find();
        $query->joinWith('provincetb');
        $query->joinWith('provincetb.zone');
        $query->joinWith('salesHasCustomer');
        
        
        if(Yii::$app->user->identity->id!=1)
        {
            //if($myRule['rules_id']==4 || $myRule['rules_id']==1 ) // 4 = Sale Admin
            if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','search')))
            {
                $query->where(['or',
                    ['customer.comp_id' => $myRule['comp_id']],
                    ['customer.id' => 909]
                ]);
                
            }else {            
                
                $query->where(['or',
                    ['customer.comp_id' => $myRule['comp_id']],
                    ['customer.id' => 909]
                ])
                ->andWhere(new Expression('FIND_IN_SET(:owner_sales, owner_sales)'))
                ->addParams([':owner_sales' => $myRule['sales_id']]);
            }
           
             
        }
        // add conditions that should always apply here

        // $dataProvider->sort->attributes['customer'] = [
        //     'asc' => ['customer.name' => SORT_ASC],
        //     'desc' => ['customer.name' => SORT_DESC],
        // ];

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => [
                'code' => SORT_ASC,
                'name'=> SORT_ASC]
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
            'status' => $this->status,
            'genbus_postinggroup' => $this->genbus_postinggroup,
            //'sum(sale_header.balance)' => $this->balance
        ]);

        if($this->balance){
           // $query->Having('sale_header.balance = :balance',[':balance' => $this->balance]);
        }
        
        //$name = explode(' ',$this->name);

        $query->andFilterWhere(['or',
                ['like', 'customer.name', explode(' ',$this->name)],
                ['like', 'customer.address', explode(' ',$this->name)],
                ['like', 'customer.phone', explode(' ',$this->name)],
                //['like', 'district.DISTRICT_NAME', explode(' ',$this->name)],
                //['like', 'amphur.AMPHUR_NAME', explode(' ',$this->name)]
            ])     
            ->andFilterWhere(['like', 'owner_sales', $this->owner_sales])      
            ->andFilterWhere(['like', 'province.PROVINCE_NAME', $this->province])
            ->andFilterWhere(['like', 'customer.code', $this->code])
            ->andFilterWhere(['like', 'zone.id', $this->region])
            ->andFilterWhere(['like', 'customer.suspend', $this->suspend]);

        if(isset($_GET['new'])){
            $query->andFilterWhere(['YEAR(create_date)' => date('Y'),'MONTH(create_date)' => date('m')]);
        }


        return $dataProvider;
    }

    
}
