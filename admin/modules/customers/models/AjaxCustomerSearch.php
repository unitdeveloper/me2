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
class AjaxCustomerSearch extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['id', 'user_id','payment_term','status','genbus_postinggroup','suspend'], 'integer'],
            // [['name', 'address', 'address2', 'city', 'district', 'province', 'postcode', 'country', 'vatbus_postinggroup', 'genbus_postinggroup', 'status', 'vat_regis', 'headoffice', 'create_date'], 'safe'],
            // [['code'],'string'],
            [['address','code','name','owner_sales','transport','province','district','city'],'string'],
            [['blance','credit_limit'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
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
        

        if(Yii::$app->user->identity->id!=1)
        {
            if (in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','search'))) { 
                // Sale Admin
                $query->where(['or',['id' => 909],['customer.comp_id' => $myRule['comp_id']]]);
                
            }else  if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Data Access','accounting','Invoice','Customer','SearchCustomer'))) {            
                
                $query->where(['or',['id' => 909],['customer.comp_id' => $myRule['comp_id']]]);

            }else {
                
                $query->where(['or',['id' => 909],['customer.comp_id' => $myRule['comp_id']]])
                ->andWhere(new Expression('FIND_IN_SET(:owner_sales, customer.owner_sales)'))
                ->addParams([':owner_sales' => $myRule['sales_id']]);
            
            }
    
        }


        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => [
                'code' => SORT_ASC,
                'name'=> SORT_ASC]
            ],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'customer.status' => $this->status,
            //'genbus_postinggroup' => $this->genbus_postinggroup,
        ]);

        

        return $dataProvider;
    }

    
}
