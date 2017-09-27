<?php

namespace app\modules\basket\models;

use app\modules\catalog\models\Codes;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\models\DeliveriesPrices;
use app\modules\common\models\User;
use app\modules\common\models\UsersCards;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

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
 * @property string $comment
 * @property integer $status
 */
class BasketLg extends \yii\db\ActiveRecord
{
    public $action = true;
    public $categoryWithOutAction = [];
    public $bonus = 0;
    public $basketError = false;

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
        return 'basket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date_create', 'last_update', 'delivery_id', 'address_id', 'payment_id', 'promo_code_id', 'status'], 'integer'],
            [['session_id', 'delivery_id', 'address_id', 'payment_id', 'status'], 'required'],
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

    public static function setRecalculateBasket(){
        \Yii::$app->session['recalculate-basket'] = '1';
    }

    public static function setDisableRecalculateBasket(){
        \Yii::$app->session['recalculate-basket'] = '0';
    }

    public function findByUserId($userId){
        return self::find()->where(['user_id' => $userId])->all();
    }

    public function findBySessionId($sessionId){
        return self::find()->where(['session_id' => $sessionId])->all();
    }

    public function findById($id){
        return self::find()->where(['id' => $id])->one();
    }

    public function mergerBasket($basketSession = false, $basketBase = false){
        if(!$basketBase && !$basketSession){
            $basket = new BasketLg();

            $basket->address_id = 0;
            $basket->delivery_id = 0;
            $basket->payment_id = 0;
            $basket->user_id = Yii::$app->user->isGuest ? NULL : Yii::$app->user->identity->id;
            $basket->session_id = $this->getBasketSessionId();
            $basket->status = 0;

            $basket->save();

            return $basket;
        }

        if(!$basketSession){
            $basketBase->session_id = $this->getBasketSessionId();
            $basketBase->save();
            return $basketBase;
        }

        if(!$basketBase){
            $basketSession->session_id = $this->getBasketSessionId();
            $basketSession->user_id = Yii::$app->user->isGuest ? false : Yii::$app->user->identity->id;
            $basketSession->save();

            return $basketSession;
        }

        if($basketBase->id == $basketSession->id){
            $basketBase->session_id = $this->getBasketSessionId();
            $basketBase->save();
            return $basketBase;
        }

        if(count($basketSession->products) > 0){
            $basketBase->delete();
            $basketSession->user_id = Yii::$app->user->isGuest ? false : Yii::$app->user->identity->id;
            $basketSession->save();
            return $basketSession;
        }

        $basketBase->session_id = $this->getBasketSessionId();
        $basketBase->save();
        $basketSession->delete();
        return $basketBase;
    }

    private function getBasketSessionId(){
        return !empty(Yii::$app->session['basket-session-id']) ? Yii::$app->session['basket-session-id'] : Yii::$app->session->id;
    }

    public function findCurrentBasket(){
        $basketBase = !empty(Yii::$app->user->identity) ? self::find()->where(['status' => 0,'user_id' => Yii::$app->user->identity->id])->one() : false;
        $basketSession = self::find()->where(['status' => 0,'session_id' => $this->getBasketSessionId()])->one();

        return $this->mergerBasket($basketSession,$basketBase);
    }


    public function getProducts(){
        return $this->hasMany(BasketProducts::className(), ['basket_id' => 'id']);
    }

//    public function getProducts(){
//        if(!$this->basketProducts){
//            return false;
//        }else{
//            $variationsIds = [];
//            foreach ($this->basketProducts as $basketProduct) {
//                $variationsIds[] = $basketProduct->variant_id;
//            }
//            return GoodsVariations::find()->where(['IN','id',$variationsIds])->all();
//        }
//    }

    public function getFullPriceWithOutDiscount(){
        if(!$this->products){
            return 0;
        }else{
            $fullPrice = 0;
            foreach ($this->products as $product) {
//                Zloradnij::print_arr($product);
                $fullPrice += $product->count * $product->variant->priceValue;
            }
            return $fullPrice;
        }
    }

    public function getFullPriceWithOutDiscountAndBonus(){
        if(!$this->products){
            return 0;
        }else{
            $fullPrice = 0;
            foreach ($this->products as $product) {
                $fullPrice += $product->count * $product->variant->price_out;
            }
            return $fullPrice;
        }
    }

    public function getFullPrice($discount = 0){
        if(!$this->products){
            return 0;
        }else{
            $fullPrice = 0;
            foreach ($this->products as $product) {
                $fullPrice += $product->count * $product->variant->getDiscountPrice($discount);
            }
            return $fullPrice;
        }
    }

    public function getDefaultPrice(){
        if(!$this->products){
            return 0;
        }else{
            $fullPrice = 0;
            foreach ($this->products as $product) {
                //Zloradnij::print_arr($product->variant->priceValue.' - '.$product->variant->bonus.' - '.$product->variant->price_out.' - '.$product->variant->discountPrice);
                $fullPrice += $product->count * $product->variant->priceValue;
            }
            return $fullPrice;
        }
    }

    public function getResultPrice(){
        $discountPercent = !empty($this->promo_code_id) ? $this->getPromoCodePercent() : 0;

        return $this->delivery_price + $this->getFullPrice($discountPercent);
    }

    public function getBonusValue(){
        if(!$this->products){
            return $this->bonus;
        }else{
            if(!empty($this->user->bonus) && $this->user->bonus > 0){
                foreach ($this->products as $product) {
                    if($product->product->bonus == 1){
                        $product->variant->bonus = min(
                            $product->variant->priceValue * $product->count,
                            $this->user->bonus - $this->bonus
                        );
                        $this->bonus += $product->variant->bonus;
                    }
                }
            }else{

            }
            return $this->bonus;
        }
    }

    public function getFullCount(){
        if(!$this->products){
            return 0;
        }else{
            $fullCount = 0;
            foreach ($this->products as $product) {
                $fullCount += $product->count;
            }
            return $fullCount;
        }
    }

    public function getSmallBasket(){
        $response = [
            'count' => $this->fullCount ? $this->fullCount : 0,
            'price' => $this->fullPriceWithOutDiscount ? $this->fullPriceWithOutDiscount : 0,
        ];

        return $response;
    }

    public function setDelivery($id)
    {
        $this->delivery_id = !empty(intval($id)) ? intval($id) : 0;
        $this->save();
    }

    public function setAddress($id)
    {
        $this->address_id = !empty(intval($id)) ? intval($id) : 0;
        $this->save();
    }

    public function getProductTypes(){
        $result = false;
        if(empty($this->products)){

        }else{
            foreach ($this->products as $product) {
                $result[$product->product->type_id][] = $product;
            }
        }
        return $result;
    }

    public function getTypeProductInBasket(){
        $result = false;
        if(empty($this->products)){

        }else{
            foreach ($this->products as $product) {
                $result[] = $product->product->type_id;
            }
        }
        return array_unique($result);
    }

    public function getTimeList(){
        $type = $this->typeProductInBasket;

    }

    public function getDeliveryAddresses(){
        $extremeAddress = [];//$this->clubAddresses;

        // Проверка пользователя;
        if(\Yii::$app->user->identity){
            $userAddress = (new Query())->from('address')
                ->select([
                    '\'1006\' AS `id`',
                    'id AS value',
                    'delivery_id',
                    'CONCAT_WS(\', \', district, street, house, room) AS address',
                ])
                ->where(['user_id' => \Yii::$app->user->identity->id])
                ->andWhere(['status' => 1])
                ->all();
            $result = array_merge($extremeAddress,$userAddress);
        }else{
            $result = $extremeAddress;
        }

        return $result;
    }

    public function getClubAddresses(){
        // Загрузка адресов клубов и адресов пользователя;
        $result = (new Query())->from('shops_stores')
            ->select([
                '\'1003\' AS `id`',
                '`address`.`id` AS `value`',
                'CONCAT(\'ExtremeFitness – \', CONCAT_WS(\', \', `street`, `house`, `room`)) AS `address`',
            ])
            ->leftJoin('address','`address`.`id` = `shops_stores`.`address_id`')
            ->where(['shops_stores.shop_id' => 10000001])
            ->andWhere(['shops_stores.show' => 1])
            ->andWhere(['shops_stores.status' => 1])
            ->andWhere(['address.status' => 1])
            ->all();

        return $result;
    }

    public function getUserCards(){
        if(!empty($this->user_id)){
            return UsersCards::find()
                ->where(['user_id' => $this->user_id])
                ->andWhere(['status' => 1])
                ->orderBy('id DESC')
                ->all();
        }

        return false;
    }

    public function getUser(){
        if(!empty($this->user_id)){
            return User::findOne($this->user_id);
        }else{
            return false;
        }
    }

    public function getPromoCode(){
        return Codes::find()->where(['id' => $this->promo_code_id])->one();
    }

    public function getPromoCodeCode(){
        return $this->promoCode ? $this->promoCode->code : false;
    }

    public function getPromoCodePercent(){
        return $this->promoCode ? $this->promoCode->getDiscount() : 0;
    }

    public function setPromoCode($id){
        $this->promo_code_id = $id;
        $this->save();
    }

    public function addProduct($productId,$variantId,$count){
        $find = false;
        if(!$this->products){

        }else{
            foreach ($this->products as $product) {
                if($product->variant_id == $variantId){
                    $find = true;
                    if(!empty($product->product->count_min) && $product->product->count_min > $count){
                        $count = $product->product->count_min;
                    }
                    $product->count = $count;
                    $product->save();
                }
            }
        }
        if(!$find){
            $currentProduct = Goods::findOne($productId);
            if(!empty($currentProduct->count_min) && $currentProduct->count_min > $count){
                $count = $currentProduct->count_min;
            }
            $product = new BasketProducts();
            $product->product_id = $productId;
            $product->variant_id = $variantId;
            $product->count = $count;
            $product->basket_id = $this->id;
            $product->save();
        }
        $this->start();
        return !empty($product->id) ? $product->id : false;
    }

    public function removeProduct($id){
        $find = false;
        if(!$this->products){

        }else{
            foreach ($this->products as $product) {
                if($product->id == $id){
                    $find = true;
                    $product->delete();
                }
            }
        }
        if(!$find){
            // error !!!
        }
    }

    public function findVariantByTags($productId,$tagIds){
        $variant = (new Query)
            ->from('tags')
            ->select(['goods_variations.id','COUNT(*) AS count'])
            ->leftJoin('tags_links','tags_links.tag_id = tags.id')
            ->leftJoin('goods_variations','tags_links.variation_id = goods_variations.id')
            ->leftJoin('goods','goods.id = goods_variations.good_id')
            ->where(['goods.id' => $productId,'goods_variations.status' => 1])
            ->andWhere(['IN','tags.id', $tagIds])
            ->groupBy('goods_variations.id')
            ->orderBy('count DESC')
            ->one();

        if(!$variant){
            return false;
        }
        return $variant['id'];
    }

    public function findProductByItemId($id){
        return BasketProducts::findOne($id);
    }

    public function start(){
        if(!empty($this->user)){
            $allBasketBonus = 0;
            foreach ($this->products as $i => $product) {
                if($product->variant){
                    if($product->product->bonus == 1){
                        $min = min(
                            $product->variant->priceValue * $product->count,
                            $this->user->bonus - $allBasketBonus
                        );
                        $this->products[$i]->variant->setBonusValue(floor($min / $product->count));
                        $this->bonus += $product->variant->bonus * $product->count;
                        $allBasketBonus += $product->variant->bonus * $product->count;
                    }
                    $this->products[$i]->variant->setPriceValue();
                }else{
//                    $product->delete();
                }
            }
        }else{
            foreach ($this->products as $i => $product) {
                $this->products[$i]->variant->setPriceValue();
            }
        }

        if(!empty($this->delivery_id)){
            $this->setDeliveryPrice($this->delivery_id);
        }
    }

    public static function getClubDelivery(){
        // Загрузка адресов клубов и адресов пользователя;
        $result = (new Query())->from('shops_stores')
            ->select([
                '\'1003\' AS `id`',
                '`address`.`id` AS `value`',
                'CONCAT(\'ExtremeFitness – \', CONCAT_WS(\', \', `street`, `house`, `room`)) AS `address`',
            ])
            ->leftJoin('address','`address`.`id` = `shops_stores`.`address_id`')
            ->where(['shops_stores.shop_id' => 10000001])
            ->andWhere(['shops_stores.show' => 1])
            ->andWhere(['shops_stores.status' => 1])
            ->andWhere(['address.status' => 1])
            ->all();

        return $result;
    }

    public function getBasketProductById($id){
        return BasketProducts::find()->where(['product_id' => $id,'basket_id' => $this->id])->all();
    }

    public function checkData(){
        if(count($this->products) < 1){
            $this->basketError[] = 'Вы ничего не выбрали';
            return false;
        }
        if(empty($this->user_id)){
            $this->basketError[] = 'Вы неавторизованы...';
            return false;
        }
        if(empty($this->session_id)){
            $this->basketError[] = 'Пустая сессия';
            return false;
        }
        if(empty($this->delivery_id) || $this->delivery_id == 0){
            $this->basketError[] = 'Не выбрана доставка';
            return false;
        }
        if(empty($this->address_id)){
            $this->basketError[] = 'Не выбран адрес';
            return false;
        }
        if(empty($this->payment_id) || $this->payment_id == 0){
            $this->basketError[] = 'Не выбран способ оплаты';
            return false;
        }
        if(empty($this->time_list)){
            $basketError[] = 'Не выбрано время доставки';
            return false;
        }
        $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
        $deliveryGroup->setProducts($this->products);
        $deliveryGroup->setDeliveryId($this->delivery_id);
        $deliveryGroup->setProductDeliveryGroup();
        $timeList = json_decode($this->time_list,true);

        if(!empty($deliveryGroup->productDeliveryGroup)){
            foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                if(!empty($timeList[$key]['day']) && !empty($timeList[$key]['time'])){

                }else{
                    $this->basketError[] = 'Время доставки выбрано не для всех типов товаров';
                    return false;
                }
            }
        }else{
            $this->basketError[] = 'Нет групп продуктов';
            return false;
        }

        return true;
    }

//    public function afterSave($insert, $changedAttributes)
//    {
//        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
//
////        if(!empty($changedAttributes) && !empty($changedAttributes['status'])){
////            if($changedAttributes['status'] == 0 && $this->status == 1){
////                if(!empty($this->products)){
////                    foreach ($this->products as $product) {
////                        $count = $product->variant;
////
////                    }
////                }
////            }elseif($changedAttributes['status'] == 1 && $this->status == 0){
////
////            }
////        }
//    }

    public function setDeliveryPrice($deliveryId){
        $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
        $deliveryGroup->setProducts($this->products);
        $deliveryGroup->setDeliveryId($this->delivery_id);
        $deliveryGroup->setProductDeliveryGroup();

        $deliveryPrice = 0;

        if(!empty($deliveryGroup->productDeliveryGroup)){
            foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                if(!empty($group[0])){
                    if($key == 'address_0' || $key == 'club_1'){
                        $key = true;
                    }else{
                        $key = false;
                    }
//                    $key = $key == 'address_0' ? true : false;
                    $deliveryPrice += $this->getDeliveryPriceWithParams($deliveryId,$group[0],$key);
                }
            }

            $this->delivery_price = $deliveryPrice;
            $this->save();
        }else{

        }
    }

    public function getDeliveryPriceWithParams($deliveryId,$groupId,$key = false){
        $price = DeliveriesPrices::find()->where(['delivery_id' => $deliveryId,'good_type_id' => $groupId])->select('price')->scalar();
        /*
        if($key && $price > 0){
            $deliveryZeroPrice = 0;
            if(!empty($this->products)){
                foreach ($this->products as $product) {
                    $deliveryZeroPrice += ($product->product->type_id == 1003 || $product->product->type_id == 1007) ? ($product->variant->priceValue) * $product->count : 0;
                }
            }
            if($deliveryZeroPrice > 3000){
                $price = $price - 150;
            }
        }

        return $price;*/
        return Yii::$app->action->calcDeliveryPriceWithDiscont($price);
    }

    public function getStoreListJson(){
        $storeList = [];
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                $storeList[$product->product->id] = $product->product->storeList;
            }
        }

        if(!$storeList){
            return false;
        }else{
            $storeListForJs = [];
            foreach($storeList as $productId => $storeItem){
                if(!empty($storeItem)){
                    foreach ($storeItem as $item) {
                        $storeListForJs[$productId][] = [
                            'address' => $item['address'],
                            'id' => $item['address_id'],
                            'product_id' => $productId,
                        ];
                    }
                }
            }
            return json_encode($storeListForJs);
        }
    }
}
