<?php

namespace app\modules\coders\models;

use app\modules\common\models\UsersPays;
use Yii;

class SberbankPayment extends ShopPayment
{
    const SBERBANK_URLS_TEST = [
        'register'              => 'https://3dsec.sberbank.ru/payment/rest/register.do',//Регистрация заказа
        'registerPreAuth'       => 'https://3dsec.sberbank.ru/payment/rest/registerPreAuth.do',//Регистрация заказа с предавторизацией
        'deposit'               => 'https://3dsec.sberbank.ru/payment/rest/deposit.do', //Запрос завершения оплаты заказа
        'reverse'               => 'https://3dsec.sberbank.ru/payment/rest/reverse.do',//Запрос отмены оплаты заказа
        'refund'                => 'https://3dsec.sberbank.ru/payment/rest/refund.do',//Запрос возврата средств оплаты заказа
        'orderStatus'           => 'https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do',//Получение статуса заказа
        'orderStatusExtended'   => 'https://3dsec.sberbank.ru/payment/rest/getOrderStatusExtended.do',//Получение статуса заказа
        'verifyEnrollment'      => 'https://3dsec.sberbank.ru/payment/rest/verifyEnrollment.do',//Запрос проверки вовлеченности карты в 3DS
        'paymentOrderBinding'   => 'https://3dsec.sberbank.ru/payment/rest/paymentOrderBinding.do',//Запрос проведения оплаты по связкам
        'unBindCard'            => 'https://3dsec.sberbank.ru/payment/rest/unBindCard.do',//Запрос деактивации связки
        'bindCard'              => 'https://3dsec.sberbank.ru/payment/rest/bindCard.do',//Запрос активации связки
        'extendBinding'         => 'https://3dsec.sberbank.ru/payment/rest/extendBinding.do',//Запрос изменения срока действия связки
        'bindings'              => 'https://3dsec.sberbank.ru/payment/rest/getBindings.do',//Запрос списка всех связок клиента
        'bindingsByCardOrId'    => 'https://3dsec.sberbank.ru/payment/rest/getBindingsByCardOrId.do',//Запрос списка связок определённой банковской карты
        'lastOrders'            => 'https://3dsec.sberbank.ru/payment/rest/getLastOrdersForMerchants.do',//Запрос статистики по платежам за период
        'updateSSLCardList'     => 'https://3dsec.sberbank.ru/payment/rest/updateSSLCardList.do',//Запрос добавления карты в список SSL-карт
    ];
    const SBERBANK_URLS = [
        'register'              => 'https://securepayments.sberbank.ru/payment/rest/register.do',//Регистрация заказа
        'registerPreAuth'       => 'https://securepayments.sberbank.ru/payment/rest/registerPreAuth.do',//Регистрация заказа с предавторизацией
        'deposit'               => 'https://securepayments.sberbank.ru/payment/rest/deposit.do', //Запрос завершения оплаты заказа
        'reverse'               => 'https://securepayments.sberbank.ru/payment/rest/reverse.do',//Запрос отмены оплаты заказа
        'refund'                => 'https://securepayments.sberbank.ru/payment/rest/refund.do',//Запрос возврата средств оплаты заказа
        'orderStatus'           => 'https://securepayments.sberbank.ru/payment/rest/getOrderStatus.do',//Получение статуса заказа
        'orderStatusExtended'   => 'https://securepayments.sberbank.ru/payment/rest/getOrderStatusExtended.do',//Получение статуса заказа
        'verifyEnrollment'      => 'https://securepayments.sberbank.ru/payment/rest/verifyEnrollment.do',//Запрос проверки вовлеченности карты в 3DS
        'paymentOrderBinding'   => 'https://securepayments.sberbank.ru/payment/rest/paymentOrderBinding.do',//Запрос проведения оплаты по связкам
        'unBindCard'            => 'https://securepayments.sberbank.ru/payment/rest/unBindCard.do',//Запрос деактивации связки
        'bindCard'              => 'https://securepayments.sberbank.ru/payment/rest/bindCard.do',//Запрос активации связки
        'extendBinding'         => 'https://securepayments.sberbank.ru/payment/rest/extendBinding.do',//Запрос изменения срока действия связки
        'bindings'              => 'https://securepayments.sberbank.ru/payment/rest/getBindings.do',//Запрос списка всех связок клиента
        'bindingsByCardOrId'    => 'https://securepayments.sberbank.ru/payment/rest/getBindingsByCardOrId.do',//Запрос списка связок определённой банковской карты
        'lastOrders'            => 'https://securepayments.sberbank.ru/payment/rest/getLastOrdersForMerchants.do',//Запрос статистики по платежам за период
        'updateSSLCardList'     => 'https://securepayments.sberbank.ru/payment/rest/updateSSLCardList.do',//Запрос добавления карты в список SSL-карт
    ];

    private $userName = '';
    private $password = '';
    private $returnUrl = '';
    private $failUrl = '';



    const CURRENCY =[
        'rub' => 643,
        'usd' => 840,
        'eup' => 978,
        'gbp' => 826,
        'jpy' => 392,
    ];

    public function __construct()
    {
        $this->userName = Yii::$app->params['sber']['userName'];
        $this->password = Yii::$app->params['sber']['password'];
        $this->returnUrl = Yii::$app->params['sber']['returnUrl'];
        $this->failUrl  = Yii::$app->params['sber']['failUrl'];
    }

    public function registerOrder($amount = false)
    {

        if(!$amount){
            return false;
        }

        if(is_numeric($amount)){
            $this->setAmount($amount);
        }
        else{
            return false;
        }

        $shopTransaction = $this->fillEmptyAccount(1, $this->getAmount());

        $params['userName'] = $this->userName;
        $params['password'] = $this->password;
        $params['orderNumber'] = $this->order->id;
        $params['amount'] = $this->amount;
        $params['currency'] = self::CURRENCY['rub'];
        $params['returnUrl'] = $this->returnUrl;
        $params['failUrl'] = $this->failUrl;
        $result = $this->requestPaymentGet(self::SBERBANK_URLS['register'].'?'.http_build_query($params));
        if(!isset($result['errorCode'])){
            $this->paymentId = $result['orderId']; //пока не понятно нахер нужно в "глобальном" доустпе
            $trasactionUpd = UsersPays::find()->where(['id'=>$shopTransaction])->one();
            if(!empty($trasactionUpd)){
                $trasactionUpd->transaction_id = $result['orderId'];
                if($trasactionUpd->save(true)){
                    return $result['formUrl'];
                }
            }
        }
        else{
            //пишем лог
            $this->errorsLogInText($result);
        }
        return false;

    }

    public function getTransactionStatus($transactionId = false, $flagAmount = false)
    {
        if(!$transactionId){
            return false;
        }

        $params['userName'] = $this->userName;
        $params['password'] = $this->password;
        $params['orderId'] = $transactionId;
        $result = $this->requestPaymentGet(self::SBERBANK_URLS['orderStatus'].'?'.http_build_query($params));
        if($result['ErrorCode']==0 && $result['OrderStatus']==2){//платеж успешно звершен
            if($flagAmount){
                $this->amount = $result['Amount'];
            }
            return $result;
        }
        else{
            $userPay = UsersPays::find()->where(['transaction_id'=>$transactionId])->one();
            if(!empty($userPay)){
                $userPay->error_code = $result['ErrorCode'].$result['OrderStatus'];
                $userPay->save(true);
            }
        }
        return false;

    }

    public function getTransactionStatusExtended($transactionId = false)
    {
        //описать при необходимости
        if(!$transactionId){
            return false;
        }
        //$params['userName'] = $this->userName;
        //$params['password'] = $this->password;
        //$params['orderId'] = $this->paymentId;
        //$result = $this->requestPaymentGet(self::SBERBANK_URLS['orderStatusExtended'].'?'.http_build_query($params));
        //print_r($result);
        return false;
    }

    public function CancelPaymentOrder($payId){
        $params['userName'] = $this->userName;
        $params['password'] = $this->password;
        $params['orderId'] = $payId;
        $result = $this->requestPaymentGet(self::SBERBANK_URLS['reverse'].'?'.http_build_query($params));
        //print_r($result);
    }


    private function requestPaymentPost($params, $url){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($curl);
        print_r($response);
        curl_close($curl);
        return json_decode($response);
    }

    private function requestPaymentGet($url){
        $curl = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPGET => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        );
        curl_setopt_array($curl, $options);
        $data = curl_exec($curl);
        $result=json_decode($data, true);
        //print_r($result);
        curl_close($curl);
        return $result;
    }

}