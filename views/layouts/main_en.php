<?php

/* @var $this \yii\web\View */
/* @var $content string */
use app\assets\AppAsset;


use app\modules\common\models\ModFunctions;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\common\models\User;
use yii\bootstrap\Progress;

AppAsset::register($this);
//CatalogAsset::register($this);


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
        <?php if(!empty(Yii::$app->params['mobile'])): ?>
            <meta name="apple-mobile-web-app-capable" content="yes" />
        <?php endif; ?>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
        <meta name="yandex-verification" content="7ef6ad82f324d076" />
        <meta name="yandex-verification" content="59b3df0bbfde7230" />
        <meta name="yandex-verification" content="7ef8ba088516092f" />
        <meta name='wmail-verification' content='205d519a9d38dbaff767419f0eb76233' />

        <title><?= Html::encode($this->title) ?></title>

    </head>

    <body <?php
          if(Yii::$app->request->url == '/'):?>class=""<?php endif;?> ><?php
    ?>
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
    //var_dump(\Yii::$app->language);
    ?>
    <div class="alert___content"></div>
    <div id="to-top">
        <div class="str en"></div>
    </div>
    <div id="br-show"></div>
    <div class="br-shadow"></div>
    <div id="loadAjax"><div class="loader"></div></div>

    <!--Главная container-->
    <div class="container shop-container" data-page="<?= \Yii::$app->controller->uniqueId?>">
        <!--Шапка-->
        <div id="header">

            <!--Десктопная версия-->
            <div class="header-content desktop">
                <div class="fix-content-panel">
                    <div class="total">

                    </div>
                    <div class="block">
                        <?php if(!Yii::$app->user->isGuest):?>
                            <?php
                            if(!empty(Yii::$app->user->id)){
                                $userInfo = User::find()->select('money, bonus')->where(['id'=>Yii::$app->user->id])->asArray()->one();
                                if(empty($userInfo)){
                                    $userInfo['bonus'] = Yii::$app->user->identity->bonus;
                                    $userInfo['money'] = Yii::$app->user->identity->money;
                                }
                            }
                            else{
                                $userInfo['bonus'] = Yii::$app->user->identity->bonus;
                                $userInfo['money'] = Yii::$app->user->identity->money;
                            }
                            ?>
                            <div class="bonus"><?= ModFunctions::bonus($userInfo['bonus'])?> /</div>
                            <div class="money"><?=ModFunctions::money($userInfo['money'])?></div>
                            <div class="user-container"><a href="#" class="user user-icon white" data-toggle="dropdown"  onclick="return false;"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></a></div> / <a href="/site/logout" class="out white">Sign out</a>
                        <?php else: ?>
                            <a href="#" class="user user-icon white" onclick="return window_show('login','login',false,false,true);">login</a> / <a href="#" class="user reg white" onclick="return window_show('signup','Sign up',false,false,true);">Sign up</a>  <!--/Войти или авторизоваться-->
                        <?php endif; ?>
                        <div class="small-basket-block"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="top">
                    <div class="br-top hidden"></div>
                    <div class="row">
                        <div class="col-md-2 col-xs-2" style="width: 11.667%">
                            <div class="city city-icon"><a href="#" class="white" onclick="return window_show('city');">Toronto</a></div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="item">
                                <a href="/static/page/rules#how-to-order" class="white item-header hidden">Place order</a>
                                <a href="/static/page/rules#payment" class="white item-header hidden">Payment</a>
                                <a href="/static/page/rules#shipping" class="white item-header">Delivery</a>
                                <a href="/static/page/rules#return" class="white item-header hidden">Return</a>
                                <a href="/static/page/corporative" class="white item-header">Corporate Orders</a>
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
                                        <div class="money"><span class="bonus" rel="popover" data-placement="bottom" data-content="Bonus balance. You can spend your bonuses at GH.cafe for goods with β sign."><?= ModFunctions::bonus($userInfo['bonus'])?></span> / <span class="money" rel="popover" data-placement="bottom" data-content="Your balance! You can buy with pleasure!."><?=ModFunctions::money($userInfo['money'])?></span></div>
                                        <span class="user-container"><a href="#" class="user user-icon white" data-toggle="dropdown"  onclick="return false;"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></a>
                                   <div class="box-container">
                                       <!-- Меню ЛК-->
                                       <?= \app\components\WMyMenu::widget()?>
                                       <!-- Меню ЛК-->
                                   </div>
                               </span> / <a href="/site/logout" class="out white">Sign out</a>
                                    </div>
                                <?php else: ?>
                                    <a href="#" class="user user-icon white" onclick="return window_show('login','login',false,false,true);">login</a> / <a href="#" class="user reg white" onclick="return window_show('signup','Signup',false,false,true);">Sign up</a>  <!--/Войти или авторизоваться-->
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="bottom">
                    <div class="row">
                        <div class="col-md-3 col-xs-3">
                            <div class="logo"><a href="/"><img src="/images/logotype.png" alt=""></a></div>
                        </div>
                        <div class="col-md-7 col-xs-7">
                            <!--Поиск-->
                            <div class="search">
                                <form action="/search/index" method="post" >
                                    <div class="input">
                                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                                        <input type="text" name="search" value="" maxlength="64" autocomplete="off" placeholder="Enter a keyword, category or brand" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Enter a keyword, category or brand')" />
                                    </div>
                                    <div class="button" onclick="$(this).parents('form').submit();"></div>
                                </form>
                            </div> <!--/Поиск-->
                            <div class="info-header">
                                <span class="phone"><?php if(!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == '192.168.0.14' || $_SERVER['HTTP_HOST'] == '192.168.0.11'): ?> <?=$_SERVER['HTTP_HOST']?><?php else: ?>   8 383 349-92-09<?php endif; ?></span>
                                <span class="version"><a href="mailto:info@Esalad.ru">info@Esalad.ru</a></span>
                                <span class="time"><?=$pagesOptions['time']?></span>
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
                            <div class="col-xs-2 grid">
                                <!--Пользватель-->
                                <div class="user menu-qml menu-qml-icon js-catalog-menu"><span></span></div>  <!--./Пользватель -->
                            </div>
                            <div class="col-xs-6">
                                <a href="/" class="no-border text-center" style="color: rgb(255, 255, 255); display: block; font-size: 16px; margin-top: 14px;">GH Cafe</a>
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
                                <input type="text" name="search" value="" maxlength="64" autocomplete="off" placeholder="Find on GH Cafe" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Find on GH Cafe')" />
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
                <div class="select-menu open" rel="1"><div class="home-icon"></div> </div>
                <?php if(!Yii::$app->user->isGuest):?>
                    <div class="select-menu" rel="2"><div class="m-user-icon"></div></div>
                <?php endif;?>
                <?php if(!empty(\Yii::$app->user->identity) && (\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('GodMode'))): ?>
                    <div class="select-menu" rel="3"><div class="m-admin-icon"></div></div>
                <?php endif; ?>
            </div>
            <?php if(!Yii::$app->user->isGuest):?>
                <div class="user-nav">
                    <div class="balance">
                        <div class="user-name"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></div>
                        <div class="money">Balance: <span><?=ModFunctions::money(Yii::$app->user->identity->money)?></span> / <span><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?></span> bonuses</div>
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
                    <div class="login button_oran" onclick="return window_show('login','Login',false,false,true);"><div>Login</div></div> / <div class="reg button_oran" onclick="return window_show('signup','Sign up',false,false,true);"><div>Sign up</div></div>
                </div>
            <?php endif; ?>
            <!--Каталог -->
            <div class="container-menu open" rel="1">
                <?= \app\components\shopMobile\WMainMenu::widget()?>
            </div> <!--./Каталог -->
            <?php  if(!empty(\Yii::$app->user->identity) && (\Yii::$app->user->can('categoryManager') || \Yii::$app->user->can('GodMode'))): ?>
                <!--Админская часть меню -->
                <div class="container-menu" rel="3">
                    <?= \app\components\shopMobile\WMainAdminMenu::widget()?>
                </div> <!--Админская часть меню -->
            <?php endif; ?>
            <div class="menu-footer-content">
                <?php
                // Загрузка Меню;
                $pagesMenus = \app\modules\pages\models\Pages::getPagesMenu();
                 if(!Yii::$app->params['en']) {
                     foreach ($pagesMenus['header'] as $key => $menu): ?>
                         <a href="/static/page/<?= $menu['url']; ?><?php if (isset($menu['anchor'])) print '#' . $menu['anchor']; ?>"
                            class="blue i"><?= $menu['name'] ?></a>
                     <?php endforeach;
                 }
                    ?>

                <?php  if(!Yii::$app->user->isGuest):?><a href="/site/logout" class="out blue i">Sign out</a><?php endif;?>
            </div>
        </div> <!--./Навигация и авторизация-->
        <!--Меню топ дестопкая версия-->
        <div id="menu-top" class="menu-top desktop">
            <div class="bg"></div><?php
            print \app\components\WGeneralCatalogMenu::widget();?>
        </div><!--/Меню топ-->
        <!--Центр-->
        <div id="center">

            <?php

            if(!Yii::$app->user->isGuest){
                $user = User::findOne(Yii::$app->user->getId());

                if(!Yii::$app->user->isGuest && $user->secret_word==NULL)
                {

                    Modal::begin([
                        'header' => '<h3>Enter the code word</h3>',
                        'id' => 'secretWord',
                        'class' => 'modal show',
                    ]);


                    echo "
                <p>Please enter the code word and remember it.<br><br> The code word is used to change the data, phone, as well as password recovery.<br><br>
                <span color='red'>Warning!</span> The code word is replaced and can not be restored.</p>";

                    echo Html::beginForm(['/site/save-secret-word'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
                    echo Html::input('text', 'secretWord', '', ['class' => 'form-control', 'minlength' => 6, 'maxlength' => 255]);
                    echo Html::submitButton(\Yii::t('app', 'Сохранить'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
                    echo Html::endForm();

                    Modal::end();
                }
            }

            ?>

            <?= $content ?>
        </div><!--/Центр-->
        <!--Подвал-->
        <div id="footer" class="push" style="height: 35px">

            <div class="copyright col-xs-3">© GH.Cafe <?= date('Y') ?>. All rights reserved.</div>
            <div class="menu-footer-en col-xs-6">
                <a href="/site/service" class="grey">Terms of service</a>
                <a href="/site/policy" class="grey">Privacy policy</a>
            </div>

        </div><!--/Подвал-->

    </div><!--/Главная container-->


    <!--Мастер помощник-->
    <?= \app\components\html\WMasterHelp::widget(['menuId'=>\Yii::$app->params['menuListId']['parent_id']])?>
    <!--/Мастер помощник-->


    <?php $this->endBody() ?>

    <!--Метрика -->
    <?= \app\components\html\WCounters::widget()?>

    <?php if(!\Yii::$app->user->can('categoryManager') && !\Yii::$app->user->can('GodMode') && empty(Yii::$app->params['mobile'])):?>
        <!--Онлайн консультант-->

    <?php endif; ?>

    <script type="text/javascript">
        $(window).load(function(){

            var flag = false;

            $('#secretWord').modal('show');
            //$('#secretWord').modal({backdrop:'static',keyboard:false, show:true});

            $("#secretWord").on('hide.bs.modal', function () {
                return false;
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

<?php


