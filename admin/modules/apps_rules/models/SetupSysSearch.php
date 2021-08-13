<?php

namespace admin\modules\apps_rules\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SetupSysMenu;

/**
 * SetupSysSearch represents the model behind the search form about `common\models\SetupSysMenu`.
 */
class SetupSysSearch extends SetupSysMenu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[  'comp_id'], 'integer'],
            [[
                'description', 
                'rules_id',
                'function_group_type',
                'function_group',
                'function_name',
                'function_modules',
                'function_controllers',
                'function_models',
                'detail'
            ], 'safe'],
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
        $query = SetupSysMenu::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            //'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'rules_id', $this->rules_id])
            ->andFilterWhere(['like', 'function_name', $this->function_name])
            ->andFilterWhere(['like', 'function_modules', $this->function_modules])
            ->andFilterWhere(['like', 'function_controllers', $this->function_controllers])
            ->andFilterWhere(['like', 'function_models', $this->function_models])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'function_group_type', $this->function_group_type]);

        return $dataProvider;
    }
}
