<?php
/**
 * @link http://zloradnij.ru/
 * @copyright Copyright (c) 2015 Studio 90-is
 * @license http://zloradnij.ru/license/
 */

namespace app\assets;

use app\modules\common\models\Zloradnij;
use yii\web\AssetBundle;

class MobileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/styles.css',
        'css/my.css',
        'css/shop-management/jquery-ui.min.css',
        'css/global.css',
    ];

    public $js = [
        'scripts/jquery.zoom.js',
        'scripts/slider/slider.js',
        'js/config.js',
        'js/shop.js',
        'js/global.js',
        'js/scripts.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        $this->css = [
            'css/global.css',
            'css/my.css',
            'css/my.css',
            'css/mobile/m-styles.css',
            'css/mobile/screen.css',

        ];
        $this->js = [
            'js/callback.js',
            'js/config.js',
            'js/catalog.js?'.time(),
            'js/shop.js?'.time(),
            'js/basket.js?'.time(),
            'https://api-maps.yandex.ru/2.1/?lang=ru_RU',
            'js/polygon.js',
            'js/mobile/m-script.js?'.time(),
            'scripts/slider/slider.js',
            'js/global.js?'.time(),
            'scripts/jquery.date.js',
        ];
        parent::init(); // TODO: Change the autogenerated stub
    }
}