<?php

namespace admin\modules\config\model;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ImportFile;

/**
 * ImportSearch represents the model behind the search form of `common\models\ImportFile`.
 */
class ImportSearch extends ImportFile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'position_qty', 'position_discount', 'position_total'], 'integer'],
            [['name', 'description', 'position_qty_num', 'position_discount_num', 'position_total_num', 'keyword_po', 'auto_remark', 'find_code'], 'safe'],
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
        $query = ImportFile::find();

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
            'position_qty' => $this->position_qty,
            'position_discount' => $this->position_discount,
            'position_total' => $this->position_total,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'position_qty_num', $this->position_qty_num])
            ->andFilterWhere(['like', 'position_discount_num', $this->position_discount_num])
            ->andFilterWhere(['like', 'position_total_num', $this->position_total_num])
            ->andFilterWhere(['like', 'keyword_po', $this->keyword_po])
            ->andFilterWhere(['like', 'auto_remark', $this->auto_remark])
            ->andFilterWhere(['like', 'find_code', $this->find_code]);

        return $dataProvider;
    }
}
