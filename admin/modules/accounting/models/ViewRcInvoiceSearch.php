<?php

namespace admin\modules\accounting\models;

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

      
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['posting_date'=>SORT_DESC]]
        ]);
        


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
 
        // $filter = explode('/',$this->posting_date); 

        $query->andFilterWhere([
            'view_rc_invoice.id' => $this->id,
            'view_rc_invoice.vat_percent' => $this->vat_percent,
            'MONTH(view_rc_invoice.posting_date)' => ($this->posting_date)? explode('/',$this->posting_date)[0] : null,
            'YEAR(view_rc_invoice.posting_date)' => ($this->posting_date)? explode('/',$this->posting_date)[1] : null,
        ]);
        
        

        $query->andFilterWhere(['like', 'view_rc_invoice.no_', $this->no_])
        ->andFilterWhere(['like', 'view_rc_invoice.cust_name_', $this->cust_name_])
        ->andFilterWhere(['like', 'view_rc_invoice.cust_address', $this->cust_address])
        ->andFilterWhere(['like', 'customer.genbus_postinggroup', $this->postinggroup]);

         

        return $dataProvider;
    }


}
