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


AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<?php

/*Меню ЛК*/
$subItems = [];
if(!Yii::$app->user->isGuest) {
    $subItems[] = [
        'label' => 'Мои адреса',
        'url'   => '#'
    ];
    $subItems[] = [
        'label' => 'Операции с балансом',
        'url'   => '#'
    ];
}
if(Yii::$app->user->can('GodMode')){
    $subItems[] =   [
        'label' => 'Управление пользователями',
        'url' => '/user'
    ];
    $subItems[] =   [
        'label' => 'Управление магазином',
        'url' => '#'
    ];
}
if(!Yii::$app->user->isGuest) {
    $subItems[] =   [
        'label' => 'История заказов',
        'url' => '#'
    ];
    $subItems[] =   [
        'label' => 'Промо код',
        'url' => '#'
    ];
    $subItems[] =   [
        'label' => 'Избранные товары',
        'url' => '#'
    ];
    $subItems[] =   [
        'label' => 'Обратная связь',
        'url' => '#'
    ];
}
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

<!--Главная container-->
<div class="container">
    <!--Шапка-->
    <div id="header">
        <div class="top">
            <div class="row">
                <div class="col-md-2 col-xs-2">
                    <div class="city city-icon"><a href="#" class="black" onclick="return window_show('city');">Новосибирск</a></div>
                </div>
                <div class="col-md-6 col-xs-6"></div>
                <div class="col-md-4 col-xs-4">
                    <div class="user">
                        <?php if(!Yii::$app->user->isGuest):?>
                            <div class="user-profile">
                                <div class="money"></div>
                               <span class="user-container"><a href="#" class="user user-icon" data-toggle="dropdown"  onclick="return false;"><?=Yii::$app->user->identity->name?></a>
                                   <div class="box-container">
                                       <!-- Меню ЛК-->
                                       <?php
                                       if(!empty($subItems)){
                                           foreach($subItems as $item){
                                               ?><div class="item"> <a href="<?= $item['url']?>"><?= $item['label']?></a></div><?php
                                           }
                                       }?>
                                       <!-- Меню ЛК-->
                                   </div>
                               </span> / <a href="/site/logout" class="out">Выйти</a>
                            </div>
                        <?php else: ?>
                            <a href="#" class="user user-icon" onclick="return window_show('login','Вход');">Вход</a> / <a href="#" class="user reg" onclick="return window_show('signup','Регистрация');">Регистрация</a>  <!--/Войти или авторизоваться-->
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

                </div>
                <div class="col-md-2 col-xs-2">

                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div><!--/Шапка-->

    <!--Меню топ-->
    <div id="menu-top">
        <div class="bg"></div>
    </div><!--/Меню топ-->
    <!--Центр-->
    <div id="center">



        <table width="100%" cellpadding="3" cellspacing="0">
        <tr>
            <td valign="top" width="250">
                    <h1>Меню</h1>

                    <ul>
                        <?php
                        foreach (Yii::$app->controller->actionsMenu as $group) {
                            ?><li><?=$group['title']?>
                            <ul>
                                <?php
                                foreach ($group['items'] as $item) {
                                    ?><li><a href="<?=$item['link']?>"><?=$item['title']?></a></li><?php
                                }?>
                            </ul>
                            </li><?php
                        }?>
                    </ul>
            </td>
            <td valign="top">

                <?= $content ?>

            </td>
            </tr>
        </table>


    </div><!--/Центр-->
    <!--Подвал-->
    <div id="footer">
        <div class="panel-footer-main"></div>
        <div class="row">
            <div class="col-md-3 col-xs-3 item">
                <div class="payments"> <div class="name-main">Принимаем к оплате</div><img src="/templates/images/payments.png" alt="Принемаем к оплате"></div>
            </div>
            <div class="col-md-6 col-xs-6 item">
                <div class="row">
                    <div class="col-md-7 col-xs-7">
                        <div class="menu-footer">
                            +++
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-3 item">
                <div class="contacts">
                    <div class="phone">8 383 349-92-09</div>
                    <div class="time">Время работы операторов: <br><b><?=$pagesOptions['time']?></b><br>Вс выходной</div>

                    <div class="call"><a href="#" onclick="">Заказать звонок</a></div>
                </div>
                <div class="social">
                    <a href="https://instagram.com/Esalad.ru" rel="nofollow" target="_blank" class="no-border opacity"><img src="/templates/images/instagram.png" alt=""> </a>
                    <a href="https://vk.com/Esalad_russia" rel="nofollow" target="_blank" class="no-border opacity"><img src="/templates/images/vk.png" alt=""> </a>
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
