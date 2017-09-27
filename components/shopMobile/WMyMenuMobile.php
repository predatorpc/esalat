<?php

namespace app\components\shopMobile;

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
class WMyMenuMobile extends Widget
{

    public function run(){
        $foo =  count(PagesMenus::getPagesMyMenu());
        $iNumber = 0;
        foreach(PagesMenus::getPagesMyMenu() as $key => $menu){
               $iNumber++;
            ?>

            <div class="item">
                <a href="<?=$menu['url']?>" class="<?php if($foo == $iNumber):?>no<?php endif;?> no-border"><?=$menu['label']?></a>
            </div>
            <?php
        }
    }
}