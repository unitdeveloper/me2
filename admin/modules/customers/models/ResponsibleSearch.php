<?php

namespace admin\modules\customers\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SalesPeople;

/**
 * ResponsibleSearch represents the model behind the search form of `common\models\SalesPeople`.
 */
class ResponsibleSearch extends SalesPeople
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'comp_id'], 'integer'],
            [['code', 'prefix', 'cname', 'name', 'gender', 'surname', 'nickname', 'sale_group', 'tax_id', 'position', 'address', 'address2', 'postcode', 'date_added', 'sign_img', 'status', 'photo', 'wall_photo', 'mobile_phone', 'line_id'], 'safe'],
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
        $query = SalesPeople::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50]
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
            'comp_id' => $this->comp_id,
            'date_added' => $this->date_added,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'cname', $this->cname])
            ->andFilterWhere(['or',
                ['like', 'name', $this->name],
                ['like', 'surname', $this->name]
            ])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'sale_group', $this->sale_group])
            ->andFilterWhere(['like', 'tax_id', $this->tax_id])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'address2', $this->address2])
            ->andFilterWhere(['like', 'postcode', $this->postcode])
            ->andFilterWhere(['like', 'sign_img', $this->sign_img])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'wall_photo', $this->wall_photo])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'line_id', $this->line_id]);

        return $dataProvider;
    }
}
