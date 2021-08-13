<?php

namespace admin\modules\itemgroup\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Itemgroup;

/**
 * SearchItemGroup represents the model behind the search form about `common\models\Itemgroup`.
 */
class SearchItemGroup extends Itemgroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['GroupID'], 'integer'],
            [['Description','Description_th', 'Child', 'Status'], 'safe'],
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
        $query = Itemgroup::find()
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
            'GroupID' => $this->GroupID,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like','Description_th',$this->Description_th])
            ->andFilterWhere(['like', 'Child', $this->Child])
            ->andFilterWhere(['like', 'Status', $this->Status]);

        return $dataProvider;
    }
}
