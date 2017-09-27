<?php

namespace app\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\catalog\models\CodesTypes;

/**
 * CodesTypesSearch represents the model behind the search form about `app\modules\catalog\models\CodesTypes`.
 */
class CodesTypesSearch extends CodesTypes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'max_sum_fee', 'show', 'status', 'shop_id', 'money_discount'], 'integer'],
            [['name', 'period_days'], 'safe'],
            [['discount', 'fee', 'discount_sport', 'fee_sport'], 'number'],
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
        $query = CodesTypes::find();

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
            'discount' => $this->discount,
            'fee' => $this->fee,
            'discount_sport' => $this->discount_sport,
            'fee_sport' => $this->fee_sport,
            'max_sum_fee' => $this->max_sum_fee,
            'show' => $this->show,
            'status' => $this->status,
            'shop_id' => $this->shop_id,
            'money_discount' => $this->money_discount,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'period_days', $this->period_days]);

        return $dataProvider;
    }
}
