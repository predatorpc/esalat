<?php

namespace app\components\html;

use Yii;
use yii\base\Widget;
use\app\modules\catalog\models\Goods;
use\app\modules\common\models\ModFunctions;
use yii\helpers\Url;

class WStock extends Widget
{
    public function run()
    {
        // Текущий урл;
        $urlTo = explode('/',Url::to(['index']));
        // Акция формула подсчета индикатор; current_accumulation
        $stock = \Yii::$app->action->getActiveAccum();
        if(!empty($stock[0]['condition_value'])) {
            $progress = ModFunctions::moneyFloat(($stock[0]['current_accumulation'] <= $stock[0]['max_accumulation']) ? $stock[0]['current_accumulation'] * 100 / $stock[0]['max_accumulation'] : 100);
        } ?>
       <?php if(!empty($stock[0]['condition_value'])  && ($urlTo[2] == 'index' || $urlTo[1] == 'basket')): ?>
           <div id="stock" >

               <div class="content-stock"  data-placement="right" title="Сделай 6 заказов на сумму от 3000 руб. каждый и получи пресс для чеснока Gipfel в подарок! Спортивные товары и спортивное питание в акции не участвуют." >
                   <div class="img ">
                       <input type="hidden" value="<?=$progress?>" id="initValue">
                       <a href="<?=  Goods::getPath($stock[0]['condition_value']); ?>" class="no-border">
                           <div id="indicatorContainer"  <?php  if(empty($stock[0]['spent']) && $stock[0]['max_accumulation'] <= $stock[0]['current_accumulation']): ?>class="pulse-hover"<?php endif; ?> ></div>
                           <img src="http://www.esalad.ru<?=Goods::findProductImage($stock[0]['condition_value'],'min')?>" class="ad">
                         <?php  if(empty($stock[0]['spent']) && $stock[0]['max_accumulation'] <= $stock[0]['current_accumulation']): ?>
                           <div class="btn button btn-xs">Получить</div>
                         <?php endif; ?>
                       </a>
                   </div>
               </div>
           </div>
       <?php  endif; ?>
        <?php
    }
}