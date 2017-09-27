<?php

namespace app\modules\shop\models;

use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketLg;
use app\modules\basket\models\BasketShop;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\coders\models\ClientLog;
use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\DeliveriesPrices;
use app\modules\common\models\HelperConnector;
use app\modules\common\models\UpdateLogs;
use app\modules\common\models\User;
use app\modules\common\models\UsersPays;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Api;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $code_id
 * @property integer $type
 * @property string $extremefitness
 * @property string $comments
 * @property string $comments_call_center
 * @property string $date
 * @property integer $call_status
 * @property integer $status
 *
 * @property UsersPays[] $usersPays
 */
class Orders extends UpdateLogs //ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'code_id', 'type', 'call_status', 'basket_id', 'add_Bonus', 'add_Rubl','negative_review', 'status'], 'integer'],
            [['extremefitness'], 'number'],
//            [['comments'], 'required'],
            [['comments', 'comments_call_center'], 'string'],
            [['actions_json'], 'string', 'max' => 500],
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
            'user_id' => 'User ID',
            'code_id' => 'Code ID',
            'type' => 'Type',
            'extremefitness' => 'Extremefitness',
            'comments' => 'Comments',
            'comments_call_center' => 'Comments Call Center',
            'date' => 'Date',
            'call_status' => 'Call Status',
            'basket_id' => 'Basket ID',
            'actions_json' => 'Actions Json',
            'add_Bonus' => 'Add  Bonus',
            'add_Rubl' => 'Add  Rubl',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersPays()
    {
        return $this->hasMany(UsersPays::className(), ['order_id' => 'id']);
    }

    public function getCode()
    {
        return $this->hasOne(Codes::className(), ['id' => 'code_id']);
    }

    public function getOrdersGroups()
    {
        return $this->hasMany(OrdersGroups::className(), ['order_id' => 'id']);
    }

    public function getItems()
    {
        return $this->hasMany(OrdersItems::className(), ['order_group_id' => 'id'])
            ->viaTable(OrdersGroups::tableName(),['order_id' => 'id']);
    }

    public function getOrdersItems()
    {
        return OrdersItems::find()
            ->leftJoin('orders_groups','orders_groups.id = orders_items.order_group_id')
            ->where(['orders_groups.order_id' => $this->id])
            ->all();
    }

    public function getOrdersGoods()
    {
        return Goods::find()
            ->leftJoin('orders_items', 'orders_items.good_id = goods.id')
            ->leftJoin('orders_groups', 'orders_groups.id = orders_items.order_group_id')
            ->where(['orders_groups.order_id' => $this->id])
            ->asArray()->all();
    }

    public function getOrdersItemsQuery()
    {
        //Zloradnij::print_arr(OrderFilter::getOrderItemQuery($this->id));
        return !empty(OrderFilter::getOrderItemQuery($this->id)) ? OrderFilter::getOrderItemQuery($this->id) : OrdersItems::find()
            ->leftJoin('orders_groups','orders_groups.id = orders_items.order_group_id')
            ->where(['orders_groups.order_id' => $this->id]);
    }

    public function checkData(){

    }

    public function createNewOrder(Basket $basket,$params){
        if(!empty($params) && !empty($basket)){
            $order = new Orders();
            $order->call_status = 0;
            $order->code_id = $basket->promo_code_id ? $basket->promo_code_id : NULL;
            $order->date = date('Y-m-d H:i:s');
            $order->status = 0;
            $order->type = 1;
            $order->user_id = $basket->user_id;
            $order->comments = $params['order_comments'] ? $params['order_comments'] : '';
            $order->basket_id = $basket->id;
            $order->actions_json = \Yii::$app->action->getActivActionsIdJsonArray();

            $cashBaks = \Yii::$app->action->cashBackValues();
            if(!empty($cashBaks)) {
                if ($cashBaks['money'] > 0) {
                    $order->add_Rubl = $cashBaks['money'];
                }
                if ($cashBaks['bonus'] > 0) {
                    $order->add_Bonus = $cashBaks['bonus'];
                }
            }

            if($order->save()){
                $deliveryTime = json_decode($basket->time_list,true);

                $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
                $deliveryGroup->setProducts($basket->products);
                $deliveryGroup->setDeliveryId($basket->delivery_id);
                $deliveryGroup->setProductDeliveryGroup();

                if(!empty($deliveryGroup->productDeliveryGroup)){
                    foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                        sort($group);
                        if(!empty($group[0])){
                            $orderGroup = new OrdersGroups();
                            $orderGroup->status = 1;
                            $orderGroup->address_id = $deliveryGroup->getAddressForBase($key,$basket->address_id);
                            $orderGroup->delivery_id = $deliveryGroup->getDeliveryForBase($key,$basket->delivery_id);
                            $orderGroup->delivery_date = date('Y-m-d H:i:s',$deliveryGroup->getDateForBase($key,$deliveryTime[$key]['time']));
                            $keyGroup = ($key == 'address_0' || $key == 'club_1') ? true : false;
                            foreach ($group as $key => $value){
                                if($value != 1014){
                                    $group[0] = $value;  //<<<-------------------------КАСТЫЛЬ ДЛЯ БЫСТРОЙ ДОСТАВКИ ТУТ, ВОН ТАМ СЛЕВА
                                    break;
                                }
                            }
                            $orderGroup->delivery_price = $basket->getDeliveryPriceWithParams($basket->delivery_id,$group[0],$keyGroup);
                            $orderGroup->order_id = $order->id;
                            $orderGroup->type_id = $group[0];

                            if($orderGroup->type_id == '1014'){
                                if(date('H',strtotime($orderGroup->delivery_date)) >= 22 || date('H',strtotime($orderGroup->delivery_date)) <= 7){
                                    foreach (Yii::$app->params['nightButterflies'] as $butterfly){
                                        $api = new Api();
                                        $api->sms($butterfly['phone'],'Новый заказа №'.$order->id.' из ночной доставки.');
                                    }
                                }
                            }
                            if($orderGroup->save()){
                                foreach ($basket->products as $product) {
                                    if(in_array($product->product->type_id,$group)){
                                        $orderItem = new OrdersItems();
                                        $findStore = !empty($params['store-list'][$product->product_id]) ? $params['store-list'][$product->product_id] : $product->product->shop->shops[0]->stores[0]->id;
                                        $findStore = !empty($product->store_id) ? $product->store_id : $findStore;

                                        if($orderGroup->type_id == '1014'){
                                            if(date('H',strtotime($orderGroup->delivery_date)) >= 22 || date('H',strtotime($orderGroup->delivery_date)) <= 7){
                                                $findStore = 10000296;
                                            }else{
                                                $findStore = 10000284;
                                            }
                                        }



                                        $orderItem->variation_id = $product->variant_id;
                                        $orderItem->good_id = $product->product_id;
                                        $orderItem->status = 1;
                                        $orderItem->order_group_id = $orderGroup->id;
                                        $orderItem->store_id = $findStore;
                                        //if($order->user_id == 10020120){
                                        $orderItem->comission = $product->price - $product->variant->price;//$product->price - $product->variant->price;
                                        //}
                                        //else{
                                        //    $orderItem->comission = $product->priceDiscount - $product->variant->price + $product->bonus;//$product->price - $product->variant->price;
                                        //}
                                        //$orderItem->comission = $product->priceDiscount - $product->variant->price + $product->bonus;//$product->price - $product->variant->price;
                                        $orderItem->price = $product->price;
                                        $orderItem->discount = ($product->price - $product->priceDiscount - $product->bonus);//- $product->bonus
                                        $orderItem->count = $product->count;
                                        $orderItem->count_save = $product->count;
                                        if(!empty($basket->promo_code_id)){
                                            /*$deltaPrice = ($product->price - $product->bonus);
                                            if($deltaPrice>0){
                                                $orderItem->fee = $deltaPrice *  $basket->promoCode->type->fee / 100;
                                            }
                                            else{
                                                $orderItem->fee = 0;
                                            }*/
                                            $orderItem->fee = $product->fee;
                                        }
                                        else{
                                            $orderItem->fee = 0;
                                        }
                                        //$orderItem->fee = $basket->promo_code_id ? ($product->variant->priceValue * $basket->promoCode->type->fee / 100) : 0;
                                        $orderItem->bonus = $product->bonus;
                                        $orderItem->bonusBack = $product['bonusBack'];
                                        $orderItem->rublBack = $product['rublBack'];



                                        if($orderItem->save()){

                                        }else{
                                            return false;
                                        }
                                    }
                                }
                            }else{
                                return false;
                            }
                        }
                    }
                }
                /*if($order->findMetroProducts()){
                    $order->preparationMetro($basket);
                }*/
                $order->createSellerMasterLog();
                //$this->createSellreMasterLog();
                return $order->id;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function createNewOrderFromBasket(Basket $basket){
        if(!empty($basket)){
            $order = new Orders();
            $order->call_status = 0;
            $order->code_id = $basket->promo_code_id ? $basket->promo_code_id : NULL;
            $order->date = date('Y-m-d H:i:s');
            $order->status = 0;
            $order->type = 1;
            $order->user_id = $basket->user_id;
            $order->comments = !empty($basket->comments) ? $basket->comments : '';
            $order->basket_id = $basket->id;
            $order->actions_json = \Yii::$app->action->getActivActionsIdJsonArray();

            $cashBaks = \Yii::$app->action->cashBackValues();
            if(!empty($cashBaks)){
                if($cashBaks['money']>0){
                    $order->add_Rubl =  $cashBaks['money'];
                }
                if($cashBaks['bonus']>0){
                    $order->add_Bonus = $cashBaks['bonus'];
                }
            }

            if($order->save()){
                $deliveryTime = json_decode($basket->time_list,true);

                $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
                $deliveryGroup->setProducts($basket->products);
                $deliveryGroup->setDeliveryId($basket->delivery_id);
                $deliveryGroup->setProductDeliveryGroup();

                if(!empty($deliveryGroup->productDeliveryGroup)){
                    foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                        if(!empty($group[0])){
                            $orderGroup = new OrdersGroups();
                            $orderGroup->status = 1;
                            $orderGroup->address_id = $deliveryGroup->getAddressForBase($key,$basket->address_id);
                            $orderGroup->delivery_id = $deliveryGroup->getDeliveryForBase($key,$basket->delivery_id);
                            $orderGroup->delivery_date = date('Y-m-d H:i:s',$deliveryGroup->getDateForBase($key,$deliveryTime[$key]['time']));
                            $keyGroup = ($key == 'address_0' || $key == 'club_1') ? true : false;
                            $orderGroup->delivery_price = $basket->getDeliveryPriceWithParams($basket->delivery_id,$group[0],$keyGroup);
                            $orderGroup->order_id = $order->id;
                            $orderGroup->type_id = $group[0];

                            if($orderGroup->save()){
                                foreach ($basket->products as $product) {
                                    if(in_array($product->product->type_id,$group)){
                                        $orderItem = new OrdersItems();
                                        $findStore = !empty($params['store-list'][$product->product_id]) ? $params['store-list'][$product->product_id] : $product->product->shop->shops[0]->stores[0]->id;
                                        $findStore = !empty($product->store_id) ? $product->store_id : $findStore;

                                        $orderItem->variation_id = $product->variant_id;
                                        $orderItem->good_id = $product->product_id;
                                        $orderItem->status = 1;
                                        $orderItem->order_group_id = $orderGroup->id;
                                        $orderItem->store_id = $findStore;
                                        $orderItem->comission = $product->priceDiscount - $product->variant->price + $product->bonus;//$product->price - $product->variant->price;
                                        $orderItem->price = $product->price;
                                        $orderItem->discount = ($product->price - $product->priceDiscount - $product->bonus);
                                        $orderItem->count = $product->count;
                                        //var_dump($basket->promo_code_id);
                                        //var_dump($product->variant->priceValue);
                                        //var_dump($basket->promoCode);
                                        //echo $product->count*ceil($product->variant->priceValue * $basket->promoCode->type->discount / 100);
                                        if(!empty($basket->promo_code_id)){
                                            /*
                                            $deltaPrice = ($product->price - $product->bonus);
                                            if($deltaPrice>0){
                                                $orderItem->fee = $deltaPrice *  $basket->promoCode->type->fee / 100;
                                            }
                                            else{
                                                $orderItem->fee = 0;
                                            }*/
                                            $orderItem->fee = $product->fee;
                                        }
                                        else{
                                            $orderItem->fee = 0;
                                        }
                                        //$orderItem->fee = $basket->promo_code_id ? ceil($product->variant->priceValue * intval($basket->promoCode->type->discount) / 100) : 0;
                                        $orderItem->bonus = $product->bonus;
                                        $orderItem->bonusBack = $product['bonusBack'];
                                        $orderItem->rublBack = $product['rublBack']/$product->count;

                                        if($orderItem->save()){
                                            print_r($orderItem);
                                        }else{
                                            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0000.txt', 'ORDER ITEM ERROR '.var_export($orderItem->errors, true));
                                            return false;
                                        }
                                    }
                                }
                            }else{
                                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0000.txt', 'ORDER GROUP ERROR '.var_export($orderGroup->errors, true));
                                return false;
                            }
                        }
                    }
                }
                /*if($this->findMetroProducts()){
                    $this->preparationMetro($basket);
                }*/

                return $order->id;
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0000.txt', 'ORDER ERROR '.var_export($order->errors, true));
                return false;
            }
        }else{
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/actionResponsePaymentCenter0000.txt', 'BASKET OR PARAMS EMPTY '.var_export($basket, true));
            return false;
        }
    }

    public function checkMetroItems($orderId = false,Basket $basket){
        if($orderId){
            $order = new Orders();
            $order = $order->findOne($orderId);
            if($order->status == 1){
                if($order->findMetroProducts()){
                    $order->preparationMetro($basket);
                }
            }
        }
    }

    public function getMoney(){
        $money = 0;
        if(!$this->ordersGroups){

        }else{
            foreach ($this->ordersGroups as $ordersGroup) {
                $orderItems = $ordersGroup->ordersItems;
                if(!$orderItems){

                }else{
                    foreach ($orderItems as $orderItem) {
                        $money += ($orderItem->price - $orderItem->bonus - $orderItem->discount) * $orderItem->count;
                    }
                }
            }
        }
        return $money;
    }

    public function getBonus(){
        $bonus = 0;
        if(!$this->ordersGroups){

        }else{
            foreach ($this->ordersGroups as $ordersGroup) {
                $orderItems = $ordersGroup->ordersItems;
                if(!$orderItems){

                }else{
                    foreach ($orderItems as $orderItem) {
                        $bonus += $orderItem->bonus * $orderItem->count;
                    }
                }
            }
        }
        return $bonus;
    }

    // Отчисления по промо-коду
    public function getFee(){
        $fee = 0;
        if(!$this->ordersGroups){

        }else{
            foreach ($this->ordersGroups as $ordersGroup) {
                $orderItems = $ordersGroup->ordersItems;
                if(!$orderItems){

                }else{
                    foreach ($orderItems as $orderItem) {
                        $fee += $orderItem->fee * $orderItem->count;
                    }
                }
            }
        }
        return $fee;
    }

    public function getDeliveryPrice(){
        $deliveryPrice = 0;
        if(!$this->ordersGroups){

        }else{
            foreach ($this->ordersGroups as $ordersGroup) {
                $deliveryPrice += $ordersGroup->delivery_price;
            }
        }
        return $deliveryPrice;
    }

    public function getShops(){
        $result = [];

        if(!empty($this->ordersGroups)){
            foreach ($this->ordersGroups as $ordersGroup) {
                if(!empty($ordersGroup->ordersItems)){
                    foreach ($ordersGroup->ordersItems as $ordersItem) {
                        $result[] = $ordersItem->store_id;
                    }
                }
            }
        }
        if(!empty($result)){
            return Shops::find()->leftJoin('shops_stores','shops_stores.shop_id = shops.id')->where(['IN', 'shops_stores.id', $result])->all();
        }else{
            return false;
        }
    }

    public function getShopsManagements(){
        $result = [];
        $shops = $this->shops;

        if(!empty($shops)){
            foreach ($shops as $shop) {
                $managers = User::find()->leftJoin('users_roles','users_roles.user_id = users.id')->where(['users_roles.shop_id' => $shop->id,'users.status' => 1,'users_roles.status' => 1])->all();
                if(!empty($managers)){
                    foreach ($managers as $manager) {
                        $result[$shop->id][] = $manager;
                    }
                }
            }
        }
        return $result;
    }

    public function getShopItems(){
        $result = [];

        if(!empty($this->ordersGroups)){
            foreach ($this->ordersGroups as $ordersGroup) {
                if(!empty($ordersGroup->ordersItems)){
                    foreach ($ordersGroup->ordersItems as $ordersItem) {
                        $store = ShopsStores::find()->where(['id' => $ordersItem->store_id])->one();
                        $result[$store->shop_id][] = Goods::find()->where(['id' => $ordersItem->good_id])->one();
                    }
                }
            }
        }
        if(!empty($result)){
            return $result;
        }else{
            return false;
        }
    }

    public function getUser(){
        return User::findOne($this->user_id);
    }

    public function getPromoCode(){
        return Codes::findOne($this->code_id);
    }

    public function preparationOrdersToShipped(OrdersGroups $orderGroup){
        if(empty($orderGroup)
            || ($orderGroup->delivery_id != 1006 && $orderGroup->delivery_id != 1007 && $orderGroup->delivery_id != 1003)
            || ($orderGroup->type_id == 1010 && $orderGroup->delivery_id == 1003)
            || ($orderGroup->type_id == 1005 && $orderGroup->delivery_id == 1003)
            || $orderGroup->status != 1){
            return false;
        }

        $postDataList = [];
        $ordersItems = $orderGroup->ordersItems;

        if(!$ordersItems){

        }else{
            $productList = [];
            $pointsListResult = [];
            $earlyStoreID = '';
            $allPreset = ArrayHelper::getColumn(Yii::$app->params['presentAll'],'present');
            foreach($ordersItems as $pointId => $point){
                if(!in_array($point->variation_id, Yii::$app->params['present'])){
                    if(in_array($point->variation_id, $allPreset)){
                        $point->store_id = $earlyStoreID;
                    }
                    $pointsListResult[$point->store_id] = $point;
                    $good = GoodsVariations::find()->where(['id'=>$point->variation_id])->one();
                    $weight = $good->weight;
                    
                    if(!empty($good) && !empty($good->code))
                        $code = $good->code;
                    else
			$code = "";
			
//.		    if(empty($weight))	
			                
                    $productList[$point->store_id][] = [
                        'productId' => $point->good_id,
                        'variantId' => $point->variation_id,
                        'count' => $point->count,
                        'name' => $point->good->name,
                        'weight' => !empty($weight) ? $weight->value : 0,
                        'vendorCode'=> $code,
                    ];

                    $earlyStoreID = $point->store_id;
                }

            }

            foreach($pointsListResult as $storeId => $point){
                if(!empty($point->shops_stores) && !empty($point->shops_stores->addressString)){
                    $pointAddress = $point->shops_stores->addressString;

                    $postDataList['points'][] = [
                        'AdministrativeAreaName' => 'Новосибирская область',
                        'city' => !empty($pointAddress->city) ? $pointAddress->city : 'Новосибирск',
                        'comment' => $pointAddress->comments,
                        'street' => $pointAddress->street,
                        'house' => $pointAddress->house,
                        'room' => $pointAddress->room,
                        'phone' => $pointAddress->phone,
                        'destination' => 0,
                        'products' => $productList[$storeId],
                    ];
                }

            }

            $finalPoint = $orderGroup->users_address;
            if(empty($finalPoint->phone)){
                $orderForUser = Orders::find()->where(['id'=>$orderGroup->order_id])->one();
                $userForPhone = User::find()->where(['id'=>$orderForUser->user_id])->one();
                $finalPoint->phone = $userForPhone->phone;
            }
            $finalPointComment = $finalPoint->comments;
            $finalPointComment .= !empty($orderGroup->orders->comments) ? "\r\n" . $orderGroup->orders->comments : '';
            $finalPointComment .= !empty($orderGroup->orders->comments_call_center) ? "\r\n" . $orderGroup->orders->comments_call_center : '';

            $postDataList['points'][] = [
                'AdministrativeAreaName' => 'Новосибирская область',
                'city' => !empty($finalPoint->city) ? $finalPoint->city : 'Новосибирск',
                'comment' => $finalPointComment,
                'street' => $finalPoint->street,
                'house' => $finalPoint->house,
                'room' => $finalPoint->room,
                'phone' => $finalPoint->phone,
                'destination' => 1,
            ];

            $postDataList['delivery_date'] = date('d.m.Y',strtotime($orderGroup->delivery_date));
            $postDataList['response_order_id'] = $orderGroup->order_id;
            $postDataList['comment'] = $orderGroup->orders->comments;
            $postDataList['time'] = date('H:i',strtotime($orderGroup->delivery_date));
            $postDataList['response_order_id'] = $orderGroup->order_id;
            $postDataList['response_id'] = $orderGroup->id;
            $postDataList['weight'] = 0;
            $postDataList['size'] = 0;
            $postDataList['castCost'] = 0;
            $postDataList['fastDelivery'] = $orderGroup->type_id==1014?1:0;
            $postDataList['far'] = $orderGroup->delivery_price < 250 ? 0 : 1;
        }
        //print_r($postDataList);die();
        $h = new HelperConnector();
        $h->setOrder($postDataList);
        if(empty($h->getErrors())){
            $h->orderShipping();
        }else{
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre1.txt',"\n\r--------ERRORS !!!----------\n\r".var_export($h,true));
        }
    }

    private function preparationMetro(Basket $basket){
        $metroProducts = $postDataList = [];
        if(!empty($this->ordersItems)){
            foreach ($this->ordersItems as $shopItem) {
                if($shopItem->shops_stores->shop_id == 10000182 || $shopItem->shops_stores->shop_id == 10000258){
                    $metroProducts[] = $shopItem;
                }
            }

            if(!empty($metroProducts)){
                foreach ($metroProducts as $metroProduct) {
                    $postDataList['products'][] = [
                        'productId' => $metroProduct->good_id,
                        'variantId' => $metroProduct->variation_id,
                        'vendorCode' => $metroProduct->goodsVariations->code,
                        'count' => $metroProduct->count,
                        'name' => $metroProduct->product->name . ' ' . $metroProduct->goodsVariations->titleWithPropertiesForCatalog,
                        'category' => $metroProduct->product->category['id'],
                        'categoryName' => $metroProduct->product->category['title'],
                    ];
                }
                if(!empty($postDataList)){
                    $postDataList['delivery_date'] = date('d.m.Y',strtotime($metroProducts[0]->orderGroup->delivery_date));
                    $postDataList['time'] = date('H:i',strtotime($metroProducts[0]->orderGroup->delivery_date));
                    $postDataList['comment'] = $metroProducts[0]->orderGroup->orders->comments;
                    $postDataList['response_id'] = $metroProducts[0]->orderGroup->id;
                    $postDataList['response_order_id'] = $metroProducts[0]->orderGroup->order_id;
                    $postDataList['uniqueShopKey'] = '57a17952800d0';
                    $postDataList['fastDelivery'] = $metroProducts[0]->orderGroup->type_id==1014?1:0;

                    // 1 - +
                    // 2 - Kirova
                    // 3 - Versal

                    $basket->current_club = !empty($basket->current_club) ? json_decode($basket->current_club) : false;
                    $postDataList['address'] = !empty($basket->current_club->metro) ? $basket->current_club->metro : 1;
                }
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://helper.express/ajax/createmetroapi');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postDataList));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $data = curl_exec($ch);

            if(!$data){
//                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre121.txt',"\n\r--------ERROR!!!----------\n\r".var_export($data,true));
            }else{
//                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre121.txt',"\n\r--------GET DATA -- 2!!!----------\n\r".var_export($data,true));
            }
            curl_close($ch);
        }
    }

    private function findMetroProducts(){
        if(!empty($this->ordersItems)){
            foreach ($this->ordersItems as $shopItem) {
                if($shopItem->shops_stores->shop_id == 10000182 || $shopItem->shops_stores->shop_id == 10000258){
                    return true;
                }
            }
        }
        return false;
    }

    private function createSellerMasterLog(){

        $fullMasterTime = \Yii::$app->session->get('fullMasterTime',0);

        if(!empty($_SESSION['startMaster']) && !empty($_SESSION['shopMaster']) && $_SESSION['shopMaster'] == 1 ){
            $fullMasterTime += time() - $_SESSION['startMaster'];
        }
        $terminal = 0;
        $userAgent = '';
        if(empty(ClientLog::find()->where(['order_id'=>$this->id])->one())) {
            if(isset($_SERVER['HTTP_USER_AGENT'])){
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
            }
            if(!empty($userAgent) && stristr($userAgent, 'iPadTerminal')){
                $terminal = intval(preg_replace('/(.*iPadTerminal)[\D]*/', '', $userAgent));
            }

            $clientLog = new ClientLog();
            $clientLog->master = $fullMasterTime;
            $clientLog->session_id = \Yii::$app->session->id;
            $clientLog->user_id = $this->user_id;
            $clientLog->order_id = $this->id;
            $clientLog->terminal = is_int($terminal)?$terminal:0; //stristr($userAgent, 'iPadTerminal')? 1:0;
            $clientLog->user_agent = $userAgent;
            $clientLog->remote_addr =isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'' ;
            if($clientLog->save(true)){
                if($fullMasterTime > 0){
                    unset($_SESSION['startMaster']);
                    unset($_SESSION['stopMaster']);
                    unset($_SESSION['fullMasterTime']);
                    unset($_SESSION['shopMaster']);
                    return true;
                }
            }
        }
        return false;
    }
    /*
    public function afterSave($insert, $changedAttributes)
    {


        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }*/
}
