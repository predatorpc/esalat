<?php

namespace app\modules\actions\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\actions\models\ActionsAccumulation;


/**
 * ActionsAccumulationSearch represents the model behind the search form about `app\modules\actions\models\ActionsAccumulation`.
 */
class ActionsAccumulationSearch extends ActionsAccumulation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'order_id', 'product_id', 'current_value', 'currency_id', 'action_id', 'action_param_value_id', 'active', 'status'], 'integer'],
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
        $query = ActionsAccumulation::find()
            ->select('action_id, action_param_value_id, user_id, Sum(current_value) as `current_value`, count(id) as `count_row`, sum(active) as `active_row`')
            ->where(['status'=>1])
            ->groupBy('action_id, action_param_value_id, user_id')
            ->with('user')
            ->with('action')
            ->with('paramName');

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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'current_value' => $this->current_value,
            'currency_id' => $this->currency_id,
            'action_id' => $this->action_id,
            'action_param_value_id' => $this->action_param_value_id,
            'active' => $this->active,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
