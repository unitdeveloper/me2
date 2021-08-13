<?php

namespace admin\modules\express\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Isvat;

/**
 * VatSearch represents the model behind the search form of `\common\models\Isvat`.
 */
class VatSearch extends Isvat
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ORGNUM'], 'integer'],
            [['TREC', 'VATTYP', 'RECTYP', 'VATPRD', 'LATE', 'VATDAT', 'DOCDAT', 'DOCNUM', 'REFNUM', 'NEWNUM', 'DEPCOD', 'DESCRP', 'REMARK', 'SELF_ADDED', 'HAD_MODIFY', 'DOCSTAT', 'TAXID', 'PRENAM'], 'safe'],
            [['AMT01', 'VAT01', 'AMT02', 'VAT02', 'AMTRAT0'], 'number'],
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
        $query = Isvat::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => [
                'VATDAT' => SORT_DESC
                ],
            ]
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
            'VATPRD' => $this->VATPRD,
            'VATDAT' => $this->VATDAT,
            'DOCDAT' => $this->DOCDAT,
            'AMT01' => $this->AMT01,
            'VAT01' => $this->VAT01,
            'AMT02' => $this->AMT02,
            'VAT02' => $this->VAT02,
            'AMTRAT0' => $this->AMTRAT0,
            'ORGNUM' => $this->ORGNUM,
        ]);

        $query->andFilterWhere(['like', 'TREC', $this->TREC])
            ->andFilterWhere(['like', 'VATTYP', $this->VATTYP])
            ->andFilterWhere(['like', 'RECTYP', $this->RECTYP])
            ->andFilterWhere(['like', 'LATE', $this->LATE])
            ->andFilterWhere(['like', 'DOCNUM', $this->DOCNUM])
            ->andFilterWhere(['like', 'REFNUM', $this->REFNUM])
            ->andFilterWhere(['like', 'NEWNUM', $this->NEWNUM])
            ->andFilterWhere(['like', 'DEPCOD', $this->DEPCOD])
            ->andFilterWhere(['like', 'DESCRP', $this->DESCRP])
            ->andFilterWhere(['like', 'REMARK', $this->REMARK])
            ->andFilterWhere(['like', 'SELF_ADDED', $this->SELF_ADDED])
            ->andFilterWhere(['like', 'HAD_MODIFY', $this->HAD_MODIFY])
            ->andFilterWhere(['like', 'DOCSTAT', $this->DOCSTAT])
            ->andFilterWhere(['like', 'TAXID', $this->TAXID])
            ->andFilterWhere(['like', 'PRENAM', $this->PRENAM]);

        return $dataProvider;
    }
}
