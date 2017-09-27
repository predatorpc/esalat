<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "users_bonus".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $type
 * @property string $bonus
 * @property string $date
 * @property string comments
 * @property integer $status
 */
class UsersBonus extends ActiveRecordRelation
{
    public $summ;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'status', 'bonus'], 'required'],
            [['user_id', 'created_user_id', 'order_id', 'type', 'status','type_id'], 'integer'],
            [['bonus'], 'number'],
            [['comments'], 'string'],
            [['date', 'order_id'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('admin','Пользователь'),
            'type' => Yii::t('admin','Тип'),
            'bonus' => Yii::t('admin','Бонус'),
            'date' => Yii::t('admin','Дата'),
            'status' => Yii::t('admin','Статус'),
            'type_id' => Yii::t('admin','ID Тип'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserPhone()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
