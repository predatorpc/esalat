<?php
// _list_item.php
//use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */

$priceAttributes = [
    'commissionId' => $model->commissionId,
    'productPrice' => $model->productPrice,
    'countPack' => $model->countPack,
    'productCommission' => $model->productCommission,
    'productDiscount' => $model->productDiscount,
];
$firstVariant = key($variation);

?>
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 product-item">
    <div >
        <?php
        if(is_array($image)){
            ?>
                <img src="<?= $image[0]?>" alt="<?= $model->name?>" />
            <?php
        }
        ?>
    </div>

        <div>
            <a href="<?= Url::toRoute($url)?>">
                <?= $model->name;?>
            </a>
        </div>
        <div>
            <?= \app\models\Goods::getPrice($model->productId,$model->variantId,0,$priceAttributes);?>

        </div>
        <div>
            <?= \app\models\Goods::getPrice($model->productId,$model->variantId,5,$priceAttributes);?>
        </div>
        <div>
            <?= $model->productPrice;?>
        </div>
        <div>
            <?= $model->productCommission;?>
        </div>
        <div>
            <?= (implode('/',$sticker));?>
        </div>

        <div class="product-control-buttons">
            <?php
            $position = array_search($model->productId,\Yii::$app->session['basket-yii']['productsShort']);
            if ($position !== false) {
                ?>
                <input
                    data-action="remove"
                    value="Remove"
                    type="button"

                    data-basket="<?= \Yii::$app->session['basket-yii']['basketItems'][$position]?>"
                    data-product="<?= $model->productId?>"
                    data-variant="<?= $variation?key($variation):$model->variantId?>"
                    data-count="0"
                    data-url="<?= $url?>"
                    data-max="<?=$variation[$firstVariant]['productCount']?>"
                />
                <?php
            }else{
                ?>
                <input
                    data-action="bay"
                    value="Купить"
                    type="button"

                    data-basket=""
                    data-product="<?=$model->productId?>"
                    data-variant="<?=$variation?key($variation):$model->variantId?>"
                    data-count="1"
                    data-url="<?= $url?>"
                    data-max="<?=$variation[$firstVariant]['productCount']?>"
                />
                <?php
            }
            ?>
        </div>
    </div>
