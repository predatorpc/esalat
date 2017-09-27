<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\WWishlistProductItemOne;
use \app\modules\common\models\ModFunctions;
use yii\web\CatalogAsset;

$this->registerCssFile('@web/css/catalog.css');


$this->title = Yii::t('app','Список желаний');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content">

    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-3">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <h1 class="title my"><?= $this->title; ?></h1>
            <div class="desire-list goods">
                <?php if(!empty($products)): ?>
                   <div id="list-wrapper" class="product-list js-product-list mod___goods_list goods-list">
                    <div id='sort' class='items' style="overflow: hidden;">
                         <?php
                           foreach ($products as  $product){
                               if(!empty($product->product)) {
                                   print WWishlistProductItemOne::widget([
                                       'model' => $product->product,
                                       'user' => Yii::$app->user->can('categoryManager'),
                                       //'categoryCurrent' => 1001,
                                   ]);
                               }
                      }
                          ?>
                    </div>
                    <div class='clear'></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
