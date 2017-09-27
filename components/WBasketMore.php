<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use app\modules\pages\models\PagesMenus;
use yii\helpers\Url;

/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "product-item" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WBasketMore extends Widget
{
    public function run(){
        $params = array_replace($_GET, ['page-size' => $count]);
        if (isset($params['page'])) unset($params['page']);
        $params = array_merge(['/product/status-list'],$params);
        $links[] = Html::a($count,\yii\helpers\Url::toRoute($params));

        if(!empty(Yii::$app->session['confirmBasketAction']) && Yii::$app->session['confirmBasketAction'] == 'moreBasket'){?>
            <div class="col-xs-12 col-sm-10 col-md-6 col-lg-5">
                <h3>Выберите действие</h3>
                <span class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center"><a href="<?= Url::to('')?>"></a>Объединить корзины</span>
                <span class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center"><a href="<?= Url::to('')?>"></a>Забыть старую корзину</span>
                <span class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center"><a href="<?= Url::to('')?>"></a>Вернуть старую корзину</span>
            </div><?php
        }
    }
}
