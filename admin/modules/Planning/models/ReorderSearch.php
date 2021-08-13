<?php

namespace admin\modules\Planning\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ItemMystore;

/**
 * ItemSearch represents the model behind the search form of `common\models\ItemMystore`.
 */
class ReorderSearch extends ItemMystore
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'item', 'user_modify', 'user_added', 'comp_id', 'unit_of_measure', 'clone', 'status'], 'integer'],
            [['item_no', 'barcode', 'master_code', 'name', 'name_en', 'detail', 'size', 'Photo', 'thumbnail1', 'thumbnail2', 'thumbnail3', 'thumbnail4', 'thumbnail5', 'online', 'date_added', 'date_modify'], 'safe'],
            [['unit_cost', 'sale_price', 'qty_per_unit', 'count_stock', 'safety_stock', 'reorder_point', 'minimum_stock'], 'number'],
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
        $query = ItemMystore::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 100],
            //'sort'=> ['defaultOrder' => ['items.invenByCache'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['items.invenByCache'] = [
            'asc' => ['items.invenByCache' => SORT_ASC],
            'desc' => ['items.invenByCache' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            
            return $dataProvider;
        }

        $query->where([
            'comp_id' => Yii::$app->session->get('Rules')['comp_id'],
            'status' => 1
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'item' => $this->item,
            'user_modify' => $this->user_modify,
            'user_added' => $this->user_added,
            'comp_id' => $this->comp_id,
            'date_added' => $this->date_added,
            'date_modify' => $this->date_modify,
            'unit_cost' => $this->unit_cost,
            'sale_price' => $this->sale_price,
            'qty_per_unit' => $this->qty_per_unit,
            'unit_of_measure' => $this->unit_of_measure,
            'clone' => $this->clone,
            'status' => $this->status,
            'count_stock' => $this->count_stock,
            'safety_stock' => $this->safety_stock,
            'reorder_point' => $this->reorder_point,
            'minimum_stock' => $this->minimum_stock
        ]);

        $query->andFilterWhere(['like', 'item_no', $this->item_no])
            ->andFilterWhere(['like', 'barcode', $this->barcode])
            ->andFilterWhere(['like', 'master_code', $this->master_code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'Photo', $this->Photo])
            ->andFilterWhere(['like', 'thumbnail1', $this->thumbnail1])
            ->andFilterWhere(['like', 'thumbnail2', $this->thumbnail2])
            ->andFilterWhere(['like', 'thumbnail3', $this->thumbnail3])
            ->andFilterWhere(['like', 'thumbnail4', $this->thumbnail4])
            ->andFilterWhere(['like', 'thumbnail5', $this->thumbnail5])
            ->andFilterWhere(['like', 'online', $this->online]);

        return $dataProvider;
    }
}
