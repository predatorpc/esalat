<?php

namespace app\modules\common\models;

use Yii;
use app\modules\common\models\User;



/**
 * This is the model class for table "users_roles".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $shop_id
 * @property integer $main
 * @property string $date
 * @property integer $status
 *
 * @property Shops $shop
 * @property Users $user
 */
class UserRoles extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'shop_id', 'main', 'status'], 'integer'],
            [['date'], 'safe']
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
            'shop_id' => 'Shop ID',
            'main' => 'Main',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shops::className(), ['id' => 'shop_id']);
    }

    public function getRoles()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id']);
    }

    /**
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
