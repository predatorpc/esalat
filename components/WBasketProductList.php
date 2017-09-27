<?php

namespace app\components;

use app\modules\basket\models\CatalogProduct;
use app\modules\catalog\models\GoodsTypes;
use app\modules\catalog\models\Lists;
use app\modules\common\models\Zloradnij;
use yii\base\Widget;

class WBasketProductList extends Widget
{
    public $basket;

    public function init()
    {

        parent::init();
        if ($this->basket === null) {
            $this->basket = false;
        }
    }

    public function run(){
        if(!$this->basket){
            return false;
        }else{

            $lists = $this->basket->basketList;
            if(!empty($lists)){
                foreach ($lists as $list) {?>
                    <div class="product-type-big-block">
                        <div class="type_name product-list-type-title"><?=\Yii::t('app','Список');?>: <?= $list->title;?></div>
                        <!--Список товаров-->
                        <div class="goods-basket product-list-block row" ><?php

                            foreach($this->basket->products as $i => $product){
                                if($product->list_id == $list->id){?>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 <?= ($i % 2) ? 'odd' : 'even'?>">
                                        <div class="col-xs-5 col-sm-6 col-md-6 col-lg-6">
                                            <a href="<?= $product->variant->product->catalogPath?>"><?= $product->variant->product->name?></a>
                                        </div>
                                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-center">
                                            <?= $product->variant->titleWithPropertiesForCatalog?>
                                        </div>
                                        <div class="col-xs-2 col-sm-1 col-md-1 col-lg-1 text-right">
                                            <?= $product->count?> <?=\Yii::t('app','шт.')?>
                                        </div>
                                    </div><?php
                                }
                            }?>
                        </div> <!--Список товаров-->
                       <div class="clear"></div>
                    </div><?php
                }
            }

            foreach($this->basket->productTypes as $productTypeId => $products){
                $currentProductTypeItems = [];
                foreach($products as $product){
                    if(empty($product->list_id)){
                        $currentProductTypeItems[] = $product;
                    }
                }

                if(!empty($currentProductTypeItems)){?>
                    <div class="product-type-big-block">
                        <div class="type_name"><?= \app\components\WBasketTypeProduct::widget(['typeName' => GoodsTypes::find()->where(['id' => $productTypeId])->one()]);?></div>
                        <div class="shop_type hidden">
                            <div class="title_shop "><b></b></div>
                        </div>
                        <!--Список товаров-->
                        <div class="goods-basket goods__tile product-type-block" ><?php
                            foreach($currentProductTypeItems as $product){
                                print \app\components\WBasketProductVOne::widget([
                                    'product' => $product,
                                ]);
                            }?>
                            <div class="clear"></div>
                        </div> <!--Список товаров-->
                    </div><?php
                }
            }?>
            <div class="clear"></div>
            <div class="button">
                <div class="button_blue right remove-all-basket-products <?= \Yii::$app->user->id == 10013181 || \Yii::$app->user->id == 10000933 ? '' : 'hidden'?>">
                    <div><?=\Yii::t('app','Очистить все');?></div>
                </div>
            </div>
            <div class="clear"></div>
            <?php

            if(!\Yii::$app->user->isGuest){?>
                <div class="save-product-list-block">
                    <input type="text" name="product-list-name" class="product-list-name-input form-control" placeholder="<?=\Yii::t('app','Введите название списка');?>">
                    <div class="button_oran right min">
                        <div><?=\Yii::t('app','Сохранить как список');?></div>
                    </div>
                </div><?php
            }?>
            <div class="clear"></div>
            <?php
        }
    }
}
