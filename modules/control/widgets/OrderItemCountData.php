<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class OrderItemCountData extends Widget{
    public $orderItem;

    public function run(){
        if(empty($this->orderItem)){
            return false;
        }?>
        <!-- Количество-->
        <div class="product-control-buttons count count__com "><?php

            $jsonList = [];
            foreach($this->orderItem->good->variationsCatalog as $variant){
                $dataJson = [];
                if(!empty($propertyList[$variant->id])){
                    foreach($propertyList[$variant->id] as $key => $propertyId){
                        $dataJson[$key] = key($propertyId);
                    }
                }?>
                <div
                    class="control-buttons-for-variant js-control-buttons-for-variant"
                    data-variant="<?= $variant->id ?>"
                    <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                ><?php
                if(!isset($jsonList[json_encode($dataJson)])) {
                    $jsonList[json_encode($dataJson)] = 1;

                    if ($this->orderItem->variation_id == $variant->id) { ?>
                        <div
                            class="count-select-button product-list-plus-minus minus"
                            data-action="minus"
                            data-basket=""
                            data-product="<?= $this->orderItem->good_id ?>"
                            data-variant="<?= $this->orderItem->variation_id ?>"
                            data-count-pack="<?= $this->orderItem->good->count_pack ?>"
                            data-current-count="<?= $this->orderItem->count ?>"
                            data-max="<?= $this->orderItem->goodsVariations->maxCount ?>"
                        ></div>
                        <span class="num"><?= $this->orderItem->count ?> шт.</span>
                        <div
                            class="count-select-button product-list-plus-minus plus"
                            data-action="plus"
                            data-basket=""
                            data-product="<?= $this->orderItem->good_id ?>"
                            data-variant="<?= $this->orderItem->variation_id ?>"
                            data-count-pack="<?= $this->orderItem->good->count_pack ?>"
                            data-current-count="<?= $this->orderItem->count ?>"
                            data-max="<?= $this->orderItem->goodsVariations->maxCount ?>"
                        ></div><?php
                    } else {?>
                        <input
                            data-action="bay"
                            value="Купить"
                            type="hidden"

                            data-basket=""
                            data-product="<?= $this->orderItem->good_id ?>"
                            data-variant="<?= $this->orderItem->variation_id ?>"
                            data-count-pack="<?= $this->orderItem->good->count_pack ?>"
                            data-max="<?= $this->orderItem->goodsVariations->maxCount ?>"
                        /><?php
                    }
                }?>
                </div><?php
            }?>
        </div> <!-- /Количество-->

        <?php
    }
}
