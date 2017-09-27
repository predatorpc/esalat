<?php

namespace app\modules\crm\models;

use Yii;

/**
 * This is the model class for table "crm_tasks_comments".
 *
 * @property int $id
 * @property int $task_id
 * @property string $date
 * @property int $user_id
 * @property string $text
 */
class CrmTasksComments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crm_tasks_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'user_id','read'], 'integer'],
            [['date'], 'safe'],
            [['text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'date' => 'Date',
            'user_id' => 'User ID',
            'text' => 'Text',
        ];
    }
}
