<?php

namespace app\modules\questionnaire\models;

use Yii;

/**
 * This is the model class for table "questionnaire_questions".
 *
 * @property integer $id
 * @property string $question
 * @property integer $active
 *
 * @property QuestionnaireAnswers[] $questionnaireAnswers
 */
class QuestionnaireQuestions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questionnaire_questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question'], 'string'],
            [['active'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Вопрос',
            'active' => 'Активно',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionnaireAnswers()
    {
        return $this->hasMany(QuestionnaireAnswers::className(), ['question_id' => 'id']);
    }
}
