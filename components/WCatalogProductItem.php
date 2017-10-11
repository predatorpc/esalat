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

    public $limit;
    public $categories;

    public function init() {
        parent::init();
        if ($this->categories === null) {
            $this->categories = false;
        }
        $this->limit  =  (!empty($this->limit) ? $this->limit : 0);

    }

    public function run(){
        $counts_category_parents = 0;
        ?>

                <?php
                   foreach ($this->categories as $category) {

                    $counts_category_parents = $category->getCategories()->count();

                    foreach ($category->getCategories()->limit(1)->offset($this->limit)->all() as $category_parent) {
                          print '<div class="main_title_js ">';
                                if(!empty($category_parent->categories)) {
                                        print '<h3 class="title"><b>' . $category_parent->title . '</b></h3>';
                                        ?>
                                        <div class="items">
                                            <?php foreach ($category_parent->productsClear as $key => $product):
                                                $stickers = Goods::findProductStickers([$product->id]);
                                                $url = (!empty($product->catalogUrl)) ? $product->catalogUrl : '/';
                                                ?>
                                                <div id="<?= $product->id ?>" class="item  item-<?= $product->id ?>">
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
                                        print '<div class="main_title_js">';
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
                                        print '</div>';

                                    }
                                }
                           print '</div>';
                            echo '<div class="clear"></div>';
                     }


                }
                ?>
                <?php if($counts_category_parents > 0): ?>
                   <div class="more more__load_js" data-count="<?=$counts_category_parents?>" data-all-cont="<?=$counts_category_parents?>"></div>
                   <div class="content-load" style="margin-top: 40px"></div>
                <?php  endif; ?>

        <?php
    }
}

