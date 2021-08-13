<?php
namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WarehouseMoving;

/**
 * WarehouseSearch represents the model behind the search form about `common\models\WarehouseMoving`.
 */
class BestsaleSearch extends WarehouseMoving
{
    /**
     * @inheritdoc
     */
    
    public function rules()
    {
        return [
            [['id', 'line_no'], 'integer'],
            [['DocumentNo', 'PostingDate', 'TypeOfDocument', 'SourceDoc', 'SourceDocNo', 'ItemNo', 'Description', 'DocumentDate','ItemId','name'], 'safe'],
            [['Quantity', 'QtyToMove', 'QtyMoved', 'QtyOutstanding','qty'], 'number'],
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
        $query->JoinWith('itemstb')
        ->select('warehouse_moving.item,items.Description as name,sum(ABS(warehouse_moving.Quantity)) as qty')
        ->andWhere(['warehouse_moving.TypeOfDocument' => 'Sale'])
        ->orderBy(['qty'=> SORT_DESC]);
        $query->groupBy('warehouse_moving.item,name');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort'=> ['defaultOrder' => ['PostingDate'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['qty'] = [
            'asc' => ['qty' => SORT_ASC],
            'desc' => ['qty' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        // $query->andFilterWhere([
        //     'id' => $this->id,
             
        // ]);

        if($this->qty){
            $query->Having('qty = :qty',[':qty'=> $this->qty]);
        }
        $query->andFilterWhere(['like', 'items.Description', $this->name]);
        

        return $dataProvider;
    }
}
