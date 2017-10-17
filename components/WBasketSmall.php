<?php

namespace app\components;

use app\modules\common\models\Zloradnij;
use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketOne;
use app\modules\catalog\models\Goods;
use yii\base\Widget;
use yii\bootstrap\Progress;
use app\modules\common\models\ModFunctions;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "product-item" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WBasketSmall extends Widget {
    public function run(){
        $basket = \Yii::$app->controller->basket;?>
            <div class="basket-content mobile">
                <?php if(!\Yii::$app->user->isGuest && false): ?>

                    <div class="total-top-info" style="display:none">
                        <span class="has">
                               <a class="no-border" href="/basket/" >
                                 <?php if (empty(\Yii::$app->params['en'])): ?>
                                  <?= \app\components\WBasketDeliveryFree::widget()?>
                                  <?= \app\components\WBasketCardFree::widget()?>
                                  <?= \app\components\WBasketCardFreeGoldPlus::widget()?>
                                 <?php endif; ?>
                                </a>
                            <div class="clear"></div>
                        </span>
                    </div>
                <?php endif; ?>
                <!-->
                <a class="basket  no-border" href="/basket/" >
                    <img src="/images/mobil/icon-basket.png"  class="hidden"/>
                    <img src="/images/mobil/basket.svg"  width="35px"/>
                    <?php if(!empty($basket) && $basket->getCount() > 0  ): ?><div class="count icon-counts "><?=$basket->count?></div><?php endif; ?>
                </a>  <!--.Корзина-->
                <a href="/basket/"><div class="money"><?=ModFunctions::money($basket->basketPrice)?></div></a>
                <div class="clear"></div>
            </div>

        <!--Корзина-->
        <div class="basket desktop">
            <!--Акция бонус-->
            <div class="basket-bonus hidden">
               <div>Добавьте в корзину товаров на <b class="money">10</b> руб. и получите в подарок <b class="bonus">20</b> бонусов</div>
               <div class="hidden">Оформите покупку и получите в подарок <b class="bonus">10</b> бонусов</div>
            </div><!--/Акция бонус-->
            <!--Корзина всплывашка инфо-->
            <div class="basket-bonus basket-info-goods hidden">
                <div>Текст <b>100</b> руб.</div>
                <div>Текст Текст Текст Текст</div>
            </div><!--Корзина всплывашка инфо-->
            <a href="/basket/" class="no-border">
                <div class="basket-icon _master_h_basket    <?php if($basket->count > 0) :?>open<?php endif; ?> ">
                    <div id="countsmallbasket" class="counts"> <?=(isset($basket->count) ? $basket->count : '0')?></div>
                </div>
            </a>
            <div class="block">
                <?php if($basket->count > 0) :?>
                    <a class="no-border" href="/basket/">
                        <div class="money">
                            <?=$basket->basketPrice?>
                            <small class="rubznak">p.</small>
                        </div>
                    </a>
                <?php else: ?>
                    <div href="/basket/" class="no"><?=\Yii::t('app', 'Нет товаров')?></div>
                <?php endif; ?>
                <?php
                if($basket->count > 0) :?>
                    <!--Товар в корзине-->
                    <div class="box-container">
                        <div class="goods">
                            <?php foreach($basket->products as $key =>$product): ?>
                                <div class="item" id="<?= $product->variant_id?>">
                                    <a href="<?= $product->product->category->catalogPath . $product->product_id?>" class="black">
                                        <span class="images"><img src="http://www.esalad.ru<?= Goods::findProductImage($product->product_id);?>" alt="" class="ad"/></span>
                                        <span class="title" title="<?= $product->product->name?>"><?= $product->product->name?></span>
                                        <span class="price"><?= ModFunctions::money($product->priceDiscount)?></span>
                                        <span class="count"><?= $product->count?> шт.</span>
                                    </a>
                                    <span
                                        class="close delete"
                                        aria-hidden="true"
                                        data-basket-item="<?= $product->id?>"
                                        data-page="<?= \Yii::$app->controller->uniqueId?>"
                                    >&times;</span>
                                </div>
                            <?php endforeach; ?>
                            <div class="clear"></div>
                        </div>
                        <div class="button"><a href="/basket/" class="button_oran"><?=\Yii::t('app', 'В корзину')?></a></div>
                    </div><!--.Товар в корзине-->
                <?php endif; ?>
            </div>
        </div>

    <?php
    }
}
