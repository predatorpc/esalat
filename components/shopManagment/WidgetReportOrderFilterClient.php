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

class WidgetReportOrderFilterClient extends Widget{
    public $client;
    public $i;

    public function init(){
        parent::init();
        if($this->client === null){
            return false;
        }
        $this->i = !empty($i) ? $i : 0;
    }

    public function run(){?>
    <div class="order-report-filter-client-element" data-client-id="<?= $this->client->id?>">
        <input type="hidden" name="OwnerOrderFilter[clients][id][<?= $this->i?>]" value="<?= $this->client->id?>">
        <?= $this->client->name?>
        </div><?php
    }
}
