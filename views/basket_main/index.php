<?php

//use Yii;
use app\modules\catalog\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

$category = new Category();
?>
<div class="content basket-index ">
    <!--Корзина-->
    <div id="basket" class="basket-goods">
        <!--Хлебная крошка-->
        <div class="path"><a href="/">Главная</a> / <span>Корзина</span></div><!--/Хлебная крошка-->
  <?php if(isset(\Yii::$app->controller->basket['productsShort']) && !empty(\Yii::$app->controller->basket['productsShort'])):?>

              <div class="message">Текст++</div>

            <form action="" method="post">
                <!--Пошаговая констркуция-->
                <div class="step step__wid">
                  <div class="step-item">
                    <div class="title">Сейчас в корзине</div>
                    <div class="step-container goods-basket">
                        <?php  foreach(\Yii::$app->controller->basket['productTypeList'] as $keyType => $productsOfTypes):?>
                            <div class="type_name">Группы++</div>
                            <div class="shop_type">
                                <div class="title_shop"><b> <?= \app\components\WBasketTypeProduct::widget(['typeName' => $typeProducts[$keyType]]);?></b></div>
                            </div>
                         <!--Список товаров-->
                         <div class="goods-basket goods__tile" >
                            <?php
                                $productsOfTypes = array_unique($productsOfTypes);
                                foreach($productsOfTypes as $productId){
                                    $dataProviderProducts  = $category->findProduct(Yii::$app->controller->basket['products'][$productId]['productId']);
                                    $product = $dataProviderProducts->getModels();

                                    print \app\components\WBasketProduct::widget([
                                        'product' => $productId,
                                        'catalogItem' => $product[0],
                                        'variationsAllProductsList' => !empty(Yii::$app->controller->basket['products'][$productId]['productId']) ? $category->findVariations([Yii::$app->controller->basket['products'][$productId]['productId']]) : [],
                                    ]);
                                }
                            ?>
                          </div> <!--Список товаров-->
                        <?php endforeach;?>

                         <div class="button"><div class="button_blue right" onclick="clear_basket();"><div>Очистить все</div></div></div>
                    </div>
                </div>




                {if $user}
                <div class="step-item">
                    <div class="title">Выберите адрес доставки</div>
                    <div class="step-container">
                        <!--Адресс доставка-->
                        <div class="my-deliveries">
                            <div class="address"><a onclick="return window_show('address',true,true);" class="dashed" href="/basket/">Добавить любой удобный для вас адрес</a></div>
                            {assign var="selected" value=0}
                            {foreach from=$basket.delivery.items item=item name=foo}
                            <div class="radio item"><label class="radio__label my-deliveries-form" ><input data-address-clear="{$item.address_light}"  type="radio" class="" value="{$item.id}:{$item.value}" name="delivery_id" {if $item.id == $basket.delivery.delivery_id and $item.value == $basket.delivery.address_id and $type.type_id != 1008} checked{/if} {if $type.type_id == 1008 and !$smarty.foreach.foo.first}disabled{/if} /><span class="radio-checked">{$item.address}  {if $item.phone and $item.phone != $user.phone} ({$item.phone}){/if}</span></label></div>
                            {assign var="selected" value=1}
                            {/foreach}
                        </div><!--/Адресс доставка-->
                    </div>
                </div>
                <div class="step-item">
                    <div class="title">Дата и время</div>
                    <div class="step-container">
                        {if $type.times}
                        <!--Время доставки-->
                        <div class="times" id="times">
                            {foreach from=$basket.goods item=type}
                            {if $type.type_id == 1008}
                            <input type="hidden" name="times[{$type.type_id}]" value="{$type.time.key}" />
                            <div class="text">Дата и время доставки для <strong>{$type.type_name}</strong>&nbsp; При поступлении товара на указанный Вами номер придет СМС-уведомление. <br />Заказ можно получить по адресу ул. Советская 64 1 этаж.</div>
                            {else}
                            <div class="time-item" rel="{$type.type_id}">
                                <div class="description-normal">Дата и время доставки для <b>{$type.type_name}</b></div>
                                <input type="hidden" name="times[{$type.type_id}]" value="{$type.time.key}" />
                                <input class="json-data-block" type="hidden" data-delivery-json='{$type.timesJson}' />
                                <!--Дата-->
                                <div class="form_block block_inline form-date-flag-block date">
                                    <div class="select__form min time_select">
                                        <div class="container-select"><div class="option-text">Выберите дату доставки</div><div class="selectbox"></div></div>
                                        <div class="row">
                                            {foreach name="times" from=$type.times item=times key=day}
                                            <div class="option">{$day|date_format}</div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div> <!--./Дата-->
                                <!--Время-->
                                <div class="form_block block_inline time">
                                    <div class="select__form min time_select">
                                        <div class="container-select disabled"><div class="option-text">Выберите время доставки</div><div class="selectbox"></div></div>
                                        <div class="row">
                                            {foreach from=$times item=time_value key=time_key}
                                            <div class="option">{$time_value}</div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div><!--/Время-->
                            </div>
                            {/if}
                            {/foreach}
                        </div><!--./Время доставки-->
                        {/if}
                    </div>
                </div>
                {/if}
                <div class="step-item">
                    <div class="title">Оплата</div>
                    <div class="step-container">
                        <!--Оплата контент-->
                        <div class="payments">
                            <!--Оплата-->
                            {if $user}{include file="templates/html/_payments.html" user_money=true}{/if}
                            <!--/Оплата-->
                            <!--Промокод-->
                            <div class="promo">
                                <div class="description-min">* Цена с промо-кодом. Промо-код спрашивайте у администраторов спортзала.</a></div>
                                <div class="form___gl min">
                                    <div class="form-inline has-feedback {if $promo}has-success{/if}">
                                        <input type="text" name="promo" value="{$promo}" maxlength="32" onkeydown="if(event.keyCode==13)return false;" class="form-control text-center" placeholder="Введите промо-код" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Введите промо-код')" >
                                        <span class="glyphicon form-control-feedback {if $promo} glyphicon-ok{/if}"></span>
                                        <label class="control-label">{if $promo}Промо-код активирован{/if}</label>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <!--Комментарий-->
                                <div class="comments form___gl form-group">
                                    <div class="title-comments">Комментарий</div>
                                    <textarea name="comments" class="form-control" placeholder="Текст" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Текст')" >{$basket.comments}</textarea>
                                </div><!--./Комментарий-->
                            </div> <!--./Промокод-->
                            <!-- Оформить и Результат -->
                            <div class="details">
                                <div class="sum">
                                    <div class="i"><span>Сумма:</span> {$basket.money|money}</div>
                                    <div class="i"><span>Скидка:</span> {if $basket.discount > 0}-{/if}{$basket.discount|money}</div>
                                    <div class="i"><span>Бонусы:</span> {if $basket.bonus > 0}-{/if}{$basket.bonus|money}</div>
                                    <div class="i"><span>Доставка:</span> {$basket.delivery.price|money}</div>
                                    <div class="border-b"></div>
                                    <div class="i total"><span>Итого:</span> <span class="total-money">{$basket.money_sum|money}</span> </div>
                                </div>
                                <div class="clear"></div>
                            </div> <!-- /Оформить и Результат -->
                        </div><!--.Оплата контент-->
                        {if $user}
                        <!--Оформит заказ-->
                        <div class="button_pay button-ajax">
                            <div class="button_oran center button__a yMapsActive {if $type.type_id != 1008}hidden{/if}" onclick="return basket_check_data();"><div>Оформить</div></div>
                            <div class="load"></div>
                            <div class="error center">{$basket_error}</div>
                        </div>
                        {else}
                        <div class="text" style="text-align: center">Для оформления заказа необходимо <a href="/" onclick="return window_show('registration');">войти</a> или <a href="/" onclick="return window_show('registration');">зарегистрироваться</a></div>
                        {/if}
                    </div>  <!--/Оформит заказ-->
                </div>
                </div><!--/Пошаговая констркуция-->
            </form>
    <?php else:?>
      <div>Ничего не выбрали? В нашем каталоге огромный выбор товаров, посмотрите <a href="/catalog/new/">еще</a></div>
    <?php endif;?>

    </div><!--/Корзина-->
</div><!--/Content-->



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




