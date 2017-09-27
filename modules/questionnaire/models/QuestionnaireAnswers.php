<?php

namespace app\modules\questionnaire\models;

use Yii;

/**
 * This is the model class for table "questionnaire_answers".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $user_id
 * @property string $answer
 * @property string $date
 * @property integer $viewed
 * @property integer $basket_id
 *
 * @property QuestionnaireQuestions $question
 * @property Users $user
 */
class QuestionnaireAnswers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questionnaire_answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'user_id', 'date'], 'required'],
            [['question_id', 'user_id', 'viewed', 'basket_id'], 'integer'],
            [['answer'], 'string'],
            [['date'], 'safe'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionnaireQuestions::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Вопрос',
            'user_id' => 'Пользователь',
            'answer' => 'Ответ',
            'date' => 'Дата',
            'viewed' => 'Ответ из',
            'basket_id' => 'Basket ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(QuestionnaireQuestions::className(), ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
