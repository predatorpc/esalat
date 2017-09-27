<?php

namespace app\modules\control\controllers;

use app\modules\basket\models\BasketLg;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\User;
use app\modules\common\models\Zloradnij;
use app\modules\control\models\UserBalance;
use app\modules\control\models\UserQuery;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


class OrdersController extends BackendController{
    public $layout = '@app/views/layouts/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'ok-payment',
                            'fail-payment',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'index',
                            'update',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $orderFilter = false;
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'orderFilter' => $orderFilter,
        ]);
    }

    public function actionUpdate($id)
    {
//        $this->view->registerJsFile('/js/control/control-orders.js');
        $order = Orders::findOne($id);
        if(!$order){
            return $this->render('/errors/error',[
                'name' => 'Заказ №'.$id.' не найден',
                'message' => 'Заказ №'.$id.' не найден. Скорее всего, вы неправильно скопировали ID заказа',
            ]);
        }

        $flagDelivery = $flagAddress = false;
        foreach ($order->ordersGroups as $ordersGroup) {
            if($ordersGroup->delivery_id > 1005){
                $flagDelivery = $ordersGroup->delivery_id;
                $flagAddress =  $ordersGroup->address_id;
            }
        }

        $orderBasket = Yii::$app->session->get('admin',[]);
        $orderBasket['edit']['order'][$order->id]['basket'] = [
            'user_id' => $order->user_id,
            'delivery_id' => ($flagDelivery && $flagDelivery != $order->ordersGroups[0]->delivery_id) ? $flagDelivery : $order->ordersGroups[0]->delivery_id,
            'delivery_price' => $order->deliveryPrice,
            'address_id' => ($flagDelivery && $flagDelivery != $order->ordersGroups[0]->delivery_id) ? $flagAddress : $order->ordersGroups[0]->address_id,
            'payment_id' => 1,
            'promo_code_id' => $order->code_id,
            'time_list' => [],
            'comment' => $order->comments,
            'status' => 1,
        ];

        foreach ($order->ordersItems as $ordersItem) {
            $orderBasket['edit']['order'][$order->id]['products'][] = [
                'user_id' => $order->user_id,
                'delivery_id' => ($flagDelivery && $flagDelivery != $order->ordersGroups[0]->delivery_id) ? $flagDelivery : $order->ordersGroups[0]->delivery_id,
                'delivery_price' => $order->deliveryPrice,
                'address_id' => ($flagDelivery && $flagDelivery != $order->ordersGroups[0]->delivery_id) ? $flagAddress : $order->ordersGroups[0]->address_id,
                'payment_id' => 1,
                'promo_code_id' => $order->code_id,
                'time_list' => [],
                'comment' => $order->comments,
                'status' => 1,
            ];
        }

        Yii::$app->session->set('admin',$orderBasket);
        Zloradnij::print_arr(Yii::$app->session->get('admin'));
//        Zloradnij::print_arr($_SESSION);
        $userBalance = new UserBalance($order);

        $userQuery = UserQuery::findOne($order->user_id);
//        Zloradnij::print_arr($userQuery);
//        $lastOrder = $userQuery->getLastOrder()->one();
//
//        Zloradnij::print_arr($lastOrder);
//        Zloradnij::print_arr($userQuery->money);
//        Zloradnij::print_arr($userQuery->getSumUserPays());
//        Zloradnij::print_arr($lastOrder->date);
//        Zloradnij::print_arr($userQuery->getSumUserPaysBeforeOrder($order));
//        print '---------------------------------';
//        Zloradnij::print_arr($userQuery->getUserPays());
//        Zloradnij::print_arr($userQuery->getUserPaysBeforeOrder($order));
//        print '---------------------------------';
//        Zloradnij::print_arr($userQuery->getSumUserBonusBeforeOrder($order));


//        foreach ($dataProvider as $item) {
//            Zloradnij::print_arr($item);
//        }
//        Zloradnij::print_arr($userQuery->getModel()->limit(3)->all());

        return $this->render('update',[
            'order' => $order,
            'orderBasket' => $orderBasket,
            'userQuery' => $userQuery,
            'userBalance' => $userBalance,
        ]);
    }
}
