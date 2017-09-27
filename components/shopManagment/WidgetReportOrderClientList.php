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

class WidgetReportOrderClientList extends Widget{
    public $clientList;

    public function init(){
        parent::init();
        if($this->clientList === null){
            return false;
        }
    }

    public function run(){?>
        <div class="client-list-fixed-position">
            <div class="content-shop">
            <?php
                    foreach ($this->clientList as $client) {?>
                        <div data-client-id="<?= $client->id?>"><?= $client->name?></div><?php
                    }?>
            </div>
        </div>
        <?php
    }
}
