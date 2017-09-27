<?php

namespace app\controllers;

use app\components\WBasketCheckData;
use app\modules\basket\models\BasketLg;
use app\modules\basket\models\BasketProducts;
use app\modules\basket\models\CatalogProduct;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\GoodsVariations;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Address;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\ShopsStores;

use app\modules\shop\models\Orders;
use Yii;

/**
 * BasketController implements the CRUD actions for Basket model.
 */
class AjaxBasketController extends FrontController
{

    //------------------

    public function actionChangeDelivery(){
        $deliveryId = !empty(intval($_POST['deliveryId'])) ? intval($_POST['deliveryId']) : false;
        $addressId = !empty(intval($_POST['addressId'])) ? intval($_POST['addressId']) : false;

        if(!$deliveryId || !$addressId){
            return false;
        }
        else{
            Yii::$app->basket->setDelivery($deliveryId);
            Yii::$app->basket->setAddress($addressId);
            Yii::$app->basket->setDeliveryPrice();
        }

        return \app\components\WDeliverySelect::widget([
            'basket' => Yii::$app->basket->getBasket(),
            'sort' => 2
        ]);
    }

    public function actionChangeDate(){
        $basket = Yii::$app->controller->basket;
        $basket->time_list = !empty($basket->time_list) ? $basket->time_list : '';
        $basketTimeList = json_decode($basket->time_list,true);
        $basketTimeList[$_POST['data']['productGroupKey']]['day'] = $_POST['data']['day'];
        if(!empty($basketTimeList[$_POST['data']['productGroupKey']]['time'])){
            unset($basketTimeList[$_POST['data']['productGroupKey']]['time']);
        }

        $basket->time_list = json_encode($basketTimeList);
        $basket->save();

        return \app\components\WBasketTimeDelivery::widget([
            'basket' => $basket,
            'sort' => 3
        ]);
    }

    public function actionChangePayment(){
        $paymentId = !empty(intval($_POST['paymentId'])) ? intval($_POST['paymentId']) : false;
        if(!$paymentId){
            return false;
        }else{
            Yii::$app->basket->setPayment($paymentId);
        }
    }

    public function actionChangeBonusPay(){
        $flagBonus = !empty(intval($_POST['paymentId'])) ? intval($_POST['paymentId']) : false;
        Yii::$app->basket->setBonusPay($flagBonus);
    }

    public function actionChangeTime(){
        $basket = Yii::$app->controller->basket;
        $basket->time_list = !empty($basket->time_list) ? $basket->time_list : '';
        $basketTimeList = json_decode($basket->time_list,true);
        $basketTimeList[$_POST['data']['productGroupKey']]['day'] = $_POST['data']['day'];
        $basketTimeList[$_POST['data']['productGroupKey']]['time'] = $_POST['data']['time'];

        foreach ($basketTimeList as $ket => $item) {
            if(empty($item['day']) || empty($item['time'])){
                unset($basketTimeList[$ket]);
            }
        }

        $basket->time_list = json_encode($basketTimeList);
        $basket->save();

        return \app\components\WBasketTimeDelivery::widget([
            'basket' => $basket,
            'sort' => 3
        ]);
    }

    public function actionGetBasketDateTimeBlock(){
        return \app\components\WBasketTimeDelivery::widget([
            'basket' => Yii::$app->controller->basket,
            'sort' => 3
        ]);
    }

    //------------------

    public function actionSetPromo(){
        $promoCodeVisual = $promoMessages = '';
        $promoCode = !empty($_POST['promo_code_id']) ? $_POST['promo_code_id'] : false;
        if(!$promoCode){
            $promoMessages = 'Пусто';
        }else{
            $promoCodeChcek = Codes::find()->where([
                'code' => $promoCode,
                'status' => 1,
            ])->One();
            if(isset($promoCodeChcek) && $promoCodeChcek->type->shop_id > 0 && $promoCodeChcek->type->money_discount >0
                && $promoCodeChcek->user_id != Yii::$app->user->id){
                    $promoMessages = 'Промо-код не принят';

            }else {
                $promoCodeId = Codes::find()
                    ->where([
                        'code' => $promoCode,
                        'status' => 1,
                    ])
                    ->andWhere(['>', 'count', 0])
                    ->select(['id'])
                    ->scalar();
                if (!$promoCodeId) {
                    $promoMessages = 'Нет такого промо-кода';
                } else {
                    Yii::$app->basket->setPromoCode($promoCodeId);
                    $promoCodeVisual = $promoCodeId;
                    $promoMessages = 'Промо-код принят';
                    Yii::$app->action->applyActions();
                }
            }
        }
        print \app\components\WBasketPromo::widget([
            'promo' => $promoCodeVisual,
            'message' => $promoMessages,
        ]);
    }

    public function actionSetEmptyBasketParams(){
        if(!empty($_POST['StoreList'])){
            $post = $_POST['StoreList'];
        }else{
            return false;
        }

        if(!empty($post) && !empty($_POST['basket-id'])){
            $basket = Yii::$app->controller->basket;
            //print_r($basket);
            if(!empty($basket)){

            }else{
                return false;
            }
            if(!empty($post['order_comments'])){
                $basket->comment = $post['order_comments'];
                $basket->save();
            }

            if(!empty($post)){
                foreach (Yii::$app->controller->basket->products as $product) {
                    $product->store_id = $post[$product->product_id];
                    $product->save();
                }
            }
        }
        if(!empty($_POST['metro-club-index'])){
            $basket->current_club = json_encode(['metro' => $_POST['metro-club-index']]);
            $basket->save();
        }
    }

    //------------------

    public function actionRemoveBasketProduct(){
        $basketItem = !empty(intval($_POST['data']['basketItem'])) ? intval($_POST['data']['basketItem']) : false;
        if(!$basketItem){

        }else{
            Yii::$app->basket->removeProductCurrentBasket($basketItem);
        }
    }

    public function actionRemoveAllBasketProduct(){
        Yii::$app->basket->clearCurrentBasket();
    }

    public function actionAddInBasket(){
        $count = (!empty(intval($_POST['count'])) && intval($_POST['count']) > 0) ? intval($_POST['count']) : false;
        $productId = (!empty(intval($_POST['id'])) && intval($_POST['id']) > 0) ? intval($_POST['id']) : false;
        $variantId = (!empty(intval($_POST['variant'])) && intval($_POST['variant']) > 0) ? intval($_POST['variant']) : false;

        if(!$count || !$productId || !$variantId){

        }else{
            if($count > 0){
                Yii::$app->controller->basket->add($variantId,$count);
            }
        }
    }

    public function actionRepeatOrder(){
        $parametrs = Yii::$app->request->post('items');
        //print_r($parametrs);
        if(!empty($parametrs)){
            foreach ($parametrs as $parametr){
                //print_r(json_decode($parametr));
                $param = json_decode($parametr);
                if((!empty($param->variationId)) && ( !empty($param->count))){
                    Yii::$app->controller->basket->add($param->variationId,$param->count);
                }
            }
        }

        return json_encode(['status'=>'true']);
        //$parametrs['order_id'] = 10051721;
        /*$order_id = 10051721;
        $order = Orders::find()->where(['id'=>$order_id])->with('items')->asArray()->one();
        foreach ($order['items'] as $item){
            Yii::$app->controller->basket->add($item['variation_id'],$item['count']);
        }*/
    }

    public function actionRemoveInBasket(){
        $itemId = (!empty(intval($_POST['id'])) && intval($_POST['id']) > 0) ? intval($_POST['id']) : false;

        if(!$itemId){

        }else{
            Yii::$app->basket->removeProductCurrentBasket($itemId);
        }
    }

    public function actionChangeProductVariant(){
        $basketItemId = !empty(intval($_POST['basketItemId'])) ? intval($_POST['basketItemId']) : false;
        $productId = !empty(intval($_POST['productId'])) ? intval($_POST['productId']) : false;
        $tagIds = !empty($_POST['tagIds']) ? $_POST['tagIds'] : false;
        $count_item = !empty($_POST['count_pack']) ? $_POST['count_pack'] : 1;

        if(!$basketItemId || !$productId || !$tagIds){
            return false;
        }else{
            $variantId = (new BasketLg())->findVariantByTags($productId,$tagIds);
            Yii::$app->basket->changeProductCurrentBasket($basketItemId,$variantId,$count_item);
        }
    }

    public function actionChangeCountProduct(){
        $basketItem = !empty(intval($_POST['basketId'])) ? intval($_POST['basketId']) : false;
        $count = !empty(intval($_POST['count'])) ? intval($_POST['count']) : false;

        if(!$basketItem || !$count){
            return false;
        }else{
            if($count > 0){
                $basketProducts = BasketProducts::find()->where(['id' => $basketItem])->one();
                if(!in_array($basketProducts->variant_id, [1000066096, 1000066336])){
            	    $basketProducts->count = $count;
                    $basketProducts->save();
                }
            }
            return false;
        }
    }

    //----------------------

    public function actionGetAddressList(){
        return \app\components\WDeliverySelect::widget([
            'basket' => Yii::$app->action->applyActions(),//Yii::$app->basket->getBasket(),
            'sort' => 2
        ]);
    }

    public function actionGetBasketResult(){
        //Yii::$app->action->checkingAllActions();
        print \app\components\WBasketResult::widget([
            //'basket' => Yii::$app->basket->getBasket(),
            'basket' => Yii::$app->action->applyActions(),//getCurrentBasket(),
        ]);
    }

    public function actionGetBasketPayment(){
        if (!Yii::$app->user->isGuest){
            print \app\components\WPaymentSelect::widget([
                //'basket' => Yii::$app->basket->getBasket(),
                //'basket' => \Yii::$app->action->getCurrentBasket(),
                'basket' => Yii::$app->action->applyActions(),//getCurrentBasket(),
            ]);
        }
    }

    public function actionGetBasketDateTime(){
        $basket = Yii::$app->basket->getBasket();
        $basket->time_list = !empty($basket->time_list) ? $basket->time_list : '';
        $basketTimeList = json_decode($basket->time_list,true);

        if(!empty($basketTimeList)){
            foreach ($basketTimeList as $time){
                if(!empty($time['time'])){
                    if(time() > strtotime(date('Y-m-d') . ' 08:00:00')){
                        if($time['time'] <= (strtotime(date('Y-m-d')) + 3600*24)){
                            $basket->time_list = '';
                            $basket->save();
                        }
                    }
                }else{
                    if(!empty($time['date'])){
                        unset($basket->time_list[$time['date']]);
                        if(!empty($basket->time_list)){
                            $basket->time_list = json_encode($basket->time_list);
                        }else{
                            $basket->time_list = '';
                        }
                        $basket->save();
                    }
                }
            }

            $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
            $deliveryGroup->setProducts( $basket->products);
            $deliveryGroup->setDeliveryId($basket->delivery_id);
            $deliveryGroup->setProductDeliveryGroup();

            if(!empty($deliveryGroup->productDeliveryGroup)){
                foreach ($basketTimeList as $key => $item) {
                    if(empty($deliveryGroup->productDeliveryGroup[$key]) || empty($item['day']) || empty($item['time'])){
                        unset($basketTimeList[$key]);
                    }
                }
            }
        }
        $basket->time_list = json_encode($basketTimeList);

        print \app\components\WBasketTimeDelivery::widget([
            'basket' => $basket,
            'sort' => 3
        ]);
    }

    public function actionGetSmallBasket(){
        return Yii::$app->basket->displaySmallBasket();
    }

    public function actionGetBasketProductList(){
        print \app\components\WBasketProductList::widget([
            'basket' => Yii::$app->basket->getBasket(),
        ]);
    }

    public function actionGetBasketProduct(){
        $basketItemId = !empty(intval($_POST['basketItemId'])) ? intval($_POST['basketItemId']) : false;
        if(!$basketItemId){
            return false;
        }else{
            if(!Yii::$app->basket->emptyBasket()){
                //foreach (Yii::$app->basket->getBasketProducts() as $product) {
                foreach (Yii::$app->action->getCurrentBasketProducts() as $product) {
                    if($product->id == $basketItemId){
                        print \app\components\WBasketProductVOne::widget([
                            'product' => $product,
                        ]);
                    }
                }
            }
        }
    }

    public function actionGetBasketStoreBlock(){
        print \app\widgets\basket\WBasketFindStore::widget([
            'basket' => Yii::$app->basket->getBasket(),
        ]);
    }

    //--------------------

    public function actionAddNewAddress(){
        if(!Yii::$app->user->identity){
            return false;
        }else{
            $address = new Address();

            $address->city = (!empty($_POST['city'])) ? $_POST['city'] : '';
            $address->street = (!empty($_POST['street'])) ? $_POST['street'] : '';
            $address->house = (!empty($_POST['house'])) ? $_POST['house'] : '';
            $address->room = (!empty($_POST['room'])) ? $_POST['room'] : '';
            $address->district = (!empty($_POST['district'])) ? $_POST['district'] : '';
            $address->delivery_id = intval(\Yii::$app->request->post('delivery_id'));
            $address->comments = (!empty($_POST['comments'])) ? $_POST['comments'] : '';
            $address->phone = (!empty($_POST['phone'])) ? $_POST['phone'] : '';
            $address->user_id = Yii::$app->user->identity->getId();
            $address->date = date('Y-m-d H:i:s');
            $address->status = 1;

            if($address->save()){
                return 'OK';
            }else{
                return 'Пожалуйста, заполните обязательные поля';
            }
        }
    }

    //----------------------

    public function actionBasketCheckData(){
        return WBasketCheckData::widget([
            //'basket' => Yii::$app->basket->getBasket(),
            'basket' => Yii::$app->action->applyActions(),
        ]);
    }
    // Закрыть модалка;
    public function actionModalStock(){
        if(Yii::$app->request->post('ModalStock')) {
            $_SESSION['ModalStock'] = 1;
        }
       return false;
    }


}
