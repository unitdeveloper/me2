<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SaleHeader;
 
use admin\modules\apps_rules\models\SysRuleModels;
 
/**
 * SalehearderSearch represents the model behind the search form about `common\models\SaleHeader`.
 */
class SaleListSearch extends SaleHeader
{
    /**
     * @inheritdoc
     */
    public $business_type;
    public $customer_name;
    public $search;
    public $vat;
    public $fdate;
    public $tdate;
    public $Invoice;
    public function rules()
    {
        return [
            [['id', 'discount', 'user_id', 'comp_id','update_by'], 'integer'],
            //[['ext_document', 'balance'], 'string'],
            [['no', 'customer_id','ext_document','payment_term','vat_type','include_vat', 'business_type'], 'safe'],
            [['order_date', 'ship_date', 'create_date','update_date'], 'safe'],
            [['customer', 'customer_name','search', 'vat', 'fdate', 'tdate'], 'safe'],
            [['status','remark','transport','sale_id', 'Invoice'],'string'],
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
        $query = SaleHeader::find();
        

        $query->joinWith(['salespeople']);

        $myRule         = Yii::$app->session->get('Rules');
        $SalePeople     = $myRule['sale_id'];
        $MYID           = $myRule['user_id'];
        
        if($myRule['rules_id']==1)
        {
            $query->joinWith(['customer'])->where(['sale_header.comp_id' => $myRule['comp_id']]);          
            
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
                // Sale Admin  
                $query->joinWith(['customer'])
                ->where(['sale_header.comp_id' => $myRule['comp_id']])
                //->andWhere(['<>','sale_header.status','Open'])
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
            ->where(['sale_header.comp_id' => $myRule['comp_id']])
            ->andWhere(['<>','genbus_postinggroup',2]);
        }else { 

            // Every One  (Default)         
            $query->joinWith(['customer'])
            ->where(['sale_header.comp_id' => $myRule['comp_id']])
            ->andWhere(['sale_header.sale_id' => $SalePeople]);
            
            
        }

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date'=>SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->sort->attributes['customer_name'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];

        

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['or',
            ['like','customer.name',trim($this->search)],
            ['like','customer.code',trim($this->search)],
            ['like','sales_people.code',trim($this->search)],
            ['like','sale_header.no',trim($this->search)]
        ]);

        $query->andFilterWhere([
                    'sale_header.status' => $this->status,
                    'customer.id' => $this->customer_id,
                    'customer.genbus_postinggroup' => $this->business_type
                ])
                ->andFilterWhere(['or',
                            ['like','sales_people.name',$this->sale_id],
                            ['like','sales_people.code',$this->sale_id]
                        ])
                ->andFilterWhere(['or',
                                ['like','customer.name',trim($this->no)],
                                ['like','customer.code',trim($this->no)],
                                ['like','sale_header.no',trim($this->no)]
                            ])
                ->andFilterWhere([$this->vat > 0 ? '>' : '<=','sale_header.vat_percent',$this->vat]);

        if(isset($_GET['month'])){
            $query->andFilterWhere(["MONTH(order_date)" => $_GET['month']]);
            $query->andFilterWhere(["YEAR(order_date)" => (Yii::$app->session->get('workyears'))? Yii::$app->session->get('workyears') : date('Y')]);
            Yii::$app->session->set('workmonth',$_GET['month']);
        }   

        if(isset($_GET['Y'])){
            Yii::$app->session->set('workyears',$_GET['Y']);
        }
  
        if($this->order_date){
            if (!is_null($this->order_date) &&  strpos($this->order_date, ' - ') !== false ) {
                list($start_date, $end_date) = explode(' - ', $this->order_date);
                $query->andFilterWhere(['between', 'DATE(order_date)', $start_date, $end_date]);
            }
            
        }

        


        //--- Date Filter ---
        //$LastDay    = date('t',strtotime(date('Y-m-d')));

        $formdate   = date('Y-m-').'01  00:00:0000';

        $todate     = date('Y-m-t').' 23:59:59.9999';

        if($this->fdate!='') $formdate     = date('Y-m-d 00:00:0000',strtotime($this->fdate));

        if($this->tdate!='') $todate       = date('Y-m-d 23:59:59.9999',strtotime($this->tdate));

        if($this->fdate){
            $query->andFilterWhere(['between', 'date(sale_header.order_date)', $formdate, $todate]);
            Yii::$app->session->set('workyears',date('Y',strtotime($todate)));
            //--- /. Date Filter ---
        }else{
            // When filter customer (Request from customer detail)
            // Except Years filter 
            if(!isset($this->customer_id)){
                if(!$this->order_date){
                    $query->andFilterWhere(["YEAR(sale_header.order_date)" => (Yii::$app->session->get('workyears'))
                                                                            ? Yii::$app->session->get('workyears') 
                                                                            : date('Y')
                    ]);
                }
            }
            
        }
        //var_dump($query->createCommand()->rawSql);
        return $dataProvider;
    }

  

}
