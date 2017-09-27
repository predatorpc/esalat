<?php

namespace app\components;

use yii\base\Widget;

class WBasketVisualShop extends Widget
{
    public $shop;

    public function init()
    {
        parent::init();
        if ($this->shop === null) {
            $this->shop = false;
        }
    }

    public function run(){
        if(!$this->shop){
            return false;
        }else{
            $result = '
            <tr class="top">
                <td class="shop-image"></td>
                <td colspan="5" class="shop-name">
                    <div class="title"><b>'.$this->shop['shop_name'].'</b></div>
                    '.(($this->shop['min_order'] > $this->shop['money'])?'<div class="min_order">Минимальная сумма заказа: <b>'.$this->shop['min_order'].' p.</b></div>':'').'
                    '.(($this->shop['shop_phone'])?'<div class="phone">Служба доставки: '.$this->shop['shop_phone'].'</div>':'').'
                </td>
            </tr>
            ';
            return $result;
        }
    }
}