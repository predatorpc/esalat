<?php

namespace app\components\html;

use Yii;
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
class WChat extends Widget
{
    public function run()
    {?>
        <!-- BEGIN JIVOSITE CODE -->
        <script type='text/javascript'>(function(){ var widget_id = 'sVP36DGYON'; var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
        <!-- END JIVOSITE CODE -->
        <?php
    }
}
