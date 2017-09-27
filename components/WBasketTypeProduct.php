<?php

namespace app\components;

use yii\base\Widget;

class WBasketTypeProduct extends Widget
{
    public $typeName;

    public function init()
    {
        parent::init();
        if ($this->typeName === null) {
            $this->typeName = false;
        }
    }

    public function run(){
        if(!$this->typeName){
            return false;
        }else{
            $result = $this->typeName->name;
            return $result;
        }
    }
}