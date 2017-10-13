<?php
/*
 *
 *
 *  Internal shop version 1.1.1500
 *
 *
 * */

namespace app\assets;

use app\modules\common\models\Zloradnij;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [

            'css/styles.css',
            'css/my.css',
            'scripts/slider/slider.css',
            'css/shop-management/jquery-ui.min.css',
            'css/global.css',
            'css/screen.css',
            'css/shop-management/admin_screen.css',
            'css/shop-management/shop.css',
            'css/catalog.css',
            'css/green_styles.css',

    ];
        // Сайт;
    public $js = [
           'scripts/slider/slider.js',
            'js/config.js',
            'js/shop-management/cms.js',
            'js/shop.js',
            'js/catalog.js',
            'js/global.js',
            'js/scripts.js',
            'js/mobile.js',
            'https://api-maps.yandex.ru/2.1/?lang=ru_RU',
            'js/polygon.js',
            'js/wishlist.js',

            'scripts/jquery.date.js',
            'scripts/jquery.zoom.js',
            'scripts/jquery.custom.js',
            'scripts/progressbar.js',
            'scripts/touch-zoom.js',

    ];

    // Менеджер ПУ ;
    public function init()
    {

        if(\Yii::$app->controller->module->uniqueId == 'catalog'){
            $this->js = [];
            $this->css = [];
            $this->depends = [];
        }

        if(\Yii::$app->controller->module->uniqueId == 'basket'){
            $this->js = [];
            $this->css = [];
            $this->depends = [];
        }

        if(\Yii::$app->controller->uniqueId == 'control/orders' || \Yii::$app->controller->uniqueId == 'control/orders'){
            $this->css = [
                'css/styles.css',
                'css/global.css',
            ];
            $this->js = [
                'js/config.js',
                'js/shop.js?'.time(),
                'scripts/slider/slider.js',
                'scripts/jquery.calendar.js',
                'scripts/jquery.date.js',
                'js/control/control-orders.js?'.time(),
            ];
        }else{
            if(\Yii::$app->controller->layout == 'shop-control-page'){
                $this->css = [
                    'css/styles.css',
                    'css/my.css',
                    'css/global.css',
                    'css/shop-management/shop.css',
                    'css/shop-management/cms.css',
                    'css/shop-management/jquery-ui.min.css',
                    'css/shop-management/jquery-ui.structure.min.css',
                    'css/shop-management/jquery-ui.theme.min.css',
                    'css/screen.css?'.time(),
                    'css/shop-management/admin_screen.css?'.time(),

                ];
                $this->js = [
                    'js/config.js',
                    'js/shop.js',
                    'js/catalog.js',
                    'js/global.js',
                    'js/mobile.js?'.time(),
                    'js/shop-management/dropzone.js',
                    'js/shop-management/shopStatistic.js',
                    'scripts/jquery.calendar.js',
                    'scripts/jquery.date.js',
                    'scripts/jquery-ui.min.js',
                    'js/shop-management/cms.js',
                    'js/shop-management/manager.js',
                    'js/shop-management/shopScripts.js',
                    'js/shop-management/numeral.min.js',
                    'js/shop-management/languages/ru.min.js',
                ];
            }

            if(\Yii::$app->controller->layout == 'control-page'){
                $this->css = [
                    'css/styles.css',
                    'css/my.css',
                    'css/global.css',
                    'css/shop-management/shop.css',
                    'css/shop-management/cms.css',
                    'css/screen.css?'.time(),
                    'css/shop-management/admin_screen.css?'.time(),
                    'css/shop-management/jquery-ui.min.css?'.time(),
                    'css/shop-management/jquery-ui.structure.min.css',
                    'css/shop-management/jquery-ui.theme.min.css',
                    //'css/green_styles.css',
                ];
                $this->js = [
                    'js/config.js',
                    'js/shop.js',
                    'js/catalog.js',
                    'js/global.js',
                    'js/mobile.js?'.time(),
                    'js/shop-management/dropzone.js',
                    'js/shop-management/shopStatistic.js',
                    'scripts/jquery.zoom.js',
                    'scripts/jquery.calendar.js',
                    'scripts/jquery.date.js',
                    'scripts/jquery-ui.min.js',
                    'js/shop-management/cms.js',
                    'js/shop-management/manager.js',
                    'js/shop-management/shopScripts.js',
                    'js/shop-management/numeral.min.js',
                    'js/shop-management/languages/ru.min.js',
                    'js/crm.js',
                ];
            }

            // Мобильная версия;
            if(\Yii::$app->controller->layout == 'mobile-page' || \Yii::$app->controller->layout == '@app/views/layouts/mobile-page'){
                $this->css = [
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
            }
        }

        parent::init(); // TODO: Change the autogenerated stub
    }
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    //public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
