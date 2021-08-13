<?php

namespace admin\modules\warehousemoving\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarehouseMoving;

/**
 * ShipmentSearch represents the model behind the search form about `common\models\WarehouseMoving`.
 */
class ShipmentSearch extends WarehouseMoving
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'line_no'], 'integer'],
            [['DocumentNo', 'PostingDate', 'TypeOfDocument', 'SourceDoc', 'SourceDocNo', 'ItemNo', 'Description', 'DocumentDate'], 'safe'],
            [['Quantity', 'QtyToMove', 'QtyMoved', 'QtyOutstanding'], 'number'],
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
        $query = WarehouseMoving::find();

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
            'line_no' => $this->line_no,
            'PostingDate' => $this->PostingDate,
            'Quantity' => $this->Quantity,
            'QtyToMove' => $this->QtyToMove,
            'QtyMoved' => $this->QtyMoved,
            'QtyOutstanding' => $this->QtyOutstanding,
            'DocumentDate' => $this->DocumentDate,
        ]);

        $query->andFilterWhere(['like', 'DocumentNo', $this->DocumentNo])
            ->andFilterWhere(['like', 'TypeOfDocument', $this->TypeOfDocument])
            ->andFilterWhere(['like', 'SourceDoc', $this->SourceDoc])
            ->andFilterWhere(['like', 'SourceDocNo', $this->SourceDocNo])
            ->andFilterWhere(['like', 'ItemNo', $this->ItemNo])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
