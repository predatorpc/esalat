<?php

namespace app\modules\basket\models;

use app\modules\common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "basket".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $session_id
 * @property integer $last_update
 * @property integer $delivery_id
 * @property integer $delivery_price
 * @property integer $address_id
 * @property integer $payment_id
 * @property integer $promo_code_id
 * @property string $time_list
 * @property integer $status
 */
class ShopAction extends \yii\db\ActiveRecord
{
    public $bonus = 0;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'last_update',
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'delivery_id', 'address_id', 'payment_id', 'status'], 'required'],
            [['delivery_price'],'number'],
            [['created_at', 'updated_at', 'created_user', 'updated_user', 'status'], 'integer'],
            [['title'], 'string', 'max' => 254],
            [['time_list'], 'string', 'max' => 254],
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
            'session_id' => 'Session ID',
            'date_create' => 'Date Create',
            'last_update' => 'Last Update',
            'delivery_id' => 'Delivery ID',
            'delivery_price' => 'Delivery Price',
            'address_id' => 'Address ID',
            'payment_id' => 'Payment ID',
            'promo_code_id' => 'Promo Code ID',
            'time_list' => 'Time List',
            'status' => 'Status',
        ];
    }
}
