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
class WProductItem extends Widget {
    public $model;
    public $variation;
    public $image;
    public $url;
    public $sticker;
    public $productTypes;

    public function init() {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
    }
    public function run(){
        if(!$this->model || !$this->url){
            return false;
        }else{
            $firstVariant = ($this->variation)?key($this->variation):'';
            ?>
            <div data-product-id="<?= $this->model->id?>" class="item item-<?= $this->model->id?>  product-item js-product-item" >
                <div class="block">
                    <div class="images">
                        <a href="<?= Url::toRoute($this->url)?>">
                            <img class="ad" src="http://www.esalad.ru<?=(is_array($this->image)) ? $this->image[0]:''?>" alt="<?= $this->model->name?>">
                        </a>
                    </div>

                    <div class="title">
                        <a class="black" title="<?= $this->model->name?>" href="<?= Url::toRoute($this->url)?>"><?= $this->model->name?></a>
                    </div>
                    <div class="group"><?= $this->model->category->title?></div>
                    <div class="prices">
                        <span class="price normal variation-price">
                            <?= $this->model->priceVariant;?> <small class="rubznak">p.</small>
                        </span>
                        <span class="price discount variation-discount-price">
                            <?= $this->model->priceVariantDiscount;?> <small class="rubznak">p.</small> *
                        </span>
                    </div>

                    <div class="row-private js-variants-select">
                        <div class="tag-value-list">
                             <div class="options tags-item select__form_multi">
                                    <?php
                                    if(count($this->variation) > 1){
                                        $tagList = [];
                                        foreach($this->variation as $key => $variant){
                                            if(isset($variant['props']) && !empty($variant['props'])){
                                                foreach($variant['props'] as $propertyId => $propertyValue){
                                                    $tagList[$propertyId][key($propertyValue)] = $propertyValue[key($propertyValue)];
                                                    $this->variation[$key]['propertiesInId'][$propertyId] = key($propertyValue);
                                                }
                                            }
                                        }

                                        foreach($tagList as $groupTagId => $groupTagListValue){?>
                                            <div class='tag-value-group-title'></div>

                                            <div data-good-id="<?= $this->model->id?>" data-variation-id="<?= $firstVariant?>">
                                                <div class="block-option" rel="<?= $groupTagId ?>">
                                                    <div class="select__form items" data-group_id="<?= $groupTagId ?>">
                                                        <div class="container-select" rel="1">
                                                            <div class="option-text tag-value-group-title" data-text-select="Выберите <?= $this->productTypes[$groupTagId]->name ?>">Выберите <?= $this->productTypes[$groupTagId]->name ?></div>
                                                            <div class="selectbox"></div>
                                                        </div>
                                                        <div class="row tag-value-group-items" data-tag-group-id='<?= $groupTagId ?>'>
                                                        <?php
                                                        foreach($groupTagListValue as $tagId => $tagValue){
                                                            $activeTagFlag = '';
                                                            if(isset($this->variation[$firstVariant]['props'][$groupTagId][$tagId])){
                                                                $activeTagFlag = ' selected';
                                                            }
                                                            ?>
                                                            <div
                                                                class="option goodVariant tag-value-group-item<?= $activeTagFlag ?>"
                                                                data-tag-id="<?= $tagId?>"
                                                                data-product-id='<?= $this->model->id ?>'
                                                            ><?= $tagValue ?></div>
                                                            <?php
                                                        }?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                ?>
                             </div>
                            <div class="clear"></div>
                            <div class="product-control-buttons">
                                    <?php
                                    $jsonList = [];
                                    if(isset($this->variation) && !empty($this->variation)){
                                        foreach($this->variation as $variantKey => $variantItem){
                                            $position = false;//array_search($variantKey,\Yii::$app->session['shop']['basket']['variantsShort']);
                                            $activeClass = ($variantKey == $firstVariant)?'active':'';

                                            $dataJson = [];
                                            if(isset($variantItem['propertiesInId']) && !empty($variantItem['propertiesInId'])){
                                                foreach($variantItem['propertiesInId'] as $key => $propertyId){
                                                    $dataJson[$key] = $propertyId;
                                                }
                                            }
                                            if(!isset($jsonList[json_encode($dataJson)])){
                                                $jsonList[json_encode($dataJson)] = 1;
                                                ?>
                                            <div class="control-buttons-for-variant js-control-buttons-for-variant <?=$activeClass?>"
                                                data-variant="<?=$variantKey?>"
                                                data-first="<?=$firstVariant?>"
                                                <?=(empty($dataJson)?'':"data-json='" . json_encode($dataJson)) . "'"?> >

                                                <?php
                                                if ($position !== false) {
                                                    $currentCount = \Yii::$app->session['shop']['basket']['countInBasket'][\Yii::$app->session['shop']['basket']['basketItems'][$position]];
                                                    ?>
                                                   <div class="counts count__com">
                                                    <span
                                                        class="minus count-select-button product-list-plus-minus"
                                                        data-action="minus"
                                                        data-basket="<?= \Yii::$app->session['shop']['basket']['basketItems'][$position]?>"
                                                        data-product="<?= $this->model->id?>"
                                                        data-variant="<?= $this->variation?key($this->variation):$this->model->variantId?>"
                                                        data-max="<?= $this->variation[$firstVariant]['productCount']?>"
                                                        data-count-pack="<?= $this->model->count_pack?>"
                                                        data-current-count="<?= $currentCount?>"
                                                        data-url="<?= $this->url?>">
                                                    </span>
                                                    <span class="num"><?=$currentCount?> шт.</span>
                                                    <span
                                                        class="plus count-select-button product-list-plus-minus"
                                                        data-action="plus"
                                                        data-basket="<?= \Yii::$app->session['shop']['basket']['basketItems'][$position]?>"
                                                        data-product="<?= $this->model->id?>"
                                                        data-variant="<?= $this->variation?key($this->variation):$this->model->variantId?>"
                                                        data-max="<?=$this->variation[$firstVariant]['productCount']?>"
                                                        data-count-pack="<?= $this->model->count_pack?>"
                                                        data-current-count="<?= $currentCount?>"
                                                        data-url="<?= $this->url?>"
                                                    ></span></div>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <div
                                                        class="button_oran center basket_button"
                                                        data-action="bay"
                                                        data-basket=""
                                                        data-product="<?=$this->model->id?>"
                                                        data-variant="<?=$this->variation?key($this->variation):$this->model->variantId?>"
                                                        data-count="<?= $this->model->count_pack?>"
                                                        data-url="<?= $this->url?>"
                                                        data-max="<?=$this->variation[$firstVariant]['productCount']?>"
                                                    ><div>Купить</div></div>
                                                    <?php
                                                }
                                                ?></div>
                                            <?php
                                            }
                                        }
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="stickers stickers__com">
                        <?= isset($this->sticker['discount'])?'<div class="stikers-icon discount"></div>':''?>
                        <?= isset($this->sticker['bonus'])?'<div class="stikers-icon bonus"></div>':''?>
                        <?= isset($this->sticker['news'])?'<div class="stikers-icon news"></div>':''?>
                        <?= isset($this->sticker['hit'])?'<div class="stikers-icon hit"></div>':''?>
                    </div>
                </div>
            </div>

<?php
        }
    }

}

