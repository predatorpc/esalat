<?php

namespace app\modules\common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "users_sms_logs".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $phone
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 */
class UsersSmsLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_sms_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'phone', 'text'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['phone'], 'string', 'max' => 12],
            [['text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'user_id' => Yii::t('admin', 'User ID'),
            'phone' => Yii::t('admin', 'Phone'),
            'text' => Yii::t('admin', 'Text'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
        ];
    }
}
