<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "users_cards".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $card_number
 * @property string $rebill_anchor
 * @property string $date
 * @property integer $status
 *
 * @property Users $user
 */
class UsersCards extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_cards';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['date'], 'safe'],
            [['card_number'], 'string', 'max' => 20],
            [['rebill_anchor'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'card_number' => 'Card Number',
            'rebill_anchor' => 'Rebill Anchor',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
