<style>
    .material-switch > input[type="checkbox"] {
        display: none;
    }

    .material-switch > label {
        cursor: pointer;
        height: 0px;
        position: relative;
        width: 40px;
    }

    .material-switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 16px;
        margin-top: -8px;
        position: absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 40px;
    }

    .material-switch > label::after {
        background: rgb(255, 255, 255);
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 24px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 24px;
    }

    .material-switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }

    .material-switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }
</style>
<?php

use app\modules\common\models\User;
use app\modules\my\models\Feedback;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\RatesAvg;
use app\models\RatesAvgSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Профайл');
$this->params['breadcrumbs'][] = $this->title;

define('MINIMUM_RANGE_INSIDE_RUB', '1000'); //сумма в рублях порог вхождения
define('MINIMUM_RANGE_INSIDE_COUNT', '10'); //сумма в рублях порог вхождения
define('MINIMUM_RANGE_INSIDE_COUNT_CAT', '15'); //сумма в рублях порог вхождения
define('LAST_BUY_DAYS', '25'); // последний заказ (дней)
define('LIMIT_CAR_FOR_ADD', '1'); // последний заказ (дней)
define('LIMIT_PETS_FOR_ADD', '1'); // последний заказ (дней)
define('LIMIT_CHILDREN_FOR_ADD', '1'); // последний заказ (дней)


$db = Yii::$app->getDb();

$sql = 'SELECT id FROM profile WHERE user_id = ' . $id;
$check_user = $db->createCommand($sql)->queryScalar();

if (empty($check_user) or $check_user == null) {

    $sql = 'SELECT id, name FROM users WHERE id = ' . $id;
    $user = $db->createCommand($sql)->queryOne();

    // \app\modules\common\models\Zloradnij::print_arr($user);die();


    $category_arr = ['car' => '10000244', 'pets' => '10000006', 'children' => '10000004'];
    $out_arr = [];

    foreach ($category_arr as $category => $category_id) {
        $sql = 'SELECT count(*) FROM `orders_items` 
LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id`
LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` 
WHERE `orders_items`.`good_id` IN (SELECT `product_id` FROM `category_links` WHERE `category_id` IN (SELECT `category`.`id` FROM `category` WHERE `category`.`parent_id` = ' . $category_id . ' OR `category`.`id` = ' . $category_id . ' AND`category`.`active` = 1))
AND `orders_items`.`status` = 1 
AND `orders`.`user_id` = ' . $id;
        $out_arr[$category] = $db->createCommand($sql)->queryScalar();
    }


    $car = 0;
    $pets = 0;
    $children = 0;

    if ($out_arr['car'] >= LIMIT_CAR_FOR_ADD) $car = 1;
    if ($out_arr['pets'] >= LIMIT_PETS_FOR_ADD) $pets = 1;
    if ($out_arr['children'] >= LIMIT_CHILDREN_FOR_ADD) $children = 1;

    // Определение пола
    include_once('../libraries/NameCaseLib/Library/NCL.NameCase.ru.php'); // Определение пола
    $nc = new NCLNameCaseRu();
    $gender = $nc->genderDetect($user['name']);

    $pol = 0;
    if ($gender == NCL::$MAN) {
        $pol = 1;
    } elseif ($gender == NCL::$WOMAN) {
        $pol = 2;
    }

    // Заполение таблицы
    $db->createCommand("INSERT INTO `profile` (`user_id`, `gender`, `pets`, `children`, `car`) VALUES (" . $user['id'] . ", " . $pol . ", " . $pets . ", " . $children . ", " . $car . ")")->execute();
    //$last_id = Yii::$app->db->getLastInsertID();

}

$sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`,
                `orders_items`.`price`,
                `orders_items`.`count`,
                `orders_items`.`status`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
    . " (`orders_groups`.`type_id` > 0)"
    . " AND orders.user_id = " . $id
    . " ORDER BY `orders`.`date` ASC";

$statisticGeneral = $db->createCommand($sql)->queryAll();

function sortGraphResult($statisticGeneral, $dateFormat = 'd')
{

    $dataArray = [];

    foreach ($statisticGeneral as $key => $item) {
//    echo date('Y-m-d',strtotime($item['date']))."<br>";
        $index = date('Y-m-d', strtotime($item['date']));
        if (empty($dataArray[$index][$item['type_id']])) {
            $dataArray[$index][$item['type_id']] = 0;
        }
        $dataArray[$index][$item['type_id']] += $item['price'];
    }

// \app\modules\common\models\Zloradnij::print_arr($dataArray);die();

    $cnt = 0;
    $newArray = [];
    $arrItem = [];

    foreach ($dataArray as $key => $item) {
        $arrItem['date'] = $key;
        $arrItem['sum'] = 0;
        foreach ($item as $i => $value) {
            $arrItem[$i] = $value;
            $arrItem['sum'] += $value;
        }
        $newArray[] = $arrItem;
        $arrItem = [];
    }

//arsort($newArray);
// \app\modules\common\models\Zloradnij::print_arr($newArray);


    $header[0] = [Yii::t('admin', 'День')];

//\app\modules\common\models\Zloradnij::print_arr(count($newArray));die();

    $tmpArr = [];
//$cnt=0;
    foreach ($newArray as $item) {
        switch ($dateFormat) {
            case "d":
                $tmpArr[0] = date("d", strtotime($item['date']));
                break;
            case "m":
                $tmpArr[0] = date("m", strtotime($item['date']));
                break;
        };
//            $tmpArr[0] = date("d",strtotime($item['date']));

        if (!empty($item['1001'])) {
            $tmpArr[1] = $item['1001'];
            if (!in_array(Yii::t('admin', 'Товары'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары'));
        } else {
            $tmpArr[1] = 0;
            if (!in_array(Yii::t('admin', 'Товары'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары'));
        }

        if (!empty($item['1002'])) {
            $tmpArr[2] = $item['1002'];
            if (!in_array(Yii::t('admin', 'Услуги'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Услуги'));
        } else {
            $tmpArr[2] = 0;
            if (!in_array(Yii::t('admin', 'Услуги'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Услуги'));
        }

        if (!empty($item['1003'])) {
            $tmpArr[3] = $item['1003'];
            if (!in_array(Yii::t('admin', 'Продукты'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Продукты'));
        } else {
            $tmpArr[3] = 0;
            if (!in_array(Yii::t('admin', 'Продукты'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Продукты'));
        }

        if (!empty($item['1004'])) {
            $tmpArr[4] = $item['1004'];
            if (!in_array(Yii::t('admin', 'Готовая еда'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Готовая еда'));
        } else {
            $tmpArr[4] = 0;
            if (!in_array(Yii::t('admin', 'Готовая еда'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Готовая еда'));
        }

        if (!empty($item['1005'])) {
            $tmpArr[5] = $item['1005'];
            if (!in_array(Yii::t('admin', 'Спортивные товары'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Спортивные товары'));
        } else {
            $tmpArr[5] = 0;
            if (!in_array(Yii::t('admin', 'Спортивные товары'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Спортивные товары'));
        }

        if (!empty($item['1006'])) {
            $tmpArr[6] = $item['1006'];
            if (!in_array(Yii::t('admin', 'Товары под заказ'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары под заказ'));
        } else {
            $tmpArr[6] = 0;
            if (!in_array(Yii::t('admin', 'Товары под заказ'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары под заказ'));
        }

        if (!empty($item['1007'])) {
            $tmpArr[7] = $item['1007'];
            if (!in_array(Yii::t('admin', 'Товары для дома'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары для дома'));
        } else {
            $tmpArr[7] = 0;
            if (!in_array(Yii::t('admin', 'Товары для дома'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары для дома'));
        }

        if (!empty($item['1008'])) {
            $tmpArr[8] = $item['1008'];
            if (!in_array(Yii::t('admin', 'Доставка 5-14 дней'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Доставка 5-14 дней'));
        } else {
            $tmpArr[8] = 0;
            if (!in_array(Yii::t('admin', 'Доставка 5-14 дней'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Доставка 5-14 дней'));
        }

        if (!empty($item['1009'])) {
            $tmpArr[9] = $item['1009'];
            if (!in_array(Yii::t('admin', 'Товары от Pure Protein'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары от Pure Protein'));
        } else {
            $tmpArr[9] = 0;
            if (!in_array(Yii::t('admin', 'Товары от Pure Protein'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Товары от Pure Protein'));
        }

        if (!empty($item['1010'])) {
            $tmpArr[10] = $item['1010'];
            if (!in_array(Yii::t('admin', 'Спортивное питание под заказ'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Спортивное питание под заказ'));
        } else {
            $tmpArr[10] = 0;
            if (!in_array(Yii::t('admin', 'Спортивное питание под заказ'), $header[0]))
                array_push($header[0], Yii::t('admin', 'Спортивное питание под заказ'));
        }

        $tmpArr[11] = $item['sum'];
        $header[] = $tmpArr;
        $tmpArr = [];
    }

    array_push($header[0], Yii::t('admin', 'Всего'));

    //Zloradnij::print_arr($header);die();

    return $header;
}


function variationSort($data)
{
    $temparr = [];

    foreach ($data as $key => $item) {
        if (empty($temparr[$item['variation_id']])) {
            $temparr[$item['variation_id']]['count'] = 0;
        }
        //else

        if (!empty($item['variation_id'])) {
            $var = \app\modules\catalog\models\GoodsVariations::find()
                ->where('id = ' . $item['variation_id'])->one();
            if (!empty($var->product->category->id)) {
                $cat = $var->product->category->id;

                $temparr[$item['variation_id']]['category_id'] = $cat;
                $temparr[$item['variation_id']]['count'] = $temparr[$item['variation_id']]['count'] + $item['count'];
                $temparr[$item['variation_id']]['price'] = $item['price'];
            }

        }
    }
    return $temparr;

}


function variationSort2($data)
{
    $temparr = [];

    foreach ($data as $key => $item) {

        if (!empty($item['variation_id'])) {
            $var = \app\modules\catalog\models\GoodsVariations::find()
                ->where('id = ' . $item['variation_id'])->one();
            if (!empty($var->product->category->id)) {
                $cat = $var->product->category->id;

                if (empty($temparr[$cat])) {
                    $temparr[$cat]['count'] = 0;
                    $temparr[$cat]['category_id'] = 0;
                    $temparr[$cat]['price'] = 0;
                }
                //else

                //$temparr[$item['cate_id']]['category_id'] = $cat;
                $temparr[$cat]['count'] = $temparr[$cat]['count'] + $item['count'];
                $temparr[$cat]['category_id'] = $cat;
                $temparr[$cat]['price'] += $item['price'];
            }
        }

    }
    return $temparr;

}

$tmp = variationSort($data);

//var_dump($tmp);die();

arsort($tmp);
$newTmp = [];

foreach ($tmp as $key => $item) {
    if (!empty($item['price'])) {
        $newTmp[$key]['summa'] = $item['count'] * $item['price'];
        $newTmp[$key]['count'] = $item['count'];
        $newTmp[$key]['category_id'] = $item['category_id'];
        $newTmp[$key]['price'] = $item['price'];
    }
}

//arsort($newTmp);

function array_sort($array, $on, $order = SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

//\app\modules\common\models\Zloradnij::print_arr(array_sort($newTmp,'count',SORT_DESC));
//\app\modules\common\models\Zloradnij::print_arr(array_sort($newTmp,'price',SORT_DESC));
//\app\modules\common\models\Zloradnij::print_arr(array_sort($newTmp,'summa',SORT_DESC));


$arr1 = array_sort($newTmp, 'count', SORT_DESC);

$tmp2 = variationSort2($data);
$arr2 = array_sort($tmp2, 'count', SORT_DESC);

//var_dump($arr1); die();

$user = \app\modules\common\models\User::find()->where('id = ' . $id)->one();

$avgIndex = RatesAvg::find()->orderBy(['id' => SORT_DESC])->one();

$db = Yii::$app->getDb();
$sql = "select SUM((`price` - `discount` - `bonus`) * `count`) as result from orders_items
LEFT JOIN orders_groups ON orders_groups.id = orders_items.order_group_id
LEFT JOIN orders ON orders_groups.order_id = orders.id
WHERE orders_items.status =1 AND orders.user_id = " . $id;
$selfSumm = $db->createCommand($sql)->queryScalar();

$sql = "SELECT Count(*) FROM orders WHERE user_id = " . $id . " AND status = 1";
$selfCount = $db->createCommand($sql)->queryScalar();

if (!$selfCount == 0) {
    $selfIndex = round(($selfSumm / $selfCount), 2);
    if ($selfIndex == 0) $selfIndex = 0;
    $avgSelfIndex = round(floatval($selfIndex) / floatval($avgIndex->rate), 2);
} else {
    $avgSelfIndex = 0;
    $selfIndex = 0;
}

// Добавление индекса в БД
$sql = 'SELECT id FROM profile WHERE status = 1 AND user_id = ' . $id . ' LIMIT 1';
$profile_id = $db->createCommand($sql)->queryScalar();

$sql = 'SELECT created_at FROM profile_links WHERE status = 1 AND profile_id = ' . $profile_id . ' ORDER BY  created_at DESC LIMIT 1';
$index_date = date('Y-m-d', strtotime($db->createCommand($sql)->queryScalar()));

if ($index_date != date('Y-m-d', time())) {
    $db->createCommand("INSERT INTO `profile_links` (`profile_id`, `rate`) VALUES (" . $profile_id . ", " . $avgSelfIndex . ")")->execute();

    // Обновление предпологаемых характеристик
    $category_arr = ['car' => '10000244', 'pets' => '10000006', 'children' => '10000004'];
    $out_arr = [];

    foreach ($category_arr as $category => $category_id) {
        $sql = 'SELECT count(*) FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id` LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` 
WHERE `orders_items`.`good_id` IN (SELECT `product_id` FROM `category_links` WHERE `category_id` IN (SELECT `category`.`id` FROM `category` WHERE `category`.`parent_id` = ' . $category_id . ' OR `category`.`id` = ' . $category_id . ' AND`category`.`active` = 1)) AND `orders_items`.`status` = 1 AND `orders`.`user_id` = ' . $id;
        $out_arr[$category] = $db->createCommand($sql)->queryScalar();
    }

    $car = 0;
    $pets = 0;
    $children = 0;

    if ($out_arr['car'] >= LIMIT_CAR_FOR_ADD) $car = 1;
    if ($out_arr['pets'] >= LIMIT_PETS_FOR_ADD) $pets = 1;
    if ($out_arr['children'] >= LIMIT_CHILDREN_FOR_ADD) $children = 1;

    include_once('../libraries/NameCaseLib/Library/NCL.NameCase.ru.php'); // Определение пола
    $nc = new NCLNameCaseRu();
    $gender = $nc->genderDetect($user['name']);

    $pol = 0;
    if ($gender == NCL::$MAN) {
        $pol = 1;
    } elseif ($gender == NCL::$WOMAN) {
        $pol = 2;
    }

    $db->createCommand('UPDATE `profile` SET `gender`=' . $gender . ', `pets`=' . $pets . ', `children`=' . $children . ', `car`=' . $car . ' WHERE `user_id`=' . $id . ' ')->execute();
}

// \app\modules\common\models\Zloradnij::print_arr($index_date);die();

$header = sortGraphResult($statisticGeneral);

// Получение даты
$sql = 'SELECT `date` FROM `orders` WHERE `orders`.`user_id` = ' . $id . ' ORDER BY `date` DESC LIMIT 1';
$last_buy_date = strtotime($db->createCommand($sql)->queryScalar());

if (!empty($last_buy_date)) {
    $last_buy_date = new DateTime('@' . $last_buy_date);
    $now_date = new DateTime('@' . time());
    $interval = $last_buy_date->diff($now_date);
    $days_diff = $interval->format('%a');
}

// Получение предпологаемых характеристик
$sql = 'SELECT gender, pets, children, car FROM profile WHERE user_id = ' . $id;
$user_features = $db->createCommand($sql)->queryAll()[0];

// Получение индекса
$sql = 'SELECT rate, created_at FROM profile_links WHERE profile_id = (SELECT id FROM profile WHERE user_id = ' . $id . ') AND profile_links.status = 1 ORDER BY created_at DESC';
$rate = $db->createCommand($sql)->queryAll();

// Формирование массива для графика (Динамика индекса)


// \app\modules\common\models\Zloradnij::print_arr($rate);die();

?>

<h1><?= Yii::t('admin', 'Общая информация о клиенте') ?></h1><br><br><br><br>


<div style="margin: -35px 0 10px 0">
    <b><?= Yii::t('admin', 'Количество обращений всего') ?>:</b> <span
            class="badge"><?= Feedback::find()->where(['type_id' => 1002, 'user_id' => $id])->count() ?></span><br>
    <b><?= Yii::t('admin', 'Количество негативных обращений') ?>:</b> <span
            class="badge danger-com"><?= Feedback::find()->where(['active' => 1, 'type_id' => 1002, 'user_id' => $id])->count() ?></span>
    <span
        <?php $a = Feedback::find()->where(['type_id' => 1002, 'user_id' => $id])->count(); if ($a != 0): ?>
            class="text-danger bold"><?= (Feedback::find()->where(['active' => 1, 'type_id' => 1002, 'user_id' => $id])->count() / Feedback::find()->where(['type_id' => 1002, 'user_id' => $id])->count()  * 100) ?>
        <?php else: ?>
            0
        <?php endif; ?>
        %</span>

    <?php if (\Yii::$app->user->can('GodMode') || \Yii::$app->user->can('conflictManager') || \Yii::$app->user->can('callcenterOperator') || \Yii::$app->user->can('categoryManager')): ?>
    <div class="material-switch pull-right">
        <span><a href="/actions/actions/compliment?userId=<?=Yii::$app->request->get('id')?>" onclick="return window_show('/'+ location.hostname +'/actions/actions/compliment?userId=<?=Yii::$app->request->get('id')?>','Добавить скидку');">Скидка</a></span>&nbsp;&nbsp;
        <span><?= Yii::t('admin', 'Предоставить скидку при следующем заказе') ?></span>&nbsp;&nbsp;
        <?php $uid = User::findOne($id);  if ($uid->compliment == 1): ?>
            <input id="switch" name="switch" checked onchange="ajaxCompliment()"
               type="checkbox">
        <?php else: ?>
            <input id="switch" name="switch" onchange="ajaxCompliment()"
               type="checkbox">
        <?php endif; ?>
        <label for="switch" class="label-success"></label>
        <?php endif; ?>
    </div>


    <script>
        function ajaxCompliment() {
            var chk = $('#switch').prop('checked');
            $.ajax({
                type: 'GET',
                url: '/support/compliment',
                data: {'user_id': <?= $id ?>, 'switch': chk},
                success: function (data) {
                    console.log(data);
                }
            });
        }
    </script>
</div>


<div id="profile">
    <div class="col-md-6">
        <div class="row">
            <table id="table_total" class="table table-bordered">
                <thead>
                <tr>
                    <td colspan="2" class="text-center"><?= Yii::t('admin', 'Суммарно') ?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= Yii::t('admin', 'ФИО') ?></td>
                    <td><?= $user->name ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Регистрация') ?></td>
                    <td><?= $user->registration ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Телефон') ?></td>
                    <td><?= $user->phone ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Деньги (бонусы)') ?></td>
                    <td><?= number_format($user->money, 0, ',', ' '); ?> ₽ (<?= $user->bonus ?> β)</td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Средний чек по магазину') ?></td>
                    <td><?= number_format($avgIndex->rate, 0, ',', ' '); ?> ₽</td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Средний чек клиента') ?></td>
                    <td><?= number_format($selfIndex, 0, ',', ' '); ?> ₽</td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Всего потратил') ?></td>
                    <td><?= number_format($selfSumm, 0, ',', ' '); ?> ₽</td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Всего заказов') ?></td>
                    <td><?= $selfCount ?> шт.</td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Последний заказ') ?></td>
                    <?php if (!empty($last_buy_date) && $days_diff > LAST_BUY_DAYS): ?>
                        <td class="text-danger"><?= $days_diff ?> <?= Yii::t('admin', 'дней назад') ?></td>
                    <?php elseif (!empty($last_buy_date) && $days_diff = LAST_BUY_DAYS): ?>
                        <td class="text-success"><?= Yii::t('admin', 'Сегодня') ?></td>
                    <?php elseif (!empty($last_buy_date) && $days_diff < LAST_BUY_DAYS): ?>
                        <td class="text-success"><?= $days_diff ?> <?= Yii::t('admin', 'дней назад') ?></td>
                    <?php else: ?>
                        <td class="text-danger"><?= Yii::t('admin', 'Нет покупок') ?></td>
                    <?php endif; ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-5 col-md-offset-1">
        <div class="row">
            <table id="table_features" class="table table-bordered">
                <thead>
                <tr>
                    <td colspan="2" class="text-center"><?= Yii::t('admin', 'Предполагаемые характеристики') ?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= Yii::t('admin', 'Возраст') ?></td>
                    <td><?= Yii::t('admin', 'Неизвестно') ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Пол') ?></td>
                    <?php if ($user_features['gender'] == 1): ?>
                        <td class="text-primary"><?= Yii::t('admin', 'Мужской') ?></td>
                    <?php elseif ($user_features['gender'] == 2): ?>
                        <td class="text-primary"><?= Yii::t('admin', 'Женский') ?></td>
                    <?php else: ?>
                        <td><?= Yii::t('admin', 'Неизвестно') ?></td>
                    <?php endif; ?>

                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Наличие детей') ?></td>
                    <?php if ($user_features['children'] == 1): ?>
                        <td class="text-primary"><?= Yii::t('admin', 'Есть') ?></td>
                    <?php else: ?>
                        <td><?= Yii::t('admin', 'Неизвестно') ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Наличие дом. животных') ?></td>
                    <?php if ($user_features['pets'] == 1): ?>
                        <td class="text-primary"><?= Yii::t('admin', 'Есть') ?></td>
                    <?php else: ?>
                        <td><?= Yii::t('admin', 'Неизвестно') ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td><?= Yii::t('admin', 'Наличие автомобиля') ?></td>
                    <?php if ($user_features['car'] == 1): ?>
                        <td class="text-primary"><?= Yii::t('admin', 'Есть') ?></td>
                    <?php else: ?>
                        <td><?= Yii::t('admin', 'Неизвестно') ?></td>
                    <?php endif; ?>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div class="row">
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript" src="https://www.google.com/jsapi"></script>
            <script type="text/javascript">


                google.load("visualization", "1", {packages: ["corechart"]});
                google.setOnLoadCallback(drawChart);

                function drawChart() {

                    var obj = [
                        <?php  foreach ($rate as $r): ?>
                        [new Date(<?php $m = intval(date('n', strtotime($r['created_at']))); echo date('Y', strtotime($r['created_at'])) . ', ' . $m . ', ' . date('j', strtotime($r['created_at'])) ?>),   <?= $r['rate'] ?>],
                        <?php endforeach; ?>

                    ];

                    var data = new google.visualization.DataTable();
                    data.addColumn('date', '<?= Yii::t('admin', 'Дни') ?>');
                    data.addColumn('number', '<?= Yii::t('admin', 'Индекс') ?>');
                    data.addRows(obj);


                    var options = {
                        width: '100%',
                        height: 110,
                        vAxis: {title: '<?= Yii::t('admin', 'Индекс') ?>'},
                        backgroundColor: {fill: 'transparent'},
                        title: '<?= Yii::t('admin', 'Динамика индекса за все время') ?>',
                        hAxis: {
                            //format: "HH:mm"
                            //format: "HH:mm:ss"
                            format: 'd.M'
                        },
                        explorer: {
                            actions: ['dragToZoom', 'rightClickToReset'],
                            axis: 'vertical'
                        }
                    };

                    var chart = new google.visualization.LineChart(
                        document.getElementById('chart_index'));
                    chart.draw(data, options);
                }

            </script>
            <div id="chart_index" style="width: 100%; height: 110px; vertical-align: top;"></div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="row">
            <h2 class="text-center rate bg-warning"><?= Yii::t('admin', 'Покупательский индекс') ?>:

                <?php if (!empty($rate)): ?>
                    <?php if (!empty($rate[1]['rate']) && $rate[0]['rate'] > $rate[1]['rate']): ?>
                        <b class="text-success"><?= $rate[0]['rate'] ?> &uarr;
                            <small>+<?= $rate[0]['rate'] - $rate[1]['rate'] ?></small>
                        </b>
                    <?php elseif (!empty($rate[1]['rate']) && $rate[0]['rate'] < $rate[1]['rate']): ?>
                        <b class="text-danger"><?= $rate[0]['rate'] ?> &darr;
                            <small>-<?= $rate[1]['rate'] - $rate[0]['rate'] ?></small>
                        </b>
                    <?php else: ?>
                        <b><?= $rate[0]['rate'] ?></b>
                    <?php endif; ?>
                <?php else: ?>
                    <b><?= Yii::t('admin', 'Неизвестен') ?></b>
                <?php endif; ?>
            </h2>
        </div>
    </div>


</div> <!--/profile end-->

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td colspan="2"><br>

            <script type="text/javascript">
                google.charts.load('current', {'packages': ['corechart']});
                google.charts.setOnLoadCallback(drawVisualization);
                /*google.charts.setOnLoadCallback(drawVisualizationMonth);*/
                google.charts.setOnLoadCallback(drawVisualizationYear);

                function drawVisualization() {
                    // Some raw data (not necessarily accurate)
                    var data = google.visualization.arrayToDataTable([

                        <?php

                        foreach ($header as $i => $item) {
                            $str = '[';
                            foreach ($item as $key => $value) {
                                if ($i == 0) {
                                    $str .= "'" . $value . "', ";
                                } else {
                                    if ($key == 0 && $i > 0) {
                                        $str .= "'" . $value . "', ";
                                    } else {
                                        $str .= $value . ", ";
                                    }
                                }
                            }
                            $str .= '],';
                            echo $str . "\n";
                            $str = '';
                        }

                        ?>
                    ]);

                    var options = {
                        title: '<?= Yii::t('admin', 'Динамика покупок за все время') ?>',
                        vAxis: {title: '<?= Yii::t('admin', 'Рубли') ?>'},
                        hAxis: {title: '<?= Yii::t('admin', 'Дни') ?>'},
                        seriesType: 'bars',
                        curveType: 'function',
                        backgroundColor: {fill: 'transparent'},
                        series: {10: {type: 'line'}},
                        colors: ['#ff0000', '#0000ff', '#008edf', '#ff5a00', '#ff5a00', '#00a8df', '#af4726', '#ff5a00', '#6400df', '#f6c7b6', '#268800',],
                    };

                    var chart = new google.visualization.ComboChart(document.getElementById('chart_div1'));

                    chart.draw(data, options);
                }

            </script>

            <div id="chart_div1" style="width: 100%; vertical-align: top;"></div>
        </td>
    </tr>
</table>


<h2><?= Yii::t('admin', 'Популярные категории (для предложений):') ?></h2>

<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <td colspan="4">
            <h4><?= Yii::t('admin', 'Популярные категории на сумму более') ?> <?= MINIMUM_RANGE_INSIDE_RUB ?> <?= Yii::t('admin', '₽. за все время (Сорт. Кол-во)') ?></h4>
        </td>
    </tr>
    <tr>
        <th>#</th>
        <th><?= Yii::t('admin', 'Категория') ?></th>
        <th><?= Yii::t('admin', 'Количество') ?></th>
        <th><?= Yii::t('admin', 'Сумма, ₽') ?></th>
    </tr>
    </thead>
    <tbody>

    <?php $i = 1;
    foreach ($arr2 as $key => $item) {
        if ($item['price'] > MINIMUM_RANGE_INSIDE_RUB && !empty($key)) { ?>
            <tr>
                <th scope="row"><?= $i ?></th>
                <td><?php
                    $catid = $item['category_id'];
                    $cat = \app\modules\catalog\models\Category::find()
                        ->where('id = ' . $catid)->one();
                    echo '<small>' . $catid . '</small> ' . $cat->title;

                    ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['price'] ?></td>
            </tr>
            <?php $i++;
        }
    } ?>
    </tbody>
</table>


<h2><?= Yii::t('admin', 'Популярные товары (для предложений)') ?>:</h2>
<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <td colspan="4">
            <h4><?= Yii::t('admin', 'Популярные товары на сумму более') ?> <?= MINIMUM_RANGE_INSIDE_RUB ?> <?= Yii::t('admin', '₽. за все время (Сорт. Кол-во)') ?></h4>
        </td>
    </tr>
    <tr>
        <th>#</th>
        <th><?= Yii::t('admin', 'Товар') ?></th>
        <th><?= Yii::t('admin', 'Цена, ₽') ?></th>
        <th><?= Yii::t('admin', 'Количество') ?></th>
        <th><?= Yii::t('admin', 'Сумма, ₽') ?></th>
    </tr>
    </thead>
    <tbody>

    <?php $i = 1;
    foreach ($arr1 as $key => $item) {
        if ($item['summa'] > MINIMUM_RANGE_INSIDE_RUB && !empty($key)) {
            ?>
            <tr>
                <th scope="row"><?= $i ?></th>
                <td>
                    <?php
                    $goodVariation
                        = \app\modules\catalog\models\GoodsVariations::find()
                        ->where('id = ' . $key)->one();
                    $good = \app\modules\catalog\models\Goods::find()
                        ->where('id = ' . $goodVariation->good_id)->one();
                    echo $good->name;
                    ?>
                </td>
                <td><?php echo round($item['price'], 0) ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['summa'] ?></td>
            </tr>
            <?php
            $i++;
        }

    }
    ?>

    </tbody>
</table><br>


<?php
$newTmp = array_sort($newTmp, 'price', SORT_DESC);
$j = 1;
?>

<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <td colspan="4">
            <h4><?= Yii::t('admin', 'Популярные товары на сумму более') ?> <?= MINIMUM_RANGE_INSIDE_RUB ?> <?= Yii::t('admin', '₽. за все время (Сорт. Цена)') ?></h4>
        </td>
    </tr>
    <tr>
        <th>#</th>
        <th><?= Yii::t('admin', 'Товар') ?></th>
        <th><?= Yii::t('admin', 'Цена, ₽') ?></th>
        <th><?= Yii::t('admin', 'Количество') ?></th>
        <th><?= Yii::t('admin', 'Сумма, ₽') ?></th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($newTmp as $key => $item) {
        if ($item['summa'] > MINIMUM_RANGE_INSIDE_RUB && !empty($key)) {
            ?>
            <tr>
                <th scope="row"><?= $j ?></th>
                <td>
                    <?php
                    //if(!empty($item['variation_id'])) {
                    $goodVariation
                        = \app\modules\catalog\models\GoodsVariations::find()
                        ->where('id = ' . $key)->one();
                    $good = \app\modules\catalog\models\Goods::find()
                        ->where('id = ' . $goodVariation->good_id)->one();
                    echo $good->name;
                    //}
                    ?>
                </td>
                <td><?= $item['price'] ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['summa'] ?></td>
            </tr>
            <?php
            $j++;
        }

    }
    ?>

    </tbody>
</table><br>

<?php $newTmp = array_sort($newTmp, 'summa', SORT_DESC);
$c = 1; ?>

<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <td colspan="4">
            <h4><?= Yii::t('admin', 'Популярные товары на сумму более') ?> <?= MINIMUM_RANGE_INSIDE_RUB ?> <?= Yii::t('admin', '₽. за все время (Сорт. по Сумме)') ?></h4>
        </td>
    </tr>
    <tr>
        <th>#</th>
        <th><?= Yii::t('admin', 'Товар') ?></th>
        <th><?= Yii::t('admin', 'Цена, ₽') ?></th>
        <th><?= Yii::t('admin', 'Количество') ?></th>
        <th><?= Yii::t('admin', 'Сумма, ₽') ?></th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($newTmp as $key => $item) {
        if ($item['summa'] > MINIMUM_RANGE_INSIDE_RUB && !empty($key)) {
            ?>
            <tr>
                <th scope="row"><?= $c ?></th>
                <td>
                    <?php
                    //  if(!empty($item['variation_id'])) {
                    $goodVariation
                        = \app\modules\catalog\models\GoodsVariations::find()
                        ->where('id = ' . $key)->one();
                    $good = \app\modules\catalog\models\Goods::find()
                        ->where('id = ' . $goodVariation->good_id)->one();
                    echo $good->name;
                    //}
                    ?>
                </td>
                <td><?= $item['price'] ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['summa'] ?></td>
            </tr>
            <?php
            $c++;
        }

    }
    ?>

    </tbody>
</table><br>


<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <td colspan="4"><h4><?= Yii::t('admin', 'Все товары за все время с номерами заказов') ?></h4></td>
    </tr>
    <tr>
        <th>#</th>
        <th><?= Yii::t('admin', 'Товар') ?></th>
        <th><?= Yii::t('admin', 'Цена, ₽') ?></th>
        <th><?= Yii::t('admin', 'Количество') ?></th>
        <td><?= Yii::t('admin', 'Заказ') ?></td>
        <td><?= Yii::t('admin', 'Дата') ?></td>
    </tr>
    </thead>
    <tbody>

    <?php if (!empty($key)) { ?>
        <?php $a = 1;
        foreach ($data as $key => $item) { ?>
            <tr>
                <th scope="row"><?= $key + 1 ?></th>
                <td>                <?php

                    if (!empty($item['variation_id'])) {
                        $goodVariation
                            = \app\modules\catalog\models\GoodsVariations::find()
                            ->where('id = ' . $item['variation_id'])->one();
                        $good = \app\modules\catalog\models\Goods::find()
                            ->where('id = ' . $goodVariation->good_id)->one();
                        echo $good->name;
                    }
                    ?>
                </td>
                <td><?= $item['price'] ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['order_id'] ?></td>
                <td><?= $item['date'] ?></td>
            </tr>

            <?php $a++;
        }
    } ?>
    </tbody>
</table>
