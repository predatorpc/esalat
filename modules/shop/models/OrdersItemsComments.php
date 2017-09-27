<?php

namespace app\modules\shop\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "orders_items_comments".
 *
 * @property integer $id
 * @property integer $order_item_id
 * @property integer $user_id
 * @property string $date
 * @property integer $status
 */
class OrdersItemsComments extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_items_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_item_id', 'user_id', 'status'], 'integer'],
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
            'order_item_id' => 'Order Item ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
