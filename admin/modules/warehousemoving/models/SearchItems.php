<?php

namespace admin\modules\warehousemoving\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Items;

/**
 * SearchItems represents the model behind the search form about `common\models\Items`.
 */
class SearchItems extends Items
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['No','master_code','Description','description_th'],'string'],            
            // [['Description', 'UnitOfMeasure', 'TypeOfProduct', 'CostingMethod', 'ProductionBom','master_code'], 'safe'],
            // [['Inventory', 'UnitCost', 'CostGP', 'StandardCost'], 'number'],
             [['ItemGroup', 'PriceStructure_ID'], 'integer'],
            // [['master_code'], 'string'],
             [['barcode'],'integer'],
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
        $query = Items::find();

        // if(Yii::$app->user->identity->id!=1)
        // {
        //     $query->where(['id' => Yii::$app->session->get('myCompany')]);
             
        // }

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
        

        
        

        
        $query->andFilterWhere(['or',
                ['like', 'description_th', explode(' ',trim($this->description_th))],
                ['like', 'Description', explode(' ',trim($this->description_th))]
            ])
            ->andFilterWhere(['like', 'No', trim($this->No)])
            ->andFilterWhere(['like', 'ItemGroup', $this->ItemGroup])
            ->andFilterWhere(['like', 'CostingMethod', $this->CostingMethod])
            ->andFilterWhere(['or',
                              ['like', 'master_code', trim($this->master_code)],
                              ['like', 'barcode', trim($this->master_code)]
                              ])
           ->andFilterWhere(['like', 'ProductionBom', $this->ProductionBom]);

           

        return $dataProvider;
    }
}
