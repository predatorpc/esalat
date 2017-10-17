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

class WMyMenuMobile extends Widget
{

    public function run(){
        $foo =  count(PagesMenus::getPagesMyMenu());
        $iNumber = 0;
        foreach(PagesMenus::getPagesMyMenu() as $key => $menu){
               $iNumber++;

            if($key != 8 && $key != 7) {
                ?>

                <div class="item">
                    <a href="<?= $menu['url'] ?>"
                       class="<?php if ($foo == $iNumber): ?>no<?php endif; ?> no-border"><?= $menu['label'] ?></a>
                </div>
                <?php
            }
        }
    }
}