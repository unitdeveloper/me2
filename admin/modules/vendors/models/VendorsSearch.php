<?php

namespace admin\modules\vendors\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Vendors;

/**
 * VendorsSearch represents the model behind the search form about `common\models\Vendors`.
 */
class VendorsSearch extends Vendors
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'district', 'user_id', 'comp_id'], 'integer'],
            [['name', 'address', 'address2', 'city', 'province', 'postcode', 'country', 'phone', 'fax', 'contact', 'vendor_posting_group', 'vatbus_posting_group', 'email', 'homepage', 'headoffice','Search'], 'safe'],
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
        $query = Vendors::find()
        ->where(['or',
            ['comp_id' => Yii::$app->session->get('Rules')['comp_id']],
            ['id' => 1]
        ]);

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
            'code' => $this->code,
            'user_id' => $this->user_id,
            'comp_id' => $this->comp_id,
        ]);

        $query->andFilterWhere(['or',
            ['like', 'name', explode(' ',$this->Search)],
            ['like', 'address', explode(' ',$this->Search)],
            ['like', 'province', explode(' ',$this->Search)],
            ['like', 'code', explode(' ',$this->Search)]
        ]);

        // $query->andFilterWhere(['like', 'name', $this->name])
        //     ->andFilterWhere(['like', 'address', $this->address])
        //     ->andFilterWhere(['like', 'address2', $this->address2])
        //     ->andFilterWhere(['like', 'city', $this->city])
        //     ->andFilterWhere(['like', 'province', $this->province])
        //     ->andFilterWhere(['like', 'postcode', $this->postcode])
        //     ->andFilterWhere(['like', 'country', $this->country])
        //     ->andFilterWhere(['like', 'phone', $this->phone])
        //     ->andFilterWhere(['like', 'fax', $this->fax])
        //     ->andFilterWhere(['like', 'contact', $this->contact])
        //     ->andFilterWhere(['like', 'vendor_posting_group', $this->vendor_posting_group])
        //     ->andFilterWhere(['like', 'vatbus_posting_group', $this->vatbus_posting_group])
        //     ->andFilterWhere(['like', 'email', $this->email])
        //     ->andFilterWhere(['like', 'homepage', $this->homepage])
        //     ->andFilterWhere(['like', 'headoffice', $this->headoffice]);

        return $dataProvider;
    }
}
