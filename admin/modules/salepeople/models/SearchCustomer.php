<?php

namespace admin\modules\salepeople\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use common\models\SalesHasCustomer;

 

/**
 * SearchCustomer represents the model behind the search form about `common\models\customer`.
 */
class SearchCustomer extends SalesHasCustomer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['id', 'sale_id','cust_id','comp_id'], 'integer'],
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


        $query = SalesHasCustomer::find()->where(['sales_has_customer.comp_id' => $myRule['comp_id']]);
        $query->joinWith('salespeople');
        $query->joinWith('customer');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
             
            
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'sales_has_customer.comp_id' => $this->comp_id,            

        ]);


        $query->andFilterWhere(['like', 'sale_id', $this->sale_id]);
 
            

        return $dataProvider;
    }

    
}
