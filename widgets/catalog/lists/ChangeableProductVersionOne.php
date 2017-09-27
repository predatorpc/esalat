<?php

/* @var $product app\modules\catalog\models\Goods */

namespace app\widgets\catalog\lists;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\common\models\ModFunctions;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ChangeableProductVersionOne extends Widget
{
    public $product;
    public $percent;
    public $activeTagsGroups;

    public function init()
    {
        parent::init();

        if ($this->product === null) {
            return false;
        }
        if ($this->activeTagsGroups === null) {
            $this->activeTagsGroups = TagsGroups::find()->where(['status' => 1,'show' => 1,'type' => 1])->indexBy('id')->all();
        }
        if ($this->percent === null) {
            $this->percent = 0;
        }
    }

    public function run(){
        if(empty($this->product->variant->product->variationsCatalog) || !is_array($this->product->variant->product->variationsCatalog)){
            \app\modules\common\models\Zloradnij::print_arr($this->product->variant->product->variationsCatalog);
            return false;
        }?>

        <div

            class="item js-basket-item list-product-item"
            data-variant-id="<?= $this->product->variation_id?>"
            data-product-id="<?= $this->product->good_id?>"
            data-list-id="<?= $this->product->list_id?>"
            data-category-title="<?= $this->product->variant->product->category->parent->title . ' / ' . $this->product->variant->product->category->title?>"
        >
            <span class="close delete no-border delete-list-product" aria-hidden="true" data-variation-id="<?= $this->product->variation_id?>" data-list-id="<?= $this->product->list_id?>" >×</span>
            <div class="content-goods">

            <div class="images">
                <a href="<?= $this->product->variant->product->catalogPath?>" class="no-border" target="_blank">
                    <img class="" src="http://www.esalad.ru<?= Goods::findProductImage($this->product->variant->product->id,'min');?>" alt="<?= $this->product->variant->product->name?> <?= $this->product->variant->titleWithPropertiesForCatalog?>" class="ad" />
                </a>
                <input type="hidden" name="store-list-json" data-product-id="<?= $this->product->good_id?>" value="">
            </div>
            <div class="container-goods">
                <div class="block-1">
                    <div class="name">
                        <?= Html::a(
                            $this->product->variant->product->name,
                            $this->product->variant->product->catalogPath,
                            [
                                'title' => $this->product->variant->product->name,
                                //'target' => '_blank',
                                'class' => 'variation-name black',
                            ]
                        )?>
                        <div class="subs-string"></div>
                    </div>

                    <div class="tags tags__info">
                        <div class="tags-items select">
                            <div class="tags-item variation-tags"><?= $this->product->variant->titleWithPropertiesForCatalog?></div>
                        </div><?php
                        if(count($this->product->variant->product->variationsCatalog) > 1){?>

                            <!--Выбор вариация-->
                            <div id="" class="mod__variations-box variations-select"  >
                                <div class="variations-box-content">
                                    <div class="arrow"></div>
                                    <div class="close" aria-hidden="true">&times;</div><?php

                                    foreach($this->product->variant->propertyGroups as $groupTagListValue) {
                                        $groupTagListValue = (object)$groupTagListValue;?>
                                        <!--Бох вариация -->
                                        <div class="item-box">
                                            <div class="group_name"><?= $groupTagListValue->name ?></div>
                                            <div class="container-variation tag-value-group-items"
                                                 data-tag-group-id='<?= $groupTagListValue->id ?>'
                                            ><?php
                                                foreach ($this->product->variant->product->propertyIndexed as $tagId => $tags) {
                                                    if($tagId == $groupTagListValue->id){
                                                        foreach ($tags as $tag) {
                                                            $activeTagFlag = '';
                                                            if(!empty($this->product->variant->propertiesIndexed[$tag->id])){
                                                                $activeTagFlag = ' open';
                                                            }?>
                                                            <!--active-->
                                                            <div
                                                                class='i tag-value-group-item<?= $activeTagFlag ?>'
                                                                data-tag-id='<?= $tag->id ?>'
                                                                data-variant-id='<?= $this->product->variation_id ?>'
                                                                data-product-id='<?= $this->product->good_id ?>'
                                                            ><?= $tag->value ?>
                                                            </div><?php
                                                        }
                                                    }
                                                }?>
                                            </div>
                                        </div><!--.Бох вариация --><?php
                                    }?>
                                </div>
                                <div class="clear"></div>
                            </div> <!--./Выбор вариация--><?php
                        }?>
                    </div><!--.Инфо тeг-->
                    <div class="change-product-button">Заменить товар</div>
                    <div
                        class="change-product-container-block"
                        data-category="<?= $this->product->variant->product->category->id?>"
                        data-list="<?= $this->product->list_id?>"
                        data-product="<?= $this->product->variation_id?>"
                        data-action="change"
                    >
                        <div class="arrow"></div>
                        <div class="close-change" aria-hidden="true">&times;</div>
                        <img class="preload-image" src="/images/ajax_load.gif">
                    </div>
                </div>
                <!--Цены--><?php
                $jsonList = [];

                foreach($this->product->variant->product->variationsCatalog as $variant){
                    $dataJson = [];

                    if(!empty($variant->propertiesFrontVisible)){
                        foreach($variant->propertiesFrontVisible as $key => $propertyId){
                            if(!empty($this->activeTagsGroups[$propertyId->group_id])){
                                $dataJson[$propertyId->group_id] = $propertyId->id;
                            }
                        }
                    }
                    ksort($dataJson);

                    if(!isset($jsonList[json_encode($dataJson)])) {
                        $jsonList[json_encode($dataJson)] = 1; ?>

                        <div
                            class="block-2 price-count-block <?= ($this->product->variant->id == $variant->id) ? 'open' : ''?>"
                            data-variant="<?= $variant->id ?>" <?= empty($dataJson)?'':"data-json='" . json_encode($dataJson) . "'"?>
                        >
                            <div class="price">
                                <span class="price variation-price"><?= ModFunctions::money($variant->priceValue)?></span>
                            </div>
                            <!-- Количество-->
                            <div class="product-control-buttons count count__com">
                                <div
                                    class="control-buttons-for-variant js-control-buttons-for-variant"
                                    data-variant="<?= $variant->id ?>"
                                    <?= empty($dataJson)?'':"data-json='" . json_encode($dataJson) . "'"?>
                                >
                                    <div
                                        class="count-select-button product-list-plus-minus minus"
                                        data-action="minus"
                                        data-product="<?= $this->product->good_id ?>"
                                        data-variant="<?= $this->product->variation_id ?>"
                                        data-count-pack="<?= $this->product->variant->product->count_pack ?>"
                                        data-current-count="<?= $this->product->amount ?>"
                                        data-max="<?= $this->product->variant->maxCount ?>"
                                    ></div>
                                    <span class="num"><?= $this->product->amount ?> шт.</span>
                                    <div
                                        class="count-select-button product-list-plus-minus plus"
                                        data-action="plus"
                                        data-product="<?= $this->product->good_id ?>"
                                        data-variant="<?= $this->product->variation_id ?>"
                                        data-count-pack="<?= $this->product->variant->product->count_pack ?>"
                                        data-current-count="<?= $this->product->amount ?>"
                                        data-max="<?= $this->product->variant->maxCount ?>"
                                    ></div>
                                </div>
                            </div> <!-- /Количество-->
                            <div class="money">
                                <span class="money variation-money">
                                    <?= ModFunctions::money($variant->priceValue * $this->product->amount)?>
                                </span>
                            </div>
                        </div><!--.Цены--><?php
                    }
                }?>

            </div>
             <div class="clear"></div>
            </div>

        </div>

        <?php
//        Zloradnij::print_arr($propertyList);
    }
}

