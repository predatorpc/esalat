<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\modules\pages\models\LoginForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\Pjax;
use yii\bootstrap\Button;
use yii\bootstrap\Modal;
use app\modules\common\models\Menu;
use kartik\nav\NavX;
use \app\modules\common\models\ModFunctions;
use app\modules\basket\models\Basket;
//AppAsset::register($this);
\app\assets\MobileAsset::register($this);
?>
<?php $this->beginPage() ?>

<?php

// Загрузка Меню;
$pagesMenus = \app\modules\pages\models\Pages::getPagesMenu();
// Загрузка параметров;
$pagesOptions = \app\modules\pages\models\PagesOptions::pagesOptions();
// Корзина;
//$basket = \Yii::$app->controller->basketObject;
//$smallBasket = $basket->smallBasket;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="rus" lang="rus">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <link rel="stylesheet" type="text/css" href="/css/global.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/my.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/mobile/m-styles.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/mobile/screen.css?rand=<?=time();?>" />
    <meta name="yandex-verification" content="7ef6ad82f324d076" />
    <meta name="yandex-verification" content="59b3df0bbfde7230" />
    <meta name="yandex-verification" content="7ef8ba088516092f" />
    <meta name='wmail-verification' content='205d519a9d38dbaff767419f0eb76233' />

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



?>
<div id="br-show"></div>
<div id="loadAjax"><div class="loader"></div></div>
<div id="to-top"></div>
<div id="basket-total-info"><div class="basket-total"></div></div>
<!--Главная container-->
<div class="container shop-container" data-page="<?= \Yii::$app->controller->uniqueId?>">
    <!--Шапка-->
    <div id="header">
        <div class="top">
            <div class="row">
                <div class="col-xs-3 grid">
                    <!--Пользватель -->
                    <div class="user icon-user"></div> <!--./Пользватель -->
                </div>
                <div class="col-xs-6">
                    <a href="/"><div class="logo"></div></a>
                </div>
                <div class="col-xs-3 grid basket small-basket-block">
                   <?php
                   print Yii::$app->basket->displaySmallBasket();
                   /*
                    * =\app\components\WBasketSmall::widget()
                   */
                   ?>
                </div>
            </div>
            <div class="clear"></div>
            <!--Навигация и авторизация-->
            <div class="m-navigation">
                <?php if(!Yii::$app->user->isGuest):?>
                <div class="user-nav">
                    <div class="balance">
                        <div class="user-name"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></div>
                        <div class="money">Баланс: <span><?=ModFunctions::money(Yii::$app->user->identity->money)?></span> / <span><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?></span> бонусов</div>
                        <a href="/site/logout" class="out">Выйти</a>
                        <div class="clear"></div>
                    </div>
                </div>
                <!--меню-->
                <div class="items">
                    <!--Навигация для ЛК-->
                    <div class="my-menu-mob">
                        <?= \app\components\shopMobile\WMyMenuMobile::widget()?>
                    </div>
                </div><!--/меню-->
                <?php else: ?>
                  <!--Регистрация или вход-->
                    <div class="user-account">
                      <div class="login button_oran" onclick="return window_show('login','Вход');"><div>Вход</div></div> / <div class="reg button_oran" onclick="return window_show('signup','Регистрация');"><div>Регистрация</div></div>
                    </div>
                <?php endif; ?>
            </div> <!--./Навигация и авторизация-->
        </div>
        <div class="bottom">
            <!--Поиск-->
            <div class="search">
                <form action="/search/" method="post">
                    <div class="input">
                        <input type="text" name="search" value="" maxlength="64" autocomplete="off" placeholder="Найти на Esalad" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Найти на Esalad')" />
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                    </div>
                    <div class="button" onclick="$(this).parents('form').submit();"></div>
                </form>
            </div> <!--/Поиск-->
        </div>
        <div class="clear"></div>
    </div><!--/Шапка-->
    <?php if(Yii::$app->request->url != '/'):?>
    <!--Меню топ-->
    <div id="menu-top">
        <div class="main-menu"><div>Меню</div></div>
        <?= \app\components\shopMobile\WMainMenu::widget()?>
    </div><!--/Меню топ-->
    <?php endif;?>
    <!--Центр-->
    <div id="center">
        <?= $content ?>
    </div><!--/Центр-->
    <!--Подвал-->
    <div id="footer">
        <div class="menu-footer">
            <?php foreach($pagesMenus['header'] as $key=>$menu): ?>
                <a class="no-border item-menu" href="/static/page/<?=$menu['url'];?><?php if(isset($menu['anchor'])) print '#'.$menu['anchor'];?>"><?=$menu['name']?></a>
            <?php endforeach; ?>
        </div>
        <div class="phone"><div><?=$pagesOptions['phone']?></div></div>
        <div class="call"><a href="/" class="white" onclick="return window_show('call','Заказ звонка');">Заказать звонок</a> </div>
        <div class="social">
            <div class="item"><a href="https://instagram.com/Esalad.ru" rel="nofollow" target="_blank" class="no-border icon-instagram"></a></div>
            <div class="item"><a href="https://vk.com/Esalad_russia" rel="nofollow" target="_blank" class="no-border icon-vk"></a></div>
        </div>
        <div class="clear"></div>
        <div class="version"><a class="white" href="<?=$_SERVER['REQUEST_URI']?>?version=no">Полная версия</a></div>
        <div class="time copyright" style="padding:5px 0px;">Время работы операторов:<br><?=$pagesOptions['time']?> (Вс выходной)</div>
        <div class="copyright">© Esalad 2015 Все права защищены.</div>
    </div><!--/Подвал-->
</div><!--/Главная container-->



<?php $this->endBody() ?>

<script type="text/javascript">
    $(window).load(function(){

        var flag = false;

        $('#secretWord').modal('show');
        //$('#secretWord').modal({backdrop:'static',keyboard:false, show:true});

        $("#secretWord").on('hide.bs.modal', function () {
            return false;
        });

        $("#windows").on('hide.bs.modal', function () {
            //alert('!!');
            //return false;
            window.location.reload();
        });

        $('#agree').click(function(){
            if(flag==false) {
                $('#agree_button').removeAttr('disabled');
                flag = true;
            }
            else {
                $('#agree_button').attr('disabled',true);
                flag = false;
            }

        });

    });

</script>

<?= \app\components\html\WCounters::widget()?>
</body>
</html>
<?php $this->endPage() ?>
