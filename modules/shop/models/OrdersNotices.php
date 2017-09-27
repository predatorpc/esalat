<?php

namespace app\modules\shop\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "orders_notices".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $order_id
 * @property integer $user_id
 * @property string $phone
 * @property string $email
 * @property string $date
 * @property integer $status
 */
class OrdersNotices extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_notices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'order_id', 'user_id', 'status'], 'integer'],
            [['date'], 'safe'],
            [['phone'], 'string', 'max' => 16],
            [['email'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'email' => 'Email',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
