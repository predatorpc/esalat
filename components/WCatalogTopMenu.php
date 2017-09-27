<?php

namespace app\components;

use Yii;
use app\controllers\MenuController;
use yii\base\Widget;

class WCatalogTopMenu extends Widget{
    public $key;

//    public function init(){
//        parent::init();
//    }
    public function run(){
        $menu = MenuController::getCatalogTopMenu();
        ?>
            <div class="items">
                <div class="catalog-goods item _bg">
                    <div class="menu-icon catalog"> </div>
                    <!--Контайнер меню-->
                    <div class="container-menu">
            <?php foreach(Yii::$app->controller->catalogMenu as $key => $item): ?>
                <div class="item-menu">
                    <a data-id="<?=$item['id']?>"  class="menu-top one _bg" href="/catalog/<?=$item['alias']?>"><?=$item['title']?></a>
                    <?php if(isset($item['items'])):?>
                        <!--Контайнер каталог-->
                        <div rel="<?=$item['id']?>" class="container-catalog">
                            <div class="block">
                                <?php $iNumber = 0; ?>
                                <?php $foo =  count($item['items']); ?>
                                <?php foreach($item['items'] as $subcat): ?>
                                    <?php $iNumber++ ?>
                                    <?php if(isset($subcat['items'])):?>
                                        <div class="row-container">
                                            <div class="item-cat"><a class="menu-cat main blue" href="<?=$subcat['url']?>"><?=$subcat['title']?></a></div>
                                            <!-- Обход вложения подгруппы -->
                                            <?php foreach($subcat['items'] as $k=> $i): ?>
                                                <div class="item-cat"><a class="menu-cat blue" href="<?=$i['url']?>"><?=$i['title']?></a></div>
                                                <!-- /Обход вложения подгруппы -->
                                            <?php endforeach;?>
                                        </div>
                                    <?php else: ?>
                                        <?php if($foo == $iNumber):?>
                                            <div class="row-container">
                                                <div class="item-cat"><a  data-id="<?=$item['id']?>" class="menu-cat main blue" href="/catalog/<?=$item['alias']?>" ></a></div>
                                                <?php foreach($item['items'] as $subcat): ?>
                                                <?php if(!isset($subcat['items'])):?>
                                                    <div class="item-cat"><a data-id="<?=$subcat['id']?>" class="menu-cat blue bold" href="<?=$subcat['url']?>"><?=$subcat['title']?></a></div>
                                                <?php endif; ?>
                                                <?php endforeach;?><!-- Обход вложения подгруппы -->
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </div>
                        </div><!--/Контайнер каталог-->
                    <?php endif; ?>
                </div>
            <?php endforeach;?>
            </div><!--/Контайнер меню-->
            </div>
            <?php $index = 0;?>
            <?php foreach(Yii::$app->controller->catalogMenu as $key => $item_group): ?>

                <div class="item selected <?php if($index++ < 4):?>_bg <?php endif; ?>">
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
            <?php endforeach;?>
            </div>
            <?php
    }
}
