<?php

namespace app\modules\common\models;

use app\modules\shop\models\Orders;
use Yii;

/**
 * This is the model class for table "users_pays".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $type
 * @property string $money
 * @property string $comments
 * @property string $date
 * @property integer $status
 * @property integer $transaction_id
 * @property integer $error_code
 *
 * @property Orders $order
 * @property Users $user
 */
class UsersPays extends \yii\db\ActiveRecord
{
    public $summ;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_pays';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'money', 'type', 'status', 'comments'], 'required'],
            [['user_id', 'created_user_id', 'order_id', 'type', 'status','type_id',], 'integer'],
            [['money'], 'number'],
            [['comments', 'transaction_id', 'error_code'], 'string'],
            [['date'], 'safe'],
//            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
//            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserShop::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'order_id' => 'ID Заказа',
            //'basket_id' => 'ID корзины',
            'type' => 'Тип',
            'money' => 'Деньги',
            'comments' => 'Комментарий',
            'date' => 'Дата',
            'status' => 'Статус',
            'type_id' => 'ID Тип',
            'transaction_id' => 'ID транзакции PayOnline'
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

    public function getCreatorUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_user_id']);
    }




}
