<?php

namespace app\modules\questionnaire\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\questionnaire\models\QuestionnaireAnswers;

/**
 * QuestionnaireAnswersSearch represents the model behind the search form about `app\modules\questionnaire\models\QuestionnaireAnswers`.
 */
class QuestionnaireAnswersSearch extends QuestionnaireAnswers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'question_id', 'user_id', 'viewed', 'basket_id'], 'integer'],
            [['answer', 'date'], 'safe'],
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
        $query = QuestionnaireAnswers::find();

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
            'question_id' => $this->question_id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'viewed' => $this->viewed,
            'basket_id' => $this->basket_id,
        ]);

        $query->andFilterWhere(['like', 'answer', $this->answer]);

        return $dataProvider;
    }
}
