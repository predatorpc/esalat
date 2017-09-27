<?php

namespace app\modules\managment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShopsSearch represents the model behind the search form about `app\models\Shops`.
 */
class ShopsSearch extends Shops
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'delay', 'comission_id', 'count', 'show', 'notice', 'user_id', 'status'], 'integer'],
            [['name', 'name_full', 'contract', 'tax_number', 'description', 'phone', 'delivery_delay', 'registration'], 'safe'],
            [['min_order', 'comission_value'], 'number'],
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
        $query = Shops::find();

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
            'type_id' => $this->type_id,
            'min_order' => $this->min_order,
            'delivery_delay' => $this->delivery_delay,
            'delay' => $this->delay,
            'comission_id' => $this->comission_id,
            'comission_value' => $this->comission_value,
            'count' => $this->count,
            'show' => $this->show,
            'notice' => $this->notice,
            'user_id' => $this->user_id,
            'registration' => $this->registration,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name_full', $this->name_full])
            ->andFilterWhere(['like', 'contract', $this->contract])
            ->andFilterWhere(['like', 'tax_number', $this->tax_number])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
