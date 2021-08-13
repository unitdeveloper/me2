<?php

namespace admin\modules\items\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ItemsHasGroups;

/**
 * SearchItems represents the model behind the search form about `common\models\Items`.
 */
class InStockSearch extends ItemsHasGroups
{
    /**
     * @inheritdoc
     */
 
    public function rules()
    {
        return [
            [['item_id', 'group_id', 'comp_id'], 'integer']            
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
        $query = ItemsHasGroups::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
         
            return $dataProvider;
        }

        $query->andFilterWhere([
            //'ItemGroup' => $this->groups,          
        ]);

        $query->andFilterWhere(['or',
                ['like', 'item_id', explode(' ',trim($this->item_id))],
                ['like', 'group_id', explode(' ',trim($this->group_id))]
            ]);

        return $dataProvider;
    }
}
