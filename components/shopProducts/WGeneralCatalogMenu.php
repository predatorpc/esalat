<?php

namespace app\components\shopProducts;

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
// Каталог;
class WGeneralCatalogMenu extends Widget
{
    public function run(){

        ?>
        <div class="items">
            <?php $index = 0;?>
            <?php foreach(Yii::$app->controller->catalogMenu as $key => $item_group): ?>
                <?php if($item_group['type'] == '2'): ?>
                <div class="item selected _bg">
                    <a class="menu-top <?php if(strlen ($item_group['title']) > 26):?> max <?php endif;?>" href="/catalog/<?=$item_group['alias']?> " ><?=$item_group['title']?></a>
                    <?php if(isset($item_group['items'])):?>
                        <!--Контайнер каталог-->
                        <div id="nav-<?=$item_group['id']?>" class="container-catalog groups">
                            <div class="block">
                                <?php $iNumber_cat = 0; ?>
                                <?php $foo_cat =  count($item_group['items']); ?>
                                <?php foreach($item_group['items'] as $subcat): ?>
                                    <?php $iNumber_cat++ ?>
                                    <?php if(isset($subcat['items'])):?>
                                        <div class="row-container">
                                            <div class="item-cat"><a class="menu-cat main blue" href="/catalog/<?=$item_group['alias']?>/<?=$subcat['alias']?>" ><?=$subcat['title']?></a></div>
                                            <!-- Обход вложения подгруппы -->
                                            <?php foreach($subcat['items'] as $k=> $i): ?>
                                            <div class="item-cat"><a class="menu-cat blue" href="<?=$i['url']?>"><?=$i['title']?></a></div>
                                            <?php endforeach;?><!-- Обход вложения подгруппы -->
                                        </div>
                                    <?php else: ?>
                                        <?php if($foo_cat == $iNumber_cat):?>
                                            <div class="row-container">
                                                <div class="item-cat"><a class="menu-cat main blue" href="/catalog/<?=$item_group['alias']?>" ></a></div>
                                                <?php foreach($item_group['items'] as $subcat): ?>
                                                <?php if(!isset($subcat['items'])):?>
                                                    <div class="item-cat"><a class="menu-cat blue bold" href="<?=$subcat['url']?>"><?=$subcat['title']?></a></div>
                                                <?php endif; ?>
                                                <?php endforeach;?><!-- Обход вложения подгруппы -->
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </div>
                        </div><!--/Контайнер каталог-->
                    <?php endif;?>
                </div>
             <?php endif;?>
            <?php endforeach;?>
        </div>

        <?php
    }
}
