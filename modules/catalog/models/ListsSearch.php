<?php

namespace app\modules\catalog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\catalog\models\Lists;
/**
 * ListsSearch represents the model behind the search form about `app\models\Lists`.
 */
class ListsSearch extends Lists
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'show_banners', 'position', 'change', 'list_type', 'level', 'status'], 'integer'],
            [['title', 'description', 'image', 'date_create', 'date_update'], 'safe'],
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
        $query = Lists::find();
         //   ->joinWith('variation');

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
        //    'variation.full_name' => $this->variation,
            'user_id' => $this->user_id,
            'show_banners' => $this->show_banners,
            'position' => $this->position,
            'change' => $this->change,
            'list_type' => $this->list_type,
            'level' => $this->level,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image', $this->image]);
      //      ->andFilterWhere(['like', 'variation.full_name', $this->variation]);

        return $dataProvider;
    }
}
