<?php

namespace app\components;

use yii\base\Widget;

class WBasketProductListTest extends Widget
{
    public $productList;

    public function init()
    {
        parent::init();
        if ($this->productList === null) {
            $this->productList = false;
        }
    }

    public function run(){
        if(!$this->productList){
            return false;
        }else{
            $result = '';
            foreach($this->productList as $idProduct => $product){
                $result .= \app\components\WBasketProductTest::widget([
                    'product' => $product,

                ]);

            }
            return $result;
        }
    }
}

