<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class OrderGroupData extends Widget{
    public $ordersGroup;

    public function run(){
        if(empty($this->ordersGroup)){
            return false;
        }?>

        <div class="row">
            <div class="col-xs-3 col-sm-5 col-md-4 col-lg-3 text-right bold"><?= $this->ordersGroup->type->name?></div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"></div>
        </div><?php
        if(!empty($this->ordersGroup->ordersItems)){
            foreach ($this->ordersGroup->ordersItems as $ordersItem) {
                print OrderItemData::widget(['orderItem' => $ordersItem]);
            }
        }

    }
}
