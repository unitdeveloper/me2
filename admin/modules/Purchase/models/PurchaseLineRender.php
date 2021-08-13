<?php

namespace admin\modules\Purchase\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PurchaseLine;

/**
 * PurchaseLineSearch represents the model behind the search form about `common\models\PurchaseLine`.
 */
class PurchaseLineRender extends PurchaseLine
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source_id','user_id', 'comp_id'], 'integer'],
            [['source_no', 'type', 'items_no', 'description', 'location', 'unit_of_measure', 'expected_date', 'planned_date'], 'safe'],
            [['quantity', 'unitcost', 'lineamount', 'linediscount'], 'number'],
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
        $query = PurchaseLine::find()
                ->where([ 'source_id' => isset($_GET['id']) ?   $_GET['id'] : '' ]);

                //'user_id' => Yii::$app->user->identity->id

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
            'source_id' => $this->source_id,
            'quantity' => $this->quantity,
            'unitcost' => $this->unitcost,
            'lineamount' => $this->lineamount,
            'linediscount' => $this->linediscount,
            'expected_date' => $this->expected_date,
            'planned_date' => $this->planned_date,
        ]);

        $query->andFilterWhere(['like', 'source_no', $this->source_no])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'items_no', $this->items_no])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'unit_of_measure', $this->unit_of_measure]);

        return $dataProvider;
    }
}
