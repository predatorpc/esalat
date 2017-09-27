<?php

namespace app\modules\shop\models;

use app\modules\catalog\models\GoodsTypes;
use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\Address;
use app\modules\common\models\Deliveries;
use app\modules\common\models\UsersAddress;
use Yii;

/**
 * This is the model class for table "orders_groups".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $delivery_id
 * @property string $delivery_date
 * @property string $delivery_price
 * @property integer $store_id
 * @property integer $type_id
 * @property integer $address_id
 * @property integer $status
 */
class OrdersGroups extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'delivery_id', 'store_id', 'address_id', 'status','type_id'], 'integer'],
            [['delivery_date'], 'safe'],
            [['delivery_price'], 'number']
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
            'delivery_id' => 'Delivery ID',
            'delivery_date' => 'Delivery Date',
            'delivery_price' => 'Delivery Price',
            'store_id' => 'Store ID',
            'address_id' => 'Address ID',
            'status' => 'Status',
        ];
    }

    public function getorders()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    public function getOrdersItems()
    {
        return $this->hasMany(OrdersItems::className(), ['order_group_id' => 'id']);
    }

    public function getType()
    {
        return $this->hasOne(GoodsTypes::className(), ['id' => 'type_id']);
    }

    public function getDeliveries(){
        return $this->hasOne(Deliveries::className(), ['id' => 'delivery_id']);
    }

    public function getUsers_address(){
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }

}
