<?php
namespace app\widgets\layout;

use yii\base\Widget;


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
class WCashButton extends Widget {

    public function run(){?>
        <div
            class="fixed-admin-block"
             style="
                position: fixed;
                bottom:0;
                right: 0;
                border: 1px solid #000;
                background: rgba(155,0,0,0.7);
                z-index: 999;
             "
        >
            <div
                class="clear-cash-button"
                style="
                    padding: 10px;
                    cursor: pointer;
                    text-shadow: 1px 1px 0px #FFF;
                 "
            >
                Сбросить кэш
            </div>
        </div>
        <?php
    }
}

