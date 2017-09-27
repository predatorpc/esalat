<?php

namespace app\modules\actions\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\actions\models\Actions;

/**
 * ActionsSearch represents the model behind the search form about `app\modules\actions\models\Actions`.
 */
class ActionsSearch extends Actions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'periodic', 'date_start', 'date_end', 'priority', 'block', 'accumulation', 'notification_type', 'alerts_place', 'created_at', 'updated_at', 'created_user', 'updated_user', 'status'], 'integer'],
            [['title', 'description', 'file_type'], 'safe'],
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
        $query = Actions::find();

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
            'periodic' => $this->periodic,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'priority' => $this->priority,
            'block' => $this->block,
            'accumulation' => $this->accumulation,
            'notification_type' => $this->notification_type,
            'alerts_place' => $this->alerts_place,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_user' => $this->created_user,
            'updated_user' => $this->updated_user,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'file_type', $this->file_type]);

        return $dataProvider;
    }
}
