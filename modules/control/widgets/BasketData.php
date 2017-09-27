<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class BasketData extends Widget{
    public $order;

    public function run(){
        if(empty($this->order)){
            return false;
        }?>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Стоимость</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->money?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Стоимость доставки</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->deliveryPrice?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Бонусы</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->bonus?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Промо-код</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->code_id ? $this->order->promoCode->code : ''?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Отчисления по промо-коду</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->fee?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Тип</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->type?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Комментарий</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->comments?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Комментарий Call-центра</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->comments_call_center?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Статус Call-центра</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->call_status?></span></div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right">Статус</div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $this->order->status?></span></div>
        </div>

        <?php
    }
}
