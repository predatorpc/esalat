<?php

namespace app\modules\common\models;

use Yii;
use app\modules\common\models\User;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property integer $type_id
 * @property integer $group_id
 * @property integer $user_id
 * @property string $name
 * @property string $topic
 * @property string $order
 * @property string $phone
 * @property string $text
 * @property string $answer
 * @property string $date
 * @property integer $show
 * @property integer $status
 *
 * @property MessagesTypes $type
 * @property Users $user
 * @property Messages $group
 * @property Messages[] $messages
 * @property MessagesImages[] $messagesImages
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'group_id', 'user_id', 'show', 'status'], 'integer'],
            [['text', 'answer'], 'required'],
            [['text', 'answer'], 'string'],
            [['date'], 'safe'],
            [['name', 'topic'], 'string', 'max' => 128],
            [['order'], 'string', 'max' => 16],
            [['phone'], 'string', 'max' => 12],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MessagesTypes::className(), 'targetAttribute' => ['type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Messages::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'topic' => 'Topic',
            'order' => 'Order',
            'phone' => 'Phone',
            'text' => 'Text',
            'answer' => 'Answer',
            'date' => 'Date',
            'show' => 'Show',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(MessagesTypes::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Messages::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesImages()
    {
        return $this->hasMany(MessagesImages::className(), ['message_id' => 'id']);
    }
}
