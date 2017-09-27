<?php

use app\modules\catalog\models\Category;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

//$basket = Yii::$app->basket->getBasket();
$basket = Yii::$app->action->applyActions();
//print \app\modules\common\models\User::findOne(Yii::$app->user->identity->getId())->discount;
?>
    <div class="content basket-index ">
        <!--Корзина-->
        <div id="basket" class="basket-goods">
            <!--Хлебная крошка-->
            <div class="path"><a href="/">Главная</a> / <span>Корзина</span></div><!--/Хлебная крошка-->
            <?php if(!Yii::$app->basket->emptyBasket()):
                $form = ActiveForm::begin([
                    'action' => '/',
                    'options' => [
                        'class'=>'basket-general-form',
                    ],
                ]);?>
                    <input type="hidden" name="basket-id" value="<?=$basket->id?>" />
                    <!--Пошаговая констркуция-->
                    <div class="step step__wid">
                        <div class="step-item">
                            <div class="title">Сейчас в корзине</div>
                            <div class="step-container goods-basket" id="basket-product-block" data-address-list-all='<?=$basket->storeListJson?>'>
                                <?= \app\components\WBasketProductList::widget([
                                    'basket' => $basket,
                                ]);?>
                            </div>
                        </div>
                        <?php if(!Yii::$app->user->isGuest): ?>
                            <div class="step-item">
                                <div class="title">Выберите адрес доставки</div>
                                <div class="step-container" id="delivery-type-and-address-select-block">
                                    <?= \app\components\WDeliverySelect::widget([
                                        'basket' => $basket,
                                        'sort' => 2
                                    ]);?>
                                </div>
                            </div>
                            <div class="step-item date-time-change-block">
                                <div class="title">Дата и время</div>
                                <div class="step-container" id="basket-page-date-time-data">
                                    <?= \app\components\WBasketTimeDelivery::widget([
                                        'basket' => $basket,
                                        'sort' => 3
                                    ]);
                                    ?>
                                </div>
                            </div>
                        <?php endif;?>
                        <div class="step-item">
                            <div class="title">Оплата</div>
                            <div class="step-container">
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

                                <?php if(!Yii::$app->user->isGuest): ?>
                                    <!--Оформит заказ-->
                                    <div class="button_pay button-ajax">
                                        <div
                                            class="button_oran center button__a yMapsActive"
                                            onclick="return shop.windowShow('/ajax-basket/basket-check-data','Проверьте данные вашего заказа','lg',false);"
                                            data-url="/ajax-basket/basket_check_data"
                                            data-title="Проверьте данные"
                                            data-size="max"
                                        >
                                            <div>Оформить</div>
                                        </div>
                                        <div class="load"></div>
<!--                                        <div class="error center">Вот тут ошибка</div>-->
                                    </div> <!--Оформит заказ-->
                                <?php else: ?>
                                    <div class="text" style="text-align: center">Для оформления заказа необходимо <a href="/" onclick="return window_show('login','Вход');">войти</a> или <a href="/" onclick="return window_show('signup','Регистрация');">зарегистрироваться</a></div>
                                <?php endif;?>
                            </div>  <!--/Оформит заказ-->
                        </div>
                    </div><!--/Пошаговая констркуция--><?php
                $form->end();?>
            <?php else:?>
                <div>Ничего не выбрали? В нашем каталоге огромный выбор товаров, посмотрите <a href="/catalog/new/">еще</a></div>
            <?php endif;?>

        </div><!--/Корзина-->
    </div><!--/Content-->
    <div id="map"></div>
    <div id="map1"></div>


<?php

/*
print '<pre>$basketProductsTypes';
print_r($basketProductsTypes);
print '</pre>';
print '<pre>$basketProducts';
print_r($basketProducts);
print '</pre>';
print '<pre>$basketProductsVariant';
print_r($basketProductsVariant);
print '</pre>';
print '<pre>$basketVariants';
print_r($basketVariants);
print '</pre>';
print '<pre>$basketProductsVariantTags';
print_r($basketProductsVariantTags);
print '</pre>';


print '<pre>';
                print_r($basket);
                print '</pre>';
<pre>
<?php print_r(\Yii::$app->session)?>
</pre>

<pre>
<?php print_r(\Yii::$app->session['basket'])?>
</pre>

*/
//md5('9137172874');
/*
print substr(md5('+79137172874'),1,8);
print substr(md5('+79137929000'),1,8);
print '<pre>$basketTest ';
//print_r($basketTest);
print '</pre>';
print '<pre>$basket ';
//print_r(\Yii::$app->session['basket']['goods']);
print '</pre>';
*/




