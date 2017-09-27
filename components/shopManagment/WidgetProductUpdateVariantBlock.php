<?php

namespace app\components\shopManagment;

use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsVariations;
use app\modules\managment\models\Shops;
use app\modules\catalog\models\TagsGroups;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UserShop;

class WidgetProductUpdateVariantBlock extends Widget{
    public $variant;
    public $uniqueHashParent;
    public $productStores;
    public $productCountInStores;
    public $tagList;
    public $tagsListValue;
    public $form;

    public function init(){
        parent::init();
        if($this->uniqueHashParent === null){
            return false;
        }
        if($this->variant === null){
            $this->variant = new GoodsVariations();
        }
    }

    public function run(){
        $variantTitle = $this->variant->id
            . ($this->variant->full_name ? ' - ' . $this->variant->full_name:'')
            . ' - ' . $this->variant->tags_name
            . ($this->variant->status == 1 ? ' <span class="alert-success">Включен</span>':' <span class="alert-warning">Выключен</span>');
        ?>
        <div class="variants" data-key="<?=$this->uniqueHashParent?>">
            <div class="variant-title">
                <?= $variantTitle?>
            </div>
            <div class="variant-body close">
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]uniqueHashParentString")->hiddenInput()->label(''); ?>
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]uniqueHashString")->hiddenInput()->label(''); ?>
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]id")->hiddenInput()->label('') ?>
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]full_name")->textInput(['maxlength' => true]) ?>
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]code")->textInput(['maxlength' => true]) ?>
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]price")->textInput(['maxlength' => true,'class' => 'form-control price_in'])->label('Цена входная *') ?>
                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]comission")->textInput(['maxlength' => true,'class' => 'form-control comission money'])->label('Комиссия *') ?>

                <div class="form-group has-success">
                    <label class="control-label" for="goodsvariations-0-price">Цена выходная</label>
                    <input id="goodsvariations-0-price-output" class="form-control price_out" type="text" value="<?=round($this->variant->price*(1+$this->variant->comission/100),2)?>" name="">
                    <div class="help-block"></div>
                </div>

                <div class="storesBlock">
                    <?php
                    if(!$this->productStores){

                    }else{
                        foreach($this->productStores as $storeItem){?>
                            <?= Html::hiddenInput("ShopsStores[".$storeItem->uniqueHashString."][id]", $storeItem->id) ?>
                            <?= Html::hiddenInput("ShopsStores[".$storeItem->uniqueHashString."][shop_id]", $storeItem->shop_id) ?>
                            <?= Html::hiddenInput("ShopsStores[".$storeItem->uniqueHashString."][uniqueHashString]", $storeItem->uniqueHashString) ?>

                            <?php
                            $zeroProductCount = new GoodsCounts();
                            $zeroProductCount->uniqueHashParentString = $this->variant->uniqueHashString;
                            $zeroProductCount->count = isset($productCountInStores[$this->variant->id][$storeItem->id]) ? $productCountInStores[$this->variant->id][$storeItem->id] : 0;?>
                            <?= Html::hiddenInput("GoodsCounts[".$this->variant->uniqueHashString."][$storeItem->uniqueHashString][uniqueHashParentString]", $storeItem->uniqueHashString) ?>
                            <?= Html::hiddenInput("GoodsCounts[".$this->variant->uniqueHashString."][$storeItem->uniqueHashString][uniqueHashString]", $zeroProductCount->uniqueHashString) ?>
                            <?= $this->form
                                ->field($zeroProductCount, "[".$this->variant->uniqueHashString."][$storeItem->uniqueHashString]count")
                                ->textInput(['maxlength' => true])
                                ->label($storeItem->getShopNameStringTitle() .'<br />'. $storeItem->getAddressStringTitle().'<br />'.$storeItem->address);?>
                            <?php
                        }
                    }?>
                </div>

                <!-- add tags -->
                <div class="variation" data-unique-hash="<?= $this->variant->uniqueHashString?>">
                    <?php
                    foreach($this->tagList as $k => $tagItem){
                        $oldTagsVariantsString = '';
                        if(isset($this->tagsListValue[$this->variant->id][$tagItem->id])){
                            $oldTagsVariantsString = '<span class="tag">';
                            foreach($this->tagsListValue[$this->variant->id][$tagItem->id] as $idTag => $valTag){
                                $oldTagsVariantsString .=
                                    Html::hiddenInput("Tags[".$this->variant->uniqueHashString."][$valTag->id]", $valTag->id)
                                    .$valTag->value.'
                            <a onclick="$(this).parent().remove(); return false;" title="Удалить тег" href="/">X</a>';
                            }
                            $oldTagsVariantsString .= '</span>';
                        }
                        print '
                    <div class="form-group field-tagsgroups-name i options">
                        <label class="control-label" for="tagsgroups-name">'.$tagItem->name.'</label>
                        <div class="value value-'.$tagItem->id.'">
                            <input type="text" name="" value="" maxlength="64" group="'.$tagItem->id.'" class="string form-control">
                            <div class="load"></div>
                            <div class="values"></div>
                            '.$oldTagsVariantsString.'
                        </div>
                    </div>';
                    }
                    ?>
                </div>
                <div class="images row general-image-variant-block" data-variant="<?=$this->variant->id?>">
                    <?php
//                    $images[$this->variant->id][] = new GoodsImages();
//                    foreach($images[$this->variant->id] as $image){
//                        if(isset($image->id) && !empty($image->id)){?>
<!--                            <div style="display:inline-block;position: relative;">-->
<!--                            <img src="/files/goods/--><?//= GoodsImages::getImageFolder($image->id).'/'.$image->id?><!--.jpg" style="width:150px;" />-->
<!--                        <span-->
<!--                            class="delete-image-block-new"-->
<!--                            style="position: absolute;display: block;top:0;right:0;cursor: pointer;padding:5px 10px;"-->
<!--                            data-product="--><?//= $model->id?><!--"-->
<!--                            data-variant="--><?//= $this->variant->id?><!--"-->
<!--                            data-image="--><?//= $image->id?><!--"-->
<!--                        >X</span>-->
<!--                            </div>--><?php
//                        }
//                    }
//                    $j = 0;
//                    print $this->form->field($image, "[".$variant->id."][$j]id")->fileInput()->hint('Загрузить фото товара')->label('');
                    ?>
                </div>
                <!-- END >> add tags -->

                <?= $this->form->field($this->variant, "[".$this->variant->uniqueHashString."]status")->checkbox(['value' => 1,'label' => 'Активность']) ?>

            </div>
        </div>
        <hr />
        <?php
    }
}

/*
class WVariantForm extends CWidget {

    public function init() {
        return 'qwe';
    }

}*/