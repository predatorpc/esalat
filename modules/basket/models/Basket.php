<?php
namespace app\modules\basket\models;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\models\Address;
use app\modules\common\models\Deliveries;
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
 * @property string $current_club
 * @property integer $status
 */
class Basket extends \yii\db\ActiveRecord
{
    protected $basketError = [];

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
            [['user_id', 'date_create', 'last_update', 'delivery_id', 'address_id', 'payment_id', 'promo_code_id', 'status', 'bonus_pay'], 'integer'],
            [['session_id', 'delivery_id', 'address_id', 'payment_id', 'status'], 'required'],
            [['delivery_price'], 'number'],
            [['comment'], 'string'],
            [['session_id'], 'string', 'max' => 64],
            [['time_list','current_club'], 'string', 'max' => 255],
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
            'current_club' => 'Current Club',
            'status' => 'Status',
        ];
    }

    public function start(){
        if(!empty($this->products)){
            $this->setEmptyProducts();
            $this->setBasketProductPrice();

            if(!empty($this->user_id)){
                $this->setBasketProductBonus();
                if(!empty($this->promo_code_id)){
                    $this->setBasketProductPriceDiscount();
                }
                $this->setBasketProductCommissionValue();
            }
        }
    }

    /* -------------- Relations ------------- */

    public function getProducts(){
        return $this->hasMany(BasketProducts::className(), ['basket_id' => 'id'])->where(['status'=>1]);
    }

    public function getEmptyProducts(){
        return $this->hasMany(BasketProducts::className(), ['basket_id' => 'id'])->where(['status'=>0]);
    }

    public function getVariants(){
        return $this->hasMany(GoodsVariations::className(), ['id' => 'variant_id'])
            ->viaTable(BasketProducts::tableName(),['basket_id' => 'id']);
    }

    public function getGoods(){
        return $this->hasMany(Goods::className(), ['id' => 'product_id'])
            ->viaTable(BasketProducts::tableName(),['basket_id' => 'id']);
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id' => 'user_id']);
    }

    public function getPromoCode(){
        return $this->hasOne(Codes::className(),['id' => 'promo_code_id']);
    }

    public function getPromoCodeValue(){
        return $this->promoCode ? $this->promoCode->code : false;
    }

    public function getAddress(){
        return $this->hasOne(Address::className(),['id' => 'address_id']);
    }

    public function getPayment(){
        return $this->payment_id;
    }

    public function getDelivery(){
        return $this->hasOne(Deliveries::className(),['id' => 'delivery_id']);
    }

    /* -------------- END >> Relations ------------- */

    /* -------------- Set Value Basket Products ------------- */
    public function setEmptyProducts(){
        foreach ($this->products as $i => $product) {
            $counts = $product->variant->maxCounts;
            //print_R($product);
            $allCount=0;
            foreach ($counts as $count){
                $allCount = $allCount+$count->count;
            }
            if($allCount<=0 ||  $product->variant->status==0 || $product->product->status==0 ){
                $product->status=0;
                $product->save(true);
            }
            if($product->count > $allCount){
                $product->count = $allCount;
                $product->save(true);
            }
            if($product->count==0){

                //unset($this->products[$i]);
                //$product->status=0;
                //$product->save(true);
                $this->removeProduct($product->id);
            }
        }
        foreach ($this->emptyProducts as $i => $emptyProduct) {
            $emptyCounts = $emptyProduct->variant->maxCounts;
            $emptyAllCount=0;
            foreach ($emptyCounts as $emptyCount){
                $emptyAllCount = $emptyAllCount+$emptyCount->count;
            }
            if($emptyAllCount>0 && $emptyProduct->variant->status==1 && $emptyProduct->product->status==1){
                $emptyProduct->status=1;
                $emptyProduct->save(true);
            }
        }
    }

    public function setBasketProductPrice(){
        foreach ($this->products as $i => $product) {
            $product->price = $product->variant->priceValue;
            $product->priceDiscount = $product->price;
            $product->bonus = 0;
        }
    }

    protected function setBasketProductBonus(){
        $basketBonusValue = 0;
        foreach ($this->products as $i => $product) {
            if ($product->product->wishListGood) {
                    if((Yii::$app->user->identity->bonus/($product->price/100))>=30){
                        $min = min($product->price * $product->count,$this->user->bonus - $basketBonusValue);
                        $product->bonus = floor($min / $product->count);

                        $basketBonusValue += $product->bonus * $product->count;

                        $product->priceDiscount = $product->price - $product->bonus;
                    }
            }
        }
        if($this->bonus_pay==1){
            foreach ($this->products as $i => $product){
                if ($product->product->wishListGood) {
                    continue;
                }
                if($product->count>0){
                    if($product->product->bonus == 1){
                        $min = min($product->price * $product->count,$this->user->bonus - $basketBonusValue);
                        $product->bonus = floor($min / $product->count);

                        $basketBonusValue += $product->bonus * $product->count;
                    }
                    $product->priceDiscount = $product->price - $product->bonus;
                }
            }
        }

    }

    protected function setBasketProductPriceDiscount(){
        foreach ($this->products as $i => $product) {
            if($product->variant->product->discount == 1){
                $product->priceDiscount = floor(($product->price - $product->bonus) * (100 - $this->promoCode->type->discount)/100);
            }
        }
    }

    public function setBasketProductCommissionValue($discountPercent = 0){
        foreach ($this->products as $i => $product) {
            if($product->product->shop->comission_id == 1001){
                $product->commission = round(ceil($product->price * $product->product->count_pack) - ($product->price * $product->product->count_pack * (1 - $product->variant->comission / 100)), 2);
            }
            if($product->product->shop->comission_id == 1002) {
                $product->commission = round(ceil(($product->price + $product->price * $product->variant->comission / 100) * $product->product->count_pack) - ($product->price * $product->product->count_pack) - $product->priceDiscount, 2);
            }
        }
    }


    /* -------------- END >> Set Value Basket Products ------------- */

    /* -------------- Results Basket ------------- */

    public function getBasketPrice(){
        $sum = 0;
        foreach ($this->products as $i => $product) {
            $sum += $product->price * $product->count;
        }
        return $sum;
    }

    public function getBasketBonus(){
        $sum = 0;
        foreach ($this->products as $i => $product) {
            $sum += $product->bonus * $product->count;
        }
        return $sum;
    }

    public function getBasketDiscount(){
        $sum = 0;
        foreach ($this->products as $i => $product) {
            $sum += ($product->price - $product->priceDiscount - $product->bonus) * $product->count;
        }
        return $sum;
    }

    public function getBasketPriceDiscount(){
        $sum = 0;
        foreach ($this->products as $i => $product) {
            $sum += $product->priceDiscount * $product->count;
        }
        return $sum;
    }

    public function getProductTypes(){
        $result = [];

        if(!empty($this->products)){
            foreach ($this->products as $product) {
                $result[$product->product->type_id][] = $product;
            }
        }
        return $result;
    }

    public function getBasketTypePrice(){
        $result = [];
        foreach ($this->products as $i => $product) {
            $result[$product->product->type_id] = $product->price * $product->count + (!empty($result[$product->product->type_id]) ? $result[$product->product->type_id] : 0);
        }
        return $result;
    }

    public function getCount(){
        $result = 0;

        if(!empty($this->products)){
            foreach ($this->products as $product) {
                $result += $product->count;
            }
        }
        return $result;
    }

    /* -------------- END >> Results Basket ------------- */

    public function findByUserId($userId){
        return self::find()->where(['user_id' => $userId])->all();
    }


    /* -------------- Actions Basket ------------- */

    protected function addProduct(BasketProducts $product){
        $basketProduct = $this->getProductByVariantId($product->variant_id);
        if(!$basketProduct){
            $basketProduct = $product;
        }
        $basketProduct->list_id = !empty($product->list_id) ? $product->list_id : $basketProduct->list_id;
        $basketProduct->tool = !empty($product->tool) ? $product->tool : $basketProduct->tool;
        $basketProduct->count = $product->count;
        if($basketProduct->save()){
            return $basketProduct->id;
        }
    }

    public function removeProduct($id){
        $basketProduct = $this->getProductByItemId($id);
        if(!$basketProduct){
            return false;
        }else{
            return $basketProduct->delete();
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

    public function getProductByItemId($id){
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                if($id == $product->id){
                    return $product;
                }
            }
        }
        return false;
    }

    public function findProductByVariantId($id){
        return in_array($id,$this->variantIds) ? true : false;
    }

    public function getProductByVariantId($id){
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                if($id == $product->variant_id){
                    return $product;
                }
            }
        }
        return false;
    }

    public function getVariantIds(){
        $result = [];
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                $result[] = $product->variant_id;
            }
        }
        return $result;
    }

    public function clear(){
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                $product->delete();
            }
        }
    }

    /* -------------- END >> Actions Basket ------------- */

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

    public function getBasketError(){
        return $this->basketError;
    }

    public function afterSave($insert, $changedAttributes)
    {
//        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
//        if(!empty($changedAttributes) && !empty($changedAttributes['status'])){
//            if($changedAttributes['status'] == 0 && $this->status == 1){
//                if(!empty($this->products)){
//                    foreach ($this->products as $product) {
//                        $count = $product->variant;
//                    }
//                }
//            }elseif($changedAttributes['status'] == 1 && $this->status == 0){
//
//            }
//        }
    }


    /* -------------- Delivery ------------- */

    public function setDeliveryPrice($deliveryId){
        $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
        $deliveryGroup->setProducts($this->products);
        $deliveryGroup->setDeliveryId($this->delivery_id);
        $deliveryGroup->setProductDeliveryGroup();

        $deliveryPrice = 0;

        if(!empty($deliveryGroup->productDeliveryGroup)){
            foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                foreach ($group as $key => $value){
                    if($value != 1014){
                        $group[0] = $value;  //<<<-------------------------КАСТЫЛЬ ДЛЯ БЫСТРОЙ ДОСТАВКИ ТУТ, ВОН ТАМ СЛЕВА
                        break;
                    }
                }
                if(!empty($group[0])){
                    if($key == 'address_0' || $key == 'club_1'){
                        $key = true;
                    }else{
                        $key = false;
                    }
                    $deliveryPrice += $this->getDeliveryPriceWithParams($deliveryId,$group[0],$key);
                }
            }

            $this->delivery_price = $deliveryPrice;
            $this->save();
        }
    }

    public function getDeliveryPriceWithParams($deliveryId,$groupId,$key = false){
        $price = DeliveriesPrices::find()->where(['delivery_id' => $deliveryId,'good_type_id' => $groupId])->select('price')->scalar();
      /*  if($key && $price > 0){
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

        //return $price;
      */
        return Yii::$app->action->calcDeliveryPriceWithDiscont($price, $groupId);
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

    /* -------------- END >> Delivery ------------- */

    public function getStoreList(){
        $storeList = $storeListUnique = [];
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                foreach ($product->product->shop->shops as $shop) {
                    foreach ($shop->stores as $store) {
                        if(!in_array($store->id,$storeListUnique)){
                            $storeList[$product->product_id][] = [
                                'store_address' => $store->addressStringTitle,
                                'store_id' => $store->id,
                                'product_id' => $product->product_id,
                                'shop_group_id' => $product->product->shop->id,
                            ];
                            $storeListUnique[] = $store->id;
                        }
                    }
                }
            }
        }
        return $storeList;
    }

    public function getStoreListJson(){
        return json_encode($this->storeList);
    }

    public function getPriceGroups(){
        $result = 0;
        if(!empty($this->products)){
            foreach ($this->products as $product) {
                if(($product->product->type_id == 1003 || $product->product->type_id == 1007) &&  (!in_array($product['variant_id'], Yii::$app->params['present']))){
                    $result += $product->price * $product->count;
                }
            }
        }
        return $result;
    }
    public function getPresentInBasket(){

        if(!empty($this->products)){
            foreach ($this->products as $product) {
                if(in_array($product['variant_id'], array_column(Yii::$app->params['presentAll'], 'present'))){
                    return $product['id'];
                }
            }
        }
        return false;
    }
    public function getPriceGroupsAll($presentKey=-1){
        $result = 0;
        if(!empty($this->products) && $presentKey>-1){
            foreach ($this->products as $product) {
                if(in_array($product->product->type_id, Yii::$app->params['presentAll'][$presentKey]['goodsType']) &&  (!in_array($product['variant_id'], array_column(Yii::$app->params['presentAll'], 'present')) ) ){
                    $result += $product->price * $product->count;
                }
            }
        }
        return $result;
    }
    public function getPresentInBasketAll($presentKey=-1){
        if(!empty($this->products) && $presentKey>-1){
            foreach ($this->products as $product) {
                if($product['variant_id'] == Yii::$app->params['presentAll'][$presentKey]['present']){
            	    if($product->count>1){
            		$product->count=1;
            		$product->save(true);
            	    }
                    return $product['id'];
                }
            }
        }
        return false;
    }
}