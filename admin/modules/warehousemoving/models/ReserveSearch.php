<?php

namespace admin\modules\warehousemoving\models;

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
class ReserveSearch extends SaleHeader
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
            [['status','remark','transport'],'string'],
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
        $query = SaleHeader::find()
        ->where(['sale_header.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);


        $myRule         = Yii::$app->session->get('Rules');
        $SalePeople     = $myRule['sales_id'];
        $MYID           = $myRule['user_id'];


        // Policy Sale Admin
        $Policy         = SetupSysMenu::findOne(2);
        $myPolicy       = explode(',',$Policy->rules_id);


        if($myRule['rules_id']==1)
        {
            $query->joinWith(['customer']);

        }else {

            $query->joinWith(['customer'])
            ->where(['sale_header.comp_id' => $myRule['comp_id']]);

        }


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['no'=>SORT_DESC]],
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sale_header.status' => $this->status,
            'customer_id' => $this->customer_id,

        ]);

        $query->andFilterWhere(['or',
                                    ['like', 'no', explode(' ',$this->no)],
                                    ['like', 'customer.name', explode(' ',$this->no)],
                                    ['like', 'customer.code', $this->no]
                                ]);


        return $dataProvider;
    }

    public function getMyitem($company)
    {


        if(ItemMystore::find()->where(['comp_id' => $company])->count() > 0 )
        {
            $model = ItemMystore::find()->where(['comp_id' => $company])->all();
            foreach ($model as $value) {
                $itemArr[]= $value->item_no;
            }

            return $itemArr;
        } else {
            return '0';
            //throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
