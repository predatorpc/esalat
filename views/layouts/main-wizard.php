<?php

/* @var $this \yii\web\View */
/* @var $content string */
use app\assets\AppAsset;
use app\modules\common\models\ModFunctions;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\pages\models\SignupForm;
use app\modules\common\models\User;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<?php
// Загрузка параметров;
$pagesOptions = \app\modules\pages\models\PagesOptions::pagesOptions();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="rus" lang="rus">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <!-- link rel="stylesheet" type="text/css" href="/css/styles.css?rand=<?=time();?>" /-->
    <!-- link rel="stylesheet" type="text/css" href="/css/global.css?rand=<?=time();?>" /-->
    <!-- link rel="stylesheet" type="text/css" href="/css/my.css?rand=<?=time();?>" /-->
    <meta name="yandex-verification" content="7ef6ad82f324d076" />
    <meta name="yandex-verification" content="59b3df0bbfde7230" />
    <meta name="yandex-verification" content="7ef8ba088516092f" />
    <title><?= Html::encode($this->title) ?></title>

</head>

<body <?php if(Yii::$app->request->url == '/'):?>class=""<?php endif;?> >




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
//print Yii::$app->basket->basketId;
//\app\modules\common\models\Zloradnij::print_arr($_SESSION['basket']['object']);
//\app\modules\common\models\Zloradnij::print_arr($_SESSION['basket']['products'][1000054187]);
?>

<div id="to-top"></div>
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
        <div class="clear"></div>
    </div><!--/Шапка-->
    <!--Меню топ-->
    <!-- div id="menu-top">
        <div class="bg"></div>
        <?php //= \app\components\WGeneralCatalogMenu::widget()?>
    </div --><!--/Меню топ-->
    <!--Центр-->
    <div id="center">

        <?php

        if(!Yii::$app->user->isGuest){
            $user = User::findOne(Yii::$app->user->getId());

            if(!Yii::$app->user->isGuest && $user->secret_word==NULL)
            {

                Modal::begin([
                    'header' => '<h3>Ввод кодового слова</h3>',
                    'id' => 'secretWord',
                    'class' => 'modal show',
                ]);


                echo "
                <p>Пожалуйста, введите кодовое слово и запомните его.<br><br> Кодовое слово используется для смены данных, телефона, а так же восстановления пароля.<br><br>
                <span color='red'>Внимание!</span> Кодовое слово замене и восстановлению не подлежит.</p>";

                echo Html::beginForm(['/site/save-secret-word'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
                echo Html::input('text', 'secretWord', '', ['class' => 'form-control', 'minlength' => 6, 'maxlength' => 255]);
                echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
                echo Html::endForm();

                Modal::end();
            }
        }
        ?>

        <?= $content ?>
    </div><!--/Центр-->
    <!--Подвал-->
    <?php // if ($this->beginCache('layoutFooter', ['duration' => 3600*24])) : ?>
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
                        <div class="phone">8 383 349-92-09</div>
                        <div class="version">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:info@Esalad.ru">info@Esalad.ru</a></div>
                        <div class="time">Время работы операторов: <br><b><?=$pagesOptions['time']?> </b><br>Вс выходной</div>

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
        <?php //  $this->endCache() ?>
    <?php // endif ?>

</div><!--/Главная container-->

<?php $this->endBody() ?>

<!--Метрика-->
<?php // \app\components\html\WCounters::widget()?>
<?php if(!\Yii::$app->user->can('categoryManager') && !\Yii::$app->user->can('GodMode')):?>
    <!--Онлайн консультант-->
    <?= \app\components\html\WChat::widget()?>
<?php endif; ?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter30719268 = new Ya.Metrika({id:30719268, webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script>
<noscript><div><img src="//mc.yandex.ru/watch/30719268" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- Google Analytics (69832280) -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-69832280-1', 'auto');
    ga('send', 'pageview');
</script>
<!-- Google Code for Conversion Page -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 957141220;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "mnTTCM3d8WEQ5KGzyAM";
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<!-- TARG -->
<script type="text/javascript">
    (function () {
        window.admixer_retarg = window.admixer_retarg || [];
        window.admixer_retarg.push({  subs: "9b217741-683c-4060-a7a3-49bad050e62b", group: 593 })
        var id = 'AdmixerRetarg';
        if (document.getElementById(id)) {return;}
        var s = document.createElement('script');
        s.async = true; s.id = id;
        var r = (new Date).getDate();
        s.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//cdn.admixer.net/tscripts/retarg.js?r='+r;
        var a = document.getElementsByTagName('script')[0]
        a.parentNode.insertBefore(s, a);
    })()
</script>


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


</body>


</html>

<!-- Отработало за <?=Yii::getLogger()->getElapsedTime();?> с. Скушано памяти: <?=round(memory_get_peak_usage()/(1024*1024),2)."MB"?>  -->

<?php $this->endPage() ?>


