<?php
/**
 * @link http://zloradnij.ru/
 * @copyright Copyright (c) 2015 Studio 90-is
 * @license http://zloradnij.ru/license/
 */

namespace app\assets;

use app\modules\common\models\Zloradnij;
use yii\web\AssetBundle;

class CatalogAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/styles.css',
        'css/my.css',
        'css/shop-management/jquery-ui.min.css',
        'css/global.css',
        'css/screen.css',
        'css/green_styles.css',
    ];

    //public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $js = [
        'scripts/jquery.zoom.js',
        'scripts/slider/slider.js',
        'scripts/jquery.custom.js',
        'js/config.js',
        'js/shop.js',
        'js/global.js',
        'js/scripts.js',
        'js/mobile.js',
        'js/wishlist.js',
        'scripts/progressbar.js',
        'scripts/touch-zoom.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}