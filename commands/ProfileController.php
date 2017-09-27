<?php

namespace app\commands;

use app\models\RatesAvg;
use app\modules\catalog\models\Category;
use app\modules\common\models\User;
use app\modules\common\models\Profile;
use app\modules\shop\models\Orders;

use NCL;
use NCLNameCaseRu;
use Yii;
use yii\console\Controller;


class ProfileController extends Controller
{
    /**
     * @return string
     */

    public function actionTransfer()
    {
        define('LIMIT_CAR_FOR_ADD', '1');
        define('LIMIT_PETS_FOR_ADD', '1');
        define('LIMIT_CHILDREN_FOR_ADD', '1');

        require(__DIR__ . '/../libraries/NameCaseLib/Library/NCL.NameCase.ru.php');

        $db = Yii::$app->getDb();

        $sql = "SELECT `id`, `name` FROM users WHERE status = 1";
        $users = $db->createCommand($sql)->queryAll();

        foreach ($users as $user) {

            $category_arr = ['car' => '10000244', 'pets' => '10000006', 'children' => '10000004'];
            $out_arr = [];

            foreach ($category_arr as $category => $category_id) {
                $sql = 'SELECT count(*) FROM `orders_items` 
LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id`
LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` 
WHERE `orders_items`.`good_id` IN (SELECT `product_id` FROM `category_links` WHERE `category_id` IN (SELECT `category`.`id` FROM `category` WHERE `category`.`parent_id` = ' . $category_id . ' OR `category`.`id` = ' . $category_id . ' AND`category`.`active` = 1))
AND `orders_items`.`status` = 1 
AND `orders`.`user_id` = ' . $user['id'];
                $out_arr[$category] = $db->createCommand($sql)->queryScalar();
            }
            $car = 0;
            $pets = 0;
            $children = 0;

            if ($out_arr['car'] >= LIMIT_CAR_FOR_ADD) $car = 1;
            if ($out_arr['pets'] >= LIMIT_PETS_FOR_ADD) $pets = 1;
            if ($out_arr['children'] >= LIMIT_CHILDREN_FOR_ADD) $children = 1;

            // Определение пола
            $nc = new NCLNameCaseRu();
            $gender = $nc->genderDetect($user['name']);

            $pol = 0;
            if ($gender == NCL::$MAN) {
                $pol = 1;
            } elseif ($gender == NCL::$WOMAN) {
                $pol = 2;
            }

            // Заполение таблицы
            $user_check = $db->createCommand("SELECT id FROM `profile` WHERE user_id = ".$user['id'])->queryScalar();
            if ($user_check) {
                // update
                $db->createCommand("UPDATE `profile` SET `gender` = ".$pol.", `pets` = ".$pets.", `children` = ".$children.", `car` = ".$car." WHERE `user_id` = ".$user['id']);
                $last_id = $user_check;
            } else {
                // insert
                $db->createCommand("INSERT INTO `profile` (`user_id`, `gender`, `pets`, `children`, `car`) VALUES (" . $user['id'] . ", " . $pol . ", " . $pets . ", " . $children . ", " . $car . ")")->execute();
                $last_id = Yii::$app->db->getLastInsertID();
            }


            // Получение суммы
            $sql = "SELECT SUM((`price` - `discount` - `bonus`) * `count`) as result from orders_items
    LEFT JOIN orders_groups ON orders_groups.id = orders_items.order_group_id LEFT JOIN orders ON orders_groups.order_id = orders.id WHERE orders_items.status = 1 AND orders.user_id = " . $user['id'];
            $selfSumm = $db->createCommand($sql)->queryScalar();
            // Получение заказов
            $sql = "SELECT Count(*) FROM orders WHERE user_id = " . $user['id'] . " AND status = 1";

            $selfCount = $db->createCommand($sql)->queryScalar();
            if ($selfCount == 0) $selfCount = 1;

            $selfIndex = round(($selfSumm / $selfCount), 2);
            if ($selfIndex == 0) $selfIndex = 0;

            $avgIndex = RatesAvg::find()->orderBy(['id' => SORT_DESC])->one();
            $avgSelfIndex = round(floatval($selfIndex) / floatval($avgIndex->rate), 2);

            $db->createCommand("INSERT INTO `profile_links` (`profile_id`, `rate`) VALUES (" . $last_id . ", " . $avgSelfIndex . ")")->execute();


            echo 'Added user: ' . $user['id'] . ' AvgIndex: ' . $avgSelfIndex . ' | CAR: ' . $car . ' PETS: ' . $pets . ' CHILDREN: ' . $children . "\r\n";

        }


        // Получение суммы
        /*$sql = "SELECT SUM((`price` - `discount` - `bonus`) * `count`) as result from orders_items
LEFT JOIN orders_groups ON orders_groups.id = orders_items.order_group_id
LEFT JOIN orders ON orders_groups.order_id = orders.id
WHERE orders_items.status = 1 AND orders.user_id = " . $user['id'];
        $selfSumm = $db->createCommand($sql)->queryScalar();
        // Получение заказов
        $sql = "SELECT Count(*) FROM orders WHERE user_id = " . $user['id'] . " AND status = 1";
        $selfCount = $db->createCommand($sql)->queryScalar();
        $selfIndex = round(($selfSumm / $selfCount), 2);*/

        // \app\modules\common\models\Zloradnij::print_arr($users);

    }


    public function actionFastOrderMorning(){
        echo 'start update morning';
        $db = Yii::$app->getDb();
        $db->createCommand("UPDATE  shops SET status = 1 WHERE id = 10000258")->execute();
        $db->createCommand("UPDATE  shops SET status = 0 WHERE id = 10000264")->execute();
        $db->createCommand("UPDATE goods_variations SET goods_variations.comission = 20 WHERE   goods_variations.good_id in (SELECT id from goods where type_id = 1014)")->execute();
        $db->createCommand("UPDATE  deliveries_prices SET price= 150 WHERE good_type_id = 1014")->execute();
        echo "\n morning update commission success";
    }

    public function actionFastOrderEvening(){
        echo 'start update evening';
        $db = Yii::$app->getDb();
        $db->createCommand("UPDATE  shops SET status = 0 WHERE id = 10000258")->execute();
        $db->createCommand("UPDATE  shops SET status = 1 WHERE id = 10000264")->execute();
        $db->createCommand("UPDATE goods_variations SET goods_variations.comission = 30 WHERE   goods_variations.good_id in (SELECT id from goods where type_id = 1014)")->execute();
        $db->createCommand("UPDATE  deliveries_prices SET price= 250 WHERE good_type_id = 1014")->execute();
        echo "\n evening update commission success";
    }

}