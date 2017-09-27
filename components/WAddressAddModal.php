<?php

namespace app\components;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Modal;
use app\modules\common\models\ModFunctions;

class WAddressAddModal extends Widget{
    public function run(){?>
        <?php
        Modal::begin([
            'header' => '<h2>'.Yii::t('app','Заполните форму').'</h2>',
            'size' => 'modal-big',
            'id' => 'address-modal',
            'toggleButton' => [
                'tag' => 'a',
                'class' => 'dashed',
                'label' => Yii::t('app','Добавить любой удобный для вас адрес'),
                'clientOptions' => false,
            ]

        ]);?>
        <div class="br"></div>
        <div id="address" class="window">
            <div class="close"></div>
            <div id="poligon"></div>
            <div class="form">
                <span style="position: relative; top: -10px; color: rgb(120, 120, 120);">* <?=\Yii::t('app','Обязательные поля')?></span>
                <?php if(!empty(\Yii::$app->params['mobile'])):?> <div class="links"><a href="/site/map" target="_blank" class="no-border"><?=\Yii::t('app','Карта доставки')?></a></div><?php endif;?>
                <form action="" method="post">
                    <input type="hidden" name="delivery_id" id="delivery_id"  class="delivery" value="" >
                    <input type="hidden" name="city" value="" maxlength="8" autocomplete="off" class="string city" />
                    <input type="hidden" name="street" value="" maxlength="72" autocomplete="off" class="string street" />
                    <input type="hidden" name="house" value="" maxlength="8" autocomplete="off" class="string house" />
                    <input type="hidden" name="district" value="" maxlength="4" autocomplete="off" class="string district" />
                    <div class="item wrapper_adress form-inline">
                        <div class="input">
                            <input placeholder="<?=\Yii::t('app','Адрес* например ул. Мира 56')?>" type="text" name="address" value="" maxlength="80" autocomplete="off" class="string address_point" id="s1" />
                        </div>
                        <div class="maybeArdess"></div>
                    </div>
                    <div class="item">
                        <div class="input">
                            <input placeholder="<?=\Yii::t('app','Квартира')?>" type="text" name="room" value="" maxlength="4" autocomplete="off" class="string room" />
                        </div>
                    </div>
                    <div class="item">
                        <div class="input phone">
                            <span>+7</span>
                            <input placeholder="<?=\Yii::t('app','Телефон')?>" type="text" name="phone" value="<?=ModFunctions::phone(Yii::$app->user->identity->phone)?>" maxlength="10" autocomplete="off" class="string number" />
                        </div>
                    </div>
                    <div class="item">
                        <div class="input">
                            <textarea placeholder="<?=\Yii::t('app','Комментарий')?>" name="comments" class="text"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="form_address" value="true" />
                    <div class="error"></div>
                    <div class="button">
                        <div><?=\Yii::t('app','Сохранить')?></div>
                    </div>
                    <div class="button_load"></div>
                </form>
            </div>
         <?php if(empty(\Yii::$app->params['mobile'])):?>
            <div class="text_min">
                <p><span style="background: #84DD7B; padding: 0px 10px; margin: 0px 5px 0px 0px;"></span><?=\Yii::t('app','Стоимость доставки')?> 250 рублей.</p>
                <p><span style="background: #FFC477; padding: 0px 10px; margin: 0px 5px 0px 0px;"></span><?=\Yii::t('app','Стоимость доставки')?> 350 рублей.</p>
            </div>
            <?php endif;?>
        </div>
        <?php
        Modal::end();?><?php
    }
}
