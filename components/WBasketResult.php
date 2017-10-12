<?php

namespace app\components;
use app\modules\common\models\ModFunctions;
use app\modules\common\models\Zloradnij;
use yii\base\Widget;

class WBasketResult extends Widget{
    public $basket;

    public function run(){
        //\Yii::$app->basket->setDeliveryPrice();
        //\Yii::$app->action->getCurrentBasket();
        ?>
        <div class="sum" data-basket-id="<?= $this->basket->id?>">
            <div class="i"><?=\Yii::t('app','Сумма');?>: <?= ModFunctions::money($this->basket->basketPrice)?> </div>
            <div class="i"><?=\Yii::t('app','Бонусы');?>: <?= ($this->basket->basketBonus > 0 ? '-' : '' ) . ModFunctions::money($this->basket->basketBonus)?></div>
            <div class="i"><?=\Yii::t('app','Скидка');?>: <?= ($this->basket->basketDiscount > 0 ? '-' : '' ) . ModFunctions::money($this->basket->basketDiscount)?></div>
            <div class="i"><?=\Yii::t('app','Доставка');?>: <?= ModFunctions::money($this->basket->delivery_price)?> </div>
            <div class="border-b"></div>
            <div class="i total">
                <span><?=\Yii::t('app','Итого');?>:</span>
                <span class="total-money"><?= ModFunctions::money($this->basket->delivery_price + $this->basket->basketPriceDiscount)?></span>
            </div>
        </div><?php
    }
}
