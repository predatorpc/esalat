<?php

use app\models\Menu;
use yii\db\ActiveDataProvider;

//use yii\widgets\Menu;


/* @var $this yii\web\View */

$this->title = 'Esalad - Экстрим шоп в Новосибирске интернет-магазин';

?>
<?php if ($this->beginCache('siteIndex', ['duration' => 3600*24])) : ?>

<!--Слайд-->
<div class="slides">
    <div class="items">
        <?php foreach($slider as $item): ?>
            <div class="item "><a href="<?=$item['url']?>"><img src="http://www.Esalad.ru/files/slides/<?=$item['id']?>.jpg" alt=""  class="ad"/></a></div>
        <?php endforeach;?>
    </div>
</div><!--/Слайд-->

<!--Баннеры категория-->
<div class="posters">
    <div class="items">
        <div class="item poster-8"><a href="/catalog/foods" class="no-border"><img src="/files/posters/8.png" alt=""  class="ad"/></a></div>
        <div class="item poster-2"><a href="/catalog/pets" class="no-border"><img src="/files/posters/2.png" alt=""  class="ad"/></a></div>
        <div class="item poster-1"><a href="/catalog/sportivnoe-pitanie" class="no-border"><img src="/files/posters/1.png" alt=""  class="ad"/></a></div>
        <div class="item poster-6"><a href="/catalog/recreation" class="no-border"><img src="/files/posters/6.png" alt=""  class="ad"/></a></div>
        <div class="item poster-7"><a href="/catalog/tovary-dlya-doma/bytovaya-himia" class="no-border"><img src="/files/posters/7.png" alt=""  class="ad"/></a></div>
    </div>
</div><!--./Баннеры категория-->

<!--Модуль .goods-top(Статик),.goods-carousel(Карусель)-->
<div class="mod___goods_list goods-carousel">
    <a href="/catalog/new/" class="black"><h2 class="title">Новинки</h2></a>
    <?=\app\components\WNewProductItem::widget()?>
    <div class="clear"></div>
</div><!--/Модуль-->
<!--Инфо -->
<div class="main-info">
    <div class="row">
        <div class="col-md-3 col-xs-3">
            <div class="item color__orang-b ">
                <b>Бонусная программа</b>
                <p>Кэшбэк 5% и возможность заработать с покупок друзей</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-3">
            <div class="item color__blue-b">
                <b>Пункты выдачи</b>
                <p>7 пунктов по линии метро</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-3">
            <div class="item color__orang-red-b">
                <b>Доставка</b>
                <p>по Новосибирску и пригороду</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-3">
            <div class="item color__mint-b">
                <b>Гарантия качества</b>
                <p></p>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>  <!--Инфо -->
    <?php $this->endCache() ?>
<?php endif ?>
