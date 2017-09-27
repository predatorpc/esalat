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
use app\models\Menu;
use kartik\nav\NavX;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <meta charset="<?= Yii::$app->charset ?>">
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="container">
        <div id="header">
            <div class="header-content desktop">
              <div class="top">
                <div class="row">
                    <div class="col-md-2 col-xs-2">
                        <div class="city city-icon">
                            <a class="black" onclick="return window_show('city');" href="#">Новосибирск</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <span class="phone">8 383 349-92-09</span>
                        <span class="time">
                            Пн-Сб <?=$pagesOptions['time']?> <! --11:00 - 20:00  --> /
                            <a onclick="return window_show('call');" href="#">Заказать звонок</a>
                        </span>
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="user">
                            <?php
                            $menuItems = [];
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
                                $subItems[] =   [
                                    'label' => '',
                                    'options' => [
                                        'role' => 'presentation',
                                        'class' => 'divider'
                                    ]
                                ];
                                $subItems[] =   [
                                    'label'       => 'Выход из системы',
                                    'url'         => '/site/logout',
                                    'linkOptions' => ['data-method' => 'post'],
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
                                $label = Yii::$app->user->identity->name . " / ". Yii::$app->user->identity->phone;
                                $style = 'margin:5px;';
                            }
                            else {
                                $label = '';
                                $style = 'margin: 5px;';
                                $menuItems[] = '<li>'
                                    . Button::widget([
                                        'label'   => 'Вход',
                                        'options' => [
                                            'class' => 'user user-icon',
                                            //'style' => 'margin:5px',
                                            'id'    => 'login',
                                        ],
                                    ])
                                    . '</li>';
                                $menuItems[] = '<li class="">/</li>';

                                $menuItems[] = '<li>'
                                    . Button::widget([
                                        'label'   => 'Регистрация',
                                        'options' => [
                                            'class' => 'user',
                                            //'style' => 'margin:5px',
                                            'id'    => 'signup',
                                        ],
                                    ])
                                    . '</li>';
                            }

                            if(!empty($subItems)) {
                                $menuItems[] = '<li>'
                                    . ButtonDropdown::widget([
                                        'label' => $label,
                                        'options' => [
                                            'class' => 'user user-icon',
                                            'style' => 'margin: 5px;',
                                            'id' => 'userlabel',
                                        ],
                                        'dropdown' => [
                                            'items' => $subItems,
                                        ]
                                    ])
                                    . '</li>';
                            }
                            echo Nav::widget([
                                'options' => [
                                    'class' => 'navbar-nav navbar-right',
                                ],
                                'items' => $menuItems,
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
              <div class="bottom">
                <div class="row">
                    <div class="col-md-3 col-xs-3">
                        <div class="logo">
                            <img alt="" src="/templates/images/logo.png">
                        </div>
                    </div>
                    <div class="col-md-7 col-xs-7">
                        <div class="search">
                            <form method="post" action="/search/">
                                <div class="input">
                                    <input type="text" onblur="$(this).attr('placeholder','Введите товар, категорию или бренд')" onfocus="$(this).attr('placeholder','')" placeholder="Введите товар, категорию или бренд" autocomplete="off" maxlength="64" value="" name="search">
                                </div>
                                <div class="button" onclick="$(this).parents('form').submit();"></div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2">
                        <?=\app\components\WBasketSmall::widget()?>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
              <div class="clear"></div>
            </div>
        </div>
        <div id="menu-top">
            <div class="items">
                <div class="catalog-goods item">
                    <div class="menu-icon catalog"> </div>
                    <div class="container-menu">
                        <?= \app\components\WGeneralCatalogMenu::widget(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="center">
            <div class="content">
                <?= Breadcrumbs::widget([
                    'homeLink' => ['label' => 'Главная', 'url' => '/'],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= $content ?>
            </div>
        </div>

    </div>
















<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Esalad <?= date('Y') ?></p>


        <!-- Modal "Записаться на занятия" -->
        <div class="modal fade" id="my-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <!-- Modal "Записаться на занятия" -->
        <div class="modal fade" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
<?php
//print_r(\app\models\Basket::getSmallBasket());
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
