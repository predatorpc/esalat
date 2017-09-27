<?php

namespace app\modules\control\models;

use app\modules\catalog\models\Codes;
use app\modules\common\models\ActiveRecordRelation;
use app\modules\common\models\UsersBonus;
use app\modules\common\models\UsersPays;
use app\modules\shop\models\Orders;
use Yii;

class UserBalance
{
    private $maxMoney;
    private $money;

    private $maxBonus;
    private $bonus;

    private $order;

    public function __construct(Orders $order){
        $this->order = $order;

        $this->setMaxMoney($this->order->user->bonus + $this->order->bonus);
        $this->setMaxBonus($this->order->user->money + $this->order->money);
    }

    public function setMaxMoney($money){
        $this->maxMoney = $money;
    }

    public function getMaxMoney(){
        return $this->maxMoney;
    }

    public function setMoney($money){
        $this->money = $money;
    }

    public function getMoney(){
        return $this->money;
    }
    //--------------------------------------------------
    public function setMaxBonus($bonus){
        $this->maxBonus = $bonus;
    }

    public function getMaxBonus(){
        return $this->maxBonus;
    }

    public function setBonus($bonus){
        $this->bonus = $bonus;
    }

    public function getBonus(){
        return $this->bonus;
    }
}
