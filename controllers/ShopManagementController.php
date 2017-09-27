<?php

namespace app\controllers;

use app\modules\common\models\UserSearch;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\CategoryLinks;
use app\modules\catalog\models\Codes;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsImagesLinks;
use app\modules\catalog\models\GoodsSearch;
use app\modules\catalog\models\GoodsTypes;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\catalog\models\TagsLinks;
use app\modules\catalog\models\GoodsImages;

use app\modules\common\models\User;
use app\modules\common\models\Zloradnij;

use app\modules\managment\models\ShopGeneral;
use app\modules\managment\models\ShopGroup;
use app\modules\managment\models\ShopGroupRelated;
use app\modules\managment\models\ShopGroupVariantLink;
use app\modules\managment\models\ShopsStores;
use app\modules\common\models\UserShop;
use app\modules\catalog\models\CategorySearch;
use app\modules\catalog\models\Category;

use app\modules\common\controllers\BackendController;

use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersItems;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
//use app\modules\managment\models\ShopGroupRelated;
use app\modules\catalog\models\CodesTypes;
use app\modules\catalog\models\CodesSearch;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Address;


/**
 * CategoryController implements the CRUD actions for Category model.
 */
class ShopManagementController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'promo-code-statistic',
                            'promo-code-statistic-export',
                            'promo-statistic',
                            'shop-groups',
                            'shop-group-create',
                            'shop-group-update',
                            //'shop-group-related-delete',
                            'admin-address-base',
                            'download-address-base',
                            'disable-invalid-address',
                            'shop-group-related-delete',
                            'stores',
                            'stores-update',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'callcenterOperator', 'categoryManager', 'conflictManager', 'clubAdmin', /*'shopOwner',*/ 'HR'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionDownloadAddressBase(){
        $model = Address::find()->where('status = 1')->asArray()->all();
        return json_encode($model);
    }

    public function actionDisableInvalidAddress(){
        //if(!empty($_POST['model'])){
            //$model = $_POST['model'];
        $model = Yii::$app->request->post();
            Zloradnij::print_arr($model);die();
            //return $this->render('admin-address-base');
        //}
    }

    public function actionAdminAddressBase(){
        return $this->render('admin-address-base');
    }

    /**
     *
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {

        $db = Yii::$app->getDb();


        // Загрузка заказов;
//        $sql = "SELECT
//                `orders`.`id` AS `order_id`,
//                `orders`.`date`,
//                `orders_groups`.`type_id`
//                FROM `orders`
//                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
//                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
//                WHERE"
//                ." DATE(`orders`.`date`) >= '".date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
//                ." AND DATE(`orders`.`date`) <= '".date("Y-m-d")."'"
//                ." AND `orders`.`type`=1"
//                ." AND `orders_groups`.`type_id` IS NOT NULL "
//                ." AND `orders`.`status` = '1' GROUP BY `orders_groups`.`type_id` ORDER BY `orders`.`date` DESC";
//

        $now = date('Y-m-d', strtotime("Monday")); // 25.12.2006
        $monday  = date('Y-m-d', strtotime("Monday")); // 25.12.2006
        if($now == $monday)

            $monday  = date('Y-m-d', strtotime("last Monday")); // 25.12.2006

        $sunday = date('Y-m-d', strtotime("Sunday"));

//        Zloradnij::print_arr($monday);
//        Zloradnij::print_arr($sunday);
//        die();
//        `orders_items`.`status` as order_status,
//                `orders_groups`.`status` as group_status,
//                `orders`.`status` as item_status,
//                `orders_items`.`status_id` as item_status_id

        $sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            ." AND (`orders_items`.`status` = 1 ) "
            ." AND (`orders`.`status` = 1 ) "
            ." AND (`orders_groups`.`status` = 1 ) "
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "

            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ." GROUP by `orders`.`id` ORDER BY `orders`.`date` DESC";

       // echo $sql;die();
        $statistic = $db->createCommand($sql)->queryAll();

        $sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`,
                `orders_items`.`price`,
                `orders_items`.`count`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            ." AND (`orders_items`.`status` = 1 ) "
            ." AND (`orders`.`status` = 1 ) "
            ." AND (`orders_groups`.`status` = 1 ) "
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "

            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ." ORDER BY `orders`.`date` DESC";
        $statisticPrice = $db->createCommand($sql)->queryAll();


        $sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            ." AND (`orders_items`.`status` = 0 ) "
            ." AND (`orders`.`status` = 1 ) "
//            ." AND (`orders_groups`.`status` = 0 ) "
            //." AND (`orders`.`comments` NOT LIKE 'тест')"
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "

            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ." GROUP by `order_id` ORDER BY `orders`.`date` DESC";

        $statisticCancel = $db->createCommand($sql)->queryAll();


        $sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`,
                `orders_items`.`price`,
                `orders_items`.`count`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            ." AND (`orders_items`.`status` = 0 ) "
            ." AND (`orders`.`status` = 1 ) "
            //." AND (`orders`.`comments` NOT LIKE 'тест')"
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "

            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ." ORDER BY `orders`.`date` DESC";

        $statisticCancelPrice = $db->createCommand($sql)->queryAll();


        $sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            ." AND (`orders_items`.`status` = 0 ) "
       //     ." AND (`orders`.`status` = 0 ) "
//            ." AND (`orders_groups`.`status` = 0 ) "
            ." AND ((`orders`.`comments` LIKE '%тест%')"
            ." OR (`orders`.`comments` LIKE '%test%'))"
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "

            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ." GROUP by `order_id` ORDER BY `orders`.`date` DESC";

        $statisticCancelTest = $db->createCommand($sql)->queryAll();


        $sql = "SELECT
                `orders`.`id` AS `order_id`,
                `orders`.`date`,
                `orders_groups`.`type_id`,
                `orders_items`.`price`,
                `orders_items`.`count`
                FROM `orders`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` =  `orders_items`.`order_group_id`
                WHERE"
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            ." AND (`orders_items`.`status` = 0 ) "
     //       ." AND (`orders`.`status` = 0 ) "
            ." AND ((`orders`.`comments` LIKE '%тест%')"
            ." OR (`orders`.`comments` LIKE '%test%'))"
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "

            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ." ORDER BY `orders`.`date` DESC";

        $statisticCancelPriceTest = $db->createCommand($sql)->queryAll();



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
            ." DATE(`orders`.`date`) >= '".$monday."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$sunday."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            //." AND (`orders_items`.`status` = 1 ) "
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "
            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ."ORDER BY `orders`.`date` ASC";
        $statisticGeneral = $db->createCommand($sql)->queryAll();

        $month_start = strtotime('first day of this month', time());
        $month_end = strtotime('last day of this month', time());

        $month_1 = date('Y-m-d', $month_start);
        $month_2 = date('Y-m-d', $month_end);

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
            ." DATE(`orders`.`date`) >= '".$month_1."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$month_2."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            //." AND (`orders_items`.`status` = 1 ) "
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "
            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ."ORDER BY `orders`.`date` ASC";
        $statisticGeneralMonth = $db->createCommand($sql)->queryAll();


        $year_start = strtotime('first day of this year', time());
//        $month_end = strtotime('last day of this month', time());
        $year_first_day = date('Y-01-01');
        $now = date('Y-m-d', strtotime('now', time()));

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
            ." DATE(`orders`.`date`) >= '".$year_first_day."'" //.date("Y-m-d", time() - (60 * 60 * 24 * 6))."'"
            ." AND DATE(`orders`.`date`) <= '".$now."'"//.date("Y-m-d")."'"
            //  ." AND `orders`.`type`=1"
//                ." AND ((`orders_groups`.`type_id` = 1003) OR (`orders_groups`.`type_id` = 1001) OR (`orders_groups`.`type_id` = 1010))"
            ." AND (`orders_groups`.`type_id` > 0)"
//                ." AND ((`orders`.`status` = '1') AND (`orders_groups`.`status` = '1') "
            //." AND (`orders_items`.`status` = 1 ) "
            //." AND (`orders_items`.`status_id` IS NOT NULL ) "
            //." AND (SELECT `users`.`staff` FROM `users` WHERE `id` = `orders`.`user_id` LIMIT 1) IS NULL "
            ."ORDER BY `orders`.`date` ASC";
        $statisticGeneralYear = $db->createCommand($sql)->queryAll();

//        Zloradnij::print_arr($statisticGeneralYear);die();



        return $this->render('index', [
            'actionsMenu' => $this->actionsMenu,
            'data' => $statistic,
            'sql' => $sql,
            'dataCancel' => $statisticCancel,
            'statisticPrice' => $statisticPrice,
            'statisticCancelPrice' => $statisticCancelPrice,
            'statisticCancelTest' => $statisticCancelTest,
            'statisticCancelPriceTest' => $statisticCancelPriceTest,
            'statisticGeneral' => $statisticGeneral,
            'statisticGeneralMonth' => $statisticGeneralMonth,
            'statisticGeneralYear' => $statisticGeneralYear,
            'monday' => $monday,
            'sunday' => $sunday,
            'month_start' => $month_1,
            'month_end' => $month_2,
        ]);
    }

    public function actionPromoCodeStatistic(){

     /*   $trash = 0;
        $db = Yii::$app->getDb();

        if(empty($_GET['CodesSearch']['dateStart']))$dateStart = date("Y-m-d 00:00:00", strtotime("now"));
        if(empty($_GET['CodesSearch']['dateStop'])) $dateStop = date("Y-m-d 23:59:59", strtotime("now"));
  //      if(empty($_GET['CodesSearch']['usetype'])) $_GET['CodesSearch']['usetype'] = -1;
//
        if(!empty($_GET['CodesSearch']['dateStart']) && !empty($_GET['CodesSearch']['dateStop']) && empty($_GET['CodesSearch']['code'])) {
            $dateStart = date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart']));
            $dateStop = date("Y-m-d 23:59:59", strtotime($_GET['CodesSearch']['dateStop']));
            $sql = "SELECT users.id, users.name, users.phone
        FROM users
        WHERE users.staff IS NOT NULL        
        AND users.typeof IS NOT NULL        
        AND users.status = 1
        AND users.id NOT IN (10004448, 10013181)
        AND (
        SELECT orders.id
                FROM orders
                WHERE DATE(orders.date) >= '" . $dateStart . "'  AND DATE(orders.date) <= '" . $dateStop . "' AND orders.user_id = users.id
                LIMIT 1
        ) IS NULL
        AND (
        SELECT orders.id
               FROM orders
               LEFT JOIN codes ON codes.id = orders.code_id
               WHERE DATE(orders.date) >= '" . $dateStart . "' AND DATE(orders.date) <= '" . $dateStop . "' AND codes.user_id = users.id
               LIMIT 1
       ) IS NULL";

            if(!empty($_GET['CodesSearch']['club'])){
                $store_id = $_GET['CodesSearch']['club'];
                $sql .= " AND users.store_id = ".$store_id." ";
            }

            if(!empty($_GET['CodesSearch']['typeof'])){
                $typeof = $_GET['CodesSearch']['typeof'];
                $sql .= " AND users.typeof = ".$typeof." ";
            }

       $sql .=" group by users.id order by users.name DESC";

            $trash = $db->createCommand($sql)->queryAll();
    }

        if(empty($_GET['CodesSearch']['code'])) $_GET['CodesSearch']['code'] = 0;

        $codeTypeAll = CodesTypes::find()->indexBy('id')->all();

        $searchModel = new CodesSearch();
        $dataProvider = $searchModel->searchForGoodStatistic(Yii::$app->request->queryParams);

      //  var_dump(Yii::$app->request->get('p'));die();

        if(Yii::$app->request->get('p')!=NULL)
            $dataProvider->pagination = boolval(Yii::$app->request->get('p')); // отключаем пагинацию

        $userId = Yii::$app->user->getId();//UserShop::getIdentityUser();
        //Yii::info(date("Y-m-d H:i:s").' UserID: '.$userId.' Action: PromoCodeStatistic','Shops');




        $clubModel = new ShopsStores();*/




        $searchModel = new UserSearch();
        $dataProvider = $searchModel->StaffPromo($_GET['CodesSearch']);
        $trash = [];

        if(empty($_GET['CodesSearch']['dateStart']) || !isset($_GET['CodesSearch']['dateStart'])){$dateStart = date("Y-m-d", strtotime("-1 month"));}
        if(empty($_GET['CodesSearch']['dateStop']) || !isset($_GET['CodesSearch']['dateStart'])){ $dateStop = date("Y-m-d", strtotime("now"));}

        if(!empty($_GET['CodesSearch']['dateStart'])) {
            $dateStart = date("Y-m-d 00:00:00", strtotime($_GET['CodesSearch']['dateStart']));
        }
        if(!empty($_GET['CodesSearch']['dateStop'])){
            $dateStop = date("Y-m-d 23:59:59", strtotime($_GET['CodesSearch']['dateStop']));
        }

        $arUsers = User::find()->select(['id','name','phone'])->where(['status'=>1])->andWhere(['NOT IN','id',[10004448, 10013181]])->andWhere(['IS NOT','staff',NULL])->andWhere(['IS NOT','typeof',NULL])->asArray();

        //Задаем Тип пользователя
        if(!empty($_GET['CodesSearch']['typeof'])){
            $typeof = $_GET['CodesSearch']['typeof'];
            $arUsers = $arUsers->andWhere(['typeof'=>$typeof]);
        }

        //Задаем клуб
        if($_GET['CodesSearch']['club']>0){
            $club = $_GET['CodesSearch']['club'];
            $arUsers = $arUsers->andWhere(['store_id'=>$club]);
        }

        //Задаем промокод
        if(isset($_GET['CodesSearch']['code']) && $_GET['CodesSearch']['code']>0){
            $code = $_GET['CodesSearch']['code'];
            $code = Codes::find()->where(['code'=> $code])->One();
            if($code != NULL){
                $arUsers = $arUsers->andWhere(['id'=>$code->user_id]);
            }else{
                //Если такого промо нет, тут кастыль, что бы резульат был 0 в выводе
                $arUsers = $arUsers->andWhere(['id'=>101010101010101]);
            }

        }

        $arUsers = $arUsers->All();
        $arCodes = Codes::find()->where(['status'=>1])->andWhere(['IN','user_id',ArrayHelper::getColumn($arUsers,'id')])->asArray()->All();
        $code = ArrayHelper::getColumn($arCodes,'id');

        $arOrders = Orders::find()->where(['>=','date',$dateStart])->andWhere(['<=','date',$dateStop])->andWhere(['status'=>1])->andWhere(['IS NOT','code_id',NULL])->asArray()->All();
        $arOrders = ArrayHelper::getColumn($arOrders,'code_id');
        foreach ($code as $key => $id){
            if(!in_array($id, $arOrders )){
                $promo = Codes::find()->where(['id'=>$id])->One();
                $trash[] = ['code' =>$promo->code,
                            'user' => $promo->user->name,
                            'phone' => $promo->user->phone,
                ];
            }
        }

        return $this->render('promo-code-statistic-new', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'trash'=>$trash,
//            'userId' => $userId,
//            'codeTypeAll' => $codeTypeAll,
//            'clubModel' => $clubModel,
//            'data' => $data,
        ]);
    }

    public function actionPromoCodeStatisticExport(){
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->StaffPromo($_GET['CodesSearch']);


        return $this->render('promo-code-statistic-export', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPromoStatistic(){
        $list = (new CodesSearch())->searchStatistic(Yii::$app->request->queryParams);
        return $this->render('promo-statistic', [
            'list' => $list,
        ]);
    }

    /**
     * Displays a list Products.
     * @param integer $id
     * @return mixed
     */
    /////////////////////////////////////////////
    //  Shop Groups start
    ////////////////////////////////////////////
    public function actionShopGroups(){
        $groupsSearch = new \app\modules\managment\models\ShopGroupSearch();
        $groups = $groupsSearch->search(Yii::$app->request->queryParams);

        return $this->render('/shop-group/index',[
            'groupsSearch' => $groupsSearch,
            'groups' => $groups,
        ]);
    }

    public function actionShopGroupCreate(){
        $find = false;
        $model = new ShopGroup();

        if ($model->load(Yii::$app->request->post())) {
            $find = ShopGroup::find()->where(['name' => $model->name])->all();
        }

        if (!$find && $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'shop-groups',
                'groups' => new ActiveDataProvider([
                    'query' => ShopGroup::find()->where(['>=','status',0]),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]),
            ]);
        }
        return $this->render('/shop-group/shop-group-create',[
            'model' => new ShopGroup(),
        ]);
    }

    public function actionShopGroupUpdate($id){
        $model = \app\modules\managment\models\ShopGroup::find()->where(['id' => $id])->one();
        $shopGroupRelated = ShopGroupRelated::find()->where(['shop_group_id' => $id])->all();
        if(!$shopGroupRelated){
            $shopGroupRelated = [];
        }
        $shopGroupRelated[] = new ShopGroupRelated();

        if (Model::loadMultiple($shopGroupRelated, Yii::$app->request->post()) && Model::validateMultiple($shopGroupRelated)) {
            $count = 0;
            foreach ($shopGroupRelated as $key => $item) {
                $find = false;
                if(!isset($item->shop_group_id) || empty($item->shop_group_id)){
                    $item->shop_group_id = $id;
                }
                $find = ShopGroupRelated::find()->where(['shop_group_id' => $id,'shop_id' => $item->shop_id])->all();
                if ($item->shop_id != 0 && !$find && $item->save()) {
                    $count++;
                }
            }
            if($count == count($shopGroupRelated)){
                $shopGroupRelated[] = new ShopGroupRelated();
            }
            Yii::$app->session->setFlash('success', "Processed {$count} records successfully.");
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            unset($_POST);

            return $this->render('/shop-group/shop-group-update',[
                'model' => $model,
                'shops' => $shopGroupRelated,
            ]);
        }
        return $this->render('/shop-group/shop-group-update',[
            'model' => $model,
            'shops' => $shopGroupRelated,
        ]);
    }

    public function actionShopGroupRelatedDelete($id,$group){
        $model = ShopGroupRelated::find()->where(['id' => $id])->one();
        $model->delete();

        return $this->redirect([
            '/shop-management/shop-group-update',
            'id' => $group,
        ]);
    }
    ///////////////////////////////////////////////////
    //  Shop Groups END
    ///////////////////////////////////////////////////////



    //  Shops Stores
    public function actionStores(){
        $searchModel = new ShopsStores();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/shops-stores/store-list',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStoresUpdate($id){
        $model = $this->findModelStore($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render('/shops-stores/update', [
                'model' => $model,
            ]);
        } else {
            return $this->render('/shops-stores/update', [
                'model' => $model,
            ]);
        }
    }

    protected function findModelStore($id)
    {
        if (($model = ShopsStores::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function sortGraphResult($statisticGeneral, $dateFormat = 'd')
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

//\app\modules\common\models\Zloradnij::print_arr($dataArray);die();

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
//\app\modules\common\models\Zloradnij::print_arr($newArray);


        $header[0] = ['День'];

//\app\modules\common\models\Zloradnij::print_arr(count($newArray));die();

        $tmpArr = [];
//$cnt=0;
        foreach ($newArray as $item) {
            switch($dateFormat) {
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
                if(!in_array('Товары', $header[0]))
                    array_push($header[0], 'Товары');
            }
            else {
                $tmpArr[1] = 0;
                if(!in_array('Товары', $header[0]))
                    array_push($header[0], 'Товары');
            }

            if (!empty($item['1002'])) {
                $tmpArr[2] = $item['1002'];
                if(!in_array('Услуги', $header[0]))
                    array_push($header[0], 'Услуги');
            }
            else{
                $tmpArr[2] = 0;
                if(!in_array('Услуги', $header[0]))
                    array_push($header[0], 'Услуги');
            }

            if (!empty($item['1003'])) {
                $tmpArr[3] = $item['1003'];
                if(!in_array('Продукты', $header[0]))
                    array_push($header[0], 'Продукты');
            }
            else {
                $tmpArr[3] = 0;
                if(!in_array('Продукты', $header[0]))
                    array_push($header[0], 'Продукты');
            }

            if (!empty($item['1004'])) {
                $tmpArr[4] = $item['1004'];
                if(!in_array('Готовая еда', $header[0]))
                    array_push($header[0], 'Готовая еда');
            }
            else{
                $tmpArr[4] = 0;
                if(!in_array('Готовая еда', $header[0]))
                    array_push($header[0], 'Готовая еда');
            }

            if (!empty($item['1005'])) {
                $tmpArr[5] = $item['1005'];
                if(!in_array('Спортивные товары', $header[0]))
                    array_push($header[0], 'Спортивные товары');
            }
            else{
                $tmpArr[5] = 0;
                if(!in_array('Спортивные товары', $header[0]))
                    array_push($header[0], 'Спортивные товары');
            }

            if (!empty($item['1006'])) {
                $tmpArr[6] = $item['1006'];
                if(!in_array('Товары под заказ', $header[0]))
                    array_push($header[0], 'Товары под заказ');
            }
            else {
                $tmpArr[6] = 0;
                if(!in_array('Товары под заказ', $header[0]))
                    array_push($header[0], 'Товары под заказ');
            }

            if (!empty($item['1007'])) {
                $tmpArr[7] = $item['1007'];
                if(!in_array('Товары для дома', $header[0]))
                    array_push($header[0], 'Товары для дома');
            }
            else{
                $tmpArr[7] = 0;
                if(!in_array('Товары для дома', $header[0]))
                    array_push($header[0], 'Товары для дома');
            }

            if (!empty($item['1008'])) {
                $tmpArr[8] = $item['1008'];
                if(!in_array('Доставка 5-14 дней', $header[0]))
                    array_push($header[0], 'Доставка 5-14 дней');
            }
            else{
                $tmpArr[8] = 0;
                if(!in_array('Доставка 5-14 дней', $header[0]))
                    array_push($header[0], 'Доставка 5-14 дней');
            }

            if (!empty($item['1009'])) {
                $tmpArr[9] = $item['1009'];
                if(!in_array('Товары от Pure Protein', $header[0]))
                    array_push($header[0], 'Товары от Pure Protein');
            }
            else{
                $tmpArr[9] = 0;
                if(!in_array('Товары от Pure Protein', $header[0]))
                    array_push($header[0], 'Товары от Pure Protein');
            }

            if (!empty($item['1010'])) {
                $tmpArr[10] = $item['1010'];
                if(!in_array('Спортивное питание под заказ', $header[0]))
                    array_push($header[0], 'Спортивное питание под заказ');
            }
            else{
                $tmpArr[10] = 0;
                if(!in_array('Спортивное питание под заказ', $header[0]))
                    array_push($header[0], 'Спортивное питание под заказ');
            }

            $tmpArr[11] = $item['sum'];
            $header[] = $tmpArr;
            $tmpArr = [];
        }

        array_push($header[0], 'Всего');

        //Zloradnij::print_arr($header);die();

        return $header;
    }

    //  Shops Stores END
}
