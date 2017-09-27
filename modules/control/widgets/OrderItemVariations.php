<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class OrderItemVariations extends Widget{
    public $orderItem;

    public function run(){
        if(empty($this->orderItem)){
            return false;
        }
        $propertySorted = $this->orderItem->good->propertyIndexed;
        $activeProperties = $this->orderItem->goodsVariations->propertiesIndexed;
        ?>

        <div class="tags-items select">
            <div class="tags-item variation-tags"><?= $this->orderItem->goodsVariations->titleWithProperties?></div>
        </div><?php
        if(count($this->orderItem->good->variationsCatalog) > 1){?>
            <!--Выбор вариация-->
            <div id="" class="mod__variations-box variations-select">
                <div class="arrow"></div>
                <div class="close" aria-hidden="true">&times;</div><?php

                foreach($this->orderItem->goodsVariations->propertyGroups as $groupTagListValue) {?>
                    <!--Бох вариация -->
                    <div class="item-box">
                        <div class="group_name"><?= $groupTagListValue->name ?></div>
                        <div class="container-variation tag-value-group-items"
                             data-tag-group-id='<?= $groupTagListValue->id ?>'><?php
                            foreach ($propertySorted as $tagId => $tags) {
                                if($tagId == $groupTagListValue->id){
                                    foreach ($tags as $tag) {
                                        $activeTagFlag = '';
                                        if(!empty($activeProperties[$tag->id])){
                                            $activeTagFlag = ' open';
                                        }?>
                                        <div
                                            class='i tag-value-group-item<?= $activeTagFlag ?>'
                                            data-tag-id='<?= $tag->id ?>'
                                            data-variant-id='<?= $this->orderItem->variation_id ?>'
                                            data-product-id='<?= $this->orderItem->good_id ?>'
                                            data-basket-item-id='<?= $this->orderItem->good_id ?>'
                                        >
                                            <?= $tag->value ?>
                                        </div><?php
                                    }
                                }
                            }?>
                        </div>
                    </div><!--.Бох вариация -->
                    <?php
                }?>
                <div class="clear"></div>

            </div> <!--./Выбор вариация--><?php
        }
    }
}
