<?php

namespace app\modules\common\controllers;

use Yii;
use yii\web\Controller;

/**
 * UserController implements the CRUD actions for Useradmin model.
 */
class BackendController extends GeneralShopController
{
    public $layout = 'control-page';

    public $actionsMenu;
    public $actionsShopOwnerMenu;

    public function init()
    {
        parent::init();

        $this->actionsShopOwnerMenu = [
        'products' => [
            'title' => Yii::t('admin','Личный кабинет'),
            'link'=>'/shop/index',
            'status' => 1,
            'items' => [
                [
                    'link' => '/shop/index',
                    'title' => Yii::t('admin','Управление магазином'),
                ],
                [
                    'link' => '/shop/product-list',
                    'title' => Yii::t('admin','Товары'),
                ],
                [
                    'link' => '/shop/order-report',
                    'title' => Yii::t('admin','Заказы'),
                ],
                [
                    'link' => '/shop/statistics',
                    'title' => Yii::t('admin','Статистика продаж'),
                ],

                [
                    'link' => '/shop/edit-store',
                    'title' => Yii::t('admin','Редактировать остатки на складах'),
                ],
                [
                    'link' => '/shop/shop-params',
                    'title' => Yii::t('admin','Настройки'),
                ],
//                [                
//                    'link' => '/shop/edit-store',
//                    'title' => Yii::t('admin','Управление остатками на складах'),
//                ],
            ],
        ],
    ];

    $this->actionsMenu = [
        'main' => [
            'title' => Yii::t('admin','Панель управления'),
            'link' => '/shop-management/index',
            'status' => 1,
            'items' => [
                [
                    'link' => '/shop-management/index',
                    'title' => Yii::t('admin','Панель управления'),
                ],
            ],
        ],

        'crm' => [
            'title' => Yii::t('admin','CRM'),
            'link' => '/crm/index',
            'status' => 1,
            'items' => [
                [
                    'link' => '/crm/index',
                    'title' => Yii::t('admin','Управление задачами'),
                ],
                [
                    'link' => '/crm/report',
                    'title' => Yii::t('admin','Отчет о звонках'),
                ],
            ],
        ],
        'actions' => [
            'title' => Yii::t('admin','Акции'),
            'link' => '/actions/actions',
            'status' => 1,
            'items' => [
                [
                    'link' => '/actions/actions/create',
                    'title' => Yii::t('admin','Добавить новую акцию'),
                ],
                [
                    'link' => '/actions/accumulation/',
                    'title' => Yii::t('admin','Отчет по накоплениям'),
                ],
                [
                    'link' => '/actions-present-save',
                    'title' => Yii::t('admin','Сброс отказов от акций'),
                ],

            ],

        ],

        'category' => [
            'title' => Yii::t('admin','Категории'),
            'status' => 1,
            'link'=>'/category',
            'items' => [
                [
                    'link' => '/category',
                    'title' => Yii::t('admin','Управление категориями'),
                ],
                [
                    'link' => '/category/create',
                    'title' => Yii::t('admin','Новая категория'),
                ],
                [
                    'link' => '/category/index-page',
                    'title' => Yii::t('admin','Управление страницами'),
                ],
            ],
        ],

        'products' => [
            'title' => Yii::t('admin','Продукты'),
            'link' => '/product/index',
            'status' => 1,
            'items' => [
                [
                    'link' => '/product/index',
                    'title' => Yii::t('admin','Управление продуктами'),
                ],
                [
                    'link' => '/product/create',
                    'title' => Yii::t('admin','Новый продукт'),
                ],
                [
                    'link' => '/tags',
                    'title' => Yii::t('admin','Управление свойствами товаров'),
                ],
                [
                    'link' => '/sticker',
                    'title' => Yii::t('admin','Стикеры'),
                ],
            ],
        ],

        'lists' => [
            'title' => Yii::t('admin','Управление списками'),
            'link'=>'/list',
            'status' => 1,
            'items' => [
                [
                    'link' => '/list',
                    'title' => Yii::t('admin','Управление списками'),
                ],
                [
                    'link' => '/list/create',
                    'title' => Yii::t('admin','Новый список'),
                ],
            ],
        ],


        'shops' => [
            'title' => Yii::t('admin','Магазины'),
            'status' => 1,
            'link'=>'/shops',
            'items' => [
                [
                    'link' => '/shops',
                    'title' => Yii::t('admin','Управление магазинами'),
                ],
                [
                    'link' => '/shops/create',
                    'title' => Yii::t('admin','Новый магазин'),
                ],
            ],
        ],

        'shop-groups' => [
            'title' => Yii::t('admin','Группы магазинов'),
            'status' => 1,
            'link'=>'/shop-management/shop-groups',
            'items' => [
                [
                    'link' => '/shop-management/shop-groups',
                    'title' => Yii::t('admin','Управление группами магазинов'),
                ],
                [
                    'link' => '/shop-management/shop-group-create',
                    'title' => Yii::t('admin','Новая группа магазинов'),
                ],
            ],
        ],

        'promo' => [
            'title' => Yii::t('admin','Промо-коды'),
            'status' => 1,
            'link' => '/shop-management/promo-code-statistic?CodesSearch[usetype]=0&CodesSearch[club]=0&CodesSearch[dateStart]='.date('d.m.Y',strtotime('- 1 month')).'&CodesSearch[dateStop]='.date('d.m.Y'),
            'items' => [
                [
                    'link' => '/shop-management/promo-code-statistic?CodesSearch[usetype]=0&CodesSearch[club]=0&CodesSearch[dateStart]='.date('d.m.Y',strtotime('- 1 month')).'&CodesSearch[dateStop]='.date('d.m.Y'),
                    'title' => Yii::t('admin','Статистика по промокодам'),
                ],
                [
                    'link' => '/shop-management/promo-code-statistic-export?CodesSearch[usetype]=0&CodesSearch[club]=0&CodesSearch[dateStart]='.date('d.m.Y',strtotime('- 1 month')).'&CodesSearch[dateStop]='.date('d.m.Y'),
                    'title' => Yii::t('admin','Статистика по промокодам (экспорт)'),
                ],
                [
                    'link' => '/codes/index?sort=-code',
                    'title' => Yii::t('admin','Управление промокодами'),
                ],
                [
                    'link' => '/codes/order',
                    'title' => Yii::t('admin','Список промокодов'),
                ],
            ],
        ],


        'seo' => [
            'title' => Yii::t('admin','SEO инструменты'),
            'status' => 1,
            'link' => '/seo-tool',
            'items' => [
                [
                    'link' => '/seo-tool/',
                    'title' => Yii::t('admin','Автолинк-тэги'),
                ],
                [
                    'link' => '/seo-tool/banners',
                    'title' => Yii::t('admin','Список баннеров'),
                ],
                [
                    'link' => '/seo-tool/index-xml',
                    'title' => Yii::t('admin','Выгрузка YML/XML'),
                ],

            ],
        ],

        'analytics' => [
            'title' => Yii::t('admin','Аналитика'),
            'status' => 1,
            'link' => '/analytics',
            'items' => [
                [
                    'link' => '/analytics/',
                    'title' => Yii::t('admin','Просмотр аналитики'),
                ],
            ],
        ],

        'questionnaire' => [
            'title' => Yii::t('admin','Анкета'),
            'status' => 1,
            'link' => '/questionnaire-answers',
            'items' => [
                [
                    'link' => '/questionnaire-questions',
                    'title' => Yii::t('admin','Вопросы'),
                ],
                [
                    'link' => '/questionnaire-answers',
                    'title' => Yii::t('admin','Ответы'),
                ],
            ],
        ],

        'reports' => [
            'title' => Yii::t('admin','Отчеты'),
            'status' => 0,
            'link' => '/reports/order',
            'items' => [
                [
                    'link' => '/reports/order-new',
                    'title' => Yii::t('admin','Отчет о ПРОДАЖАХ'),
                ],  
                [
                    'link' => '/reports/order',
                    'title' => Yii::t('admin','Старый отчет о продажах'),
                ],
                [
                    'link' => '/reports/mini-order-new',
                    'title' => Yii::t('admin','Мини-отчет о ПРОДАЖАХ'),
                ],
                [
                    'link' => '/reports/new-goods',
                    'title' => Yii::t('admin','Отчет о добавленных товарах'),
                ],
/*                [
                    'link' => '/reports/reports-users',
                    'title' => Yii::t('admin','Отчет о добавленных товарах (пользователи)'),
                ],*/
                [
                    //старая ссылка
                    // 'link' => '/reports/delivery',
                    'link' => 'http://helper.express/admin/order',
                    'title' => Yii::t('admin','Просмотр отчета о доставке'),
                ],
                [
                    'link' => '/reports/profile-report',
                    'title' => Yii::t('admin','Отчет по Профайлам'),
                ],
                [
                    'link' => '/reports/find-order-list',
                    'title' => Yii::t('admin','Приемка товаров'),
                ],
                [
                    'link' => '/reports/shopsgoods',
                    'title' => Yii::t('admin','Отчет по товарам'),
                ],
                [
                    'link' => '/reports/preorder',
                    'title' => Yii::t('admin','Предзаказ'),
                ],
                [
                    'link' => '/reports/shame-board',
                    'title' => Yii::t('admin','Доска позора'),
                ],
                [
                    'link' => '/reports/abandoned-basket-report',
                    'title' => Yii::t('admin','Просмотр отчета о брошенных корзинах'),
                ],
                [
                    'link' => '/reports/master-statistic',
                    'title' => Yii::t('admin','Просмотр статистики работы мастера продаж'),
                ],
                [
                    'link' => '/reports/master-pay',
                    'title' => Yii::t('admin','Статистика покупок в мастере продаж'),
                ],
                [
                    'link' => '/reports/real-clients',
                    'title' => Yii::t('admin','Отчёт о реальных клиентах (НЕ товар ЭкстримФитнес)'),
                ],
                [
                    'link' => '/reports/real-clients-2',
                    'title' => Yii::t('admin','Отчёт о реальных клиентах (Товар ЭкстримФитнес)'),
                ],
                [
                    'link' => '/reports/reports-orders-month',
                    'title' => Yii::t('admin','Отчет о заказах по месяцам'),
                ],
                [
                    'link' => '/reports/sales-report',
                    'title' => Yii::t('admin','Просмотр отчета о продажах (общий)'),
                ],
                [
                    'link' => '/reports/category-product',
                    'title' => Yii::t('admin','Товары по категориям'),
                ],

            ],
        ],

        'support' => [
            'title' => Yii::t('admin','Техподдержка'),
            'status' => 1,
            'link' => '/support',
            'items' => [
                [
                    'link' => '/support/',
                    'title' => Yii::t('admin','Заявки для обратной связи'),
                    'id' => 1001
                ],
                [
                    'link' => '/support/error-message',
                    'title' => Yii::t('admin','Сообщение об ошибке'),
                ],
                [
                    'link' => '/support/call',
                    'title' => Yii::t('admin','Заявки для заказа звонка'),
                    'id' => 1002
                ],
                [
                    'link' => '/support/comments',
                    'title' => Yii::t('admin','Комментарии'),
                    'id' => 1003
                ],
                [
                    'link' => '/support/feed',
                    'title' => Yii::t('admin','Отзывы'),
                    'id' => 1004
                ],
                [
                    'link' => '/support/sms-send',
                    'title' => Yii::t('admin','Отправка СМС'),
                    'id' => 1005
                ],
            ],
        ],
        'users' => [
            'title' => Yii::t('admin','Пользователи'),
            'status' => 1,
            'link' => '/user',
            'items' => [
                [
                    'link' => '/user',
                    'title' => Yii::t('admin','Управление пользователями'),
                ],
                [
                    'link' => '/user/create',
                    'title' => Yii::t('admin','Добавить пользователя'),
                ],
            ],
        ],

        'transactions' => [
            'title' => Yii::t('admin','Транзакции'),
            'status' => 1,
            'link' => '/transactions/index?sort=-date',
            'items' => [
                [
                    'link' => '/users-credits/index',
                    'title' => Yii::t('admin','Зачисления сотрудникам по З.П.'),
                ],
                [
                    'link' => '/transactions/index?sort=-date',
                    'title' => Yii::t('admin','Управление денежными транзакциями'),
                ],
                [
                    'link' => '/transactions/bonus?sort=-date',
                    'title' => Yii::t('admin','Управление бонусными транзакциями'),
                ],
//                [
//                    'link' => '/transactions/create',
//                    'title' => 'Добавить денежную транзакцию',
//                ],
                [
                    'link' => '/transactions/create-bonus',
                    'title' => Yii::t('admin','Добавить бонусную транзакцию'),
                ],
            ],
        ],


        'logs' => [
            'title' => Yii::t('admin','Логи БД'),
            'status' => 1,
            'link' => '/logs/index?sort=-time',
            'items' => [
                [
                    'link' => '/logs/index?sort=-time',
                    'title' => Yii::t('admin','Просмотр логов'),
                ],
            ],
        ],


        'fake' => [
            'title' => Yii::t('admin','Подмена пользователя'),
            'status' => 1,
            'link' => '/site/login-god',
            'items' => [
                [
                    'link' => '/site/login-god',
                    'title' => Yii::t('admin','Подмена пользователя'),
                ],
            ],
        ],



    ];


    }

}
