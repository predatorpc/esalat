<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "basket".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $session_id
 * @property integer $date_create
 * @property integer $last_update
 * @property integer $delivery_id
 * @property string $delivery_price
 * @property integer $address_id
 * @property integer $payment_id
 * @property integer $promo_code_id
 * @property string $time_list
 * @property string $comment
 * @property integer $status
 */
class BasketTest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'basket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date_create', 'last_update', 'delivery_id', 'address_id', 'payment_id', 'promo_code_id', 'status'], 'integer'],
            [['session_id', 'date_create', 'last_update', 'delivery_id', 'address_id', 'payment_id', 'comment', 'status'], 'required'],
            [['delivery_price'], 'number'],
            [['comment'], 'string'],
            [['session_id'], 'string', 'max' => 64],
            [['time_list'], 'string', 'max' => 255],
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
            'comment' => 'Comment',
            'status' => 'Status',
        ];
    }
}
