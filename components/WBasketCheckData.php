<?php

namespace app\components;

use app\modules\basket\models\BasketLg;
use app\modules\common\models\Address;
use app\modules\common\models\ModFunctions;
use yii\base\Widget;

class WBasketCheckData extends Widget{
    public $basket;

    public function init(){
        parent::init();
        if($this->basket === null){
            $this->basket = false;
        }
    }

    public function run(){
        if(!$this->basket){
            return false;
        }?>
        <div class="modal_result__com">
        <?php if(empty(\Yii::$app->basket->getErrors())){?>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right"><b><?=\Yii::t('app','Адрес доставки');?>:</b></div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <?= Address::find()
                        ->where(['id' => $this->basket->address_id])
                        ->select(['CONCAT(city,\', \',district,\', \',street,\', \',house,\', \',room)'])
                        ->scalar()?>
                </div>
                &nbsp;
            </div>
            <div class="row">
                <?php
                $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
                $deliveryGroup->setProducts(\Yii::$app->basket->getBasketProducts());
                $deliveryGroup->setDeliveryId($this->basket->delivery_id);
                $deliveryGroup->setProductDeliveryGroup();

                $i = 0;
                foreach (json_decode($this->basket->time_list,true) as $key => $item) {
                    if(!empty($deliveryGroup->getDateList($key))){
                        //$deliveryGroup->setMinDayDelivery();
                        $deliveryGroupDateList = $deliveryGroup->getDateList($key);
                        //$deliveryGroupDateList = $deliveryGroup->getUpdatedDteList($deliveryGroupDateList);
                        if(!empty($item['time']) && !empty($deliveryGroupDateList[$item['day']][$item['time']])){
                            if($i == 0){?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right"><b><?=\Yii::t('app','Дата и время');?>:</b></div><?php
                            }else{?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right">&nbsp</div><?php
                            }?>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"><?= date('d.m.Y',$item['time'])?>, <?= $deliveryGroup->getDateList($key)[$item['day']][$item['time']]?></div><?php
                            $i++;
                        }
                    }
                }?>

                &nbsp;
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right"><b><?=\Yii::t('app','Способ оплаты');?>:</b></div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <?php
                    $resultPrice = \Yii::$app->basket->getResultPrice();
                    //$basketDiscount = \Yii::$app->basket->getBasketDiscount();
                    $user = $this->basket->user;
                    if($this->basket->payment_id == 2 && ($resultPrice - $user->money) > 0){?>
                        <?=\Yii::t('app','Доплатить с банковской карты');?> (<b><?= ModFunctions::money($resultPrice - $user->money)?></b>)<?php
                    }
                    if($this->basket->payment_id == 2 && ($resultPrice - $user->money) <= 0){?>
                        <?=\Yii::t('app','Оплата с банковской карты');?><?php
                    }
                    if($this->basket->payment_id == 3 && ($resultPrice - $user->money) > 0){?>
                        <?=\Yii::t('app','Доплатить с сохраненной банковской карты');?> (<b><?= ModFunctions::money($resultPrice - $user->money)?></b>)<?php
                    }
                    if($this->basket->payment_id == 3 && ($resultPrice - $user->money) <= 0){?>
                        <?=\Yii::t('app','Оплата с сохраненной банковской карты');?><?php
                    }
                    if($this->basket->payment_id == 1){?>
                        <?=\Yii::t('app','Оплата с лицевого счета, доступно');?>(<b><?= ModFunctions::money($user->money)?></b>)<?php
                    }
                    ?>
                </div>
                &nbsp;
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right"><b><?=\Yii::t('app','Комментарий');?>:</b></div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" id="comments-modal"></div>
                &nbsp;
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right"><b><?=\Yii::t('app','Итог')?>:</b></div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <div class="i">
                        <span class="col-lg-4 col-md-4 col-sm-4 col-xs-6"><b><?=\Yii::t('app','Сумма')?>:</b></span>
                    <span class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
                        <?= ModFunctions::money($this->basket->basketPrice)?>
                    </span>
                        <div class="clear"></div>
                    </div>
                    <div class="i">
                        <span class="col-lg-4 col-md-4 col-sm-4 col-xs-6"><b><?=\Yii::t('app','Скидка')?>:</b></span>
                    <span class="col-lg-8 col-md-8 col-sm-8 col-xs-6 ">
                        <?= ModFunctions::money($this->basket->basketDiscount*(-1))?>
                    </span>
                        <div class="clear"></div>
                    </div>
                    <div class="i">
                        <span class="col-lg-4 col-md-4 col-sm-4 col-xs-6"><b><?=\Yii::t('app','Бонусы')?>:</b></span>
                    <span class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
                        <?= ModFunctions::money($this->basket->basketBonus*(-1))?>
                    </span>
                        <div class="clear"></div>
                    </div>
                    <div class="i">
                        <span class="col-lg-4 col-md-4 col-sm-4 col-xs-6"><b><?=\Yii::t('app','Доставка')?>:</b></span>
                    <span class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
                        <?= ModFunctions::money($this->basket->delivery_price)?>
                    </span>
                        <div class="clear"></div>
                    </div>
                    <div class="border-b"></div>
                    <div class="i total-content">
                        <span class="col-lg-4 col-md-4 col-sm-4 col-xs-6 total"><?=\Yii::t('app','Итого')?>:</span>
                    <span class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-muted total-money">
                        <?= ModFunctions::money($resultPrice)?>
                    </span>
                    </div>
                </div>
                &nbsp;
            </div>
            <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right">
                <!-- div class="button btn btn-danger" onclick="close_basket_check_data()">Назад</div-->
                <button type="button" class="button btn btn-danger" data-dismiss="modal" aria-hidden="true" style="width:100px;"><?=\Yii::t('app','Назад')?></button>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 button_success text-center hidden"><?php
                if(($this->basket->payment_id == 2 || $this->basket->payment_id == 3) && (!empty(\Yii::$app->user->identity) && in_array(\Yii::$app->user->identity->id,[/*10015520,*/10013387,10015549, /*10014596/*,10000123*/]))){
                    // Проверка доплаты;
                    if (($resultPrice - $user->money) > 0) {
                        // Сумма доплаты;
                        $money = number_format(($resultPrice - $user->money), 2, '.', '');
                    } else {
                        // Сумма платежа;
                        $money = number_format($resultPrice, 2, '.', '');
                    }
                    ?>
                    <form action="https://secured.payment.center" method="POST" name="paymentPayCenter">
                        <input type="hidden" name="Amount" value="<?= number_format($money, 2, '.', '')?>" />
                        <input type="hidden" name="Currency" value="<?= \Yii::$app->params['payment-center']['Currency']?>" />
                        <input type="hidden" name="Desc" value="Оплата заказа" />
                        <input type="hidden" name="Operation" value="<?= \Yii::$app->params['payment-center']['Operation']?>" />
                        <input type="hidden" name="Order_id" value="<?= $this->basket->id;?>" />
                        <input type="hidden" name="Service_id" value="<?= \Yii::$app->params['payment-center']['Service_Id']?>" />
                        <input type="hidden" name="Type" value="<?= \Yii::$app->params['payment-center']['Type']?>" />
                        <input type="submit" id="pay_001" value="<?=\Yii::t('app','Купить')?>"  class="payment-center button btn btn-success order-success" style="display: inline-block;width:100px;"/>
                    </form><?php
                }else{?>
                    <div id="pay_001" class="button btn btn-success order-success" style="display: inline-block;width:100px;"><?=\Yii::t('app','Купить')?></div><?php
                }?>
                <div class="button_load" style="margin: -5px 0px 0px 60px; display: none;"></div>
            </div>
                <div class="button_load" style="position: relative; top: -7px;"></div>
            </div><?php
            if(!empty(\Yii::$app->user->identity)){?>
                <input type="hidden" name="" value="<?= \Yii::$app->user->identity->id?>" />
                <?php
            }
        }else{
            foreach (\Yii::$app->basket->getErrors() as $error) {
                print '
                <div class="alert-danger">
                '.$error.'
                </div>
                ';
            }
         } ?>
            <div class="clear"></div>
        </div>
<?php }
}
