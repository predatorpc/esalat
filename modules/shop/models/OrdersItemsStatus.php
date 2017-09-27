<?php

namespace app\modules\shop\models;

use app\modules\common\models\ActiveRecordRelation;
use Yii;

/**
 * This is the model class for table "orders_items_status".
 *
 * @property integer $id
 * @property integer $order_item_id
 * @property integer $status_id
 * @property integer $user_id
 * @property string $date
 * @property integer $status
 */
class OrdersItemsStatus extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_items_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_item_id', 'status_id', 'user_id', 'status'], 'integer'],
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
            'status_id' => 'Status ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    public function getUser(){
        return \app\modules\common\models\User::findOne($this->user_id);
    }

    public function getStatusTitle(){
        return OrdersStatus::findOne($this->status_id);
    }
}
