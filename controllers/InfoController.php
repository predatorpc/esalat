<?php

namespace app\controllers;

use app\components\WProductItem;
use app\components\WVariantForm;
use app\modules\catalog\models\GalleryShop;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsImages;
use app\modules\catalog\models\GoodsImagesLinks;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\common\controllers\FrontController;
use app\modules\common\controllers\BackendController;
use app\modules\common\models\Address;
use app\modules\common\models\Api;
use app\modules\common\models\UserParams;
use app\modules\common\models\UserShop;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\ShopGroupVariantLink;
use app\modules\managment\models\ShopsImages;
use app\modules\managment\models\ShopsStores;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use app\modules\shop\models\OrdersItemsStatus;
use app\modules\shop\models\OrdersStatus;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
// old sites

/**
 * BasketController implements the CRUD actions for Basket model.
 */
class InfoController extends BackendController
{
   // public $enableCsrfValidation = false;

    public function behaviors()
    {
    //    return [];
     /*   return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'get-orders-count',
                        ],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
        ]; */
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
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

    public function actionGetOrdersCount()
    {
        //header('Access-Control-Allow-Origin: *');
      //  Zloradnij::print_arr($_SERVER);

        $db = Yii::$app->getDb();
 /*       $sql = "
SELECT count(*)
LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id`
LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` = `orders_groups`.`id`
FROM  `orders`
WHERE  `orders`.`date` >  '".date("Y-m-d 00:00:00",strtotime("now"))."'
AND  `orders`.`status` =1
AND  `orders`.`type` =1
ORDER BY  `orders`.`date` DESC
LIMIT 0 , 30";
*/

        $sql = "
SELECT  `orders`.`id`
FROM  `orders`
LEFT JOIN  `orders_groups` ON  `orders_groups`.`order_id` =  `orders`.`id`
LEFT JOIN  `orders_items` ON  `orders_items`.`order_group_id` =  `orders_groups`.`id`
WHERE  `orders`.`type` =1
AND  `orders`.`status` =1
AND  `orders_items`.`status` =1
AND  `orders_groups`.`status` =1
AND  `orders`.`date` >  '".date("Y-m-d 00:00:00",strtotime("now"))."'
AND (`orders_groups`.`type_id` = 1003 OR `orders_groups`.`type_id` = 1007)
AND  `orders_items`.`store_id` NOT
IN ( 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000008, 10000108, 10000191 )
AND (`orders_groups`.`type_id`=1003 OR `orders_groups`.`type_id`=1007)
GROUP BY  `orders`.`id`
ORDER BY  `orders`.`date` DESC ";

    //    Zloradnij::print_arr($sql);die();

        $data = $db->createCommand($sql)->queryAll();

        $sql = "
SELECT  `orders`.`id`, `orders_items`.`price`, `orders_items`.`count`
FROM  `orders`
LEFT JOIN  `orders_groups` ON  `orders_groups`.`order_id` =  `orders`.`id`
LEFT JOIN  `orders_items` ON  `orders_items`.`order_group_id` =  `orders_groups`.`id`
WHERE  `orders`.`type` =1
AND  `orders`.`status` =1
AND  `orders_items`.`status` =1
AND  `orders_groups`.`status` =1
AND  `orders`.`date` >  '".date("Y-m-d 00:00:00",strtotime("now"))."'
AND (`orders_groups`.`type_id` = 1003 OR `orders_groups`.`type_id` = 1007)
AND  `orders_items`.`store_id` NOT
IN ( 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000008, 10000108, 10000191 )
ORDER BY  `orders`.`date` DESC
        ";

        //Zloradnij::print_arr($sql);die();

        $data_sum = $db->createCommand($sql)->queryAll();

        $sum = 0;
        foreach($data_sum as $item)
            $sum += $item['price']*$item['count'];

        //Zloradnij::print_arr($sum);die();
        //
        //return '!!!!!!';
      //  $data_return = [];
       // $data_return['status'] = 1;
       // $data_return['data'] = $data;

       // Zloradnij::print_arr($data);die();


        $result['count'] = count($data);
        $result['summa'] = $sum;

        return 'ES Сегодня '.$result['count'].' на сумму '.number_format($result['summa'], 2, '.', ' ').' р.';

    }

    public function actionGetOrdersCountYesterday()
    {
        //header('Access-Control-Allow-Origin: *');
        //  Zloradnij::print_arr($_SERVER);

        $db = Yii::$app->getDb();
        /*       $sql = "
       SELECT count(*)
       LEFT JOIN `orders_groups` ON `orders_groups`.`order_id` = `orders`.`id`
       LEFT JOIN `orders_items` ON `orders_items`.`order_group_id` = `orders_groups`.`id`
       FROM  `orders`
       WHERE  `orders`.`date` >  '".date("Y-m-d 00:00:00",strtotime("now"))."'
       AND  `orders`.`status` =1
       AND  `orders`.`type` =1
       ORDER BY  `orders`.`date` DESC
       LIMIT 0 , 30";
       */

        $sql = "
SELECT  `orders`.`id`
FROM  `orders`
LEFT JOIN  `orders_groups` ON  `orders_groups`.`order_id` =  `orders`.`id`
LEFT JOIN  `orders_items` ON  `orders_items`.`order_group_id` =  `orders_groups`.`id`
WHERE  `orders`.`type` =1
AND  `orders`.`status` =1
AND  `orders_items`.`status` =1
AND  `orders_groups`.`status` =1
AND  `orders`.`date` >  '".date("Y-m-d 00:00:00",strtotime("yesterday"))."'
AND  `orders`.`date` <  '".date("Y-m-d",strtotime("yesterday"))." ".date("H:i:s",strtotime("now"))."'
AND (`orders_groups`.`type_id` = 1003 OR `orders_groups`.`type_id` = 1007)
AND  `orders_items`.`store_id` NOT
IN ( 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000008, 10000108, 10000191 )
GROUP BY  `orders`.`id`
ORDER BY  `orders`.`date` DESC ";

        //Zloradnij::print_arr($sql);die();

        $data = $db->createCommand($sql)->queryAll();

        $sql = "
SELECT  `orders`.`id`, `orders_items`.`price`, `orders_items`.`count`
FROM  `orders`
LEFT JOIN  `orders_groups` ON  `orders_groups`.`order_id` =  `orders`.`id`
LEFT JOIN  `orders_items` ON  `orders_items`.`order_group_id` =  `orders_groups`.`id`
WHERE  `orders`.`type` =1
AND  `orders`.`status` =1
AND  `orders_items`.`status` =1
AND  `orders_groups`.`status` =1
AND  `orders`.`date` >  '".date("Y-m-d 00:00:00",strtotime("yesterday"))."'
AND  `orders`.`date` <  '".date("Y-m-d",strtotime("yesterday"))." ".date("H:i:s",strtotime("now"))."'
AND (`orders_groups`.`type_id` = 1003 OR `orders_groups`.`type_id` = 1007)
AND  `orders_items`.`store_id` NOT
IN ( 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000008, 10000108, 10000191 )
ORDER BY  `orders`.`date` DESC
        ";

       // Zloradnij::print_arr($sql);die();

        $data_sum = $db->createCommand($sql)->queryAll();

        $sum = 0;
        foreach($data_sum as $item)
            $sum += $item['price']*$item['count'];

        //Zloradnij::print_arr($sum);die();
        //
        //return '!!!!!!';
        //  $data_return = [];
        // $data_return['status'] = 1;
        // $data_return['data'] = $data;

        // Zloradnij::print_arr($data);die();


        $result['count'] = count($data);
        $result['summa'] = $sum;

        return 'ES Вчера '.$result['count'].' на сумму '.number_format($result['summa'], 2, '.', ' ').' р.';

    }

}