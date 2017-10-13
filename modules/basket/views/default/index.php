<?php

use app\modules\catalog\models\Category;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Корзина');
$this->params['breadcrumbs'][] = $this->title;

//$basket = Yii::$app->controller->basket;
$basket = Yii::$app->action->applyActions();

//$o = \app\modules\shop\models\Orders::findOne(10054030);
//\app\modules\common\models\Zloradnij::print_arr($o->ordersItems[0]->shops_stores->shop_id);
?>
    <div class="content basket-index ">
        <div id="map3" style="display: none;"></div>
        <!--Корзина-->
        <div id="basket" class="basket-goods">
            <!--Хлебная крошка-->
            <div class="path"><a href="/"><?=\Yii::t('app','Главная');?></a> / <span><?=\Yii::t('app','Корзина')?></span></div><!--/Хлебная крошка--><?php

            if(!empty($basket->products)):
                $form = ActiveForm::begin([
                    'action' => '/',
                    'options' => [
                        'class'=>'basket-general-form',
                    ],
                ]);?>
                <div class="hidden" id="basket-find-store-block">
                    <?= \app\widgets\basket\WBasketFindStore::widget([
                        'basket' => $basket,
                    ]);?>
                </div>

                <input class="basket-id-param-input" type="hidden" name="basket-id" value="<?=$basket->id?>" />
                <!--Пошаговая конструкция-->
                <div class="step step__wid">
                    <div class="step-item">
                        <div class="title">1. <?=\Yii::t('app','Сейчас в корзине');?></div>
                        <div class="step-container goods-basket open" id="basket-product-block">
                            <?= \app\components\WBasketProductList::widget([
                                'basket' => $basket,
                            ]);?>
                        </div>
                    </div>
                    <?php if(!Yii::$app->user->isGuest): ?>
                        <div class="step-item">
                            <div class="title">2. <?=\Yii::t('app','Выберите адрес доставки');?> </div>
                            <div class="step-container open" id="delivery-type-and-address-select-block">
                                <?= \app\components\WDeliverySelect::widget([
                                    'basket' => $basket,
                                    'sort' => 2
                                ]);?>

                            </div>
                        </div>
                        <div class="step-item date-time-change-block">
                            <div class="title">3. <?=\Yii::t('app','Дата и время');?></div>
                            <div class="step-container <?=(!empty($basket->time_list) || !empty($basket->delivery_id)  ? 'open' : '')?>" id="basket-page-date-time-data">
                                <?= \app\components\WBasketTimeDelivery::widget([
                                    'basket' => $basket,
                                    'sort' => 3
                                ]);
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(!Yii::$app->user->isGuest): ?>
                    <div class="step-item">
                        <div class="title">4. <?=\Yii::t('app','Оплата');?></div>
                        <div class="step-container  <?=(!empty($basket->time_list) ? 'open' : '')?>" id="pay-order">
                            <!--Оплата контент-->
                            <div class="payments">
                                <!--Оплата-->
                                <div id="basket-page-payments" class="my-payments">
                                    <?php if(!Yii::$app->user->isGuest): ?>
                                        <!--Оплата-->
                                        <?= \app\components\WPaymentSelect::widget([
                                            'basket' => $basket,
                                        ])?>
                                    <?php endif;?>
                                </div>
                                <!--/Оплата-->
                                <!--Промокод-->
                                <div  id="basket-page-promo-code" class="promo">
                                    <div class="promo-code-input-block">
                                        <?= \app\components\WBasketPromo::widget(['promo' => !empty($basket->promo_code_id) ? $basket->promo_code_id : '','message' => ''])?>
                                    </div>
                                    <?= \app\components\WBasketComment::widget(['comment' => ''])?>
                                </div> <!--./Промокод-->

                                <!-- Оформить и Результат -->
                                <div class="position-rev">
                                    <div id="basket-page-result-data" class="details">
                                        <?= \app\components\WBasketResult::widget([
                                            'basket' => $basket,
                                        ])?>
                                        <div class="clear"></div>
                                    </div> <!-- /Оформить и Результат -->
                                </div>
                            </div><!--.Оплата контент-->
                        </div>  <!--/Оформит заказ-->
                    </div>
                    <?php endif;?>
                    <?php if(false): ?>
                        <?php if(!Yii::$app->user->isGuest): ?>
                        <!--Оформит заказ-->
                        <div class="button_pay button-ajax">
                            <div
                                class="button_oran center button__a yMapsActive"
                                onclick="return shop.windowShow('/ajax-basket/basket-check-data','<?=\Yii::t('app','Проверьте данные вашего заказа');?>','mid',false);"
                                data-url="/ajax-basket/basket_check_data"
                                data-title="Проверьте данные"
                                data-size="max"
                            >
                                <div class="testOrder"><?=\Yii::t('app','Купить');?></div>
                            </div>
                            <div class="load"></div>
                        </div> <!--Оформит заказ-->
                    <?php else: ?>
                        <!--Оформит заказ-->
                        <div class="button_pay button-ajax">
                            <div class="button_oran center button__a" onclick="return window_show('login','<?=\Yii::t('app','Вход');?>');">
                                <div><?=\Yii::t('app','Оформить');?></div>
                            </div>
                            <div class="load"></div>
                        </div> <!--Оформит заказ-->
                        <div class="text hidden" style="text-align: center">
                            <?php if(Yii::$app->params['en']): ?>
                                To place an order you need to <a href="/" onclick="return window_show('login','Login');">login</a> or <a href="/" onclick="return window_show('signup','Sign up');">register</a>
                            <?php else:  ?>
                                Для оформления заказа необходимо <a href="/" onclick="return window_show('login','Вход');">войти</a> или <a href="/" onclick="return window_show('signup','Регистрация');">зарегистрироваться</a>
                            <?php endif;  ?>
                        </div>
                    <?php endif;?>
                    <?php endif; ?>
                </div><!--/Пошаговая констркуция--><?php
                $form->end();?>
            <?php else:?>
                    <div><?=\Yii::t('app','Ничего не выбрали? В нашем каталоге огромный выбор товаров, посмотрите');?> <a href="/catalog/new/"><?=\Yii::t('app','еще');?></a></div>
            <?php endif;?>

        </div><!--/Корзина-->
    </div><!--/Content-->
