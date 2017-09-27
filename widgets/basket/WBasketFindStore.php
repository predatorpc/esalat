<?php

/* @var $product app\modules\catalog\models\Goods */

namespace app\widgets\basket;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\common\models\ModFunctions;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class WBasketFindStore extends Widget
{
    public $basket;

    public function init()
    {
        parent::init();

        if ($this->basket === null) {
            return false;
        }
    }

    public function run(){?>
        <input
            type="hidden"
            name="currentClub"
            value="1"
        />
        <div class="all-products-input-for-set-store-params" data-json='<?= $this->basket->getStoreListJson()?>'><?php
            foreach ($this->basket->products as $product) {?>
                <input
                type="hidden"
                data-product-id="<?= $product->product_id?>"
                data-shop-group-id="<?= $product->product->shop->id?>"
                data-store-id="<?= $product->product->shop->shops[0]->stores[0]->id?>"
                data-address-string="<?= $product->product->shop->shops[0]->stores[0]->addressString->concatAddress?>"
                name="StoreList[<?= $product->product_id?>]"
                value="<?= $product->product->shop->shops[0]->stores[0]->id?>"
                /><?php
            }?>
        </div>

        <?php
    }
}

