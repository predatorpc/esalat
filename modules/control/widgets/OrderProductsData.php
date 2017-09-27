<?php

namespace app\modules\control\widgets;

use yii\base\Widget;

class OrderProductsData extends Widget{
    public $order;

    public function run(){
        if(empty($this->order)){
            return false;
        }

        foreach ($this->order->ordersGroups as $ordersGroup) {
            print OrderGroupData::widget(['ordersGroup' => $ordersGroup]);
        }
    }
}
