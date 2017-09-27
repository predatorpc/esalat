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

class WidgetReportOrderShopList extends Widget{
    public $shopList;

    public function init(){
        parent::init();
        if($this->shopList === null){
            return false;
        }
    }

    public function run(){?>
        <div class="shop-list-fixed-position">
           <div class="content-shop">
              <?php foreach ($this->shopList as $shop) {?>
                    <div data-shop-id="<?= $shop->id?>"><?= $shop->name?></div><?php
                }?>
           </div>
        </div><?php
    }
}
