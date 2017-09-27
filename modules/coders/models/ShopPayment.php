<?php

namespace app\modules\coders\models;

use app\modules\actions\models\Actions;
use app\modules\basket\models\Basket;
use app\modules\actions\models\ActionsPresentSave;
use app\modules\basket\models\BasketLg;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsPreorder;
use app\modules\common\models\Api;
use app\modules\common\models\User;
use app\modules\common\models\UsersBonus;
use app\modules\common\models\UsersPays;
use app\modules\actions\models\ActionsAccumulation;
use Yii;

class ShopPayment
{
    public $basketId = false;// basketID
    public $description = '';
    public $order = false; // order object
    public $paymentId = false; // payment ID form
    public $orderId = false; // real order ID
    public $amount = 0;
    public $cardId = false;
    public $saveCardParam = 0;
    public $basket; // basket Object

    private $user = false; //user object

    //создать запись в транзакциях шопа
    public function fillEmptyAccount($type, $money, $transactionId = false)
    {
        if(!$this->user){

        }else{
            $money = abs(intval($money));

            $pay = new UsersPays();
            $pay->user_id = $this->user->id;
            $pay->order_id = $this->order ? $this->order->id : null;
            //$pay->basket_id = $this->basketId ? $this->orderId : null;
            $pay->type = Yii::$app->params['paymentType'][$type]['type'];
            $pay->comments = Yii::$app->params['paymentType'][$type]['description'];
            $pay->money = $money;
            $pay->date = date('Y-m-d H:i:s');
            $pay->status = 0;
            $pay->transaction_id = !empty($transactionId) ? strval($transactionId) : '0';
            if($pay->save()){
                return $pay->id;
            }else{
                $this->errorsLogInText($pay->errors);
            }
        }
        return false;
    }

    //зачисление средств пользователю на счет
    public function fillAccountUpdate($transactionId = false){
        if(!$this->user || !$transactionId){
            return false;
        }
        else{
            $pay = UsersPays::find()->where(['transaction_id'=>$transactionId])->one();
            //$pay->order_id = $this->order->id;
            $pay->status = 1;
            if($pay->save(true)){
                $this->updateUserBalance($pay->money);
                return true;
            }
            else{
                $this->errorsLogInText($pay->errors);
            }
        }
        return false;
    }

    public function checkEmptyAccount($id,$transactionId = false,$errorCode = false){
        if(!$this->user){

        }
        else{
            $pay = UsersPays::findOne($id);
            if(!$pay){

            }else{
                $pay->status = empty($errorCode) ? 1 : 0;
                $pay->error_code = !empty($errorCode) ? $errorCode : 0;
                $pay->transaction_id = (empty($pay->transaction_id) && !empty($transactionId)) ?  strval($transactionId) : '0';
                if($pay->save()){
                    $this->updateUserBalance($pay->money);
                    return true;
                }
                else{
                    $this->errorsLogInText($pay->errors);
                }
            }
        }
        return false;
    }

    // Зачисляем денег
    public function fillAccount($type,$money,$transactionId = false){
        if(!$this->user){

        }else{
            $money = abs(intval($money));

            $pay = new UsersPays();
            $pay->user_id = $this->user->id;
            $pay->order_id = $this->order ? $this->order->id : null;
            $pay->type = Yii::$app->params['paymentType'][$type]['type'];
            $pay->comments = Yii::$app->params['paymentType'][$type]['description'];
            $pay->money = $money;
            $pay->date = date('Y-m-d H:i:s');
            $pay->status = 1;
            $pay->transaction_id = !empty($transactionId) ? strval($transactionId) : '0';

            if($pay->save()){
                $this->updateUserBalance($money);
                return true;
            }
            else{
                $this->errorsLogInText($pay->errors);
            }
        }
        return false;
    }

    // списание денег
    public function debitAccount($type, $money = 0){
        if(!$this->user){

        }
        else{
            $money = 0 - abs(intval($money));
            $pay = new UsersPays();
            $pay->user_id = $this->user->id;
            $pay->order_id = $this->order ?  $this->order->id : null;
            $pay->type = Yii::$app->params['paymentType'][$type]['type'];
            $pay->comments = Yii::$app->params['paymentType'][$type]['description'].($this->order ?  $this->order->id : '');
            $pay->money = $money;
            $pay->date = date('Y-m-d H:i:s');
            $pay->status = 1;
            if($pay->save()){
                $this->updateUserBalance($money);
                return true;
            }
            else{
                $this->errorsLogInText($pay->errors);
            }
        }
        return false;

    }

    //-------------------------
    // Зачисляем бонусов
    public function fillAccountBonus($type,$bonus){
        if(!$this->user){

        }else{
            $bonus = abs(intval($bonus));

            $payBonus = new UsersBonus();
            $payBonus->user_id = $this->user->id;
            $payBonus->order_id = $this->order ? $this->order->id : null;
            $payBonus->type = Yii::$app->params['bonusOperationType'][$type]['type'];
            $payBonus->comments = Yii::$app->params['bonusOperationType'][$type]['description'];
            $payBonus->bonus = $bonus;
            $payBonus->date = date('Y-m-d H:i:s');
            $payBonus->status = 1;
            if($payBonus->save()){
                $this->updateUserBonus($bonus);
                return true;
            }
            else{
                $this->errorsLogInText($payBonus->errors);
            }
        }
        return false;
    }

    // Списываем Бонусы
    public function debitAccountBonus($type, $bonus = 0){
        if(!$this->user){

        }else{
            $bonus = 0 - abs(intval($bonus));

            $payBonus = new UsersBonus();
            $payBonus->user_id = $this->user->id;
            $payBonus->order_id = $this->order ?  $this->order->id : null;
            $payBonus->type = Yii::$app->params['bonusOperationType'][$type]['type'];
            $payBonus->comments = Yii::$app->params['bonusOperationType'][$type]['description'];
            $payBonus->bonus = $bonus;
            $payBonus->date = date('Y-m-d H:i:s');
            $payBonus->status = 1;
            if($payBonus->save()){
                $this->updateUserBonus($bonus);
                return true;
            }
            else{
                $this->errorsLogInText($payBonus->errors);
            }
        }
        return false;

    }

    //-------------------------

    public function updateUserBalance($money){
        if(!$this->user){

        }else{
            $this->user->money += $money;

            if($this->user->save()){
                return true;
            }
            else{
                $this->errorsLogInText($this->user->errors);
            }
        }
        return false;
    }

    public function updateUserBonus($bonus){
        if(!$this->user){

        }else{
            $this->user->bonus += $bonus;

            if($this->user->save()){
                return true;
            }else{
                $this->errorsLogInText($this->user->errors);
            }
        }
        return false;
    }

    //-------------------------

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($orderObj){
        $this->order = $orderObj;
    }

    public function getUser(){
        return $this->user;
    }

    public function setUser($user){
        $this->user = $user;
    }

    public function setBasket(Basket $basket){
        $this->basket = $basket;
    }

    public function getBasket()
    {
        return $this->basket;
    }

    public function setAmount($amount = 0){
        $this->amount = $amount *100;
    }

    public function getAmount(){
        return round($this->amount/100);
    }

    public function errorsLogInText($obj)
    {
        $file = "----------------------------------------------------\n------------------------START-----------------------\n";
        $fileName =  'error_sberbank_'.time().'_'.rand(0, 1000).'.txt';
        $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
        $file.= var_export($obj, true);
        $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
        if(!file_exists($dirName)){
            mkdir($dirName);
        }
        file_put_contents($dirName.'/'.$fileName, $file."\n");
    }
    //-------------------------

    public function orderPayment(){
        // Списываем деньги за товары
        if(!$this->debitAccount(4, $this->order->money)){
            $this->errorsLogInText('4 -'.$this->order->money);
        }

        // Списываем деньги за доставку
        if(!$this->debitAccount(9, $this->order->deliveryPrice)){
            $this->errorsLogInText('4 -'.$this->order->deliveryPrice);
        }

        // Заказ оплачен
        $this->order->status = 1;
        if($this->order->save()){
            //$basket = Basket::find()->where(['id'=>$this->order->basket_id])->one();
            //$basket->start();
            //$basket = \Yii::$app->action->applyActionOnFindBasket($basket);
            //$this->order->checkMetroItems($this->order->id, $basket);//отправка заказа Metro в helper

            $this->order->checkMetroItems($this->order->id, $this->basket);//отправка заказа Metro в helper
            $this->fillAccumulation(\Yii::$app->action->getAccumulation());//зачисление накоплений
            $this->substructCountOfPersonalAction();// деактивация индивидуальных акций

            $api = new Api();
            $api->sms($this->user->phone, 'Номер заказа: #'.$this->order->id.' Подробности: www.Esalad.ru/my/orders-history/');

            if ($this->order->bonus > 0) {// Списываем Бонусы
                $this->debitAccountBonus(0, $this->order->bonus);
            }

            //усиленно проверить
            if ($this->order->bonus <= 0 && $this->user->bonus < 0 && $this->user->bonus == $this->order->bonus) {
                //$this->user->bonus == ($this->getAmount() - $this->order->money - $this->order->deliveryPrice+ $this->order->bonus)
                //$this->debitAccount(31, abs($this->user->bonus));// Покупатель задолжал магазину бонусов списываем деньги за бонусы
                $this->fillAccountBonus(0, $this->order->bonus);// Зачисляем Бонусы
            }

            if ($this->order->code_id){ //&& $this->order->fee > 0) {// Проверка применения промо-кода и размера комиссии;
                $this->checkPoromoCode();
            }



            $this->sendWF();//отпарвка подарка

            if($this->basket->status == 0){
                $oldBasket = Basket::find()->where(['id' => $this->basket->id])->one();
                $oldBasket->status = 1;
                //$this->basket->status = 1;
                //if(!$this->basket->save(true)){
                if(!$oldBasket->save(true)){
                    $this->errorsLogInText($oldBasket->errors);
                }
            }
            $this->ShopManagmentApi();//отправка писем манагерам
        }
        else{
            $this->errorsLogInText($this->order->errors);
        }
    }

    private function ShopManagmentApi()
    {
        if($this->order){
            $shopManagmentContent = [];
            if(!empty($this->order->ordersGroups)){
                foreach ($this->order->ordersGroups as $ordersGroup) {
                    if(!empty($ordersGroup->ordersItems)){
                        foreach ($ordersGroup->ordersItems as $ordersItem) {
                            if(!empty($ordersItem)){
                                $shopManagmentContent[$ordersItem->shop->id] = !empty($shopManagmentContent[$ordersItem->shop->id]) ? $shopManagmentContent[$ordersItem->shop->id] : '';
                                $shopManagmentContent[$ordersItem->shop->id] .= '<tr>';
                                $shopManagmentContent[$ordersItem->shop->id] .= '<td style="width: 40px; text-align: center;"></td>';
                                $shopManagmentContent[$ordersItem->shop->id] .= '<td>'.$ordersItem->good->name.'<br /><span style="color: #999999;">'.$ordersItem->goodsVariations->titleWithProperties.'</span></td>';
                                $shopManagmentContent[$ordersItem->shop->id] .= '<td style="width: 80px; text-align: center;">'.$ordersItem->count.' шт.</td>';
                                $shopManagmentContent[$ordersItem->shop->id] .= '</tr>';

                                if(in_array($ordersItem->product->type_id, Yii::$app->params['preorderType'])){
                                    //найдти запись в предзаказе если нет то создать и добавить количество заказанной вариации
                                    $pre = GoodsPreorder::find()
                                        ->where(['good_variant_id'=>$ordersItem->variation_id])
                                        ->andWhere(['between', 'date', Date('Y-m-d 00:00:00', time()), Date('Y-m-d 23:59:59', time())])
                                        ->one();
                                    if(empty($pre)){//создать новую запись
                                        $pre = new GoodsPreorder();
                                        $pre->good_variant_id = $ordersItem->variation_id;
                                        $pre->count = $ordersItem->count;
                                    }
                                    else{
                                        $pre->count = $pre->count+ $ordersItem->count;
                                    }
                                    if(!$pre->save(true)){
                                        $this->errorsLogInText($pre);
                                    }
                                }
                                //уменьшаем количество товара
                                $count = GoodsCounts::find()->where(['variation_id' => $ordersItem->variation_id,'store_id' => $ordersItem->store_id])->one();
                                if(!$count){
                                    $this->errorsLogInText($ordersItem);
                                }
                                else{
                                    $count->count -= $ordersItem->count;
                                    if(!$count->save()){
                                        $this->errorsLogInText($count->errors);
                                    }
                                }

                            }
                        }
                    }
                }
            }

            $shopManagment = $this->order->shopsManagements;
            $api = new Api();

            if(!empty($shopManagment)){
                $content = '';
                $content .= '<table cellpadding="5" cellspacing="0" border="1" style="width: 800px;">';
                $content .= '<tr>';
                $content .= '<td></td>';
                $content .= '<td colspan="4"><b>#'.$this->order->id.'</b><br/><span style="color: #999999;"> '.$this->order->comments.'</span></td>';
                $content .= '</tr>';

                foreach ($shopManagment as $shopId => $managerList){
                    foreach ($managerList as $manager) {
                        if((!empty($manager->email)) && (filter_var($manager->email, FILTER_VALIDATE_EMAIL)) ){
                            Yii::$app->mailer->compose()
                                ->setTo($manager->email)
                                ->setFrom(['order@Esalad.org' => 'Esalad'])
                                ->setSubject('Новый заказ: #'.$this->order->id)
                                ->setHtmlBody('Новый заказ: #'.$this->order->id . '<br />' . $content . $shopManagmentContent[$shopId] .'</table><a href="http://www.Esalad.ru/shop/order-report" target="_blank">Личный кабинет</a><br />')
                                ->send();

                        }
                        if(!empty($manager->phone) && !empty($manager->sms) && $manager->sms == 1){
                            $api->sms($manager->phone, 'Новый заказ: #'.$this->order->id);
                        }
                    }
                }
            }
        }
        return false;

    }

    private function fillAccumulation(array $accumulations =[])
    {
        if(empty($accumulations)){
            return false;
        }
        foreach ($accumulations as $actionId=>$params){
            foreach ($params as $paramId=>$param ){
                if(isset($param['currency_id']) && !empty($param['currency_id'])){
                    $addAcum = new ActionsAccumulation();
                    $addAcum->user_id = $this->order->user_id;//$basket->user_id;//$param['user_id'];
                    $addAcum->order_id = $this->order->id;
                    $addAcum->current_value = $param['current_value'];
                    $addAcum->action_id = $actionId;
                    $addAcum->action_param_value_id = $paramId;
                    $addAcum->currency_id = $param['currency_id'];
                    $addAcum->save(true);

                    if((!empty($param['spent'])) && ($param['spent']==1)) {//деактивация потраченых накоплений
                        $usedAcums = ActionsAccumulation::find()
                            ->where(['action_id' => $actionId, 'action_param_value_id' => $paramId, 'status' => 1, 'active' => 1, 'currency_id' => $param['currency_id'], 'user_id' => $param['user_id']])
                            ->all();
                        foreach ($usedAcums as $usedAcum) {
                            $usedAcum->active = 0;
                            $usedAcum->save(true);
                        }
                    }
                }
            }
        }
        return false;

    }

    private function checkPoromoCode()
    {
        $olduser = $this->getUser();//Текущий пользователь
        $promoCode = \app\modules\catalog\models\Codes::findOne($this->order->code_id);
        if (!empty($promoCode)){
            $user = \app\modules\common\models\User::findOne($promoCode->user_id);
            $this->setUser($user);// Устанавливаем юзера платежа

            // Записываем платёж в базу с типом 6 - Комиссия за продажу товара
            if(!$this->fillAccountBonus(6, $this->order->fee)){
                $this->errorsLogInText($this->order);
            }

            $promoCode->count -= 1;
            if(!$promoCode->save()){
                $this->errorsLogInText($promoCode->errors);
            }

            $arActions = json_decode($this->order->actions_json,1);
            foreach ($arActions as $key => $value){
                $action = Actions::find()->where(['id'=>$value['action_id']])->One();
                if(!empty($action)){
                    if($promoCode->type_id == $action->type_promo_code){
                        $this->fillAccount(32,$this->order->add_Rubl);
                        $this->fillAccountBonus(6,$this->order->add_Bonus);
                    }
                }
            }

            $this->setUser($olduser);
            return true;
        }
        return false;
    }

    private function sendWF()
    {
        $flag = false;
        foreach ($this->order->ordersGroups as $ordersGroup) {
            if(!empty($ordersGroup->ordersItems)){
                foreach ($ordersGroup->ordersItems as $ordersItem) {
                    if(in_array($ordersItem->variation_id, Yii::$app->action->getIgnored())){
                        $params['phone'] = $this->user->phone;
                        $params['name']  = $this->user->name;
                        foreach (Yii::$app->params['presentAll'] as $keyParam => $present){
                            if($present['present'] == $ordersItem->variation_id){
                                $params['type']= Yii::$app->params['presentAll'][$keyParam]['fitnessType'];
                            }
                        }
                        /*if(Yii::$app->params['presentAll'][0]['present']==$ordersItem->variation_id){
                            $params['type']= 'GOLD';
                        }
                        if(Yii::$app->params['presentAll'][1]['present']==$ordersItem->variation_id){
                            $params['type']= 'PLATINUM';
                        }*/
                        $params['present']= 'true';
                        $result = $this->requestWFPost($params);
                        //print_r($result);die();
                        if(!empty($result->code)){
                            $presentSave = ActionsPresentSave::find()->where(['basket_id'=>$this->order->basket_id, 'present'=>$ordersItem->variation_id])->one();
                            if(!empty($presentSave)){
                                $presentSave->card_number = strval($result->code);
                                $presentSave->bought_date = Date('Y-m-d H:i:00', time());

                                if($presentSave->save(true)){
                                    return true;
                                }

                            }
                        }
                    }
                }
            }
        }
        /*if($flag){
            $params['phone'] = $this->user->phone;
            $params['name']  = $this->user->name;
            $params['present']= 'true';
            $result = $this->requestWFPost($params);
            //print_r($result);die();
            if(isset($result->code)){
                $presentSave = ActionsPresentSave::find()->where(['basket_id'=>$this->order->basket_id])->one();
                if(!empty($presentSave)){
                    $presentSave->card_number = $result->code;
                    $presentSave->bought_date = Date('Y-m-d H:i:00', time());
                    if($presentSave->save(true)){
                        return true;
                    }
                }
            }
        }*/
        return false;

    }

    private function requestWFPost($params = [])
    {
        //$curl = curl_init('http://192.168.0.1/api/shop');
        $curl = curl_init('http://192.168.0.254/ajax/api-interface');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($curl);
        //$this->errorsLogInText($response);
        //print_r($response);die();
        curl_close($curl);
        return json_decode($response);
    }

    private function substructCountOfPersonalAction(){
        $actions = Actions::find()->where(['id'=>array_column(json_decode($this->order->actions_json), 'action_id'), 'for_user_id'=>$this->order->user_id])->all();
        foreach ($actions as $action){
            $action->count_for_user = $action->count_for_user-1;
            if($action->count_for_user < 1){
                $action->status = 0;
            }
            $action->save(true);
        }
    }




}