<?php

namespace admin\modules\Management\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewRcInvoice;

use admin\modules\apps_rules\models\SysRuleModels;

/**
 * SaleinvheaderSearch represents the model behind the search form about `common\models\SaleInvoiceHeader`.
 */
class ReceiveSearch extends ViewRcInvoice
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
        $query = ViewRcInvoice::find();

        $myRule         = Yii::$app->session->get('Rules');

      
        if(Yii::$app->user->identity->id!=1){

            //var_dump(SysRuleModels::getPolicy('Main Function','Finance','report','common','menu'));
            if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin'))){    
            
                // Sale Admin
                
                

                if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Modern-Trade'))){  
                // Sale Modern Trade

                    $query->joinWith(['customer'])
                    ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']])
                    ->andWhere(['genbus_postinggroup' => 2]);
                    //->andWhere(['<>','view_rc_invoice.cust_no_','909']);


                }else if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Customer','customer','actionIndex','Customer-General'))){  

                    $query->joinWith(['customer'])
                    ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']])
                    ->andWhere(['genbus_postinggroup' => 1]);
                    //->andWhere(['<>','view_rc_invoice.cust_no_','909']);

                }else {

                    $query->joinWith(['customer'])
                    ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']]);
                    //->andWhere(['<>','view_rc_invoice.cust_no_','909']);


                }

                

                
            }else {
                $query->joinWith(['customer'])
                ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']]);
                //->andWhere(['<>','view_rc_invoice.cust_no_','909']);
            }

        }else {
            $query->joinWith(['customer'])
            ->where(['view_rc_invoice.comp_id' => $myRule['comp_id']]);
            //->andWhere(['<>','view_rc_invoice.cust_no_','909']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        

        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
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

        $query->andFilterWhere(['like', 'view_rc_invoice.no_', $this->no_])
        ->andFilterWhere(['like', 'customer.name', $this->name]);

            

        return $dataProvider;
    }
}
