<?php

namespace admin\modules\warehousemoving\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarehouseMoving;

/**
 * WarehouseSearch represents the model behind the search form about `common\models\WarehouseMoving`.
 */
class WarehouseSearch extends WarehouseMoving
{
    /**
     * @inheritdoc
     */
    public $month;
    public function rules()
    {
        return [
            [['id', 'line_no'], 'integer'],
            [['DocumentNo', 'PostingDate', 'TypeOfDocument', 'SourceDoc', 'SourceDocNo', 'ItemNo', 'Description', 'DocumentDate','ItemId','location', 'month'], 'safe'],
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
        $query->JoinWith('items')
        ->where(['warehouse_moving.comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $workdate   = Yii::$app->session->get('workdate');
        $workYears  = date('Y',strtotime($workdate));

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
            //'PostingDate' => $this->PostingDate,
            'Quantity' => $this->Quantity,
            'QtyToMove' => $this->QtyToMove,
            'QtyMoved' => $this->QtyMoved,
            'QtyOutstanding' => $this->QtyOutstanding,
            'DocumentDate' => $this->DocumentDate,
            'location' => $this->location,
            'item' => base64_decode($this->ItemId),
        ]);

        $query->andFilterWhere(['like', 'DocumentNo', $this->DocumentNo])
            ->andFilterWhere(['like', 'TypeOfDocument', $this->TypeOfDocument])
            ->andFilterWhere(['like', 'SourceDoc', $this->SourceDoc])
            ->andFilterWhere(['like', 'SourceDocNo', $this->SourceDocNo])
            ->andFilterWhere(['like', 'items.master_code', $this->ItemNo])
            ->andFilterWhere(['like', 'items.Description', $this->Description]);

        // $this->month ?   
        // $dataProvider->query->andFilterWhere(['between', 
        //     'PostingDate', 
        //     date('Y-m-d',strtotime($workYears.'-'.$this->month.'-01')).' 00:00:0000',
        //     date('Y-m-t',strtotime($workYears.'-'.$this->month.'-01')).' 23:59:59'
        // ]) : null;


        if (!is_null($this->PostingDate) && strpos($this->PostingDate, ' - ') !== false ) {
            list($start_date, $end_date) = explode(' - ', $this->PostingDate);
            $query->andFilterWhere(['between', 'warehouse_moving.PostingDate', $start_date.' 00:00:0000', $end_date.' 23:59:59']);
        }    
        

        return $dataProvider;
    }
}
