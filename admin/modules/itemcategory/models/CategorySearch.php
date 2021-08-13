<?php

namespace admin\modules\itemcategory\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ItemCategory;

/**
 * CategorySearch represents the model behind the search form about `common\models\ItemCategory`.
 */
class CategorySearch extends ItemCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'child', 'comp_id'], 'integer'],
            [['name', 'discription', 'status', 'date_added'], 'safe'],
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
        $query = ItemCategory::find();

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
            'child' => $this->child,
            'date_added' => $this->date_added,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'discription', $this->discription])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
