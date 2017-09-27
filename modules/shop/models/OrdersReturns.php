<?php

namespace app\modules\shop\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "orders_returns".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $good_id
 * @property integer $count
 * @property string $comments
 * @property string $user_name
 * @property string $return
 * @property integer $status
 */
class OrdersReturns extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_returns';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'good_id', 'count', 'status'], 'integer'],
            [['comments'], 'required'],
            [['comments'], 'string'],
            [['return'], 'safe'],
            [['user_name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'good_id' => 'Good ID',
            'count' => 'Count',
            'comments' => 'Comments',
            'user_name' => 'User Name',
            'return' => 'Return',
            'status' => 'Status',
        ];
    }
}
