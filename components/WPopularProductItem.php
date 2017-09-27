<?php
namespace app\components;

use app\modules\common\models\Zloradnij;
use yii\base\Widget;
use yii\helpers\Url;
use app\modules\catalog\models\Goods;
use app\modules\common\models\ModFunctions;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
class WPopularProductItem extends Widget {

    public function run(){
         $newProduct = Goods::find()
             ->where(['goods.status' => 1, 'goods.show' => 1, 'goods.confirm' => 1, 'goods.hit' => 1, ])->orderBy('goods.id  DESC')->limit(21)->all();

        ?>
            <div class="items">
                <?php foreach ($newProduct as $key => $product):
                    $stickers = Goods::findProductStickers([$product->id]);
                    $url = (!empty($product->catalogUrl)) ? $product->catalogUrl : '/';
                    ?>
                <div id="<?=$product->id?>" class="item item-<?= $product->id?>">
                    <div class="block" onclick="return show_modal_compact('/catalog/compact',' ','<?=$product->id?>');">
                        <div class="images">
                            <a href="<?=$url?>"><?php if(!empty($product->imageSimple)): ?><img alt="<?=$product->name?>" src="http://www.esalad.ru/<?=$product->imageSimple;?>" class="ad"><?php else: ?><img src="http://www.esalad.ru/images/<?=(!empty(\Yii::$app->params['en']) ? 'good_min_en' : 'good_min')?>.png" alt="<?=$product->name?>" class="ad"/><?php endif;?></a>
                        </div>
                        <div class="title"><a href="<?=$url?>" class="black"  title="<?=$product->name?>"><?=$product->name?></a></div>
                        <div class="group"><?=$product->category->title?></div>
                        <div class="prices"><span class="price<?php if(!empty($product->priceVariantDiscount)): ?> normal<?php endif; ?> variation-price"> <?= ModFunctions::money($product->priceVariant)?></span><?php if(!empty(!$product->priceVariantDiscount)): ?><span class="price discount variation-discount-price"><?=ModFunctions::money($product->priceVariantDiscount)?>*</span><?php endif; ?></div>
                        <!-- Приватный блок-->
                        <div class="row-private">
                        </div><!-- /Приватный блок-->
                        <!-- Наклейки -->
                        <div class="stickers__new">
                            <?php
                            if(!empty($product->stickerLinks)){
                                foreach($product->stickerLinks as $stickersNew): ?>
                                    <div class="sticker-image" style="background-image: url('/files/sticker/<?=$stickersNew->sticker->id?>.png');"></div>
                                <?php endforeach;
                            }?>
                        </div> <!-- Наклейки -->
                        <!-- Наклейки -->
                        <div class="stickers stickers__com ">
                            <?= isset($stickers[$product->id]['bonus'])?'<div class="stikers-icon bonus hidden"></div>':''?>
                            <?= isset($stickers[$product->id]['news'])?'<div class="stikers-icon news hidden"></div>':''?>
                        </div> <!-- Наклейки -->
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php
        }
}

