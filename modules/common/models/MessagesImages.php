<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "messages_images".
 *
 * @property integer $id
 * @property integer $message_id
 * @property string $hash
 * @property string $date
 * @property integer $position
 * @property integer $status
 *
 * @property Messages $message
 */
class MessagesImages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages_images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'position', 'status'], 'integer'],
            [['date'], 'safe'],
            [['hash'], 'string', 'max' => 32],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Messages::className(), 'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_id' => 'Message ID',
            'hash' => 'Hash',
            'date' => 'Date',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Messages::className(), ['id' => 'message_id']);
    }
}
