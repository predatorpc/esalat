<?php

namespace app\modules\my\models;

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
            [['exp'], 'string', 'max' => 30],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::className(),'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'message_id' => Yii::t('app', 'Message ID'),
            'hash' => Yii::t('app', 'Hash'),
            'date' => Yii::t('app', 'Date'),
            'exp' => Yii::t('app', 'Exp'),
            'position' => Yii::t('app', 'Position'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['id' => 'message_id']);
    }
}
