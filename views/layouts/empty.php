<?php

/* @var $this \yii\web\View */
/* @var $content string */
use app\assets\AppAsset;
use app\modules\common\models\ModFunctions;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\pages\models\SignupForm;

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
    <link rel="stylesheet" type="text/css" href="/css/styles.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/global.css?rand=<?=time();?>" />
    <link rel="stylesheet" type="text/css" href="/css/my.css?rand=<?=time();?>" />
    <meta name="yandex-verification" content="7ef6ad82f324d076" />
    <meta name="yandex-verification" content="59b3df0bbfde7230" />
    <meta name="yandex-verification" content="7ef8ba088516092f" />

    <title><?= Html::encode($this->title) ?></title>

</head>

<body>

<?php $this->beginBody() ?>
<?=$content?>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
