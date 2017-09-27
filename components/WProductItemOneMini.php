<?php
namespace app\components;

use app\modules\common\models\Zloradnij;
use yii\base\Widget;
use yii\helpers\Url;
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
class WProductItemOneMini extends Widget {
    public $model;
    public $user;

    public function init() {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
    }
    public function run(){
        if(!$this->model){
            return false;
        }else{
            $allVariations = $this->model->variationsCatalog;
            $firstVariant = (!empty($allVariations[0])) ? $allVariations[0] : false;
            $propertyList = [];
            if(!empty($allVariations[0])){
                foreach ($allVariations as $i => $variation) {
                    if($variation->maxCount == 0){
                        $variation->status = 0;
                        $variation->save();
                        unset($allVariations[$i]);
                    }
                }
            }
            if(!empty($allVariations[0])){

            }else{
                $this->model->status = 0;
                $this->model->save();
                return false;
            }

            foreach ($allVariations as $variation) {
                if(!$variation->propertiesFrontVisible){
                }else{
                    foreach ($variation->propertiesFrontVisible as $item) {
                        $propertyList[$variation->id][$item->group_id][$item->id] = $item->value;
                    }
                }
            }?>
            <?php


            ?>
            <div data-product-id="<?= $this->model->id?>" class="item item-<?= $this->model->id?>  product-item js-product-item" style="width: 130px; height: 150px;">
                <div class="block wizard">
                    <div class="images">
                        <a href="<?= Url::toRoute($this->model->catalogUrl)?>">
                            <img class="ad" style="width: 100px;" src="http://www.esalad.ru<?=Goods::findProductImage($this->model->id,'min')?>" alt="<?= $this->model->name?>">
                        </a>
                        <!-- div class="compact" onclick="return show_modal_compact('<?= Url::toRoute($this->model->catalogUrl) ?>',' ');"><div>Быстрый просмотр</div></div><!--.Быстрый просмотр-->
                    </div>


                    <!-- SHOP`s STORES -->

                    <div class="title">
                        <a class="black" style="font-size: 12px; font-family: verdana;" title="<?= $this->model->name?>" href="<?= Url::toRoute($this->model->catalogUrl)?>"><?= $this->model->name?></a>
                    </div>

                    <div class="group"><?= $this->model->category->title?></div>
                    <!-- div class="prices"><?php
                        $hiddenClass = '';
                        if(!empty($allVariations)){
                            foreach ($allVariations as $allVariation) {?>
                            <div class="price-block-list <?= $hiddenClass?>"  data-variant-id="<?= $allVariation->id?>">
                                <span class="price normal variation-price">
                                        <?= \app\modules\common\models\ModFunctions::money($allVariation->priceValue)?>*
                                    </span>
                                    <span class="price discount variation-discount-price">
                                        <?= \app\modules\common\models\ModFunctions::money(floor($allVariation->priceValue * 0.95))?>*
                                    </span>
                                </div><?php
                                $hiddenClass = 'hidden';
                            }
                        }?>
                    </div -->

                    <div class="row-private js-variants-select">
                        <div class="tag-value-list">
                            <div class="options tags-item select__form_multi"><?php
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

                                            <div data-good-id="<?= $this->model->id?>" data-variation-id="<?= $firstVariant->id?>">
                                                <div class="block-option" rel="<?= $groupTagListValue->id ?>">
                                                    <div class="select__form items" data-group_id="<?= $groupTagListValue->id ?>">
                                                        <div class="container-select" rel="1">
                                                            <div class="option-text tag-value-group-title" data-text-select="Выберите <?= $groupTagListValue->name?>"><?= $activeProperties[key($propertyList[$firstVariant->id][$groupTagListValue->id])]->value?><!--Выберите  <?= $groupTagListValue->name?>--></div>
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
                                    <div class="clear"></div>
                                    <div class="error hidden_r"></div>
                                    <div class="button_oran hidden">
                                        <div>Добавить</div>
                                    </div> <!--./Выбор вариация--><?php
                                }?>
                            </div>
                            <div class="clear"></div>
                            <div class="product-control-buttons">
                                <?php
                                $jsonList = [];
                                if(isset($allVariations) && !empty($allVariations)){
                                    foreach($allVariations as $variantKey => $variantItem){
                                        $position = false;
                                        if(\Yii::$app->basket->findProduct($variantItem->id)){
                                            $position = \Yii::$app->basket->getBasketProduct($variantItem->id);
                                        }

                                        $activeClass = ($variantItem->id == $firstVariant->id)?'active':'';
                                        $dataJson = [];

                                        if(!empty($propertyList[$variantItem->id])){
                                            foreach($propertyList[$variantItem->id] as $key => $propertyId){
                                                if(!empty($key) && !empty($propertyId)){
                                                    $dataJson[$key] = key($propertyId);
                                                }
                                            }
                                        }

                                        if(!empty($dataJson) && !isset($jsonList[json_encode($dataJson)])){
                                            $jsonList[json_encode($dataJson)] = 1;?>

                                        <div class="control-buttons-for-variant js-control-buttons-for-variant <?=$activeClass?>"
                                             data-variant="<?=$variantItem->id?>"
                                             data-first="<?=$firstVariant->id?>"
                                            <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                                        >
                                            <div
                                                class="button_oran center basket_button"
                                                data-action="bay"
                                                data-basket=""
                                                data-product="<?= $this->model->id?>"
                                                data-variant="<?= $variantItem->id?>"
                                                data-count="<?= $this->model->count_pack?>"
                                                data-max="<?= $variantItem->maxCount?>"
                                            >
                                                <div>Купить</div>
                                            </div>


                                            </div><?php
                                        }
                                    }
                                }
                                //                                if(empty($activeClass)){?>
                                <!--                                    <div-->
                                <!--                                        class="button_grey center"-->
                                <!--                                    >-->
                                <!--                                        <div>Купить</div>-->
                                <!--                                    </div>--><?php
                                //                                }
                                ?>

                            </div>
                        </div>
                    </div>

                    <div class="stickers stickers__com">
                        <?= !empty($this->model->discount)?'<div class="stikers-icon discount"></div>':''?>
                        <?= !empty($this->model->bonus)?'<div class="stikers-icon bonus"></div>':''?>
                        <?= !empty($this->model->new)?'<div class="stikers-icon news"></div>':''?>
                        <?= !empty($this->model->hit)?'<div class="stikers-icon hit"></div>':''?>
                    </div>

                    <!-- Управление -->
                    <?php if(!empty(\Yii::$app->user->identity) && (\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('GodMode'))): ?>
                        <div class="manager manager___shop">
                            <div class="items">
                                <div class="i edit" title="Редактировать" onclick="return good_edit('<?=$this->model->id?>');"></div>
                                <div class="i discount<?php if(!empty($this->model->discount)): ?> disabled<?php endif;?>" title="Акция" onclick="return good_discount('<?=$this->model->id?>');"></div>
                                <div class="i delete" title="Скрыть" onclick="return good_delete('<?=$this->model->id?>');"></div>
                                <div class="i position" title="Смена позиций"></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    <?php  endif;?>
                </div>
            </div>
            <?php
        }
    }

}

