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
class RcinvBillSearch extends RcInvoiceHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
            [['id'], 'integer'],
            [['no_', 'cust_no_', 'cust_name_', 'cust_address', 'cust_address2', 'posting_date', 'order_date', 'ship_date'], 'safe'],
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
        $query = RcInvoiceHeader::find()
        ->select('cust_no_, paymentdue')
        ->where(['<>','cust_no_','909'])
        ->groupBy('cust_no_, paymentdue');

        
         
        $query->andwhere(['NOT IN','id',$this->paid]);
        

        //var_dump($this->getPaid());

        // $myRule         = Yii::$app->session->get('Rules');
 

         
        // $query->joinWith(['customer'])
        //  ->where(['rc_invoice_header.comp_id' => $myRule['comp_id']]);
        
        

        // แสดงรายการที่ใกล้ที่วันวางบิลที่สุด
        // จัดกลุ่มในแต่ละลูกค้า
        // 

        //$query->andWhere(['between', 'paymentdue', $formdate,$todate]);



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => ['pageSize' => 50],
            //'sort'=> ['defaultOrder' => ['paymentdue' => SORT_DESC]],
            //'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);


       

        //$dataProvider->query->indexBy('no_');
        // $dataProvider->pagination->pageSize=false;


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

         

        

        // $Cheque = \common\models\Cheque::find()->where(['apply_to' => $this->id]);
        // $ApproveStatus = array();
        // $data = '';
        // foreach ($Cheque->all() as $value) {

        //     $data = $value->getComplete();
        //     if($value->getComplete() > 0 ) {
                
        //         if($value->getComplete() == $this->getSumTotal()){
        //             $ApproveStatus[] = $value->apply_to;
        //         }else {
        //             $ApproveStatus[] = $value->apply_to;
        //         }
        //     }else {
        //         //$ApproveStatus[] = NULL;
        //     }

            

        // }

        // Approve แล้ว
         

        
        



        // grid filtering conditions
        $query->andFilterWhere([
            // 'id' => $this->id,
            // 'order_date' => $this->order_date,
            // 'ship_date' => $this->ship_date,
        ]);

        // $query->andFilterWhere(['like', 'rc_invoice_header.no_', $this->no_])
        //     ->andFilterWhere(['or',
        //         ['like', 'rc_invoice_header.cust_no_', $this->cust_no_],
        //         ['like', 'rc_invoice_header.cust_name_', $this->cust_no_]
        //         ])
        //     ->andFilterWhere(['like', 'rc_invoice_header.posting_date', $this->posting_date])
        //     ->andFilterWhere(['like', 'rc_invoice_header.cust_address', $this->cust_address]);
            //->andFilterWhere(['like', 'cust_address2', $this->cust_address2]);



        

        return $dataProvider;
    }
}
