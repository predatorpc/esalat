<?php

//last version 29082016

namespace app\controllers;

use app\models\RatesAvg;
use app\modules\basket\models\Basket;
use app\modules\basket\models\BasketSearch;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\CategoryLinks;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsPreorderSearch;
use app\modules\catalog\models\GoodsSearch;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\GoodsVariationsSearch;
use app\modules\catalog\RatesAvgSearch;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\HelperConnector;
use app\modules\common\models\Api;
use app\modules\common\models\UserSearchDefault;
use app\modules\common\models\UserSearchDefault2;
use app\modules\common\models\UsersLogsSearch;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\ShopsStores;
use app\modules\managment\models\ShopStoresTimetable;
use app\modules\shop\models\OrderFilter;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
//use app\modules\shop\models\OrdersGroupsSearch;
use app\modules\shop\models\OrdersItems;
use app\modules\shop\models\OrdersSearch;
use app\modules\shop\models\OwnerOrderFilter;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsSearch;
use DateInterval;
use app\modules\common\models\ProfileSearch;
use app\modules\common\models\Profile;
use DatePeriod;
use DateTime;
use kartik\form\ActiveForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\common\models\user;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;


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
                            'orders-zlrdn',
                            'orders_delivery_cancel',
                            'orders_delivery_double',
                            'orders_delivery_surcharge',
                            'orders_driver_set',
                            'orders_items',
                            'orders_items_status',
                            'orders_item_cancel',
                            'order',
                            'delivery',
                            'find-order',
                            'find-order-list',
                            'test-sma',
                            'delivery-plus-money',
                            'abandoned-basket-report',
                            'sales-report',
                            'master-statistic',
                            'master-pay',
                            'master-tanya',
                            'short-order-statistic',
                            'profile',
                            'profile-report',
                            'shame-board',
                            'generate-users-average-activity-index',
                            'xml',
                            'xls',
                            'shopsgoods',
                            'viewgoods',
                            'basketdelete',
                            'preorder',
                            'real-clients',
                            'real-clients2',
//                            'orderpreorder',
                            'category-product',
                            'new-goods',
                            'orders_item_return',
                            'reports-orders-month',
                            'vit',
                            'order-new',
                            'order-new-data-slice',
                            'change-order-time',
                            'reports-users',
                            'gold',
                            'mini-order-new'
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'categoryManager', 'conflictManager', 'callcenterOperator', 'clubAdmin', 'helperManager', 'HR'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'orders_item_return'=>['post'],
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

    public static function sksort(&$array, $subkey="id", $sort_ascending=false) {
    if (count($array))
            $temp_array[key($array)] = array_shift($array);
            
    foreach($array as $key => $val){
                        $offset = 0;
                        $found = false;
                        foreach($temp_array as $tmp_key => $tmp_val)
                        {
                                if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
                                {
                                $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                            array($key => $val),
                        array_slice($temp_array,$offset)
                  );
          $found = true;
     }
     }
     }
     }
     
                                                                                                                                                                                                                                                                                              
    public function actionVit(){
    
        $db = Yii::$app->getDb();
        $sql =
	    "SELECT orders.id as oid, users.id, users.phone, users.name FROM `orders` 
	    LEFT JOIN users ON users.id = orders.user_id
	    WHERE `orders`.`user_id` IN
	    (10023372, 10023373, 10007263, 10022626, 10023389, 10023166, 10023395, 10023399, 10023400, 10023401, 10020391, 10023412, 10023259, 10001546, 10023416, 10023422, 10023427, 10023431, 10001515,10023432, 10023434, 10009262, 10023439, 10013527, 10023440, 10023447, 10023448, 10022451, 10022740, 10023461, 10023465, 10022395, 10011334, 10023473, 10016300, 10011545, 10020996, 10023489, 10000455, 10023498, 10013388, 10017334, 10021493,10023504, 10023511, 10023514, 10023515, 10013213, 10022318, 10023529, 10006179, 10023535, 10023446, 10022326, 10023577, 10023587, 10023591, 10022639, 10023598, 10023605, 10023607, 10023615, 10023625, 10023630, 10020623, 10023635, 10023604, 10023644, 10023658, 10023665, 10023666, 10023048, 10020956, 10023683, 10023672, 10023688, 10023690, 10023691, 10023697, 10023716, 10023728, 10023730, 10023731, 10007156, 10023736, 10023737, 10023745, 10023760, 10023069, 10023761, 10023762, 10023765, 10023164, 10022627, 10021743, 10023775, 10023777, 10015435, 10020863, 10022975, 10009217, 10023796, 10023808, 10022998, 10023732, 10023817, 10011030, 10023814, 10023838, 10022890, 10022944, 10001763, 10023858, 10023860, 10023865, 10023868, 10023878, 10023881, 10023886, 10023891, 10023892, 10023869, 10023899, 10021842, 10023906, 10023908, 10023248, 10017880, 10023924, 10023925, 10023948, 10021844, 10022813, 10023967, 10014241, 10023986, 10023971, 10023996, 10024002, 10024003, 10024004, 10024007, 10024031, 10022524, 10024060, 10017771, 10024072, 10003011, 10022762, 10010580, 10020388, 10015946, 10022233, 10022932)
	    AND `orders`.`date` >= DATE('2017-03-29 00:00:00')
	    AND orders.date <= DATE('".date("Y-m-d 00:00:00",strtotime("now"))."')
	    AND orders.status =1
	    ORDER BY users.name ASC";
    
        $users = $db->createCommand($sql)->queryAll();
        echo "<html><head></head><body><h3>Клиенты сделавшие покупки после подарка 29/03/2017</h3><table>";
        echo "<td>ID</td><td>UID</td><td>Заказ</td><td>Телефон</td><td>ФИО</td>";
        
        $counter = 0;
        foreach ($users as $key=>$item){
                $color = ($counter%2)==0 ? "'#CCCCCC'" : "'#ffffff'";
                echo "<tr bgcolor=".$color.">";
                echo "<td>".$counter."</td>";
                echo "<td>".$item['id']."</td>";
                echo "<td>".$item['oid']."</td>";
                echo "<td>".$item['phone']."</td>";
                echo "<td>".$item['name']."</td>";
                echo "</tr>";
    		$counter++;
	}
        echo "</table>";
        //die();
        
        $sql = "SELECT goods.name, goods.full_name, orders_items.* FROM `orders_items` LEFT JOIN goods ON orders_items.good_id = goods.id WHERE `store_id` = 10000196";    
        $ordersItems = $db->createCommand($sql)->queryAll();
        
//      echo "<pre>";
//      print_r($ordersItems);
//	echo "</pre>";
        
        echo "<table><td>ID</td><td>Good ID</td><td>Name</td><td>Count</td>";
        
        
	$ar = [];
        foreach($ordersItems as $key=>$item){    	
    	    if(empty($ar[$item['good_id']])){
    		$ar[$item['good_id']]=[];    	
    		$ar[$item['good_id']]['name']=0;    
    		$ar[$item['good_id']]['count']=0;    
    	    }    	    
    	    $ar[$item['good_id']]['count']+=$item['count'];
    	    $ar[$item['good_id']]['name']=$item['name'];
        }

//	$this->sksort($ar, 'count');

        $counter = 0;
        foreach ($ar as $key=>$item){
                $color = ($counter%2)==0 ? "'#CCCCCC'" : "'#ffffff'";
                echo "<tr bgcolor=".$color.">";
                echo "<td>".$counter."</td>";
                echo "<td>".$key."</td>";
                echo "<td>".$item['name']."</td>";
                echo "<td>".$item['count']."</td>";
//                echo "<td>".$item['name']."</td>";
                echo "</tr>";
    		$counter++;
	}
            
      echo "<pre>";
//      print_r($ar);
      echo "</pre>";
        
        
        echo "</table></body></html>";
        die();
    }

    //SELECT * FROM `users` WHERE NOT EXISTS ( SELECT orders.user_id FROM orders WHERE orders.user_id = users.id AND orders.date > '2016-10-01' ) AND users.staff is not null AND users.typeof is not null AND users.status = 1

    public function actionShameBoard($date_start = null, $date_end = null)
    {
        $db = Yii::$app->getDb();

        if(empty($date_start))
            $date_start = date('Y-m-d 00:00:00', strtotime("-7 day"));
        else
            $date_start = date('Y-m-d 00:00:00', strtotime($date_start));

        if(empty($date_end))
            $date_end = date('Y-m-d 23:59:59', strtotime("now"));
        else
            $date_end = date('Y-m-d 23:59:59', strtotime($date_end));

        $sql =
            "SELECT * FROM users WHERE NOT EXISTS ( SELECT orders.user_id FROM orders WHERE orders.user_id = users.id AND orders.date > '"
            . $date_start
            . "' AND orders.date < '" . $date_end
            . "') AND users.staff IS NOT NULL AND users.typeof IS NOT NULL AND users.status = 1 
                AND users.id NOT IN (10004448, 10017651, 10013181, 10000039) ORDER BY users.name DESC";

        $users = $db->createCommand($sql)->queryAll();

        return $this->render('shame-board', [
            'users' => $users,
            'date_start' => $date_start,
            'date_end' => $date_end,
        ]);

    }

    /*public function actionGenerateUsersAverageActivityIndex()
    {
        $db = Yii::$app->getDb();

        $sql = "select SUM((`price` - `discount` - `bonus`) * `count`) as result from orders_items WHERE status =1";
        $data = $db->createCommand($sql)->queryScalar();

        $sql = "SELECT count(*) FROM orders_items";
        $cntItems = $db->createCommand($sql)->queryScalar();

        $sql = "SELECT count(*) FROM users";
        $cntUsers = $db->createCommand($sql)->queryScalar();

        $searchModel = new \app\models\RatesAvgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('avgusrindx',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'data' => $data,
            'cntItems' => $cntItems,
            'cntUsers' => $cntUsers,
        ]);
    }*/

    public function actionProfileReport()
    {
        $searchModel = new ProfileSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('profile-report', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    public function actionBasketdelete($id,$url)
    {
        $basket = Basket::find()->where(['id'=>$id])->one();
        $basket->status = -1;
        if($basket->save(true)){
            return $this->redirect($url);
        }
    }

    public function actionGenerateUsersAverageActivityIndex($index = null)
    {
        $db = Yii::$app->getDb();
        $sql = "SELECT SUM((`price` - `discount` - `bonus`) * `count`) AS result FROM orders_items WHERE status =1";
        $data = $db->createCommand($sql)->queryScalar();

        $sql = "SELECT count(*) FROM orders_items";
        $cntItems = $db->createCommand($sql)->queryScalar();

        $sql = "SELECT count(*) FROM users";
        $cntUsers = $db->createCommand($sql)->queryScalar();

        $searchModel = new \app\models\RatesAvgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $oldIndex = RatesAvg::find()->orderBy(['id' => SORT_DESC])->one();

        //var_dump(floatval($oldIndex->rate));die();

        $okFlag = false;
        if (!empty($index) && floatval($index) != floatval($oldIndex->rate)) {
            $avg = new RatesAvg();
            $avg->name = 'Customer Average Activity Index';
            $avg->rate = floatval($index);
            $avg->date = strtotime(date("Y-m-d H:i:s"));
            if ($avg->save()) {
                $okFlag = true;
            } else {
                //   var_dump($avg->errors);die();
            };
            return $this->render('avgusrindx', [
                'index' => floatval($index),
                'okFlag' => $okFlag,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data' => $data,
                'cntItems' => $cntItems,
                'cntUsers' => $cntUsers,
            ]);
        } else {
            return $this->render(
                'avgusrindx', [
                    'index' => null,
                    'okFlag' => $okFlag,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'data' => $data,
                    'cntItems' => $cntItems,
                    'cntUsers' => $cntUsers,
                ]
            );
        }
    }

    public function actionProfile($id = null)
    {

        $db = Yii::$app->getDb();

        $sql = "
        SELECT users.id AS user_id, orders.date AS date, orders.id AS order_id, orders_items.variation_id AS variation_id,
                  orders_items.price AS price, orders_items.count AS count
        FROM `orders`
        LEFT JOIN users ON users.id = orders.user_id
        LEFT JOIN orders_groups ON orders.id = orders_groups.order_id
        LEFT JOIN orders_items ON orders_items.order_group_id = orders_groups.id
        WHERE
        orders.user_id = " . $id
            . " AND `orders`.`status` = 1 ORDER BY `variation_id` DESC";
        $data = $db->createCommand($sql)->queryAll();

        return $this->render('profile',
            [
                'id' => $id,
                'data' => $data,
            ]);
    }

    public function actionSalesReport()
    {
        $this->view->registerJsFile('http://code.highcharts.com/highcharts.js', ['depends' => 'yii\web\JqueryAsset']);

        if (isset($_POST['date1']) && isset($_POST['date2'])) {

            $db = Yii::$app->getDb();

            // Получение введенных дат
            $begin = $_POST['date1'];
            $end = $_POST['date2'];

// Для теста
            /*$begin = '2016-08-23';
            $end   = '2016-08-25';*/

            $date1 = new DateTime($begin);
            $date2 = new DateTime($end);

            // Преобразование дат
            //$date1 = new DateTime($_POST['date1']);
            //$date2 = new DateTime($_POST['date2']);

            // Разница между датами
            //$interval = date_diff($date1, $date2);

            // Получение общей суммы
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS `date_sum`, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY `date_sum` ORDER BY `orders`.`date` DESC";
            $sum = $db->createCommand($sql)->queryAll();

            // Получение суммы продуктов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) AND `goods`.`type_id` = 1003 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod = $db->createCommand($sql)->queryAll();

            $date2 = $date2->modify('+1 day');

            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($date1, $interval, $date2);

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products' => 0);
            }

            $merged_normalized =
                array_column($sum_prod, 'money_products', 'date1') +
                array_column($arr, 'money_products', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod = array_reverse($result);
            $arr3['test'] = $result;
            unset($result);


            // Получение кол-во заказов для общей суммы
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, COUNT(DISTINCT `orders`.`id`) AS `sum_count` FROM `orders` LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` = `orders_groups`.`id` WHERE `orders`.`type` = 1 AND `orders`.`status` = 1 AND `orders_items`.`status` = 1 AND `orders_groups`.`status` = 1 AND `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_count = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'sum_count' => 0);
            }

            $merged_normalized =
                array_column($sum_count, 'sum_count', 'date1') +
                array_column($arr, 'sum_count', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'sum_count' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_count = array_reverse($result);

            unset($result);


            // Получение кол-во заказов для продуктов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, COUNT(DISTINCT `orders`.`id`) AS `sum_prod_count`  FROM `orders` LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` =  `orders_groups`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`WHERE `orders`.`type` = 1 AND `orders`.`status` = 1
AND `orders_items`.`status` = 1 AND `orders_groups`.`status` = 1 AND `goods`.`type_id` = 1003 AND `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod_count = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'sum_prod_count' => 0);
            }

            $merged_normalized =
                array_column($sum_prod_count, 'sum_prod_count', 'date1') +
                array_column($arr, 'sum_prod_count', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'sum_prod_count' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod_count = array_reverse($result);

            unset($result);


            // Получение кол-во заказов для "Товары для дома"
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, COUNT(DISTINCT `orders`.`id`) AS `sum_home_count`  FROM `orders` LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` =  `orders_groups`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`WHERE `orders`.`type` = 1 AND `orders`.`status` = 1
AND `orders_items`.`status` = 1 AND `orders_groups`.`status` = 1 AND `goods`.`type_id` = 1007 AND `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_home_count = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'sum_home_count' => 0);
            }

            $merged_normalized =
                array_column($sum_home_count, 'sum_home_count', 'date1') +
                array_column($arr, 'sum_home_count', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'sum_home_count' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_home_count = array_reverse($result);

            unset($result);


            // Получение кол-во заказов для "Спортпита"
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, COUNT(DISTINCT `orders`.`id`) AS `money_sportpit_count`  FROM `orders` LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` =  `orders_groups`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`WHERE `orders`.`type` = 1 AND `orders`.`status` = 1
AND `orders_items`.`status` = 1 AND `orders_groups`.`status` = 1 AND `goods`.`type_id` = 1005 AND `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $money_sportpit_count = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_sportpit_count' => 0);
            }

            $merged_normalized =
                array_column($money_sportpit_count, 'money_sportpit_count', 'date1') +
                array_column($arr, 'money_sportpit_count', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_sportpit_count' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $money_sportpit_count = array_reverse($result);

            unset($result);


            // Получение суммы продуктов и товаров для дома (руб.) для сотрудников
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products_staff` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY ) AND `goods`.`type_id` IN (1003, 1007) AND `users`.`staff` = 1 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod_staff = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products_staff' => 0);
            }

            $merged_normalized =
                array_column($sum_prod_staff, 'money_products_staff', 'date1') +
                array_column($arr, 'money_products_staff', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products_staff' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod_staff = array_reverse($result);
            unset($result);

            // Получение суммы товаров для дома (руб.)
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products_home` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY ) AND `goods`.`type_id` = 1007 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1
GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_home = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products_home' => 0);
            }

            $merged_normalized =
                array_column($sum_home, 'money_products_home', 'date1') +
                array_column($arr, 'money_products_home', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products_home' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_home = array_reverse($result);
            unset($result);

            // Получение суммы товаров для спортпита (руб.)
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_sportpit` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY ) AND `goods`.`type_id` = 1005 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0  AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_sportpit = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_sportpit' => 0);
            }

            $merged_normalized =
                array_column($sum_sportpit, 'money_sportpit', 'date1') +
                array_column($arr, 'money_sportpit', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_sportpit' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_sportpit = array_reverse($result);
            unset($result);


            // Получение суммы продуктов и товаров для дома (руб.) для клиентов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products_client` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY )AND `goods`.`type_id` IN (1003, 1007) AND `users`.`staff` IS NULL AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod_client = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products_client' => 0);
            }

            $merged_normalized =
                array_column($sum_prod_client, 'money_products_client', 'date1') +
                array_column($arr, 'money_products_client', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products_client' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod_client = array_reverse($result);
            unset($result);

            // Получение суммы продуктов и товаров для дома (бонусов) для сотрудников
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`bonus`)*(`orders_items`.`count`)) AS `bonus_products_staff` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY )AND `goods`.`type_id` IN (1003, 1007) AND `users`.`staff` = 1 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sp_bonus_staff = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'bonus_products_staff' => 0);
            }

            $merged_normalized =
                array_column($sp_bonus_staff, 'bonus_products_staff', 'date1') +
                array_column($arr, 'bonus_products_staff', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'bonus_products_staff' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sp_bonus_staff = array_reverse($result);
            unset($result);

            // Получение суммы продуктов и товаров для дома (бонусов) для клиенов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`bonus`)*(`orders_items`.`count`)) AS `bonus_products_client` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY ) AND `goods`.`type_id` IN (1003, 1007) AND `users`.`staff` IS NULL AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sp_bonus_client = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'bonus_products_client' => 0);
            }

            $merged_normalized =
                array_column($sp_bonus_client, 'bonus_products_client', 'date1') +
                array_column($arr, 'bonus_products_client', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'bonus_products_client' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sp_bonus_client = array_reverse($result);
            unset($result);

            // Формирование финального массива
            function arr_merge()
            {
                $output = array();
                foreach (func_get_args() as $array) {
                    foreach ($array as $key => $value) {
                        $output[$key] = isset($output[$key]) ?
                            array_merge($output[$key], $value) : $value;
                    }
                }
                return $output;
            }

            $arr = arr_merge($sum, $sum_prod);
            $arr = arr_merge($arr, $sum_home);
            $arr = arr_merge($arr, $sum_sportpit);
            $arr = array_map(
                function ($x) {
                    $x['money_other'] = $x['money'] - $x['money_products'] - $x['money_products_home'] - $x['money_sportpit'];
                    return $x;
                },
                $arr
            );

            for ($i = count($sum_prod_staff) - 1; $i >= 0; $i--) {
                $sum_prod_staff[$i]['money_products_staff'] -= $sp_bonus_staff[$i]['bonus_products_staff'];
            }

            $arr2 = arr_merge($arr, $sum_prod_staff);
            $arr2 = arr_merge($arr2, $sp_bonus_staff);

            for ($i = count($sum_prod_client) - 1; $i >= 0; $i--) {
                $sum_prod_client[$i]['money_products_client'] -= $sp_bonus_client[$i]['bonus_products_client'];
            }

            $arr2 = arr_merge($arr2, $sum_prod_client);
            $arr2 = arr_merge($arr2, $sp_bonus_client);
            $arr2 = arr_merge($arr2, $sum_count);
            $arr2 = arr_merge($arr2, $sum_prod_count);
            $arr2 = arr_merge($arr2, $sum_home_count);
            $arr2 = arr_merge($arr2, $sum_home);
            $arr2 = arr_merge($arr2, $money_sportpit_count);

            $sum_count_other = [];
            for ($i = count($sum_count) - 1; $i >= 0; $i--) {
                if (empty($sum_count[$i]['sum_count'])) $sum_count[$i]['sum_count'] = 0;
                if (empty($sum_prod_count[$i]['sum_prod_count'])) $sum_prod_count[$i]['sum_prod_count'] = 0;
                if (empty($sum_home_count[$i]['sum_home_count'])) $sum_home_count[$i]['sum_home_count'] = 0;
                if (empty($money_sportpit_count[$i]['money_sportpit_count'])) $money_sportpit_count[$i]['money_sportpit_count'] = 0;

                $sum_count_other[$i]['sum_count_other'] = $sum_count[$i]['sum_count'] - $sum_prod_count[$i]['sum_prod_count'] - $sum_home_count[$i]['sum_home_count'] - $money_sportpit_count[$i]['money_sportpit_count'];
            }

            $arr2 = arr_merge($arr2, $sum_count_other);
            //print_r($sum_home_count);
            //print_r($arr2);
            $arr3['response'] = $arr2;
            $arr3 = json_encode($arr3);

            return $arr3;

        } else {
            return $this->render('sales-report');
        }

    }

    public function actionSalesssReport()
    {

        if (isset($_POST['date1']) && isset($_POST['date2'])) {
            $db = Yii::$app->getDb();

            // Получение введенных дат
            $begin = $_POST['date1'];
            $end = $_POST['date2'];

// Для теста
            /*$begin = '2016-08-23';
            $end   = '2016-08-25';*/

            $date1 = new DateTime($begin);
            $date2 = new DateTime($end);

            // Преобразование дат
            //$date1 = new DateTime($_POST['date1']);
            //$date2 = new DateTime($_POST['date2']);

            // Разница между датами
            //$interval = date_diff($date1, $date2);

            // Получение общей суммы
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS `date_sum`, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY `date_sum` ORDER BY `orders`.`date` DESC";
            $sum = $db->createCommand($sql)->queryAll();

            // Получение кол-во заказов для общей суммы
            $sql = "SELECT COUNT(DISTINCT `orders`.`id`) AS `sum_count` FROM `orders` LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` = `orders_groups`.`id` WHERE `orders`.`type` = 1 AND `orders`.`status` = 1 AND `orders_items`.`status` = 1 AND `orders_groups`.`status` = 1 AND `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_count = $db->createCommand($sql)->queryAll();

            $sql = "SELECT COUNT(DISTINCT `orders`.`id`) AS `sum_prod_count`  FROM `orders` LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` =  `orders_groups`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`WHERE `orders`.`type` = 1 AND `orders`.`status` = 1
AND `orders_items`.`status` = 1 AND `orders_groups`.`status` = 1 AND `goods`.`type_id` = 1003 AND `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod_count = $db->createCommand($sql)->queryAll();

            // Получение суммы продуктов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY) AND `goods`.`type_id` = 1003 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod = $db->createCommand($sql)->queryAll();

            $date2 = $date2->modify('+1 day');

            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($date1, $interval, $date2);

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products' => 0);
            }

            $merged_normalized =
                array_column($sum_prod, 'money_products', 'date1') +
                array_column($arr, 'money_products', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod = array_reverse($result);
            $arr3['test'] = $result;
            unset($result);

            // Получение суммы продуктов (руб.) для сотрудников
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products_staff` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY )AND `goods`.`type_id` = 1003 AND `users`.`staff` = 1 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod_staff = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products_staff' => 0);
            }

            $merged_normalized =
                array_column($sum_prod_staff, 'money_products_staff', 'date1') +
                array_column($arr, 'money_products_staff', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products_staff' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod_staff = array_reverse($result);
            unset($result);

            // Получение суммы продуктов (руб.) для клиентов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money_products_client` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY )AND `goods`.`type_id` = 1003 AND `users`.`staff` IS NULL AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sum_prod_client = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'money_products_client' => 0);
            }

            $merged_normalized =
                array_column($sum_prod_client, 'money_products_client', 'date1') +
                array_column($arr, 'money_products_client', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'money_products_client' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sum_prod_client = array_reverse($result);
            unset($result);

            // Получение суммы продуктов (бонусов) для сотрудников
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`bonus`)*(`orders_items`.`count`)) AS `bonus_products_staff` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY )AND `goods`.`type_id` = 1003 AND `users`.`staff` = 1 AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sp_bonus_staff = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'bonus_products_staff' => 0);
            }

            $merged_normalized =
                array_column($sp_bonus_staff, 'bonus_products_staff', 'date1') +
                array_column($arr, 'bonus_products_staff', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'bonus_products_staff' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sp_bonus_staff = array_reverse($result);
            unset($result);

            // Получение суммы продуктов (бонусов) для клиенов
            $sql = "SELECT DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') AS date1, SUM((`orders_items`.`bonus`)*(`orders_items`.`count`)) AS `bonus_products_client` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id` LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `orders`.`user_id` = `users`.`id` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id` WHERE `orders`.`date` >= '" . $begin . "' AND `orders`.`date` <= ('" . $end . "' + INTERVAL 1 DAY )AND `goods`.`type_id` = 1003 AND `users`.`staff` IS NULL AND `orders_items`.`status` = 1 AND `orders`.`type` = 1 AND `orders_groups`.`type_id` > 0 AND `orders`.`status` = 1 GROUP BY DATE_FORMAT(`orders`.`date`, '%d.%m.%Y') ORDER BY `orders`.`date` DESC";
            $sp_bonus_client = $db->createCommand($sql)->queryAll();

            $arr = array();
            foreach ($daterange as $date) {
                $arr[] = array('date1' => $date->format('d.m.Y'), 'bonus_products_client' => 0);
            }

            $merged_normalized =
                array_column($sp_bonus_client, 'bonus_products_client', 'date1') +
                array_column($arr, 'bonus_products_client', 'date1');
            $result = [];
            foreach ($merged_normalized as $date => $money) {
                $result[] = [
                    'date1' => $date,
                    'bonus_products_client' => $money
                ];
            }
            unset($merged_normalized, $date, $money);

            // Сортировка
            usort($result, function ($arr1, $arr2) {
                return strtotime($arr1['date1']) > strtotime($arr2['date1']);
            });

            $sp_bonus_client = array_reverse($result);
            unset($result);


            // Формирование финального массива
            function arr_merge()
            {
                $output = array();
                foreach (func_get_args() as $array) {
                    foreach ($array as $key => $value) {
                        $output[$key] = isset($output[$key]) ?
                            array_merge($output[$key], $value) : $value;
                    }
                }
                return $output;
            }

            $arr = arr_merge($sum, $sum_prod);
            $arr = array_map(
                function ($x) {
                    $x['money_other'] = $x['money'] - $x['money_products'];
                    return $x;
                },
                $arr
            );

            for ($i = count($sum_prod_staff) - 1; $i >= 0; $i--) {
                $sum_prod_staff[$i]['money_products_staff'] -= $sp_bonus_staff[$i]['bonus_products_staff'];
            }

            $arr2 = arr_merge($arr, $sum_prod_staff);
            $arr2 = arr_merge($arr2, $sp_bonus_staff);

            for ($i = count($sum_prod_client) - 1; $i >= 0; $i--) {
                $sum_prod_client[$i]['money_products_client'] -= $sp_bonus_client[$i]['bonus_products_client'];
            }

            $arr2 = arr_merge($arr2, $sum_prod_client);
            $arr2 = arr_merge($arr2, $sp_bonus_client);
            $arr2 = arr_merge($arr2, $sum_count);
            $arr2 = arr_merge($arr2, $money_sportpit_count);
            $arr2 = arr_merge($arr2, $sum_prod_count);

            //$sum_count_other = [];
            for ($i = count($sum_count) - 1; $i >= 0; $i--) {
                $sum_count_other[$i]['sum_count_other'] = $sum_count[$i]['sum_count'] - $sum_prod_count[$i]['sum_prod_count'] - $money_sportpit_count[$i]['money_sportpit_count'];
            }
            //VarDumper::dump($sum_count_other);
            $arr2 = arr_merge($arr2, $sum_count_other);

            $arr3['response'] = $arr2;
            $arr3 = json_encode($arr3);

            return $arr3;

        } else {
            return $this->render('sales-report');
        }

        //$array = User::find()->where('staff is not null')->all();

        // Передача во View


    }

    public function actionOrdersZlrdn()
    {
        $orderItem = OrdersGroups::findOne(1000052157);
        (new Orders())->preparationOrdersToShipped($orderItem);

    }

    public function actionOrders_search()
    {
        $db = Yii::$app->getDb();
        $data = '';
        // Поиск магазина;
        if (!empty($_POST['search_name'])) {
            if ($_POST['search_name'] == 'shops') {//&& !empty($_SESSION['filter']['shops'])) {
                // Загрузка магазина;
                $sql = "SELECT `id`, `name` FROM `shops` WHERE "
                    . 'name LIKE \'%' . $_POST['search_value'] . '%\' '
                    . 'OR name_full LIKE \'% ' . $_POST['search_value'] . '%\''

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
            if ($_POST['search_name'] == 'users') {//&& !empty($_SESSION['filter']['users'])) {
                // Загрузка покупателя;
                $sql = "SELECT `id`, `name` FROM `users` WHERE "
                    . 'name LIKE \'%' . $_POST['search_value'] . '%\' '
                    . 'OR phone LIKE \'% ' . $_POST['search_value'] . '%\' '

//                    . ($_POST['search_value'] ? "(`name` LIKE '%"
//                        . $_POST['search_value'] . "%' OR `phone` LIKE '%"
//                        . $_POST['search_value'] . "%') AND " : "")
//                    . "`id` NOT IN ('" . implode(
//                        "', '", array_keys($_SESSION['filter']['users'])
                    . " ORDER BY `name` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }
            // Поиск промо-кода;
            if ($_POST['search_name'] == 'codes') {//&& !empty($_SESSION['filter']['codes'])) {
                // Загрузка промо-кода;
                $sql = "SELECT `codes`.`id`, `codes`.`code` AS `name` FROM `codes` LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id` WHERE "
                    . "(`codes`.`code` LIKE '%" . $_POST['search_value'] . "%' OR `users`.`phone` LIKE '%" . $_POST['search_value'] . "%' "
                    . "OR `users`.`name` LIKE '%" . $_POST['search_value'] . "%') "
                    . " ORDER BY `codes`.`code` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }
            // Поиск водителя;
            if ($_POST['search_name'] == 'drivers') {//&& !empty($_SESSION['filter']['drivers'])) {
                // Загрузка водителя;
                $sql = "SELECT `id`, `name` FROM `users` WHERE "
                    . "`name` LIKE '%" . $_POST['search_value'] . "%'" .
                    " OR `phone` LIKE '%" . $_POST['search_value'] . "%'"
                    . " AND `driver` IS NOT NULL ORDER BY `name` ASC";
                $data = $db->createCommand($sql)->queryAll();
            }
        }
        // Обработка данных;
        return json_encode($data);

    }

    public function actionOrders_search_items_add()
    {

        $db = Yii::$app->getDb();
        // Добавление магазина;
        if ($_POST['name'] == 'shops') {
            // Загрузка магазина;
            $sql = "SELECT `name` FROM `shops` WHERE `id` = '" . intval($_POST['item_id']) . "' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Добавление покупателя;
        if ($_POST['name'] == 'users') {
            // Загрузка покупателя;
            $sql = "SELECT `name` FROM `users` WHERE `id` = '" . intval($_POST['item_id']) . "' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Добавление промо-кода;
        if ($_POST['name'] == 'codes') {
            // Загрузка промо-кода;
            $sql = "SELECT `code` FROM `codes` WHERE `id` = '" . intval($_POST['item_id']) . "' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Добавление водителя;
        if ($_POST['name'] == 'drivers') {
            // Загрузка водителя;
            $sql = "SELECT `name` FROM `users` WHERE `id` = '" . intval($_POST['item_id']) . "' LIMIT 1";
            $item_name = $db->createCommand($sql)->queryScalar();
        }
        // Сохранение ID записи в сессии;
        $_SESSION['filter'][$_POST['name']][$_POST['item_id']] = $item_name;
        // Вывод данных;
        return $item_name;

    }

    public function actionOrders_search_items_delete()
    {
        // Удаление ID записи из сессии;
        unset($_SESSION['filter'][$_POST['name']][$_POST['item_id']]);
        return 0;
    }

    public function actionOrders_items_status()
    {

        $db = Yii::$app->getDb();

        // Загрузка данных;
        $sql = "SELECT `orders_items_status`.`date`, `orders_status`.`name` AS `status_name`, `users`.`name` AS `user_name` FROM `orders_items_status` LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items_status`.`status_id` LEFT JOIN `users` ON `users`.`id` = `orders_items_status`.`user_id` WHERE `orders_items_status`.`order_item_id` = '" . intval($_POST['order_item_status']) . "' AND `orders_items_status`.`status` = '1' ORDER BY `orders_items_status`.`date` DESC";
        if ($status = $db->createCommand($sql)->queryAll()) {
            foreach ($status as $key => $value) {
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
        $sql = "UPDATE `orders_items` SET `status_id` = '1008' WHERE `id` = '" . $order_item_id . "' LIMIT 1";
        $db->createCommand($sql)->execute();

        // Добавление статуса в историю;
        $sql = "INSERT INTO `orders_items_status` (`order_item_id`, `status_id`, `user_id`, `date`, `status`) VALUES ('" . $order_item_id . "', '1008', '" . Yii::$app->user->getId() . "', NOW(), '1')";
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
    `orders_items`.`id` = '" . $order_item_id . "' AND
    `orders_items`.`status` = '1' AND
    `orders`.`status` = '1' AND
    `shops_stores`.`status` = 1
LIMIT 1";
        if ($order_item = $db->createCommand($sql)->queryOne()) {
            if ($order_item['shop_id'] == NULL) {
                $order_item['shop_id'] = $order_item['shop_id_old'];
            }
            // Обновление товара в заказе;
            $sql = "UPDATE `orders_items` SET `status` = '0' WHERE `id` = '" . $order_item['id'] . "' LIMIT 1";
            $db->createCommand($sql)->execute();

            // Обновление количества товара (на складе по умолчанию);
            $sql = "UPDATE `goods_counts` SET `count` = `count` + '" . $order_item['count'] . "' WHERE `good_id` = '" . $order_item['good_id'] . "' AND `variation_id` = '" . $order_item['variation_id'] . "' AND `store_id` = (SELECT `id` FROM `shops_stores` WHERE `shop_id` = '" . $order_item['shop_id'] . "' AND `main` = '1' LIMIT 1)";
            //debug('debug', $sql);
            //echo $sql;

            $db->createCommand($sql)->execute();
            //$db->query($sql);
            // Загрузка покупателя;
            $sql = "SELECT * FROM `users` WHERE `id` = '" . $order_item['user_id'] . "' LIMIT 1";
            if ($order_item['user'] = $db->createCommand($sql)->queryOne()) {
                // Рассчет суммы;
                $money = ($order_item['price'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'];
                // Перерасчет средств покупателя;
                $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('" . $order_item['user']['id'] . "', '" . $order_item['order_id'] . "', '5', '" . $money . "', 'Отмена заказа: #" . $order_item['order_id'] . "', NOW(), '1')";
                $db->createCommand($sql)->execute();//$db->query($sql);
                // Проверка типа заказа;
                if ($order_item['type'] == 2) {
                    // Проверка привязки к абонементу ExtremeFitness;
                    if ($order_item['extremefitness']) {
                        // Перевод средств на абонемент ExtremeFitness;
                        $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('" . $order_item['user']['id'] . "', '" . $order_item['order_id'] . "', '2', '-" . $money . "', 'Перевод средств на ExtremeFitness', NOW(), '1')";
                        $db->createCommand($sql)->execute();//$db->query($sql);
                        // Зачисление средств на абонемент ExtremeFitness;
                        //$sql = "CALL pay('13', '".$order_item['extremefitness']."', '".$money."', NULL, NULL, 'Отмена заказа: #".$order_item['order_id']."')";
                        //$db->query($sql);
                    }
                } else {
                    // Обновление баланса покупателя;
                    $sql = "UPDATE `users` SET `money` = `money` + '" . $money . "' WHERE `id` = '" . $order_item['user']['id'] . "' LIMIT 1";
//$db->query($sql);
                    $db->createCommand($sql)->execute();
                }
                // Проверка расхода бонусов;
                if ($order_item['bonus'] > 0) {
                    // Перерасчет бонусов покупателя;
                    $sql = "INSERT INTO `users_bonus` (`user_id`, `type`, `bonus`, `date`, `status`) VALUES ('" . $order_item['user']['id'] . "', '0', '" . ($order_item['bonus'] * $order_item['count']) . "', NOW(), '1')";
                    $db->createCommand($sql)->execute();//$db->query($sql);
                    // Обновление бонусов покупателя;
                    $sql = "UPDATE `users` SET `bonus` = `bonus` + '" . ($order_item['bonus'] * $order_item['count']) . "' WHERE `id` = '" . $order_item['user']['id'] . "' LIMIT 1";
                    $db->createCommand($sql)->execute();//$db->query($sql);
                }
            }
            // Загрузка менеджеров магазина;
            $sql = "SELECT `id`, `email` FROM `users` WHERE ((SELECT `id` FROM `users_roles` WHERE `user_id` = `users`.`id` AND `shop_id` = '" . $order_item['shop_id'] . "' AND `status` = '1' LIMIT 1) OR (`manager` IS NOT NULL AND `level` >= '2')) AND `email` != '' AND `status` = '1' ORDER BY `id` ASC";
            $managers = $db->createCommand($sql)->queryAll();//$db->assoc($sql);
            // Проверка агентского вознаграждения по промо-коду;
            if ($order_item['fee'] > 0) {
                // Загрузка промо-кода;
                $sql = "SELECT `codes`.`id`, `codes`.`user_id`, `users`.`extremefitness` FROM `codes` LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id` WHERE `codes`.`id` = '" . $order_item['code_id'] . "' LIMIT 1";
                if ($code = $db->createCommand($sql)->queryOne()) {
                    // Перерасчет средств агента;
                    $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('" . $code['user_id'] . "', '" . $order_item['order_id'] . "', '6', -'" . ($order_item['fee'] * $order_item['count']) . "', 'Отмена комиссии: #" . $order_item['order_id'] . "', NOW(), '1')";
                    $db->createCommand($sql)->execute();//$db->query($sql);

                    // Обновление баланса агента;
                    $sql = "UPDATE `users` SET `money` = `money` - '" . ($order_item['fee'] * $order_item['count']) . "' WHERE `id` = '" . $code['user_id'] . "' LIMIT 1";
                    //$db->query($sql);
                    $db->createCommand($sql)->execute();
                }
            }
            // Загрузка оставшихся товаров в заказе;
            $sql = "SELECT SUM((`orders_items`.`price` - `orders_items`.`discount` - `orders_items`.`bonus`) * `orders_items`.`count`) AS `money` FROM `orders_items` LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id` LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id` LEFT JOIN `users` ON `users`.`id` = `orders`.`user_id` WHERE `orders`.`id` = '" . $order_item['order_id'] . "' AND `orders_items`.`status` = '1' AND `orders`.`status` = '1' LIMIT 1";
            $order_money = $db->createCommand($sql)->queryScalar();//$db->one($sql);
            // Проверка суммы оставшихся товаров в заказе;
            if ($order_money < 1000) {
                // Загрузка подарочных бонусов за данный заказ;
                $sql = "SELECT SUM(`bonus`) FROM `users_bonus` WHERE `order_id` = '" . $order_item['order_id'] . "' AND `user_id` = '" . $order_item['user_id'] . "' AND `type` = '4' AND `date` > DATE_SUB(NOW(), INTERVAL '30' DAY) AND `status` = '1' LIMIT 1";
                $bonus = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                // Проверка суммы бонусов;
                if ($bonus > 0) {
                    // Перерасчет бонусов покупателя;
                    $sql = "INSERT INTO `users_bonus` (`user_id`, `order_id`, `type`, `bonus`, `date`, `status`) VALUES ('" . $order_item['user_id'] . "', '" . $order_item['order_id'] . "', '4', '-1000', NOW(), '1')";
                    //$db->query($sql);
                    $db->createCommand($sql)->queryScalar();
                    // Обновление бонусов покупателя;
                    $sql = "UPDATE `users` SET `bonus` = `bonus` - '1000' WHERE `id` = '" . $order_item['user_id'] . "' LIMIT 1";
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
        $sql = "SELECT `orders_groups`.`order_id`, `orders_groups`.`id` AS `order_group_id`, `orders_groups`.`delivery_price`, `orders`.`user_id` FROM `orders_groups` LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id` WHERE `orders_groups`.`id` = '" . $order_group_id . "' AND `orders_groups`.`delivery_price` > '0' LIMIT 1";
        if ($order_group = $db->createCommand($sql)->queryOne()) {
            // Обновление стоимости доставки;
            $sql = "UPDATE `orders_groups` SET `delivery_price` = '0' WHERE `id` = '" . $order_group['order_group_id'] . "' LIMIT 1";
            $db->createCommand($sql)->execute();//$db->query($sql);
            // Перерасчет средств покупателя;
            $sql = "INSERT INTO `users_pays` (`user_id`, `order_id`, `type`, `money`, `comments`, `date`, `status`) VALUES ('" . $order_group['user_id'] . "', '" . $order_group['order_id'] . "', '9', '" . $order_group['delivery_price'] . "', 'Отмена доставки: #" . $order_group['order_id'] . "', NOW(), '1')";
            $db->createCommand($sql)->execute();//$db->query($sql);
            // Обновление баланса покупателя;
            $sql = "UPDATE `users` SET `money` = `money` + '" . $order_group['delivery_price'] . "' WHERE `id` = '" . $order_group['user_id'] . "' LIMIT 1";
            $db->createCommand($sql)->execute();//$db->query($sql);            $db->query($sql);
        }
        // Вывод сообщения;
        die('Возврат средств за доставку выполнен');

    }

    public function actionOrders_delivery_double()
    {
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
        $sql = "SELECT `id`, `delivery_surcharge` FROM `orders_groups` WHERE `id` = '" . $order_group_id . "' LIMIT 1";
        if ($order_group = $db->createCommand($sql)->queryOne()) {
            // Проверка доплаты;
            if ($money != $order_group['delivery_surcharge']) {
                // Обновление водителя на доставку;
                $sql = "UPDATE `orders_groups` SET `delivery_surcharge` = '" . $money . "' WHERE `id` = '" . $order_group['id'] . "' LIMIT 1";
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

    public function actionOrders_driver_set()
    {
        $db = Yii::$app->getDb();
        // Обработка данных;
        $order_group_id = intval($_POST['order_group_id']);
        $driver_id = intval($_POST['driver_id']);
        // Загрузка данных о доставке;
        $sql = "SELECT `id` FROM `orders_selects` WHERE `order_group_id` = '" . $order_group_id . "' AND `status` >= '0' LIMIT 1";
        if ($order_select = $db->createCommand($sql)->queryOne()) {
            if ($driver_id) {
                // Обновление водителя на доставку;
                $sql = "UPDATE `orders_selects` SET `user_id` = '" . $driver_id . "' WHERE `id` = '" . $order_select['id'] . "' LIMIT 1";
                $db->createCommand($sql)->execute();
                // Сообщение;
                $message = 'Водитель назначен на доставку';
            } else {
                // Обновление водителя на доставку (снять водителя);
                $sql = "UPDATE `orders_selects` SET `status` = '-1' WHERE `id` = '" . $order_select['id'] . "' LIMIT 1";
                $db->createCommand($sql)->execute();
                //$db->query($sql);
                // Обновление списка товаров;
                $sql = "UPDATE `orders_items` SET `status_id` = '1001' WHERE `order_group_id` = '" . $order_group_id . "' AND `status_id` IN ('1004', '1005') AND `status` = '1'";
                $db->createCommand($sql)->execute();
                //$db->query($sql);
                // Сообщение;
                $message = 'Водитель снят с доставки';
            }
        } else {
            if ($driver_id) {
                // Добавление водителя на доставку;
                $sql = "INSERT INTO `orders_selects` (`order_group_id`, `user_id`, `price`, `date_begin`, `status`) VALUES ('" . $order_group_id . "', '" . $driver_id . "', '300', NOW(), '0')";
                $db->createCommand($sql)->execute();//$db->query($sql);
                // Обновление списка товаров;
                $sql = "UPDATE `orders_items` SET `status_id` = '1004' WHERE `order_group_id` = '" . $order_group_id . "' AND `status_id` = '1001' AND `status` = '1'";
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

    public function actionOrders_delivery()
    {
        //Zloradnij::print_arr($_SESSION['filter']);
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


        if (empty($_SESSION['filter']['users']))
            if (!empty($_POST['users']))
                $_SESSION['filter']['users'] = $_POST['users'];
            else
                $_SESSION['filter']['users'] = [];


        if (empty($_SESSION['filter']['codes']))
            if (!empty($_POST['codes']))
                $_SESSION['filter']['codes'] = $_POST['codes'];
            else
                $_SESSION['filter']['codes'] = [];

        if (empty($_SESSION['filter']['drivers'])) {
            if (!empty($_POST['drivers']))
                $_SESSION['filter']['drivers'] = $_POST['drivers'];
            else
                $_SESSION['filter']['drivers'] = [];
        }


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
            . ($_SESSION['filter']['order_id'] ? "`orders`.`id` = '"
                . $_SESSION['filter']['order_id'] . "' AND " : "")
            . ($_SESSION['filter']['users'] ? "`orders`.`user_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['users'])) . "') AND " : "")
            . "DATE(`orders_groups`.`delivery_date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "' AND "
            . "DATE(`orders_groups`.`delivery_date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end'])
            . "' AND ((`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1003')
             OR (`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1007')
             OR (`orders_groups`.`delivery_id` = '1003' AND `orders_groups`.`type_id` = '1010')
            OR `orders_groups`.`delivery_id` = '1006' OR `orders_groups`.`delivery_id` = '1007') AND `orders_groups`.`status` = '1' AND (SELECT `id` FROM `orders_items` WHERE `order_group_id` = `orders_groups`.`id` AND `status_id` IN ('1001', '1004', '1005', '1006', '1007', '1008') AND `status` = '1' LIMIT 1) IS NOT NULL AND (SELECT `id` FROM `orders_items` WHERE `order_group_id` = `orders_groups`.`id` AND `status_id` IS NULL AND `status` = '1' LIMIT 1) IS NULL AND `orders`.`type` = '1' AND `orders`.`status` = '1' ORDER BY `orders_groups`.`delivery_date` DESC";

        if ($orders = $db->createCommand($sql)->queryAll()) {
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
     WHERE `order_group_id` = '" . $item['order_group_id'] . "'"
                    . ($_SESSION['filter']['drivers'] ? " AND `users`.`id` IN ('" . implode("', '", array_keys($_SESSION['filter']['drivers'])) . "')" : "")
                    . " AND `orders_selects`.`status` >= '0' LIMIT 1";


                if ($orders[$i]['select'] = $db->createCommand($sql)->queryOne()) {
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
                    $orders[$i]['select']['price'] = number_format($orders[$i]['select']['price'], 0, '.', ' ');// maks, убрал это вывражение, 15,06,2016 //  - $item['delivery_surcharge']
                    // Рассчет итоговых данных (оплаты покупателей);
                    $info_price += $item['delivery_price'];
                } else {
                    // Рассчет текущей цены за доставку;
                    $api = new Api();
                    $orders[$i]['price'] = $api->delivery_price($orders[$i]['order_group_id']);
                    // Обработка статуса;
                    $orders[$i]['status_name'] = 'ожидает курьера';
                    // Проверка фильтра по водителям;
                    if ($_SESSION['filter']['drivers']) {
                        // Удаление записи;
                        unset($orders[$i]);
                    } else {
                        // Рассчет итоговых данных (оплаты покупателей);
                        $info_price += $item['delivery_price'];
                    }
                }
            }
        }
        // Загрузка водителей;
        $sql = "SELECT `id`, `name`, `phone` FROM `users` WHERE (`driver` IS NOT NULL OR (SELECT `id` FROM `orders_selects` WHERE `user_id` = `users`.`id` AND `status` >= '0' LIMIT 1) IS NOT NULL) AND `status` = '1' ORDER BY `name` ASC";
        $drivers = $db->createCommand($sql)->queryAll();

        // Загрузка суммы оплат таксистам;
        $sql = "SELECT
ABS(SUM(`users_pays`.`money`))
FROM `users_pays`
LEFT JOIN `users` ON `users`.`id` = `users_pays`.`user_id`
WHERE `users`.`driver` IS NOT NULL
 AND `users_pays`.`type` = '22'
 AND DATE(`users_pays`.`date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "'
 AND DATE(`users_pays`.`date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end']) . "'"
            . ($_SESSION['filter']['drivers'] ? " AND `users`.`id` IN ('" . implode("', '", array_keys($_SESSION['filter']['drivers'])) . "')" : "")
            . " AND `users_pays`.`status` = '1' LIMIT 1";

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

    public function actionTestSma()
    {
        $api = new Api();
        $api->sms('9237271543', 'Новый заказ: #555');

    }

    public function actionOrders_items()
    {
        //print_R($_POST);
        //print_r($_SESSION);
        //die();

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
        $_SESSION['filter']['delivery_store_id'] = $_POST['delivery_store_id'] ? $_POST['delivery_store_id'] : '';

        //$_SESSION['filter']['our_shops'] = $_POST['delivery_store_id'] ? $_POST['delivery_store_id'] : '';
        $_SESSION['filter']['store_id'] = $_POST['store_id'] ? $_POST['store_id'] : '';


//        Zloradnij::print_arr($_SESSION['filter']['date_begin']);
//        Zloradnij::print_arr($_SESSION['filter']['date_end']);
//
//        Zloradnij::print_arr(date("Y-m-d", $_SESSION['filter']['date_begin']));
//        Zloradnij::print_arr(date("Y-m-d", $_SESSION['filter']['date_end']));
//
//        die();

        //не вызывает Notice
        if (array_key_exists('group', $_POST)) $_SESSION['filter']['group'] = 1; else $_SESSION['filter']['group'] = 0;
        if (array_key_exists('our_shops', $_POST)) $_SESSION['filter']['our_shops'] = 1; else $_SESSION['filter']['our_shops'] = 0;
        if (array_key_exists('not_our_shops', $_POST)) $_SESSION['filter']['not_our_shops'] = 1; else $_SESSION['filter']['not_our_shops'] = 0;
        if (array_key_exists('not_free_delivery', $_POST)) $_SESSION['filter']['not_free_delivery'] = 1; else $_SESSION['filter']['not_free_delivery'] = 0;
        if (array_key_exists('good_id', $_POST)) $_SESSION['filter']['good_id'] = $_POST['good_id']; else $_SESSION['filter']['good_id'] = 0;
        if (array_key_exists('no_promo', $_POST)) $_SESSION['filter']['no_promo'] = $_POST['no_promo']; else $_SESSION['filter']['no_promo'] = 0;
        if (array_key_exists('basket_sort', $_POST)) $_SESSION['filter']['basket_sort'] = $_POST['basket_sort']; else $_SESSION['filter']['basket_sort'] = 0;
        if($_SESSION['filter']['group']==1){
            if (array_key_exists('category_all', $_POST)) $_SESSION['filter']['category_all'] = $_POST['category_all']; else $_SESSION['filter']['category_all'] = 0;
        }
        $_SESSION['filter']['user_type'] = $_POST['user_type'] ? $_POST['user_type'] : 0;
        $_SESSION['filter']['type_id'] = $_POST['type_id'] ? $_POST['type_id'] : '';


        if (empty($_SESSION['filter']['users']))
            if (!empty($_POST['users']))
                $_SESSION['filter']['users'] = $_POST['users'];
            else
                $_SESSION['filter']['users'] = [];


        if (empty($_SESSION['filter']['codes']))
            if (!empty($_POST['codes']))
                $_SESSION['filter']['codes'] = $_POST['codes'];
            else
                $_SESSION['filter']['codes'] = [];

        if (empty($_SESSION['filter']['shops'])) {
            if (!empty($_POST['shops']))
                $_SESSION['filter']['shops'] = $_POST['shops'];
            else
                $_SESSION['filter']['shops'] = [];
        }

        // Значение статусов поумолчанию;
        $_SESSION['filter']['status'] = 1;
        $_SESSION['filter']['status_where'] = "";

        // Обработка статусов;
        if ($_SESSION['filter']['status_id'] >= 1001) {
            $_SESSION['filter']['status_where'] = " AND `orders_items`.`status_id` = '" . $_SESSION['filter']['status_id'] . "'";
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
        $info_food = 0;
        $info_sportpit = 0;
        $info_home_goods = 0;
        $info_food_weight = 1;

        $info_comissions_goods_all = 0;
        $info_comissions_goods_all_count =0;

        $info_real_comissions_food =0;
        $info_real_comissions_home =0;
        $info_real_comissions_ef =0;
        $info_real_comissions_sportpit =0;

        $info_real_comissions_food_comission =0;
        $info_real_comissions_home_comission =0;
        $info_real_comissions_ef_comission =0;
        $info_real_comissions_sportpit_comission =0;


        //Zloradnij::print_arr($_SESSION['filter']);

        // Загрузка заказов;
        $sql = "SELECT
                    `orders`.`id` AS `order_id`,
                    `orders`.`date`,
                    `orders`.`negative_review`,
                    '' AS `groups`,
                    '' AS `money`,
                    `orders`.`code_id`,
                    `orders`.`user_id`,
                    `orders`.`comments`,
                    `orders`.`comments_call_center`

                    FROM `orders` 
                    LEFT JOIN `codes` ON `codes`.`id` = `orders`.`code_id`
                    LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id`
                    LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                    LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                    WHERE "
            . ($_SESSION['filter']['order_id'] ? "`orders`.`id` = '" . $_SESSION['filter']['order_id'] . "' AND " : "")
            . (($_SESSION['filter']['date'] == '1') ? "DATE(`orders`.`date`) >= '" . date("Y-m-d", ($_SESSION['filter']['date_begin']))
                . "' AND " . "DATE(`orders`.`date`) <= '" . date("Y-m-d", ($_SESSION['filter']['date_end']))
                . "' AND " : "")
            . ($_SESSION['filter']['users'] ? "`orders`.`user_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['users'])) . "') AND " : "")
            . ($_SESSION['filter']['codes'] ? "`orders`.`code_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['codes'])) . "') AND " : "")
            . (($_SESSION['filter']['type'] == '1' and $_SESSION['filter']['store_id']) ? "`users`.`store_id` = '" . $_SESSION['filter']['store_id'] . "' AND " . " (`orders_groups`.`type_id` >0  ) AND " : " ")
            . (($_SESSION['filter']['user_type'] == 1) ? "(SELECT IFNULL(`users`.`staff`, 0) FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) =0 AND " : "")
            . (($_SESSION['filter']['user_type'] == 2) ? "(SELECT IFNULL(`users`.`staff`, 0) FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) >0 AND " : "")
            . "`orders`.`type` = '" . $_SESSION['filter']['type'] . "' "
            . " AND (`orders`.`status` = 1 )"
            . " AND (`orders_items`.`status` = " . $_SESSION['filter']['status'] . " )"
            . (($_SESSION['filter']['delivery_store_id'] != NULL) ? " AND (`orders_items`.`store_id` = " . $_SESSION['filter']['delivery_store_id'] . " )" : "")
            . (($_SESSION['filter']['our_shops'] == 1) ? " AND (`orders_items`.`store_id` IN (10000191,10000001,10000002,10000003,10000004,10000005,10000006,10000007,10000108) )" : "")
            . (($_SESSION['filter']['our_shops'] == 1) ? " AND (`orders_items`.`store_id` IN (10000191,10000001,10000002,10000003,10000004,10000005,10000006,10000007,10000108) )" : "")
            . (($_SESSION['filter']['not_our_shops'] == 1) ? " AND (`orders_items`.`store_id` NOT IN (10000191,10000001,10000002,10000003,10000004,10000005,10000006,10000007,10000108) )" : "")
            . (($_SESSION['filter']['not_free_delivery'] == 1) ? " AND orders_groups.delivery_price > 0 " : "")
            . (($_SESSION['filter']['no_promo'] == 1) ? " AND (`orders`.`code_id` IS NULL)" : "")
            . (!empty($_SESSION['filter']['good_id']) ? " AND orders_items.good_id in (".$_SESSION['filter']['good_id'].") " : "")
//        ." AND (`orders_groups`.`status` = ".$_SESSION['filter']['status']." )"
//        ." AND (`orders`.`status` = ".$_SESSION['filter']['status'].")"
            //." AND (`orders_items`.`status` = 1 )"
//        ." AND (`orders_groups`.`status` = 1 )"
            //." AND (`orders`.`status` = 0)"
            . " GROUP BY `order_id` ORDER BY `orders`.`date` DESC";

        //Zloradnij::print_arr($_SESSION['filter']['delivery_store_id']);
        ///Zloradnij::print_arr($sql);die();

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

        if ($orders = $db->createCommand($sql)->queryAll()) {//$db->all($sql)) {
            foreach ($orders as $order_key => $order) {
                // Обработка даты;
                $orders[$order_key]['date'] = date("d.m.Y, H:i", strtotime($order['date']));
                // Загрузка покупателя;
                $sql = "SELECT `id`, `name`, `phone`, `email`, `staff`, `typeof` FROM `users` WHERE `id` = '" . $order['user_id'] . "' LIMIT 1";
                $orders[$order_key]['user'] = $db->createCommand($sql)->queryOne();//$db->row($sql);
                // Проверка промо-кода;
                if ($orders[$order_key]['code_id']) {
                    // Загрузка промо-кода;
                    $sql = "SELECT `codes`.`code`, `users`.`name` FROM `codes` LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id` WHERE `codes`.`id` = '" . $order['code_id'] . "' LIMIT 1";
                    $orders[$order_key]['code'] = $db->createCommand($sql)->queryOne();//$db->row($sql);
                }
                // Загрузка групп заказов;
                $sql = "SELECT `orders_groups`.`id` AS `order_group_id`, '' AS `goods`, `deliveries`.`name` AS `delivery_name`, `orders_groups`.`delivery_price`, `orders_groups`.`delivery_surcharge`, '0' AS `delivery_pay`, `orders_groups`.`delivery_date`, '' AS `delivery_address`, `orders_groups`.`address_id`, `orders_groups`.`store_id` FROM `orders_groups` LEFT JOIN `deliveries` ON `deliveries`.`id` = `orders_groups`.`delivery_id` WHERE `order_id` = '" . $order['order_id'] . "' AND " . ($_SESSION['filter']['delivery_id'] ? "`orders_groups`.`delivery_id` = '" . $_SESSION['filter']['delivery_id'] . "' AND " : "") . (($_SESSION['filter']['date'] == '2') ? "DATE(`orders_groups`.`delivery_date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "' AND DATE(`orders_groups`.`delivery_date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end']) . "' AND " : "") . ((($_SESSION['filter']['type'] == '2' OR $_SESSION['filter']['delivery_id'] == 1003) and $_SESSION['filter']['store_id']) ? "`orders_groups`.`store_id` = '" . $_SESSION['filter']['store_id'] . "' AND " : "") . "`orders_groups`.`status` = '1' ORDER BY `orders_groups`.`id` ASC";

                //    $deb['sql1'] = $sql;

                if ($orders[$order_key]['groups'] = $db->createCommand($sql)->queryAll()) {//$db->all($sql)) {
                    foreach ($orders[$order_key]['groups'] as $order_group_key => $order_group) {
                        // Обработка даты;
                        $orders[$order_key]['groups'][$order_group_key]['delivery_date'] = date("d.m.Y, H:i", strtotime($order_group['delivery_date']));
                        // Проверка адреса;
                        if ($order_group['address_id']) {
                            // Загрузка адреса;
                            $sql = "SELECT CONCAT_WS(', ',  `district`, `street`, `house`, `room`, `phone`) AS `address` FROM `address` WHERE `id` = '" . $order_group['address_id'] . "' LIMIT 1";
                            $orders[$order_key]['groups'][$order_group_key]['delivery_address'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
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
WHERE  `order_group_id` = '" . $order_group['order_group_id'] . "'"

                            . ($_SESSION['filter']['type_id'] ? "  AND `goods`.`type_id` = '" . $_SESSION['filter']['type_id'] . "'" : "")
                            . ($_SESSION['filter']['shops'] ? "  AND `shop_group_related`.`shop_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "')" : "")
                            . $_SESSION['filter']['status_where'] . "  AND `orders_items`.`status` = '0' LIMIT 1";

                        //  $deb['sql2'] = $sql;

                        $info_goods_cancel += $db->createCommand($sql)->queryScalar();//$db->one($sql);
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
                            `orders_items`.`count_save`,
                            '0' AS `money`,
                            `orders_items`.`comission`,
                            '0' AS `comission_percent`,
                            `orders_items`.`fee`,
                            `orders_items`.`bonusBack`,
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
                        WHERE `order_group_id` = '" . $order_group['order_group_id'] . "'"
                            . ($_SESSION['filter']['type_id'] ? " AND `goods`.`type_id` = '" . $_SESSION['filter']['type_id'] . "'" : "")
                            . ($_SESSION['filter']['shops'] ? " AND ((`shops`.`id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "') AND `orders_items`.`store_id` IS NOT NULL) OR (`orders_items`.`store_id` IS NULL AND `goods`.`shop_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "')))" : "")
                            . $_SESSION['filter']['status_where']
                            . " AND `orders_items`.`status` = '" . $_SESSION['filter']['status'] . "'"
                            . (!empty($_SESSION['filter']['good_id']) ? " AND orders_items.good_id in (".$_SESSION['filter']['good_id'].") " : "");
                        if($_SESSION['filter']['basket_sort'] == 0){
                            $sql .=  " ORDER BY `shops`.`name` ASC, `goods`.`name` ASC";
                        }



                        //  $deb['sql3'] = $sql;r


//                    print $sql;die();
                        $weigthAll=0;
                        if ($orders[$order_key]['groups'][$order_group_key]['goods'] = $db->createCommand($sql)->queryAll()) {//$db->all($sql)) {
                            foreach ($orders[$order_key]['groups'][$order_group_key]['goods'] as $order_item_key => $order_item) {
                                if ($order_item['storeId'] == NULL) {
                                    $sqlForShop = 'SELECT shops.*, shops.id AS shop_id FROM shops LEFT JOIN goods ON goods.shop_id = shops.id WHERE goods.id="' . $order_item['good_id'] . '"';
                                    $currentShop = $db->createCommand($sqlForShop)->queryAll();//$db->all($sqlForShop);
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['shop_name'] = $currentShop[0]['name'];
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['shop_id'] = $currentShop[0]['shop_id'];
                                }
                                $weight=0;
                                $good = GoodsVariations::find()->where(['id'=>$order_item['variation_id']])->one();
                                $weight = $good->weight;
                                if(!empty($weight)){
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['weight'] = $weight->value*$order_item['count'];
                                    $weigthAll = $weigthAll + ($weight->value*$order_item['count']);
                                }
                                else{
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['weight'] = 0;
                                }
                                $orders[$order_key]['weight']=$weigthAll;
                                if(!empty($good->comission)){
                                    $info_comissions_goods_all =  $info_comissions_goods_all+$good->comission;
                                    $info_comissions_goods_all_count++;
                                }

                                if($good->product->type_id == 1003){
                                    $info_food += $order_item['price_out'] * $order_item['count'];
                                    $info_food_weight += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['weight'];

                                    $info_real_comissions_food += $order_item['price_out'];
                                    $info_real_comissions_food_comission +=$order_item['comission'];
                                }
                                elseif($good->product->type_id == 1005){
                                    $info_real_comissions_ef += $order_item['price_out'];
                                    $info_real_comissions_ef_comission +=$order_item['comission'];
                                }
                                elseif($good->product->type_id == 1007){
                                    $info_home_goods += $order_item['price_out'] * $order_item['count'];

                                    $info_real_comissions_home += $order_item['price_out'];
                                    $info_real_comissions_home_comission +=$order_item['comission'];
                                }
                                elseif(in_array($good->product->type_id,[1010,1012])){
                                    $info_sportpit += $order_item['price_out'] * $order_item['count'];


                                    $info_real_comissions_sportpit += $order_item['price_out'];
                                    $info_real_comissions_sportpit_comission +=$order_item['comission'];
                                }

                                // Номер фотографии;
                                //                             $image_id = good_image($order_item['good_id']);
                                //$image_id = 0;
                                // Проверка группировки по товарам;
                                if ($_SESSION['filter']['group']) {
                                    // Обработка данных товара;
                                    $flagAddGroup = false;
                                    if(!empty($_SESSION['filter']['category_all'])){
                                        //var_dump('sess = '.$_SESSION['filter']['category_all']);
                                        //var_dump('good = '.Goods::find()->where(['id'=>$order_item['good_id']])->one()->category->id);
                                        $category = Goods::find()->where(['id'=>$order_item['good_id']])->one()->category;
                                        while(!empty($category)){
                                            if($_SESSION['filter']['category_all'] == $category->id){
                                                $flagAddGroup = true;
                                                break;
                                            }
                                            $category = $category->parent;
                                        }
                                    }
                                    else{
                                        $flagAddGroup = true;
                                    }
                                    if($flagAddGroup){
                                        $goods[$order_item['variation_id']]['good_id'] = $order_item['good_id'];
                                        $goods[$order_item['variation_id']]['variation_id'] = $order_item['variation_id'];
                                        $goods[$order_item['variation_id']]['good_name'] = $order_item['good_name'];
                                        $goods[$order_item['variation_id']]['shop_name'] = $order_item['shop_name'];
                                        $goods[$order_item['variation_id']]['comission'] = $order_item['comission'];
                                        // Обработка фотографии;
                                        $goods[$order_item['variation_id']]['good_image'] = Goods::findProductImage($order_item['good_id']); //'/files/goods/'.image_dir($image_id).'/'.$image_id.'_min.jpg';
                                        // Обработка вариантов (теги);
                                        $goods[$order_item['variation_id']]['tags'] = $order_item['tags'] ? $order_item['tags'] : '';
                                        // Расчет стоимости;

                                        if (isset($goods[$order_item['variation_id']])) {

                                            if (empty($goods[$order_item['variation_id']]['price_in'])) $goods[$order_item['variation_id']]['price_in'] = 0;
                                            //$goods[$order_item['variation_id']]['price_in'] += number_format(($order_item['price_out'] - $order_item['comission'] - $order_item['discount']) * $order_item['count'], 2, '.', '');
                                            $goods[$order_item['variation_id']]['price_in'] += number_format(($order_item['price_out'] - $order_item['comission']) * $order_item['count'], 2, '.', '');
                                            if (empty($goods[$order_item['variation_id']]['price_out'])) $goods[$order_item['variation_id']]['price_out'] = 0;
                                            $goods[$order_item['variation_id']]['price_out'] += number_format($order_item['price_out'] * $order_item['count'], 2, '.', '');

                                            if (empty($goods[$order_item['variation_id']]['comission'])) $goods[$order_item['variation_id']]['comission'] = 0;
                                            $goods[$order_item['variation_id']]['comission'] = $order_item['comission'];

                                            if (empty($goods[$order_item['variation_id']]['count'])) $goods[$order_item['variation_id']]['count'] = 0;
                                            $goods[$order_item['variation_id']]['count'] += $order_item['count'];

                                            if (empty($goods[$order_item['variation_id']]['discount'])) $goods[$order_item['variation_id']]['discount'] = 0;
                                            $goods[$order_item['variation_id']]['discount'] += number_format($order_item['discount'] * $order_item['count'], 2, '.', '');
                                            if (empty($goods[$order_item['variation_id']]['bonus'])) $goods[$order_item['variation_id']]['bonus'] = 0;
                                            $goods[$order_item['variation_id']]['bonus'] += number_format($order_item['bonus'] * $order_item['count'], 2, '.', '');
                                            if (empty($goods[$order_item['variation_id']]['money'])) $goods[$order_item['variation_id']]['money'] = 0;
                                            $goods[$order_item['variation_id']]['money'] += number_format(($order_item['price_out'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'], 2, '.', '');

                                        }
                                        else {
                                            if (empty($goods[$order_item['variation_id']]['price_in'])) $goods[$order_item['variation_id']]['price_in'] = 0;
                                            //$goods[$order_item['variation_id']]['price_in'] = number_format(($order_item['price_out'] - $order_item['comission'] - $order_item['discount']) * $order_item['count'], 2, '.', '');
                                            $goods[$order_item['variation_id']]['price_in'] += number_format(($order_item['price_out'] - $order_item['comission']) * $order_item['count'], 2, '.', '');
                                            if (empty($goods[$order_item['variation_id']]['price_out'])) $goods[$order_item['variation_id']]['price_out'] = 0;
                                            $goods[$order_item['variation_id']]['price_out'] = number_format($order_item['price_out'] * $order_item['count'], 2, '.', '');

                                            if (empty($goods[$order_item['variation_id']]['comission'])) $goods[$order_item['variation_id']]['comission'] = 0;
                                            $goods[$order_item['variation_id']]['comission'] = $order_item['comission'];

                                            if (empty($goods[$order_item['variation_id']]['count'])) $goods[$order_item['variation_id']]['count'] = 0;
                                            $goods[$order_item['variation_id']]['count'] = $order_item['count'];

                                            if (empty($goods[$order_item['variation_id']]['discount'])) $goods[$order_item['variation_id']]['discount'] = 0;
                                            $goods[$order_item['variation_id']]['discount'] = number_format($order_item['discount'] * $order_item['count'], 2, '.', '');
                                            if (empty($goods[$order_item['variation_id']]['bonus'])) $goods[$order_item['variation_id']]['bonus'] = 0;
                                            $goods[$order_item['variation_id']]['bonus'] = number_format($order_item['bonus'] * $order_item['count'], 2, '.', '');
                                            if (empty($goods[$order_item['variation_id']]['money'])) $goods[$order_item['variation_id']]['money'] = 0;
                                            $goods[$order_item['variation_id']]['money'] = number_format(($order_item['price_out'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'], 2, '.', '');
                                        }
                                        $goods[$order_item['variation_id']]['comission_percent'] = '';
                                        if (!empty($order_item['variation_id'])) {
                                            $g = GoodsVariations::find()->where('id = ' . $order_item['variation_id'])->one();
                                            if (!empty($g)){
                                                $goods[$order_item['variation_id']]['comission_percent'] = $g->comission;
                                            }
                                        }
                                        // Загрузка остатка товара;
                                        $sql = "SELECT `count` FROM `goods_counts` WHERE `good_id` = '" . $order_item['good_id'] . "' AND `variation_id` = '" . $order_item['variation_id'] . "' AND `status` = '1' LIMIT 1";
                                        $goods[$order_item['variation_id']]['count_all'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                                    }
                                }
                                else {
                                    // Обработка статуса заказа;
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['status_name'] = $order_item['status_name'] ? $order_item['status_name'] : 'не обработан';
                                    // Обработка фотографии;
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['good_image'] = Goods::findProductImage($order_item['good_id']);// '/files/goods/'.image_dir($image_id).'/'.$image_id.'_min.jpg';
                                    // Обработка вариантов (теги);
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['tags'] = $order_item['tags'] ? $order_item['tags'] : '';
                                    // Рассчет входной цены товаров;
                                    //$orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_in'] = number_format($order_item['price_out'] - $order_item['comission'] - $order_item['discount'], 2, '.', '');
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_in'] = number_format($order_item['price_out'] - $order_item['comission'], 2, '.', '');
                                    // Рассчет наценки в рублях;
                                    //$orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission'] = number_format(($order_item['comission'] + $order_item['discount']), 2, '.', '');
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission'] = number_format(($order_item['comission']), 2, '.', '');
                                    //$orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['count_save'] = $order_item['count_save'];

                                    // Рассчет наценки в процентах;
//                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission_percent'] = number_format(($order_item['comission'] + $order_item['discount']) * 100 / ($order_item['price_out'] - $order_item['comission'] - $order_item['discount']), 2, '.', '');

                                    if (!empty($order_item['variation_id'])) {
                                        $g = GoodsVariations::find()->where('id = ' . $order_item['variation_id'])->one();
                                        if (!empty($g)){
                                            $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission_percent'] = $g->comission;
                                        }
                                        else
                                            $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission_percent'] = null;


                                    }
                                    else
                                        $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission_percent'] = '';

                                    // Рассчет потраченных бонусов;
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['bonus'] = number_format(($order_item['bonus'] * $order_item['count']), 2, '.', '');
                                    // Рассчет общей стоимости товаров;
                                    $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['money'] = number_format(($order_item['price_out'] - $order_item['discount'] - $order_item['bonus']) * $order_item['count'], 2, '.', '');

                                    // Рассчет промежуточного итога;
                                    if (empty($orders[$order_key]['price_in'])) $orders[$order_key]['price_in'] = 0;
                                    $orders[$order_key]['price_in'] = $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_in'];
                                    if (empty($orders[$order_key]['comission'])) $orders[$order_key]['comission'] = 0;
                                    $orders[$order_key]['comission'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['comission'];
                                    if (empty($orders[$order_key]['price_out'])) $orders[$order_key]['price_out'] = 0;
                                    $orders[$order_key]['price_out'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['price_out'];
                                    if (empty($orders[$order_key]['discount'])) $orders[$order_key]['discount'] = 0;
                                    $orders[$order_key]['discount'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['discount'];
                                    if (empty($orders[$order_key]['count'])) $orders[$order_key]['count'] = 0;
                                    $orders[$order_key]['count'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['count'];

                                    if (empty($orders[$order_key]['count_save'])) $orders[$order_key]['count_save'] = 0;
                                    $orders[$order_key]['count_save'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['count_save'];

                                    if (empty($orders[$order_key]['bonus'])) $orders[$order_key]['bonus'] = 0;
                                    $orders[$order_key]['bonus'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['bonus'];
                                    if (empty($orders[$order_key]['money'])) $orders[$order_key]['money'] = 0;
                                    $orders[$order_key]['money'] += $orders[$order_key]['groups'][$order_group_key]['goods'][$order_item_key]['money'];

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
                                $sql = "SELECT `price` FROM `orders_selects` WHERE `order_group_id` = '" . $order_group['order_group_id'] . "' AND `status` >= '0' LIMIT 1";
                                $order_group['delivery_pay'] = $db->createCommand($sql)->queryScalar();//$db->one($sql);
                                // Рассчет итоговых данных;
                                $info_goods_price += $order_item['price_out'] * $order_item['count'];
                                $info_goods_discount += $order_item['discount'] * $order_item['count'];
                                $info_goods_bonus += $order_item['bonus'] * $order_item['count'];
////                                    $info_pays_goods += ($order_item['price_out'] - $order_item['discount'] - $order_item['comission']) * $order_item['count'];
                                $info_pays_goods += ($order_item['price_out'] - $order_item['comission']) * $order_item['count'];

                                $info_pays_fee += ($order_item['fee']+$order_item['bonusBack']) * $order_item['count'];
                                $info_pays_delivery += 0;
                                $info_comissions_goods += ($order_item['comission'] + $order_item['discount']) * $order_item['count'];
                                $info_comissions_minus += ($order_item['discount'] + $order_item['bonus'] + $order_item['fee']) * $order_item['count'];

                            }
                            // Рассчет итоговых данных;
                            $info_goods_delivery += $order_group['delivery_price'];
                            $info_pays_delivery += ($order_group['delivery_pay'] + $order_group['delivery_surcharge']);
                            $info_comissions_delivery += ($order_group['delivery_price'] - $order_group['delivery_pay'] - $order_group['delivery_surcharge']);
                        }
                        else {
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
        $info['sales']=[
            'food'          => number_format($info_food, 2, '.', ' '),
            'sportpit'      => number_format($info_sportpit, 2, '.', ' '),
            'home_goods'    => number_format($info_home_goods, 2, '.', ' '),
            'comission'     => $info_comissions_goods_all,
            'count_goods'   => $info_comissions_goods_all_count,
            'good_weight'   => $info_food_weight,
            'kg_cost'       => $info_food_weight==1?'non':round(($info_food*1000)/$info_food_weight,2),
            'real_food'     => $info_real_comissions_food_comission==0?'non':round(($info_real_comissions_food_comission/$info_real_comissions_food)*100,2),
            'real_ef'       => $info_real_comissions_ef_comission==0?'non':round(($info_real_comissions_ef_comission/$info_real_comissions_ef)*100,2),
            'real_home'     => $info_real_comissions_home_comission==0?'non':round(($info_real_comissions_home_comission/$info_real_comissions_home)*100,2),
            'real_sportpit'  => $info_real_comissions_sportpit_comission==0?'non':round(($info_real_comissions_sportpit_comission/$info_real_comissions_sportpit)*100,2),
        ];





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
        //print_r($data);
        return json_encode($data);
        // Вывод данных;
        // die($data);
//        return $this->render('/site/error',['name' => '404', 'message' => 'Ooooops!']);
    }

    public function actionXml()
    {

        $db = Yii::$app->getDb();

        // Загрузка товаров;
        $sql = "
SELECT
    `orders_items`.`id` AS `order_item_id`,
    `orders_items`.`store_id` AS `storeId`,
    `shops`.`id` AS `shop_id`,
    `shops`.`name` AS `shop_name`,
    `shops`.`comission_id` AS `type`,
    `shop_group`.`comission_id` AS `groupComission`,
    `goods_variations`.`id` AS `good_code`,
    `goods`.`name`,
    `goods`.`id` AS `goodId`,
    `goods_variations`.`full_name`,
    get_tags(`orders_items`.`variation_id`) AS `tags`,
    ROUND(`orders_items`.`price` - `orders_items`.`discount` - `orders_items`.`comission`, 2) AS `price_in`,
    ROUND(`orders_items`.`price` - `orders_items`.`discount` - `orders_items`.`bonus`, 2) AS `price_out`,
    ROUND(`orders_items`.`comission` / `orders_items`.`price` * 100, 2) AS `comission`,
    (`orders_items`.`discount` + `orders_items`.`bonus`) AS `discount` ,
    `orders_items`.`count`
FROM `orders_items`
LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id`
LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id`
LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`
LEFT JOIN `goods_variations` ON `goods_variations`.`id` = `orders_items`.`variation_id`
LEFT JOIN `shops_stores` ON `shops_stores`.`id` = `orders_items`.`store_id`
LEFT JOIN `shops` ON `shops`.`id` = `shops_stores`.`shop_id`
LEFT JOIN `shop_group_related` ON `shop_group_related`.`shop_id` = `shops`.`id`
LEFT JOIN `shop_group` ON `shop_group`.`id` = `shop_group_related`.`shop_group_id`
LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id`
LEFT JOIN `deliveries` ON `deliveries`.`id` = `orders_groups`.`delivery_id`
WHERE
    `orders`.`type` = '" . $_SESSION['filter']['type'] . "'" .
            ($_SESSION['filter']['delivery_id'] ? " AND `orders_groups`.`delivery_id` = '" . $_SESSION['filter']['delivery_id'] . "'" : "") .
            ($_SESSION['filter']['order_id'] ? " AND `orders`.`id` = '" . $_SESSION['filter']['order_id'] . "'" : "") .
            (($_SESSION['filter']['date'] == '1') ? " AND DATE(`orders`.`date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "' AND DATE(`orders`.`date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end']) . "'" : "") .
            (($_SESSION['filter']['date'] == '2') ? " AND DATE(`orders_groups`.`delivery_date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "' AND " . "DATE(`orders_groups`.`delivery_date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end']) . "'" : "") .
            ($_SESSION['filter']['type_id'] ? " AND `goods`.`type_id` = '" . $_SESSION['filter']['type_id'] . "'" : "") .
            ($_SESSION['filter']['users'] ? " AND `orders`.`user_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['users'])) . "')" : "") .
            ($_SESSION['filter']['codes'] ? " AND `orders`.`code_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['codes'])) . "')" : "") .

            (
            $_SESSION['filter']['shops'] ?
                " AND ((`shops`.`id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "') AND `orders_items`.`store_id` IS NOT NULL) OR (`orders_items`.`store_id` IS NULL AND `goods`.`shop_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "')))"
                : ""
            ) .
            (($_SESSION['filter']['type'] == '1' and $_SESSION['filter']['store_id']) ? " AND `users`.`store_id` = '" . $_SESSION['filter']['store_id'] . "'" : "") .
            (($_SESSION['filter']['type'] == '2' and $_SESSION['filter']['store_id']) ? " AND `orders_groups`.`store_id` = '" . $_SESSION['filter']['store_id'] . "'" : "") .
            (($_SESSION['filter']['user_type'] == 1) ? "AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL " : "") .
            (($_SESSION['filter']['user_type'] == 2) ? "AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NOT NULL " : "") .
            $_SESSION['filter']['status_where'] . " AND
    `orders`.`status` = '1' AND
    `orders_groups`.`status` = '1' AND
    `orders_items`.`status` = '" . $_SESSION['filter']['status'] . "'
    ORDER BY `shops`.`name` ASC";

        if ($orders = $db->createCommand($sql)->queryAll()) {
            foreach ($orders as $i => $order) {
                if ($order['storeId'] == NULL || $order['storeId'] == '' || $order['storeId'] == false) {
                    $sqlForShop = 'SELECT shops.* FROM shops LEFT JOIN goods ON goods.shop_id = shops.id WHERE goods.id="' . $order['goodId'] . '"';
                    $currentShop = $db->createCommand($sqlForShop)->queryOne();
                    $order['shop_name'] = $currentShop[0]['name'];
                    $order['shop_id'] = $currentShop[0]['id'];
                    $order['type'] = $currentShop[0]['comission_id'];
                }
                // Обработка типа комиссии;
                $typeOrig = $order['type'];
                if ($order['type'] == NULL) {
                    $order['type'] = $order['groupComission'];
                }
                if ($order['type'] == 1001) {
                    $order['type'] = 1;
                    // Рассчет входной цены товара;
                    $order['price_in'] = $order['price_out'];
                }
                if ($order['type'] == 1002) {
                    $order['type'] = 2;
                    // Рассчет наценки;
                    $order['comission'] = '100.00';
                }
                // Добавление данных о магазине в массив;
                if (!isset($data[$order['shop_id']])) {
                    $data[$order['shop_id']] = array(
                        'type' => $order['type'],
                        'code' => 'S' . $order['shop_id'],
                        'name' => $order['shop_name'],
                        'items' => array()
                    );
                }
                // Добавление данных о товаре в массив;
                $data[$order['shop_id']]['items'][$order['order_item_id']] = array('good_code' => 'V' . $order['good_code'], 'good_name' => ($order['full_name'] ? $order['full_name'] : $order['name'] . ($order['tags'] ? ' / ' . $order['tags'] : '')), 'price_in' => $order['price_in'], 'price_out' => $order['price_out'], 'count' => $order['count'], 'discount' => $order['discount'], 'comission' => $order['comission']);
            }
            // Передача данных в шаблон экспорта;
            $shopsName = Shops::find()->select('name_full')->where(['id' => array_keys($_SESSION['filter']['shops'])])->asArray()->all();
            //implode(", ", array_column($shopsName, 'name_full'))
            $_SESSION['exports'] = array('type' => 'orders', 'name' => 'Продажа продукции', 'data' => $data, 'comments' => (!empty($shopsName) ? implode(", ", array_column($shopsName, 'name_full')) . ', ' : '') . 'с ' . date("d.m.Y", $_SESSION['filter']['date_begin']) . ' по ' . date("d.m.Y", $_SESSION['filter']['date_end']));
            $result = array('type' => 'orders', 'name' => 'Продажа продукции', 'data' => $data, 'comments' => (!empty($shopsName) ? implode(", ", array_column($shopsName, 'name_full')) . ', ' : '') . 'с ' . date("d.m.Y", $_SESSION['filter']['date_begin']) . ' по ' . date("d.m.Y", $_SESSION['filter']['date_end']));
        }
        //Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        Yii::$app->response->headers->add('Cache-Control', 'max-age=0');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment;filename="exports.xml"');
        return $this->renderPartial('exports_xml', ['model' => $result]);
    }

    public function actionXls()
    {
        $db = Yii::$app->getDb();
        // Загрузка товаров;
        $sql = "
SELECT
    `orders`.`id` AS `order_id`,
    `orders`.`date`,
    `goods`.`name` AS `good_name`,
    `goods`.`id` AS `goodId`,
    `shops`.`id` AS `shop_id`,
    `shops`.`name` AS `shop_name`,
    get_tags(`orders_items`.`variation_id`) AS `tags`,
    '0' AS `price_in`,
    `orders_items`.`store_id` AS `storeId`,
    `orders_items`.`price` AS `price_out`,
    `orders_items`.`discount`,
    `orders_items`.`bonus`,
    `orders_items`.`count`,
    '0' AS `money`,
    `orders_items`.`comission`,
    '0' AS `comission_percent`,
    `orders_items`.`fee`,
    '0' AS `sum`,
    `orders_status`.`name` AS `status_name`,
    `orders_items`.`status`,
    `deliveries`.`name` AS `delivery_name`,
    `orders_groups`.`delivery_date`,
    `orders_groups`.`delivery_price`,
    `orders_groups`.`address_id`,
    `users`.`name`,
    `users`.`phone`
FROM `orders_items`
LEFT JOIN `orders_groups` ON `orders_groups`.`id` = `orders_items`.`order_group_id`
LEFT JOIN `orders` ON `orders`.`id` = `orders_groups`.`order_id`
LEFT JOIN `users` ON `users`.`id` = `orders`.`user_id`
LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`
LEFT JOIN `shops_stores` ON `shops_stores`.`id` = `orders_items`.`store_id`
LEFT JOIN `shops` ON `shops`.`id` = `shops_stores`.`shop_id`
LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id`
LEFT JOIN `deliveries` ON `deliveries`.`id` = `orders_groups`.`delivery_id`
WHERE
    `orders`.`type` = '" . $_SESSION['filter']['type'] . "'" .
            ($_SESSION['filter']['delivery_id'] ? " AND `orders_groups`.`delivery_id` = '" . $_SESSION['filter']['delivery_id'] . "'" : "") .
            ($_SESSION['filter']['order_id'] ? " AND `orders`.`id` = '" . $_SESSION['filter']['order_id'] . "'" : "") .
            (($_SESSION['filter']['date'] == '1') ? " AND DATE(`orders`.`date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "' AND DATE(`orders`.`date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end']) . "'" : "") .
            (($_SESSION['filter']['date'] == '2') ? " AND DATE(`orders_groups`.`delivery_date`) >= '" . date("Y-m-d", $_SESSION['filter']['date_begin']) . "' AND " . "DATE(`orders_groups`.`delivery_date`) <= '" . date("Y-m-d", $_SESSION['filter']['date_end']) . "'" : "") .
            ($_SESSION['filter']['type_id'] ? " AND `goods`.`type_id` = '" . $_SESSION['filter']['type_id'] . "'" : "") .
            ($_SESSION['filter']['users'] ? " AND `orders`.`user_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['users'])) . "')" : "") .
            ($_SESSION['filter']['codes'] ? " AND `orders`.`code_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['codes'])) . "')" : "") .

            (
            $_SESSION['filter']['shops'] ?
                " AND ((`shops`.`id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "') AND `orders_items`.`store_id` IS NOT NULL) OR (`orders_items`.`store_id` IS NULL AND `goods`.`shop_id` IN ('" . implode("', '", array_keys($_SESSION['filter']['shops'])) . "')))"
                : ""
            ) .

//        ($_SESSION['filter']['shops'] ? " AND `shops`.`id` IN ('".implode("', '", array_keys($_SESSION['filter']['shops']))."')" : "").
            (($_SESSION['filter']['type'] == '1' and $_SESSION['filter']['store_id']) ? " AND `users`.`store_id` = '" . $_SESSION['filter']['store_id'] . "'" : "") .
            (($_SESSION['filter']['type'] == '2' and $_SESSION['filter']['store_id']) ? " AND `orders_groups`.`store_id` = '" . $_SESSION['filter']['store_id'] . "'" : "") .
            (($_SESSION['filter']['user_type'] == 1) ? " and (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL  " : "") .
            (($_SESSION['filter']['user_type'] == 2) ? " and (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NOT NULL  " : "") .
            $_SESSION['filter']['status_where'] . " AND
    `orders`.`status` = '1' AND
    `orders_groups`.`status` = '1' AND
    `orders_items`.`status` = '" . $_SESSION['filter']['status'] . "'
    ORDER BY `orders`.`date` DESC";

        if ($orders = $db->createCommand($sql)->queryAll()) {
            foreach ($orders as $i => $order) {
                if ($order['storeId'] == NULL) {
                    $sqlForShop = 'SELECT shops.* FROM shops LEFT JOIN goods ON goods.shop_id = shops.id WHERE goods.id="' . $order['goodId'] . '"';
                    $currentShop = $db->createCommand($sqlForShop)->queryAll();
                    $orders[$i]['shop_name'] = $currentShop[0]['name'];
                    $orders[$i]['shop_id'] = $currentShop[0]['shop_id'];
                }
                // Обработка даты заказа;
                $orders[$i]['date'] = date("d.m.Y, H:i", strtotime($order['date']));
                // Обработка даты доставки;
                $orders[$i]['delivery_date'] = date("d.m.Y, H:i", strtotime($order['delivery_date']));
                // Рассчет входной цены товаров;
                $orders[$i]['price_in'] = number_format(($order['price_out'] - $order['comission']), 2, '.', '');//$order['price_out'] - $order['comission'] - $order['discount'];
                // Рассчет наценки;
                $orders[$i]['comission'] = $order['comission']; //+ $order['discount'];
                // Рассчет суммы;
                $orders[$i]['money'] = ($order['price_out'] - $order['discount'] - $order['bonus']) * $order['count'];
                // Рассчет агентского вознаграждения;
                $orders[$i]['fee'] = $order['fee'] * $order['count'];
                // Рассчет прибыли
                $orders[$i]['sum'] = ($order['comission'] - $order['fee']) * $order['count'];
                // Проверка адреса доставки;
                if ($order['address_id']) {
                    // Загрузка адреса доставки;
                    $sql = "SELECT CONCAT_WS(', ', `street`, `house`, `room`) AS `address` FROM `address` WHERE `id` = '" . $order['address_id'] . "' LIMIT 1";
                    $addressName = $db->createCommand($sql)->queryOne();
                    $orders[$i]['delivery_name'] .= ': ' . $addressName['address'];
                }
            }
            // Передача данных в шаблон экспорта;
            $_SESSION['exports'] = array('type' => 'orders', 'name' => 'Продажа продукции', 'data' => $orders, 'comments' => '');
            $result = array('type' => 'orders', 'name' => 'Продажа продукции', 'data' => $orders, 'comments' => '');
        }
        Yii::$app->response->headers->add('Content-Type', 'application/vnd.ms-excel');
        Yii::$app->response->headers->add('Cache-Control', 'max-age=0');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment;filename="exports.xls"');
        return $this->renderPartial('exports_xls', ['model' => $result]);


    }

    public function actionOrder()
    {
        if (Yii::$app->request->post('exports_xml')) {
            /*Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->add('Content-Type', 'text/xml');
            return $this->renderPartial('exports_xml', ['model'=>'']);*/
            return 'ok';
        } elseif (Yii::$app->request->post('exports_xls')) {
            /*Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->add('Content-Type', 'application/vnd.ms-excel');
            return $this->renderPartial('exports_xls', ['model'=>'']);*/
            return 'ok';
        }


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


            $_SESSION['filter']['type'] = '1';
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


            $_SESSION['filter']['type'] = '1';
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

    public function actionDeliveryPlusMoney()
    {
        $message = '';
        // Доплата за доставку;
        if (isset($_POST['delivery_surcharge'])) {
            $money = intval($_POST['money']);

            $orderGroup = OrdersGroups::findOne(intval($_POST['order_group_id']));
            if (!$orderGroup) {
//                $message = 'group not find';
            } else {
                // Проверка доплаты;
                if ($money != $orderGroup['delivery_surcharge']) {
                    // Обновление водителя на доставку;
                    $orderGroup->delivery_surcharge = $money;
                    $orderGroup->save();

                    // Сообщение;
                    $message = 'Доплата за доставку сохранена';
                } else {
//                    $message = 'money !=';
                }
            }
            // Вывод сообщения;
            return $message;
        }
    }

    public function actionFindOrder()
    {
       // $this->view->registerCssFile('/css/shop-management/reports-find-order.css');
        $filter = new OrderFilter();
//        $filter->setFilterParams(Yii::$app->session['find-orders-filter']);

//        Zloradnij::print_arr(Yii::$app->request->post());

        $filter->setFilterParams(Yii::$app->request->post());
        //$filter->load(Yii::$app->request->post());
        $filter->getOrderListFilter();
        $orders = new ActiveDataProvider([
            'query' => $filter->getOrderList(),
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);

        return $this->render('find-order', [
            'filter' => $filter,
            'orders' => $orders,

            'modelOrder' => $filter->getModelOrder(),
            'modelOrderType' => $filter->getModelOrderType(),
            'modelOrderId' => $filter->getModelOrderId(),
            'modelOrderUser' => $filter->getModelOrderUser(),

            'modelOrderItem' => $filter->getModelOrderItem(),
            'modelPromoCode' => $filter->getModelPromoCode(),

            'modelUser' => $filter->getModelUser(),
            'modelUserForStore' => $filter->getModelUserForStore(),

            'modelOrdersGroupsForStore' => $filter->getOrdersGroupsForStore(),

            'modelOrderDateStart' => $filter->getModelOrderDateStart(),
            'modelOrderDateEnd' => $filter->getModelOrderDateEnd(),
        ]);
    }

    public function actionFindOrderList()
    {
        //$this->view->registerCssFile('/css/shop-management/reports-find-order.css');
        $filter = new OwnerOrderFilter();
        if(Yii::$app->request->isGet){
            $filter->setParams(Yii::$app->request->get());
        }
        //$filter->setParams(Yii::$app->request->get());
        //$filter->setParams(Yii::$app->request->post());

        if(\Yii::$app->user->can('clubAdmin')){
            $store_id = User::find()->where(['id'=>Yii::$app->user->id])->one()->store_id;
            $addParams['OwnerOrderFilter'] = [
                'addressClub' => ShopsStores::find()->where(['id'=>$store_id])->one()->address_id
            ];
            $filter->setParams($addParams);
        }
        $filter->getOrderListQuery();
        //print_r($filter->getOrderList());die();
        $orders = new ActiveDataProvider([
            'query' => $filter->getOrderList(),
/*            'pagination' => [
                'pageSize' => 20,
            ],*/
        ]);

        return $this->render('find-order-list', [
            'filter' => $filter,
            'orders' => $orders,
        ]);
    }

    public function actionAbandonedBasketReport()
    {
        $searchModel = new BasketSearch();
        $dataProvider = $searchModel->searchAbandonedBasket(Yii::$app->request->queryParams);

        return $this->render('abandoned-basket-report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMasterStatistic()
    {
        return $this->render('master-statistic');
    }

    public function actionMasterPay()
    {
        $searchModel = new BasketSearch();
        $dataProvider = $searchModel->searchMasterPayBasket(Yii::$app->request->queryParams);

        return $this->render('master-pay', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMasterTanya()
    {
        $orders = Orders::find()->where(['>', 'date', '2016-09-00'])->andWhere(['status' => 1])->andWhere(['NOT IN', 'user_id', [10015520, 10013181, 10013387, 10015549]])->all();

        foreach ($orders as $i => $order) {
            if ($order->bonus == 0) {
                unset($orders[$i]);
            }
        }

        return $this->render('master-tanya', [
            'orders' => $orders,
        ]);
    }


    public function actionShopsgoods()
    {
        $searchModel = new ShopsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('shopgoods', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewgoods($id)
    {
        //print_r(GoodsVariations::find()->where(['id'=>1000000022])->with('countsVariation')->one());die();
        if (!empty($id)) {
            $id = intval($id);
            if (is_int($id)) {
                $shop = Shops::find()->where(['id' => $id])->asArray()->one();
            }
        }

        $searchModel = new GoodsVariationsSearch();
        if (empty($shop)) {
            $dataProvider = $searchModel->searchByShop(-1, Yii::$app->request->queryParams);
        } else {
            $dataProvider = $searchModel->searchByShop($shop['id'], Yii::$app->request->queryParams);
        }

        return $this->render('viewgoods', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shop' => $shop,

        ]);
    }

    public function actionPreorder()
    {
        $searchModel = new GoodsPreorderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('preorder', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRealClients() {

        // Получение даты
        //$get_date = Yii::$app->request->get('UserSearchDefault');
        $params = Yii::$app->request->get();
        /*if (!empty($get_date['dateStart']) && !empty($get_date['dateEnd'])) {
            $params = ['UserSearchDefault']['dateStart'] = $get_date['dateStart'];
            $params = ['UserSearchDefault']['dateEnd'] = $get_date['dateEnd'];
        } else
        */
        if(empty($params['UserSearchDefault']['dateStart']) && empty($params['UserSearchDefault']['dateEnd'])){
            $params['UserSearchDefault']['dateStart'] = date('Y-m-d', strtotime('first day of this month'));
            $params['UserSearchDefault']['dateEnd'] = date('Y-m-d', time());
        }

        $searchModel = new UserSearchDefault();
        $dataProvider = $searchModel->search($params);

        return $this->render('real-clients', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'params' => $params,
        ]);
    }

    public function actionRealClients2() {

        // Получение даты
        //$get_date = Yii::$app->request->get('UserSearchDefault');
        $params = Yii::$app->request->get();
        /*if (!empty($get_date['dateStart']) && !empty($get_date['dateEnd'])) {
            $params = ['UserSearchDefault']['dateStart'] = $get_date['dateStart'];
            $params = ['UserSearchDefault']['dateEnd'] = $get_date['dateEnd'];
        } else
        */
        if(empty($params['UserSearchDefault2']['dateStart']) && empty($params['UserSearchDefault2']['dateEnd'])){
            $params['UserSearchDefault2']['dateStart'] = date('Y-m-d', strtotime('first day of this month'));
            $params['UserSearchDefault2']['dateEnd'] = date('Y-m-d', time());
        }

        $searchModel = new UserSearchDefault2();
        $dataProvider = $searchModel->search($params);

        return $this->render('real-clients-2', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'params' => $params,
        ]);
    }

    public function actionReportsOrdersMonth()
    {
        // Диапазон дат
        $begin = '2015-01-01';
        $end = date('Y-m-d', time());

        $db = Yii::$app->getDb();
        $sql = "SELECT date_format(orders.date, \"%Y-%m\") AS `date_sum`, SUM((`orders_items`.`price`)*(`orders_items`.`count`)) AS `money`, COUNT(DISTINCT `orders`.`id`) AS `sum_count`
                FROM `orders_items`
                LEFT JOIN `orders_groups` ON `orders_items`.`order_group_id` = `orders_groups`.`id`
                LEFT JOIN `orders` ON `orders_groups`.`order_id` = `orders`.`id`
                LEFT JOIN `goods` ON `goods`.`id` = `orders_items`.`good_id`
                LEFT JOIN `orders_status` ON `orders_status`.`id` = `orders_items`.`status_id`
                WHERE `orders`.`date` >= '".$begin."' AND `orders`.`date` <= '".$end."'
                AND `orders_items`.`status` = 1 
                AND `orders`.`type` = 1
                AND `orders_groups`.`type_id` > 0
                AND `orders`.`status` = 1 
                GROUP BY `date_sum`
                ORDER BY date_format(orders.date, \"%Y-%m\") ASC";
        $arr = $db->createCommand($sql)->queryAll();

        return $this->render('reports-orders-month', ['arr' => $arr]);
    }


    public function actionCategoryProduct(){
        $categoty_id = '';
        if(Yii::$app->request->get()) {
            $request = Yii::$app->request->get();
            $categoty_id = $request['category'];
            $searchModel = new GoodsVariationsSearch();
            $dataProvider = $searchModel->searchCategoryProduct(Yii::$app->request->get());
            return $this->render('categoryproduct', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel,'categoty_id'=>$categoty_id]);
        }else{
            return $this->render('categoryproduct',['categoty_id' => $categoty_id]);
        }
    }


    public function actionNewGoods(){
        $data = [];
        $filter = [];
        $totalGoods = 0;
        $totalVariations = 0;
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $date_begin = date('Y-m-d',strtotime($request['date_begin'])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime($request['date_end'])).' 23:23:59';
            $filter['date_begin'] = $date_begin;
            $filter['date_end'] = $date_end;
            $goods = Goods::find()->select(['id','date_create','name'])->where('date_create >= "'.$date_begin.'"')->andWhere('date_create<= "'.$date_end.'"')->asArray()->orderBy('date_create')->All();
            $goods = ArrayHelper::index($goods, 'id');
            $totalGoods = count($goods);
            // РАСКЛАДКА ПО ДАТАМ
//            foreach($goods as $id => $good){
//                $good['date_create'] = date('Y-m-d', strtotime($good['date_create']));
//                if(!isset($data[$good['date_create']])){
//                    $data[$good['date_create']] = [];
//                }
//                $category = CategoryLinks::find()->select('category_id')->where(['product_id'=>$id])->asArray()->One();
//                $category = Category::find()->select('title')->where(['id'=> $category['category_id']])->asArray()->One();
//                if(!isset($data[$good['date_create']][$category['title']])){
//                    $data[$good['date_create']][$category['title']] = [];
//                }
//                $data[$good['date_create']][$category['title']][$id] = $good;
//            }
            foreach($goods as $id => $good){
                $category = CategoryLinks::find()->select('category_id')->where(['product_id'=>$id])->asArray()->One();
                $category = Category::find()->select('title')->where(['id'=> $category['category_id']])->asArray()->One();
                if(!isset($data[$category['title']])) {
                    $data[$category['title']] = [];
                }
                $data[$category['title']][$id] = $good;
                $goodVariations = GoodsCounts::find()->where(['good_id'=>$id])->andWhere(['>=','update',$date_begin])->andWhere(['<=','update',$date_end])->asArray()->All();
                $data[$category['title']][$id]['variations'] = count($goodVariations);
                $totalVariations += $data[$category['title']][$id]['variations'];

            }
            //echo '<pre>'.print_r($data,1).'</pre>';die;

        }
        return $this->render('newgoods',['data'=>$data,'filter'=>$filter,'totalVariations' => $totalVariations,'totalGoods'=>$totalGoods]);
    }


    public function actionOrderNew()
    {
        $filter = new OwnerOrderFilter();
        if(Yii::$app->request->isGet){
            $filter->setParams(Yii::$app->request->get());
            $session = Yii::$app->session;
            $session->set('filter', json_encode(Yii::$app->request->get()));
        }else{
            $filter->setParams(json_decode($_SESSION['filter'],1));
        }

        $filter->getOrderListQuery();
        $orders = new ActiveDataProvider([
            'query' => $filter->getOrderList(),
                           'pagination' => [
                            'pageSize' => 20 //20,
                        ],
        ]);
        $itemsIDs = ArrayHelper::getColumn($filter->itemsIDs,'id');
        $items = OrdersItems::find()->where(['IN','id',$itemsIDs]);
        return $this->render('order-new', [
            'filter' => $filter,
            'orders' => $orders,
            'items' => $items,
        ]);
    }


    public function actionMiniOrderNew()
    {
        $dateStart = date('Y-m-d',strtotime('- 1 week'));
        $dateEnd = date('Y-m-d',strtotime('now'));
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            if(isset($request['dateStart'])){
                $dateStart = $request['dateStart'];
            }
            if(isset($request['dateEnd'])){
                $dateEnd = $request['dateEnd'];
            }

        }
        $data = [];
        $orders = Orders::find()
            ->where(['status' => 1])
            ->andWhere(['>=','date',$dateStart.' 00:00:00'])
            ->andWhere(['<=','date',$dateEnd.' 23:59:59'])
            ->andWhere(['NOT LIKE','comments','test'])
            ->andWhere(['NOT LIKE','comments','тест'])
            ->andWhere(['NOT LIKE','comments','tect'])
            ->andWhere(['NOT LIKE','comments','te$t'])
            ->orderBy('date DESC')
            ->All();
        foreach ($orders as $order){
            if(!isset($data[date('Y-m-d',strtotime($order->date))])) {
                $data[date('Y-m-d', strtotime($order->date))] = [
                    'date' => date('d.m.Y', strtotime($order->date)),
                    'count' => 0, //количество заказов
                    'full_price' => 0, //Полная стоимость
                    'discount' => 0, //Скидка
                    'bonus' => 0, //Расчет бонусами
                    'profit' => 0, //Прибыль
                    'revenues' => 0, //Выручка
                    'deliveryPrice' => 0, //Доставка
                    'payments_to_suppliers' => 0, //Выплаты поставщикам
                    'cashback' => 0, //Кэшбэк
                    'first_cost' => 0, //Себестоимость
                    'commission' => 0, //Комиссия за товар'
                    'cancel' => 0, //Отмены
                    'detail' => [
                        'fastOrder' => 0,
                        'sportGoods' => 0,
                        'extrmeGoods' => 0,
                        'other' => 0,
                    ],
                ];
            }
            foreach ($order->ordersGroups as $ordersGroup){
                if($ordersGroup->status == 1){
                    $data[date('Y-m-d', strtotime($order->date))]['deliveryPrice'] += $ordersGroup->delivery_price;
                    foreach ($ordersGroup->ordersItems as $ordersItem){
                        if($ordersItem->status == 1){
                            $fullPrice = $ordersItem->price * $ordersItem->count;
                            $discount = $ordersItem->discount * $ordersItem->count;
                            $bonus = $ordersItem->bonus * $ordersItem->count;
                            $paymentsToSuppliers = ($ordersItem->price - $ordersItem->comission)* $ordersItem->count;
                            $cashback = $ordersItem->fee * $ordersItem->count;
                            $commission = ($ordersItem->discount + $ordersItem->comission) * $ordersItem->count;
                            $data[date('Y-m-d', strtotime($order->date))]['full_price'] += $fullPrice;
                            $data[date('Y-m-d', strtotime($order->date))]['discount'] += $discount;
                            $data[date('Y-m-d', strtotime($order->date))]['bonus'] += $bonus;
                            $data[date('Y-m-d', strtotime($order->date))]['payments_to_suppliers'] += $paymentsToSuppliers;
                            $data[date('Y-m-d', strtotime($order->date))]['cashback'] += $cashback;
                            $data[date('Y-m-d', strtotime($order->date))]['commission'] += $commission;
                            $data[date('Y-m-d', strtotime($order->date))]['revenues'] += $fullPrice - $bonus - $discount;
                            $data[date('Y-m-d', strtotime($order->date))]['profit'] += $fullPrice - ($fullPrice - $commission +  $discount +  $cashback - $discount)  - $bonus - $discount;
                            $data[date('Y-m-d', strtotime($order->date))]['first_cost'] += $fullPrice - $commission +  $discount +  $cashback;
                            if($ordersItem->product->type_id == 1014){
                                $data[date('Y-m-d', strtotime($order->date))]['detail']['fastOrder'] += $fullPrice;
                            }else if($ordersItem->product->type_id == 1011){
                                $data[date('Y-m-d', strtotime($order->date))]['detail']['extrmeGoods'] += $fullPrice;
                            }else if(in_array($ordersItem->product->type_id,[1009,1012,1010])){
                                $data[date('Y-m-d', strtotime($order->date))]['detail']['sportGoods'] += $fullPrice;
                            }else{
                                $data[date('Y-m-d', strtotime($order->date))]['detail']['other'] += $fullPrice;
                            }
                        }else if($ordersItem->status == 0){
                            $data[date('Y-m-d', strtotime($order->date))]['cancel'] += ($ordersItem->price - $ordersItem->discount - $order->bonus) * $ordersItem->count;
                        }
                    }
                    $data[date('Y-m-d', strtotime($order->date))]['revenues'] += $ordersGroup->delivery_price;
                    $data[date('Y-m-d', strtotime($order->date))]['profit'] += $ordersGroup->delivery_price;
                }
            }
            $data[date('Y-m-d', strtotime($order->date))]['count']++;
        }



        $dataProvider = new ArrayDataProvider([
            //'key' => 'id',
            'allModels' => $data,
            'sort' => [
                'attributes' => [
                    'date',
                    'full_price', //Полная стоимость
                    'discount', //Скидка
                    'bonus', //Расчет бонусами
                    'profit', //Прибыль
                    'revenues', //Выручка
                    'deliveryPrice', //Доставка
                    'payments_to_suppliers', //Выплаты поставщикам
                    'cashback', //Кэшбэк
                    'first_cost', //Себестоимость
                    'commission', //Комиссия за товар'
                    'cancel',
                    'count'
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('mini-order-new', [
            'dataProvider' => $dataProvider,

        ]);
    }


    public function actionOrderNewDataSlice()
    {
        if(Yii::$app->request->isPost){
            $params = Yii::$app->request->post();
            $filter = new OwnerOrderFilter();
            $filter->setParams(Yii::$app->request->post());
            $filter->getOrderListQuery();
            $orders = $filter->getOrderList()->All();
            $data = [
                'totalFood' => 0,
                'totalSport' => 0,
                'totalHome' => 0,
                'midComission' => 0,
                'midWeight' => 0,
                'foodComission' => 0,
                'sportComission' => 0,
                'homeComission' => 0,
            ];
            $comission = 0;
            $weight = 0;
            $countOrderItem = 0;
            foreach ($orders as $order){
                foreach ($order->ordersGroups as $orders_group){
                        if(in_array($orders_group->type_id,['1003'])){
                            $data['totalFood'] = $data['totalFood'] + OrdersItems::find()->where(['order_group_id'=>$orders_group->id,'status'=>1])->sum('orders_items.price * orders_items.count');//sum('(orders_items.price - orders_items.discount - orders_items.comission) * orders_items.count');
                            $data['foodComission'] = $data['foodComission'] + OrdersItems::find()->where(['order_group_id'=>$orders_group->id,'status'=>1])->sum('orders_items.comission * orders_items.count');
                        }elseif(in_array($orders_group->type_id,['1005','1010','1009','1012'])){
                            $data['totalSport'] = $data['totalSport'] + OrdersItems::find()->where(['order_group_id'=>$orders_group->id,'status'=>1])->sum('orders_items.price * orders_items.count');//sum('(orders_items.price - orders_items.discount - orders_items.comission) * orders_items.count');
                            $data['sportComission'] = $data['sportComission'] + OrdersItems::find()->where(['order_group_id'=>$orders_group->id,'status'=>1])->sum('orders_items.comission * orders_items.count');
                        }elseif(in_array($orders_group->type_id,['1007'])){
                            $data['totalHome'] = $data['totalHome'] + OrdersItems::find()->where(['order_group_id'=>$orders_group->id,'status'=>1])->sum('orders_items.price * orders_items.count');//sum('(orders_items.price - orders_items.discount - orders_items.comission) * orders_items.count');
                            $data['homeComission'] = $data['homeComission'] + OrdersItems::find()->where(['order_group_id'=>$orders_group->id,'status'=>1])->sum('orders_items.comission * orders_items.count');
                        }
                        $ordersItems = OrdersItems::find()->where(['status'=>1,'order_group_id'=>$orders_group->id]);
                        $countOrderItem = $countOrderItem + $ordersItems->count();
                        foreach ($ordersItems->all() as $ordersItem){
                            if($ordersItem->goodsVariations->weight && !is_array($ordersItem->goodsVariations->weight)){
                                $weight = $ordersItem->goodsVariations->weight->value;
                            }
                            $comission = $comission + $ordersItem->goodsVariations->comission;
                        }
                }
            }
            if($weight>0){
                $data['midWeight'] = round($filter->priceResult['shopsPays']/$weight,2);
            }
            if($countOrderItem>0){
                $data['midComission'] = round($comission/$countOrderItem*100)/100;
            }


            return $this->renderPartial('_order-new-data-slice', [
                'data' => $data,
            ]);

        }

    }


    public function actionChangeOrderTime($orderId,$newTime,$orderGroupId){
        if(strtotime($newTime) <= strtotime('now') && date('h',strtotime($newTime)) <= 8){
            return 'Перенос на это время не возможен';
        }
        $data = '';
        $msg = '';
        $order_group = OrdersGroups::find()->where(['id'=>$orderGroupId])->One();
        if(Yii::$app->user->can('GodMode')){

        }else if(strtotime($order_group->delivery_date) < strtotime($newTime)){
            return  'Не допустимое время';
        }

        if(OrdersItems::find()->where(['status'=>1,'order_group_id'=>$orderGroupId,'store_id'=>'10000196'])->count()>0) {
            $postData['order_id'] = $orderId;
            $postData['order_group'] = $orderGroupId;
            $postData['new_time'] = $newTime;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://helper.express/ajax/changeordertimemetroapi');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $data = curl_exec($ch);
            curl_close($ch);
            if($data == 'done'){

            }elseif($data == 'unavailable time'){
                return  'Не допустимое время';
            }elseif ($data == 'Error'){
                return  'Ошибка helper!';
            }
        }

        if(OrdersItems::find()->where(['status'=>1,'order_group_id'=>$orderGroupId,'store_id'=>'10000196'])->count()>0) {
            $postData['order_id'] = $orderId;
            $postData['order_group'] = $orderGroupId;
            $postData['new_time'] = $newTime;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://helper.express/ajax/changeordertimeapi');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $data = curl_exec($ch);
            curl_close($ch);
            if($data == 'done'){

            }elseif($data == 'unavailable time'){
                return  'Не допустимое время';
            }elseif ($data == 'Error'){
                return  'Ошибка helper!';
            }else{
                return $data;
            }
        }



        $order_group->delivery_date = date('Y-m-d H:i:s',strtotime($newTime));
        if($order_group->save()){
            $msg =  'Новое время установлено';
        }else{
            $msg = 'Что то пошло не так..';
        }

        return $msg;
    }

    public function actionReportsUsers() {

        // Получение даты
        $params = Yii::$app->request->get();

        if(empty($params['UsersLogsSearch']['dateStart']) && empty($params['UsersLogsSearch']['dateEnd'])){
            $params['UsersLogsSearch']['dateStart'] = date('Y-m-d', strtotime('first day of this month'));
            $params['UsersLogsSearch']['dateEnd'] = date('Y-m-d', time());
        }

        $searchModel = new UsersLogsSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('reports-users', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'params' => $params
        ]);
    }



    /*public function actionOrderpreorder()
    {
        $searchModel = new OrdersGroupsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('orderpreorder', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }*/

    public function actionFillTimetable(){
        foreach(ShopsStores::find()->All() as $store){
            for($i = 1;$i<=5;$i++){
                $timeTable = new ShopStoresTimetable();
                $timeTable->day = $i;
                $timeTable->store_id= $store->id;
                $timeTable->time_begin = '08:00';
                $timeTable->time_end = '20:00';
                $timeTable->status = 1;
                $timeTable->save();
            }
        }
    }


}





