<?php

namespace app\modules\managment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\managment\models\ShopsCallback;

/**
 * ShopsCallbackSearch represents the model behind the search form of `app\modules\managment\models\ShopsCallback`.
 */
class ShopsCallbackSearch extends ShopsCallback
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'shop_id', 'status'], 'integer'],
            [['action', 'date', 'comment', 'phone', 'contact'], 'safe'],
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
        $query = ShopsCallback::find();

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
            'shop_id' => $this->shop_id,
            'date' => $this->date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'contact', $this->contact]);

        return $dataProvider;
    }
}
