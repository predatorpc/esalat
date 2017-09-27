<?php

namespace app\modules\shop\models;

use app\modules\catalog\models\Category;
use app\modules\catalog\models\CategoryLinks;
use app\modules\catalog\models\GoodsCounts;
use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\Api;
use app\modules\common\models\User;
use Yii;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\models\Deliveries;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;
use app\modules\common\models\HelperConnector;
use app\modules\catalog\models\Tags;

/**
 * This is the model class for table "orders_items".
 *
 * @property integer $id
 * @property integer $order_group_id
 * @property integer $good_id
 * @property integer $variation_id
 * @property string $time
 * @property string $comission
 * @property string $price
 * @property string $discount
 * @property integer $count
 * @property string $fee
 * @property integer $bonus
 * @property integer $store_id
 * @property string $comments
 * @property string $comments_shop
 * @property string $comments_call_center
 * @property integer $seller_status_id_
 * @property string $receive
 * @property string $user_name
 * @property string $release
 * @property integer $status_id
 * @property integer $status
 */
class OrdersItems extends ActiveRecordRelation
{
    public $date;
    public $prod_id;
    public $item_id;
    public $order_id;
    public $count_item;
    public $image;
    public $producer_name;
    public $delivery_address;
    public $delivery_name;
//    public $variation_id;
    public $itemVariantId;

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
            [['order_group_id', 'good_id','store_id', 'variation_id', 'count', 'count_save', 'seller_status_id_', 'status_id', 'bonusBack', 'rublBack', 'status'], 'integer'],
            [['time', 'receive', 'release'], 'safe'],
            [['comission', 'price', 'discount', 'fee', 'bonus'], 'number'],
//            [['comments', 'comments_shop'], 'required'],
            [['comments', 'comments_shop', 'comments_call_center'], 'string'],
            [['user_name'], 'string', 'max' => 64],
            [['order_id','date','prod_id','item_id','count_item','image','producer_name','delivery_address','delivery_name','variation_id','itemVariantId'], 'safe'],
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
            'time' => 'Time',
            'comission' => 'Comission',
            'price' => 'Price',
            'discount' => 'Discount',
            'count' => 'Count',
            'count_save' => 'Count Save',
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
            'bonusBack' => 'Bonus Back',
            'rublBack' => 'Rubl Back',
            'status' => 'Status',
            'store_id',
        ];
    }

    public function getorders_groups(){
        return $this->hasMany(OrdersGroups::className(), ['id' => 'order_group_id']);
    }

    public function getOrderGroup(){
        return $this->hasOne(OrdersGroups::className(), ['id' => 'order_group_id']);
    }

    public function getOrders(){
        return $this->hasOne(Orders::className(), ['id' => 'order_id'])
            ->viaTable('orders_groups', ['id' => 'order_group_id']);
    }

    public function getGoods()
    {
        return $this->hasMany(Goods::className(), ['id' => 'good_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public function getGood()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public function getShops(){
        return $this->hasOne(Shops::className(), ['id' => 'shop_id'])
            ->viaTable('goods', ['id' => 'good_id']);
    }

    public function getShop(){
        return Shops::find()->leftJoin('shops_stores','shops_stores.shop_id = shops.id')->where(['shops_stores.id' => $this->store_id])->one();
    }

    public function getDeliveries(){
        return $this->hasOne(Deliveries::className(), ['id' => 'delivery_id']);

    }

    public function getCategoryLinks(){
        return $this->hasOne(CategoryLinks::className(), ['product_id' => 'good_id']);

    }

    public function getCategory(){
        return $this->hasOne(Category::className(), ['id' => 'category_id'])->via('categoryLinks');
    }

    public function getWeight(){
        return $this->hasOne(Tags::className(), ['id' => 'tag_id'])
            ->viaTable('tags_links', ['variation_id' => 'id'])->where(['group_id'=>1014]);
    }

    public function getShops_stores(){
        return $this->hasOne(ShopsStores::className(), ['id' => 'store_id']);
    }

    public function getShopsStoresTitle(){
        $store = $this->shops_stores;

        return $store ? $store->address_id:'';
    }

    public function getGoodsVariations(){
        return $this->hasOne(GoodsVariations::className(), ['id' => 'variation_id']);
    }

    public function getStatusTitle(){
        return OrdersStatus::findOne($this->status_id);
    }

    public function sendCanceledItemToHelper($itemId = false){
        /*var_dump($this->id);
        var_dump($this->order_id);
        var_dump($this->variation_id);
        var_dump($this->order_group_id);
        var_dump($this->orderGroup);*/
        $cancelItem = [
            'order_id' => $this->orderGroup->order_id,
            'group_id' => $this->order_group_id,
            'good_id' => $this->good_id,
            'variation_id' => $this->variation_id,
            'cancel' => 'true',
        ];
        if(empty($this->status_id)){
            $cancelItem['process'] = 'metro';
        }
        else{
            $cancelItem['process'] = 'delivery';
        }
        (new HelperConnector())->sendCancel($cancelItem);
        //print_R($cancelItem);

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

            //if(!empty($this->status_id) && !empty($changedAttributes['status_id']) && $this->status_id != $changedAttributes['status_id']){
            if(!empty($this->status_id) && (!empty($this->id))){
                $ordersItemsStatus = new OrdersItemsStatus();
                $ordersItemsStatus->order_item_id = $this->id;
                $ordersItemsStatus->status_id = $this->status_id;
                $ordersItemsStatus->user_id = !empty(\Yii::$app->user->identity) ? \Yii::$app->user->identity->id : 10013387;
                $ordersItemsStatus->date = date('Y-m-d H:i:s');
                $ordersItemsStatus->status = 1;
                $ordersItemsStatus->save();
            }
            /*
            if($this->status_id == \Yii::$app->params['emptyCountProductStatusId']){
                $this->goodsVariations->status = 0;
                $this->goodsVariations->save();
            }*/

            if(($this->status_id == 1001 || $this->status_id == 1008 || $this->status_id == \Yii::$app->params['emptyCountProductStatusId']) || ($this->status=0)){
                $flag = false;
                $orderItemListStatus = self::find()->where(['order_group_id' => $this->order_group_id,'status' => 1])->groupBy('status_id')->indexBy('status_id')->all();
                if(!$orderItemListStatus){

                }
                else{
                    foreach($orderItemListStatus as $statusId => $item){
                        if($statusId < 1001){
                            $flag = true;
                        }
                    }
                    if(!$flag){
                        (new Orders())->preparationOrdersToShipped($this->orderGroup);
                    }
                }
            }
            elseif($this->status_id == 1006 || $this->status_id == \Yii::$app->params['emptyCountProductStatusId']){
                $flag = false;
                $orderItemListStatus = self::find()->where(['order_group_id' => $this->order_group_id,'status' => 1])->groupBy('status_id')->indexBy('status_id')->all();
                if(!empty($orderItemListStatus)){
                    foreach($orderItemListStatus as $statusId => $item){
                        if(!in_array($statusId, [1002,1006]) ){
                            $flag = true;
                        }
                    }
                    if(!$flag){
                        (new Api())->sms(User::find()->where(['id'=>$this->orders->user_id])->one()->phone, 'Заказ #'.$this->orders->id.' доставлен в пункт выдачи');
                    }
                }



            }
            else{
                if($this->status==0){//отмена items
                    //отправляем в хелпер отмену
                    $this->sendCanceledItemToHelper($this->id);
                }
            }
    }
}
