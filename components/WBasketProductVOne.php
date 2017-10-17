<?php

namespace app\components;

use app\modules\catalog\models\Goods;
use app\modules\common\models\ModFunctions;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
class WBasketProductVOne extends Widget
{
    public $product;

    public function init()
    {
        parent::init();
        if ($this->product === null) {
            return false;
        }
    }

    public function run(){
        if(empty($this->product->product->variationsCatalog) || !is_array($this->product->product->variationsCatalog)){
            return false;
        }
        $catalogPath = $this->product->product->category->catalogPath . $this->product->product_id;
        ?>

        <div class="item js-basket-item"
            data-basket-item="<?= $this->product->id?>"
            data-variant-id="<?= $this->product->variant_id?>"
            data-product-id="<?= $this->product->product_id?>"
        >
            <div class="content-goods">
                <a class="close delete no-border delete-basket-product"
                    aria-hidden="true"
                    data-product-id="<?= $this->product->product_id?>"
                    data-variation-id="<?= $this->product->variant_id?>"
                    data-basket-item="<?= $this->product->id?>"
                    href="#"
                >&times;</a>
                <div class="images">
                    <a href="/<?= $catalogPath?>" class="no-border" target="_blank">
                        <img src="http://www.esalad.ru<?= Goods::findProductImage($this->product->product_id,'min');?>" alt="<?= $this->product->product->name?>" class="ad" />
                    </a>
                </div>
                <div class="container-goods">
                    <div class="block-1">
                        <div class="name">
                            <?= Html::a(
                                $this->product->product->name,
                                '/'.$catalogPath,
                                [
                                    'title' => $this->product->product->name,
                                    //'target' => '_blank',
                                    'class' => 'variation-name black',
                                ]
                            )?>
                            <div class="subs-string"></div>
                        </div>

                        <!--Инфо тeг-->

                        <div class="tags tags__info">
                            <div class="tags-items select">
                                <div class="tags-item variation-tags " data-t="<?= $this->product->variant->titleWithPropertiesForCatalog?>"><?= $this->product->variant->titleWithPropertiesForCatalog?><br /><?= count($this->product->product->variationsCatalog) > 1 ?  Yii::t('app','Выбор вариаций') : ''?><?php // $this->product->variantName?></div>
                            </div>
                            <?php
                            if(count($this->product->product->variationsCatalog) > 1){?>
                                <!--Выбор вариация-->
                                <div id="" class="mod__variations-box variations-select"  >
                                    <div class="variations-box-content">
                                    <div class="arrow"></div>
                                    <div class="close" aria-hidden="true">&times;</div>
                                    <?php
                                    if(!empty($this->product->variant->propertyGroups)){
                                        foreach($this->product->variant->propertyGroups as $groupTagListValue) {
                                            $groupTagListValue = (object)$groupTagListValue;?>
                                            <!--Бох вариация -->
                                            <div class="item-box">
                                                <div class="group_name"><?= $groupTagListValue->name ?></div>
                                                <div class="container-variation tag-value-group-items"
                                                     data-tag-group-id='<?= $groupTagListValue->id ?>'>
                                                    <?php
                                                    foreach ($this->product->product->propertyIndexed as $tagId => $tags) {
                                                        if($tagId == $groupTagListValue->id){
                                                            foreach ($tags as $tag) {
                                                                $tag = (object)$tag;
                                                                $activeTagFlag = '';
                                                                if(!empty($this->product->variant->propertiesIndexed[$tag->id])){
                                                                    $activeTagFlag = ' open';
                                                                }
                                                                ?>
                                                                <?php

                                                                ?>
                                                                <!--active-->
                                                                <div
                                                                    class='i tag-value-group-item<?= $activeTagFlag ?>'
                                                                    data-tag-id='<?= $tag->id ?>'
                                                                    data-variant-id='<?= $this->product->variant_id ?>'
                                                                    data-product-id='<?= $this->product->product_id ?>'
                                                                    data-basket-item-id='<?= $this->product->id ?>'
                                                                    data-count-item='<?= ($this->product->product->count_pack * $this->product->product->count_min) ?>'
                                                                ><?= $tag->value ?>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                    }?>
                                                </div>
                                            </div><!--.Бох вариация -->
                                            <?php
                                        }
                                    }?>
                                    <div class="clear"></div>

                                    </div>
                                </div> <!--./Выбор вариация-->
                                <?php
                            }?>
                        </div><!--.Инфо тeг-->

                        <?php
                        if($this->product->product->type_id == 1009){?>
                            <div class="text-min">
                                <?=\Yii::t('app','Доставка этого товара осуществляется в срок от 5 до 14 дней')?>.<br />
                                <?=\Yii::t('app','Точная дата доставки будет известна после обработки заказа');?>.
                            </div><?php
                        }?>

                    </div>
                    <!--Цены-->
                    <div class="block-2">
                        <?php if($this->product->price > $this->product->priceDiscount):?>
                            <div class="price">
                                <b class="name"><?=\Yii::t('app','Сумма');?>:</b>
                                <span class="price deleted variation-price hidden"><?= ModFunctions::money($this->product->price)?></span>
                                <?php if(!empty($this->product->bonus)):?> <span class="price variation-price">- <?= $this->product->bonus?> β.</span><?php endif; ?>
                                <span class="price discount variation-discount-price"><?= ModFunctions::money($this->product->priceDiscount)?></span>
                            </div>
                        <?php else:?>
                            <div class="price">
                                <b class="name"><?=\Yii::t('app','Сумма');?>:</b>
                                <span class="price variation-price"><?= ModFunctions::money($this->product->price)?></span>
                            </div>
                        <?php endif;?>

                         <?php
                             if(!empty($this->product->variant->getDateOfAvailible($this->product->variant->store->id))) {

                                 $days = $this->product->variant->getDateOfAvailible($this->product->variant->store->id);
                                 if($days == 1){
                                     $textDay = 'завтра';
                                 }elseif ($days == 2){
                                     $textDay = 'послезавтра';
                                 }else{
                                     $textDay = date('d.m.Y',(strtotime('midnight')+24*60*60*$days));
                                 }
                                 if($this->product->product->type_id == 1014){
                                     $textDay = 'сегодня';
                                 }
                                 echo '<div class="delivery">Ближайшая доставка:  <b>' . $textDay . '</b></div>';
                             }else{
                                 $textDay = '';
                                 /*if(date('N')==5){
                                     $textDay = 'послезавтра';
                                 }
                                 else{
                                     $textDay = 'завтра';
                                 }
                                 if($this->product->product->type_id == 1014){*/
                                     $textDay = 'сегодня';
                                 //}
                                 echo '<div class="delivery">Ближайшая доставка:  <b>' . $textDay . '</b></div>';
                             }
                         ?>
                    <!-- Количество-->
                        <div class="product-control-buttons count count__com">
                            <?php
                            $jsonList = [];
                            foreach($this->product->product->variationsCatalog as $variant){
                                $dataJson = [];
                                if(!empty($this->product->propertyList[$variant->id])){
                                    foreach($this->product->propertyList[$variant->id] as $key => $propertyId){
                                        $dataJson[$key] = key($propertyId);
                                    }
                                }
                                if(!isset($jsonList[json_encode($dataJson)])) {
                                    $jsonList[json_encode($dataJson)] = 1; ?>
                                    <div class="control-buttons-for-variant js-control-buttons-for-variant"
                                        data-variant="<?= $variant->id ?>"
                                        <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                                    >
                                    <?php

                                    if ($this->product->variant_id == $variant->id) { ?>
                                        <div
                                            class="count-select-button product-list-plus-minus minus"
                                            data-action="minus"
                                            data-basket="<?= $this->product->id ?>"
                                            data-product="<?= $this->product->product_id ?>"
                                            data-variant="<?= $this->product->variant_id ?>"
                                            data-count-pack="<?= ($this->product->product->count_pack) ?>"
                                            data-current-count="<?= $this->product->count ?>"
                                            data-max="<?= $this->product->variant->maxCount ?>"
                                            data-count-min= <?=$this->product->product->count_min?>
                                        ></div>
                                        <span class="num"><?= $this->product->count ?> шт.</span>
                                        <div
                                            class=" plus  <?=($this->product->product_id == 10404594 ? '' : 'count-select-button product-list-plus-minus') ?>"
                                            data-action="plus"
                                            data-basket="<?= $this->product->id ?>"
                                            data-product="<?= $this->product->product_id ?>"
                                            data-variant="<?= $this->product->variant_id ?>"
                                            data-count-pack="<?= ($this->product->product->count_pack ) ?>"
                                            data-current-count="<?= $this->product->count ?>"
                                            data-max="<?= $this->product->variant->maxCount ?>"

                                        ></div>
                                        <?php
                                    } else {
                                        ?>
                                        <input
                                            data-action="bay"
                                            value="<?=\Yii::t('app','Купить');?>"
                                            type="hidden"

                                            data-basket=""
                                            data-product="<?= $this->product->product_id ?>"
                                            data-variant="<?= $this->product->variant_id ?>"
                                            data-count-pack="<?= ($this->product->product->count_pack * $this->product->product->count_min) ?>"
                                            data-max="<?= $this->product->variant->maxCount ?>"
                                        />
                                        <?php
                                    } ?>
                                    </div>
                                <?php } ?><?php
                            }
                            ?>
                        </div> <!-- /Количество-->
                        <div class="money hidden">
                            <b class="hidden_r"><?=\Yii::t('app','Итого');?>:</b>
                            <span class="money deleted variation-money">{$good.money|money}</span>
                            <span class="money discount variation-discount-money">{$good.discount_money|money}</span>
                        </div>
                        <div class="money">
                            <b class="hidden_r"><?=\Yii::t('app','Итого');?>:</b>
                            <span class="money variation-money">
                                <?= ModFunctions::money($this->product->priceDiscount * $this->product->count)?>
                            </span>
                        </div>
                    </div><!--.Цены-->
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }
}

