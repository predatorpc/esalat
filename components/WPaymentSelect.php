<?php

namespace app\components;

use app\modules\common\models\Zloradnij;
use yii\base\Widget;

class WPaymentSelect extends Widget{
    public $basket;
    public $sort = 4;

    public function run(){
        if(empty($this->basket)){
            return false;
        }
        //$resultPrice = $this->basket->basketPriceDiscount;
        $resultPrice = \Yii::$app->action->getResultPrice();
        $userCards = $this->basket->user->cards;

        $surcharge = false;

        // Флаг для вывода доплаты вместо оплаты с банковской карты;
        if(empty($this->basket->delivery_price)){
            $this->basket->delivery_price = 0;
        }

        if (($this->basket->delivery_price + $this->basket->basketPriceDiscount - $this->basket->user->money) > 0) {
            $surcharge = $this->basket->delivery_price + $this->basket->basketPriceDiscount - $this->basket->user->money;
        }



        if($this->basket->user && $this->basket->user->money){?>
            <div class="item radio">
            <label class="radio__label">
                <input id="payment-1" type="radio" name="payment_id" value="1"<?= (($this->basket->payment_id == 1 && $surcharge <= 0)?' checked': ' ').((($resultPrice > IntVal(\Yii::$app->user->identity->money)) || empty($resultPrice)) ? ' disabled': '  ')?> class="radio" />
                <span class="radio-checked"><?=\Yii::t('app','Оплата с лицевого счета, доступно')?> (<?= floor($this->basket->user->money)?><small class="rubznak">p.</small>)</span>
                <div class="description-min"><?=\Yii::t('app','Денежные средства спишутся с вашего лицевого счета в магазине')?>.</div>
            </label>
            </div><?php
        }
        foreach($userCards as $i => $card){?>
            <div class="" style="display: none;"><div class="item radio">
            <input type="hidden" name="card_id" value="<?= $card->id?>" />
            <label class="radio__label">
                <input id="payment-3-<?= $i?>" type="radio" name="payment_id" value="3"<?= (($this->basket->payment_id == 3)?' checked':'')?> class="radio" /><?php
                if($surcharge > 0){?>
                    <span class="radio-checked"><?=\Yii::t('app','Доплатить с сохраненной банковской карты')?> (<?= $surcharge?> <small class="rubznak">p.</small>) <?= $card['card_number']?> &nbsp; <a href="/" class="dotted" onclick="return delete_card(<?= $i?>//);"><?=\Yii::t('app','удалить')?></a></span><?php
                }else{?>
                    <span class="radio-checked"><?=\Yii::t('app','Оплата с сохраненной банковской карты') ?> <?= $card['card_number']?>&nbsp; <a href="/" class="dotted" onclick="return delete_card(<?= $i?>//);"><?=\Yii::t('app','удалить')?> </a></span><?php
                }?>
                <div class="description-min"><?=\Yii::t('app','Денежные средства спишутся со счета сохраненной банковской карты')?>.</div>
            </label>
            </div></div><?php
        }
        ?>

        <div class="item radio" data-q="2">
        <label class="radio__label">
            <input id="payment-2" type="radio" name="payment_id" value="2"<?= (($this->basket->payment_id == 2)?' checked':'').((!$this->basket->user->money)?' checked':'')?> class="radio" /><?php
            if($surcharge > 0) {?>
                <span class="radio-checked" ><?=\Yii::t('app','Доплатить с банковской карты')?>(<?= $surcharge?> <small class="rubznak">p.</small>)</span ><?php
            }else{?>
                <span class="radio-checked" ><?=\Yii::t('app','Оплата с банковской карты')?></span ><?php
            }?>
            <div class="description-min"><?=\Yii::t('app','Денежные средства спишутся со счета банковской карты') ?>.</div>
            <!--Сохранения карта-->
            <div class="checkbox hidden">
                <label>
                    <input type="checkbox" class="check" value="1" name="save_card">
                    <?=\Yii::t('app','Запомнить данные моей карты для будущих платежей') ?>
                    <div class="form-links">
                        <a target="_blank" href="http://www.esalad.ru/static/page/rules#payment"> <?=\Yii::t('app','Правила привязки карты') ?></a>
                    </div>
                </label>
            </div>
            <!--/Сохранения карта-->
        </label>

        </div>
        <div class="item radio" data-q="3">
            <div class="checkbox">
                <label>
                    <input type="checkbox" class="check" value="<?=$this->basket->bonus_pay?>" name="bonus_pay" <?=$this->basket->bonus_pay==1 ? 'checked="checked"' : ''?> >
                   <?=\Yii::t('app','Использовать бонусы для оплаты части заказа') ?>
                </label>
            </div>
        </div>
        <?php
    }
}
