<?php

namespace admin\modules\vendors\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use common\models\Customer;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SearchCustomer represents the model behind the search form about `common\models\customer`.
 */
class CustomerSearch extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['id', 'user_id','payment_term','status','genbus_postinggroup'], 'integer'],
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
        $query->joinWith('citytb');
        $query->joinWith('districttb');

        

       

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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
            'genbus_postinggroup' => $this->genbus_postinggroup,

        ]);
        //$name = explode(' ',$this->name);

        $query->andFilterWhere(['or',
                ['like', 'name', explode(' ',$this->name)],
                ['like', 'address', explode(' ',$this->name)],
                ['like', 'district.DISTRICT_NAME', explode(' ',$this->name)],
                ['like', 'amphur.AMPHUR_NAME', explode(' ',$this->name)]
            ])     
            ->andFilterWhere(['owner_sales' =>  $this->owner_sales])      
            ->andFilterWhere(['like', 'province.PROVINCE_NAME', $this->province])
            ->andFilterWhere(['like', 'code', $this->code]);
 
            

        return $dataProvider;
    }

    
}
