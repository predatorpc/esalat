<?php

namespace app\modules\crm\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\crm\models\CrmTasks;

/**
 * CrmTasksSearch represents the model behind the search form of `app\modules\crm\models\CrmTasks`.
 */
class CrmTasksSearch extends CrmTasks
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'creator', 'slave', 'name', 'progress','priority'], 'integer'],
            [['date_create', 'description', 'start', 'deadline','department'], 'safe'],
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
        $query = CrmTasks::find();

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
            'date_create' => $this->date_create,
            'creator' => $this->creator,
            'slave' => $this->slave,
            'name' => $this->name,
            'progress' => $this->progress,
            'start' => $this->start,
            'deadline' => $this->deadline,
            'department' => $this->department,
            'priority' => $this->priority,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
