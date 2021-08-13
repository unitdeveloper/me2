<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleHeader;
use common\models\ItemMystore;

use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;
/**
 * SalehearderSearch represents the model behind the search form about `common\models\SaleHeader`.
 */
class SalehearderFilterSearch extends SaleHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'discount', 'user_id', 'comp_id','update_by'], 'integer'],
            //[['ext_document', 'balance'], 'string'],
            [['no', 'customer_id','ext_document','payment_term','vat_type','include_vat'], 'safe'],
            [['order_date', 'ship_date', 'create_date','update_date'], 'safe'],
            [['customer'], 'safe'],
            [['status','remark','transport','sale_id'],'string'],
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
        $query          = SaleHeader::find();

        $query->joinWith(['salespeople']);

        $myRule         = Yii::$app->session->get('Rules');
        $SalePeople     = $myRule['sale_id'];
        $MYID           = $myRule['user_id'];


        // Policy Sale Admin
        // $Policy         = SetupSysMenu::findOne(2);
        // $myPolicy       = explode(',',$Policy->rules_id);
        $NotInSaleStatus  = ['Open','Reject','Credit-Note','Cancel'];
      
        if($myRule['rules_id']==1)
        {
            $query->joinWith(['customer']);          
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            
            // For Sale Man
            // IF Sale Admin
            //      -> จะไม่สามารถมองเห็นใบงานที่เป็น Open 
            //      -> เว้นแต่เป็นผู้สร้างใบงานเอง
            if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade'))){  
                
                // Sale Modern Trade
                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['NOT IN','sale_header.status',$NotInSaleStatus])
                ->andWhere(['genbus_postinggroup' => 2])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

                // Sale Admin
                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['NOT IN','sale_header.status',$NotInSaleStatus])
                ->andWhere(['genbus_postinggroup' => 1])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }else {

                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['NOT IN','sale_header.status',$NotInSaleStatus])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }            
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalesDirector','SalesDirector'))){  
            
            // Sale Director
            $query->joinWith(['customer'])
            ->where(['sale_header.comp_id' => $myRule['comp_id']]);
        }else { 

            // Every One  (Default)   
            // General Sale Man     
            $query->joinWith(['customer'])
            ->where(['sale_header.comp_id' => $myRule['comp_id']])
            ->andWhere(['NOT IN','sale_header.status',$NotInSaleStatus])
            ->andWhere(['sale_header.sale_id' => $SalePeople]);
            
            
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
                    'customer_id' => $this->customer_id,
                ]);

 
        $query->andFilterWhere(['or',
                            ['like','sales_people.name',$this->sale_id],
                            ['like','sales_people.code',$this->sale_id]
                        ])
        ->andFilterWhere(['or',
                            ['like', 'no', explode(' ',$this->no)],
                            ['like', 'customer.name', explode(' ',$this->no)],
                            ['like', 'customer.code', $this->no]
                        ])
        ->andFilterWhere(['like', 'customer_id',$this->customer_id]);

        // $query->andFilterWhere([
        //     'DAY(order_date)'    => ($this->order_date)? explode('/',$this->order_date)[0] : null,
        //     'MONTH(order_date)'   => ($this->order_date)? explode('/',$this->order_date)[1] : null,
        //     'YEAR(order_date)'    => ($this->order_date)? explode('/',$this->order_date)[2] : null,
        // ]);
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
