<?php

use app\modules\catalog\models\Goods;

?>
<div class="content">
    <div class="product-detail-page good compact good-<?=$model->id?>" id="<?=$model->id?>">
        <?php if($model->status): ?>
            <div class="title text-success">Статус: <b>Опубликовано!</b></div>
        <?php else: ?>
            <div class="title text-danger">Статус: <b>Не опубликовано!</b></div>
        <?php endif; ?>
        <div class="row content-text">
            <!--Изображения-->
            <div class="col-md-4 col-xs-4 content-images">
                <div class="image">
                    <a href="<?= Goods::getPath($model->id)?>" ><img src="http://www.esalad.ru<?= Goods::findProductImage($model->id);?>" alt="<?=$model->name?>" class="ad" /></a>
                    <!-- Наклейки -->
                    <div class="stickers stickers__com hidden">
                        <?=$model->bonus ? '<div class="stikers-icon bonus"></div>':''?>
                        <?=$model->discount ? '<div class="stikers-icon discount"></div>':''?>
                        <?=$model->new ? '<div class="stikers-icon news"></div>':''?>
                    </div> <!-- Наклейки -->
                    <?php if($model->count_min > 1):?>
                        <div class="info-img"><?=$model->count_min?>x</div>
                    <?php endif; ?>

                </div>
                <?php
                if(!$model->images){
                }else{?>
                    <div class="images"><?php
                    if(!empty($model->imagesvariants)) {
                        foreach ($model->imagesvariants as $key => $image) {
                            $oneVariation = Goods::getProductVariantsId($image['variation_id']);
                            ?>
                        <div class="item carousel_item" data-tag-id="<?= $oneVariation['tag_id'] ?>">
                            <a href="#" class="no-border">
                                <img src="http://www.esalad.ru<?= $image['img'] ?>"
                                     class="ad  <?= ($key == 0 ? ' open' : '') ?> "
                                     title="<?= $oneVariation['tag_name'] ?>"/>
                            </a>
                            </div><?php
                        }
                    }
                    ?>
                    <div class="clear"></div>
                    </div><?php
                }?>
            </div><!--/Изображения-->

            <?php if(!empty($model->relatedProducts)): ?>
                <div class="goods-items__com hidden_r">
                    <div class="title">Сопутствующие товары</div>
                    <div class="items">
                        <?php foreach($model->relatedProducts as $key=>$value):?>
                            <a class="item" href="#" onclick="return show_modal_compact('/catalog/compact/<?=$value->id;?>',' ');"><img class="ad" src="http://www.esalad.ru/<?= Goods::findProductImage($value->id);?>" title="<?=$value->name?>" alt="<?=$value->name?>" /></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php endif;?>
            <!--Контент товара-->
            <?= \app\components\WProductDetailVariable::widget([
                'model' => $model,
            ]);?>
            <div class="clear"></div>
            <?php if(!empty($model->relatedProducts)): ?>
                <div class="goods-items__com desktop">
                    <div class="title">Сопутствующие товары</div>
                    <div class="items">
                        <?php foreach($model->relatedProducts as $key=>$value):?>
                            <a class="item" href="#" onclick="return show_modal_compact('/catalog/compact/<?=$value->id;?>',' ');"><img class="ad" src="http://www.esalad.ru/<?= Goods::findProductImage($value->id);?>" title="<?=$value->name?>" alt="<?=$value->name?>" /></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php endif;?>
            <div class="description">
                <?php if(isset($model->description)):?><?=$model->description?><?php else:?> <noindex><p>В данный момент мы работаем над описанием товара.</p></noindex><?php endif;?>
                <br>
            </div>
            <div class="subs-string"></div>
            <!--/Контент товара-->
            <div class="clear"></div>
        </div>
    </div>
</div><!--/Content-->
