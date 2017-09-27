<?php
use app\modules\catalog\models\Goods;
$this->title = 'Esalad - Экстрим шоп в Новосибирске интернет-магазин';
?>

<!--Слайд-->
<div class="m-slides">
    <div class="items">
        <?php foreach($slider as $item): ?>
            <div class="item "><a href="<?=$item['url']?>"><img src="http://www.Esalad.ru/files/slides/<?=$item['id']?>.jpg" alt=""  class="ad"/></a></div>
        <?php endforeach;?>
    </div>
</div><!--/Слайд-->
<div class="clear"></div>
<!--Главная Категория-->
<div class="main-category">
    <div class="items">
        <?php foreach($main_category as $key => $category): ?>
            <div class="title-main">
                <h2><a href="/catalog/<?=$category['alias']?>/" class="title-main no-border"><?=$category['title']?></a> <a href="#" class="menu-all hidden" >Показать все</a></h2>
            </div>
            <?php if(isset($category['groups'])):?>
                <?php foreach($category['groups'] as $key => $subcat): ?>
                    <div class="container-item" rel="<?=$subcat['id']?>">
                        <div class="item">
                            <div class="images"><a href="/catalog/<?=$category['alias']?>/<?=$subcat['alias']?>"><img src="http://www.Esalad.ru<?=Goods::findProductImage($subcat['image']['good_id']) ?>" alt="" class="ad"></a></div>
                            <div class="title"><a href="/catalog/<?=$category['alias']?>/<?=$subcat['alias']?>" class="white top"><?=$subcat['title']?></a></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="clear"></div>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="clear"></div>
    </div>
</div><!--Главная Категория-->

<!--Модуль .goods-top(Статик),.goods-carousel(Карусель)-->
<div class="mod___goods_list goods-carousel goods_list_m">
    <a href="/catalog/new/" class="black"><h2 class="title"><h2 class="title">Новинки</h2></a>
        <?=\app\components\WNewProductItem::widget()?>
    <div class="clear"></div>
</div><!--/Модуль-->

