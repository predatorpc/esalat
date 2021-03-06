<?php

namespace app\modules\coders\models;

use app\modules\actions\models\ActionsPresentSave;
use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketLg;
use app\modules\catalog\models\GoodsCounts;
use app\modules\common\models\Api;
use app\modules\common\models\User;
use app\modules\common\models\UsersBonus;
use app\modules\common\models\UsersPays;
use app\modules\actions\models\ActionsAccumulation;
use Yii;

class Payment
{
    public $merchantId;
    public $language = 'ru';
    public $privateSecurityKey;
    public $currency;
    public $returnUrl;
    public $failUrl;
    public $order = false; // order object
    public $orderId = false; // payment ID for dend
    public $orderReport = false; // real order ID
    public $amount = 0;
    public $orderDescription = '';
    public $cardId = false;
    public $rebillAnchor = false;

    public $saveCardParam = 0;

    public $basket;

    private $user = false;

    public function __construct($params = false){
        $this->merchantId = Yii::$app->params['payment']['merchantId'];
        $this->language = Yii::$app->params['payment']['language'];
        $this->privateSecurityKey = Yii::$app->params['payment']['privateSecurityKey'];
        $this->currency = Yii::$app->params['payment']['currency'];
        $this->returnUrl = Yii::$app->params['payment']['returnUrl'];
        $this->failUrl = Yii::$app->params['payment']['failUrl'];

        if(!$params){

        }else{
            $this->saveCardParam = !empty($params['save_card']) ? 1 : 0;
            $this->orderId = !empty($params['orderId']) ? $params['orderId'] : false;
            $this->orderReport = !empty($params['orderReport']) ? $params['orderReport'] : false;
            $this->amount = !empty($params['amount']) ? $params['amount'] : 0;
            $this->orderDescription = !empty($params['orderDescription']) ? $params['orderDescription'] : '';
            $this->cardId = !empty($params['cardId']) ? $params['cardId'] : false;
            $this->rebillAnchor = !empty($params['rebillAnchor']) ? $params['rebillAnchor'] : false;
        }
    }

    public function changePaymentType(){

    }

    //-------------------------

    public function fillEmptyAccount($type,$money,$transactionId = false){
        if(!$this->user){

        }else{
            $money = abs(intval($money));

            $pay = new UsersPays();
            $pay->user_id = $this->user->id;
            $pay->order_id = $this->orderReport ? $this->orderReport : false;
            $pay->type = Yii::$app->params['paymentType'][$type]['type'];
            $pay->comments = Yii::$app->params['paymentType'][$type]['description'];
            $pay->money = $money;
            $pay->date = date('Y-m-d H:i:s');
            $pay->status = 0;
            $pay->transaction_id = !empty($transactionId) ? strval($transactionId) : '0';
            if($pay->save()){
                return $pay->id;
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fillEmptyAccount.txt',var_export($pay->errors,true));
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
                $pay->error_code = !empty($errorCode) ? strval($errorCode) : '0';
                $pay->transaction_id = (empty($pay->transaction_id) && !empty($transactionId)) ? strval($transactionId) : '0';
                if($pay->save()){
                    $this->updateUserBalance($pay->money);
                    return true;
                }else{
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/checkEmptyAccount.txt',var_export($pay->errors,true));
                }
            }
        }
        return false;
    }

    public function fillAccount($type,$money,$transactionId = false){
        if(!$this->user){

        }else{
            $money = abs(intval($money));

            $pay = new UsersPays();
            $pay->user_id = $this->user->id;
            $pay->order_id = $this->orderReport ? $this->orderReport : false;
            $pay->type = Yii::$app->params['paymentType'][$type]['type'];
            $pay->comments = Yii::$app->params['paymentType'][$type]['description'];
            $pay->money = $money;
            $pay->date = date('Y-m-d H:i:s');
            $pay->status = 1;
            $pay->transaction_id = !empty($transactionId) ? strval($transactionId) : '0';

            if($pay->save()){
                $this->updateUserBalance($money);
                return true;
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fillAccount.txt',var_export($pay->errors,true));
            }
        }
        return false;
    }

    public function debitAccount($type, $money = 0){
        if(!$this->user){

        }else{
            $money = 0 - abs(intval($money));

            $pay = new UsersPays();
            $pay->user_id = $this->user->id;
            $pay->order_id = $this->orderReport ?  $this->orderReport : '';
            $pay->type = Yii::$app->params['paymentType'][$type]['type'];
            $pay->comments = Yii::$app->params['paymentType'][$type]['description'] .($this->orderReport ?  $this->orderReport : '');
            $pay->money = $money;
            $pay->date = date('Y-m-d H:i:s');
            $pay->status = 1;
            if($pay->save()){
                $this->updateUserBalance($money);
                return true;
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debitAccount.txt',var_export($pay->errors,true));
            }
        }
        return false;

    }

    //-------------------------

    public function fillAccountBonus($type,$bonus){
        if(!$this->user){

        }else{
            $bonus = abs(intval($bonus));

            $payBonus = new UsersBonus();
            $payBonus->user_id = $this->user->id;
            $payBonus->order_id = $this->orderReport ? $this->orderReport : false;
            $payBonus->type = Yii::$app->params['bonusOperationType'][$type]['type'];
            $payBonus->comments = Yii::$app->params['bonusOperationType'][$type]['description'];
            $payBonus->bonus = $bonus;
            $payBonus->date = date('Y-m-d H:i:s');
            $payBonus->status = 1;
            if($payBonus->save()){
                $this->updateUserBonus($bonus);
                return true;
            }else{
                $file = "----------------------------------------------------\n------------------------START-----------------------\n";
                $fileName =  'bonusPay_'.time().'_'.rand(0, 1000).'.txt';
                $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
                $file.= var_export($payBonus->errors, true);

                $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
                if(!file_exists($dirName)){
                    mkdir($dirName);
                }
                file_put_contents($dirName.'/'.$fileName, $file."\n");
                //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fillAccountBonus.txt',var_export($payBonus->errors,true));
            }
        }
        return false;
    }

    public function debitAccountBonus($type, $bonus = 0){
        if(!$this->user){

        }else{
            $bonus = 0 - abs(intval($bonus));

            $payBonus = new UsersBonus();
            $payBonus->user_id = $this->user->id;
            $payBonus->order_id = $this->orderReport ?  $this->orderReport : '';
            $payBonus->type = Yii::$app->params['bonusOperationType'][$type]['type'];
            $payBonus->comments = Yii::$app->params['bonusOperationType'][$type]['description'];
            $payBonus->bonus = $bonus;
            $payBonus->date = date('Y-m-d H:i:s');
            $payBonus->status = 1;
            if($payBonus->save()){
                $this->updateUserBonus($bonus);
                return true;
            }else{
                $file = "----------------------------------------------------\n------------------------debit-----------------------\n";
                $fileName =  'bonusPay_'.time().'_'.rand(0, 1000).'.txt';
                $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
                $file.= var_export($payBonus->errors, true);

                $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
                if(!file_exists($dirName)){
                    mkdir($dirName);
                }
                file_put_contents($dirName.'/'.$fileName, $file."\n");
                //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debitAccountBonus.txt',var_export($payBonus->errors,true));
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
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/updateUserBalance.txt',var_export($this->user->errors,true));
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/updateUserBalance1.txt',var_export($this->user,true));
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
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/updateUserBonus.txt',var_export($this->user->errors,true));
            }
        }
        return false;
    }

    //-------------------------

    public function getOrder(){
        return $this->order;
    }

    public function setOrder(){
        if(!$this->orderReport){
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/setOrder.txt','setOrder empty ORDER');
        }else{
            $this->order = \app\modules\shop\models\Orders::findOne($this->orderReport);
            //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/setOrderOk.txt',var_export($this->order,true));
        }
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

    //-------------------------

    public function getUrlParamsString(){
        $params	 = 'MerchantId='. $this->merchantId;
        $params .= '&OrderId='.$this->orderId;
        $params .= '&Amount='. $this->amount;
        $params .= '&Currency='. $this->currency;

        if (strlen($this->orderDescription)<101 AND strlen($this->orderDescription)>1)
        {
            $params .= '&OrderDescription=' . urlencode($this->orderDescription);
        }
        $params .= '&PrivateSecurityKey=' . $this->privateSecurityKey;

        return $params;
    }

    public function getUrlParamsStringForSecure(){
        $params	 = 'MerchantId='. $this->merchantId;
        $params .= '&OrderId='.$this->orderId;
        $params .= '&Amount='. $this->amount;
        $params .= '&Currency='. $this->currency;

        if (strlen($this->orderDescription)<101 AND strlen($this->orderDescription)>1)
        {
            $params .= '&OrderDescription=' . $this->orderDescription;
        }
        $params .= '&PrivateSecurityKey=' . $this->privateSecurityKey;

        return $params;
    }

    public function checkSecurityKey($securityKey = '',$data = []){
        $dataCheck = md5('DateTime='.$data['DateTime'].'&TransactionID='.$data['TransactionID'].'&OrderId='.$data['OrderId'].'&Amount='.$data['Amount'].'&Currency=RUB&PrivateSecurityKey='.$this->privateSecurityKey);
        return $dataCheck == $securityKey ? true : false;
    }

    public function getSecurityKey(){
        return md5($this->getUrlParamsStringForSecure());
    }

    //-------------------------

    public function getPaymentUrl(){
        $paymenturl = "https://secure.payonlinesystem.com/".$this->language."/payment/?";

        $url_query = $this->getUrlParamsString();
        $url_query .= "&SecurityKey=";
        $url_query .= $this->getSecurityKey();

        $url_query .= "&ReturnUrl=".urlencode($this->returnUrl . '?ORDER=' . $this->orderReport);
        $url_query .= "&FailUrl=".urlencode($this->failUrl . '?ORDER=' . $this->orderReport);

        $url_query .= $this->rebillAnchor ? "&RebillAnchor=".urlencode($this->rebillAnchor) : '';
        $url_query .= $this->saveCardParam ? "&save_card=1" : '';

        return $paymenturl . $url_query;
    }

    //-------------------------

    public function orderPayment(){

        /*echo $this->amount.'</br>';
        echo $this->order->money.'</br>';
        echo $this->order->deliveryPrice.'</br>';
        echo $this->order->bonus.'</br>';
        echo ($this->amount - $this->order->money - $this->order->deliveryPrice+$this->order->bonus).'</br>';
        print_R($this->user);
        die();*/
        // Списываем деньги за товары
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/t_104.txt','4 -'.$this->order->id);
        if($this->debitAccount(4, $this->order->money)){

        }else{
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errorDebitAccountGoods.txt','4 -'.$this->order->money);
        }

//        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/t_105.txt','4 -'.$this->order->id);

        // Списываем деньги за доставку
        if($this->debitAccount(9, $this->order->deliveryPrice)){

        }else{

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errorDebitAccountDel.txt','4 -'.$this->order->deliveryPrice);
        }
//        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/t_106.txt','4 -'.$this->order->id);

        // Заказ оплачен
        $this->order->status = 1;
        if($this->order->save()){
            $basket = Basket::find()->where(['id'=>$this->order->basket_id])->one();
            $basket->start();
            $basket = \Yii::$app->action->applyActionOnFindBasket($basket);
            $accumulations = \Yii::$app->action->getAccumulation();
            $this->order->checkMetroItems($this->order->id, $basket);//отправка заказа Metro в helper
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
                                ->where(['action_id' => $actionId, 'action_param_value_id' => $paramId, 'status' => 1, 'active' => 1, 'currency_id' => $param['currency_id'], 'user_id' => $this->order->user_id])
                                ->all();

                            /*$file = "----------------------------------------------------\n------------------------Okpayment-----------------------\n";
                            $fileName =  'acc_'.time().'_'.rand(0, 1000).'.txt';
                            $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
                            $file.= var_export($usedAcums, true);

                            $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
                            if(!file_exists($dirName)){
                                mkdir($dirName);
                            }
                            file_put_contents($dirName.'/'.$fileName, $file."\n");*/

                            foreach ($usedAcums as $usedAcum) {
                                $usedAcum->active = 0;
                                $usedAcum->save(true);
                            }
                        }
                    }
                }
            }

            $this->sendWF();// для отправка подарочной карты в WF

        }


//        if($this->basketObject->status == 0){
//            $this->basketObject->status = 1;
//            $this->basketObject->save();
//        }

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

                            $count = GoodsCounts::find()->where(['variation_id' => $ordersItem->variation_id,'store_id' => $ordersItem->store_id])->one();
                            if(!$count){
                                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/GoodsCountsNotFound.txt',var_export($ordersItem,true));
                            }else{
                                $count->count -= $ordersItem->count;
                                if($count->save()){

                                }else{
                                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/orderPaymentCount.txt',var_export($count->errors,true));
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
            $content .= '<td colspan="4"><b>#'.$this->order->id.'</b></td>';
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
        $api->sms($this->user->phone, 'Номер заказа: #'.$this->order->id.' Подробности: www.Esalad.ru/my/orders-history/');

        if ($this->order->bonus > 0) {
            // Списываем Бонусы
            $this->debitAccountBonus(0, $this->order->bonus);
        }

        if ($this->order->bonus <= 0 && $this->user->bonus < 0 && $this->user->bonus == ($this->amount - $this->order->money - $this->order->deliveryPrice + $this->order->bonus)) {
            // Покупатель задолжал магазину бонусов
            // Списываем деньги за бонусы
            $this->debitAccount(31, abs($this->user->bonus));

            // Зачисляем Бонусы
            $this->fillAccountBonus(0, $this->order->bonus);
        }
//        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/t_110.txt','4 -'.$this->order->bonus);

        // Проверка применения промо-кода и размера комиссии;
        if ($this->order->code_id and $this->order->fee > 0) {
            $promoCode = \app\modules\catalog\models\Codes::findOne($this->order->code_id);
            if (!$promoCode) {
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/thisOrderCodeIdEmpty.txt',$this->order->code_id);

            } else {
//                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/thisOrderCodeIdFull.txt',$this->order->code_id);
                $user = \app\modules\common\models\User::findOne($promoCode->user_id);
                // Устанавливаем юзера платежа
                $this->setUser($user);
//                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/thisOrderCodeIdSetUser.txt',var_export($this->user->id,true));

                // Записываем платёж в базу с типом 6 - Комиссия за продажу товара
                if(!$this->fillAccountBonus(6, $this->order->fee)){
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/thisOrderCodeIdFillAccount.txt',var_export($this->order,true));
                }else{
//                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/thisOrderCodeIdFillAccountOk.txt',var_export($this->order,true));
                }

                $promoCode->count -= 1;
                if($promoCode->save()){

                }else{
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/orderPaymentPromoCode.txt',var_export($promoCode->errors,true));
                }
            }
        }

        $this->basket = new BasketLg();
        $this->basket = $this->basket->findCurrentBasket($this->user);

        if($this->basket->status == 0){
            $this->basket->status = 1;
            $this->basket->save();
        }

    }
    private function sendWF()
    {
        $flag = false;
        foreach ($this->order->ordersGroups as $ordersGroup) {
            if(!empty($ordersGroup->ordersItems)){
                foreach ($ordersGroup->ordersItems as $ordersItem) {
                    if(in_array($ordersItem->variation_id, Yii::$app->action->getIgnored())){
                        $flag = true;
                        break;
                    }
                }
            }
        }
        if($flag){
            $params['phone'] = $this->user->phone;
            $params['name']  = $this->user->name;
            $params['phone']= '0000000000';
            $params['name']= 'Вася';
            $params['present']= 'true';
            $result = $this->requestWFPost($params);
            print_r($result);die();
            if(isset($result['code'])){
                $presentSave = ActionsPresentSave::find()->where(['basket_id'=>$this->order->basket_id])->one();
                if(!empty($presentSave)){
                    $presentSave->card_number = $result['card'];
                    if($presentSave->save(true)){
                        return true;
                    }
                }
            }
        }
        return false;

    }

    private function requestWFPost($params = [])
    {
        $curl = curl_init('http://192.168.0.1/api/shop');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($curl);
        //print_r($response);
        curl_close($curl);
        return json_decode($response);
    }

}