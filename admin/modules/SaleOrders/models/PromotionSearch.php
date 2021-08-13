<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Promotions;

/**
 * PromotionSearch represents the model behind the search form of `common\models\Promotions`.
 */
class PromotionSearch extends Promotions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'items', 'create_by', 'approve_by', 'status', 'comp_id'], 'integer'],
            [['item_group', 'create_date', 'approve_date'], 'safe'],
            [['sale_amount', 'discount'], 'number'],
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
        $query = Promotions::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'items' => $this->items,
            'sale_amount' => $this->sale_amount,
            'discount' => $this->discount,
            'create_by' => $this->create_by,
            'create_date' => $this->create_date,
            'approve_by' => $this->approve_by,
            'approve_date' => $this->approve_date,
            'status' => $this->status,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'item_group', $this->item_group]);

        return $dataProvider;
    }
}
