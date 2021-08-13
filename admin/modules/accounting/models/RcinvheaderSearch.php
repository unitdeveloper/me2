<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RcInvoiceHeader;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class RcinvheaderSearch extends RcInvoiceHeader
{
    /**
     * @inheritdoc
     */
    public $postinggroup;
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['no_', 'cust_no_', 'cust_name_', 'cust_address', 'cust_address2', 'posting_date', 'order_date', 'ship_date', 'postinggroup', 'vat_percent'], 'safe'],
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
        $query = RcInvoiceHeader::find();



        $myRule         = Yii::$app->session->get('Rules');

        //var_dump($myRule);
        // if(Yii::$app->user->identity->id==1){
        //     $query->joinWith(['customer'])
        //     ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']]);
        // }else {

        
        //     if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            
        //         // Sale Admin
                
                

        //         if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade'))){  
        //         // Sale Modern Trade

        //             $query->joinWith(['customer'])
        //             ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']])
        //             ->andWhere(['genbus_postinggroup' => 2]);


        //         }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

        //             $query->joinWith(['customer'])
        //             ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']])
        //             ->andWhere(['genbus_postinggroup' => 1]);

        //         }else {

        //             $query->joinWith(['customer'])
        //             ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']]);


        //         }

                

                
        //     }else {
        //         $query->joinWith(['customer'])
        //         ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']]);
        //     }
            
        // }

        $query->joinWith(['customer'])
                ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['posting_date' => SORT_DESC,'no_'=>SORT_DESC]],
            //'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            
            return $dataProvider;
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_date' => $this->order_date,
            'ship_date' => $this->ship_date,
            'cust_no_' => $this->cust_no_,
            'customer.genbus_postinggroup' => $this->postinggroup,
            'rc_invoice_header.vat_percent' => $this->vat_percent
        ]);

        $query->andFilterWhere(['like', 'rc_invoice_header.no_', $this->no_])
            ->andFilterWhere(['or',['like', 'rc_invoice_header.cust_name_', explode(' ',$this->cust_name_)]])
            ->andFilterWhere(['like', 'rc_invoice_header.cust_address', $this->cust_address]);
            //->andFilterWhere(['like', 'customer.genbus_postinggroup', $this->postinggroup]);
        
        if (!is_null($this->posting_date) && 
            strpos($this->posting_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->posting_date);

            $query->andFilterWhere(['between', 'DATE(posting_date)', $start_date, $end_date]);

        }    

        return $dataProvider;
    }
}
