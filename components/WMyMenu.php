<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use app\modules\pages\models\PagesMenus;
/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "product-item" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WMyMenu extends Widget
{
    public function run(){
        foreach(PagesMenus::getPagesMyMenu() as $key => $menu){?>
               <div class="item"><a href="<?=$menu['url']?>"><?= $menu['label']?></a></div>
        <?php
        }
    }
}
