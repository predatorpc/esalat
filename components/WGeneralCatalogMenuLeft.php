<?php

namespace app\components;

use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\CategoryLinks;

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
class WGeneralCatalogMenuLeft extends Widget
{
    public function run(){
        $catalogHash = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        $urls = [];
        $catalogMenu = Catalog::buildTree($catalogHash,$urls);
        $_index = 1;

        ?>
        <div class="items ">
            <div class="row">
              <div class="col-xs-3 bg_panel left"><div class="title-menu">Каталог товаров</div></div>
              <div class=" col-xs-9 bg_panel right">
                  <div class="title-menu">
                      <!--<a class="menu-top one no-border" href="/catalog/tovary-dlya-doma/sad/">Все для сада и огорода</a>-->
                      <!--<a class="menu-top one no-border" href="/catalog/dostavka2-4/tovdom2-4/sport-turizm/">Активный отдых и туризм</a>-->

                     <!--<a class="menu-top one no-border" href="/catalog/sports/velosipedy/">Велоспорт</a>-->
                      <!--<a class="menu-top one no-border" href="/catalog/tovary-dlya-detey/games/">Игрушки</a>-->
                      <!--<a class="menu-top one no-border _master_h_stock" href="/static/page/stock">Акции</a>-->
                  </div>
              </div>
            </div>
            <div class="clear"></div>
            <!--Контайнер меню-->
            <div class="container-menu col-lg-3 <?=Yii::$app->request->url != '/' ? 'open' : ''?>">
                <?php foreach($catalogMenu as $key => $item): ?>
                    <div class="item-menu">
                            <a class="menu-top one no-border" href="/catalog/<?=$item['alias']?>"><?=$item['title']?><div class="border" style="<?=!empty($item['color']) ? 'background:'.$item['color'] : ''?>"></div><span class="current" style="font-size:11px; margin-left: 3px;">(<?=Catalog::getCategoryGoodsCount($item['id'])?>)</span></a>
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
                                                <div class="item-cat"><a class="menu-cat main blue" href="<?=$subcat['url']?>"><?=$subcat['title']?> <span>(<?=Catalog::getCategoryGoodsCount($subcat['id'])?></span>)</a></div>
                                                <!-- Обход вложения подгруппы -->
                                                <?php foreach($subcat['items'] as $k=> $i): ?>
                                                    <?php
                                                    $count_i =  Catalog::getCategoryGoodsCount($i['id']);
                                                    ?>
                                                    <div class="item-cat"><a class="menu-cat blue" href="<?=$i['url']?>"><?=$i['title']?> <span>(<?=$count_i?>)</span></a></div>
                                                    <!-- /Обход вложения подгруппы -->
                                                <?php endforeach;?>
                                            </div>
                                        <?php else: ?>
                                            <?php if($foo == $iNumber):?>
                                                <div class="row-container">
                                                    <div class="item-cat hidden"><a class="menu-cat main blue" href="/catalog/<?=$item['alias']?>" ></a></div>
                                                    <?php foreach($item['items'] as $subcat): ?>
                                                    <?php if(!isset($subcat['items'])):?>
                                                        <div class="item-cat"><a class="menu-cat bold blue all" href="<?=$subcat['url']?>"><?=$subcat['title']?> <span>(<?=Catalog::getCategoryGoodsCount($subcat['id'])?></span>)</a></div>
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
                <!--<div class="item-menu">
                    <a class="menu-top one no-border" href="/catalog/wish-list">Я хочу</a>
                </div>-->
                <div class="poster__left  <?=Yii::$app->request->url != '/' ? 'hidden' : ''?>">
                    <?=\app\components\WBanners::widget()?>
                </div>
            </div><!--/Контайнер меню-->
            <?=Yii::$app->request->url != '/' ? '<div class="clear"></div> ' : ''?>

        </div>


    <?php
    }
}
