<?php

namespace admin\modules\apps_rules\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppsRules;

/**
 * SearchRules represents the model behind the search form about `common\models\AppsRules`.
 */
class SearchRules extends AppsRules
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'permission_id', 'comp_id', 'rules_id'], 'integer'],
            [['date_created', 'sales_people'], 'safe'],
            [['sales_id','name'],'string'],
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
        $query = AppsRules::find();

        if(Yii::$app->user->identity->id!=1)
        {
            $query->where(['comp_id' => Yii::$app->session->get('myCompany')]);
            $query->andWhere(['<>','id',1]);
        }
        
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
            'user_id' => $this->user_id,
            'permission_id' => $this->permission_id,
            'comp_id' => $this->comp_id,
            'date_created' => $this->date_created,
            'rules_id' => $this->rules_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
