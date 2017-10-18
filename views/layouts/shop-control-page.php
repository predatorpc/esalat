<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\LoginForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\Pjax;
use yii\bootstrap\Button;
use yii\bootstrap\Modal;
use app\modules\common\models\Menu;
use kartik\nav\NavX;
use app\modules\common\models\ModFunctions;
use app\modules\common\models\User;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=10.0, user-scalable=yes" />
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
<!-- Шаблон для Личный кабинет поставщика--!>
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
<!--Главная container-->
<div id="br-shadow" title="Кликай сюда что бы закрыть меню!"></div>
<div class="br-shadow"></div>
<div class="container">
    <!--Шапка-->
    <div id="header">
        <!--Десктопная версия-->
        <div class="header-content desktop">

            <div class="top">
                <div class="br-top hidden"></div>
                <div class="row">
                    <div class="col-md-2 col-xs-2 hidden" style="width: 11.667%">
                        <div class="city city-icon"><a href="#" class="white" onclick="return window_show('site/sity','Новосибирск');">Новосибирск</a></div>
                    </div>
                    <div class="col-md-8 col-xs-8">
                        <div class="item">
                            <a href="/static/page/rules#how-to-order" class="white item-header">Оформление заказа</a>
                            <a href="/static/page/rules#payment" class="white item-header">Оплата</a>
                            <a href="/static/page/rules#shipping" class="white item-header">Доставка</a>
                            <a href="/static/page/rules#return" class="white item-header">Возврат</a>
                            <a href="/static/page/corporative" class="white item-header">Корпоративным клиентам</a>
                            <a href="/site/feed" class="white item-header _master _master_feed">Отзывы</a>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="user">
                            <?php if(!Yii::$app->user->isGuest):?>
                                <?php
                                if(!empty(Yii::$app->user->id)){
                                    $userInfo = User::find()->select('money, bonus')->where(['id'=>Yii::$app->user->id])->asArray()->one();
                                    if(empty($userInfo)){
                                        $userInfo['money']=Yii::$app->user->identity->money;
                                        $userInfo['bonus']=Yii::$app->user->identity->bonus;
                                    }
                                }
                                else{
                                    $userInfo['money']=Yii::$app->user->identity->money;
                                    $userInfo['bonus']=Yii::$app->user->identity->bonus;
                                }
                                ?>
                                <div class="user-profile">
                                    <div class="money"><span class="bonus hidden" rel="popover" data-placement="bottom" data-content="Бонусный баланс. Потратить бонусные деньги вы можете в магазине Esalad на товары со значком.β"><?= ModFunctions::bonus($userInfo['bonus'])?></span>  <span class="money" rel="popover" data-placement="bottom" data-content="Баланс интернет-магазина Esalat. Совершайте покупки в интернет-магазине Esalat с удовольствием."><?=ModFunctions::money($userInfo['money'])?></span></div>
                                    <span class="user-container _master_user"><a href="#" class="user white" data-toggle="dropdown"  onclick="return false;"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></a>
                                           <div class="box-container">
                                                <!-- Меню ЛК-->
                                               <?= \app\components\WMyMenu::widget()?>
                                               <!-- Меню ЛК-->

                                           </div>
                                       </span>/<?php if(\Yii::$app->user->can('callcenterOperator')):?><a href="/user/inviteuser" class="out white">AP</a>/<?php endif ?><a href="/site/logout" class="out white">Выйти</a>
                                </div>
                            <?php else: ?>
                                <a href="#" class="user white" onclick="return window_show('login','Вход',false,false,true);">Вход</a>/<a href="#" class="user reg white" onclick="return window_show('signup','Регистрация',false,false,true);">Регистрация</a>  <!--/Войти или авторизоваться-->
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="bottom">
                <div class="row">
                    <div class="col-md-3 col-xs-3">
                        <div class="logo"><a href="/"><img src="/images/logo.png?123" alt=""></a></div>
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
                            <span class="phone"><?php if(!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == '192.168.0.14' || $_SERVER['HTTP_HOST'] == '192.168.0.11'): ?> <?=$_SERVER['HTTP_HOST']?><?php else: ?><?=$pagesOptions['phone']?><?php endif; ?></span>
                            <span class="version"><a href="mailto:<?=$pagesOptions['email']?>"><?=$pagesOptions['email']?></a></span>
                            <span class="time"><?=$pagesOptions['time']?></span>
                            <div class="social hidden">
                                <a href="https://instagram.com/esalad.ru" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/instagram.png?123" alt=""> </a>
                                <a href="https://vk.com/esalad" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/vk.png?123" alt=""> </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2 small-basket-block desktop" id="basketDesktop" >
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div> <!--/Десктопная версия-->
        <!--Мобильная версия-->
        <div class="header-content mobile">
            <div class="fix-content">
                <div id="basket-total-info"></div>
                <div class="top push">
                    <div class="row">
                        <div class="col-xs-4 grid">
                            <!--Пользватель-->
                            <div class="user menu-qml menu-qml-icon js-catalog-menu"><span></span></div>  <!--./Пользватель -->
                        </div>
                        <div class="col-xs-4">
                            <?php if(Yii::$app->params['en']): ?>
                                <a href="/" class="no-border text-center" style="color: rgb(255, 255, 255); display: block; font-size: 16px; margin-top: 14px;">GH Cafe</a>
                            <?php else: ?>
                                <a href="/" class="logo"><img src="/images/mobil/logo-m.png"  style="margin: 0px; width: 90px; position: relative; top: -8px;"/> </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-xs-4 grid basket small-basket-block">
                            <?php print Yii::$app->basket->displaySmallBasket(); ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="bottom push">
                <!--Поиск-->
                <div class="search">
                    <form action="/search/" method="post">
                        <div class="input">
                            <input type="text" name="search" value="" maxlength="64" autocomplete="off" placeholder="Найти на Esalat" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Найти на Esalat')" />
                            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        </div>
                        <div class="button" onclick="$(this).parents('form').submit();"></div>
                    </form>
                </div> <!--/Поиск-->
            </div>
            <div class="clear"></div>
        </div><!--/Мобильная версия-->
    </div><!--/Шапка-->
    <!--Навигация и авторизация мобильная версия-->
    <div  class="m-navigation pushy-left mobile" id="slidingMenu">
        <div class="br-header">
            <div class="select-menu" rel="1"><div class="home-icon"></div> </div>
            <?php if(!Yii::$app->user->isGuest):?>
                <div class="select-menu" rel="2"><div class="m-user-icon"></div></div>
            <?php endif;?>
            <div class="select-menu open" rel="3"><div class="m-admin-icon"></div></div>
        </div>
        <?php if(!Yii::$app->user->isGuest):?>
            <div class="user-nav">
                <div class="balance">
                    <div class="user-name"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></div>
                    <div class="money">Баланс: <span><?=ModFunctions::money(Yii::$app->user->identity->money)?></span> / <span><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?></span> бонусов</div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--меню ЛК-->
            <div class="container-menu" rel="2">
                <!--Навигация для ЛК-->
                <?= \app\components\shopMobile\WMyMenuMobile::widget()?>
            </div><!--/меню ЛК-->
        <?php else: ?>
            <!--Регистрация или вход-->
            <div class="user-account">
                <div class="login button_oran" onclick="return window_show('login','Вход',false,false,true);"><div>Вход</div></div> / <div class="reg button_oran" onclick="return window_show('signup','Регистрация',false,false,true);"><div>Регистрация</div></div>
            </div>
        <?php endif; ?>
        <!--Каталог -->
        <div class="container-menu" rel="1">
            <?= \app\components\shopMobile\WMainMenu::widget()?>
        </div> <!--./Каталог -->
        <!--Админская часть меню -->
        <div class="container-menu open" rel="3">
            <?= \app\components\shopMobile\WMainAdminMenu::widget(['menuAdmin' => Yii::$app->controller->actionsShopOwnerMenu])?>
        </div> <!--Админская часть меню -->
        <div class="menu-footer-content">
            <?php
            // Загрузка Меню;
            $pagesMenus = \app\modules\pages\models\Pages::getPagesMenu();
            foreach($pagesMenus['header'] as $key=>$menu): ?>
                <a href="/static/page/<?=$menu['url'];?><?php if(isset($menu['anchor'])) print '#'.$menu['anchor'];?>" class="blue i"><?=$menu['name']?></a>
            <?php endforeach; ?>
            <?php  if(!Yii::$app->user->isGuest):?><a href="/site/logout" class="out blue i">Выйти</a><?php endif;?>
        </div>
    </div> <!--./Навигация и авторизация-->
    <!--Меню топ дестопкая версия-->
    <div id="menu-top" class="menu-top desktop">
        <div class="bg"></div><?php
        print \app\components\WGeneralCatalogMenu::widget();?>
    </div><!--/Меню топ-->
    <!--Центр-->
    <div id="center">
        <!--Content-->
        <div class="content cms">
            <div class="row">
                <!--Сайт-бар reports-->
                <div class="arrow-menu">Меню</div>
                <div class="sidebar sidebar-js col-md-3 col-xs-3">
                    <div class="br-sidebar"></div>
                    <!--Категория-->
                    <div class="category___sidebar category-list">
                        <div class="close"></div>
                        <h1 class="title">Категория</h1>
                        <?php if(Yii::$app->controller->actionsMenu): ?>
                            <?php  foreach (Yii::$app->controller->actionsShopOwnerMenu as $group): ?>
                                <div class="item">
                                    <a href="#" class="main white" onclick="return false;"><b><?=$group['title']?></b></a>
                                    <?php  foreach ($group['items'] as $item): ?>
                                        <div class="i"><a href="<?=$item['link'] ?>" class="white"><?=$item['title'] ?></a></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div><!--/Категория-->
                </div><!--Сайт-бар-->

                <div id="shop_management" class="col-md-12 col-xs-12">
                    <?= $content ?>
                </div>
            </div>
            <div class="clear"></div>
        </div><!--/Content-->
    </div><!--/Центр-->

    <!--Подвал-->
    <div id="footer" class="push">
        <!--Десктопная версия-->
        <div class="footer-content desktop">
            <div class="panel-footer-main"></div>
            <div class="row">
                <div class="col-md-3 col-xs-3 item">
                    <div class="payments"> <div class="name-main">Принимаем к оплате</div><img src="/images/payments.png" alt="Принемаем к оплате"></div>
                </div>
                <div class="col-md-6 col-xs-6 item">
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="menu-footer">
                                <?php
                                // Загрузка Меню;
                                $pagesMenus = \app\modules\pages\models\Pages::getPagesMenu();
                                foreach($pagesMenus['header'] as $key=>$menu): ?>
                                    <a href="/static/page/<?=$menu['url'];?><?php if(isset($menu['anchor'])) print '#'.$menu['anchor'];?>" class="blue i"><?=$menu['name']?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-xs-3 item">
                    <div class="contacts">
                        <div class="phone"><?=$pagesOptions['phone']?></div>
                        <div class="mail"><a href="mailto:<?=$pagesOptions['email']?>"><?=$pagesOptions['email']?></a></div>
                        <div class="time">Время работы операторов: <br><b><?=$pagesOptions['time']?></b></div>

                        <div class="call"><a href="#" onclick="return window_show('call','Заказ звонка');">Заказать звонок</a></div>
                    </div>
                    <!--
                    <div class="social ">
                        <a href="https://instagram.com/esalat.ru" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/instagram.png" alt=""> </a>
                        <a href="https://vk.com/esalat" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/vk.png" alt=""> </a>
                    </div>-->
                </div>
            </div>
            <div class="clear"></div>

            <div class="row">
                <div class="col-md-4 col-xs-4"><div class="copyright">© eSalat.ru <?=Date('Y')?> Все права защищены.</div></div>
                <div class="clear"></div>
            </div>
        </div>  <!--/Десктоп-->
        <!--Мобильная версия-->
        <div class="footer-content mobile">
            <div class="phone"><a href="tel:<?=$pagesOptions['phone']?>" class="white"><div><?=$pagesOptions['phone']?></div></a></div>
            <div class="call"><a href="/" class="white" onclick="return window_show('call','Заказ звонка');">Заказать звонок</a> </div>
            <div class="social">
                <div class="item"><a href="https://instagram.com/Esalad.ru" rel="nofollow" target="_blank" class="no-border icon-instagram"></a></div>
                <div class="item"><a href="https://vk.com/Esalad_russia" rel="nofollow" target="_blank" class="no-border icon-vk"></a></div>
                <div class="clear"></div>
            </div>
            <div class="time copyright" style="padding:5px 0px; line-height: 20px;">Время работы операторов:<br><?=$pagesOptions['time']?></div>
            <div class="copyright">© Esalad <?= date('Y') ?>  Все права защищены.</div>
        </div><!--/Мобильная версия-->
    </div><!--/Подвал-->

</div><!--/Главная container-->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
