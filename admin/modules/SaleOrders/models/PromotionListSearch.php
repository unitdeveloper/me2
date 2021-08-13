<?php

namespace admin\modules\SaleOrders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PromotionsItemGroup;

/**
 * PromotionGroupSearch represents the model behind the search form of `common\models\PromotionsItemGroup`.
 */
class PromotionListSearch extends PromotionsItemGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'items', 'comp_id'], 'integer'],
            [['name', 'description'], 'safe'],
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
        $query = PromotionsItemGroup::find()
        ->where('comp_id = :comp')
        ->addParams([':comp' => Yii::$app->session->get('Rules')['comp_id']]);
        //->andWhere(['name' => Yii::$app->request->get('name')]);

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
            //'items' => $this->items,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
