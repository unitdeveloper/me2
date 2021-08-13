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
class SalehearderSearch extends SaleHeader
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
      
        if($myRule['rules_id']==1)
        {
            $query->joinWith(['customer']);          
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            
            // Sale Admin
            // -> จะไม่สามารถมองเห็นใบงานที่เป็น Open 
            // -> เว้นแต่เป็นผู้สร้างใบงานเอง
            if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade'))){  
            // Sale Modern Trade

                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','sale_header.status','Open'])
                ->andWhere(['genbus_postinggroup' => 2])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','sale_header.status','Open'])
                ->andWhere(['<>','genbus_postinggroup',2])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }else {

                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','sale_header.status','Open'])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }            
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalesDirector','SalesDirector'))){  
            
            // Sale Director
            $query->joinWith(['customer'])
            ->where(['sale_header.comp_id' => $myRule['comp_id']]);
        }else { 

            // Every One  (Default)         
            $query->joinWith(['customer'])
            ->where(['sale_header.comp_id' => $myRule['comp_id']])
            ->andWhere(['sale_header.sale_id' => $SalePeople]);
            
            
        }
        
        
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort'=> ['defaultOrder' => ['no'=>SORT_DESC]],
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

        // if($this->order_date !== NULL){
            
        //     // Register ปีที่ค้นหา
        //     Yii::$app->session->set('workyears',date('Y',strtotime($this->order_date)));

        //     $query->andFilterWhere(['like','order_date' , date('Y-m-d',strtotime($this->order_date))]);
        // }
        $query
        ->andFilterWhere(['or',
                            ['like','sales_people.name',$this->sale_id],
                            ['like','sales_people.code',$this->sale_id]
                        ])
        ->andFilterWhere(['or',
                            ['like', 'no', explode(' ',$this->no)],
                            ['like', 'customer.name', explode(' ',$this->no)],
                            ['like', 'customer.code', $this->no]
                        ])
        ->andFilterWhere(['like', 'customer_id',$this->customer_id]);

        return $dataProvider;
    }

    public function getMyitem($company){
        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item_no;
            }
             
            return $itemArr;         
        } else {
            return '0';            
        }
    }

}
