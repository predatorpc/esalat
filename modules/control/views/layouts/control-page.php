<?php

/* @var $this \yii\web\View */
/* @var $content string */
use app\assets\AppAsset;
use app\modules\common\models\ModFunctions;
use yii\bootstrap\Modal;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<?php
// Загрузка Меню;
$pagesMenus = \app\modules\pages\models\Pages::getPagesMenu();
// Загрузка параметров;
$pagesOptions = \app\modules\pages\models\PagesOptions::pagesOptions();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="rus" lang="rus">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>

<?php
// Модальное окно;
Modal::begin([
    'header' => '<h4 class="modal-title" id="myModalLabel"></h4>',
    'size' => 'modal-min',
    'id' => 'windows',
]);
//
Modal::end();
?>
<div id="to-top"></div>
<div id="br-show"></div>
<div id="loadAjax"><div class="loader"></div></div>

<!--Главная container-->
<div class="container">
    <!--Шапка-->
    <div id="header">
        <div class="top">
            <div class="br-top"></div>
            <div class="row">
                <div class="col-md-2 col-xs-2">
                    <div class="city city-icon"><a href="#" class="white" onclick="return window_show('city');">Новосибирск</a></div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div class="item">
                        <a href="/static/page/rules#how-to-order" class="white item-header">Оформление заказа</a>
                        <a href="/static/page/rules#payment" class="white item-header">Оплата</a>
                        <a href="/static/page/rules#shipping" class="white item-header">Доставка</a>
                        <a href="/static/page/rules#return" class="white item-header">Возврат</a>
                    </div>
                </div>
                <div class="col-md-4 col-xs-4">
                    <div class="user">
                        <?php if(!Yii::$app->user->isGuest):?>
                            <div class="user-profile">
                                <div class="money"><span class="bonus" rel="popover" data-placement="bottom" data-content="Бонусный баланс. Потратить бонусные деньги вы можете в магазине Esalad на товары со значком.β"><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?></span> / <span class="money" rel="popover" data-placement="bottom" data-content="Баланс интернет-магазина Esalad. Совершайте покупки в интернет-магазине Esalad с удовольствием."><?=ModFunctions::money(Yii::$app->user->identity->money)?></span></div>
                               <span class="user-container"><a href="#" class="user user-icon white" data-toggle="dropdown"  onclick="return false;"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></a>
                                   <div class="box-container">
                                       <!-- Меню ЛК-->
                                       <?= \app\components\WMyMenu::widget()?>
                                       <!-- Меню ЛК-->
                                   </div>
                               </span> / <a href="/site/logout" class="out white">Выйти</a>
                            </div>
                        <?php else: ?>
                            <a href="#" class="user user-icon white" onclick="return window_show('login','Вход');">Вход</a> / <a href="#" class="user reg white" onclick="return window_show('signup','Регистрация');">Регистрация</a>  <!--/Войти или авторизоваться-->
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="bottom">
            <div class="row">
                <div class="col-md-3 col-xs-3">
                    <div class="logo"><a href="/"><img src="/images/logo.png" alt=""></a></div>
                </div>
                <div class="col-md-7 col-xs-7">
                    <!--Поиск-->
                    <div class="search">
                        <form action="/search/index" method="post" >
                            <div class="input">
                                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                                <input type="text" name="search" value="" maxlength="64" autocomplete="off" placeholder="Введите товар, категорию или бренд" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Введите товар, категорию или бренд')" />
                            </div>
                            <div class="button" onclick="$(this).parents('form').submit();"></div>
                        </form>
                    </div> <!--/Поиск-->
                    <div class="info-header">
                        <span class="phone">8 383 349-92-09</span><span class="time"><?=$pagesOptions['time']?></span>
                    </div>
                </div>
                <div class="col-md-2 col-xs-2 small-basket-block">
                    <?=\app\components\WBasketSmall::widget()?>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div><!--/Шапка--><!--/Шапка-->

    <!--Меню топ-->
    <div id="menu-top">
        <div class="bg"></div>
        <?= \app\components\WGeneralCatalogMenu::widget()?>
    </div><!--/Меню топ-->

    <!--Центр-->
    <div id="center">
        <!--Content-->
        <div class="content cms">
            <div class="row">
            <!--Сайт-бар reports-->

                <div class="sidebar col-md-3 col-xs-3">
                    <h1 class="title">Меню</h1>
                    <!--Категория-->
                    <div class="category___sidebar category-list">
                        <?php if(Yii::$app->controller->actionsMenu): ?>

                            <?php
                            /*
                            echo '<pre style="background: #fff;">';
                              print_r(Yii::$app->controller->actionsMenu);
                            echo '</pre>';*/

                            ?>
                           <?php foreach (Yii::$app->controller->actionsMenu as $group): ?>
                                <div class="item">
                                    <a href="<?=$group['link']; ?>" class="main blue" ><b><?=$group['title']?></b></a>
                                    <?php foreach ($group['items'] as $item): ?>
                                         <div class="i"><a href="<?=$item['link'] ?>" class="blue"><?=$item['title'] ?></a></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div><!--/Категория-->
                </div><!--Сайт-бар-->

                <div id="shop_management" class="col-md-9 col-xs-9">
                    <?= $content ?>
                </div>
            </div>
            <div class="clear"></div>
        </div><!--/Content-->
    </div><!--/Центр-->
    <!--Подвал-->
    <div id="footer">
        <div class="panel-footer-main"></div>
        <div class="row">
            <div class="col-md-3 col-xs-3 item">
                <div class="payments"> <div class="name-main">Принимаем к оплате</div><img src="/images/payments.png" alt="Принемаем к оплате"></div>
            </div>
            <div class="col-md-6 col-xs-6 item">
                <div class="row">
                    <div class="col-md-7 col-xs-7">
                        <div class="menu-footer">
                            <?php foreach($pagesMenus['header'] as $key=>$menu): ?>
                                <a href="/static/page/<?=$menu['url'];?><?php if(isset($menu['anchor'])) print '#'.$menu['anchor'];?>" class="blue i"><?=$menu['name']?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-3 item">
                <div class="contacts">
                    <div class="phone">8 383 349-92-09</div>
                    <div class="time">Время работы операторов: <br><b><?=$pagesOptions['time']?></b><br>Вс выходной</div>

                    <div class="call"><a href="#" onclick="return window_show('call','Заказ звонка');">Заказать звонок</a></div>
                </div>
                <div class="social">
                    <a href="https://instagram.com/Esalad.ru" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/instagram.png" alt=""> </a>
                    <a href="http://vk.com/Esalad_russia" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/vk.png" alt=""> </a>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="row">
            <div class="col-md-4 col-xs-4"><div class="copyright">© Esalad 2015 Все права защищены.</div></div>
            <div class="col-md-7 col-xs-7"><div class="version"><a href="">Мобильная версия</a></div></div>
            <div class="clear"></div>
        </div>
    </div><!--/Подвал-->
</div><!--/Главная container-->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
