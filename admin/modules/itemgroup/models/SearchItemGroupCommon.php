<?php

namespace admin\modules\itemgroup\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ItemgroupCommon;

/**
 * SearchItemGroup represents the model behind the search form about `common\models\ItemgroupCommon`.
 */
class SearchItemGroupCommon extends ItemgroupCommon
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name','description', 'child', 'status'], 'safe'],
            [['sequent','photo'],'string'],
            [['comp_id'], 'integer'],
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
        $query = ItemgroupCommon::find()
        ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here
       
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['sequent' => SORT_ASC]]
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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like','description',$this->description])
            ->andFilterWhere(['like', 'child', $this->child])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
