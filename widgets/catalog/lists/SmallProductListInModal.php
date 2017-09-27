<?php

/* @var $product app\modules\catalog\models\Goods */

namespace app\widgets\catalog\lists;

use app\modules\catalog\models\Goods;
use app\modules\common\models\ModFunctions;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class SmallProductListInModal extends Widget
{
    public $productList;
    public $countInBlock = 5;

    public function init()
    {
        parent::init();

        if ($this->productList === null) {
            return false;
        }
    }

    public function run(){
        $i = 0;
        foreach ($this->productList as $item) {
            if($i % $this->countInBlock == 0){?>
                <div class="row small-product-list-packet-block<?= $i == 0 ? ' open' : ''?>"><?php
                if($i != 0){?>
                    <span class="small-product-list-packet-block-arrow-left"></span><?php
                }
            }?>
                <div
                    class="row small-product"
                    data-product="<?= $item->id?>"
                    data-variant="<?= $item->variationsCatalog[0]->id?>"
                    data-count="<?= $item->count_min?>"
                >
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <img class="img-thumbnail" src="<?= Yii::$app->params['domain'] . $item->images[0]?>" />
                    </div>
            <?php
             // print_arr($item->variationsCatalog[0]->PriceValue);
            ?>
                    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                          <?= $item->name?><br> <b> <?=$item->variationsCatalog[0]->PriceValue?> р.</b>


                    </div>
                </div><?php
//        (new Goods())->count_min
            if(($i+1) % $this->countInBlock == 0 || count($this->productList) == $i+1){
                if(count($this->productList) != $i+1){?>
                    <span class="small-product-list-packet-block-arrow-right"></span><?php
                }?>
                </div><?php
            }

            $i++;
        }
    }
}

