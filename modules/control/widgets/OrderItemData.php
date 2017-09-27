<?php

namespace app\modules\control\widgets;

use app\modules\catalog\models\Goods;
use app\modules\common\models\Zloradnij;
use yii\base\Widget;

class OrderItemData extends Widget{
    public $orderItem;

    public function run(){
        if(empty($this->orderItem)){
            return false;
        }

        ?>

        <div
            class="row order-item-element"
            data-order-id="<?= $this->orderItem->orders->id?>"
            data-order-group-id="<?= $this->orderItem->orderGroup->id?>"
            data-order-item-id="<?= $this->orderItem->id?>"

            data-order-item-product-id="<?= $this->orderItem->good->id?>"
            data-order-item-variant-id="<?= $this->orderItem->goodsVariations->id?>"
            data-order-item-product-count-pack="<?= $this->orderItem->good->count_pack?>"
            data-order-item-product-comission-id="<?= $this->orderItem->good->comissionId?>"

            data-order-item-price="<?= $this->orderItem->price?>"
            data-order-item-bonus="<?= $this->orderItem->bonus?>"
            data-order-item-discount="<?= $this->orderItem->discount?>"
            data-order-item-count="<?= $this->orderItem->count?>"
            data-order-item-result-price="<?= ($this->orderItem->price - $this->orderItem->bonus - $this->orderItem->discount) * $this->orderItem->count?>"
        >
            <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1"><img style="width:100%;max-width: 100%;max-height:100px;" src="<?= \Yii::$app->params['domain']?><?= $this->orderItem->good->imagesRelationWithPath[0]?>" /></div>
            <div class="col-xs-6 col-sm-8 col-md-8 col-lg-10">
                <div class="col-xs-12 col-sm-7 col-md-6 col-lg-5">
                    <a href="<?//=$ordersItem->good->catalogUrl?>"><?=$this->orderItem->good->name?></a>
                    <br />
                    <div class="tags tags__info properties-data"><?php
                        print OrderItemVariations::widget(['orderItem' => $this->orderItem]);?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-7">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 price-input-block">

                        <input type="hidden" name="order-item-price" class="order-item-price" value="<?= $this->orderItem->price?>" />
                        <input type="hidden" name="order-item-bonus" class="order-item-bonus" value="<?= $this->orderItem->bonus?>" />
                        <input type="hidden" name="order-item-discount" class="order-item-discount" value="<?= $this->orderItem->discount?>" />

                        <input type="hidden" name="order-item-count" class="order-item-count" value="<?= $this->orderItem->count?>" />
                        <input type="hidden" name="order-item-result-price" class="order-item-result-price" value="<?= ($this->orderItem->price - $this->orderItem->bonus - $this->orderItem->discount) * $this->orderItem->count?>" />

                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 price">
                            <span class="price variation-price"><?= \app\modules\common\models\ModFunctions::money($this->orderItem->price)?></span><br />
                            <span class="price variation-price"> - <?= $this->orderItem->bonus?> β.</span><br />
                            <span class="price discount variation-discount-price"> - <?= \app\modules\common\models\ModFunctions::money($this->orderItem->discount)?></span>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <span class="s">&nbsp;<br />&times</span>
                        </div>

                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 order-item-count-data">
                        <?= OrderItemCountData::widget(['orderItem' => $this->orderItem])?>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

                        <br />
                        <span class="s">=</span>
                        <span class="money variation-money">
                            <?= \app\modules\common\models\ModFunctions::money(($this->orderItem->price - $this->orderItem->bonus - $this->orderItem->discount) * $this->orderItem->count)?>
                        </span>
                        <div class="money">
                            <b class="hidden_r">Итого:</b>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                <a data-pjax="0" data-method="post" data-confirm="Вы уверены, что хотите удалить этот товар из заказа?" aria-label="Удалить" title="Удалить" href="/control/orders/order-item-delete/?id=<?= $this->orderItem->id?>">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </div>
        </div><?php
    }
}
