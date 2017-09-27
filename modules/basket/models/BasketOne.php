<?php

namespace app\modules\basket\models;

use Yii;
use yii\db\Query;
use yii\behaviors\TimestampBehavior;
use app\modules\common\models\Api;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsTypes;
use app\modules\catalog\models\CategoryLinks;
use app\modules\catalog\models\Category;
use app\modules\managment\models\Shops;

/**
 * This is the model class for table "basket".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $session_id
 * @property integer $last_update
 * @property integer $delivery_id
 * @property integer $address_id
 * @property integer $payment_id
 * @property integer $status
 */
class BasketOne extends \yii\db\ActiveRecord
{
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
            [['session_id', 'delivery_id', 'address_id', 'payment_id', 'status'], 'required'],
            [['user_id', 'date_create', 'last_update', 'delivery_id', 'address_id', 'payment_id', 'status'], 'integer'],
            [['session_id'], 'string', 'max' => 64],
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
            'address_id' => 'Address ID',
            'payment_id' => 'Payment ID',
            'status' => 'Status',
        ];
    }

    public static function setBasketId($basketId){
        Yii::$app->session;
        $_SESSION['shop']['basket']['basketId'] = $basketId;
    }

    public static function getBasketId(){
        if(!isset(Yii::$app->session['shop']['basket']['basketId']) || empty(Yii::$app->session['shop']['basket']['basketId'])){
            self::findBasket();
        }
        return Yii::$app->session['shop']['basket']['basketId'];
    }

    public static function findBasket(){
        $basket = false;
        if(Yii::$app->user->identity) {
            $basket = self::find()
                ->where([
                    'status' => 0,
                    'user_id' => Yii::$app->user->identity->id,
                ])
                ->one();
        }
        if(!$basket){
            $basket = self::find()
                ->where([
                    'status' => 0,
                    'session_id' => Yii::$app->session->id,
                ])
                ->one();

            if(!$basket){
                $basket = new BasketOne();
                $basket->status = 0;
                $basket->session_id = Yii::$app->session->id;
                $basket->user_id = (Yii::$app->user->identity)?Yii::$app->user->identity->id:NULL;
                $basket->delivery_id = 0;
                $basket->address_id = 0;
                $basket->payment_id = 0;
                if($basket->save()){
                    return $basket;
                }else{
                    return false;
                }
            }
        }else{
            $basketSessionCurrentUser = self::find()
                ->where([
                    'status' => 0,
                    'user_id' => NULL,
                    'session_id' => Yii::$app->session->id,
                ])
                ->all();

            if(!$basketSessionCurrentUser){

            }else{
                foreach($basketSessionCurrentUser as $bas){
                    $bas->delete();
                }
            }
        }
        self::findActivityBasketProducts($basket->id);
        self::setBasketId($basket->id);
        return $basket;
    }

    public static function findActivityBasketProducts($basketId){
        $basketProducts = BasketProducts::find()->where(['basket_id' => $basketId])->all();

        if(!$basketProducts){

        }else{
            $variantIds = [];
            foreach($basketProducts as $basketProduct){
                $variantIds[] = $basketProduct->variant_id;
            }
            $activeVariants = GoodsVariations::find()
                ->select([
                    'goods.name AS name',

                    'goods_variations.price AS productPrice',
                    'goods_variations.id AS variantId',

                    'goods.id AS productId',
                    'category_links.category_id AS categoryId',
                ])
                ->leftJoin(Goods::tableName(),'goods_variations.good_id = goods.id')
                ->leftJoin(CategoryLinks::tableName(),'category_links.product_id = goods.id')
                ->leftJoin(Category::tableName(),'category_links.category_id = category.id')
                ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
                ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
                ->leftJoin('shop_group_related','shop_group_related.shop_group_id = shop_group.id')
                ->leftJoin(Shops::tableName(),'shops.id = shop_group_related.shop_id')
                ->where([
                    'shops.status' => 1,
                    'shop_group.status' => 1,
                    'goods.status' => 1,
                    'goods.show' => 1,
                    'goods.confirm' => 1,
                    'goods_variations.status' => 1,
                    'category.active' => 1,
                ])
                ->andWhere(['IN','goods_variations.id',$variantIds])
                ->andWhere(['OR',
                    ['>','good_count(`goods`.`id`, `goods_variations`.`id`)',1],
                    ['IS','good_count(`goods`.`id`, `goods_variations`.`id`)',NULL]
                ])->all();

            if(!$activeVariants){
                foreach($basketProducts as $basketProduct){
                    $basketProduct->delete();
                }
            }else{
                $activeVariantIds = [];
                foreach($activeVariants as $activeVariant){
                    $activeVariantIds[] = $activeVariant->variantId;
                }
                foreach($basketProducts as $basketProduct){
                    if(!in_array($basketProduct->variant_id,$activeVariantIds)){
                        $basketProduct->delete();
                    }
                }
            }
        }
    }

    public static function findActivityProduct($variantId){
        $activeVariant = GoodsVariations::find()
            ->select([
                'goods_variations.price AS productPrice',
                'goods_variations.id AS variantId',

                'goods.id AS productId',
                'good_count(`goods`.`id`, `goods_variations`.`id`) AS count',
            ])
            ->leftJoin(Goods::tableName(),'goods_variations.good_id = goods.id')
            ->leftJoin(CategoryLinks::tableName(),'category_links.product_id = goods.id')
            ->leftJoin(Category::tableName(),'category_links.category_id = category.id')
            ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
            ->leftJoin('shop_group_related','shop_group_related.shop_group_id = shop_group.id')
            ->leftJoin(Shops::tableName(),'shops.id = shop_group_related.shop_id')
            ->where([
                'shops.status' => 1,
                'goods.status' => 1,
                'goods.show' => 1,
                'goods.confirm' => 1,
                'goods_variations.status' => 1,
                'category.active' => 1,
                'goods_variations.id' => $variantId,
            ])
            ->andWhere(['OR',
                ['>','good_count(`goods`.`id`, `goods_variations`.`id`)',1],
                ['IS','good_count(`goods`.`id`, `goods_variations`.`id`)',NULL]
            ])->all();

        if(!$activeVariant){
            return false;
        }
        return $activeVariant[0];
    }

    public static function findProductInBasket($variantId){
        return BasketProducts::find()->where(['basket_id' => self::getBasketId(),'variant_id' => $variantId])->one();
    }

    public static function findProductsInBasket(){
        return BasketProducts::find()->where(['basket_id' => self::getBasketId()])->indexBy('id')->all();
    }

    public static function setRecalculateBasket(){
        \Yii::$app->session['recalculate-basket-yii'] = 'yes';
    }

    public static function setDisableRecalculateBasket(){
        \Yii::$app->session['recalculate-basket-yii'] = 'no';
    }

    public static function removeProductBasket($id){
        $basketItem = BasketProducts::find()->where(['id' => $id])->one();
        if(!$basketItem){

        }else{
            $basketItem->delete();
        }

        self::setRecalculateBasket();
        return true;
    }

    public static function addProductBasket($id,$variant,$count){
        $productVariant = self::findActivityProduct($variant);

        if(!$productVariant || ($productVariant->count > 1 && $productVariant->count < $count)){
            return 'false';
        }

        $findProduct = self::findProductInBasket($variant);

        if(!$findProduct){
            $findProduct = new BasketProducts();
            $findProduct->basket_id = self::getBasketId();
            $findProduct->product_id = $id;
            $findProduct->variant_id = $variant;
            $findProduct->count = $count;
            if($findProduct->save()){

            }else{
                return $findProduct->errors;
            }
        }else{
            if(isset($findProduct->count) && $findProduct->count != $count){
                $findProduct->count = $count;
                if($findProduct->save()){

                }else{
                    return $findProduct->errors;
                }
            }
        }

        self::setRecalculateBasket();
        self::initBasket();
        return $findProduct->id;
    }

    public static function getDeliveryPriceSumm($ids,$deliveryId){
        return (new Query())
            ->from('deliveries_prices')
            ->select(['SUM(`price`) AS deliverySumm'])
            ->where([
                'delivery_id' => $deliveryId,
                'status' => 1,
            ])
            ->andWhere(['IN','good_type_id', $ids])
            ->all();
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

    /**
     * @return array
     * delivery address list = personal address + clubs address
     */
    public static function getDelivery(){
        $result = [];
        $extremeAddress = self::getClubDelivery();

        // Проверка пользователя;
        if(\Yii::$app->user->identity){
            $userAddress = (new Query())->from('address')
                ->select([
                    '\'1006\' AS `id`',
                    'id AS value',
                    'CONCAT_WS(\', \', street, house, room) AS address',
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

    public static function getDeliveryTimeRules(){
        return [
            'dayStart'              => strtotime('2016-04-22'),

            'postponedUntilDate'    => [
                'default'           => false,
                'other'             => true,
                'shop'              => [
                    10000130 => strtotime('2016-04-22'),
                ],
            ],
            'productType'           => [
                'default'           => false,
                'other'             => true,
                'type'              => [
                    1006,
                ],
            ],
        ];
    }

    public static function getDeliveryTime($type_id, $delivery_id, $soon = false){
        $times = array();
        $dayCurrent = strtotime(date('Y-m-d'));
        $dayPlus = 0;

        if($type_id == 1006){
            $dayStart = self::getDeliveryTimeRules();
            $dayStart = $dayStart['dayStart'];

            if($dayStart > $dayCurrent){
                $dayPlus = ($dayStart - $dayCurrent) / 86400;
            }
        }

        for ( $i = (( date("G") >= 8 ) ? 1:0); $i <= (10 + $dayPlus); $i++ ) {
            $day = date("d.m.Y", mktime(0, 0, 0, date("n"), date("j") + $i, date("Y")));
            if(!in_array($day,Yii::$app->params['blockedDeliveryDays'])){
                for ($j = 0; $j <= 6; $j += 2) {

                    $time = mktime(12 + $j, 0, 0, date("n"), date("j") + $i, date("Y"));

                    // Товары и спортпит на клубы
                    if( ( $type_id == 1001 || $type_id == 1005 ) && $delivery_id == 1003 ) {
                        // Только понедельник и четверг;
                        if (strtotime(date('Y-m-d')) + 3600 * 24 < strtotime($day)) {
                            if ((date("w", $time) == 2 || date("w", $time) == 5) && $j >= 6) {
                                $times[$day][$time] = date("H:00", $time) . ' – ' . date("H:00", $time + 7200);

                            }

                        }

                    }elseif ($type_id == 1006 && $delivery_id == 1003) {
                        if ($dayStart <= strtotime( $day )) {
                            // Только понедельник и четверг;
                            if ((date("w", $time) == 2) and $j >= 6) {
                                $times[$day][$time] = date("H:00", $time) . ' – ' . date("H:00", $time + 7200);
                            }
                        }

                    }elseif ($type_id == 1008) {
                        $times[$day][$time] = '3-10 days';

                    }else{
                        if( ( date( "w", $time ) > 0 ) ){
                            $times[$day][$time] = date("H:00", $time).' – '.date("H:00", $time + 7200);
                        }

                    }
                }
            }
        }

        // Вывод данных;
        return ($soon ? current($times) : $times);
    }

    public static function setDeliveryTime($type,$time){
        $_SESSION['shop']['basket']['groupTypeProductValue'][$type] = $time;
    }

    public static function setDelivery($id){
        \Yii::$app->session['shop']['basket']['deliveryId'];
        $_SESSION['shop']['basket']['deliveryId'] = $id;

        $basket = self::find()
            ->where([
                'status' => 0,
                'id' => \Yii::$app->session['shop']['basket']['basketId'],
            ])
            ->one();
        if(!$basket){

        }else{
            $basket->delivery_id = $id;
            $basket->save();
        }
    }

    public static function setAddress($id){
        \Yii::$app->session['shop']['basket']['addressId'];
        $_SESSION['shop']['basket']['addressId'] = $id;

        $basket = self::find()
            ->where([
                'status' => 0,
                'id' => \Yii::$app->session['shop']['basket']['basketId'],
            ])
            ->one();
        if(!$basket){

        }else{
            $basket->address_id = $id;
            $basket->save();
        }
    }

    public static function setPayment($id){
        \Yii::$app->session['shop']['basket']['paymentId'];
        $_SESSION['shop']['basket']['paymentId'] = $id;

        $basket = self::find()
            ->where([
                'status' => 0,
                'id' => \Yii::$app->session['shop']['basket']['basketId'],
            ])
            ->one();
        if(!$basket){

        }else{
            $basket->payment_id = $id;
            $basket->save();
        }
    }

    public static function setPromoCode($code){
        \Yii::$app->session['shop']['basket'];
        $_SESSION['shop']['basket']['discount'] = 0;
        $_SESSION['shop']['basket']['promoCodeMessage'] = '';

        $promo = (new PromoCode())
            ->find()
            ->select([
                'codes_types.discount AS discount',
                'codes_types.fee',
                'codes.count',
                'codes.date_begin',
                'codes.date_end',
            ])
            ->leftJoin(PromoCodeType::tableName(),'codes_types.id = codes.type_id')
            ->where([
                'codes.code' => $code,
                'codes.status' => 1,
                'codes_types.status' => 1,
            ])
            ->one();

        if(!$promo){
            $_SESSION['shop']['basket']['promoCodeMessage'] = 'Промо-код не найден';
            $_SESSION['shop']['basket']['promoCodeStatus'] = 'error';
        }else{
            if($promo->count > 0 && strtotime($promo->date_begin) <= time() && strtotime($promo->date_end) >= time()){
                $_SESSION['shop']['basket']['discount'] = $promo->discount;
                $_SESSION['shop']['basket']['promoCode'] = $code;
                $_SESSION['shop']['basket']['promoCodeMessage'] = 'Промо-код активирован';
                $_SESSION['shop']['basket']['promoCodeStatus'] = 'ok';
            }else{
                $_SESSION['shop']['basket']['promoCodeMessage'] = 'Промо-код не активный';
                $_SESSION['shop']['basket']['promoCodeStatus'] = 'error';
            }
        }
        return ['status' => $_SESSION['shop']['basket']['promoCodeStatus'],'message' => $_SESSION['shop']['basket']['promoCodeMessage']];
    }

    public static function getUserCards(){
        if(\Yii::$app->user->identity){
            $userCards = (new Query())->from('users_cards')
                ->where(['user_id' => \Yii::$app->user->identity->id])
                ->andWhere(['status' => 1])
                ->orderBy('id DESC')
                ->all();

            return $userCards;
        }

        return false;
    }

    public static function getUserExtremefitness(){
        if (\Yii::$app->user->identity->extremefitness) {
            // Загрузка данных клиента WebFitness;
            $result = new Api();
            return $result->client_info(\Yii::$app->user->identity->extremefitness);
        }

        return false;
    }

    public static function recalculateResultBasket(){
        $basketSession = \Yii::$app->session['shop']['basket'];
        $basketSession['moneySum'] = $basketSession['money'] - $basketSession['discountSum'] - $basketSession['bonus'];
        $basketSession['moneySum'] += $basketSession['deliveryPrice'];

        return true;
    }

    public static function findVariantByTags($productId,$tagIds){
        $variant = (new Query)
            ->from('tags')
            ->select(['goods_variations.id','COUNT(*) AS count'])
            ->leftJoin('tags_links','tags_links.tag_id = tags.id')
            ->leftJoin('goods_variations','tags_links.variation_id = goods_variations.id')
            ->leftJoin('goods','goods.id = goods_variations.good_id')
            ->where(['goods.id' => $productId])
            ->andWhere(['IN','tags.id', $tagIds])
            ->groupBy('goods_variations.id')
            ->orderBy('count DESC')
            ->limit(1)
            ->all();

        if(!$variant){
            return false;
        }

        return $variant[0]['id'];
    }

    /**
     * initialize and recalculate basket
     */
    public static function initBasket()
    {
        //unset(\Yii::$app->session['basket-yii']);
        $basketSession = \Yii::$app->session['shop']['basket'];

        //$_SESSION['recalculate-basket-yii'] = 'yes';
        //\Yii::$app->session['recalculate-basket-yii'] = 'yes';

        if(
            (!isset(Yii::$app->session['shop']['basket']) || empty(Yii::$app->session['shop']['basket'])) ||
            (isset(\Yii::$app->session['recalculate-basket-yii']) && \Yii::$app->session['recalculate-basket-yii'] == 'yes')
        ){

            $basketSession = [
                'basketId' => self::getBasketId(),
                'products' => [],
                'productsShort' => [],
                'variantsShort' => [],
                'basketItems' => [],
                'count' => 0,
                'money' => 0,
                'promoPrice' => 0,
                'promoCode' => isset($basketSession['promoCode'])?$basketSession['promoCode']:'',
                'promoCodeMessage' => isset($basketSession['promoCodeMessage'])?$basketSession['promoCodeMessage']:'',
                'discount' => isset($basketSession['discount'])?$basketSession['discount']:0,
                'discountSum' => 0,
                'deliveryId' => (isset($basketSession['deliveryId']))?$basketSession['deliveryId']:Yii::$app->params['deliveryDefaultId'],
                'deliveryPrice' => 0,
                'addressId' => (isset($basketSession['addressId']))?$basketSession['addressId']:0,
                'paymentId' => (isset($basketSession['paymentId']))?$basketSession['paymentId']:0,
                'extremefitness' => (isset($basketSession['extremefitness']))?$basketSession['extremefitness']:0,
                'moneySum' => 0,
                'moneyAll' => 0,
                'bonus' => 0,
                'groupTypeProductValue' => isset($basketSession['groupTypeProductValue'])?$basketSession['groupTypeProductValue']:false,
                'productTypeForJob' => Yii::$app->params['shopDeliveryIds'],
                'productTypeForJobTitle' => [],
                'countInBasket' => [],
                'comment' => isset($basketSession['comment'])?$basketSession['comment']:'',
            ];

            $basketBaseProducts = self::findProductsInBasket();
            if(!empty($basketBaseProducts)){
                foreach($basketBaseProducts as $basketItems){
                    $basketSession['productsShort'][] = $basketItems->product_id;
                    $basketSession['variantsShort'][] = $basketItems->variant_id;

                    $basketSession['basketItems'][] = $basketItems->id;

                    $basketSession['activeVariants'][$basketItems->id] = $basketItems->variant_id;
                    $basketSession['countInBasket'][$basketItems->id] = $basketItems->count;
                    $basketSession['count'] += $basketItems->count;
                }
            }
            $_SESSION['shop']['basket'] = $basketSession;

            if(!empty($basketSession['basketItems'])){
                $productsListFull = self::getProductsListFull($basketSession['basketItems']);

                $basketSession['products'] = $productsListFull;

                foreach($productsListFull as $basketItemId => $item){
                    $item['variantId'] = $basketBaseProducts[$basketItemId]->variant_id;
                    $basketSession['products'][$basketItemId]['variantId'] = $item['variantId'];
                    $basketSession['productTypeList'][$item['productType']][] = $basketItemId;
                    $productsListFull[$basketItemId]['money'] = $productsListFull[$basketItemId]['priceClear'][$item['variantId']] * $basketSession['countInBasket'][$basketItemId];
                    $basketSession['money'] += $productsListFull[$basketItemId]['money'];

                    // Проверка скидки на товар;
                    if ($basketSession['discount'] && $productsListFull[$basketItemId]['productDiscount']) {
                        // Рассчет стоимости всех товаров со скидкой;
                        $basketSession['promoPrice'] += $productsListFull[$basketItemId]['money'];
                        // Рассчет скидки на товар;
                        $basketSession['products'][$basketItemId]['discountSum'] = ceil($productsListFull[$basketItemId]['priceClear'][$item['variantId']] * ($basketSession['discount'] / 100));
                        // Рассчет цены товара со скидкой;
                        $basketSession['products'][$basketItemId]['discount_price'] = $productsListFull[$basketItemId]['price'][$item['variantId']];
                        $basketSession['products'][$basketItemId]['discount_money'] = $basketSession['products'][$basketItemId]['discount_price'] * $basketSession['countInBasket'][$basketItemId];
                        // Рассчет скидки на заказ;
                        $basketSession['discountSum'] += $basketSession['products'][$basketItemId]['discountSum'] * $basketSession['countInBasket'][$basketItemId];
                    } else {
                        // Обнуление скидки;
                        $basketSession['products'][$basketItemId]['discountSum'] = 0;
                    }

                    // Проверка возможности покупки товара за бонусы;
                    if (isset(Yii::$app->user->identity->bonus) && Yii::$app->user->identity->bonus > 0 && $productsListFull[$basketItemId]['productBonus']) {
                        // Рассчет скидки по бонусам на позицию товар;
                        $basketSession['products'][$basketItemId]['bonus'] = min(($productsListFull[$basketItemId]['priceClear'][$item['variantId']] - $basketSession['products'][$basketItemId]['discountSum'] ) * $basketSession['countInBasket'][$basketItemId], Yii::$app->user->identity->bonus - $basketSession['bonus']);
                        // Рассчет скидки по бонусам на единицу товара;
                        $basketSession['products'][$basketItemId]['bonus'] = floor($basketSession['products'][$basketItemId]['bonus'] / $basketSession['countInBasket'][$basketItemId]);
                        // Рассчет скидки по бонусам на всю корзину;
                        $basketSession['bonus'] += ($basketSession['products'][$basketItemId]['bonus'] * $basketSession['countInBasket'][$basketItemId]);
                    } else {
                        // Обнуление скидки по бонусам;
                        $basketSession['products'][$basketItemId]['bonus'] = 0;
                    }
                    // Рассчет комиссии за продажу единицу товара;
                    $basketSession['products'][$basketItemId]['commission'] = Goods::getProductCommission($item['productId'],$item['variantId'],$basketSession['products'][$basketItemId]['discountSum'],$productsListFull[$basketItemId]);
                }
                // Рассчет общей стоимости заказа;
                $basketSession['moneySum'] = $basketSession['money'] - $basketSession['discountSum'] - $basketSession['bonus'];
                // Рассчет общей стоимости заказа с учетом оплаты абонемента ExtremeFitness;
                //$basketSession['moneyAll'] = $basketSession['moneySum'] + $basketSession['extremefitness'];

                foreach($basketSession['productTypeForJob'] as $deliveryVariantId => $deliveryVariantProductTypeList){
                    foreach($deliveryVariantProductTypeList as $j => $deliveryTypeGroup){
                        foreach($deliveryTypeGroup as $k => $deliveryItemId){
                            if(!isset($basketSession['productTypeList'][$deliveryItemId])){
                                unset($basketSession['productTypeForJob'][$deliveryVariantId][$j][$k]);
                                if(empty($basketSession['productTypeForJob'][$deliveryVariantId][$j])){
                                    unset($basketSession['productTypeForJob'][$deliveryVariantId][$j]);
                                }
                            }
                        }
                    }
                }

                $productsTypes = GoodsTypes::find()->select(['id','name'])->where(['status' => 1])->indexBy('id')->all();
                foreach($basketSession['productTypeForJob'] as $deliveryVariantId => $deliveryVariantProductTypeList){
                    foreach($deliveryVariantProductTypeList as $j => $deliveryTypeGroup){
                        foreach($deliveryTypeGroup as $k => $deliveryItemId){
                            $basketSession['productTypeForJobTitle'][$deliveryVariantId][$j][] = $productsTypes[$deliveryItemId]->name;
                        }
                    }
                }

                // Рассчитываем общую стоимость доставки
                $deliveryItemPriceList = [];
                foreach($basketSession['productTypeForJob'][$basketSession['deliveryId']] as $deliveryItem){
                    $deliveryItemPriceList[] = current($deliveryItem);
                }
                if(!empty($deliveryItemPriceList)){
                    $basketSession['deliveryPrice'] = self::getDeliveryPriceSumm($deliveryItemPriceList,$basketSession['deliveryId']);
                    if(!$basketSession['deliveryPrice']){
                        $basketSession['deliveryPrice'] = 0;
                    }else{
                        $basketSession['deliveryPrice'] = $basketSession['deliveryPrice'][0]['deliverySumm'];
                    }
                }
                $basketSession['moneySum'] += $basketSession['deliveryPrice'];

                $basketSession['productTypeList'][$item['productType']][] = $basketItemId;

                foreach($basketSession['productTypeList'] as $productTypeId => $productType){
                    if(isset($basketSession['groupTypeProductValue'][$productTypeId]) && !empty($basketSession['groupTypeProductValue'][$productTypeId])){

                    }else{
                        $basketSession['groupTypeProductValue'][$productTypeId] = false;
                    }
                }
            }

            $_SESSION['shop']['basket'] = $basketSession;
            self::setDisableRecalculateBasket();

        }
        return $basketSession;
    }

    public static function getProductsListFull($ids){
        $result = [];
        $variants = (new Query())
            ->from('goods_variations')
            ->select([
                'goods_variations.good_id as productId',
                'goods_variations.price as productPrice',
                'goods_variations.comission as productCommission',
                '`goods_variations`.`price`+`goods_variations`.`comission` as productResultPrice',
                'goods_variations.id as variantId',
                'get_tags(`goods_variations`.`id`) AS `variantName`',
                'tags.id as tagId',
                'tags.value as tagValue',

                'goods_counts.count as count',

                'tags_groups.id as tagGroupId',
                'tags_groups.name as tagGroupName',

                'goods.name as productName',
                'goods.discount as productDiscount',
                'goods.bonus as productBonus',
                'goods.type_id as productType',
                'goods.count_pack as countPack',
                'shops.comission_id as commissionId',
                'basket_products.id as basketProductId',
                //'basket_products.variant_id as variantId',

                'category_links.category_id AS categoryId',
            ])
            ->leftJoin('basket_products','basket_products.product_id = goods_variations.good_id')
            ->leftJoin('goods_counts','goods_counts.variation_id = goods_variations.id')
            ->leftJoin('tags_links','tags_links.variation_id = goods_variations.id')
            ->leftJoin('tags','tags_links.tag_id = tags.id')
            ->leftJoin('tags_groups','tags_groups.id = tags.group_id')
            ->leftJoin('goods','goods.id = goods_variations.good_id')

            ->leftJoin('shop_group_variant_link','shop_group_variant_link.product_id = goods.id')
            ->leftJoin('shop_group','shop_group.id = shop_group_variant_link.shop_group_id')
            ->leftJoin('shop_group_related','shop_group_related.shop_group_id = shop_group.id')
            ->leftJoin(Shops::tableName(),'shops.id = shop_group_related.shop_id')

            ->leftJoin('category_links','category_links.product_id = goods.id')
            ->where([
                'goods_variations.status' => 1,
                'tags_groups.status' => 1,
                'tags_groups.type' => 1,
                'tags_groups.show' => 1,
            ])
            ->andWhere(['OR',
                ['>','good_count(`goods`.`id`, `goods_variations`.`id`)',1],
                ['IS','good_count(`goods`.`id`, `goods_variations`.`id`)',NULL]
            ])
            ->orderBy('basket_products.id')
            ->andWhere(['IN','basket_products.id',$ids])
            ->all();

        if(!empty($variants)){
            foreach($variants as $variant){
                $result[$variant['basketProductId']]['price'][$variant['variantId']] = 100;//Goods::getPrice($variant['productId'],$variant['variantId'],\Yii::$app->session['shop']['basket']['discount'],$variant);
                $result[$variant['basketProductId']]['priceClear'][$variant['variantId']] = 100;//Goods::getPrice($variant['productId'],$variant['variantId'],0,$variant);

                $result[$variant['basketProductId']]['productName'] = $variant['productName'];
                $result[$variant['basketProductId']]['variantName'][$variant['variantId']] = $variant['variantName'];

                $result[$variant['basketProductId']]['productId'] = $variant['productId'];
                $result[$variant['basketProductId']]['variantId'] = $variant['variantId'];
                $result[$variant['basketProductId']]['productType'] = $variant['productType'];
                $result[$variant['basketProductId']]['countPack'] = $variant['countPack'];
                $result[$variant['basketProductId']]['productBonus'] = $variant['productBonus'];
                $result[$variant['basketProductId']]['productDiscount'] = $variant['productDiscount'];
                $result[$variant['basketProductId']]['commissionId'] = $variant['commissionId'];

                //Цена
                $result[$variant['basketProductId']]['productPrice'] = $variant['productPrice'];
                //Комиссия
                $result[$variant['basketProductId']]['commission'] = $variant['productCommission'];
                //Цена для клиента
                $result[$variant['basketProductId']]['resultPrice'] = $variant['productResultPrice'];

                //Проверка на наличие
                if(!isset($result[$variant['basketProductId']]['countAll'])){
                    $result[$variant['basketProductId']]['countAll'] = 0;
                }
                if($variant['count'] === NULL || $variant['count'] > 0){
                    $variant['count'] = Yii::$app->params['defaultCountInfiniteProductVariant'];
                    $result[$variant['basketProductId']]['countAll'] ++;
                }

                //Устанавливаем для тэга значение по умолчанию - неактивен
                if(!isset($result[$variant['basketProductId']]['tags'][$variant['tagId']]['active'])){
                    $result[$variant['basketProductId']]['tags'][$variant['tagId']]['active'] = false;
                }

                //Количество варианта товара с данным свойством (тэгом)
                $result[$variant['basketProductId']]['variants'][$variant['variantId']][$variant['tagId']] = $variant['count'];
                $result[$variant['basketProductId']]['counts'][$variant['variantId']] = $variant['count'];

                //Устанавливаем для тэга значение активности
                if(!$result[$variant['basketProductId']]['tags'][$variant['tagId']]['active'] && ($variant['count'] === NULL || $variant['count'] > 0)){
                    $result[$variant['basketProductId']]['tags'][$variant['tagId']]['active'] = true;
                }

                //Значение тэга
                $result[$variant['basketProductId']]['tags'][$variant['tagId']]['value'] = $variant['tagValue'];
                //Название свойства (тэга)
                $result[$variant['basketProductId']]['tagGroupName'][$variant['tagGroupId']] = $variant['tagGroupName'];
                //Свойства товара по группам
                $result[$variant['basketProductId']]['tagGroupWithValue'][$variant['tagGroupId']][$variant['tagId']] = $variant['tagValue'];

                $result[$variant['basketProductId']]['categoryId'] = $variant['categoryId'];
            }
        }

        return $result;
    }

    public static function getSmallBasket(){
        $responce = [
            'count' => array_sum(Yii::$app->controller->basket['countInBasket']),
            'price' => Yii::$app->controller->basket['money'],
        ];

        return $responce;
    }
    // Выпадашка списко товара в корзине;
    public static function getGoodsBasket(){
        $goods = Yii::$app->session['shop']['basket']['products'];
        $count = Yii::$app->session['shop']['basket']['countInBasket'];
        $goodsBasket = array();

        foreach($goods as $key=> $value) {
            $goodsBasket[$key]['id'] =  $value['productId'];
            $goodsBasket[$key]['name'] =  $value['productName'];
            $goodsBasket[$key]['price']  =  $value['price'][$value['variantId']];
            $goodsBasket[$key]['count']=  $count[$key];
        }
        return $goodsBasket;
    }

}
