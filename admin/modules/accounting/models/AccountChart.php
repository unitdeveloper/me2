<?php

namespace admin\modules\accounting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ChartOfAccount;

/**
 * AccountChart represents the model behind the search form about `common\models\ChartOfAccount`.
 */
class AccountChart extends ChartOfAccount
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['AccNo', 'AccName', 'AccDesc', 'Incom_Balance', 'AccType', 'Totaling'], 'safe'],
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
        $query = ChartOfAccount::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
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
        ]);

        $query->andFilterWhere(['like', 'AccNo', $this->AccNo])
            ->andFilterWhere(['like', 'AccName', $this->AccName])
            ->andFilterWhere(['like', 'AccDesc', $this->AccDesc])
            ->andFilterWhere(['like', 'Incom_Balance', $this->Incom_Balance])
            ->andFilterWhere(['like', 'AccType', $this->AccType])
            ->andFilterWhere(['like', 'Totaling', $this->Totaling]);

        return $dataProvider;
    }
}
