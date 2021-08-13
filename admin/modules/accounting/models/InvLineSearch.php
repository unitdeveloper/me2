<?php

namespace admin\modules\accounting\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewInvoiceLine;
use admin\modules\apps_rules\models\SysRuleModels;
/**
 * InvLineSearch represents the model behind the search form of `common\models\ViewInvoiceLine`.
 * 
 * // set create_date = posting_date
 * UPDATE rc_invoice_line as line INNER JOIN rc_invoice_header as h ON h.id = line.source_id SET line.create_date = h.posting_date
 * SELECT * FROM `sale_invoice_line` WHERE `source_id` NOT IN (select id from sale_invoice_header)
 * 
 * // set create_date = posting_date
 * UPDATE sale_invoice_line as line INNER JOIN sale_invoice_header as h ON h.id = line.source_id SET line.create_date = h.posting_date
 * 
 */
class InvLineSearch extends ViewInvoiceLine
{
    /**
     * {@inheritdoc}
     */
    public $items;
    public $fdate;
    public $tdate;

    public function rules()
    {
        return [
            [['id', 'source_id',  'item', 'line_no_', 'order_id', 'source_line', 'cn_reference'], 'integer'],
            [['type', 'doc_no_', 'items','customer_no_', 'code_no_', 'code_desc_', 'source_doc', 'status', 'session_id', 'posted', 'fdate', 'tdate'], 'safe'],
            [['quantity', 'unit_price', 'vat_percent', 'line_discount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = ViewInvoiceLine::find();
        $query->joinWith('items');

        // add conditions that should always apply here
        if(in_array(\Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalehearderSearch','view'))){    
            $query->joinWith('header');
            $query->andFilterWhere(["sale_id" => \Yii::$app->session->get('Rules')['sale_id']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                        'posting_date'=>SORT_DESC,
                        'doc_no_' => SORT_DESC
                    ]
                ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'view_invoice_line.id' => $this->id,
            'view_invoice_line.item' => $this->item,
            'view_invoice_line.comp_id' => \Yii::$app->session->get('Rules')['comp_id']
        ]);

        

        $query->andFilterWhere(['or',
            ['like', 'items.description_th', $this->items],
            ['like', 'items.master_code', $this->items],
            ['like', 'view_invoice_line.doc_no_', $this->items]
        ]);



        if(($this->fdate!='') || ($this->tdate!=''))
        $query->andFilterWhere(['between', 'date(view_invoice_line.posting_date)', date('Y-m-d 00:00:0000',strtotime($this->fdate)), date('Y-m-d 23:59:59.9999',strtotime($this->tdate))]);

        if($this->vat_percent==1)
        $query->andFilterWhere(['>', 'view_invoice_line.vat_percent', 0]);

        if($this->vat_percent==2)
        $query->andFilterWhere(['view_invoice_line.vat_percent' =>  0]);

        return $dataProvider;
    }
}
