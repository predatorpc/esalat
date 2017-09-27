<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class UserReserveData extends Widget{
    public $order;

    public function run(){
        if(empty($this->order)){
            return false;
        }?>

        <div class="row">
            <input type="hidden" name="order-bonus-all" id="order-bonus-all" value="<?= $this->order->user->bonus + $this->order->bonus?>">
            <input type="hidden" name="order-bonus-free" id="order-bonus-free" value="<?= $this->order->user->bonus?>">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Бонусов в наличии / в заказе</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->order->user->bonus?> / <?= $this->order->bonus?></div>
        </div>
        <div class="row">
            <input type="hidden" name="order-money-all" id="order-money-all" value="<?= $this->order->user->money + $this->order->money?>">
            <input type="hidden" name="order-money-free" id="order-money-free" value="<?= $this->order->user->money?>">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Денег в наличии / в заказе</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><?= $this->order->user->money?> / <?= $this->order->money?></div>
        </div>

        <?php
    }
}
