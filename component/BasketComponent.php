<?php
namespace app\component;

use app\components\WCatalogListButtonBlock;
use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketLg;
use app\modules\basket\models\BasketProducts;
use app\modules\basket\models\CatalogProduct;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Lists;
use app\modules\common\models\DeliveriesPrices;
use app\modules\common\models\User;
use app\modules\common\models\Zloradnij;
use yii\base\Component;
use Yii;
use yii\helpers\ArrayHelper;

class BasketComponent extends Component{

    private $basketProducts = false;
    private $basket = false;
    private $basketError = [];

    public function init(){
        parent::init();

        $this->basket = Yii::$app->controller->basket;
        $this->basketProducts = $this->basket->products;

        if(!empty($this->basket->delivery_id)) {
            $this->setDeliveryPrice($this->basket->delivery_id);
        }
    }

    private function reloadCurrentBasket(){
        $this->basket = Yii::$app->controller->basket;
        $this->basketProducts = $this->basket->products;
    }

    public function getDeliveryList(){
        $this->reloadCurrentBasket();
        if(empty($this->basket->time_list)){
            $this->basket->time_list = '';
        }
        return json_decode($this->basket->time_list,true);
    }

    public function displayButtonBlockForCatalogList($variantId){

        return WCatalogListButtonBlock::widget([
            'basketItem' => $this->basket->getProductByVariantId($variantId),
        ]);
    }

    public function displaySmallBasket(){
        return \app\components\WBasketSmall::widget();
    }

    public function getBasketDiscount(){
        return $this->basket->getBasketDiscount();
    }

    public function getResultPrice(){
        $result = !empty($this->basket->delivery_price) ? $this->basket->delivery_price : 0;
        return $result + $this->basket->basketPriceDiscount;
    }

    //-----------------------

    public function getBasketBonus(){
        return $this->basket->getBasketBonus();
    }

    //-----------------------

    public function getBasket(){
        return $this->basket;
    }

    public function getBasketProducts(){
        return $this->basketProducts;
    }

    public function getBasketProductsVariants(){
        return $this->basket->variants;
    }

    public function getBasketProductsSimple(){
        return $this->basket->getBasketProductsSimple();
    }

    public function getBasketProductsList(){
        return $this->basket->getBasketProductsList();
    }

    public function getBasketList(){
        return $this->basket->getBasketList();
    }

    public function getBasketProduct($variantId){
        return $this->basket->getProductByVariantId($variantId);
    }

    public function getBasketVariantIds(){
        return $this->basket->getVariantIds();
    }

    //-----------------------

    private function clearBasket(){
        $this->basket->clear();
    }

    public function clearCurrentBasket(){
        $this->clearBasket();
    }

    //-----------------------

    private function addProduct($basketId, CatalogProduct $product){
        $basketProduct = BasketProducts::findOne(['basket_id' => $basketId,'variant_id' => $product->variant_id]);

        if(!$basketProduct){
            $basketProduct = new BasketProducts();
            $basketProduct->basket_id = $basketId;
            $basketProduct->product_id = $product->product_id;
            $basketProduct->variant_id = $product->variant_id;
            $basketProduct->store_id = !empty($product->store_id) ? $product->store_id : 0;
            $basketProduct->list_id = !empty($product->list_id) ? $product->list_id : 0;
            $basketProduct->tool = !empty($product->tool) ? $product->tool : 0;
        }
        $basketProduct->list_id = !empty($product->list_id) ? $product->list_id : $basketProduct->list_id;
        $basketProduct->tool = !empty($product->tool) ? $product->tool : $basketProduct->tool;
        $basketProduct->count = $product->count;
        if($basketProduct->save()){
            $this->setDeliveryPrice($this->basket->delivery_id);
            return $basketProduct->id;
        }
    }

    public function getCurrentUser(){
        return !empty(Yii::$app->user->identity) ? User::findOne(Yii::$app->user->identity->id) : false;
    }


    public function addProductCurrentBasket(CatalogProduct $product){
        if(Yii::$app->session->get('shopMaster',0) > 0){
            $product->tool = 1;
        }

        $_SESSION['basket']['products'][$product->variant_id] = ArrayHelper::toArray($product);
        $_SESSION['basket']['products'][$product->variant_id]['basketProductId'] = $this->addProduct($this->basketId,$product);

        $this->reloadBasketSession();
        $basket = $this->getBasket();
        $this->setDeliveryPrice($basket->delivery_id);
        $this->reloadBasketSession();
    }

    private function changeProduct(BasketProducts $product){
        $basketProduct = BasketProducts::findOne(['basket_id' => $product->basket_id,'variant_id' => $product->variant_id]);

        if(!$basketProduct){

            $basketProduct = BasketProducts::findOne(['id' => $product->basketProductId]);
            if(!$basketProduct){

            }else{
                $basketProduct->variant_id = $product->variant_id;
            }
            $basketProduct->count = $product->count;
            $basketProduct->save();
        }else{
            $basketProduct = BasketProducts::findOne(['id' => $product->basketProductId]);
            if(!$basketProduct){

            }else{
                $basketProduct->delete();
            }
        }
    }

    public function changeProductCurrentBasket($basketItemId,$variantId,$count_item){
        $productWithVariant = $this->basket->getProductByVariantId($variantId);
        $product = $this->basket->getProductByItemId($basketItemId);
        if(!$productWithVariant){
            $product->variant_id = $variantId;
            $product->count = $count_item;
            $product->save();
        }else{
            $product->delete();
        }
    }

    //-----------------------

    private function removeProduct($basketId, $basketProductId){
        $basketProduct = BasketProducts::findOne(['basket_id' => $basketId,'id' => $basketProductId]);

        if(!$basketProduct){
            return false;
        }
        if($basketProduct->delete()){
            //$this->setDeliveryPrice($this->basket->delivery_id);
            return true;
        }
        return false;
    }

    public function removeProductCurrentBasket($basketProductId){
        $this->removeProduct($this->basket->id,$basketProductId);
    }

    //-----------------------

    public function findProduct($variantId){
        return !empty($_SESSION['basket']['products'][$variantId]) ? true : false;
    }

    public function emptyBasket(){
        return empty($this->basketProducts) ? true : false;
    }

    //-----------------------

    public function deliveryAddresses(){
        return (new BasketLg())->deliveryAddresses;
    }

    public function deliveryClubsAddresses(){
        return (new BasketLg())->clubAddresses;
    }

    private function getUserCards($userId){
        return \app\modules\common\models\UsersCards::find()
            ->where(['user_id' => $userId])
            ->andWhere(['status' => 1])
            ->orderBy('id DESC')
            ->all();
    }

    public function getCurrentUserCards(){
        if(!empty(Yii::$app->user->identity)){
            return $this->getUserCards(Yii::$app->user->identity->id);
        }

        return false;
    }

    private function saveBasket(BasketLg $basket){
        if($basket->save()){

        }else{
            //Zloradnij::print_arr($basket->errors);
        }
    }

    public function saveCurrentBasket($basket){
        $savedBasket = BasketLg::findOne($this->getBasketId());

        if($savedBasket){
            if($savedBasket->load(['BasketLg' => ArrayHelper::toArray($basket)])){
                $this->saveBasket($savedBasket);
                $_SESSION['basket']['object'] = array_merge($_SESSION['basket']['object'],ArrayHelper::toArray($savedBasket));
                $this->reloadBasketSession();
            }else{
                //Zloradnij::print_arr($savedBasket);
            }
        }else{
            //Zloradnij::print_arr($this->getBasketId());
        }
    }

    //------------------------

    public function setDelivery($deliveryId){
        $this->basket->delivery_id = $deliveryId;
        $this->basket->save();
    }

    public function setAddress($addressId){
        $this->basket->address_id = $addressId;
        $this->basket->save();
    }

    public function setDeliveryPrice(){
        $deliveryId = $this->basket->delivery_id;
        $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
        $deliveryGroup->setProducts($this->basketProducts);
        $deliveryGroup->setDeliveryId($deliveryId);
        $deliveryGroup->setProductDeliveryGroup();

        $deliveryPrice = 0;
        if(!empty($deliveryGroup->productDeliveryGroup)){
            foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                foreach ($group as $key => $value){
                    if($value != 1011){
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

            $this->basket->delivery_price = $deliveryPrice;
            $this->basket->save();
        }
        else{

        }
    }

    public function getDeliveryPriceWithParams($deliveryId,$groupId,$key = false){
        $price = DeliveriesPrices::find()->where(['delivery_id' => $deliveryId,'good_type_id' => $groupId])->select('price')->scalar();
        //if($key && $price > 0){
        $deliveryZeroPrice = 0;
        /*
        if(!empty($this->basket->products)){
            foreach ($this->basket->products as $product) {
                $deliveryZeroPrice += ($product->product->type_id == 1003 || $product->product->type_id == 1007) ? ($product->price * $product->count) : 0;
            }
        }

        if($deliveryZeroPrice > 3000){
            $price = $price - 150;
        }*/
        //}
        //return Yii::$app->action->calcDeliveryPriceWithDiscont($price);
        return $price;
    }

//    public function getDeliveryPriceWithParams($deliveryId,$groupId,$key = false){
//        $basket = $this->getBasket();
//        $price = DeliveriesPrices::find()->where(['delivery_id' => $deliveryId,'good_type_id' => $groupId])->select('price')->scalar();
//        if($key && $price > 0 && $basket->price > 3000){
//            return $price - 150;
//        }
//
//        return $price;
//    }

    public function setPromoCode($promoCodeId){
        $this->basket->promo_code_id = $promoCodeId;
        $this->basket->save();
    }

    public function setPayment($paymentId){
        $this->basket->payment_id = $paymentId;
        $this->basket->save();
    }

    public function setBonusPay($flag=1){
        if($flag==1){
            $this->basket->bonus_pay=0;
        }
        else{
            $this->basket->bonus_pay=1;
        }
        $this->basket->save();
    }
    //------------------------

    private function checkData(){
        $basket = $this->getBasket();
        if(Yii::$app->basket->emptyBasket()){
            $this->basketError[] = Yii::t('app','Вы ничего не выбрали');
            return false;
        }
        if(!$basket->user_id){
            $this->basketError[] = Yii::t('app','Вы неавторизованы...');
            return false;
        }
        if(empty($basket->session_id)){
            $this->basketError[] = Yii::t('app','Пустая сессия');
            return false;
        }
        if(empty($basket->delivery_id) || $basket->delivery_id == 0){
            $this->basketError[] = Yii::t('app','Не выбран адрес доставки');
            return false;
        }
        if(empty($basket->address_id)){
            $this->basketError[] = Yii::t('app','Не выбран адрес');
            return false;
        }
        if(empty($basket->payment_id) || $basket->payment_id == 0){
            $this->basketError[] = Yii::t('app','Не выбран способ оплаты');
            return false;
        }
        if(empty($basket->time_list)){
            $basketError[] = Yii::t('app','Не выбрано время доставки');
            return false;
        }
        $products = $this->getBasketProducts();
        $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
        $deliveryGroup->setProducts($products);
        $deliveryGroup->setDeliveryId($basket->delivery_id);
        $deliveryGroup->setProductDeliveryGroup();
        $timeList = json_decode($basket->time_list,true);

        if(!empty($deliveryGroup->productDeliveryGroup)){
            foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                if(!empty($timeList[$key]['day']) && !empty($timeList[$key]['time'])){

                }else{
                    //$this->basket->basketError[] = 'Время доставки выбрано не для всех типов товаров';
                    $this->basketError[] = Yii::t('app','Время доставки выбрано не для всех типов товаров');
                    return false;
                }
            }
        }else{
            //$this->basket->basketError[] = 'Нет групп продуктов';
            $this->basketError[] = Yii::t('app','Нет групп продуктов');
            return false;
        }
        return true;
    }

    public function getErrors(){
        $this->checkData();
        return $this->basketError;
    }

    public function getPriceGroups(){
        return $this->basket->getPriceGroups();
        /*$result = 0;
        if(!empty($this->basket->products)){
            foreach ($this->basket->products as $product) {
                if(($product->product->type_id == 1003 || $product->product->type_id == 1007) &&  (!in_array($product['variant_id'], Yii::$app->params['present']))){
                    $result += $product->price * $product->count;
                }
            }
        }
        return $result;*/
    }
    public function getPresentInBasket(){
        return $this->basket->getPresentInBasket();
        /*if(!empty($this->basket->products)){
            foreach ($this->basket->products as $product) {
                if(in_array($product['variant_id'], Yii::$app->params['present'])){
                    return $product['id'];
                }
            }
        }
        return false;*/
    }
    public function priceGroupsAll($key){
        return $this->basket->getPriceGroupsAll($key);
        /*$result = 0;
        if(!empty($this->basket->products)){
            foreach ($this->basket->products as $product) {
                if(($product->product->type_id == 1003 || $product->product->type_id == 1007) &&  (!in_array($product['variant_id'], Yii::$app->params['present']))){
                    $result += $product->price * $product->count;
                }
            }
        }
        return $result;*/
    }
    public function presentInBasketAll($key){
        return $this->basket->getPresentInBasketAll($key);
        /*if(!empty($this->basket->products)){
            foreach ($this->basket->products as $product) {
                if(in_array($product['variant_id'], Yii::$app->params['present'])){
                    return $product['id'];
                }
            }
        }
        return false;*/
    }
}

