<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Nav;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "catalog-menu-left" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WCatalogMenuLeft extends Widget
{
    public $menu;

    public function init()
    {
        parent::init();
        if ($this->menu === null) {
            $this->menu = false;
        }
    }

    public function run(){
        if(!$this->menu){
            return false;
        }else{
            ?>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 catalog-menu-left">
                <?= Nav::widget(['options' => ['id' => 'left-catalog','class' => '', 'style'=>'' ], 'items' => $this->menu]);?>
            </div>
            <?php
        }
    }
}
