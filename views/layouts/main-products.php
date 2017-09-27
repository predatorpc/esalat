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

    <meta name="yandex-verification" content="7ef6ad82f324d076" />
    <meta name="yandex-verification" content="59b3df0bbfde7230" />
    <meta name="yandex-verification" content="7ef8ba088516092f" />

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link rel="stylesheet" type="text/css" href="/css/styles.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/global.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/my.css?rand=<?=time();?>" />

    <title><?= Html::encode($this->title) ?></title>
</head>
<body <?php if(Yii::$app->request->url == '/'):?>class=""<?php endif;?>>
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

<?php
$allflash = Yii::$app->session->getAllFlashes();
//\app\modules\common\models\Zloradnij::print_arr($allflash);
?>
<div id="to-top">
    <div class="str"></div>
    <div class="text"><?=\Yii::t('app','Наверх');?></div>
</div>
<div id="br-show"></div>
<div id="loadAjax"><div class="loader"></div></div>
<!--Главная container-->
<div class="container shop-container" data-page="<?= \Yii::$app->controller->uniqueId?>">
    <!--Шапка-->
    <div id="header">
        <div class="top">
            <div class="br-top"></div>
            <div class="row">
                <div class="col-md-2 col-xs-2" style="width: 11.667%">
                    <div class="city city-icon"><a href="#" class="white" onclick="return window_show('city');">Новосибирск</a></div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div class="item">
                        <a href="/static/page/rules#how-to-order" class="white item-header">Оформление заказа</a>
                        <a href="/static/page/rules#payment" class="white item-header">Оплата</a>
                        <a href="/static/page/rules#shipping" class="white item-header">Доставка</a>
                        <a href="/static/page/rules#return" class="white item-header">Возврат</a>
                        <a href="/static/page/corporative" class="white item-header">Корпоративным клиентам</a>
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
                    Клон
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
    </div><!--/Шапка-->
    <!--Меню топ-->
    <div id="menu-top">
        <div class="bg" style="width: 100%"></div>
        <?= \app\components\shopProducts\WGeneralCatalogMenu::widget()?>
    </div><!--/Меню топ-->
    <!--Центр-->
    <div id="center">
        <?= $content ?>
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
                    <a href="https://vk.com/Esalad_russia" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/vk.png" alt=""> </a>
                </div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="row">
            <div class="col-md-4 col-xs-4"><div class="copyright">© Esalad 2015 Все права защищены.</div></div>
            <div class="col-md-7 col-xs-7"><div class="version"><a href="<?=$_SERVER['REQUEST_URI']?>?version=yes">Мобильная версия</a></div></div>
            <div class="clear"></div>
            <div class="clear" style="height: 50px;"></div>
        </div>
    </div><!--/Подвал-->
</div><!--/Главная container-->

<?php $this->endBody() ?>

<!--Метрика-->
<?= \app\components\html\WCounters::widget()?>
<?php if(!\Yii::$app->user->can('categoryManager') && !\Yii::$app->user->can('GodMode')):?>
    <!--Онлайн консультант-->
    <?= \app\components\html\WChat::widget()?>
<?php endif; ?>

</body>
</html>
<?php $this->endPage() ?>
