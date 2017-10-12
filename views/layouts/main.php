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
use yii\bootstrap\Progress;
use app\modules\pages\models\LoginForm;
//Yii::$app->cache->flush();

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
    <?php if(!empty(Yii::$app->params['mobile'])): ?>
        <meta name="apple-mobile-web-app-capable" content="yes" />
    <?php endif; ?>
    <meta name="theme-color" content="#208b0b">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

    <title><?= Html::encode($this->title) ?></title>

</head>

<body <?php
      if(Yii::$app->request->url == '/'):?>class=""<?php endif;?> ><?php
    /*if((Yii::$app->user->identity->getId() == 10013181)
        || (Yii::$app->user->identity->getId() == 10014596)
          || (Yii::$app->user->identity->getId() == 10000933)){
        print \app\widgets\layout\WCashButton::widget();
    }  */
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
?>

    <div id="to-top">
        <div class="str"></div>
        <div class="text"><?=\Yii::t('app','Наверх');?></div>
    </div>
    <!--Масштабирования экран для моб. версия-->
    <div  class="zoom-content">

        <div class="alert___content"></div>
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
                     <?php if(!Yii::$app->user->isGuest && false): ?>
                        <?= \app\components\WBasketDeliveryFree::widget()?>
                        <?= \app\components\WBasketCardFree::widget()?>
                        <?= \app\components\WBasketCardFreeGoldPlus::widget()?>
                     <?php endif; ?>
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
                              /*<div class="bonus"><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?> /</div>
                              <div class="money"><?=ModFunctions::money(Yii::$app->user->identity->money)?></div>*/
                              ?>
                                <div class="bonus hidden"><?= ModFunctions::bonus($userInfo['bonus'])?> /</div>
                                <div class="money"><?=ModFunctions::money($userInfo['money'])?></div>
                                <div class="user-container">
                                    <a href="#" class="user user-icon white js-user-menu" data-toggle="dropdown"  onclick="return false;"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></a>
                                    <div class="box-container">
                                        <!-- Меню ЛК-->
                                        <?= \app\components\WMyMenu::widget()?>
                                        <!-- Меню ЛК-->
                                    </div>
                                </div> / <a href="/site/logout" class="out white">Выйти</a>

                          <?php else: ?>
                              <a href="#" class="user user-icon white" onclick="return window_show('login','Вход',false,false,true);">Вход</a> / <a href="#" class="user reg white" onclick="return window_show('signup','Регистрация',false,false,true);">Регистрация</a>  <!--/Войти или авторизоваться-->
                          <?php endif; ?>
                          <div class="small-basket-block"></div>
                      </div>
                      <div class="clear"></div>
                  </div>
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
                                      <div class="money"><span class="bonus hidden" rel="popover" data-placement="bottom" data-content="Бонусный баланс. Потратить бонусные деньги вы можете в магазине Esalad на товары со значком.β"><?= ModFunctions::bonus($userInfo['bonus'])?></span>  <span class="money" rel="popover" data-placement="bottom" data-content="Баланс интернет-магазина Esalad. Совершайте покупки в интернет-магазине Esalad с удовольствием."><?=ModFunctions::money($userInfo['money'])?></span></div>
                                       <span class="user-container _master_user"><a href="#" class="user user-icon white" data-toggle="dropdown"  onclick="return false;"><?=ModFunctions::userName(Yii::$app->user->identity->name)?></a>
                                           <div class="box-container">
                                                <!-- Меню ЛК-->
                                               <?= \app\components\WMyMenu::widget()?>
                                               <!-- Меню ЛК-->

                                           </div>
                                       </span> / <?php if(\Yii::$app->user->can('callcenterOperator')):?><a href="/user/inviteuser" class="out white">AP</a> / <?php endif ?><a href="/site/logout" class="out white">Выйти</a>
                                    </div>
                                <?php else: ?>
                                   <a href="#" class="user user-icon white" onclick="return window_show('login','Вход',false,false,true);">Вход</a> / <a href="#" class="user reg white" onclick="return window_show('signup','Регистрация',false,false,true);">Регистрация</a>  <!--/Войти или авторизоваться-->
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
                            <div class="info-header hidden">
                                <span class="phone"><?php if(!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == '192.168.0.14' || $_SERVER['HTTP_HOST'] == '192.168.0.11'): ?> <?=$_SERVER['HTTP_HOST']?><?php else: ?>   8 383 349-92-09<?php endif; ?></span>
                                <span class="version"><a href="mailto:info@esalad.ru">info@esalad.ru</a></span>
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
                                  <input type="text" name="search" value="" maxlength="64" autocomplete="off" placeholder="Найти на Esalad" onfocus="$(this).attr('placeholder','')" onblur="$(this).attr('placeholder','Найти на Esalad')" />
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
                            <div class="user-name"><?=ModFunctions::userName(Yii::$app->user->identity->name)?> <span style="color:#0C7CA8"><?=ModFunctions::money(Yii::$app->user->identity->money)?></span> <span class="hidden" style="color:#0C7CA8"><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?></span></div>
                            <div class="money hidden"><span><?=ModFunctions::money(Yii::$app->user->identity->money)?></span>  <div class="hidden"> <span><?= ModFunctions::bonus(Yii::$app->user->identity->bonus)?></span></div></div>
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
                    foreach($pagesMenus['header'] as $key=>$menu): ?>
                        <a href="/static/page/<?=$menu['url'];?><?php if(isset($menu['anchor'])) print '#'.$menu['anchor'];?>" class="blue i"><?=$menu['name']?></a>
                    <?php endforeach; ?>
                    <?php  if(!Yii::$app->user->isGuest):?><a href="/site/logout" class="out blue i">Выйти</a><?php endif;?>
                </div>
            </div> <!--./Навигация и авторизация-->
            <!--Меню топ дестопкая версия-->
            <div id="menu-top" class="desktop" >
                <?= \app\components\WGeneralCatalogMenuTop::widget()?>
            </div>
            <div class="clear"></div>
            <!--/Меню топ-->

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
                                <div class="col-md-8 col-xs-8 ">
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
                                <a href="https://instagram.com/Esalad.ru" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/instagram.png" alt=""> </a>
                                <a href="https://vk.com/Esalad_russia" rel="nofollow" target="_blank" class="no-border opacity"><img src="/images/vk.png" alt=""> </a>
                            </div>-->
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="row">
                        <div class="col-md-4 col-xs-4"><div class="copyright">© Esalad 2015 Все права защищены.</div></div>
                        <div class="col-md-7 col-xs-7"><div class="version"><a class="hidden" href="http://www.esalad.ru/?version=yes">Мобильная версия</a></div></div>
                        <div class="clear"></div>
                    </div>
                </div>  <!--/Десктоп-->
                <!--Мобильная версия-->
                <div class="footer-content mobile">
                    <div class="phone"><div><?=$pagesOptions['phone']?></div></div>
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
    <div id="toArraySet" data-array=""></div>


    </div> <!--Масштаб-->

<!--Мастер помощник-->
<?php // \app\components\html\WMasterHelp::widget(['menuId'=>\Yii::$app->params['menuListId']['parent_id']])?>
<!--/Мастер помощник-->

<!--Таймер-->
<?= \app\components\html\WTimer::widget()?>
<!--/Таймер-->

<!--Акция-->
<?= \app\components\html\WStock::widget()?>
<!--/Акция-->
<?php $this->endBody() ?>

<!--Метрика -->
<?= \app\components\html\WCounters::widget()?>

<?php if(!\Yii::$app->user->can('categoryManager') && !\Yii::$app->user->can('GodMode') && empty(Yii::$app->params['mobile'])):?>
    <!--Онлайн консультант-->
    <?php // \app\components\html\WChat::widget()?>
<?php endif; ?>

<script type="text/javascript">
    $(window).load(function(){

        var flag = false;

        $('#secretWord').modal('show');
        //$('#secretWord').modal({backdrop:'static',keyboard:false, show:true});
        // $(".zoom-content").css('transform','inherit');
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


