<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewRcInvoice;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class ViewRcInvoiceSearch extends ViewRcInvoice
{
    /**
     * @inheritdoc
     */
    public $name;
    public $start;
    public $end;
    public $postinggroup;
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['include_vat','posting_date','no_','vat_percent','start','end','cust_name_','cust_address','postinggroup'], 'safe'],
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
        $query = ViewRcInvoice::find()
        ->joinWith('customer')
        ->where(["view_rc_invoice.comp_id" => Yii::$app->session->get('Rules')['comp_id']]);
        $query->joinWith(['salesPeople']);
        

        $myRule         = Yii::$app->session->get('Rules');
        $SalePeople     = $myRule['sale_id'];
        $MYID           = $myRule['user_id'];


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
                ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','view_rc_invoice.status','Open'])
                ->andWhere(['genbus_postinggroup' => 2])
                ->orWhere(['sale_header.user_id' => $MYID]);

            }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

                $query->joinWith(['customer'])
                ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','view_rc_invoice.status','Open'])
                ->andWhere(['genbus_postinggroup' => 1])
                ->orWhere(['view_rc_invoice.user_id' => $MYID]);

            }else {

                $query->joinWith(['customer'])
                ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','view_rc_invoice.status','Open'])
                ->orWhere(['view_rc_invoice.user_id' => $MYID]);

            }            
            
        }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalesDirector','SalesDirector'))){  
            
            // Sale Director
            $query->joinWith(['customer'])
            ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']]);
        }else { 

            // Every One  (Default)         
            $query->joinWith(['customer'])
            ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']])
            ->andWhere(['view_rc_invoice.sale_id' => $SalePeople]);
            
            
        }
        
      
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['posting_date'=>SORT_DESC]]
        ]);
        


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
 
        //$filter = explode('/',$this->posting_date); 

        
       
        $query->andFilterWhere([
            'view_rc_invoice.id' => $this->id,
            'view_rc_invoice.vat_percent' => $this->vat_percent
        ]);
        

        

        $query->andFilterWhere(['like', 'view_rc_invoice.no_', $this->no_])
        ->andFilterWhere(['like', 'view_rc_invoice.cust_name_', $this->cust_name_])
        ->andFilterWhere(['like', 'view_rc_invoice.cust_address', $this->cust_address])
        ->andFilterWhere(['like', 'customer.genbus_postinggroup', $this->postinggroup]);

        // ถ้ามี posting_date และมี '/'
        if (!is_null($this->posting_date) && strpos($this->posting_date, '/') !== false ) {
            list($month, $years) = explode('/', $this->posting_date);
            $query->andFilterWhere([
                'MONTH(view_rc_invoice.posting_date)'   => $month,
                'YEAR(view_rc_invoice.posting_date)'    => $years
            ]);

        }else{
            $query->andFilterWhere(['between', 
            'DATE(view_rc_invoice.posting_date)', 
            Yii::$app->session->get('workyears').'-01-01', 
            Yii::$app->session->get('workyears').'-12-31']);
        }
        
        return $dataProvider;
    }


}
