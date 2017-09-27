<?php

//last version 10072016

namespace app\controllers;

use app\modules\actions\models\Actions;
use app\modules\actions\models\ActionsPresentSave;
use app\modules\basket\models\PromoCode;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\GoodsPreorder;
use app\modules\coders\models\ShopPayment;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Api;
use app\modules\common\models\Fintess;
use app\modules\common\models\User;
use app\modules\common\models\UsersPays;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\ShopsStores;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use app\modules\actions\models\ActionsAccumulation;
use app\modules\actions\models\ActionsParamsValue;
use app\modules\common\models\HelperConnector;
use Yii;

class AjaxManagementController extends FrontController
{
    public function actionOrdersItemCancel()
    {
        $order = !empty(intval($_POST['order'])) ? intval($_POST['order']) : false;
        $orderGroup = !empty(intval($_POST['orderGroup'])) ? intval($_POST['orderGroup']) : false;
        $orderItem = !empty(intval($_POST['orderItem'])) ? intval($_POST['orderItem']) : false;

        if (!$orderItem || !$orderGroup || !$order) {
            return false;
        } else {
            $order = Orders::find()->where(['id' => $order])->one();
            $orderGroup = OrdersGroups::find()->where(['id' => $orderGroup])->one();
            $orderItem = OrdersItems::find()->where(['id' => $orderItem])->one();

            if (!$orderItem || !$orderGroup || !$order) {
                return false;
            } else {
                $store = ShopsStores::findOne($orderItem->store_id);

                $variant = GoodsVariations::findOne($orderItem->variation_id);
                $product = Goods::findOne($orderItem->good_id);
                $user = User::findOne($order->user_id);

                if (!$store || !$variant || !$product || !$user) {
                    return false;
                } else {
                    $count = GoodsCounts::find()->where(['store_id' => $store->id, 'variation_id' => $orderItem->variation_id])->One();
                    $money = ($orderItem->price - $orderItem->discount - $orderItem->bonus);//* $orderItem->count;

                    if (!$count) {
                        return false;
                    } else {
                        $payment = new ShopPayment();
                        $payment->setOrder($order);
                        $payment->setUser($user);

                        $payment->fillAccount(5, $money, $transactionId = false);

                        if ($orderItem->bonus > 0) {
                            $payment->fillAccountBonus(0, $orderItem->bonus);//* $orderItem->count
                        }

                        //Возврат по промокоду в акциях при отмене
                        $curUser = $user;//Сохраняем текущего user
                        $arActions = json_decode($order->actions_json,1);
                        $code_id = 	$order->code_id;
                        if(!empty($code_id)){
                            $promoCode = PromoCode::find()->where(['id'=>$code_id])->One();
                            if(!empty($promoCode)){
                                $user_id = $promoCode->user_id;
                                $user = User::find()->where(['id'=>$user_id])->One();//Получаем собственника промокода

                                foreach ($arActions as $key => $value){
                                    $action = Actions::find()->where(['id'=>$value['action_id']])->One();
                                    if(!empty($action)){
                                        if($promoCode->type_id == $action->type_promo_code){
                                            $payment->setUser($user);
                                        }
                                    }
                                }
                            }
                        }





                        if ($orderItem->bonusBack > 0) {//списать бонусы с пользователя за единицу товара
                            $payment->debitAccountBonus(8, $orderItem->bonusBack/$orderItem->count_save);
                        }

                        if ($orderItem->rublBack > 0) {//списать деньги с пользователя за единицу товара
                            $payment->debitAccount(33, $orderItem->rublBack/$orderItem->count_save);
                        }

                        $user = $curUser;//Возвращаем текущего user

                        $payment->setUser($user);

                        if ($orderItem->fee > 0) {
                            if ($order->code_id) {
                                $promoCode = \app\modules\catalog\models\Codes::findOne($order->code_id);
                                if (!empty($promoCode)) {
                                    $user = \app\modules\common\models\User::findOne($promoCode->user_id);
                                    // Устанавливаем юзера платежа
                                    $payment->setUser($user);
                                    // Записываем платёж в базу с типом 6 - Комиссия за продажу товара
                                    $payment->debitAccountBonus(9, $orderItem->fee); //*$orderItem->count
                                    $promoCode->count += 1;
                                    $promoCode->save();
                                }
                            }
                        }
                        $count->count += 1;//$orderItem->count;
                        $count->save();
                        if (($orderItem->count - 1) == 0) {
                            $orderItem->status = 0;
                        } else {
                            $orderItem->count = $orderItem->count - 1;
                        }
                        //$orderItem->status = 0;
                        $orderItem->save();//отправка отмены в хелпер
                        if (in_array($orderItem->variation_id, Yii::$app->action->getIgnored())) {//отправка отмены карты в WF

                            $card = ActionsPresentSave::find()->where(['basket_id' => $order->basket_id, 'status' => 1, 'present'=>$orderItem->variation_id])->asArray()->One();
                            if (!empty($card) && !empty($card['card_number'])) {
                                Api::requestWFPost(['delete_card_code' => $card['card_number']]);
                            }
                        }
                        //удаление количества из пердзаказа
                        if (in_array($orderItem->product->type_id, Yii::$app->params['preorderType'])) {
                            //найдти запись в предзаказе если нет то лесом
                            $pre = GoodsPreorder::find()
                                ->where(['good_variant_id' => $orderItem->variation_id])
                                ->andWhere(['between', 'date', Date('Y-m-d 00:00:00', strtotime($order->date)), Date('Y-m-d 23:59:59', strtotime($order->date))])
                                ->andWhere(['>', 'count', '0'])
                                ->one();
                            if (!empty($pre)) {
                                $pre->count = $pre->count - 1;
                                $pre->save(true);
                            }

                        }


                        /*
                        $payment = new Payment([
                            'orderReport' => $order->id,
                            'amount' => $money,
                        ]);

                        $payment->setUser($user);
                        if($payment->fillAccount(5,$money,$transactionId = false)){

                        }else{

                        }

                        if($orderItem->bonus > 0){
                            $payment->fillAccountBonus(0,$orderItem->bonus * $orderItem->count);
                        }

                        if($orderItem->fee > 0){
                            if ($order->code_id) {
                                $promoCode = \app\modules\catalog\models\Codes::findOne($order->code_id);
                                if (!$promoCode) {

                                } else {
                                    $user = \app\modules\common\models\User::findOne($promoCode->user_id);
                                    // Устанавливаем юзера платежа
                                    $payment->setUser($user);

                                    // Записываем платёж в базу с типом 6 - Комиссия за продажу товара
                                    $payment->debitAccountBonus(6, $orderItem->fee * $orderItem->count);

                                    $promoCode->count += 1;
                                    $promoCode->save();
                                }
                            }
                        }

                        $payment->setUser($user);

                        if($orderItem->bonusBack >0 ){
                            //списать бонусы с пользователя

                            $payment->debitAccountBonus(8, $orderItem->bonusBack);

                        }
                        if($orderItem->rublBack >0 ){
                            //списать деньги с пользователя
                            $payment->debitAccount(33, $orderItem->rublBack);
                        }

                        $count->count += $orderItem->count;
                        $count->save();

                        $orderItem->status = 0;
                        if($orderItem->save()){//отправка отмены в хелпер
                            //$helper = new HelperConnector();
                            //$helper->cancelItem($order->id, $orderItem->variation_id);

                        }*/
                        /*
                        if(!empty($order->actions_json)){//отменяем накопления
                            $all_action = json_decode($order->actions_json);
                            foreach($all_action as $action){
                                $actionsAcum = ActionsAccumulation::find()->where(['order_id'=>$order->id, 'action_id'=>$action->action_id])->asArray()->all();
                                if(!empty($actionsAcum)){
                                    foreach ($actionsAcum as $actAc) {
                                        $deactivAcum = ActionsAccumulation::find()->where(['order_id'=>$order->id, 'action_id'=>$actAc['action_id'], 'action_param_value_id'=>$actAc['action_param_value_id']])->one();
                                        $deactivAcum->status = 0;
                                        $deactivAcum->active = 0;
                                        $deactivAcum->save(true);
                                    }

                                }
                            }
                            //списываем кэшбеки
                            if($order->add_Bonus>0){
                                $payment->debitAccountBonus(8, $order->add_Bonus);
                                $order->add_Bonus = 0;$order->save(true);
                            }
                            if($order->add_Rubl>0){
                                $payment->debitAccount(33, $order->add_Rubl);
                                $order->add_Rubl = 0;$order->save(true);
                            }
                        }*/
                    }
                }
            }
        }

    }

    public function actionChangeProductParam()
    {
        $productId = !empty($_POST['productId']) && !empty(intval($_POST['productId'])) ? intval($_POST['productId']) : false;
        $param = !empty($_POST['params']) ? $_POST['params'] : false;
        $value = !empty($_POST['value']) && !empty(intval($_POST['value'])) ? intval($_POST['value']) : false;

        if (!$productId || !$param || !$value) {
            return false;
        }

        $param = str_replace('Goods[', '', $param);
        $param = str_replace(']', '', $param);
        $product = Goods::findOne($productId);

        if (!$product) {
            return false;
        }

        if ($param == 'position') {
            $product->$param = abs($value);
        } else {
            $product->$param = abs($product->$param - 1);
        }
        $product->save();
    }


    protected function OrdersItemCancelFull($order,$orderGroup,$OrderItem)
    {
        $order = !empty(intval($order)) ? intval($order) : false;
        $orderGroup = !empty(intval($orderGroup)) ? intval($orderGroup) : false;
        $orderItem = !empty(intval($OrderItem)) ? intval($OrderItem) : false;

        if (!$orderItem || !$orderGroup || !$order) {
            return false;
        } else {
            $order = Orders::find()->where(['id' => $order])->one();
            $orderGroup = OrdersGroups::find()->where(['id' => $orderGroup])->one();
            $orderItem = OrdersItems::find()->where(['id' => $orderItem])->one();

            if (!$orderItem || !$orderGroup || !$order) {
                return false;
            } else {
                $store = ShopsStores::findOne($orderItem->store_id);

                $variant = GoodsVariations::findOne($orderItem->variation_id);
                $product = Goods::findOne($orderItem->good_id);
                $user = User::findOne($order->user_id);

                if (!$store || !$variant || !$product || !$user) {
                    return false;
                } else {
                    $count = GoodsCounts::find()->where(['store_id' => $store->id, 'variation_id' => $orderItem->variation_id])->One();
                    $money = ($orderItem->price - $orderItem->discount - $orderItem->bonus) * $orderItem->count;

                    if (!$count) {
                        return false;
                    } else {
                        $payment = new ShopPayment();
                        $payment->setOrder($order);
                        $payment->setUser($user);

                        $payment->fillAccount(5, $money, $transactionId = false);

                        if ($orderItem->bonus > 0) {
                            $payment->fillAccountBonus(0, $orderItem->bonus) * $orderItem->count;
                        }

                        if ($orderItem->bonusBack > 0) {//списать бонусы с пользователя
                            $payment->debitAccountBonus(8, $orderItem->bonusBack);
                        }

                        if ($orderItem->rublBack > 0) {//списать деньги с пользователя
                            $payment->debitAccount(33, $orderItem->rublBack);
                        }

                        if ($orderItem->fee > 0) {
                            if ($order->code_id) {
                                $promoCode = \app\modules\catalog\models\Codes::findOne($order->code_id);
                                if (!empty($promoCode)) {
                                    $user = \app\modules\common\models\User::findOne($promoCode->user_id);
                                    // Устанавливаем юзера платежа
                                    $payment->setUser($user);
                                    // Записываем платёж в базу с типом 6 - Комиссия за продажу товара
                                    $payment->debitAccountBonus(9, $orderItem->fee) * $orderItem->count;
                                    $promoCode->count += 1;
                                    $promoCode->save();
                                }
                            }
                        }
                        $count->count += $orderItem->count;
                        $count->save();
                        if (($orderItem->count - 1) == 0) {
                            $orderItem->status = 0;
                        } else {
                            $orderItem->count = $orderItem->count - 1;
                        }
                        $orderItem->status = 0;
                        $orderItem->status_id = 1009;
                        $orderItem->save();//отправка отмены в хелпер
                        if (in_array($orderItem->variation_id, Yii::$app->action->getIgnored())) {//отправка отмены карты в WF

                            $card = ActionsPresentSave::find()->where(['basket_id' => $order->basket_id, 'status' => 1])->asArray()->one();
                            if (!empty($card) && !empty($card['card_number'])) {
                                Api::requestWFPost(['delete_card_code' => $card['card_number']]);
                            }
                        }
                        //удаление количества из пердзаказа
                        if (in_array($orderItem->product->type_id, Yii::$app->params['preorderType'])) {
                            //найдти запись в предзаказе если нет то лесом
                            $pre = GoodsPreorder::find()
                                ->where(['good_variant_id' => $orderItem->variation_id])
                                ->andWhere(['between', 'date', Date('Y-m-d 00:00:00', strtotime($order->date)), Date('Y-m-d 23:59:59', strtotime($order->date))])
                                ->andWhere(['>', 'count', '0'])
                                ->one();
                            if (!empty($pre)) {
                                $pre->count = $pre->count - 1;
                                $pre->save(true);
                            }

                        }
                    }
                }
            }
        }

    }


    public function actionOrderItemCheckAvalible()
    {
        $order = !empty(intval($_POST['order'])) ? intval($_POST['order']) : false;
        $orderItem = !empty(intval($_POST['orderItem'])) ? intval($_POST['orderItem']) : false;
        $newOrderItem = !empty(intval($_POST['newOrderItem'])) ? intval($_POST['newOrderItem']) : false;
        $newOrderItemCount = !empty(intval($_POST['newOrderItemCount'])) ? intval($_POST['newOrderItemCount']) : false;
        if (!$newOrderItem || !$newOrderItemCount || !$order) {
            return false;
        } else {
            $goodVariation = GoodsVariations::find()->where(['id' => $newOrderItem])->One();
            $goodCount = GoodsCounts::find()->where(['variation_id' => $newOrderItem, 'status' => 1])->andWhere(['>=', 'count', $newOrderItemCount])->One();
            $order = Orders::find()->where(['id' => $order])->one();
            $user_id = $order->user_id;
            $userBalance = User::find()->where(['id' => $user_id])->One()->money;
            $newOrderItemSum = $goodVariation->getPriceValue() * $newOrderItemCount;
            $orderItem = OrdersItems::find()->where(['id' => $orderItem])->One();
            $orderItemSum = $orderItem->price * $orderItem->count;

            if (!$goodCount) {
                return 'not enough';
            }

            if ($orderItemSum < $newOrderItemSum) {
                return 'more than was';
            }
            if($orderItemSum == $newOrderItemSum){

            }elseif ($userBalance + $orderItemSum < $newOrderItemSum) {
                return 'expensive';
            }


        }
    }

    public function actionOrdersItemAdd()
    {
        $order = !empty(intval($_POST['order'])) ? intval($_POST['order']) : false;
        $orderGroup = !empty(intval($_POST['orderGroup'])) ? intval($_POST['orderGroup']) : false;
        $newOrderItem = !empty(intval($_POST['newOrderItem'])) ? intval($_POST['newOrderItem']) : false;
        $newOrderItemCount = !empty(intval($_POST['newOrderItemCount'])) ? intval($_POST['newOrderItemCount']) : false;
        $orderItemCur =  !empty(intval($_POST['orderItemCur'])) ? intval($_POST['orderItemCur']) : false;

        if (!$newOrderItem || !$orderGroup || !$order || !$newOrderItemCount || !$orderItemCur) {
            return false;
        } else {

            $orderItemCur = OrdersItems::find()->where(['id'=>$orderItemCur])->One();
            if($orderItemCur->status == 1){
                $this->OrdersItemCancelFull($order,$orderGroup,$orderItemCur->id);
            }else{
                $orderItemCur->status_id = 1009;
                $orderItemCur->save();
            }
            $order = Orders::find()->where(['id' => $order])->one();
            $orderGroup = OrdersGroups::find()->where(['id' => $orderGroup])->one();
            $goodVariation = GoodsVariations::find()->where(['id' => $newOrderItem])->One();
            $orderItem = new OrdersItems();
            $orderItem->order_group_id = $orderGroup->id;
            $orderItem->good_id = $goodVariation->product->id;
            $orderItem->variation_id = $newOrderItem;
            $orderItem->price = $goodVariation->getPriceValue();
            $orderItem->discount = 0;
            $orderItem->comission = $goodVariation->getPriceValue()/100*$goodVariation->comission;
            $orderItem->count = $newOrderItemCount;
            $orderItem->status = 1;
            $itemCountStore = GoodsCounts::find()->where(['variation_id' => $newOrderItem, 'status' => 1])->andWhere(['>=', 'count', $newOrderItemCount])->One();
            $orderItem->store_id = $itemCountStore->store_id;
            if(!$orderItem->save()){
                print_r($orderItem->getErrors());
                return false;
            }

            $payment = new ShopPayment();
            $payment->setOrder($order);
            $payment->setUser($order->getUser());
            $payment->debitAccount(4,($goodVariation->getPriceValue()*$newOrderItemCount));

            if(isset($bonusPay) && $bonusPay>0){
                $payment->debitAccountBonus(0,$bonusPay);
            }


            $itemCountStore->count = $itemCountStore->count - $newOrderItemCount;
            if(!$itemCountStore->save()){
                print_r($itemCountStore->getErrors());
                return false;
            }

            return 'success';
        }
    }

}



