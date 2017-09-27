<?php

namespace app\components\shopMobile;

use Yii;
use app\controllers\MenuController;
use yii\base\Widget;
use app\modules\catalog\models\Goods;

class WMainCategory extends Widget{
    public $key;
    public function run(){
   // $main_category = MenuController::getCatalogTopMenu();
?>
        <div class="items">
            <?php foreach($main_category as $key => $category): ?>
                <div class="title-main">
                    <h2><a href="/catalog/<?=$category['alias']?>/" class="title-main no-border"><?=$category['title']?></a> <a href="#" class="menu-all hidden" >Показать все</a></h2>
                </div>
                <?php if(isset($category['items'])):?>
                    <?php foreach($category['items'] as $key => $subcat): ?>
                        <div class="container-item" rel="<?=$subcat['id']?>">
                            <div class="item">
                                <div class="images"><a href="/catalog/<?=$category['alias']?>/<?=$subcat['alias']?>"><img src="http://www.esalad.ru/<?=Goods::findProductImage($subcat['id'])?>" alt="" class="ad"></a></div>
                                <div class="title"><a href="/catalog/<?=$category['alias']?>/<?=$subcat['alias']?>" class="white top"><?=$subcat['title']?></a></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="clear"></div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="clear"></div>
        </div>
    <?php
    }
}
