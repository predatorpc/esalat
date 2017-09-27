<?php

namespace app\modules\actions\controllers;

use app\modules\basket\models\BasketLg;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Zloradnij;

class TestController extends FrontController
{
    public function actionTest()
    {
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, 'http://www.extremeshop.ru/my/response-payment-center');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->order));
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
//        $data = curl_exec($ch);
//
//        Zloradnij::print_arr($data);


        $a = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/a-000.txt');
        $b = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/a-001.txt');

        $response = json_decode($a);
        Zloradnij::print_arr($response);

        $basket = (new BasketLg())->findById(intval($response->Order_Id));
        Zloradnij::print_arr($basket);
    }
}

