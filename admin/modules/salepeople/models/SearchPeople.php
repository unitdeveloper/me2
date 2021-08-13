<?php

namespace admin\modules\salepeople\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SalesPeople;

/**
 * SearchPeople represents the model behind the search form about `common\models\SalesPeople`.
 */
class SearchPeople extends SalesPeople
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'comp_id'], 'integer'],
            [['code', 'name', 'surname', 'sale_group', 'tax_id', 'position', 'address', 'address2', 'postcode', 'date_added', 'sign_img','status'], 'safe'],
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
        $query = SalesPeople::find();

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
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
            'date_added' => $this->date_added,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', explode(' ',$this->name)])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'sale_group', $this->sale_group])
            ->andFilterWhere(['like', 'tax_id', $this->tax_id])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'address2', $this->address2])
            ->andFilterWhere(['like', 'postcode', $this->postcode])
            ->andFilterWhere(['like', 'sign_img', $this->sign_img]);

        return $dataProvider;
    }
}
