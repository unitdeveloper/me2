<?php

namespace admin\modules\warehousemoving\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewInventory;

/**
 * InventorySearch represents the model behind the search form of `common\models\ViewInventory`.
 */
class InventorySearch extends ViewInventory
{
    /**
     * @inheritdoc
     */
    public $dateRang,$type;
    public function rules()
    {
        return [
            [['id', 'vat_type'], 'integer'],
            [['SourceDocNo', 'master_code', 'DocumentNo', 'description_th', 'TypeOfDocument', 'PostingDate','dateRang','type'], 'safe'],
            [['Quantity', 'unit_price'], 'number'],
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
        $query = ViewInventory::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['PostingDate'=>SORT_DESC]],
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
            'Quantity' => $this->Quantity,
            'unit_price' => $this->unit_price,
            'vat_type' => $this->vat_type,
            'TypeOfDocument' => $this->TypeOfDocument,
            'TypeOfDocument' => $this->type,
            
        ]);
         
        $query->andFilterWhere(['like', 'SourceDocNo', $this->SourceDocNo])
            ->andFilterWhere(['like', 'master_code', $this->master_code])
            ->andFilterWhere(['like', 'DocumentNo', $this->DocumentNo])
            ->andFilterWhere(['like', 'description_th', $this->description_th]);

       

        if($this->dateRang!=''){

           
            $dateFilter = explode(' - ',trim($this->dateRang));
            //var_dump($dateFilter); exit();


            $query->andFilterWhere(['between', 'date(PostingDate)', $dateFilter[0],$dateFilter[1]]);
        }

        return $dataProvider;
    }
}
