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

class WidgetReportOrderFilterShop extends Widget{
    public $shop;
    public $i;

    public function init(){
        parent::init();
        if($this->shop === null){
            return false;
        }
        $i = !empty($i) ? $i : 0;
    }

    public function run(){?>
        <div class="order-report-filter-shop-element" data-shop-id="<?= $this->shop->id?>">
            <input type="hidden" name="OwnerOrderFilter[shops][id][<?= $this->i?>]" value="<?= $this->shop->id?>">
            <?= $this->shop->name?>
        </div><?php
    }
}
