<?php

namespace app\controllers;

use app\modules\common\controllers\BackendController;
use app\modules\common\models\Messages;
use app\modules\common\models\MessagesSearch;
use app\modules\common\models\UsersSmsLogs;
use app\modules\questionnaire\models\Users;
use app\modules\shop\models\Orders;
use app\modules\common\models\User;
use app\modules\shop\models\OrdersItems;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\common\models\Api;


class SupportController extends BackendController
{
    public $defaultAction = 'support';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'support',
                            'call',
                            'feed',
                            'comments',
                            'error-message',
                            'update',
                            'view',
                            'compliment',
                            'sms-alert',
                            'sms-send'
                        ],
                        'allow'   => true,
                        'roles'   => ['GodMode', 'conflictManager', 'callcenterOperator', 'categoryManager'],
                    ],
                    [
                        'actions' => [
                            'cron-sms-alert',
                        ],
                        'allow'   => true,
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
        ];
    }

    // Список отзывы;
    public function actionSupport()
    {
        // Загрузка Обратная связь;
        $searchModel = new MessagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '1002');
        // Обновления статус;
        if (Yii::$app->request->post('support')) {
            $id = Yii::$app->request->post('id');
            $active = Yii::$app->request->post('active');
            // Обновляем поле active 1-0;
            if ($support = Messages::findOne(['id' => $id])) {
                $support->active = $active;
                if ($support->save(false)) {
                    // Если совпадает номер заказа обновляем поле negative_review 1-0;
                    if ($order = Orders::findOne(['user_id' => $support->user_id, 'id' => intval($support->order)])) {
                        $order->negative_review = $active;
                        $order->save(false);
                    }
                    // Обновляем поле compliment 1-0;
                    if ($user = User::findOne($support->user_id)) {
                        $user->compliment = $active;
                        $user->save(false);
                    }
                }
            }

            //print_arr($status);
            //  return $this->redirect(['/support/call']);
        }

        // Загрузка в шаблон;
        return $this->render('support', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionFeed()
    {
        // Загрузка Обратная связь;
        $searchModel = new MessagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '1003');

        // Загрузка в шаблон;
        return $this->render('feed', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // Заказать звонок;
    public function actionCall()
    {
        // Загрузка заявки номер тел.;
        $searchModel = new MessagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '1000');
        // Обновления статус;
        if (Yii::$app->request->post('call')) {
            $connection = Yii::$app->getDb();
            $connection->createCommand("UPDATE `messages` SET `status`= '" . Yii::$app->request->post('status') . "' WHERE `id` = '" . Yii::$app->request->post('id') . "' AND `type_id` = '1000'")->execute();
            //  return $this->redirect(['/support/call']);
        }

        // Загрузка в шаблон;
        return $this->render('call', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // Список отзывы;
    public function actionComments()
    {
        // Обновления статус;
        if (Yii::$app->request->post('comments')) {
            $connection = Yii::$app->getDb();
            $connection->createCommand("UPDATE `goods_comments` SET `status`= '" . Yii::$app->request->post('status') . "' WHERE `id` = '" . Yii::$app->request->post('id') . "'")->execute();
        }

        // Загрузка в шаблон;
        return $this->render('comments');
    }

    // Список завяки ошибки по сайту;
    public function actionErrorMessage()
    {
        // Загрузка Обратная связь;
        $searchModel = new MessagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '1001');

        // Загрузка в шаблон;
        return $this->render('error-message', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /*----Редактирования формы----*/
    // Просмотра;
    public function actionView($id)
    {
        //Меню;
        return $this->render('view', [
            'model' => $this->findModel($id),
            'menu'  => $this->actionsMenu['support']
        ]);
    }

    // Сохранения данные;
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Messages::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCompliment($user_id, $switch)
    {
        if (!empty(Yii::$app->request->get('user_id')) && !empty(Yii::$app->request->get('switch')) && Yii::$app->request->isAjax) {
            $user = User::findOne(intval($user_id));
            $switch = (int)($switch === 'true');
            $user->compliment = $switch;
            $user->save();

            return 'success';
        } else {
            return 'fail';
        }
    }

    public function actionSmsAlert()
    {
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionCronSmsAlert()
    {
        /*        $sapi = php_sapi_name();
                if ($sapi=='cli') {
        SELECT users.id as user_id, users.name as user_name, orders.date as order_date, goods_variations.servingforday FROM users
        LEFT JOIN orders ON orders.user_id = users.id
        LEFT JOIN orders_groups ON orders_groups.order_id = orders.id
        LEFT JOIN orders_items ON orders_items.order_group_id = orders_groups.id
        LEFT JOIN goods_variations ON goods_variations.good_id = orders_items.good_id
        WHERE orders.status = 1
        GROUP BY orders.id
                }*/
        $arr_type = [123, 123];

        $order = Orders::findAll([123, 123])->getOrdersGoods();
        $arr_type = '';
        foreach ($order as $o) {
            $arr_type[] = $o['type_id'];
        }

        // Определние самого частого типа
        function mostTypeID($x)
        {
            $counted = array_count_values($x);
            arsort($counted);

            return (key($counted));
        }

        echo mostTypeID($arr_type);
        /*        echo '<pre>';
                print_r($order);
                echo '</pre>';*/

    }

    public function actionSmsSend($user_id = null)
    {

        if (Yii::$app->request->isAjax) {
            $tel = Yii::$app->request->get('phone');
            $text = Yii::$app->request->get('text');

            $user = (!empty(Yii::$app->user->id)) ? Yii::$app->user->id : '' ;

            if (!empty($tel) && !empty($text)) {

                // Log
                $logs_sms = new UsersSmsLogs();
                $logs_sms->user_id = $user;
                $logs_sms->phone = $tel;
                $logs_sms->text = $text;
                $logs_sms->save();

                $api = new Api();
                $api->sms($tel, $text);
            } else {
                echo 'Ошибка! Отсутствуют параметры';
            }

            echo 'Сообщение успешно отправлено!';
            //print_r($tel);
            die();
        }

        $u = '';
        if (!empty($user_id)) {
            $user = User::findOne(intval($user_id));
            $u = ['phone' => $user->phone, 'name' => $user->name];
        }

        return $this->render('sms-send', [
            'user_id' => $user_id,
            'u'       => $u
        ]);

    }

}