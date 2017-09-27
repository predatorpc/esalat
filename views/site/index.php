<?php

use app\models\Menu;
use yii\db\ActiveDataProvider;
use app\modules\pages\models\Pages;

//use yii\widgets\Menu;

// Описание магазин;
$shopMain = Pages::find()->where(['id'=>1001,'status'=>1])->asArray()->one();
/* @var $this yii\web\View */

$this->title = (!empty($shopMain['seo_title']) && isset($shopMain['seo_title']) ?  $shopMain['seo_title'] : $shopMain['name']);
// SEO;
$this->registerMetaTag(['name' => 'description', 'content' => (!empty($shopMain['seo_description']) && isset($shopMain['seo_description']) ?  $shopMain['seo_description'] : '')]);
$this->registerMetaTag(['name' => 'keywords', 'content' => (!empty($shopMain['seo_keywords']) && isset($shopMain['seo_keywords']) ?  $shopMain['seo_keywords'] : '')]);

// Мастер помощник;
if(false && !Yii::$app->user->isGuest && ceil((strtotime(Yii::$app->user->identity->registration) - time()) / 86400 + 7) > 0 && empty($_COOKIE['master_help'])) {
    $this->registerJsFile('/scripts/help/enjoyhint.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('/js/help.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerCssFile('/scripts/help/enjoyhint.css');
}

?>
<!--class="col-lg-9 right-content"-->
<div>
    <!--Слайд-->
    <?php if(false): ?>
    <div class="slides">
        <div class="items">
          <?php if(Yii::$app->params['en']): ?>
                <div class="item "><a href="#"><img src="http://www.esalad.ru/files/slides/10000086_en.jpg" alt=""  class="ad"/></a></div>
                <div class="item "><a href="#"><img src="http://www.esalad.ru/files/slides/10000087_en.jpg" alt=""  class="ad"/></a></div>
                <div class="item "><a href="#"><img src="http://www.esalad.ru/files/slides/10000088_en.jpg" alt=""  class="ad"/></a></div>
          <?php else:  ?>
             <?php foreach($slider as $item): ?>
                <div class="item "><a href="<?=$item['url']?>"><img src="http://www.esalad.ru/files/slides/<?=$item['id']?>.jpg" alt="" class="ad"/></a></div>
             <?php endforeach;?>
          <?php endif;  ?>
        </div>
    </div><!--/Слайд-->
    <?php endif;  ?>

    <?php if(false): ?>
        <!--Баннеры категория-->
        <div class="posters hidden_r">
            <div class="items">
                <?=\app\components\WBanners::widget()?>
            </div>
        </div><!--./Баннеры категория-->
    <?php endif;  ?>

     <div class="mod___goods_list popular goods-top">
         <?=\app\components\WCatalogProductItem::widget()?>
     </div>
    <div class="clear"></div>
</div>

<div class="clear"></div>


<?php if(false): ?>
    <!--Модуль .goods-top(Статик),.goods-carousel(Карусель)-->
    <div class="mod___goods_list goods-carousel">
       <h2 class="title"><?=\Yii::t('app', 'Новинки')?> </h2>
        <?=\app\components\WNewProductItem::widget()?>
        <div class="clear"></div>
    </div><!--/Модуль-->

    <!--Инфо -->
    <div class="main-info">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="/static/page/rules#guarantee" class="no-border">
                    <div class="item">
                        <div class="icon-advantage icon i-4"></div>
                        <b><?=\Yii::t('app', 'Гарантия качества')?></b>
                        <p></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="/static/page/rules#shipping" class="no-border">
                    <div class="item">
                        <div class="icon-advantage icon i-3"></div>
                        <b><?=\Yii::t('app', 'Доставка')?></b>
                        <p><?=\Yii::t('app', 'по Новосибирску и пригороду')?></p>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div style="cursor: pointer;" class="item"  onclick="return window_show('site/delivery-address','Карта пунктов выдачи заказов','mid',true);">
                    <div class="icon-advantage icon i-2"></div>
                    <b><?=\Yii::t('app', 'Пункты выдачи')?></b>
                    <p><?=\Yii::t('app', '7 пунктов по линии метро')?></p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="/static/page/rules#return" class="no-border">
                <div class="item b-no">
                    <div class="icon-advantage icon"></div>
                    <b><?=\Yii::t('app', 'Возврат и обмен товара')?></b>
                </div>
                </a>
            </div>
        </div>
        <div class="clear"></div>
    </div>  <!--Инфо -->
<?php endif;  ?>