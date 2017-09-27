<?php

namespace app\modules\coders\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\coders\models\Logs;

/**
 * LogsSearch represents the model behind the search form about `app\models\Logs`.
 */
class LogsSearch extends Logs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'shop_id', 'store_id', 'good_id', 'variation_id', 'category_id'], 'integer'],
            [['time', 'action', 'sql'], 'safe'],
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
        $query = Logs::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'time' => $this->time,
            'user_id' => $this->user_id,
            'shop_id' => $this->shop_id,
            'store_id' => $this->store_id,
            'good_id' => $this->good_id,
            'variation_id' => $this->variation_id,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'sql', $this->sql]);

        return $dataProvider;
    }
}
