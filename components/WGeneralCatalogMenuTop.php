<?php

namespace app\components;

use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\common\models\Zloradnij;
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
class WGeneralCatalogMenuTop extends Widget
{
    public function run(){
        $catalogHash = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        $urls = [];
        $catalogMenu = Catalog::buildTree($catalogHash,$urls);
        $count_goods = 1;
             // print_arr(Yii::$app->params['userCatalogHide']['status']);

        ?>
        <div class="items">
            <div class="catalog-goods item _bg">
                <div class="menu-icon catalog"> </div>
                <!--Контайнер меню-->
                <div class="container-menu">
                    <?php foreach($catalogMenu as $key => $item): ?>
                        <?php if(!empty(Yii::$app->params['userCatalogHide']['status'])):?>
                         <?php if($item['id'] != Yii::$app->params['userCatalogHide']['id']):?>
                        <div class="item-menu">
                            <a class="menu-top one _bg" href="/catalog/<?=$item['alias']?>"><?=$item['title']?></a>
                            <?php if(isset($item['items'])):?>
                                <!--Контайнер каталог-->
                                <div rel="<?=$item['id']?>" class="container-catalog">
                                    <div class="block">
                                        <?php $iNumber = 1; ?>
                                        <?php $foo =  count($item['items']); ?>
                                        <?php foreach($item['items'] as $subcat): ?>
                                            <?php $iNumber++ ?>
                                            <?php if(isset($subcat['items'])):?>
                                                <div class="row-container">
                                                    <div class="item-cat"><a class="menu-cat main blue" href="<?=$subcat['url']?>"><?=$subcat['title']?><span class="counts_goods">(<?=Catalog::getCategoryGoodsCount($subcat['id'])?>)</span></a></div>
                                                    <!-- Обход вложения подгруппы -->
                                                    <?php foreach($subcat['items'] as $k=> $i): ?>
                                                        <div class="item-cat"><a class="menu-cat blue" href="<?=$i['url']?>"><?=$i['title']?><span class="counts_goods">(<?=Catalog::getCategoryGoodsCount($i['id'])?>)</span></a></div>
                                                        <!-- /Обход вложения подгруппы -->
                                                    <?php endforeach;?>
                                                </div>
                                            <?php else: ?>
                                                <?php if($foo == $iNumber):?>
                                                    <div class="row-container">
                                                        <div class="item-cat"><a class="menu-cat main blue" href="/catalog/<?=$item['alias']?>" ></a></div>
                                                        <?php foreach($item['items'] as $subcat): ?>
                                                        <?php if(!isset($subcat['items'])):?>
                                                            <div class="item-cat"><a class="menu-cat <?=$subcat['id'] == 10000289 ? 'danger':'blue'?>  bold" href="<?=$subcat['url']?>"><?=$subcat['title']?><span class="counts_goods">(<?=Catalog::getCategoryGoodsCount($subcat['id'])?>)</span></a></div>
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
                         <?php endif; ?>
                         <?php else: ?>
                            <div class="item-menu">
                                <a data-id="<?=$item['id']?>" class="menu-top one _bg" href="/catalog/<?=$item['alias']?>"><?=$item['title']?></a>
                                <?php if(isset($item['items'])):?>
                                    <?php

                                    ?>
                                    <!--Контайнер каталог-->
                                    <div rel="<?=$item['id']?>" class="container-catalog">
                                        <div class="block">
                                            <?php $iNumber = 0; ?>
                                            <?php $foo =  count($item['items']); ?>
                                            <?php foreach($item['items'] as $subcat): ?>
                                                <?php $iNumber++ ?>
                                                <?php if(isset($subcat['items'])):?>
                                                    <div class="row-container">
                                                        <div class="item-cat"><a class="menu-cat main" href="<?=$subcat['url']?>"><?=$subcat['title']?></a></div>
                                                        <!-- Обход вложения подгруппы -->
                                                        <?php foreach($subcat['items'] as $k=> $i): ?>
                                                            <div class="item-cat"><a class="menu-cat blue" href="<?=$i['url']?>"><?=$i['title']?></a></div>
                                                            <!-- /Обход вложения подгруппы -->
                                                        <?php endforeach;?>
                                                    </div>
                                                <?php else: ?>
                                                    <?php if($foo == $iNumber):?>
                                                        <div class="row-container">
                                                            <div class="item-cat"><a class="menu-cat main blue" href="/catalog/<?=$item['alias']?>"></a></div>
                                                            <?php foreach($item['items'] as $subcat): ?>
                                                            <?php if(!isset($subcat['items'])):?>
                                                                <div class="item-cat"><a data-id="<?=$subcat['id']?>" class="menu-cat <?=$subcat['id'] == 10000289 ? 'danger':'blue'?> bold" href="<?=$subcat['url']?>"><?=$subcat['title']?></a></div>
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
                        <?php endif; ?>
                    <?php endforeach;?>
                </div><!--/Контайнер меню-->
            </div>
            <?php $index = 0;?>

            <?php foreach($catalogMenu as $key => $item_group): ?>
                <div class="item selected <?php if($item_group['id'] == 10000005):?> _s <?php endif; ?> <?php if($index++ < 4):?>_bg <?php endif; ?>">
                  <a class="menu-top <?php if(strlen ($item_group['title']) > 28):?> max <?php endif;?>" href="/catalog/<?=$item_group['alias']?> " ><?=$item_group['title']?></a>
                  <?php if(!empty($item_group['items'])):?>
                      <!--Контайнер каталог-->
                      <div id="nav-<?=$item_group['id']?>" class="container-catalog groups">
                          <div class="block">
                              <?php $foo_cat =  count($item_group['items']); ?>
                              <?php $iNumber_cat = 1; ?>

                              <?php foreach($item_group['items'] as $subcat): ?>

                                  <?php $iNumber_cat++ ?>

                                  <?php if(isset($subcat['items'])):?>
                                      <div class="row-container">
                                          <div class="item-cat"><a class="menu-cat main blue" href="/catalog/<?=$item_group['alias']?>/<?=$subcat['alias']?>" ><?=$subcat['title']?><span class="counts_goods">(<?=Catalog::getCategoryGoodsCount($subcat['id'])?>)</span></a></div>
                                          <!-- Обход вложения подгруппы -->
                                          <?php foreach($subcat['items'] as $k=> $i): ?>
                                              <div class="item-cat" data-id="<?=$i['id']?>"><a class="menu-cat blue" href="<?=$i['url']?>"><?=$i['title']?><span class="counts_goods">(<?=Catalog::getCategoryGoodsCount($i['id'])?>)</span></a></div>
                                          <?php endforeach;?><!-- Обход вложения подгруппы -->
                                      </div>
                                  <?php else: ?>
                                      <?php
                                    //  print_arr($foo_cat.' +');
                                   //   print_arr($iNumber_cat.' =');
                                      ?>
                                      <?php if($foo_cat == $iNumber_cat):?>
                                          <div class="row-container">
                                              <div class="item-cat"><a class="menu-cat main blue" href="/catalog/<?=$item_group['alias']?>" ></a></div>
                                              <?php foreach($item_group['items'] as $subcat): ?>
                                                  <?php if(!isset($subcat['items'])):?>
                                                      <div class="item-cat"><a data-id="<?=$subcat['id']?>" class="menu-cat <?=$subcat['id'] == 10000289 ? 'danger':'blue'?> bold" href="<?=$subcat['url']?>"><?=$subcat['title']?><span class="counts_goods">(<?=Catalog::getCategoryGoodsCount($subcat['id'])?>)</span></a></div>
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
