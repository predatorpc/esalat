<?php

namespace app\components;

use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Nav;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "catalog-menu-left" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WCatalogListButtonBlock extends Widget
{
    public $basketItem;

    public function run(){


        ?>

        <?php if(isset($this->basketItem->count)):?>
            <?= $this->basketItem->count > 1 ? '<div class="count-basket-icon js-count-button"><div>'.$this->basketItem->count.'</div></div>':'<div class="success-basket-icon js-count-button"></div>' ?>
        <?php endif; ?>

        <div class="counts count__com">
            <span
                class="minus count-select-button product-list-plus-minus"
                data-action="minus"
                data-basket="<?= $this->basketItem->id?>"
                data-product="<?= $this->basketItem->product_id?>"
                data-variant="<?= $this->basketItem->variant_id?>"
                data-max="<?= $this->basketItem->variant->maxCount?>"
                data-count-pack="<?= ($this->basketItem->product->count_pack)?>"
                data-current-count="<?= $this->basketItem->count?>"
                data-count-min="<?=$this->basketItem->product->count_min?>"
            ></span>
            <span class="num"><?= $this->basketItem->count?> шт.</span>
            <span
                class="plus <?=($this->basketItem->product_id == 10404594 ? '' : 'count-select-button product-list-plus-minus')?> button-basket-icon"
                data-action="plus"
                data-basket="<?= $this->basketItem->id?>"
                data-product="<?= $this->basketItem->product_id?>"
                data-variant="<?= $this->basketItem->variant_id?>"
                data-max="<?= $this->basketItem->variant->maxCount?>"
                data-count-pack="<?= ($this->basketItem->product->count_pack)?>"
                data-current-count="<?= $this->basketItem->count?>"
            ></span>
        </div><?php

    }
}
