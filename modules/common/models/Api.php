<?php
namespace app\modules\common\models;

// Описание API;
class Api {

    var $server_ip = "192.168.0.10";
    var $extremefitness = true;
    public $sms_key = "df00faa95ae8e85f0b8f69e27713992c";

    public static function requestWFPost($params = [])
    {
        //print_r($params);die();
        //$curl = curl_init('http://192.168.0.1/api/shop');
        $curl = curl_init('http://192.168.0.254/ajax/api-interface');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    /*
        // Поиск клиента ExtremeFitness;
        function client_find($phone, $card = false) {
            if ($this->extremefitness) {
                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, "http://".$this->server_ip."/api/extremefitness");
                curl_setopt($c, CURLOPT_FAILONERROR, 1);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_TIMEOUT, 0);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, "phone=".urlencode($phone)."&card=".$card);
                $data = curl_exec($c);
                curl_close($c);
                // Вывод данных;
                return $data;
            }
            return false;
        }
    */
    // Загрузка данных клиента ExtremeFitness;
    function client_info($client_id, $operations = false) {
        if ($this->extremefitness) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, "http://".$this->server_ip."/api/extremefitness");
            curl_setopt($c, CURLOPT_FAILONERROR, 1);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_TIMEOUT, 0);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, "client_id=".$client_id."&operations=".$operations);
            $data = curl_exec($c);
            curl_close($c);
            // Обработка данных;
            $data = json_decode($data, true);
            // Вывод данных;
            return $data;
        }
        return false;
    }

    // Рассчет цены за доставку курьером;
    function delivery_price($order_group_id) {
        // Загрузка данных;
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "http://delivery.Esalad.ru");
        curl_setopt($c, CURLOPT_FAILONERROR, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT, 0);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, "get_price=true&order_group_id=".json_encode($order_group_id));
        $data = curl_exec($c);
        curl_close($c);
        // Вывод данных;
        return $data;
    }
    /*

        // Пополнение баланса клиента ExtremeFitness;
        function client_money_in($type, $client_id, $money, $comments = '') {
            if ($this->extremefitness) {
                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, "http://".$this->server_ip."/api/extremefitness");
                curl_setopt($c, CURLOPT_FAILONERROR, 1);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_TIMEOUT, 0);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, "money_in=true&type=".$type."&client_id=".$client_id."&money=".$money."&comments=".urlencode($comments));
                $data = curl_exec($c);
                curl_close($c);
                // Вывод данных;
                return $data;
            }
            return false;
        }

        // Списание средств с баланса клиента ExtremeFitness;
        function client_money_out($type, $client_id, $money, $comments = '') {
            if ($this->extremefitness) {
                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, "http://".$this->server_ip."/api/extremefitness");
                curl_setopt($c, CURLOPT_FAILONERROR, 1);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_TIMEOUT, 0);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, "money_out=true&type=".$type."&client_id=".$client_id."&money=".$money."&comments=".urlencode($comments));
                $data = curl_exec($c);
                curl_close($c);
                // Вывод данных;
                return $data;
            }
            return false;
        }

        // Отправка данных о заказе на сервер поставщика;
        function order($url, $order) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_FAILONERROR, 1);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_TIMEOUT, 0);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, "order=".json_encode($order));
            $data = curl_exec($c);
            curl_close($c);
            // Обработка данных;
            $data = json_decode($data, true);
            // Вывод данных;
            return $data;
        }

        // Отправка уведомления о заказе;
        function order_notice($order_id) {
            // Обработка данных;
            $order_id = intval($order_id);
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, "http://".$_SERVER['SERVER_NAME']."/systems/core/orders_notices.php");
            curl_setopt($c, CURLOPT_FAILONERROR, 1);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_TIMEOUT, 0);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, "order_id=".json_encode($order_id));
            $data = curl_exec($c);
            curl_close($c);
            // Вывод данных;
            return $data;
        }
    */
    // Отправка SMS (сервис MTC);
//    function sms($phone, $message) {
//        //$phone = '79237271543';
//        // Загрузка данных;
//        $c = curl_init("http://mcommunicator.ru/M2M/m2m_api.asmx/SendMessage");
//        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($c, CURLOPT_TIMEOUT, 30);
//        curl_setopt($c, CURLOPT_POSTFIELDS, 'msid='.str_replace('+', '', $phone).'&message='.$message.'&naming=EXTREME&login=extremefitness&password='.$this->sms_key);
//        $data = curl_exec($c);
//        curl_close($c);
//        // Вывод данных;
//        return $data;
//    }
    // Отправка SMS (новый сервис MTC);
    function sms($phone, $message) {
        $phone = str_replace('+7', '', $phone);
        $phone = str_replace('+', '', $phone);
        $phone = '7' . $phone;
        // Загрузка данных;
        $c = curl_init("https://api.vas-stream.ru/a2p");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT, 30);
        curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($c, CURLOPT_POSTFIELDS, 'operation=send&login=versal_http&onum=EXTREMESHOP&unum='.str_replace('+', '', $phone).'&msg='.urlencode($message).'&sign='.sha1(str_replace('+', '', $phone).urlencode($message).'OEyvkFgA'));
        curl_setopt($c, CURLOPT_POSTFIELDS, 'operation=send&login=versal_http&onum=Esalad&unum='.str_replace('+', '', $phone).'&msg='.urlencode($message).'&sign='.sha1(str_replace('+', '', $phone).urlencode($message).'9RJFGTBIK'));
        $data = curl_exec($c);
        curl_close($c);
        // Вывод данных;
        return $data;
    }

    function sdm($order_item_id,$action,$type,$message = false){
        // Обработка данных;
        $order_item_id = intval($order_item_id);
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "".$this->server_ip."/systems/servicedesk.php");
        curl_setopt($c, CURLOPT_FAILONERROR, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT, 0);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, "no_auth=true&action=".$action."&order_item_id=".$order_item_id."&type=".$type);
        $data = curl_exec($c);
        curl_close($c);
        $data = json_decode($data, true);
        // Вывод данных;
        return $data;
    }
    /*
        // Добавление бонусов;
        function bonus_in($user_id, $type, $bonus) {
            // Загрузка данных;
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, "http://".$_SERVER['SERVER_NAME']."/systems/api/extremeshop.php");
            curl_setopt($c, CURLOPT_FAILONERROR, 1);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_TIMEOUT, 0);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, "bonus_in=true&user_id=".json_encode($user_id)."&type=".json_encode($type)."&bonus=".json_encode($bonus));
            $data = curl_exec($c);
            curl_close($c);
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/files/logs/__bonus.log', date("d.m.Y, H:i:s").' # OK '.$user_id." = ".$bonus."(".$type.")"."\r\n", FILE_APPEND);
            // Вывод данных;
            return $data;
        }

        // Списание бонусов;
        function bonus_out($user_id, $bonus) {
            // Загрузка данных;
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, "http://".$_SERVER['SERVER_NAME']."/systems/api/extremeshop.php");
            curl_setopt($c, CURLOPT_FAILONERROR, 1);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_TIMEOUT, 0);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, "bonus_out=true&user_id=".json_encode($user_id)."&bonus=".json_encode($bonus));
            $data = curl_exec($c);
            curl_close($c);
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/files/logs/__bonus.log', date("d.m.Y, H:i:s").' # OK '.$user_id." = ".$bonus."\r\n", FILE_APPEND);
            // Вывод данных;
            return $data;
        }
    */
}

// Создание объекта API;
/*
$api = new API;
$api->server_ip = $server_ip;
$api->extremefitness = $extremefitness;
$api->sms_key = $sms_key;
*/
