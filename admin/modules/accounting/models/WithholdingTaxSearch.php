<?php

namespace admin\modules\accounting\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WithholdingTax;

/**
 * WithholdingTaxSearch represents the model behind the search form of `common\models\WithholdingTax`.
 */
class WithholdingTaxSearch extends WithholdingTax
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'comp_id', 'comp_address', 'user_id', 'user_name'], 'integer'],
            [['customer_address', 'vat_regis', 'choice_substitute'], 'safe'],
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
        $query = WithholdingTax::find();

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
            'customer_id' => $this->customer_id,
            'comp_id' => $this->comp_id,
            'comp_address' => $this->comp_address,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
        ]);

        $query->andFilterWhere(['like', 'customer_address', $this->customer_address])
            ->andFilterWhere(['like', 'vat_regis', $this->vat_regis])
            ->andFilterWhere(['like', 'choice_substitute', $this->choice_substitute]);

        return $dataProvider;
    }
}
