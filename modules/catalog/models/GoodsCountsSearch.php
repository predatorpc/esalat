<?php

namespace app\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\catalog\models\GoodsCounts;

/**
 * GoodsCountsSearch represents the model behind the search form about `app\modules\catalog\models\GoodsCounts`.
 */
class GoodsCountsSearch extends GoodsCounts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'good_id', 'variation_id', 'store_id', 'count', 'status'], 'integer'],
            [['update'], 'safe'],
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
        $query = GoodsCounts::find();

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
            'good_id' => $this->good_id,
            'variation_id' => $this->variation_id,
            'store_id' => $this->store_id,
            'count' => $this->count,
            'update' => $this->update,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
