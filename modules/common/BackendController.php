<?php

namespace app\modules\common\controllers;

use Yii;
use yii\web\Controller;
use app\modules\catalog\models\Category;
use app\modules\basket\models\BasketOne;
use app\modules\catalog\models\Catalog;

/**
 * UserController implements the CRUD actions for Useradmin model.
 */
class BackendController extends Controller
{
    public $layout = 'control-page';

    public $actionsMenu = [
        'products' => [
            'title' => '<b>Продукты</b>',
            'items' => [
                [
                    'link' => '/product/index',
                    'title' => 'Управление продуктами',
                ],
                [
                    'link' => '/product/create',
                    'title' => 'Новый продукт',
                ],
                [
                    'link' => '/list',
                    'title' => 'Управление списками',
                ],
                [
                    'link' => '/tags',
                    'title' => 'Управление свойствами товаров',
                ],
            ],
        ],

        'category' => [
            'title' => '<b>Категории</b>',
            'items' => [
                [
                    'link' => '/category',
                    'title' => 'Управление категориями',
                ],
                [
                    'link' => '/category/create',
                    'title' => 'Новая категория',
                ],
            ],
        ],
        'shops' => [
            'title' => '<b>Магазины</b>',
            'items' => [
                [
                    'link' => '/shops',
                    'title' => 'Управление магазинами',
                ],
                [
                    'link' => '/shops/create',
                    'title' => 'Новый магазин',
                ],
            ],
        ],

        'shop-groups' => [
            'title' => '<b>Группы магазинов</b>',
            'items' => [
                [
                    'link' => '/shop-management/shop-groups',
                    'title' => 'Управление группами магазинов',
                ],
                [
                    'link' => '/shop-management/shop-group-create',
                    'title' => 'Новая группа магазинов',
                ],
            ],
        ],


        'seo' => [
            'title' => '<b>SEO инструменты</b>',
            'items' => [
                [
                    'link' => '#',
                    'title' => 'Автолинк-тэги',
                ],
            ],
        ],

        'promo' => [
            'title' => '<b>Промо-коды</b>',
            'items' => [
                [
                    'link' => '/shop-management/promo-code-statistic',
                    'title' => 'Статистика по промокодам',
                ],
            ],
        ],
        'transactions' => [
            'title' => '<b>Транзакции</b>',
            'items' => [
                [
                    'link' => '/tags',
                    'title' => 'Управление транзакциями',
                ],
            ],
        ],
        'reports' => [
            'title' => '<b>Отчеты</b>',
            'items' => [
                [
                    'link' => '/reports/sales-report',
                    'title' => 'Просмотр отчета о продажах (общий)',
                ],
                [
                    'link' => '/reports/order',
                    'title' => 'Просмотр отчета о продажах',
                ],
                [
                    'link' => '/reports/delivery',
                    'title' => 'Просмотр отчета о доставке',
                ],
            ],
        ],
        'logs' => [
            'title' => '<b>Логи БД</b>',
            'items' => [
                [
                    'link' => '/logs',
                    'title' => 'Просмотр логов',
                ],
            ],
        ],
        'users' => [
            'title' => '<b>Пользователи</b>',
            'items' => [
                [
                    'link' => '/user',
                    'title' => 'Управленеие пользователями',
                ],
            ],
        ],


    ];

    public $catalogMenu;
    public $catalogHash;
    public $basket;

    public function init() {
        $this->catalogHash = Category::find()->where(['active' => 1])->orderBy('level, sort')->indexBy('id')->asArray()->all();
        $urls = [];
        $this->catalogMenu = Catalog::buildTree($this->catalogHash,$urls);
        $this->basket = BasketOne::initBasket();

        parent::init();
    }

    public function actionGoHome()
    {
        echo '++';
        return $this->redirect('site/index');
    }
}
