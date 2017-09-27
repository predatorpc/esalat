<?php

namespace app\models;

use app\modules\common\models\User;
use Yii;

/**
 * This is the model class for table "users_credits".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $amount
 * @property integer $status
 */
class UsersCredits extends \yii\db\ActiveRecord
{
    public $userName='';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_credits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'status'], 'required'],
            [['user_id', 'amount', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'userName' => Yii::t('app', 'Username'),
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }
}
