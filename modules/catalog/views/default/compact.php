<?php

use app\modules\catalog\models\Goods;
use app\modules\common\models\ModFunctions;
use app\modules\shop\models\OrdersItems;
?>

<div class="content">
    <div class="product-detail-page good compact good-<?=$model->id?>" id="<?=$model->id?>">
        <h1 class="name"><?=$model->name?></h1>
        <div class="row content-text">
            <!--Изображения-->
            <div class="content-images">
                <div class="image">
                       <img src="http://www.esalad.ru<?= Goods::findProductImage($model->id);?>" alt="<?=$model->name?>" class="ad" />
                    <!-- Наклейки -->
                    <div class="stickers stickers__com hidden">
                        <?=$model->bonus ? '<div class="stikers-icon bonus"></div>':''?>
                        <?=$model->discount ? '<div class="stikers-icon discount"></div>':''?>
                        <?=$model->new ? '<div class="stikers-icon news"></div>':''?>
                    </div> <!-- Наклейки -->
                    <!-- Наклейки -->
                    <div class="stickers__new">
                        <?php
                        if(!empty($model->stickerLinks)) {
                            foreach ($model->stickerLinks as $stickers): ?>
                                <div class="sticker-image" style="background-image: url('/files/sticker/<?= $stickers->sticker->id ?>.png');"></div>
                            <?php endforeach;
                        }
                        ?>

                    </div> <!-- Наклейки -->
                    <?php if($model->count_min > 1):?>
                        <div class="info-img"><?=$model->count_min?>x</div>
                    <?php endif; ?>
                    <!-- Управление -->
                    <?php if((\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('conflictManager') ||  !\Yii::$app->user->can('GodMode'))): ?>
                    <div class="manager manager___shop">
                        <div class="items">
                            <div class="i edit" title="Редактировать" onclick="return good_edit('<?=$model->id?>');"></div>
                            <div class="i discount<?php if(!empty($model->discount)): ?> disabled<?php endif;?>" title="Акция" onclick="return good_discount('<?=$model->id?>');"></div>
                            <div class="i delete" title="Скрыть" onclick="return good_delete('<?=$model->id?>');"></div>
                            <div class="i position" title="Смена позиций"></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <?php endif; ?><!-- .Управление -->
                </div>
                <?php
                if(!$model->images){
                }else{?>

                    <div class="goods-carousel-min <?= (!empty($model->imagesvariants) && count($model->imagesvariants)> 3 ? '':'open')?>">
                    <div class="button_load"></div>
                    <div class="items">
                        <?php if(!empty($model->imagesvariants)) {
                            foreach ($model->imagesvariants as $key => $image) {
                                $oneVariation = Goods::getProductVariantsId($image['variation_id']);
                                ?>
                            <div class="item" data-tag-id="<?= $oneVariation['tag_id'] ?>">
                              <img src="http://www.esalad.ru<?= $image['img'] ?>"
                                         class="js_carousel <?= ($key == 0 ? ' open' : '') ?> "
                                         title="<?= $oneVariation['tag_name'] ?>"/>

                                </div><?php
                            }
                        }
                        ?>
                    </div>

                    </div>

                    <?php
                }?>
                <div class="clear"></div>
            </div><!--/Изображения-->
            <div class="col-xs-6 block-t">
                <div class="rating">
                    <div class="title-min">Рейтинг:</div>
                    <div class="rating-icon rating-<?=$model->rating?>"></div>
                </div>
                <a class="blue comment" href="#" onclick="return show_modal_compact('/catalog/comments','Оставить отзыв',<?=$model->id?>);">Отзывы <?=(count($model->goodsComments) > 0 ? '('.count($model->goodsComments).')' : '')?></a>
                <?php
                // Количество покупки;
                $count_buy = 0;
                $ordersItems = OrdersItems::find()->where(['status'=>1,'good_id'=> $model->id])->All();
                foreach ($ordersItems as $ordersItem){
                    $count_buy = $count_buy + $ordersItem->count;
                }?>
                <?php if($count_buy > 5): ?>
                    <div class="purchased">Куплен: <?=ModFunctions::numberSize($count_buy)?> раз</div>
                <?php else: ?>
                    <div class="purchased"></div>
                <?php endif; ?>
                <div class="purchased"></div>
            </div><!--/block max-->

            <!--Контент товара-->
            <?= \app\components\WProductDetailVariable::widget([
                'model' => $model,
            ]);?>
            <div class="clear"></div>
            <?php if(!empty($model->relatedProducts)):?>
                <div class="goods-items__com bottom">
                    <div class="title">Сопутствующие товары</div>
                    <div class="items">
                        <?php foreach($model->relatedProducts as $key=>$value):?>
                            <a class="item" href="#" onclick="return show_modal_compact('/catalog/compact',' ','<?=$value->id;?>');"><img class="ad" src="http://www.esalad.ru/<?= Goods::findProductImage($value->id);?>" title="<?=$value->name?>" alt="<?=$value->name?>" /></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php endif;?>
            <div class="title-description">Описание:</div>
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
