<?php

namespace admin\modules\Purchase\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PurchaseReqHeader;
use admin\modules\apps_rules\models\SysRuleModels;

/**
 * ReqHeaderSearch represents the model behind the search form of `common\models\PurchaseReqHeader`.
 */
class ReqHeaderSearch extends PurchaseReqHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'line_no_', 'series_id', 'vendor_id', 'address_id', 'vat_type', 'include_vat', 'payment_term', 'status', 'user_id', 'comp_id', 'withholdTaxSwitch', 'project'], 'integer'],
            [['doc_no', 'vendor_name', 'address', 'address2', 'phone', 'fax', 'contact', 'ext_document', 'detail', 'taxid', 'create_date', 'order_date', 'payment_due', 'email', 'project_name', 'session_id', 'remark', 'ref_no', 'purchaser', 'delivery_date', 'withholdTax', 'withholdAttach'], 'safe'],
            [['balance', 'discount', 'percent_discount', 'vat_percent'], 'number'],
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
        $query = PurchaseReqHeader::find();

        $myRule         = Yii::$app->session->get('Rules');
        if(!in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','Main Menu','order','actionIndex','po'))){  
            // Purchaser
            $query->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(['user_id' => $myRule->user_id]);

        }else{
            $query->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['order_date'=>SORT_DESC]]
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
            'line_no_' => $this->line_no_,
            'series_id' => $this->series_id,
            'vendor_id' => $this->vendor_id,
            'address_id' => $this->address_id,
            'create_date' => $this->create_date,
            'balance' => $this->balance,
            'discount' => $this->discount,
            'percent_discount' => $this->percent_discount,
            'vat_type' => $this->vat_type,
            'include_vat' => $this->include_vat,
            'vat_percent' => $this->vat_percent,
            'payment_term' => $this->payment_term,
            'payment_due' => $this->payment_due,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
            'delivery_date' => $this->delivery_date,
            'withholdTaxSwitch' => $this->withholdTaxSwitch,
            'project' => $this->project,
        ]);

        $query->andFilterWhere(['like', 'doc_no', $this->doc_no])
            ->andFilterWhere(['like', 'vendor_name', $this->vendor_name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'address2', $this->address2])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'contact', $this->contact])
            ->andFilterWhere(['like', 'ext_document', $this->ext_document])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'taxid', $this->taxid])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'project_name', $this->project_name])
            ->andFilterWhere(['like', 'session_id', $this->session_id])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'ref_no', $this->ref_no])
            ->andFilterWhere(['like', 'purchaser', $this->purchaser])
            ->andFilterWhere(['like', 'withholdTax', $this->withholdTax])
            ->andFilterWhere(['like', 'withholdAttach', $this->withholdAttach]);
            
        if (!is_null($this->order_date) && 
            strpos($this->order_date, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->order_date);

            $query->andFilterWhere(['between', 'DATE(order_date)', $start_date, $end_date]);

        }

        return $dataProvider;
    }
}
