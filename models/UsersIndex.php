<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_index".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $rate
 * @property integer $date
 */
class UsersIndex extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_index';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'rate', 'date'], 'integer'],
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
            'rate' => 'Rate',
            'date' => 'Date',
        ];
    }
}
