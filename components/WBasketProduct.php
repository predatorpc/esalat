<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use app\modules\catalog\models\Category;
use app\modules\common\models\ModFunctions;

class WBasketProduct extends Widget
{
    public $product;
    public $basketItem;
    public $catalogHash;
    public $catalogItem;
    public $variationsAllProductsList;

    public function init()
    {
        parent::init();
        if ($this->product === null) {
            $this->product = false;
        }
        if ($this->basketItem === null) {
            $this->basketItem = false;
        }
        if($this->catalogHash === null){
            $this->catalogHash = false;
        }
    }

    public function run(){
        if(!$this->product){
            return false;
        }else{
            if(!$this->basketItem){
                $basketItem = Yii::$app->controller->basket['products'][$this->product];
            }else{
                $basketItem = $this->basketItem;
            }
            if(!$this->catalogHash){
                $catalogHash = \Yii::$app->controller->catalogHash;
            }else{
                $catalogHash = $this->catalogHash;
            }

            ?>
            <?php

            //print_arr($basketItem);
            ?>
            <div class="item js-basket-item" id="goods-<?= $basketItem['variantId']?>" data-basket-item="<?= $basketItem['variantId']?>">
                <a class="close delete no-border" aria-hidden="true" data-variation-id="" item="" href="#" onclick="return false;">&times;</a>
                <div class="images">
                    <a href="/catalog/{$good.id}" class="no-border" target="_blank"><?= (isset($basketItem['image'])?'<img src="http://www.esalad.ru/'.$basketItem['image'].'_min.jpg" alt="'.$basketItem['productName'].'" class="ad" />':'<img src="/images/good_min.png" alt="" class="ad"/>')?></a>
                </div>
                <div class="container-goods">
                    <div class="block-1">
                        <div class="name"><a href="/catalog/" class="variation-name black" target="_blank">
                                <?= Html::a(
                                    $basketItem['productName'],
                                    Category::getCategoryPath($basketItem['categoryId'], $catalogHash) . $basketItem['productId'],
                                    [
                                        'title' => $basketItem['productName'],
                                    ]
                                )?>
                            </a>
                        </div>

                        <!--Инфо тeг НУЖНО ДОПИЛИТЬ-->
                        <div class="tags tags__info">
                            <div class="tags-items select"> <div class="tags-item variation-tags"><?= $basketItem['variantName'][$basketItem['variantId']] ?></div> </div>
                            <?php  if(count($basketItem['variants']) > 1): ?>
                                <!--Выбор вариация-->
                                <div id="" class="mod__variations-box variations-select"  >
                                    <div class="arrow"></div>
                                    <div class="close" aria-hidden="true">&times;</div>
                                    <?php foreach($basketItem['tagGroupWithValue'] as $groupTagId => $groupTagListValue):?>
                                        <!--Бох вариация -->
                                        <div class="item-box">
                                            <div class="group_name"><?= $basketItem['tagGroupName'][$groupTagId] ?></div>
                                            <div class="container-variation tag-value-group-items" data-tag-group-id='<?= $groupTagId?>'>
                                                <?php foreach($groupTagListValue as $tagId => $tagValue):?>
                                                    <?php
                                                    $activeTagFlag = '';
                                                    if(isset($basketItem['variants'][$basketItem['variantId']][$tagId])){
                                                        $activeTagFlag = ' open';
                                                    }
                                                    ?>
                                                    <!--active-->
                                                    <div class="i open  disabled hidden">Протеин</div>
                                                    <div
                                                        class='i tag-value-group-item<?= $activeTagFlag ?>'
                                                        data-tag-id='<?= $tagId ?>'
                                                        data-variant-id='<?= $basketItem['variantId'] ?>'
                                                        data-product-id='<?= $basketItem['productId'] ?>'
                                                        data-basket-item-id='<?= $this->product ?>'
                                                        ><?= $tagValue ?>
                                                    </div>
                                                <?php  endforeach;?>
                                            </div>
                                        </div><!--.Бох вариация -->
                                    <?php endforeach; ?>
                                    <div class="clear"></div>
                                    <div class="error hidden_r"></div>
                                    <div class="button_oran hidden" onclick="return basket('{$good.id}', $('#variations-{$good.id}-{$good.variation.id}').attr('data-variation-id'), '1'); "><div>Добавить</div></div>
                                </div> <!--./Выбор вариация-->
                            <?php endif;?>
                        </div><!--.Инфо тeг-->

                        <div class="text-min hidden">Доставка этого товара осуществляется в срок от 5 до 14 дней.<br />
                            Точная дата доставки будет известна после обработки заказа.
                        </div>
                    </div>
                    <!--Цены-->
                    <div class="block-2">
                        <?php if($basketItem['priceClear'][$basketItem['variantId']] != $basketItem['price'][$basketItem['variantId']]):?>
                            <div class="price"><b class="hidden_r">Сумма:</b> <span class="price deleted variation-price"><?= ModFunctions::money($basketItem['price'][$basketItem['variantId']])?></span><span class="price discount variation-discount-price"><?= ModFunctions::money($basketItem['price'][$basketItem['variantId']])?></span></div>
                        <?php else:?>
                            <div class="price"><b class="hidden_r">Сумма:</b> <span class="price variation-price"><?= ModFunctions::money($basketItem['price'][$basketItem['variantId']])?></span></div>
                        <?php endif;?>
                        <span class="s">&times</span>
                        <!-- Количество-->
                        <div class="product-control-buttons count count__com ">
                            <?php
                            $jsonList = [];

                            if(isset($this->variationsAllProductsList[$this->catalogItem->productId]) && !empty($this->variationsAllProductsList[$this->catalogItem->productId])){
                                foreach($this->variationsAllProductsList[$this->catalogItem->productId] as $variantKey => $variantItem){
                                    $position = array_search($variantKey,\Yii::$app->session['shop']['basket']['variantsShort']);
                                    $activClass = '';

                                    $dataJson = [];
                                    if(isset($variantItem['props']) && !empty($variantItem['props'])){
                                        foreach($variantItem['props'] as $key => $propertyId){
                                            $dataJson[$key] = key($propertyId);
                                        }
                                    }
                                    //Zloradnij::print_arr($dataJson);
                                    if(!isset($jsonList[json_encode($dataJson)])){
                                        $jsonList[json_encode($dataJson)] = 1;
                                        ?><div
                                        class="control-buttons-for-variant js-control-buttons-for-variant <?=$activClass?>"
                                        data-variant="<?=$variantKey?>"
                                        <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?>
                                        >
                                        <?php

                                        if ($basketItem['variantId'] == $variantKey) {
                                            $currentCount = \Yii::$app->session['shop']['basket']['countInBasket'][\Yii::$app->session['shop']['basket']['basketItems'][$position]];
                                            ?>
                                            <div
                                                class="count-select-button product-list-plus-minus minus"
                                                data-action="minus"
                                                data-basket="<?= \Yii::$app->session['shop']['basket']['basketItems'][$position]?>"
                                                data-product="<?= $this->catalogItem->productId?>"
                                                data-variant="<?=$this->variationsAllProductsList?key($this->variationsAllProductsList):$this->catalogItem->variantId?>"
                                                data-count-pack="<?= $this->catalogItem->countPack?>"
                                                data-current-count="<?= $currentCount?>"
                                                data-max="<?=$variantItem['productCount']?>"
                                                ></div>
                                            <span class="num"><?=$currentCount?> шт.</span>
                                            <div
                                                class="count-select-button product-list-plus-minus plus"
                                                data-action="plus"
                                                data-basket="<?= \Yii::$app->session['shop']['basket']['basketItems'][$position]?>"
                                                data-product="<?= $this->catalogItem->productId?>"
                                                data-variant="<?=$this->variationsAllProductsList?key($this->variationsAllProductsList):$this->catalogItem->variantId?>"
                                                data-count-pack="<?= $this->catalogItem->countPack?>"
                                                data-current-count="<?= $currentCount?>"
                                                data-max="<?=$variantItem['productCount']?>"
                                                ></div>
                                        <?php
                                        }else{
                                            ?>
                                            <input
                                                data-action="bay"
                                                value="Купить"
                                                type="hidden"

                                                data-basket=""
                                                data-product="<?=$this->catalogItem->productId?>"
                                                data-variant="<?=$this->variationsAllProductsList?key($this->variationsAllProductsList):$this->catalogItem->variantId?>"
                                                data-count="<?= $this->catalogItem->countPack?>"
                                                data-max="<?=$variantItem['productCount']?>"
                                                />
                                        <?php
                                        }
                                        ?></div><?php
                                    }
                                }
                            }
                            ?>

                        </div> <!-- /Количество-->
                        <span class="s">=</span>
                        <div class="money hidden"><b class="hidden_r">Итого:</b> <span class="money deleted variation-money">{$good.money|money}</span><span class="money discount variation-discount-money">{$good.discount_money|money}</span></div>
                        <div class="money"><b class="hidden_r">Итого:</b> <span class="money variation-money"><?= ($basketItem['price'][$basketItem['variantId']] * \Yii::$app->session['shop']['basket']['countInBasket'][$this->product])?> р.</span></div>

                    </div><!--.Цены-->
                </div>
                <div class="clear"></div>
            </div>

        <?php
        }
    }
}

