<?php

namespace admin\modules\company\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PrintPage;

/**
 * PrintSearch represents the model behind the search form about `common\models\PrintPage`.
 */
class PrintSearch extends PrintPage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pagination', 'paper_size', 'comp_id'], 'integer'],
            [['name', 'logo', 'header', 'header_height', 'footer_height', 'body_height', 'footer', 'signature'], 'safe'],
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
        $query = PrintPage::find();

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
            'pagination' => $this->pagination,
            'paper_size' => $this->paper_size,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'header', $this->header])
            ->andFilterWhere(['like', 'header_height', $this->header_height])
            ->andFilterWhere(['like', 'footer_height', $this->footer_height])
            ->andFilterWhere(['like', 'body_height', $this->body_height])
            ->andFilterWhere(['like', 'footer', $this->footer])
            ->andFilterWhere(['like', 'signature', $this->signature]);

        return $dataProvider;
    }
}
