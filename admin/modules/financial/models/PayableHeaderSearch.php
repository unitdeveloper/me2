<?php

namespace admin\modules\financial\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApInvoiceHeader;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class PayableHeaderSearch extends ApInvoiceHeader
{
    /**
     * @inheritdoc
     */
    public $name;
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['no_', 'cust_no_', 'cust_name_', 'cust_address', 'cust_address2', 'posting_date', 'order_date', 'ship_date','name'], 'safe'],
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
        $query = ApInvoiceHeader::find();



        /** Create View */

        //DROP VIEW ap_invoice_header;
        // CREATE VIEW ap_invoice_header as 
        // SELECT `id`, `posting_date`, `no_`,  `cust_no_`, `cust_name_`, `cust_address`, `cust_address2`, `order_date`, `ship_date`, `cust_code`, `sales_people`,  `user_id`, `comp_id`, `discount`, `percent_discount`, `vat_percent`, `payment_term`, `paymentdue`, `ext_document`, `include_vat`, `remark`, `session_id`, `order_id`, `status`  FROM `rc_invoice_header` 

        // UNION ALL

        // SELECT `id`, `posting_date`, `no_`,  `cust_no_`, `cust_name_`, `cust_address`, `cust_address2`, `order_date`, `ship_date`, `cust_code`, `sales_people`,  `user_id`, `comp_id`, `discount`, `percent_discount`, `vat_percent`, `payment_term`, `paymentdue`, `ext_document`, `include_vat`, `remark`, `session_id`, `order_id`, `status`   FROM `sale_invoice_header`





        $myRule         = Yii::$app->session->get('Rules');

      
        if(Yii::$app->user->identity->id!=1){

            //var_dump(SysRuleModels::getPolicy('Main Function','Finance','report','common','menu'));
            if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            
                // Sale Admin                
                

                if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade'))){  
                // Sale Modern Trade

                    $query->joinWith(['vendor'])
                    ->where(['ap_invoice_header.comp_id' => $myRule['comp_id']])
                    ->andWhere(['genbus_postinggroup' => 2])
                    ->andWhere(['<>','ap_invoice_header.vendor_id','909']);


                }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

                    $query->joinWith(['vendor'])
                    ->where(['ap_invoice_header.comp_id' => $myRule['comp_id']])
                    ->andWhere(['genbus_postinggroup' => 1])
                    ->andWhere(['<>','ap_invoice_header.vendor_id','909']);

                }else {

                    $query->joinWith(['vendor'])
                    ->where(['ap_invoice_header.comp_id' => $myRule['comp_id']])
                    ->andWhere(['<>','ap_invoice_header.vendor_id','909']);


                }                

                
            }else {
                $query->joinWith(['vendor'])
                ->where(['ap_invoice_header.comp_id' => $myRule['comp_id']])
                ->andWhere(['<>','ap_invoice_header.vendor_id','909']);
            }

        }else {
            $query->joinWith(['vendor'])
            ->where(['ap_invoice_header.comp_id' => $myRule['comp_id']])
            ->andWhere(['<>','ap_invoice_header.vendor_id','909']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        

        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['vendor.name' => SORT_ASC],
            'desc' => ['vendors.name' => SORT_DESC],
        ];

        // $this->load($params);

        // if (!$this->validate()) {
        //     return $dataProvider;
        // }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
            //'cust_no_' => $this->cust_no_,
        ]);

        $query->andFilterWhere(['like', 'ap_invoice_header.no', $this->no_])
        ->andFilterWhere(['like', 'vendors.name', $this->name]);

            

        return $dataProvider;
    }
}
