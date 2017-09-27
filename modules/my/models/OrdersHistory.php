<?php

namespace app\modules\my\models;

use app\modules\common\models\Zloradnij;
use Yii;
use app\modules\catalog\models\GoodsVariations;
/**
 * This is the model class for table "orders_items".
 *
 * @property integer $id
 * @property integer $order_group_id
 * @property integer $good_id
 * @property integer $variation_id
 * @property integer $store_id
 * @property string $time
 * @property string $comission
 * @property string $price
 * @property string $discount
 * @property integer $count
 * @property string $fee
 * @property string $bonus
 * @property string $comments
 * @property string $comments_shop
 * @property string $comments_call_center
 * @property integer $seller_status_id_
 * @property string $receive
 * @property string $user_name
 * @property string $release
 * @property integer $status_id
 * @property integer $status
 *
 * @property Goods $good
 * @property OrdersGroups $orderGroup
 * @property OrdersStatus $status0
 * @property GoodsVariations $variation
 * @property OrdersItemsComments[] $ordersItemsComments
 * @property OrdersItemsStatus[] $ordersItemsStatuses
 */
class OrdersHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_group_id', 'good_id', 'variation_id', 'store_id', 'count', 'seller_status_id_', 'status_id', 'status'], 'integer'],
            [['time', 'receive', 'release'], 'safe'],
            [['comission', 'price', 'discount', 'fee', 'bonus'], 'number'],
            [['comments', 'comments_shop'], 'required'],
            [['comments', 'comments_shop', 'comments_call_center'], 'string'],
            [['user_name'], 'string', 'max' => 64],
            [['good_id'], 'exist', 'skipOnError' => true, 'targetClass' => Goods::className(), 'targetAttribute' => ['good_id' => 'id']],
            [['order_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdersGroups::className(), 'targetAttribute' => ['order_group_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdersStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['variation_id'], 'exist', 'skipOnError' => true, 'targetClass' => GoodsVariations::className(), 'targetAttribute' => ['variation_id' => 'id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_group_id' => 'Order Group ID',
            'good_id' => 'Good ID',
            'variation_id' => 'Variation ID',
            'store_id' => 'Store ID',
            'time' => 'Time',
            'comission' => 'Comission',
            'price' => 'Price',
            'discount' => 'Discount',
            'count' => 'Count',
            'fee' => 'Fee',
            'bonus' => 'Bonus',
            'comments' => 'Comments',
            'comments_shop' => 'Comments Shop',
            'comments_call_center' => 'Comments Call Center',
            'seller_status_id_' => 'Seller Status ID',
            'receive' => 'Receive',
            'user_name' => 'User Name',
            'release' => 'Release',
            'status_id' => 'Status ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGood()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGroup()
    {
        return $this->hasOne(OrdersGroups::className(), ['id' => 'order_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(OrdersStatus::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariation()
    {
        return $this->hasOne(GoodsVariations::className(), ['id' => 'variation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItemsComments()
    {
        return $this->hasMany(OrdersItemsComments::className(), ['order_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersItemsStatuses()
    {
        return $this->hasMany(OrdersItemsStatus::className(), ['order_item_id' => 'id']);
    }

    /*
       Модель История заказов;
    */
    public static function findOrdersHistory()
    {
        //  Загрузка история заказов;
        if($orders = OrdersHistory::find()->select([
            'address.street as address','address.house','address.room','orders.id as order_id','orders.date','goods.id as good_id',
            'goods.name as good_name','orders_items.variation_id as variation_id','get_tags(`orders_items`.`variation_id`) AS tags',
            'goods.order','goods.status as status_good','orders_items.price','orders_items.discount','orders_items.count','orders_items.bonus'
            ,'orders_groups.delivery_date','deliveries.name as delivery_name','orders_items.time','orders_items.user_name',
            'orders_items.release','orders_items.status','orders_items.status as status_g','orders_status.name as status_name',
            'goods.confirm as good_confirm','goods.show as good_show','orders_groups.delivery_price','orders_groups.delivery_id',
            'orders_groups.type_id as order_type_id','goods.type_id as goods_type_id'
        ])->leftJoin('orders_groups','orders_groups.id = orders_items.order_group_id')
            ->leftJoin('deliveries','deliveries.id = orders_groups.delivery_id')
            ->leftJoin('orders','orders.id = orders_groups.order_id')
            ->leftJoin('goods','goods.id = orders_items.good_id')
            ->leftJoin('goods_types','goods_types.id = goods.type_id')
            ->leftJoin('address','address.id = orders_groups.address_id')
            ->leftJoin('orders_status','orders_status.id = orders_items.status_id')
            ->where([
                'orders.user_id'=>  Yii::$app->user->identity->id,
                'orders_groups.status'=> 1,
                'orders.status'=> 1,
            ])->orderBy(['orders.date' => SORT_DESC, 'orders_items.status'=>SORT_DESC, 'goods.name'=>SORT_ASC, 'orders_items.id'=> SORT_DESC])->groupBy('orders.id')->asArray()->all()) {

            foreach ($orders as $key => $value) {
                $orders[$key]['money'] = 0;

                $orders[$key]['good_count'] = OrdersHistory::find()->select(['count'])->from('goods_counts')
                    ->where(['good_id' => $value['good_id'],'status' => 1])->scalar();


                $orders[$key]['variation_status'] = OrdersHistory::find()->select(['status'])->from('goods_variations')
                    ->where(['good_id' => $value['good_id'],'status' => 1])->limit(1)->scalar();


                //$orders[$key]['count_max'] = GoodsVariations::getMaxCount();
                // print_arr($orders);
                // Проверка статуса товара;
                if ($value['status']) {
                    // Проверка выдачи товара;
                    if ($value['release']) {
                        // Обработка оператора выдачи товара;
                        $orders[$key]['status'] = 'Выдан: ' . date("d.m.Y, H:i", strtotime($value['release']));
                    } else {
                        $strToTimeDelivery = strtotime($value['delivery_date']);
                        // Обработка статуса;
                        if (($value['order_type_id'] == 1001 || $value['order_type_id'] == 1005 || $value['order_type_id'] == 1010) && $value['delivery_id'] == 1003) {
                            $strToTimeDelivery = $strToTimeDelivery + 3600 * 24;
                        }
                        if ($value['order_type_id'] == 1009) {
                            $strToTimeDelivery = $strToTimeDelivery + 3600 * 24;
                        }
                        if ($value['order_type_id'] == 1010) {
                            $strToTimeDelivery = $strToTimeDelivery + 3600 * 24;
                        }
                        $orders[$key]['status'] = 'Выдача: ' . date("d.m.Y, H:i", $strToTimeDelivery) . ' – ' . date("H:i", $strToTimeDelivery + 7200);
                        if ($value['goods_type_id'] == 1008) {
                            $orders[$key]['status'] = 'Выдача: в течение 4-10 дней';
                        }
                    }
                } else {
                    // Обработка отмены товара;
                    $orders[$key]['status'] = 'Отменен';
                }
                // Рассчет стоимости;
                $orders[$key]['money'] = ($value['price'] - $value['discount']) * $value['count'];
                $orders[$key]['total'] = $orders[$key]['delivery_price'];
                // Загрузка погруппы товара;
                if ($orders[$key]['group_orders'] = OrdersHistory::find()->select([
                    'address.street as address', 'address.house', 'address.room', 'orders.id as order_id', 'orders.date', 'goods.id as good_id',
                    'goods.name as good_name', 'orders_items.variation_id as variation_id', 'get_tags(`orders_items`.`variation_id`) AS tags',
                    'goods.order', 'goods.status as status_good', 'orders_items.price', 'orders_items.discount', 'orders_items.count', 'orders_items.bonus'
                    , 'orders_groups.delivery_date', 'deliveries.name as delivery_name', 'orders_items.time', 'orders_items.user_name',
                    'orders_items.release', 'orders_items.status', 'orders_items.status as status_g', 'orders_status.name as status_name',
                    'goods.confirm as good_confirm', 'goods.show as good_show', 'orders_groups.delivery_price', 'orders_groups.delivery_id',
                    'orders_groups.type_id as order_type_id', 'goods.type_id as goods_type_id'
                ])->leftJoin('orders_groups', 'orders_groups.id = orders_items.order_group_id')
                    ->leftJoin('deliveries', 'deliveries.id = orders_groups.delivery_id')
                    ->leftJoin('orders', 'orders.id = orders_groups.order_id')
                    ->leftJoin('goods', 'goods.id = orders_items.good_id')
                    ->leftJoin('goods_types', 'goods_types.id = goods.type_id')
                    ->leftJoin('address', 'address.id = orders_groups.address_id')
                    ->leftJoin('orders_status', 'orders_status.id = orders_items.status_id')
                    ->where([
                        'orders.user_id' => Yii::$app->user->identity->id,
                        'orders.id' => $orders[$key]['order_id'],
                        'orders_groups.status' => 1,
                        'orders.status' => 1,
                    ])->orderBy(['orders.date' => SORT_DESC, 'orders_items.status'=>SORT_DESC, 'goods.name'=>SORT_ASC, 'orders_items.id'=> SORT_DESC])->asArray()->all()
                ) {
                    foreach ($orders[$key]['group_orders'] as $k => $v) {
                        //Zloradnij::print_arr($v);
                        //TEST

                        $orders[$key]['group_orders'][$k]['money'] = 0;
                        $orders[$key]['group_orders'][$k]['variation_status'] = OrdersHistory::find()->select(['status'])->from('goods_variations')
                            ->where(['good_id' => $v['good_id'],'status' => 1])->limit(1)->scalar();
                        $orders[$key]['group_orders'][$k]['good_count'] = OrdersHistory::find()->select(['count'])->from('goods_counts')
                            ->where(['good_id' => $value['good_id'],'status' => 1])->scalar();
                        // Проверка статуса товара;
                        if ($v['status']) {
                            // Проверка выдачи товара;
                            if ($v['release']) {
                                // Обработка оператора выдачи товара;
                                $orders[$key]['group_orders'][$k]['status'] = 'Выдан: ' . date("d.m.Y, H:i", strtotime($v['release']));
                            } else {
                                $strToTimeDelivery = strtotime($v['delivery_date']);

                                // Обработка статуса;
                                if (($v['order_type_id'] == 1001 || $v['order_type_id'] == 1005) && $v['delivery_id'] == 1003) {
                                    if ($strToTimeDelivery >= strtotime('25-03-2016 12:00:00')) {
                                        $strToTimeDelivery = $strToTimeDelivery + 3600 * 24;
                                    }
                                }
                                $orders[$key]['group_orders'][$k]['status'] = 'Выдача: ' . date("d.m.Y, H:i", $strToTimeDelivery) . ' – ' . date("H:i", $strToTimeDelivery + 7200);
                            }
                        } else {
                            // Обработка отмены товара;
                            $orders[$key]['group_orders'][$k]['status'] = 'Отменен';
                        }
                        // Рассчет стоимости;
                        $orders[$key]['group_orders'][$k]['money'] = ($v['price'] - $v['discount']) * $v['count'];
                        $orders[$key]['total'] += $orders[$key]['group_orders'][$k]['money'];
                        if ($v['good_id'] == $value['good_id']) unset($orders[$key]['group_orders'][$k]);
                    }

                }
            }
        }


        return $orders;
    }
}
