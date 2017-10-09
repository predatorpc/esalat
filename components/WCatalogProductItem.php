<?php
namespace app\components;

use app\modules\catalog\models\GoodsGroups;
use app\modules\common\models\Zloradnij;
use yii\base\Widget;
use yii\helpers\Url;
use yii\db\Query;
use app\modules\catalog\models\Category;
use app\modules\common\models\ModFunctions;
use app\components\WProductItemOne;
use app\modules\catalog\models\Goods;
use Yii;
/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
class WCatalogProductItem extends Widget {

    public function run(){

        //$categories = Category::find()->where(['active' => 1,'level'=>0])->orderBy('level, sort')->all();
        $categories = Category::find()->where(['active' => 1,'level'=>0])->orderBy('level, sort')->all();

        ?>
        <div id="list-wrapper" class="product-list js-product-list mod___goods_list goods-list">
            <div id='sort' class='items' style="overflow: hidden;">
                <?php
                foreach ($categories as $category) {
                    print '<h2 class="title" style="text-align: left">' . $category->title . '</h2>';

                    foreach ($category->categories as $category_parent) {
                        if(!empty($category_parent->categories)) {
                            print '<h3 class="title"><b>' . $category_parent->title . '</b></h3>';
                            ?>

                            <div class="items">
                                <?php foreach ($category_parent->productsClear as $key => $product):
                                    $stickers = Goods::findProductStickers([$product->id]);
                                    $url = (!empty($product->catalogUrl)) ? $product->catalogUrl : '/';
                                    ?>
                                    <div id="<?= $product->id ?>" class="item item-<?= $product->id ?>">
                                        <div class="block"
                                             onclick="return show_modal_compact('/catalog/compact',' ','<?= $product->id ?>');">
                                            <div class="images">
                                                <a href="<?= $url ?>"><?php if (!empty($product->imageSimple)): ?><img
                                                        alt="<?= $product->name ?>"
                                                        src="http://www.esalad.ru/<?= $product->imageSimple; ?>"
                                                        class="ad"><?php else: ?><img
                                                        src="http://www.esalad.ru/images/<?= (!empty(\Yii::$app->params['en']) ? 'good_min_en' : 'good_min') ?>.png"
                                                        alt="<?= $product->name ?>" class="ad"/><?php endif; ?></a>
                                            </div>
                                            <div class="title"><a href="<?= $url ?>" class="black"
                                                                  title="<?= $product->name ?>"><?= $product->name ?></a>
                                            </div>
                                            <div class="group"><?= $product->category->title ?></div>
                                            <div class="prices"><span
                                                    class="price<?php if (!empty($product->priceVariantDiscount)): ?> normal<?php endif; ?> variation-price"> <?= ModFunctions::money($product->priceVariant) ?></span><?php if (!empty(!$product->priceVariantDiscount)): ?>
                                                    <span
                                                        class="price discount variation-discount-price"><?= ModFunctions::money($product->priceVariantDiscount) ?>
                                                    *</span><?php endif; ?></div>
                                            <!-- Приватный блок-->
                                            <div class="row-private">
                                            </div><!-- /Приватный блок-->
                                            <!-- Наклейки -->
                                            <div class="stickers__new">
                                                <?php
                                                if (!empty($product->stickerLinks)) {
                                                    foreach ($product->stickerLinks as $stickersNew): ?>
                                                        <div class="sticker-image"
                                                             style="background-image: url('/files/sticker/<?= $stickersNew->sticker->id ?>.png');"></div>
                                                    <?php endforeach;
                                                } ?>
                                            </div> <!-- Наклейки -->
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php

                        }
                        if(!empty($category_parent->categories)) {

                            foreach ($category_parent->categories as $category_i) {
                                echo '<div class="clear"></div>';
                                print '<h3 class="title">' . $category_i->title . '</h3>';
                                ?>

                                <div class="items">
                                    <?php foreach ($category_i->productsClear as $k => $product):
                                        $stickers = Goods::findProductStickers([$product->id]);
                                        $url = (!empty($product->catalogUrl)) ? $product->catalogUrl : '/';
                                        ?>
                                        <div id="<?= $product->id ?>" class="item item-<?= $product->id ?>">
                                            <div class="block"
                                                 onclick="return show_modal_compact('/catalog/compact',' ','<?= $product->id ?>');">
                                                <div class="images">
                                                    <a href="<?= $url ?>"><?php if (!empty($product->imageSimple)): ?>
                                                            <img
                                                            alt="<?= $product->name ?>"
                                                            src="http://www.esalad.ru/<?= $product->imageSimple; ?>"
                                                            class="ad"><?php else: ?><img
                                                            src="http://www.esalad.ru/images/<?= (!empty(\Yii::$app->params['en']) ? 'good_min_en' : 'good_min') ?>.png"
                                                            alt="<?= $product->name ?>" class="ad"/><?php endif; ?></a>
                                                </div>
                                                <div class="title"><a href="<?= $url ?>" class="black"
                                                                      title="<?= $product->name ?>"><?= $product->name ?></a>
                                                </div>
                                                <div class="group"><?= $product->category->title ?></div>
                                                <div class="prices"><span
                                                        class="price<?php if (!empty($product->priceVariantDiscount)): ?> normal<?php endif; ?> variation-price"> <?= ModFunctions::money($product->priceVariant) ?></span><?php if (!empty(!$product->priceVariantDiscount)): ?>
                                                        <span
                                                            class="price discount variation-discount-price"><?= ModFunctions::money($product->priceVariantDiscount) ?>
                                                        *</span><?php endif; ?></div>
                                                <!-- Приватный блок-->
                                                <div class="row-private">
                                                </div><!-- /Приватный блок-->
                                                <!-- Наклейки -->
                                                <div class="stickers__new">
                                                    <?php
                                                    if (!empty($product->stickerLinks)) {
                                                        foreach ($product->stickerLinks as $stickersNew): ?>
                                                            <div class="sticker-image"
                                                                 style="background-image: url('/files/sticker/<?= $stickersNew->sticker->id ?>.png');"></div>
                                                        <?php endforeach;
                                                    } ?>
                                                </div> <!-- Наклейки -->
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php

                            }
                        }
                        echo '<div class="clear"></div>';
                    }

                }
                ?>
            </div>
        </div>



        <?php // \app\components\WCatalogProductItem::widget()?>

        <?php
    }
}

