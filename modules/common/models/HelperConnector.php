<?php

namespace app\modules\common\models;

class HelperConnector{
    private $config = [
        'uniqueShopKey' => '57a17952800d0',
        'api-version' => '1.0.0.1',
        'ip-helper' => '',
//        'url-helper' => 'http://demo.extremeshop.ru/ajax/createorderapi',
        'url-helper' => 'http://helper.express/ajax/createorderapi',
        'url-cancel-helper' => 'http://helper.express/ajax/cancelorderapi',

        'error-email' => 'zloradnij@mail.ru',
        'error-phone' => '9237271543',
        'get-error-notification' => '1',
        'get-success-notification' => '1',
        'success-url' => 'http://Esalad/api/api-helper-success',
        'error-url' => 'http://Esalad/api/api-helper-error',
    ];
    private $configPath;
    private $order;
    private $errors;

    public function __construct()
    {
        $this->setConfig();
        $this->checkConfig();
    }

    private function checkOrder(){
        if(empty($this->order['points']) || count($this->order['points']) < 2){
            $this->setError('Few points');
        }

        if(!empty($this->order['points'])){
            foreach ($this->order['points'] as $i => $point) {
                if(empty($point['AdministrativeAreaName']) || empty($point['city'])){
                    $this->setError('Few Data FromPoint #' . $i);
                }
            }
        }

        if(empty($this->order['delivery_date'])){
            $this->setError('Empty Delivery Date');
        }

        if(empty($this->order['time'])){
            $this->setError('Empty Delivery Time');
        }

        if(empty($this->order['response_id'])){
            $this->setError('Empty Order ID');
        }

        if(empty($this->order['response_id'])){
            $this->setError('Empty Far Far Way');
        }

        return empty($this->getErrors()) ? true : false;
    }

    private function setError($error){
        $this->errors[] = $error;
    }

    public function getErrors(){
        return $this->errors;
    }

    private function checkConfig(){
        $error = 0;
        if(empty($this->config)){
            $this->setError('Empty config');
            $error++;
        }

        if(empty($this->config['uniqueShopKey']) || strlen($this->config['uniqueShopKey']) != 13){
            $this->setError('Unique Shop Key Not Found');
            $error++;
        }

        if(empty($this->config['url-helper'])){
            $this->setError('Helper Api Url Not Found');
            $error++;
        }

        if(($this->config['get-error-notification'] == 1 || $this->config['get-success-notification'] == 1) && (empty($this->config['error-email']) && empty($this->config['error-phone']))){
            $this->setError('Notification Config Error');
            $error++;
        }

        return empty($error) ? true : false;
    }

    private function setConfig(){
        if(!empty($this->configPath) && file_exists($this->configPath)){
            $this->config = require($this->configPath);
        }
    }

    public function setConfigPath($configPath){
        $this->configPath = $configPath;
    }

    /** config file - example configHelper.php
     *  return array with settings
     * */
    public function getConfig(){
        return $this->config;
    }

    public function orderShipping(){
//        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre1.txt',"\n\r--------ERROR!!!----------\n\r".var_export($this->order,true));
        if(empty($this->errors) && !empty($this->order) && $this->checkOrder()){
            $this->order['uniqueShopKey'] = $this->config['uniqueShopKey'];
            $this->order['error-email'] = $this->config['error-email'];
            $this->order['error-phone'] = $this->config['error-phone'];
            $this->order['get-error-notification'] = $this->config['get-error-notification'];
            $this->order['get-success-notification'] = $this->config['get-success-notification'];
            $this->order['success-url'] = $this->config['success-url'];
            $this->order['error-url'] = $this->config['error-url'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->config['url-helper']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->order));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $data = curl_exec($ch);

//            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/treSet.txt',"\n\r--------SET DATA!!!----------\n\r".var_export($postDataList,true));
            /*if(!$data){
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre1.txt',"\n\r--------ERROR!!!----------\n\r".var_export($data,true));
            }else{
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tre1.txt',"\n\r--------GET DATA -- 2!!!----------\n\r".var_export($data,true));
            }*/
            curl_close($ch);
//            Zloradnij::print_arr($data);
//            Zloradnij::print_arr($this->order);

            if(!empty($data)){
                $this->responseProcessing($data);
            }


        }else{
            $this->setError('Order Failure');
        }
    }

    public function setOrder($order){
        $this->order = $order;
        if($this->checkOrder()){
            return true;
        }
        return false;
    }

    public function sendCancel($item){
        if(!is_array($item)){
            return false;
        }
        $item['uniqueShopKey'] = $this->config['uniqueShopKey'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url-cancel-helper']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($item));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        //print_r($data);
        return false;

    }

    public function cancelItem($orderId = false, $itemVariant=false){
        if(((!$orderId) && intval($orderId)>0 ) || ((!$itemVariant) && (intval($itemVariant)>0)) ){
            return false;
        }

        $sendParam['order_id']=$orderId;
        $sendParam['variation_id']=$itemVariant;
        $sendParam['uniqueShopKey'] = $this->config['uniqueShopKey'];
        $response = $this->sendToHelper($sendParam);
        //$resp = json_decode($response);
        return false;
    }

    /*
    private function sendToHelper($param){
        if(!empty($param)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->config['url-helper-cancel']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;

        }
        return false;

    }*/


    private function responseProcessing($data){
        if(!empty($data['status']) && $data['status'] == 'false'){
            //Zloradnij::print_arr($data);
        }elseif(!empty($data['status']) && $data['status'] == 'true'){
            //Zloradnij::print_arr($data);
        }else{
            // NLO
        }
    }
}