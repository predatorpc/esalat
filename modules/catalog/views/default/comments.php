<?php
use app\modules\catalog\models\Goods;
/**
 * Created by PhpStorm.
 * User: mono-pc
 * Date: 13.04.2017
 * Time: 11:22
 */

?>
<div class="product-detail-page good compact">

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
                        <input type="hidden" class="form-control" value="<?=$good_id?>" name="good_id"/>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="3" name="comments" placeholder="<?=\Yii::t('app','Комментарий')?>" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','<?=\Yii::t('app','Комментарий')?>')" ></textarea>
                    </div>
                    <div class="form-group button-ajax">
                        <button type="submit" class="button_oran center button__a" onclick="form_action_json('comments_form','<?=Goods::getPath($good_id)?>'); return false;"><?=\Yii::t('app','Отправить')?></button>
                        <div class="load"></div>
                    </div>
                </form>

            </div>
        <?php else:?>
            <div class="text hidden">Необходимо <a href="/" onclick="return window_show('login','Вход');">войти</a> или <a href="/" onclick="return window_show('signup','Регистрация');">зарегистрироваться</a></div>
        <?php endif;?>
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
        </div><!--/Отзывы-->
</div>
