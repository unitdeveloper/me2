<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleHeader;
use common\models\ItemMystore;

use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;
use common\models\ViewSaleNotInvoice;
/**
 * SalehearderSearch represents the model behind the search form about `common\models\SaleHeader`.
 */
class ViewNotInvSearch extends SaleHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'discount', 'user_id', 'comp_id','balance'], 'integer'],  
            [['order_date'], 'safe'],
            [['customer_id'], 'safe'],
            [['no','status','sale_id'],'string'],
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
        $query          = ViewSaleNotInvoice::find();

        $query->joinWith(['salespeople']);

        $myRule         = Yii::$app->session->get('Rules');
        $SalePeople     = $myRule['sale_id'];
        $MYID           = $myRule['user_id'];
      
        if($myRule['rules_id']==1)
        {
            $query->joinWith(['customer'])->andWhere(['NOT IN','view_sale_not_invoice.status',['Open','Cancel','Reject']]);          
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            
            // Sale Admin
            // -> จะไม่สามารถมองเห็นใบงานที่เป็น Open 
            // -> เว้นแต่เป็นผู้สร้างใบงานเอง
            if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade'))){  
            // Sale Modern Trade

                $query->joinWith(['customer'])
                ->where(['view_sale_not_invoice.comp_id' => $myRule['comp_id']])
                ->andWhere(['NOT IN','view_sale_not_invoice.status',['Open','Cancel','Reject']])
                ->andWhere(['genbus_postinggroup' => 2])
                ->orWhere(['view_sale_not_invoice.user_id' => $MYID]);

            }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

                $query->joinWith(['customer'])
                ->where(['view_sale_not_invoice.comp_id' => $myRule['comp_id']])
                ->andWhere(['NOT IN','view_sale_not_invoice.status',['Open','Cancel','Reject']])
                ->andWhere(['genbus_postinggroup' => 1])
                ->orWhere(['view_sale_not_invoice.user_id' => $MYID]);

            }else {

                $query->joinWith(['customer'])
                ->where(['view_sale_not_invoice.comp_id' => $myRule['comp_id']])
                ->andWhere(['NOT IN','view_sale_not_invoice.status',['Open','Cancel','Reject']])
                ->orWhere(['view_sale_not_invoice.user_id' => $MYID]);

            }            
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalesDirector','SalesDirector'))){  
            
            // Sale Director
            $query->joinWith(['customer'])
            ->where(['view_sale_not_invoice.comp_id' => $myRule['comp_id']])
            ->andWhere(['NOT IN','view_sale_not_invoice.status',['Open','Cancel','Reject']]);
        }else { 

            // Every One  (Default)         
            $query->joinWith(['customer'])
            ->where(['view_sale_not_invoice.comp_id' => $myRule['comp_id']])
            ->andWhere(['view_sale_not_invoice.sale_id' => $SalePeople])
            ->andWhere(['NOT IN','view_sale_not_invoice.status',['Open','Cancel','Reject']]);
            
        }
        
        
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date'=>SORT_DESC]],
            //'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
                    'sale_header.status' => $this->status,
                    'customer' => $this->customer,
                ])      
        ->andFilterWhere(['or',
                            ['like','sales_people.name',$this->sale_id],
                            ['like','sales_people.code',$this->sale_id]
                        ])
        ->andFilterWhere(['or',
                            ['like', 'no', explode(' ',$this->no)],
                            ['like', 'customer.name', explode(' ',$this->no)],
                            ['like', 'customer.code', $this->no]
                        ])
        ->andFilterWhere(['like', 'customer',$this->customer])
        ->andFilterWhere(['like', 'balance',$this->balance]);

        if (!is_null($this->order_date) && 
            strpos($this->order_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->order_date);

            $query->andFilterWhere(['between', 'DATE(order_date)', $start_date, $end_date]);

        }else{
            $query->andFilterWhere(['between', 'DATE(order_date)', Yii::$app->session->get('workyears').'-01-01', Yii::$app->session->get('workyears').'-12-31']);
        }
        
        return $dataProvider;
    }

    

}
