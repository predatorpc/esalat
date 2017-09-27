<?php

namespace app\modules\common\controllers;

use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketShop;
use app\modules\catalog\models\Catalog;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\Tags;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\db\Connection;
use yii\web\Controller;

/**
 * UserController implements the CRUD actions for Useradmin model.
 */
class FrontController extends GeneralShopController
{
    public $layout = '@app/views/layouts/main';

    public function init()
    {
        // Транслит EN смена шаблона;
        if(Yii::$app->params['en']) {
            $this->layout = '@app/views/layouts/main_en';
        }

        parent::init();
    }
}
