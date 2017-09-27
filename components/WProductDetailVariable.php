<?php
namespace app\components;

use app\modules\basket\models\Basket;
use app\modules\common\models\Zloradnij;
use yii\base\Widget;
use app\modules\common\models\ModFunctions;
use app\modules\catalog\models\Goods;

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
class WProductDetailVariable extends Widget {
    public $model;

    public function init() {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
    }
    public function run(){
        if(!$this->model){
            return false;
        }else{?>
            <!--col-md-8 col-xs-8-->
            <div class="col-md-8 col-xs-8 content-good js-product-item product-item" data-product-id="<?= $this->model->id?>" data-view-type="detail">
                <h1 class="name" itemprop="name"><?=$this->model->name?></h1>
                <?php

                $allVariations = $this->model->variationsCatalog;
                foreach ($allVariations as $variation) {
                    if(!$variation->propertiesFrontVisible){

                    }else{
                        foreach ($variation->propertiesFrontVisible as $item) {
                            $propertyList[$variation->id][$item->group_id][$item->id] = $item->value;
                        }
                    }
                }

                $firstVariant = (!empty($allVariations[0])) ? $allVariations[0] : false;

                if(count($allVariations) > 1) {
                    $propertySorted = $this->model->propertyIndexed;
                    $activeProperties = $allVariations[0]->propertiesIndexed;
                    ?>
                    <!--Выбор вариация-->
                    <?php
                    foreach ($allVariations[0]->propertyGroups as $groupTagListValue) {
                        foreach ($propertySorted as $tagId => $tags) {
                            if ($tagId == $groupTagListValue->id) {?>
                                <div class='tag-value-group-title'></div>

                            <div class="select-content" data-good-id="<?= $this->model->id?>" data-variation-id="<?= $firstVariant->id?>">
                                <div class="block-option" rel="<?= $groupTagListValue->id ?>">
                                    <div class="select__form min items" data-group_id="<?= $groupTagListValue->id ?>">
                                        <div class="container-select" rel="1">
                                            <div class="option-text tag-value-group-title" data-text-select="Выберите <?= $groupTagListValue->name?>"><?= $activeProperties[key($propertyList[$firstVariant->id][$groupTagListValue->id])]->value?><!--Выберите <?= $groupTagListValue->name?>--></div>
                                            <div class="selectbox"></div>
                                        </div>
                                        <div class="row tag-value-group-items" data-tag-group-id='<?= $groupTagListValue->id ?>'><?php
                                            foreach ($tags as $tag) {
                                                $activeTagFlag = '';
                                                if (!empty($activeProperties[$tag->id])) {
                                                    $activeTagFlag = ' active';
                                                }?>
                                                <div
                                                class="option goodVariant tag-value-group-item<?= $activeTagFlag ?>"
                                                data-tag-id="<?= $tag->id?>"
                                                data-product-id='<?= $this->model->id ?>'
                                                ><?= $tag->value ?></div><?php
                                            }?>
                                        </div>
                                    </div>
                                </div>
                                </div><?php
                            }
                        }
                    }?>
                    <div class="clear"></div><?php
                } else{
                    if(!empty($allVariations[0]->propertyGroups)) {
                        echo '<div class="_tagName-block">';
                        foreach ($allVariations[0]->propertyGroups as $key => $tag) {

                            foreach ($this->model->propertyIndexed as $tagId => $tags) {
                                if ($tagId == $tag->id) {
                                    foreach ($tags as $tagValue) {
                                        echo '<p class="_tagName"><b>' . $tag->name . ':</b> ' . $tagValue->value . '</p>';
                                    }

                                }
                            }
                        }
                        echo '</div>';
                    }
                }
                if(isset($allVariations) && !empty($allVariations)) {
                    foreach ($allVariations as $variantKey => $variantItem) {
                        $activeClass = ($variantItem->id == $firstVariant->id) ? 'active' : '';
                        if(!empty($variantItem->getDateOfAvailible($variantItem->store->id))) {
                            $days = $variantItem->getDateOfAvailible($variantItem->store->id);
                            if($days == 1){
                                $textDay = 'завтра';
                            }elseif ($days == 2){
                                $textDay = 'послезавтра';
                            }else{
                                $textDay = date('d.m.Y',(strtotime('midnight')+24*60*60*$days));
                            }
                            if($this->model->type_id == 1014){
                                $textDay = 'сегодня';
                            }
                            //echo '<div class="delivery '.$activeClass.' ">Ближайшая доставка через <b>' . ModFunctions::NumToStr($variantItem->getDateOfAvailible($variantItem->store->id), "день", "дня", "дней") . '</b></div>';
                            echo '<div class="delivery '.$activeClass.' ">Ближайшая доставка  <b>' . $textDay . '</b></div>';
                        }else{
                            $textDay = '';
                            if(date('N')==5){
                                $textDay = 'послезавтра';
                            }else{
                                $textDay = 'завтра';
                            }
                            if($this->model->type_id == 1014){
                                $textDay = 'сегодня';
                            }
                            echo '<div class="delivery '.$activeClass.' ">Ближайшая доставка  <b>' . $textDay . '</b></div>';
                        }
                        $position = false;
                        if(\Yii::$app->basket->findProduct($variantItem->id)){
                            $position = \Yii::$app->basket->getBasketProduct($variantItem->id);
                        }



                        $dataJson = [];
                        if (!empty($propertyList[$variantItem->id])) {
                            foreach ($propertyList[$variantItem->id] as $key => $propertyId) {
                                $dataJson[$key] = key($propertyId);
                            }
                            ksort($dataJson);
                        }
                        if (!isset($jsonList[json_encode($dataJson)])) {
                            $jsonList[json_encode($dataJson)] = 1;
                            $variantItem->setPriceValue();?>

                            <div
                                class="row-container  <?=$activeClass?>"
                                data-variant="<?=$variantItem->id?>"
                                data-first="<?=$firstVariant->id?>"
                                <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                            >
                                <div class="prices block" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                    <meta itemprop="priceCurrency" content="RUB" />
                                  <?php if((\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('GodMode'))): ?>
                                    <!--Редактирования цены-->
                                    <div class="action-edit-good">
                                        <div class="_icon glyphicon-edit text-warning" onclick="$('.js-content-edit').show();$('._icon').hide()"></div>
                                        <div class="content-goods js-content-edit">
                                            <div class="close" onclick="$('.js-content-edit').hide();$('._icon').show()">&times;</div>
                                            <div class="name-min">Входная цена:</div>
                                            <input class="_price form-control input-sm" type="tel" value="<?= $variantItem->price?>" />
                                            <button class="btn btn-primary btn-sm js-save-price" data-variation-id="<?=$variantItem->id?>" type="button">Сохранить</button>
                                            <div class="clear"></div>
                                            <div class="checkbox"><label><input class="checkbox" type="checkbox">Лента</label></div>
                                        </div>
                                    </div><!--/Редактирования цены-->
                                  <?php endif; ?>
                                    <span class="price normal variation-price " itemprop="price"><?= ModFunctions::money($variantItem->priceValue) ?> </span>
                                    <?php
                                    if($this->model->discount == 1 && !$this->model->discount){?>
                                        <span class="price discount variation-discount-price "> <?= ModFunctions::money(floor($variantItem->priceValue*0.95)); ?><span class="glyphicon glyphicon-question-sign" onclick="return $('.compact .content-good .description-normal').toggle()"></span> </span><?php
                                    }?>

                                </div>
                                <div class="block block-js">
                                    <div rel="345" class="control-buttons-for-variant js-control-buttons-for-variant <?=$activeClass?>"
                                         data-variant="<?=$variantItem->id?>"
                                         data-first="<?=$firstVariant->id?>"
                                        <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                                    >
                                        <?php
                                        if ($position !== false) {?>
                                            <div class="counts count__com">
                                            <span
                                                class="minus count-select-button product-list-plus-minus"
                                                data-action="minus"
                                                data-basket="<?= $position->basketProductId?>"
                                                data-product="<?= $this->model->id?>"
                                                data-variant="<?= $variantItem->id?>"
                                                data-max="<?= $variantItem->maxCount?>"
                                                data-count-pack="<?= ($this->model->count_pack * $this->model->count_min)?>"
                                                data-current-count="<?= $position->count?>"
                                            ></span>
                                            <span class="num"><?= $position->count?> шт.</span>
                                                    <span
                                                        class="plus count-select-button product-list-plus-minus"
                                                        data-action="plus"
                                                        data-basket="<?= $position->basketProductId?>"
                                                        data-product="<?= $this->model->id?>"
                                                        data-variant="<?= $variantItem->id?>"
                                                        data-max="<?= $variantItem->maxCount?>"
                                                        data-count-pack="<?= ($this->model->count_pack * $this->model->count_min)?>"
                                                        data-current-count="<?= $position->count?>"
                                                    ></span>
                                            </div><?php
                                        }else{?>
                                        <div
                                            class="button_oran center basket_button"
                                            data-action="bay"
                                            data-basket=""
                                            data-product="<?= $this->model->id?>"
                                            data-variant="<?= $variantItem->id?>"
                                            data-count="<?= ($this->model->count_pack * $this->model->count_min)?>"
                                            data-max="<?= $variantItem->maxCount?>"
                                            data-min="<?= $this->model->count_min?>"
                                        >
                                            <div><?=\Yii::t('app','В корзину');?> </div>
                                            <?= $this->model->count_pack > 1 ? '<div class="count-min hidden">Минимальное количество для заказа: '.$this->model->count_pack.' шт.</div>' : ''?>

                                            </div><?php
                                        }?>
                                    </div><?php
                                    ?>
                                    <div class="no-variations hidden_r">Нет в наличии</div>
                                </div>
                                <div class="clear"></div>
                            </div><!--/Цены и кнопка добавить-->
                            <?php
                        }
                    }
                }
                ?>

                <?= $this->model->count_min > 1 ? '<div class="count-min ">Минимальное количество для заказа: '.$this->model->count_min.' шт.</div>' : ''?>

                <?php if($this->model->count_pack > 1) echo '<div class="count-min">Количество в упаковке: '.$this->model->count_pack.' шт.</div>' ?>
                <?php if($this->model->discount == 1 && !$this->model->discount): ?>
                <div class="description-normal">
                    <p>* Цена с промо-кодом. Промо-код спрашивайте у администраторов спортзала</p>
                </div>
                <?php endif; ?>
            </div> <!--/Контент товара--><?php
        }
    }
}
?>
