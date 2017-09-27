<?php

//last version 10072016

namespace app\controllers;

use app\modules\catalog\models\Goods;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\Api;
use app\modules\common\models\Zloradnij;
use app\modules\shop\models\OrdersGroups;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ReportsController extends BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'orders_search',
                            'orders_search_items_add',
                            'orders_search_items_delete',
                            'orders_search_item_status',
                            'orders_search_item_return',
                            'orders_search_item_cancel',
                            'orders_delivery',
                            'orders_delivery_cancel',
                            'orders_delivery_double',
                            'orders_delivery_surcharge',
                            'orders_driver_set',
                            'orders_items',
                            'orders_items_status',
                            'orders_item_cancel',
                            'order',
                            'delivery',
                            'test-sma',
                            'delivery-plus-money',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager',  'conflictManager' , 'callcenterOperator', 'clubAdmin', 'helperManager'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionOrders_search()
    {
        $db = Yii::$app->getDb();
        $data = '';
        // Поиск магазина;
        if(!empty($_POST['search_name'])) {
            if ($_POST['search_name'] == 'shops' ){//&& !empty($_SESSION['filter']['shops'])) {
                // Загрузка магазина;
                $sql = "SELECT `id`, `name` FROM `shops` WHERE "
                    . 'name like \'%'. $_POST['search_value'] . '%\' '
                    . 'OR name_full LIKE \'% '. $_POST['search_value'] . '%\''

//                        ? "(`name` LIKE '%"
//                        . $_POST['search_value'] . "%' OR `name_full` LIKE '%"
//                        . $_POST['search_value'] . "%') AND " : "")
//                    . "`id` NOT IN ('" . implode(
//                        "', '", array_keys($_SESSION['filter']['shops'])
//                    )
                    . " ORDER BY `name` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }


            // Поиск покупателя;
            if ($_POST['search_name'] == 'users' ){//&& !empty($_SESSION['filter']['users'])) {
                // Загрузка покупателя;
                $sql = "SELECT `id`, `name` FROM `users` WHERE "
                    . 'name LIKE \'%'. $_POST['search_value'] . '%\' '
                    . 'OR phone LIKE \'% '. $_POST['search_value'] . '%\' '

//                    . ($_POST['search_value'] ? "(`name` LIKE '%"
//                        . $_POST['search_value'] . "%' OR `phone` LIKE '%"
//                        . $_POST['search_value'] . "%') AND " : "")
//                    . "`id` NOT IN ('" . implode(
//                        "', '", array_keys($_SESSION['filter']['users'])
                     . " ORDER BY `name` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }
            // Поиск промо-кода;
            if ($_POST['search_name'] == 'codes' ){//&& !empty($_SESSION['filter']['codes'])) {
                // Загрузка промо-кода;
                $sql = "SELECT `codes`.`id`, `codes`.`code` AS `name` FROM `codes` LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id` WHERE "
                    . "(`codes`.`code` LIKE '%". $_POST['search_value']. "%' OR `users`.`phone` LIKE '%" . $_POST['search_value'] . "%' "
                    ."OR `users`.`name` LIKE '%". $_POST['search_value'] . "%') "
                    ." ORDER BY `codes`.`code` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }
            // Поиск водителя;
            if ($_POST['search_name'] == 'drivers' ){//&& !empty($_SESSION['filter']['drivers'])) {
                // Загрузка водителя;
                $sql = "SELECT `id`, `name` FROM `users` WHERE "
                    ."`name` LIKE '%" . $_POST['search_value'] . "%'".
                    " OR `phone` LIKE '%". $_POST['search_value'] . "%'"
                    . " AND `driver` IS NOT NULL ORDER BY `name` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }
        }
        // Обработка данных;
        return json_encode($data);

    }

    public function actionOrders_search_items_add(){

        $db = Yii::$app->getDb();
        // Добавление магазина;
        if ($_POST['name'] == 'shops') {
            // Загрузка магазина;
            $sql = "SELECT `name` FROM `shops` WHERE `id` = '".intval($_POST['item_id'])."' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Добавление покупателя;
        if ($_POST['name'] == 'users') {
            // Загрузка покупателя;
            $sql = "SELECT `name` FROM `users` WHERE `id` = '".intval($_POST['item_id'])."' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Добавление промо-кода;
        if ($_POST['name'] == 'codes') {
            // Загрузка промо-кода;
            $sql = "SELECT `code` FROM `codes` WHERE `id` = '".intval($_POST['item_id'])."' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Добавление водителя;
        if ($_POST['name'] == 'drivers') {
            // Загрузка водителя;
            $sql = "SELECT `name` FROM `users` WHERE `id` = '".intval($_POST['item_id'])."' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Сохранение ID записи в сессии;
        $_SESSION['filter'][$_POST['name']][$_POST['item_id']] = $item_name;
        // Вывод данных;
        return $item_name;

    }

    public function actionOrders_search_items_delete(){
        // Удаление ID записи из сессии;
        unset($_SESSION['filter'][$_POST['name']][$_POST['item_id']]);
        return 0;
    }

    public function actionOrders_items_status(){

        $db = Yii::$app->getDb();

        // Загрузка данных;
        $sql = "SELECT `orders_items_status`.`date`, `orders_status`.`name` AS `status_name`, `users`.`name` AS `user_name` FROM `orders_items_status` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items_status`.`status_id` LEFT JOIN `users` ON `users`.`id` = `orders_items_status`.`user_id` WHERE `orders_items_status`.`order_item_id` = '".intval($_POST['order_item_status'])."' AND `orders_items_status`.`status` = '1' ORDER BY `orders_items_status`.`date` DESC";
        if ($status = $db->createCommand($sql)->queryAll()) {
            foreach ($status as $key=>$value) {
                // Обработка даты;
                $status[$key]['date'] = date("d.m.Y, H:i", strtotime($value['date']));
                // Обработка оператора;
                $status[$key]['user_name'] = $value['user_name'] ? $value['user_name'] : 'автоматически';
            }
        }
        // Подготовка данных;
        $data = array('status' => $status);
        // Обработка данных;
        $data = json_encode($data);
        // Вывод данных;
//        die($data);
        return $data;


    }

    public function actionOrders_item_return()
    {
        $db = Yii::$app->getDb();

        // Номер товара в заказе;
        $order_item_id = intval($_POST['order_item_return']);
        // Обновление статуса товара в заказе;
        $sql = "UPDATE `orders_items` SET `status_id` = '1008' WHERE `id` = '".$order_item_id."' LIMIT 1";
        $db->createCommand($sql)->execute();

        // Добавление статуса в историю;
        $sql = "INSERT INTO `orders_items_status` (`order_item_id`, `status_id`, `user_id`, `date`, `status`) VALUES ('".$order_item_id."', '1008', '".Yii::$app->user->getId()."', NOW(), '1')";
        $db->createCommand($sql)->execute();
        //$db->query($sql);
        // Вывод сообщения;
        die('Возврат товара выполнен');

    }

    public function actionOrders_item_cancel()
    {
        $db = Yii::$app->getDb();

        // Подключение модуля отправки почты;
//        include($_SERVER['DOCUMENT_ROOT'].'/systems/core/mail.php');
        // Номер товара в заказе;
        $order_item_id = intval($_POST['order_item_cancel']);
        // Загрузка заказанных товаров;
        $sql = "
SELECT
    `orders_items`.`id`,
    `orders`.`id` AS `order_id`,
    `orders`.`type`,
    `orders`.`code_id`,
    `orders`.`user_id`,
    `shops_stores`.`shop_id` AS `shop_id`,
    `goods`.`shop_id` AS `shop_id_old`,
    `goods`.`name`,
    `orders_items`.`good_id`,
    `orders_items`.`variation_id`,
    `orders_items`.`price`,
    `orders_items`.`discount`,
    `orders_items`.`bonus`,
    `orders_items`.`fee`,
    `orders_items`.`count`,
    `orders_groups`.`store_id`,
    `users`.`extremefitness`
FROM `orders_items`
LEFT JOIN `shops_stores` ON `shops_stores`.`id` = `orders_items`.`store_id`
LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id`
LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id`
LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`
LEFT JOIN `users` ON `users`.`id` = `orders`.`user_id`
WHERE
    `orders_items`.`id` = '".$order_item_id."' AND
    `orders_items`.`status` = '1' AND
    `orders`.`status` = '1'
LIMIT 1";
        if ($order_item = $db->createCommand($sql)->queryOne()) {
            if($order_item['shop_id'] == NULL){
                $order_item['shop_id'] = $order_item['shop_id_old'];
            }
            // Обновление товара в заказе;
            $sql = "UPDATE `orders_items` SET `status` = '0' WHERE `id` = '".$order_item['id']."' LIMIT 1";
            $db->createCommand($sql)->execute();

            // Обновление количества товара (на складе по умолчанию);
            $sql = "UPDATE `goods_counts` SET `count` = `count` + '".$order_item['count']."' WHERE `good_id` = '".$order_item['good_id']."' AND `variation_id` = '".$order_item['variation_id']."' AND `store_id` = (SELECT `id` FROM `shops_stores` WHERE `shop_id` = '".$order_item['shop_id']."' AND `main` = '1' LIMIT 1)";
           // debug('debug', $sql);
            $db->createCommand($sql)->execute();
            //$db->query($sql);
            // Загрузка покупателя;
            $sql = "SELECT * FROM `users` WHERE `id` = '".$order_item['user_id']."' LIMIT 1";
            if ($order_item['user'] = $db->createCommand($sql)->queryOne()) {
                // Рассчет суммы;
                $money = ($order_item['price'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'];
                // Перерасчет средств покупателя;
                $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('".$order_item['user']['id']."', '".$order_item['order_id']."', '5', '".$money."', 'Отмена заказа: #".$order_item['order_id']."', NOW(), '1')";
                $db->createCommand($sql)->execute();//$db->query($sql);
                // Проверка типа заказа;
                if ($order_item['type'] == 2) {
                    // Проверка привязки к абонементу ExtremeFitness;
                    if ($order_item['extremefitness']) {
                        // Перевод средств на абонемент ExtremeFitness;
                        $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('".$order_item['user']['id']."', '".$order_item['order_id']."', '2', '-".$money."', 'Перевод средств на ExtremeFitness', NOW(), '1')";
                        $db->createCommand($sql)->execute();//$db->query($sql);
                        // Зачисление средств на абонемент ExtremeFitness;
                        //$sql = "CALL pay('13', '".$order_item['extremefitness']."', '".$money."', NULL, NULL, 'Отмена заказа: #".$order_item['order_id']."')";
                        //$db->query($sql);
                    }
                } else {
                    // Обновление баланса покупателя;
                    $sql = "UPDATE `users` SET `money` = `money` + '".$money."' WHERE `id` = '".$order_item['user']['id']."' LIMIT 1";
//$db->query($sql);
                    $db->createCommand($sql)->execute();
                }
                // Проверка расхода бонусов;
                if ($order_item['bonus'] > 0) {
                    // Перерасчет бонусов покупателя;
                    $sql = "INSERT INTO `users_bonus` (`user_id`, `type`, `bonus`, `date`, `status`) VALUES ('".$order_item['user']['id']."', '0', '".($order_item['bonus'] * $order_item['count'])."', NOW(), '1')";
                    $db->createCommand($sql)->execute();//$db->query($sql);
                    // Обновление бонусов покупателя;
                    $sql = "UPDATE `users` SET `bonus` = `bonus` + '".($order_item['bonus'] * $order_item['count'])."' WHERE `id` = '".$order_item['user']['id']."' LIMIT 1";
                    $db->createCommand($sql)->execute();//$db->query($sql);
                }
            }
            // Загрузка менеджеров магазина;
            $sql = "SELECT `id`, `email` FROM `users` WHERE ((SELECT `id` FROM `users_roles` WHERE `user_id` = `users`.`id` AND `shop_id` = '".$order_item['shop_id']."' AND `status` = '1' LIMIT 1) OR (`manager` IS NOT NULL AND `level` >= '2')) AND `email` != '' AND `status` = '1' ORDER BY `id` ASC";
            $managers = $db->createCommand($sql)->queryAll();//$db->assoc($sql);
            // Проверка агентского вознаграждения по промо-коду;
            if ($order_item['fee'] > 0) {
                // Загрузка промо-кода;
                $sql = "SELECT `codes`.`id`, `codes`.`user_id`, `users`.`extremefitness` FROM `codes` LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id` WHERE `codes`.`id` = '".$order_item['code_id']."' LIMIT 1";
                if ($code = $db->createCommand($sql)->queryOne()) {
                    // Перерасчет средств агента;
                    $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('".$code['user_id']."', '".$order_item['order_id']."', '6', -'".($order_item['fee'] * $order_item['count'])."', 'Отмена комиссии: #".$order_item['order_id']."', NOW(), '1')";
                    $db->createCommand($sql)->execute();//$db->query($sql);

                    // Обновление баланса агента;
                    $sql = "UPDATE `users` SET `money` = `money` - '".($order_item['fee'] * $order_item['count'])."' WHERE `id` = '".$code['user_id']."' LIMIT 1";
                    //$db->query($sql);
                    $db->createCommand($sql)->execute();
                }
            }
            // Загрузка оставшихся товаров в заказе;
            $sql = "SELECT SUM((`orders_items`.`price` - `orders_items`.`discount` - `orders_items`.`bonus`) * `orders_items`.`count`) AS `money` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id` LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `users`.`id` = `orders`.`user_id` WHERE `orders`.`id` = '".$order_item['order_id']."' AND `orders_items`.`status` = '1' AND `orders`.`status` = '1' LIMIT 1";
            $order_money = $db->createCommand($sql)->queryScalar();//$db->one($sql);
            // Проверка суммы оставшихся товаров в заказе;
            if ($order_money < 1000) {
                // Загрузка подарочных бонусов за данный заказ;
                $sql = "SELECT SUM(`bonus`) FROM `users_bonus` WHERE `order_id` = '".$order_item['order_id']."' AND `user_id` = '".$order_item['user_id']."' AND `type` = '4' AND `date` > DATE_SUB(NOW(), INTERVAL '30' DAY) AND `status` = '1' LIMIT 1";
                $bonus = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                // Проверка суммы бонусов;
                if ($bonus > 0) {
                    // Перерасчет бонусов покупателя;
                    $sql = "INSERT INTO `users_bonus` (`user_id`, `order_id`, `type`, `bonus`, `date`, `status`) VALUES ('".$order_item['user_id']."', '".$order_item['order_id']."', '4', '-1000', NOW(), '1')";
                    //$db->query($sql);
                    $db->createCommand($sql)->queryScalar();
                    // Обновление бонусов покупателя;
                    $sql = "UPDATE `users` SET `bonus` = `bonus` - '1000' WHERE `id` = '".$order_item['user_id']."' LIMIT 1";
                    $db->createCommand($sql)->queryScalar();//$db->query($sql);
                }
            }
            // Проверка менеджеров;
            if ($managers) {
                foreach ($managers as $manager) {
                    // Отправка почты;
                   // $smtp->send($manager, 'Отмена заказа', 'Отмена заказа #'.$order_item['order_id'].' ('.$order_item['name'].')');
                }
            }
        }
        // Вывод сообщения;
        die('Отмена товара выполнена');

    }

    public function actionOrders_delivery_cancel()
    {
        $db = Yii::$app->getDb();

        // Номер группы товаров в заказе;
        $order_group_id = intval($_POST['order_delivery_cancel']);
        // Загрузка данных о доставке;
        $sql = "SELECT `orders_groups`.`order_id`, `orders_groups`.`id` AS `order_group_id`, `orders_groups`.`delivery_price`, `orders`.`user_id` FROM `orders_groups` LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` WHERE `orders_groups`.`id` = '".$order_group_id."' AND `orders_groups`.`delivery_price` > '0' LIMIT 1";
        if ($order_group = $db->createCommand($sql)->queryOne()) {
            // Обновление стоимости доставки;
            $sql = "UPDATE `orders_groups` SET `delivery_price` = '0' WHERE `id` = '".$order_group['order_group_id']."' LIMIT 1";
            $db->createCommand($sql)->execute();//$db->query($sql);
            // Перерасчет средств покупателя;
            $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('".$order_group['user_id']."', '".$order_group['order_id']."', '9', '".$order_group['delivery_price']."', 'Отмена доставки: #".$order_group['order_id']."', NOW(), '1')";
            $db->createCommand($sql)->execute();//$db->query($sql);
            // Обновление баланса покупателя;
            $sql = "UPDATE `users` SET `money` = `money` + '".$order_group['delivery_price']."' WHERE `id` = '".$order_group['user_id']."' LIMIT 1";
            $db->createCommand($sql)->execute();//$db->query($sql);            $db->query($sql);
        }
        // Вывод сообщения;
        die('Возврат средств за доставку выполнен');

    }

    public function actionOrders_delivery_double(){
        // Обработка данных;
        $order_group_id = intval($_POST['order_delivery_double']);
        // ;
        // Вывод сообщения;
        die('Двойная доставка оформлена');
    }

    public function actionOrders_delivery_surcharge()
    {
        $db = Yii::$app->getDb();
        // Обработка данных;
        $order_group_id = intval($_POST['order_group_id']);
        $money = intval($_POST['money']);
        // Загрузка заказа (группы товаров в заказе);
        $sql = "SELECT `id`, `delivery_surcharge` FROM `orders_groups` WHERE `id` = '".$order_group_id."' LIMIT 1";
        if ($order_group = $db->createCommand($sql)->queryOne()) {
            // Проверка доплаты;
            if ($money != $order_group['delivery_surcharge']) {
                // Обновление водителя на доставку;
                $sql = "UPDATE `orders_groups` SET `delivery_surcharge` = '".$money."' WHERE `id` = '".$order_group['id']."' LIMIT 1";
                $db->createCommand($sql)->execute();
                // Сообщение;
                $message = 'Доплата за доставку сохранена';
                // Логирование;
               // debug('delivery_surcharge', $order_group_id.' = '.$money.' / '.$message.' = ('.$user['name'].', '.$user['phone'].')');
            }
        }
        // Вывод сообщения;
        die($message);
    }

    public function actionOrders_driver_set(){
        $db = Yii::$app->getDb();
        // Обработка данных;
        $order_group_id = intval($_POST['order_group_id']);
        $driver_id = intval($_POST['driver_id']);
        // Загрузка данных о доставке;
        $sql = "SELECT `id` FROM `orders_selects` WHERE `order_group_id` = '".$order_group_id."' AND `status` >= '0' LIMIT 1";
        if ($order_select = $db->createCommand($sql)->queryOne()) {
            if ($driver_id) {
                // Обновление водителя на доставку;
                $sql = "UPDATE `orders_selects` SET `user_id` = '".$driver_id."' WHERE `id` = '".$order_select['id']."' LIMIT 1";
                $db->createCommand($sql)->execute();
                // Сообщение;
                $message = 'Водитель назначен на доставку';
            } else {
                // Обновление водителя на доставку (снять водителя);
                $sql = "UPDATE `orders_selects` SET `status` = '-1' WHERE `id` = '".$order_select['id']."' LIMIT 1";
                $db->createCommand($sql)->execute();
                //$db->query($sql);
                // Обновление списка товаров;
                $sql = "UPDATE `orders_items` SET `status_id` = '1001' WHERE `order_group_id` = '".$order_group_id."' AND `status_id` IN ('1004', '1005') AND `status` = '1'";
                $db->createCommand($sql)->execute();
                //$db->query($sql);
                // Сообщение;
                $message = 'Водитель снят с доставки';
            }
        } else {
            if ($driver_id) {
                // Добавление водителя на доставку;
                $sql = "INSERT INTO `orders_selects` (`order_group_id`, `user_id`, `price`, `date_begin`, `status`) VALUES ('".$order_group_id."', '".$driver_id."', '300', NOW(), '0')";
                $db->createCommand($sql)->execute();//$db->query($sql);
                // Обновление списка товаров;
                $sql = "UPDATE `orders_items` SET `status_id` = '1004' WHERE `order_group_id` = '".$order_group_id."' AND `status_id` = '1001' AND `status` = '1'";
                $db->createCommand($sql)->execute();
                //$db->query($sql);
                // Сообщение;
                $message = 'Водитель назначен на доставку';
            }
        }
        // Логирование;
        //debug('drivers', $order_group_id.' = '.$message.' ('.$user['name'].', '.$user['phone'].')');
        // Вывод сообщения;
        die($message);


    }

    public function actionOrders_delivery(){
        // Итого;

        $db = Yii::$app->getDb();

        $info = array('count', 'goods', 'pays', 'comissions');
// Список заказов;
        $orders = array();

        // Обработка данных;
        parse_str($_POST['orders_delivery'], $_POST);
        // Сохранение данных фильтра;
        $_SESSION['filter']['date_begin'] = $_POST['date_begin'] ? $_POST['date_begin'] : time();
        $_SESSION['filter']['date_end'] = $_POST['date_end'] ? $_POST['date_end'] : time();
        $_SESSION['filter']['order_id'] = $_POST['order_id'] ? $_POST['order_id'] : '';
        // Поля итого;
        $info_price = 0;
        $info_delivery_price = 0;
        $info_delivery_surcharge = 0;
        // Загрузка групп заказов (доставок);
        $sql = "SELECT
`orders_groups`.
`order_id`,
`orders_groups`.
`id` AS `order_group_id`,
`deliveries`.
`name` AS `delivery_name`,
`orders_groups`.
`delivery_price`,
`orders_groups`.
`delivery_surcharge`,
`orders_groups`.
`delivery_date`,
CONCAT_WS(', ', `address`.`street`, `address`.`house`, `address`.`room`) AS `delivery_address`
FROM `orders_groups`
LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id`
LEFT JOIN `deliveries` ON `deliveries`.`id` = `orders_groups`.`delivery_id`
LEFT JOIN `address` ON `address`.`id` = `orders_groups`.`address_id`
WHERE "
            .($_SESSION['filter']['order_id'] ? "`orders`.`id` = '"
                .$_SESSION['filter']['order_id']."' AND " : "")
            //.($_SESSION['filter']['users'] ? "`orders`.`user_id` IN ('".implode("', '", array_keys($_SESSION['filter']['users']))."') AND " : "")
            ."DATE(`orders_groups`.`delivery_date`) >= '".date("Y-m-d", $_SESSION['filter']['date_begin'])."' AND "
            ."DATE(`orders_groups`.`delivery_date`) <= '".date("Y-m-d", $_SESSION['filter']['date_end'])
            ."' AND ((`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1003')
<<<<<<< HEAD
            OR (`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1007')
            OR `orders_groups`.`delivery_id` = '1006' OR `orders_groups`.`delivery_id` = '1007') AND `orders_groups`.`status` = '1' AND (SELECT `id` FROM `orders_items` WHERE `order_group_id` = `orders_groups`.`id` AND `status_id` IN ('1001', '1004', '1005', '1006', '1007', '1008') AND `status` = '1' LIMIT 1) IS NOT NULL AND (SELECT `id` FROM `orders_items` WHERE `order_group_id` = `orders_groups`.`id` AND `status_id` IS NULL AND `status` = '1' LIMIT 1) IS NULL AND `orders`.`type` = '1' AND `orders`.`status` = '1' ORDER BY `orders_groups`.`delivery_date` DESC";


        //print_r($sql);die();
=======
             OR (`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1007')
             OR (`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1010')
            OR `orders_groups`.`delivery_id` = '1006' OR `orders_groups`.`delivery_id` = '1007') AND `orders_groups`.`status` = '1' AND (SELECT `id` FROM `orders_items` WHERE `order_group_id` = `orders_groups`.`id` AND `status_id` IN ('1001', '1004', '1005', '1006', '1007', '1008') AND `status` = '1' LIMIT 1) IS NOT NULL AND (SELECT `id` FROM `orders_items` WHERE `order_group_id` = `orders_groups`.`id` AND `status_id` IS NULL AND `status` = '1' LIMIT 1) IS NULL AND `orders`.`type` = '1' AND `orders`.`status` = '1' ORDER BY `orders_groups`.`delivery_date` DESC";
>>>>>>> 053495c556647b3b24062145fd37bd5e1e3d3870

        if ($orders =  $db->createCommand($sql)->queryAll()) {
            foreach ($orders as $i => $item) {
                // Обработка даты доставки;
                $orders[$i]['delivery_date'] = date("d.m.Y, H:i", strtotime($item['delivery_date']));
                // Обработка стоимости доставки;
                $orders[$i]['delivery_price'] = number_format($item['delivery_price'], 0, '.', ' ');
                // Обработка доплаты за доставки;
                $orders[$i]['delivery_surcharge'] = number_format($item['delivery_surcharge'], 0, '.', ' ');
                // Поиск прикрепленного водителя;
                $sql = "SELECT
`users`.`id` AS `user_id`,
`users`.`name` AS `user_name`,
 `orders_selects`.`date_begin`,
  `orders_selects`.`date_end`,
   `orders_selects`.`price`,
   `orders_selects`.`driver`,
    `orders_selects`.`status`
    FROM `orders_selects`
    LEFT JOIN `users` ON `users`.`id` = `orders_selects`.`user_id`
     WHERE `order_group_id` = '".$item['order_group_id']."'"
//                    .($_SESSION['filter']['drivers'] ? " AND `users`.`id` IN ('".implode("', '", array_keys($_SESSION['filter']['drivers']))."')" : "")
                    ." AND `orders_selects`.`status` >= '0' LIMIT 1";


                if ($orders[$i]['select'] =  $db->createCommand($sql)->queryOne()) {
                    // Рассчет итоговых данных (начисления таксистам);
                    $info_delivery_price += ($orders[$i]['select']['price'] - $item['delivery_surcharge']);
                    $info_delivery_surcharge += $item['delivery_surcharge'];
                    // Проверка статуса;
                    if ($orders[$i]['select']['date_end'] and $orders[$i]['select']['status']) {
                        // Обработка даты завершения заявки (фактической выдачи);
                        $orders[$i]['select']['date_end'] = date("d.m.Y, H:i", strtotime($orders[$i]['select']['date_end']));
                        // Обработка статуса;
                        $orders[$i]['status_name'] = 'заказ доставлен';
                    } else {
                        // Обработка статуса;
                        $orders[$i]['status_name'] = 'заказ в пути';
                    }
                    // Обработка даты приема заявки;
                    $orders[$i]['select']['date_begin'] = date("d.m.Y, H:i", strtotime($orders[$i]['select']['date_begin']));
                    // Обработка стоимости доставки;
                    $orders[$i]['select']['price'] = number_format($orders[$i]['select']['price'] , 0, '.', ' ');// maks, убрал это вывражение, 15,06,2016 //  - $item['delivery_surcharge']
                    // Рассчет итоговых данных (оплаты покупателей);
                    $info_price += $item['delivery_price'];
                } else {
                    // Рассчет текущей цены за доставку;
                    $api = new Api();
                    $orders[$i]['price'] = $api->delivery_price( $orders[$i]['order_group_id']);
                    // Обработка статуса;
                    $orders[$i]['status_name'] = 'ожидает курьера';
                    // Проверка фильтра по водителям;
//                    if ($_SESSION['filter']['drivers']) {
//                        // Удаление записи;
//                        unset($orders[$i]);
//                    } else {
//                        // Рассчет итоговых данных (оплаты покупателей);
//                        $info_price += $item['delivery_price'];
//                    }
                }
            }
        }
        // Загрузка водителей;
        $sql = "SELECT `id`, `name`, `phone` FROM `users` WHERE (`driver` IS NOT NULL OR (SELECT `id` FROM `orders_selects` WHERE `user_id` = `users`.`id` AND `status` >= '0' LIMIT 1) IS NOT NULL) AND `status` = '1' ORDER BY `name` ASC";
        $drivers =  $db->createCommand($sql)->queryAll();

        // Загрузка суммы оплат таксистам;
        $sql = "SELECT
ABS(SUM(`users_pays`.`money`))
FROM `users_pays`
LEFT JOIN `users` ON `users`.`id` = `users_pays`.`user_id`
WHERE `users`.`driver` IS NOT NULL
 AND `users_pays`.`type` = '22'
 AND DATE(`users_pays`.`date`) >= '".date("Y-m-d", $_SESSION['filter']['date_begin'])."'
 AND DATE(`users_pays`.`date`) <= '".date("Y-m-d", $_SESSION['filter']['date_end'])."'"
            //.($_SESSION['filter']['drivers'] ? " AND `users`.`id` IN ('".implode("', '", array_keys($_SESSION['filter']['drivers']))."')" : "")
            ." AND `users_pays`.`status` = '1' LIMIT 1";

        $info_delivery_pays = $db->createCommand($sql)->queryScalar();;
        // Обработка итоговых данных;
        $info['count'] = number_format(count($orders), 0, '.', ' ');
        $info['price'] = number_format($info_price, 2, '.', ' ');
        $info['delivery_price'] = number_format($info_delivery_price, 2, '.', ' ');
        $info['delivery_surcharge'] = number_format($info_delivery_surcharge, 2, '.', ' ');
        $info['delivery_pays'] = number_format($info_delivery_pays, 2, '.', ' ');
        $info['sum'] = number_format($info_price - $info_delivery_price - $info_delivery_surcharge, 2, '.', ' ');
        // Подготовка данных;
        $data = array('orders' => $orders, 'drivers' => $drivers, 'info' => $info);
        // Обработка данных;

        return json_encode($data);


    }

    public function actionTestSma(){
        $api = new Api();
        $api->sms('9237271543', 'Новый заказ: #555');

    }

    public function actionOrders_items()
    {

//        print_r($_POST);
//        print_r($_SESSION);
//        die();

        $db = Yii::$app->getDb();

        // Итого;
        $info = array('count', 'goods', 'pays', 'comissions');
// Список заказов;
        $orders = array();
// Список товаров;
        $goods = array();

// Загрузка данных о заказах;
            // Обработка данных;
            parse_str($_POST['orders_items'], $_POST);
            // Сохранение данных фильтра;

        //не вызывает Notice
        //if(array_key_exists('date', $_POST))

//        if(empty($_SESSION['filter'])) $_SESSION['filter']= [];
//        if(empty($_SESSION['filter']['date'])) $_SESSION['filter']['date'] = ' ';
     //  print_r($_SESSION);die();


//        if(empty($_SESSION['filter'])){
//            $_SESSION['filter'] = [];
//        }
//        if(empty($_SESSION['filter']['date'])){
//            $_SESSION['filter'] = [];
//            $_SESSION['filter']['date'] = 1;
//        }
        $_SESSION['filter']['date'] = !empty($_POST['date']) ? $_POST['date'] : '1';
            $_SESSION['filter']['type'] = $_POST['type'] ? $_POST['type'] : '1';
            $_SESSION['filter']['order_id'] = $_POST['order_id'] ? $_POST['order_id'] : '';
            $_SESSION['filter']['status_id'] = $_POST['status_id'] ? $_POST['status_id'] : '';
            $_SESSION['filter']['date_begin'] = $_POST['date_begin'] ? $_POST['date_begin'] : time();
            $_SESSION['filter']['date_end'] = $_POST['date_end'] ? $_POST['date_end'] : time();
            $_SESSION['filter']['delivery_id'] = $_POST['delivery_id'] ? $_POST['delivery_id'] : '';
            $_SESSION['filter']['store_id'] = $_POST['store_id'] ? $_POST['store_id'] : '';


//        Zloradnij::print_arr($_SESSION['filter']['date_begin']);
//        Zloradnij::print_arr($_SESSION['filter']['date_end']);
//
//        Zloradnij::print_arr(date("Y-m-d", $_SESSION['filter']['date_begin']));
//        Zloradnij::print_arr(date("Y-m-d", $_SESSION['filter']['date_end']));
//
//        die();

        //не вызывает Notice
        if(array_key_exists('group', $_POST))  $_SESSION['filter']['group'] = 1; else $_SESSION['filter']['group'] = 0;

        $_SESSION['filter']['user_type'] = $_POST['user_type'] ? $_POST['user_type'] : 0;
        $_SESSION['filter']['type_id'] = $_POST['type_id'] ? $_POST['type_id'] : '';


        if(empty($_SESSION['filter']['users']))
            if(!empty($_POST['users']))
                $_SESSION['filter']['users'] = $_POST['users'];
        else
            $_SESSION['filter']['users'] = [];


        if(empty($_SESSION['filter']['codes']))
            if(!empty($_POST['codes']))
                $_SESSION['filter']['codes'] = $_POST['codes'];
        else
            $_SESSION['filter']['codes'] = [];

        if(empty($_SESSION['filter']['shops'])){
            if(!empty($_POST['shops']))
                $_SESSION['filter']['shops'] = $_POST['shops'];
            else
                $_SESSION['filter']['shops'] = [];
        }

            // Значение статусов поумолчанию;
            $_SESSION['filter']['status'] = 1;
            $_SESSION['filter']['status_where'] = "";

            // Обработка статусов;
            if ($_SESSION['filter']['status_id'] >= 1001) {
                $_SESSION['filter']['status_where'] = " AND `orders_items`.`status_id` = '".$_SESSION['filter']['status_id']."'";
            }
            if ($_SESSION['filter']['status_id'] == 1008) {
                $_SESSION['filter']['status'] = 0;
            }
            if ($_SESSION['filter']['status_id'] == 'NO') {
                $_SESSION['filter']['status_where'] = " AND (`orders_items`.`status_id` IS NULL OR `orders_items`.`status_id` != '1007')";
            }
            if ($_SESSION['filter']['status_id'] == 'NULL') {
                $_SESSION['filter']['status_where'] = " AND `orders_items`.`status_id` IS NULL";
            }
            if ($_SESSION['filter']['status_id'] == -1) {
                $_SESSION['filter']['status'] = 0;
                $_SESSION['filter']['status_where'] = " AND (`orders_items`.`status_id` != '1008' OR `orders_items`.`status_id` IS NULL)";
            }
            // Поля итого;
            $info_goods_price = 0;
            $info_goods_discount = 0;
            $info_goods_bonus = 0;
            $info_goods_delivery = 0;
            $info_goods_cancel = 0;
            $info_pays_goods = 0;
            $info_pays_fee = 0;
            $info_pays_delivery = 0;
            $info_comissions_goods = 0;
            $info_comissions_minus = 0;
            $info_comissions_delivery = 0;

        //Zloradnij::print_arr($_SESSION['filter']);

            // Загрузка заказов;
        $sql = "SELECT
                    `orders`.`id` AS `order_id`,
                    `orders`.`date`,
                    '' AS `groups`,
                    '' AS `money`,
                    `orders`.`code_id`,
                    `orders`.`user_id`,
                    `orders`.`comments`
                    FROM `orders`
                    LEFT JOIN `codes` ON `codes`.`id` = `orders`.`code_id`
                    LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id`
                    LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                    LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                    WHERE "
            .($_SESSION['filter']['order_id'] ? "`orders`.`id` = '".$_SESSION['filter']['order_id']."' AND " : "")
            .(($_SESSION['filter']['date'] == '1') ? "DATE(`orders`.`date`) >= '".date("Y-m-d", ($_SESSION['filter']['date_begin']))
                ."' AND "."DATE(`orders`.`date`) <= '".date("Y-m-d", ($_SESSION['filter']['date_end']))
                ."' AND " : "")
            .($_SESSION['filter']['users'] ? "`orders`.`user_id` IN ('".implode("', '", array_keys($_SESSION['filter']['users']))."') AND " : "")
            .($_SESSION['filter']['codes'] ? "`orders`.`code_id` IN ('".implode("', '", array_keys($_SESSION['filter']['codes']))."') AND " : "")
            .(($_SESSION['filter']['type'] == '1' and $_SESSION['filter']['store_id']) ? "`users`.`store_id` = '".$_SESSION['filter']['store_id']."' AND "." (`orders_groups`.`type_id` >0  ) AND " : " ")
            .(($_SESSION['filter']['user_type'] == 1) ? "(SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL AND " : "")
            .(($_SESSION['filter']['user_type'] == 2) ? "(SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NOT NULL AND " : "")
            ."`orders`.`type` = '".$_SESSION['filter']['type']."' "
        ." AND (`orders`.`status` = 1 )"
<<<<<<< HEAD
         //   ." AND ((`orders_items`.`status_id` IS NOT NULL) OR (`orders_items`.`status_id` > 0))"
        //." AND (`orders_items`.`status` = ".$_SESSION['filter']['status']." )"
=======
        ." AND (`orders_items`.`status` = ".$_SESSION['filter']['status']." )"
>>>>>>> 053495c556647b3b24062145fd37bd5e1e3d3870
//        ." AND (`orders_groups`.`status` = ".$_SESSION['filter']['status']." )"
//        ." AND (`orders`.`status` = ".$_SESSION['filter']['status'].")"
        //." AND (`orders_items`.`status` = 1 )"
//        ." AND (`orders_groups`.`status` = 1 )"
        //." AND (`orders`.`status` = 0)"
            ." GROUP BY `order_id` ORDER BY `orders`.`date` DESC";


     //   Zloradnij::print_arr($sql);die();

//        $sql = "SELECT
//                    `orders`.`id` AS `order_id`,
//                    `orders`.`date`,
//                    '' AS `groups`,
//                    '' AS `money`,
//                    `orders`.`code_id`,
//                    `orders`.`user_id`,
//                    `orders`.`comments`
//                    FROM `orders`
//                    LEFT JOIN `codes` ON `codes`.`id` = `orders`.`code_id`
//                    LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id`
//                    WHERE "
//            .($_SESSION['filter']['order_id'] ? "`orders`.`id` = '".$_SESSION['filter']['order_id']."' AND " : "")
//            .(($_SESSION['filter']['date'] == '1') ? "DATE(`orders`.`date`) >= '".date("Y-m-d", ($_SESSION['filter']['date_begin']))
//                ."' AND "."DATE(`orders`.`date`) <= '".date("Y-m-d", ($_SESSION['filter']['date_end']))
//                ."' AND " : "")
//            .($_SESSION['filter']['users'] ? "`orders`.`user_id` IN ('".implode("', '", array_keys($_SESSION['filter']['users']))."') AND " : "")
//            .($_SESSION['filter']['codes'] ? "`orders`.`code_id` IN ('".implode("', '", array_keys($_SESSION['filter']['codes']))."') AND " : "")
//            .(($_SESSION['filter']['type'] == '1' and $_SESSION['filter']['store_id']) ? "`users`.`store_id` = '".$_SESSION['filter']['store_id']."' AND " : "")
//            .(($_SESSION['filter']['user_type'] == 1) ? "(SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL AND " : "")
//            .(($_SESSION['filter']['user_type'] == 2) ? "(SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NOT NULL AND " : "")
//            ."`orders`.`type` = '".$_SESSION['filter']['type']
//            ."' AND `orders`.`status` = '1' ORDER BY `orders`.`date` DESC";
//
        $deb['sql'] = $sql;

            if ($orders = $db->createCommand($sql)->queryAll()){//$db->all($sql)) {
                //Zloradnij::print_arr($orders);
                foreach ($orders as $order_key => $order) {
                    // Обработка даты;
                    $orders[$order_key]['date'] = date("d.m.Y, H:i", strtotime($order['date']));
                    // Загрузка покупателя;
                    $sql = "SELECT `name`, `phone`, `email`, `staff` FROM `users` WHERE `id` = '".$order['user_id']."' LIMIT 1";
                    $orders[$order_key]['user'] =  $db->createCommand($sql)->queryOne();//$db->row($sql);
                    // Проверка промо-кода;
                    if ($orders[$order_key]['code_id']) {
                        // Загрузка промо-кода;
                        $sql = "SELECT `codes`.`code`, `users`.`name` FROM `codes` LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id` WHERE `codes`.`id` = '".$order['code_id']."' LIMIT 1";
                        $orders[$order_key]['code'] =  $db->createCommand($sql)->queryOne();//$db->row($sql);
                    }
                    // Загрузка групп заказов;
                    $sql = "SELECT `orders_groups`.`id` AS `order_group_id`, '' AS `goods`, `deliveries`.`name` AS `delivery_name`, `orders_groups`.`delivery_price`, `orders_groups`.`delivery_surcharge`, '0' AS `delivery_pay`, `orders_groups`.`delivery_date`, '' AS `delivery_address`, `orders_groups`.`address_id`, `orders_groups`.`store_id` FROM `orders_groups` LEFT JOIN `deliveries` ON `deliveries`.`id` = `orders_groups`.`delivery_id` WHERE `order_id` = '".$order['order_id']."' AND ".($_SESSION['filter']['delivery_id'] ? "`orders_groups`.`delivery_id` = '".$_SESSION['filter']['delivery_id']."' AND " : "").(($_SESSION['filter']['date'] == '2') ? "DATE(`orders_groups`.`delivery_date`) >= '".date("Y-m-d", $_SESSION['filter']['date_begin'])."' AND DATE(`orders_groups`.`delivery_date`) <= '".date("Y-m-d", $_SESSION['filter']['date_end'])."' AND " : "").((($_SESSION['filter']['type'] == '2' OR $_SESSION['filter']['delivery_id'] == 1003) and $_SESSION['filter']['store_id']) ? "`orders_groups`.`store_id` = '".$_SESSION['filter']['store_id']."' AND " : "")."`orders_groups`.`status` = '1' ORDER BY `orders_groups`.`id` ASC";

                //    $deb['sql1'] = $sql;

                    if ($orders[$order_key]['groups'] =  $db->createCommand($sql)->queryAll()){//$db->all($sql)) {
                        foreach ($orders[$order_key]['groups'] as $order_group_key => $order_group) {
                            // Обработка даты;
                            $orders[$order_key]['groups'][$order_group_key]['delivery_date'] = date("d.m.Y, H:i", strtotime($order_group['delivery_date']));
                            // Проверка адреса;
                            if ($order_group['address_id']) {
                                // Загрузка адреса;
                                $sql = "SELECT CONCAT_WS(', ', `street`, `house`, `room`, `phone`) AS `address` FROM `address` WHERE `id` = '".$order_group['address_id']."' LIMIT 1";
                                $orders[$order_key]['groups'][$order_group_key]['delivery_address'] =  $db->createCommand($sql)->queryScalar();//$db->one($sql);
                                // Обработка адреса;
                                $orders[$order_key]['groups'][$order_group_key]['delivery_address'] = trim($orders[$order_key]['groups'][$order_group_key]['delivery_address'], ', ');
                            }

                            // Загрузка суммы отмен (возвратов средств на счет ExtremeShop);
                            $sql = "SELECT SUM((`orders_items`.`price` - `orders_items`.`discount` - `orders_items`.`bonus`) * `orders_items`.`count`) AS `money`
FROM `orders_items`
LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`
LEFT JOIN `shop_group_variant_link` ON `shop_group_variant_link`.`product_id` = `goods`.`id`
LEFT JOIN `shop_group_related` ON `shop_group_related`.`shop_group_id` = `shop_group_variant_link`.`shop_group_id`
LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id`
WHERE  `order_group_id` = '".$order_group['order_group_id']."'"

                                .($_SESSION['filter']['type_id'] ? "  AND `goods`.`type_id` = '".$_SESSION['filter']['type_id']."'" : "")
                                .($_SESSION['filter']['shops'] ? "  AND `shop_group_related`.`shop_id` IN ('".implode("', '", array_keys($_SESSION['filter']['shops']))."')" : "")
                                .$_SESSION['filter']['status_where']."  AND `orders_items`.`status` = '0' LIMIT 1";

                          //  $deb['sql2'] = $sql;

                            $info_goods_cancel +=  $db->createCommand($sql)->queryScalar();//$db->one($sql);
                            // Загрузка товаров;
                            $sql = "SELECT
                            `orders_items`.`id` AS `order_item_id`,
                            `orders_groups`.`id` AS `order_group_id`,
                            `orders`.`id` AS `order_id`,
                            `orders_items`.`store_id` AS `storeId`,
                            `goods`.`id` AS `good_id`,
                            `goods`.`name` AS `good_name`,
                            `shops`.`id` AS `shop_id`,
                            `shops`.`name` AS `shop_name`,
                            `orders_items`.`variation_id`,
                            get_tags(`orders_items`.`variation_id`) AS `tags`,
                            '0' AS `price_in`,
                            `orders_items`.`price` AS `price_out`,
                            `orders_items`.`discount`,
                            `orders_items`.`bonus`,
                            `orders_items`.`count`,
                            '0' AS `money`,
                            `orders_items`.`comission`,
                            '0' AS `comission_percent`,
                            `orders_items`.`fee`,
                            `orders_items`.`status_id`,
                            `orders_status`.`name` AS `status_name`,
                            `orders_items`.`status`,
                            CONCAT(`address`.`street`,', ',`address`.`house`) AS `store_address`
                        FROM `orders_items`
                        LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`
                        LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id`
                        LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id`
                        LEFT JOIN `shops_stores` ON `shops_stores`.`id` = `orders_items`.`store_id`
                        LEFT JOIN `address` ON `address`.`id` = `shops_stores`.`address_id`
                        LEFT JOIN `shops` ON `shops`.`id` = `shops_stores`.`shop_id`
                        LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id`
                        WHERE `order_group_id` = '".$order_group['order_group_id']."'"
                                .($_SESSION['filter']['type_id'] ? " AND `goods`.`type_id` = '".$_SESSION['filter']['type_id']."'" : "")
                                .($_SESSION['filter']['shops'] ? " AND ((`shops`.`id` IN ('".implode("', '", array_keys($_SESSION['filter']['shops']))."') AND `orders_items`.`store_id` IS NOT NULL) OR (`orders_items`.`store_id` IS NULL AND `goods`.`shop_id` IN ('".implode("', '", array_keys($_SESSION['filter']['shops']))."')))" : "")
                                .$_SESSION['filter']['status_where']
<<<<<<< HEAD
                                //." AND `orders_items`.`status` = '".$_SESSION['filter']['status']."'"
                        ." ORDER BY `shops`.`name` ASC, `goods`.`name` ASC";
=======
                                ." AND `orders_items`.`status` = '".$_SESSION['filter']['status']."'"
                        ." ORDER BY `shops`.`name` ASC, `goods`.`name` ASC";

>>>>>>> 053495c556647b3b24062145fd37bd5e1e3d3870

                          //  $deb['sql3'] = $sql;



//                    print $sql;die();
                            //Zloradnij::print_arr($sql);
                            if ($orders[$order_key]['groups'][$order_group_key]['goods'] =  $db->createCommand($sql)->queryAll()){//$db->all($sql)) {
                                foreach ($orders[$order_key]['groups'][$order_group_key]['goods'] as $order_item_key => $order_item) {
                                    if($order_item['storeId'] == NULL){
                                        $sqlForShop = 'SELECT shops.*, shops.id as shop_id FROM shops LEFT JOIN goods ON goods.shop_id = shops.id WHERE goods.id="'. $order_item['good_id'] .'"';
                                        $currentShop =  $db->createCommand($sqlForShop)->queryAll();//$db->all($sqlForShop);
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['shop_name'] = $currentShop[0]['name'];
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['shop_id'] = $currentShop[0]['shop_id'];
                                    }
                                    // Номер фотографии;
       //                             $image_id = good_image($order_item['good_id']);
                                            //$image_id = 0;
                                    // Проверка группировки по товарам;
                                    if ($_SESSION['filter']['group']) {
                                        // Обработка данных товара;
                                        $goods[$order_item['variation_id']]['good_id'] = $order_item['good_id'];
                                        $goods[$order_item['variation_id']]['variation_id'] = $order_item['variation_id'];
                                        $goods[$order_item['variation_id']]['good_name'] = $order_item['good_name'];
                                        $goods[$order_item['variation_id']]['shop_name'] = $order_item['shop_name'];
                                        // Обработка фотографии;
                                        $goods[$order_item['variation_id']]['good_image'] =  Goods::findProductImage($order_item['good_id']); //'/files/goods/'.image_dir($image_id).'/'.$image_id.'_min.jpg';
                                        // Обработка вариантов (теги);
                                        $goods[$order_item['variation_id']]['tags'] = $order_item['tags'] ? $order_item['tags'] : '';
                                        // Расчет стоимости;

                                        if (isset($goods[$order_item['variation_id']])) {

                                            if(empty($goods[$order_item['variation_id']]['price_in']))$goods[$order_item['variation_id']]['price_in']=0;
                                            $goods[$order_item['variation_id']]['price_in'] += number_format(($order_item['price_out'] - $order_item['comission'] - $order_item['discount']) * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['price_out']))$goods[$order_item['variation_id']]['price_out']=0;
                                            $goods[$order_item['variation_id']]['price_out'] += number_format($order_item['price_out'] * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['count']))$goods[$order_item['variation_id']]['count']=0;
                                            $goods[$order_item['variation_id']]['count'] += $order_item['count'];
                                            if(empty($goods[$order_item['variation_id']]['discount']))$goods[$order_item['variation_id']]['discount']=0;
                                            $goods[$order_item['variation_id']]['discount'] += number_format($order_item['discount'] * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['bonus']))$goods[$order_item['variation_id']]['bonus']=0;
                                            $goods[$order_item['variation_id']]['bonus'] += number_format($order_item['bonus'] * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['money']))$goods[$order_item['variation_id']]['money']=0;
                                            $goods[$order_item['variation_id']]['money'] += number_format(($order_item['price_out'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'], 2, '.', '');
                                        } else {
                                            if(empty($goods[$order_item['variation_id']]['price_in']))$goods[$order_item['variation_id']]['price_in']=0;
                                            $goods[$order_item['variation_id']]['price_in'] = number_format(($order_item['price_out'] - $order_item['comission'] - $order_item['discount']) * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['price_out']))$goods[$order_item['variation_id']]['price_out']=0;
                                            $goods[$order_item['variation_id']]['price_out'] = number_format($order_item['price_out'] * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['count']))$goods[$order_item['variation_id']]['count']=0;
                                            $goods[$order_item['variation_id']]['count'] = $order_item['count'];
                                            if(empty($goods[$order_item['variation_id']]['discount']))$goods[$order_item['variation_id']]['discount']=0;
                                            $goods[$order_item['variation_id']]['discount'] = number_format($order_item['discount'] * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['bonus']))$goods[$order_item['variation_id']]['bonus']=0;
                                            $goods[$order_item['variation_id']]['bonus'] = number_format($order_item['bonus'] * $order_item['count'], 2, '.', '');
                                            if(empty($goods[$order_item['variation_id']]['money']))$goods[$order_item['variation_id']]['money']=0;
                                            $goods[$order_item['variation_id']]['money'] = number_format(($order_item['price_out'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'], 2, '.', '');
                                        }

                                        // Загрузка остатка товара;
                                        $sql = "SELECT `count` FROM `goods_counts` WHERE `good_id` = '".$order_item['good_id']."' AND `variation_id` = '".$order_item['variation_id']."' AND `status` = '1' LIMIT 1";
                                        $goods[$order_item['variation_id']]['count_all'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                                    } else {
                                        // Обработка статуса заказа;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['status_name'] = $order_item['status_name'] ? $order_item['status_name'] : 'не обработан';
                                        // Обработка фотографии;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['good_image'] = Goods::findProductImage($order_item['good_id']);// '/files/goods/'.image_dir($image_id).'/'.$image_id.'_min.jpg';
                                        // Обработка вариантов (теги);
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['tags'] = $order_item['tags'] ? $order_item['tags'] : '';
                                        // Рассчет входной цены товаров;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_in'] = number_format($order_item['price_out'] - $order_item['comission'] - $order_item['discount'], 2, '.', '');
                                        // Рассчет наценки в рублях;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission'] = number_format(($order_item['comission'] + $order_item['discount']), 2, '.', '');
                                        // Рассчет наценки в процентах;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission_percent'] = number_format(($order_item['comission'] + $order_item['discount']) * 100 / ($order_item['price_out'] - $order_item['comission'] - $order_item['discount']), 2, '.', '');
                                        // Рассчет потраченных бонусов;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['bonus'] = number_format(($order_item['bonus'] * $order_item['count']), 2, '.', '');
                                        // Рассчет общей стоимости товаров;
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['money'] = number_format(($order_item['price_out'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'], 2, '.', '');

                                        // Рассчет промежуточного итога;
                                        if(empty($orders[$order_key]['price_in']))$orders[$order_key]['price_in']=0;
                                            $orders[$order_key]['price_in'] = $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_in'];
                                        if(empty($orders[$order_key]['comission']))$orders[$order_key]['comission']=0;
                                            $orders[$order_key]['comission'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission'];
                                        if(empty($orders[$order_key]['price_out']))$orders[$order_key]['price_out']=0;
                                            $orders[$order_key]['price_out'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_out'];
                                        if(empty($orders[$order_key]['discount']))$orders[$order_key]['discount']=0;
                                            $orders[$order_key]['discount'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['discount'];
                                        if(empty($orders[$order_key]['count']))$orders[$order_key]['count']=0;
                                            $orders[$order_key]['count'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['count'];
                                        if(empty($orders[$order_key]['bonus']))$orders[$order_key]['bonus']=0;
                                            $orders[$order_key]['bonus'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['bonus'];
                                        if(empty($orders[$order_key]['money']))$orders[$order_key]['money']=0;
                                            $orders[$order_key]['money'] +=  $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['money'];

                                        // Обработка промежуточного итога;
                                                $orders[$order_key]['price_in'] = number_format($orders[$order_key]['price_in'], 2, '.', '');
                                            $orders[$order_key]['comission'] = number_format($orders[$order_key]['comission'], 2, '.', '');
                                            $orders[$order_key]['price_out'] = number_format($orders[$order_key]['price_out'], 2, '.', '');
                                            $orders[$order_key]['discount'] = number_format($orders[$order_key]['discount'], 2, '.', '');
                                            $orders[$order_key]['count'] = number_format($orders[$order_key]['count'], 2, '.', '');
                                            $orders[$order_key]['bonus'] = number_format($orders[$order_key]['bonus'], 2, '.', '');
                                            $orders[$order_key]['money'] = number_format($orders[$order_key]['money'], 2, '.', '');
                                    }
                                    // Загрузка данных по доставке;
                                    $sql = "SELECT `price` FROM `orders_selects` WHERE `order_group_id` = '".$order_group['order_group_id']."' AND `status` >= '0' LIMIT 1";
                                    $order_group['delivery_pay'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                                    // Рассчет итоговых данных;
                                    $info_goods_price += $order_item['price_out'] * $order_item['count'];
                                    $info_goods_discount += $order_item['discount'] * $order_item['count'];
                                    $info_goods_bonus += $order_item['bonus'] * $order_item['count'];
                                    $info_pays_goods += ($order_item['price_out'] - $order_item['discount'] - $order_item['comission']) * $order_item['count'];
                                    $info_pays_fee += $order_item['fee'] * $order_item['count'];
                                    $info_pays_delivery += 0;
                                    $info_comissions_goods += ($order_item['comission'] + $order_item['discount']) * $order_item['count'];
                                    $info_comissions_minus += ($order_item['discount'] + $order_item['bonus'] + $order_item['fee']) * $order_item['count'];
                                }
                                // Рассчет итоговых данных;
                                $info_goods_delivery += $order_group['delivery_price'];
                                $info_pays_delivery += ($order_group['delivery_pay'] + $order_group['delivery_surcharge']);
                                $info_comissions_delivery += ($order_group['delivery_price'] - $order_group['delivery_pay'] - $order_group['delivery_surcharge']);
                            } else {
                                // Удаление пустой группы заказа;
                                unset($orders[$order_key]['groups'][$order_group_key]);
                                // Проверка списка групп заказа;
                                if (count($orders[$order_key]['groups']) == 0) {
                                    // Удаление пустого заказа;
                                    unset($orders[$order_key]);
                                }
                            }
                        }
                    } else {
                        // Удаление пустого заказа;
                        unset($orders[$order_key]);
                    }
                }
            }
            /*
            // Проверка фильтра;
            if ($_SESSION['filter']['type'] == 1 and !$_SESSION['filter']['order_id'] and !$_SESSION['filter']['status_id'] and !$_SESSION['filter']['delivery_id'] and !$_SESSION['filter']['store_id'] and !$_SESSION['filter']['group'] and !$_SESSION['filter']['user_type'] and !$_SESSION['filter']['type_id'] and !$_SESSION['filter']['shops'] and !$_SESSION['filter']['users'] and !$_SESSION['filter']['codes']) {
                // Загрузка суммы оплат таксистам;
                $sql = "SELECT ABS(SUM(`users_pays`.`money`)) FROM `users_pays` LEFT JOIN `users` ON `users`.`id` = `users_pays`.`user_id` WHERE `users`.`driver` IS NOT NULL AND `users_pays`.`type` = '22' AND DATE(`users_pays`.`date`) >= '".date("Y-m-d", $_SESSION['filter']['date_begin'])."' AND DATE(`users_pays`.`date`) <= '".date("Y-m-d", $_SESSION['filter']['date_end'])."' AND `users_pays`.`status` = '1' LIMIT 1";
                $info_pays_delivery_money = $db->one($sql);
            }
            */
        $info_pays_delivery_money = 0;

            // Обработка итоговых данных;
            $info['goods'] = array(
                'price' => number_format($info_goods_price, 2, '.', ' '),
                'discount' => number_format(-$info_goods_discount, 2, '.', ' '),
                'bonus' => number_format(-$info_goods_bonus, 2, '.', ' '),
                'delivery' => number_format($info_goods_delivery, 2, '.', ' '),
                'sum' => number_format($info_goods_price - $info_goods_discount - $info_goods_bonus + $info_goods_delivery, 2, '.', ' '),
                'cancel' => number_format(-$info_goods_cancel, 2, '.', ' ')
            );
            $info['pays'] = array(
                'goods' => number_format($info_pays_goods, 2, '.', ' '),
                'fee' => number_format($info_pays_fee, 2, '.', ' '),
                'delivery' => number_format($info_pays_delivery, 2, '.', ' '),
                'sum' => number_format($info_pays_goods + $info_pays_fee + $info_pays_delivery_money, 2, '.', ' ')
            );
            $info['comissions'] = array(
                'goods' => number_format($info_comissions_goods, 2, '.', ' '),
                'minus' => number_format(-$info_comissions_minus, 2, '.', ' '),
                'delivery' => number_format($info_goods_delivery - $info_pays_delivery, 2, '.', ' '),
                'sum' => number_format($info_comissions_goods - $info_comissions_minus + $info_goods_delivery - $info_pays_delivery, 2, '.', ' ')
            );



            // Првоерка группировки;
            if ($_SESSION['filter']['group']) {
                // Общее количество;
                $info['count'] = number_format(count($goods), 0, '.', ' ');
                // Подготовка данных (по товарам);
                $deb['info'] = $_SESSION['filter'];

                //$data = array('goods' => $goods, 'info' => $info,  'debug' => $deb);
                $data = array('goods' => $goods, 'info' => $info, 'debug' => $deb);


            } else {
                // Общее количество;
                $info['count'] = number_format(count($orders), 0, '.', ' ');
                // Подготовка данных (по заказам);

                $deb['info'] = $_SESSION['filter'];

                //$data = array('orders' => $orders, 'info' => $info, 'debug' => $deb);
                $data = array('orders' => $orders, 'info' => $info, 'debug' => $deb);
            }
            // Обработка данных;
            return json_encode($data);
            // Вывод данных;
           // die($data);
//        return $this->render('/site/error',['name' => '404', 'message' => 'Ooooops!']);
    }

    public function actionOrder()
    {

        $db = Yii::$app->getDb();
//        $db->createCommand($sql)->queryScalar();
//        $db->createCommand($sql)->queryAll();

        // Загрузка статусов;
        $sql = "SELECT `id`, `name` FROM `orders_status` WHERE `status` = '1' ORDER BY `position` ASC";
        $status = $db->createCommand($sql)->queryAll();//$db->all($sql);
 //       ..$smarty->assign("status", $status);

// Загрузка типов товаров;
        $sql = "SELECT `id`, `name` FROM `goods_types` WHERE `status` = '1' ORDER BY `position` ASC";
        $types = $db->createCommand($sql)->queryAll();//$db->all($sql);
 //       $smarty->assign("types", $types);

// Загрузка способов доставки;
        $sql = "SELECT `id`, `name` FROM `deliveries` WHERE `status` = '1' ORDER BY `position` ASC";
        $deliveries = $db->createCommand($sql)->queryAll();//$db->all($sql);
   //     $smarty->assign("deliveries", $deliveries);

// Загрузка складов для фильтра (список клубов);
        $sql = "SELECT `id`, `name` FROM `shops_stores` WHERE `shop_id` = '10000001' AND `status` = '1' ORDER BY `position` ASC";
        $stores = $db->createCommand($sql)->queryAll();//$db->all($sql);
     //   $smarty->assign("stores", $stores);

        unset($_SESSION['filter']);

        // Данные фильтра по умолчанию;
        if (empty($_SESSION['filter'])) {
            $_SESSION['filter']['status_where'] = ' ';
            $_SESSION['filter']['date'] = ' ';
            $_SESSION['filter']['date_begin'] = time(); //strtotime(date('Y-m-d')); //strtotime(date('d.m.Y 00:00:00'));
            $_SESSION['filter']['date_end'] = time(); //strtotime(date('Y-m-d')); //strtotime(date('d.m.Y 23:59:00'));

//            $_SESSION['filter']['date_begin'] = strtotime(date('d.m.Y'));
//            $_SESSION['filter']['date_end'] = strtotime(date('d.m.Y'));
            $_SESSION['filter']['user_type'] = 1;


            $_SESSION['filter']['type'] =  '1';
            $_SESSION['filter']['order_id'] = '';
            $_SESSION['filter']['status_id'] = '';
            $_SESSION['filter']['delivery_id'] = '';
            $_SESSION['filter']['store_id'] = '';
            //не вызывает Notice
            $_SESSION['filter']['group'] = 0;
            $_SESSION['filter']['type_id'] = '';
            // Значение статусов поумолчанию;
            $_SESSION['filter']['status'] = [];
        }

        return $this->render('order', [

            'filter' => $_SESSION['filter'],
            'stores' => $stores,
            'deliveries' => $deliveries,
            'types' => $types,
            'status' => $status,

        ]);

    }

    public function actionDelivery()
    {

        $db = Yii::$app->getDb();
//        $db->createCommand($sql)->queryScalar();
//        $db->createCommand($sql)->queryAll();

        // Загрузка статусов;
        $sql = "SELECT `id`, `name` FROM `orders_status` WHERE `status` = '1' ORDER BY `position` ASC";
        $status = $db->createCommand($sql)->queryAll();//$db->all($sql);
        //       ..$smarty->assign("status", $status);

// Загрузка типов товаров;
        $sql = "SELECT `id`, `name` FROM `goods_types` WHERE `status` = '1' ORDER BY `position` ASC";
        $types = $db->createCommand($sql)->queryAll();//$db->all($sql);
        //       $smarty->assign("types", $types);

// Загрузка способов доставки;
        $sql = "SELECT `id`, `name` FROM `deliveries` WHERE `status` = '1' ORDER BY `position` ASC";
        $deliveries = $db->createCommand($sql)->queryAll();//$db->all($sql);
        //     $smarty->assign("deliveries", $deliveries);

// Загрузка складов для фильтра (список клубов);
        $sql = "SELECT `id`, `name` FROM `shops_stores` WHERE `shop_id` = '10000001' AND `status` = '1' ORDER BY `position` ASC";
        $stores = $db->createCommand($sql)->queryAll();//$db->all($sql);
        //   $smarty->assign("stores", $stores);

        unset($_SESSION['filter']);
//
        if (empty($_SESSION['filter'])) {

            // Данные фильтра по умолчанию;
            $_SESSION['filter']['status_where'] = ' ';
            $_SESSION['filter']['date'] = ' ';
            $_SESSION['filter']['date_begin'] = strtotime(date('Y-m-d 00:00:00')); //strtotime(date('d.m.Y 00:00:00'));
            $_SESSION['filter']['date_end'] = strtotime(date('Y-m-d 23:59:00')); //strtotime(date('d.m.Y 23:59:00'));
            $_SESSION['filter']['user_type'] = 1;


            $_SESSION['filter']['type'] =  '1';
            $_SESSION['filter']['order_id'] = '';
            $_SESSION['filter']['status_id'] = '';
            $_SESSION['filter']['delivery_id'] = '';
            $_SESSION['filter']['store_id'] = '';
            //не вызывает Notice
            $_SESSION['filter']['group'] = 0;
            $_SESSION['filter']['type_id'] = '';
            // Значение статусов поумолчанию;
            $_SESSION['filter']['status'] = 1;
            $_SESSION['filter']['users'] = [];

        }

        return $this->render('delivery',
            [
                    'filter' => $_SESSION['filter'],
                    'stores' => $stores,
                    'deliveries' => $deliveries,
                    'types' => $types,
                    'status' => $status,
            ]);
    }

    public function actionDeliveryPlusMoney(){
        $message = '';
        // Доплата за доставку;
        if (isset($_POST['delivery_surcharge'])) {
            $money = intval($_POST['money']);

            $orderGroup = OrdersGroups::findOne(intval($_POST['order_group_id']));
            if (!$orderGroup){

            }else{
                // Проверка доплаты;
                if ($money != $orderGroup['delivery_surcharge']) {
                    // Обновление водителя на доставку;
                    $orderGroup->delivery_surcharge = $money;
                    $orderGroup->save();

                    // Сообщение;
                    $message = 'Доплата за доставку сохранена';
                }
            }
            // Вывод сообщения;
            return $message;
        }
    }
}





