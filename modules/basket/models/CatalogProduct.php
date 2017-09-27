<?php

namespace app\modules\basket\models;

use app\modules\catalog\models\GoodsVariations;
use app\modules\common\models\Zloradnij;

class CatalogProduct
{
    public $basketProductId = false;
    public $basketId = false;
    public $product_id;
    public $variant_id;
    public $comissionId;
    public $comissionValue;
    public $price;
    public $priceIn;
    public $priceBonus = 0;
    public $priceDiscount;
    public $count;
    public $count_pack;
    public $minCount;
    public $maxCount;
    public $type;
    public $category;
    public $catalogPath;
    public $editPath;
    public $stickers;
    public $position;
    public $fee = 0;
    public $store_id;
    public $list_id;
    public $productName;
    public $variantName;
    public $variantCatalogName;
    public $storeListJson;
    public $propertyList = [];
    public $propertySorted;
    public $activeProperties;
    public $propertyGroups;
    public $variationList;
    public $propertiesJsonList = [];
    public $tool = 0;

    public function __construct()
    {

    }

    public function setParams(GoodsVariations $variant){
        $product = $variant->product;
        $this->product_id = $variant->good_id;
        $this->variant_id = $variant->id;
        $this->comissionId = $product->comissionId;
        $this->comissionValue = $variant->comission;
        $this->count_pack = $product->count_pack;
        $this->minCount = $product->count_min;
        $this->maxCount = $variant->maxCount;
        $this->type = $product->type_id;
        $this->category = $product->category->id;
        $this->catalogPath = '/' . $product->category->catalogPath . $variant->good_id;
        $this->editPath = '/product/update?id=' . $variant->good_id;
        $this->position = $product->position;
        $this->priceIn = $variant->price;

        $this->stickers = [
            'new' => $product->new ? 1 : 0,
            'sale' => $product->sale ? 1 : 0,
            'discount' => $product->discount ? 1 : 0,
            'main' => $product->main ? 1 : 0,
            'bonus' => $product->bonus ? 1 : 0,
        ];

        $this->store_id = $product->defaultStore;
        $this->productName = $product->name;
        $this->variantName = $variant->getTitleWithProperties();
        $this->variantCatalogName = $variant->getTitleWithPropertiesForCatalog();
        $this->storeListJson = $product->storeListJson;

        foreach ($product->variations as $variation) {
            if(!$variation->propertiesFrontVisible){

            }else{
                foreach ($variation->propertiesFrontVisible as $item) {
                    $this->propertyList[$variation->id][$item->group_id][$item->id] = $item->value;
                }
            }
        }

        $this->propertySorted = $product->propertyIndexed;
        $this->activeProperties = $variant->propertiesIndexed;

        $this->propertyGroups = $variant->propertyGroups;
        $this->variationList = $product->variationsCatalog;

        foreach($this->variationList as $variant){
            $dataJson = [];
            if(!empty($this->propertyList[$variant->id])){
                foreach($this->propertyList[$variant->id] as $key => $propertyId){
                    $dataJson[$key] = key($propertyId);
                }
            }
            $this->propertiesJsonList[json_encode($dataJson)] = 1;
        }

        $this->price = $this->setProductPriceValue($this->priceIn);
    }

    public function load($params){
        if(!empty($params)){
            foreach ($params as $key => $param) {
                $this->{$key} = !empty($param) ? $param : false;
            }
        }

        return $this;
    }

    public function setProductPriceValue($price){
        return $this->comissionId == 1001 ?
            ceil($price * $this->count_pack) :
            ceil(($price + $price * $this->comissionValue / 100) * $this->count_pack);
    }

    public function getDiscountPrice($discount){
        $price_out = $this->comissionId == 1001 ?
            ceil($this->priceIn * $this->count_pack) :
            ceil(($this->priceIn + $this->priceIn * $this->comissionValue / 100) * $this->count_pack);

        $price_out -= $this->priceBonus;
        if(\Yii::$app->user->identity && $this->stickers['discount'] == 1){
            return floor(($price_out) * (100 - $discount)/100);
        }else{
            return floor($price_out);
        }
    }
}
