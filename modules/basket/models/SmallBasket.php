<?php

namespace app\modules\basket\models;

use Yii;

class SmallBasket
{
    public $userId = false;
    public $sessionId = false;
    public $productList = [];
    public $allPrice = 0;

    public function init()
    {
        $basket = new BasketLg();

        if(Yii::$app->user->identity){
            $basket = $basket->findByUserId(Yii::$app->user->identity->getId());
        }else{
            $basket = $basket->findBySessionId(Yii::$app->session->id);
        }

        return $basket;
    }
}
