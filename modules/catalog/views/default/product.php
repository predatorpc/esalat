<?php

use app\modules\catalog\models\Goods;
use\app\modules\common\models\ModFunctions;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Modal;
$this->title = $model->name;
// SEO;
$this->registerMetaTag(['name' => 'description', 'content' => (!empty($model->seo_description) ? $model->seo_description : $model->description)]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords]);

$url = '/' . Yii::$app->params['catalogPath'] . '/';
$breadcrumbsUrl = '';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Каталог'), 'url' => $url,'template' => "{link}/\n"];
if(!empty($breadcrumbsCatalog)){
    foreach($breadcrumbsCatalog as $item){
        if($item['title'] != $this->title){
            $url .= $item['alias'] . '/';
            $this->params['breadcrumbs'][] = ['label' => $item['title'], 'url' => $url,'template' => "{link}/\n"];
        }
        else{
            $url .= $item['alias'] . '/';
            $breadcrumbsUrl = $url;
        }
    }
    $this->params['breadcrumbs'][] = ['label' => $this->title,'template' => "{link} \n",];
    reset($breadcrumbsCatalog);
}

if($model->id == 10404587) {
        // Акция формула подсчета индикатор; current_accumulation
       $stock = \Yii::$app->action->getActiveAccum();
    if(empty($stock[0]['spent']) && $stock[0]['max_accumulation'] == $stock[0]['current_accumulation']) {
        // Модальное окно;
        Modal::begin([
            'header' => '<h4 class="modal-title text-center" id="myModalLabel">ПОЗДРАВЛЯЕМ!</h4>',
            'size' => 'modal-min',
            'id' => 'ak-modal',
        ]);
        echo '<p style="text-align: center; font-size: 15px;line-height:22px;">Уважаемый клиент!  При добавлении данного товара в корзину Вам будет предоставлена на него скидка 100%! Спасибо, что выбрали Extreme Shop.</p>';
        //
        Modal::end();
    }
}
?>

<div class="content">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['options' => ['class' => 'path'],'tag' => 'div','homeLink' => ['label' => Yii::t('app','Главная'), 'url' => '/','template' => "{link}/ \n"],'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]);?>
    <!--Хлебная крошка-->
    <div class="product-detail-page good good-<?=$model->id?>" id="<?=$model->id?>">

    <section itemscope itemtype="https://schema.org/Product">
        <h1 class="name mobile  hidden_r"><?=$model->name?></h1>
        <div class="row">
            <!--Изображения https://www.extremeshop.ru-->
            <div class="col-md-4 col-xs-4 content-images">
                <div class="image">
                <?php if(!empty(Goods::findProductImage($model->id,'max'))){ ?>
                    <a href="http://www.esalad.ru<?= Goods::findProductImage($model->id,'max');?>" class="cloud-zoom"><img src="http://www.esalad.ru<?= Goods::findProductImage($model->id);?>" alt="<?=$model->name?>" class="ad" /></a>
                <?php }elseif(!empty(Goods::findProductImage($model->id))){ ?>
                   <img src="http://www.esalad.ru<?= Goods::findProductImage($model->id);?>" alt="<?=$model->name?>"  class="ad" />
                <?php } ?>
                    <!-- Наклейки -->
                    <div class="stickers__new">
                        <?php
                        if(!empty($model->stickerLinks)) {
                            foreach ($model->stickerLinks as $stickers): ?>
                                <div class="sticker-image"
                                     style="background-image: url('/files/sticker/<?= $stickers->sticker->id ?>.png');"></div>
                            <?php endforeach;
                        }
                        ?>
                    </div> <!-- Наклейки -->
                    <!-- Управление -->
                    <?php if((\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('conflictManager') || \Yii::$app->user->can('GodMode'))): ?>
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

                    <?php if($model->count_min > 1):?>
                        <div class="info-img" style="right: 0;top:5px;"><?=$model->count_min?>x</div>
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
                                    <img src="http://www.esalad.ru<?= $image['img'] ?>" class="ad  <?= ($key == 0 ? ' open' : '') ?> "
                                         title="<?= $oneVariation['tag_name'] ?>"/>
                                </a>
                                </div><?php
                            }
                        }?>
                        <div class="clear"></div>
                    </div><?php
                }?>
            </div><!--/Изображения-->
            <!--Контент товара-->
            <?= \app\components\WProductDetailVariable::widget([
                'model' => $model,
            ]);?>
            <!--/Контент товара-->
            <div class="col-md-12 col-xs-12 content-info module__tab">
                <div class="nav nav-tabs">
                    <div class="item active"><span data-target="#description" data-toggle="tab"><?=\Yii::t('app','Описание')?> </span></div>
                    <noindex><div class="item "><span data-target="#comment" data-toggle="tab"><?=\Yii::t('app','Отзывы')?> <?= !empty($comments) ?  '('.count($comments).')' : ''?></span></div></noindex>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade text in active variation-description continue-text" id="description" itemprop="description">
                       <?php if(isset($model->description)):?><?=$model->description?><?php else:?> <noindex><p><?=\Yii::t('app','В данный момент мы работаем над описанием товара.')?> </p></noindex><?php endif;?>
                    </div>
                    <div class="tab-pane fade text" id="comment">
                        <!--Отзывы-->
                        <div class="comments module___comments ">
                            <?php if(!empty($comments)): ?>
                              <?php foreach($comments as $key=>$item): ?>
                                <div class="item">
                                    <div class="row">
                                        <div class="profile col-md-2 col-xs-2">
                                            <div class="rating"><div class="rating-icon rating-<?=$item->rating?>"></div></div>
                                            <div class="name"><?=$item->name ?></div>
                                            <div class="date"><?= app\modules\common\models\ModFunctions::date_format($item->date)?> г. </div>
                                        </div>
                                        <div class="text col-md-9 col-xs-9"><?=$item->text?></div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            <?php endforeach;?>
                            <?php else: ?>
                               <div class="item"> <div class="text"><?=\Yii::t('app','Нет отзывов')?></div></div>
                            <?php endif;?>
                            <div class="more hidden"><a href="#" class="icon-more">Еще 10 Отзывов</a></div>
                        </div><!--/Отзывы-->
                        <?php if(!Yii::$app->user->isGuest):?>
                            <!--Форма отзыва-->
                           <div class="form___gl comments_form">
                               <div class="alert alert-danger hidden_r"></div>
                               <div class="alert alert-success hidden_r"></div>
                            <form method="post" role="form" class="comments-form">
                                <div class="form-group">
                                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                                    <!--рейтинг-->
                                    <div class="stars__com">
                                        <input type="radio" name="rating" value="5 " id="rating-5" class="star-icon rating-5"/>
                                        <label class="star-icon rating-5 " for="rating-5"></label>
                                        <input type="radio" name="rating" value="4" id="rating-4" class="star-icon rating-4"/>
                                        <label class="star-icon rating-4" for="rating-4"></label>
                                        <input type="radio" name="rating" value="3" id="rating-3" class="star-icon rating-3"/>
                                        <label class="star-icon rating-3" for="rating-3"></label>
                                        <input type="radio" name="rating" value="2" id="rating-2" class="star-icon rating-2"/>
                                        <label class="star-icon rating-2" for="rating-2"></label>
                                        <input type="radio" name="rating" value="1" id="rating-1" class="star-icon rating-1"/>
                                        <label class="star-icon rating-1" for="rating-1"></label>
                                        <div class="clear"></div>
                                        <div class="name"><?=\Yii::t('app','Оценка')?></div>
                                    </div><!--рейтинг-->
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" value="" name="name" placeholder="<?=\Yii::t('app','Ваше имя')?>" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','<?=\Yii::t('app','Ваше имя')?>')" >
                                    <input type="hidden" class="form-control" value="true" name="comments_form"/>
                                    <input type="hidden" class="form-control" value="<?=$model->id?>" name="good_id"/>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" rows="3" name="comments" placeholder="<?=\Yii::t('app','Комментарий')?>" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','<?=\Yii::t('app','Комментарий')?>')" ></textarea>
                                </div>
                                <div class="form-group button-ajax">
                                    <button type="submit" class="button_oran center button__a" onclick="form_action_json('comments_form'); return false;"><?=\Yii::t('app','Отправить')?></button>
                                    <div class="load"></div>
                                </div>
                            </form>

                        </div>
                        <?php else:?>
                          <div class="text hidden">Необходимо <a href="/" onclick="return window_show('login','Вход');">войти</a> или <a href="/" onclick="return window_show('signup','Регистрация');">зарегистрироваться</a></div>
                        <?php endif;?>
                        </div>
                        <!--/Форма отзыва-->
                </div>
            </div>
            <div class="info col-md-3 col-xs-3 hidden">
                <div class="item">
                    <div class="title">Как оплатить</div>
                    <div class="text">Мы принимаем к оплате банковские карты</div>
                    <img alt="" src="/images/payments.png"/>
                </div>
                <div class="item">
                    <div class="title">Доставка</div>
                    <div class="text">Доставим в течение 1-3 дней с момента заказа или получение в любом из <a href="#" onclick="return window_show('site/delivery-address','Карта пунктов выдачи заказов','mid',true);"> 7 залов</a> ExtremeFitness</div>
                </div>
                <div class="item">
                    <div class="title">Гарантия качества</div>
                    <div class="text">Только подлинные товары известных брендов</div>
                </div>

                <div class="item">
                    <div class="title">Возврат</div>
                    <div class="text">В течение 14 дней со дня покупки</div>
                </div>

            </div>
            <div class="clear"></div>
        </div>
    </section>
        <!--Модуль-->

        <div class="mod___goods_list goods-carousel hidden">
            <h2 class="title">Другие товары</h2>
             +
            <div class="clear"></div>
        </div><!--/Модуль-->

    </div>
</div><!--/Content-->