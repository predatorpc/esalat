<?php

namespace app\components;

use app\modules\common\models\Zloradnij;
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
class WListInCatalog extends Widget
{
    public $model;

    public function init()
    {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
    }

    public function run(){
        if(!$this->model){
            return false;
        }else{
            ?>
                <div class="item">
                    <a href="/catalog/product-list/<?=$this->model->id?>" class="no-border open">
                        <div class="images">
                            <img alt="" src="http://www.esalad.ru<?=$this->model->image?>">
                            <div class="bottom">
                                <div class="name"><?= $this->model->title?></div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
        }
    }
}
