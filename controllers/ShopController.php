<?php

namespace app\controllers;

use app\modules\catalog\models\Category;
use app\modules\catalog\models\GoodsImages;
use app\modules\common\controllers\BackendController;
use app\modules\shop\models\OrderFilter;
use yii\data\ActiveDataProvider;
use yii\debug\models\search\Log;
use app\modules\common\models\Zloradnij;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\catalog\models\CategorySearch;
use app\modules\common\models\Countries;
use yii\data\Pagination;
use yii\filters\AccessControl;


use Yii;
use app\modules\common\models\UserRoles;
use app\modules\shop\models\OrdersStatus;
use app\modules\shop\models\OrdersItemsSearch;
use app\modules\managment\models\ShopsStoresTimes;
use app\modules\common\models\UserShop;
use app\modules\common\models\UserParams;
use app\modules\managment\models\ShopsImages;

use app\modules\managment\models\Shops;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsImagesLinks;
use app\modules\catalog\models\GoodsSearch;
use app\modules\catalog\models\GoodsTypes;
use app\modules\managment\models\ShopsOptions;

use app\modules\shop\models\OrdersSearch;
use app\modules\shop\models\OrdersItems;

use app\modules\catalog\models\Tags;
use app\modules\catalog\models\TagsGroups;
use app\modules\catalog\models\TagsLinks;
use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsCountsSearch;
use app\modules\managment\models\ShopsStores;
use app\modules\catalog\models\GalleryShop;

use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

//use yii\web\UploadedFile;

/**
 * ShopsController implements the CRUD actions for Shops model.
 */
class ShopController extends BackendController
{
   // public $layout = 'shop-owner';
    public $layout = 'shop-control-page';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'gallery',
                            'settings-one-c',
                            'statistics',
                            'view',
                            'order',
                            'order-report',
                            'shop-params',
                            'index',
                            'shop-manager',
                            'goods',
                            'update-product',
                            'delete',
                            'order-report',
                            'product-list',
                            'edit-store'
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'conflictManager', 'shopOwner', 'categoryManager', 'HR'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
	            'Origin' => ['http://www.esalad.ru/'],
	            'Access-Control-Request-Method' => ['POST', 'PUT', 'GET'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
	            'Access-Control-Expose-Headers' => [],
        	],
            ],
        ];
    }
    
    public $enableCsrfValidation = false;

    public function actionOwnerOrderReport(){
        $shopList = Shops::find()->leftJoin('users_roles','users_roles.shop_id = shops.id')->where(['users_roles.user_id' => Yii::$app->user->identity->id])->all();
        if(empty($shopList)){
            throw new NotFoundHttpException(Yii::t('admin', 'У Вас не ни одного магазина!'));
        }

        $filter = new OrderFilter();
        if($filter->load(Yii::$app->request->post())){
            if(empty($filter->shops)){
                $filter->shops[] = $shopList[0]->id;
            }
        }
        $shopOrders = '';


        return $this->render('order-report', [
            'shopList' => $shopList,
            'shopOrders' => $shopOrders,
            'filter' => $filter,
        ]);
    }

    public function actionGallery()
    {
        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

        //$shopId = UserShop::getIdentityShop();

        if(!$shopId){
            return $this->redirect('https://Esalad.ru');
        }else{
            $this->view->registerCssFile('/shop/css/dropzone.min.css');
            $this->view->registerJsFile('/shop/js/dropzone.js');

            $model = new GalleryShop();
            $gallery = ShopsImages::find()->where(['shop_id' => $shopId])->andWhere(['status' => 1])->all();

            return $this->render('gallery.php', [
                'model' => $model,
                'gallery' => $gallery,
            ]);
        }
    }

    public function actionSettingsOneC()
    {
//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();

        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;


        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');

        }else{
            return $this->render('settings-1c', [
                'model' => [],
            ]);
        }
    }

    public function actionStatistics()
    {
        $shopList = Shops::find()->leftJoin('users_roles','users_roles.shop_id = shops.id')->where(['users_roles.user_id' => Yii::$app->user->identity->id])->all();
        if(empty($shopList)){
            throw new NotFoundHttpException(Yii::t('admin', 'У Вас не ни одного магазина!'));
        }

        $shopListIds = [];
        foreach ($shopList as $item) {
            $shopListIds[] = $item->id;
        }

//        $getUserRole = UserRoles::find()
//            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
//            ->andWhere(['status' => 1])
//            ->asArray()
//            ->one();
//
//        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();
//        if(!$shopId || !$userId){
//            return $this->redirect('https://extremeshop.ru');
//        }else{
            $statistic = $this->getStatisticShop();
            $payment = $this->getPaymentAccount();
            $manager = $this->getShopManager();

            $this->view->registerJsFile('https://www.gstatic.com/charts/loader.js');
            $this->view->registerCssFile('/css/shop.css');

            $visibleParams = [];
            $model = $shopList[0];

            $productStatusCount = Goods::find()
                ->select(['status','COUNT(*) AS cnt'])
                ->where(['IN','shop_id',$shopListIds])
                ->andWhere('goods.show > 0')
                ->groupBy(['status'])
                ->asArray()
                ->all();

            foreach($productStatusCount as $item){
                $visibleParams['allGoods'][$item['status']] = $item['cnt'];
            }

            $data = OrdersItems::find()
                ->joinWith(['orders','goods'])
                ->select([
                    'orders_items.*',
                    '(orders_items.price*orders_items.count) AS all_money',
                    'orders.date AS order_date',
                    'goods.name AS good_name',
                    'orders.status AS order_status',
                    'orders_items.count',
                    'get_status(orders_items.status_id) AS status_id',
                ])
                ->leftJoin('shops_stores','shops_stores.id = orders_items.store_id')
                ->leftJoin('shops','shops.id = shops_stores.shop_id')

                ->where(['IN','shops.id',$shopListIds])
                ->andWhere(['>','goods.show',0])
                ->andWhere(['orders.status'=>1])
                ->andWhere('orders.date > LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))')
                ->orderBy('orders_items.id')
                ->asArray()
                ->all();

            // всего продано
            $visibleParams['totalSales']['count'] = count($data);

            $currentMonth = date('m')*1;
            $monthSort = array();

//------------------------

            $date = new \DateTime();
            $date->sub(new \DateInterval('P11M'));
            $dataLine[] = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
            ];
            for($i = 10;$i>=0;$i--){
                $dataLine[] = [
                    'month' => $date->add(new \DateInterval('P1M'))->format('m'),
                    'year' => $date->format('Y'),
                ];
            }
//--------------------------

            for($j = $currentMonth; $j > 0; $j--){
                $monthSort[] = $j;
            }
            if(count($monthSort) < 12){
                for($j = 12; $j > $currentMonth; $j--){
                    $monthSort[] = $j;
                }
            }
            $monthSort = array_reverse($monthSort);

            for($i = 1;$i<=12;$i++){
                $visibleParams['month'][$i] = array();
                $visibleParams['month'][$i]['count'] = 0;
                $visibleParams['month'][$i]['sum'] = 0;
                $visibleParams['month'][$i]['month'] = $dataLine[$i-1]['month'];//(strlen($i+1) == 1)?'0' . ($i+1):($i+1);
                $visibleParams['month'][$i]['year'] = $dataLine[$i-1]['year'];//(($i+1) > $currentMonth)?(date('Y')*1 - 1):date('Y');
                $visibleParams['month'][$i]['days'] = [];
            }

            $visibleParams['totalSales']['status'] = [];
            foreach($data as $item){
                $item['status_id'] = ($item['status_id'])?$item['status_id']:'empty';

                if(!isset($visibleParams['totalSales']['status'][$item['status_id']])){
                    $visibleParams['totalSales']['status'][$item['status_id']] = [];
                    $visibleParams['totalSales']['status'][$item['status_id']]['count'] = 0;
                    $visibleParams['totalSales']['status'][$item['status_id']]['sum'] = 0;
                }

                // Общее количество проданных товаров
                $visibleParams['totalSales']['status'][$item['status_id']]['count']++;
                // Всего собрали денег
                $visibleParams['totalSales']['status'][$item['status_id']]['sum'] += $item['all_money'];

                $orderDay = date('d',strtotime($item['order_date']))*1;
                $orderMonth = date('m',strtotime($item['order_date']));
                $orderYear = date('Y',strtotime($item['order_date']));

                // Разбиваем по месяцам
                $monthValue = $visibleParams['month'];
                for($i = 1;$i<=12;$i++){
                    if(!isset($visibleParams['month'][$i]['days']) || empty($visibleParams['month'][$i]['days'])){
                        $countDayInMoth = cal_days_in_month(CAL_GREGORIAN, ($i), $orderYear);

                        for($k = 1;$k <= $countDayInMoth;$k++){
                            $monthValue[$i]['days'][$k] = ['count' => 0,'sum' => 0];
                        }
                    }

                    if($orderMonth == $i){
                        if(!isset($monthValue[$i]['status'][$item['status_id']])){
                            $monthValue[$i]['status'][$item['status_id']] = [];
                            $monthValue[$i]['status'][$item['status_id']]['count'] = 0;
                            $monthValue[$i]['status'][$item['status_id']]['sum'] = 0;
                        }

                        $monthValue[$i]['count']++;
                        $monthValue[$i]['sum'] += $item['all_money'];
                        $monthValue[$i]['month'] = $orderMonth;
                        $monthValue[$i]['year'] = $orderYear;
                        $monthValue[$i]['status'][$item['status_id']]['count']++;
                        $monthValue[$i]['status'][$item['status_id']]['sum'] += $item['all_money'];

                        if(!isset($monthValue[$i]['days'][$orderDay])){
                            $monthValue[$i]['days'][$orderDay] = [];
                            $monthValue[$i]['days'][$orderDay]['count'] = 0;
                            $monthValue[$i]['days'][$orderDay]['sum'] = 0;
                        }
                        $monthValue[$i]['days'][$orderDay]['count']++;
                        $monthValue[$i]['days'][$orderDay]['sum'] += $item['all_money'];
                    }
                }
                $visibleParams['month'] = $monthValue;
            }

            $graphList = array();
            $graphList['yearPrice'][] = [
                Yii::t('admin', 'Месяц'),
                Yii::t('admin', 'Доход'),
                // 'Принято на сумму',
                // 'Доставлено в пункт выдачи',
                // 'Выдано на сумму',
                // 'Не обработанных на сумму',
            ];
            $graphList['yearCount'][] = [
                Yii::t('admin', 'Месяц'),
                Yii::t('admin', 'Количество'),
                // 'Количество принято',
                // 'Количество в пункте выдачи',
                // 'Количество выдано',
                // 'Не обработанное количество',
            ];

            foreach($dataLine as $value){
                $graphList['yearPrice'][] = [
                    $value['year'] .' / '. $value['month'],
                    $visibleParams['month'][$value['month']*1]['sum'],
                ];
                $graphList['yearCount'][] = [
                    $value['year'] .' / '. $value['month'],
                    $visibleParams['month'][$value['month']*1]['count']
                ];

                $flag = true;
                foreach($visibleParams['month'][$value['month']*1]['days'] as $q => $dey){
                    if($flag){
                        $graphList['deyCount'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                            'День',
                            'Количество',
                        ];
                        $flag = false;
                    }
                    $graphList['deyCount'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                        $q,
                        $dey['count']
                    ];
                }
                $flag = true;
                foreach($visibleParams['month'][$value['month']*1]['days'] as $q => $dey){
                    if($flag){
                        $graphList['deyPrice'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                            'День',
                            'Доход'
                        ];
                        $flag = false;
                    }
                    $graphList['deyPrice'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                        $q,
                        $dey['sum']
                    ];
                }
            }

            $listValue['valueList'] = $graphList;
            $graphList = [];
            $graphList['valueList'] = $listValue['valueList'];
            $graphList['title'] = Yii::t('admin', 'Статистика продаж за год.');
            $graphList['subTitle'] = Yii::t('admin', 'Доход');

            $monthLine = [];
            //$monthSort = array_reverse($monthSort);
            foreach($monthSort as $key){
                $monthLine[] = date('M', strtotime('2016-'.$key.'-01') );
            }

            $monthLanguage = [
                'Apr' => Yii::t('admin', 'Апрель'),
                'Aug' => Yii::t('admin', 'Август'),
                'Dec' => Yii::t('admin', 'Декабрь'),
                'Feb' => Yii::t('admin', 'Февраль'),
                'Jan' => Yii::t('admin', 'Январь'),
                'Jul' => Yii::t('admin', 'Июль'),
                'Jun' => Yii::t('admin', 'Июнь'),
                'Mar' => Yii::t('admin', 'Март'),
                'May' => Yii::t('admin', 'Май'),
                'Nov' => Yii::t('admin', 'Ноябрь'),
                'Oct' => Yii::t('admin', 'Октябрь'),
                'Sep' => Yii::t('admin', 'Сентябрь'),
            ];

            $post = Yii::$app->request->get();
            if(isset($post['orders-provider-date-start'])){
                $post['orders-provider-date-start'] .= ' 00:00:01';
            }
            if(isset($post['orders-provider-date-stop'])){
                $post['orders-provider-date-stop'] .= ' 23:59:59';
            }
            if(empty($post) || isset($post['delFilter'])){
                $post = [];
            }

            $dateList = [
                'min' => strtotime(date('Y-m-d')) - 3600*24*365,
                'max' => strtotime(date('Y-m-d')),
            ];
            $filterOrders = [
                'orders-provider-date-start' => isset($post['orders-provider-date-start'])?str_replace(' 00:00:01','',$post['orders-provider-date-start']):'',
                'orders-provider-date-stop' => isset($post['orders-provider-date-stop'])?str_replace(' 23:59:59','',$post['orders-provider-date-stop']):'',
                'orders-provider-status' => isset($post['orders-provider-status'])?$post['orders-provider-status']:'',

            ];
            $searchModel = new OrdersSearch();
            $goodsReport = $searchModel->getShopReport($shopListIds,array_merge(Yii::$app->request->queryParams,$post));


            //$listValue['valueList'] = $graphList;
            $graphList = [];
            $graphList['valueList'] = $listValue['valueList'];
            $graphList['title'] = Yii::t('admin', 'Статистика продаж за год.');
            $graphList['subTitle'] = Yii::t('admin', 'Доход');


            //Zloradnij::print_arr($graphList);die();

            return $this->render('statistics.php', [
                'searchModel' => $searchModel,
                'model' => $model,
                'visibleParams' => json_encode($graphList),
                'monthLanguage' => $monthLanguage,
                'monthLine' => $monthLine,
                'statistic' => $statistic,
                'payment' => $payment,
                'manager' => $manager,
                'goodsReport' => $goodsReport,
                'filterOrders' => $filterOrders,
                'dateList' => $dateList,
            ]);
//        }
    }

    /**
     * Displays a single Shops model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionOrder()
    {
//        $productListDate = Goods::find()->where(['date_create' => '0000-00-00 00:00:00'])->all();
//        if(!$productListDate){
//
//        }else{
//            foreach($productListDate as $pp){
//                $pp->date_create = $pp->date_update;
//                if($pp->save()){
//
//                }else{
//                    Zloradnij::printArray($pp->errors);
//                }
//            }
//        }

        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;


//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();

        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
        }else{
            $post = Yii::$app->request->get();
            if(isset($post['orders-provider-date-start'])){
                $post['orders-provider-date-start'] .= ' 00:00:01';
            }
            if(isset($post['orders-provider-date-stop'])){
                $post['orders-provider-date-stop'] .= ' 23:59:59';
            }

            //print '<pre style="text-align: left">';print_r($post);print '</pre>';
            if(empty($post) || isset($post['delFilter'])){
                $post = [];
            }
            $ordersList = [];
            $statusList = OrdersStatus::find()/*->where(['type' => 2])->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->indexBy('id')->all();
            $searchModel = new OrdersItemsSearch();
            $dataProviderOriginal = $searchModel->searchOrdersForProvider(array_merge(Yii::$app->request->queryParams,$post));
            $totalCountAll = $searchModel->searchOrdersForProviderAll();
            $totalCountNoConfirm = $searchModel->searchOrdersForProviderNoConfirm();
            $totalCountOverdue = $searchModel->searchOrdersForProviderOverdue();

            $countList = [
                'allCountOnPage' => $dataProviderOriginal->getCount(),
                'allTotalCount' => $totalCountAll->getTotalCount(),
                'allTotalCountNoConfirm' => $totalCountNoConfirm->getTotalCount(),
                'allTotalCountOverdue' => $totalCountOverdue->getTotalCount(),
            ];

            $orderIds = [];
            $dataProvider = $dataProviderOriginal->getModels();
            foreach($dataProvider as $order){
                $orderIds[] = $order['id'];
            }

            $searchModel = new OrdersItemsSearch();
            $dataProvider = $searchModel->searchForProvider($orderIds);

            //$data = $dataProvider;
            $dateList = [
                'max' => strtotime(date('Y-m-d')),
                'min' => strtotime(date('Y-m-d')),
            ];


            foreach ($dataProvider as $key => $value) {
                if($dateList['max'] < strtotime($value['order_date'])){
                    $dateList['max'] = strtotime($value['order_date']);
                }
                if($dateList['min'] > strtotime($value['order_date'])){
                    $dateList['min'] = strtotime($value['order_date']);
                }
                /*
                                print '<pre style="text-align: left">';
                                print_r($value);
                                print '</pre>';
                */
                if(isset($value['store_id']) && !empty($value['store_id'])){
                    $value['delivery_address'] = $value['delivery_name'] . '<br/>' . $value['delivery_address'];
                }elseif(isset($value['address_id']) && !empty($value['address_id'])){
                    $value['delivery_address'] = $value['delivery_name'] . '<br/>' . $value['user_address'];
                }else{
                    if(isset($value['delivery_name']) && !empty($value['delivery_name'])){
                        $value['delivery_address'] = $value['delivery_name'] . $value['delivery_address'];
                    }
                }

                $image = GalleryShop::getImagePath($value['good_id'],$value['variation_id']);

                $value['image'] = '';
                if(!$image){
                    $value['image'] = '/no_iamge.jpg';
                }else{
                    if(is_array($image)){
                        $value['image'] = $image['min'];
                    }else{
                        $value['image'] = $image;
                    }
                }

                $value['producer_name'] = TagsLinks::find()
                    ->select(["tags.value as value"])
                    ->joinWith('goods_variations')
                    ->joinWith('tags')
                    ->where(['tags.group_id' => \Yii::$app->params['tagProducerNameId']])
                    ->andWhere(['goods_variations.id' => $value['variation_id']])
                    ->one();

                $ordersList[$value['order_id']][] = $value;
            }
            $filterOredrs = [
                'orders-provider-date-start' => isset($post['orders-provider-date-start'])?str_replace(' 00:00:01','',$post['orders-provider-date-start']):'',
                'orders-provider-date-stop' => isset($post['orders-provider-date-stop'])?str_replace(' 23:59:59','',$post['orders-provider-date-stop']):'',
                'orders-provider-status' => isset($post['orders-provider-status'])?$post['orders-provider-status']:'',
                'orders-provider-club' => isset($post['orders-provider-club'])?$post['orders-provider-club']:'',
                'orders-provider-confirm' => isset($post['orders-provider-confirm'])?$post['orders-provider-confirm']:'',
                'orders-provider-date-variant' => isset($post['orders-provider-date-variant'])?$post['orders-provider-date-variant']:'delivery',

            ];
            if($filterOredrs['orders-provider-confirm'] == 'noconfirm'){
                $filterOredrs['orders-provider-status'] = 'all';
            }

            return $this->render('order', [
                'searchModel' => $searchModel,
                'dataProvider' => $ordersList,
                'countList' => $countList,
                'statusList' => $statusList,
                'dataProviderOriginal' => $dataProviderOriginal,
                'dateList' => $dateList,
                'filterOredrs' => $filterOredrs,
                'shopId' => $shopId,
            ]);
        }
    }

    public function actionShopParams()
    {
        $this->view->registerCssFile('/css/shop.css');
        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();

        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
//            return $this->redirect(['/']);
        }else{
            $shop = Shops::findOne($shopId);
            $user = UserShop::findOne($userId);
            $manager = UserShop::findOne($shop->user_id);
            $userParams = UserParams::find()->where(['user_id' => $userId])->andWhere(['status' => 1])->indexBy('title')->all();
            $stores = ShopsStores::find()->where(['shop_id' => $shopId])->andWhere(['status' => 1])->andWhere(['show' => 1])->all();

            $storesTime = false;
            if($stores){
                foreach($stores as $store){
                    $storesTime[$store->id][] = ShopsStoresTimes::find()->where(['store_id' => $store->id])->andWhere(['status' => 1])->all();
                }
            }

            $shopParamsActive = [];
            $shopParams = [
                'shopName' => ['title' => Yii::t('admin', 'Название'),'value' => $shop->name, 'update' => 0],
                'shopFullName' => ['title' => Yii::t('admin', 'Юридическое название'),'value' => $shop->name_full, 'update' => 0],
                'shopRegistration' => ['title' => Yii::t('admin', 'Дата регистрации'),'value' => date('Y-m-d',strtotime($shop->registration)), 'update' => 0],
                'shopDescription' => ['title' => Yii::t('admin', 'Описание'),'value' => $shop->description, 'update' => 0],
            ];
            $shopUser = [
                'userName' => ['title' => Yii::t('admin', 'Имя'),'value' => $user->name, 'update' => 0],
                'userPhone' => ['title' => Yii::t('admin', 'Телефон'),'value' => $user->phone, 'update' => 1],
                'userEmail' => ['title' => 'Email', 'value' => $user->email, 'update' => 1],
            ];
            if($manager){
                $shopManager = [
                    'managerName' => ['title' => Yii::t('admin', 'Ваш менеджер'),'value' => $manager->name, 'update' => 0],
                    'managerPhone' => ['title' => Yii::t('admin', 'Телефон менеджера'),'value' => $manager->phone, 'update' => 0],
                    'managerEmail' => ['title' => Yii::t('admin', 'Email менеджера'),'value' => $manager->email, 'update' => 0],
                ];
            }else{
                $shopManager = [];
            }

            if(!empty(Yii::$app->params['methodNotification']))
                $shopParams['methodNotification'] = ['title' => Yii::t('admin', 'Способ оповещения'),'value' => Yii::$app->params['methodNotification'], 'update' => 1];

            $shopParams = array_merge($shopParams,$shopUser,$shopManager);

            $methodNotificationAtive = [];
            if(isset($userParams['methodNotification'])){
                foreach(Yii::$app->params['methodNotification'] as $bit => $method){
                    if($bit & $userParams['methodNotification']->value){
                        $methodNotificationAtive[$bit] = $bit;
                    }
                }
                $shopParamsActive['methodNotification'] = $methodNotificationAtive;
            }

            return $this->render('shop-params', [
                'shopParams' => $shopParams,
                'shopParamsActive' => $shopParamsActive,
                'stores' => $stores,
                'storesTime' => $storesTime,
            ]);
        }
    }

    public function actionIndex(){


    //    return $this->render('/shop/index');

//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();

            $statistic = $this->getStatisticShop();
            $payment = $this->getPaymentAccount();
            $manager = $this->getShopManager();

            if(!isset($statistic) || !$statistic || empty($statistic)){
                $statistic = [];
            }
            if(!isset($payment) || !$payment || empty($payment)){
                $payment = [];
            }
            if(!isset($manager) || !$manager || empty($manager)){
                $manager = [];
            }

            $this->view->registerJsFile('https://www.gstatic.com/charts/loader.js');
            $this->view->registerCssFile('/css/shop.css');

//            $userId = Yii::$app->user->identity ? \Yii::$app->user->identity->id : 'AAA';
//Zloradnij::print_arr(Yii::$app->user->identity->id);
//            die();
//            print_r($userId);


            $getUserRole = UserRoles::find()
                ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
                ->andWhere(['status' => 1])
                ->asArray()
                ->one();

            $shopId =$getUserRole['shop_id'];

            $visibleParams = [];
        //Zloradnij::print_arr(10013181);die();


            if(isset($shopId)){
                $model = $this->findModel($shopId);
            }else{
                return $this->redirect('/site/index?error=noshop');
            }

            $productStatusCount = Goods::find()
                ->select(['status','COUNT(*) AS cnt'])
                ->where('shop_id = '.$shopId)
                ->andWhere('goods.show > 0')
                ->groupBy(['status'])
                ->asArray()
                ->all();

            foreach($productStatusCount as $item){
                $visibleParams['allGoods'][$item['status']] = $item['cnt'];
            }

            $data = OrdersItems::find()
                ->joinWith(['orders','goods'])
                ->select([
                    'orders_items.*',
                    '(orders_items.price*orders_items.count) AS all_money',
                    'orders.date AS order_date',
                    'goods.name AS good_name',
                    'orders.status AS order_status',
                    'orders_items.count',
                    'get_status(orders_items.status_id) AS status_id',
                ])
                ->where(['goods.shop_id'=>[$shopId]])
                ->andWhere('goods.show > 0')
                ->andWhere(['orders.status'=>1])
                ->andWhere('orders.date > LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))')
                ->orderBy('orders_items.id')
                ->asArray()
                ->all();

            // всего продано
            $visibleParams['totalSales']['count'] = count($data);

            $currentMonth = date('m')*1;
            $monthSort = array();

//------------------------

            $date = new \DateTime();
            $date->sub(new \DateInterval('P11M'));
            $dataLine[] = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
            ];
            for($i = 10;$i>=0;$i--){
                $dataLine[] = [
                    'month' => $date->add(new \DateInterval('P1M'))->format('m'),
                    'year' => $date->format('Y'),
                ];
            }
            //print '<pre style="text-align:left;margin-top:100px;">';print_r($dataLine);print '</pre>';
//--------------------------

            for($j = $currentMonth; $j > 0; $j--){
                $monthSort[] = $j;
            }
            if(count($monthSort) < 12){
                for($j = 12; $j > $currentMonth; $j--){
                    $monthSort[] = $j;
                }
            }
            $monthSort = array_reverse($monthSort);

            for($i = 1;$i<=12;$i++){
                $visibleParams['month'][$i] = array();
                $visibleParams['month'][$i]['count'] = 0;
                $visibleParams['month'][$i]['sum'] = 0;
                $visibleParams['month'][$i]['month'] = $dataLine[$i-1]['month'];//(strlen($i+1) == 1)?'0' . ($i+1):($i+1);
                $visibleParams['month'][$i]['year'] = $dataLine[$i-1]['year'];//(($i+1) > $currentMonth)?(date('Y')*1 - 1):date('Y');
                $visibleParams['month'][$i]['days'] = [];
            }

            $visibleParams['totalSales']['status'] = [];
            foreach($data as $item){
                $item['status_id'] = ($item['status_id'])?$item['status_id']:'empty';

                if(!isset($visibleParams['totalSales']['status'][$item['status_id']])){
                    $visibleParams['totalSales']['status'][$item['status_id']] = [];
                    $visibleParams['totalSales']['status'][$item['status_id']]['count'] = 0;
                    $visibleParams['totalSales']['status'][$item['status_id']]['sum'] = 0;
                }

                // Общее количество проданных товаров
                $visibleParams['totalSales']['status'][$item['status_id']]['count']++;
                // Всего собрали денег
                $visibleParams['totalSales']['status'][$item['status_id']]['sum'] += $item['all_money'];

                $orderDay = date('d',strtotime($item['order_date']))*1;
                $orderMonth = date('m',strtotime($item['order_date']));
                $orderYear = date('Y',strtotime($item['order_date']));

                // Разбиваем по месяцам
                $monthValue = $visibleParams['month'];
                for($i = 1;$i<=12;$i++){
                    if(!isset($visibleParams['month'][$i]['days']) || empty($visibleParams['month'][$i]['days'])){
                        $countDayInMoth = cal_days_in_month(CAL_GREGORIAN, ($i), $orderYear);

                        for($k = 1;$k <= $countDayInMoth;$k++){
                            $monthValue[$i]['days'][$k] = ['count' => 0,'sum' => 0];
                        }
                    }

                    if($orderMonth == $i){
                        if(!isset($monthValue[$i]['status'][$item['status_id']])){
                            $monthValue[$i]['status'][$item['status_id']] = [];
                            $monthValue[$i]['status'][$item['status_id']]['count'] = 0;
                            $monthValue[$i]['status'][$item['status_id']]['sum'] = 0;
                        }

                        $monthValue[$i]['count']++;
                        $monthValue[$i]['sum'] += $item['all_money'];
                        $monthValue[$i]['month'] = $orderMonth;
                        $monthValue[$i]['year'] = $orderYear;
                        $monthValue[$i]['status'][$item['status_id']]['count']++;
                        $monthValue[$i]['status'][$item['status_id']]['sum'] += $item['all_money'];

                        if(!isset($monthValue[$i]['days'][$orderDay])){
                            $monthValue[$i]['days'][$orderDay] = [];
                            $monthValue[$i]['days'][$orderDay]['count'] = 0;
                            $monthValue[$i]['days'][$orderDay]['sum'] = 0;
                        }
                        $monthValue[$i]['days'][$orderDay]['count']++;
                        $monthValue[$i]['days'][$orderDay]['sum'] += $item['all_money'];
                    }
                }
                $visibleParams['month'] = $monthValue;
            }

            $graphList = array();
            $graphList['yearPrice'][] = [
                Yii::t('admin', 'Месяц'),
                Yii::t('admin', 'Доход'),
                // 'Принято на сумму',
                // 'Доставлено в пункт выдачи',
                // 'Выдано на сумму',
                // 'Не обработанных на сумму',
            ];
            $graphList['yearCount'][] = [
                Yii::t('admin', 'Месяц'),
                Yii::t('admin', 'Количество'),
                // 'Количество принято',
                // 'Количество в пункте выдачи',
                // 'Количество выдано',
                // 'Не обработанное количество',
            ];

            foreach($dataLine as $value){
                $graphList['yearPrice'][] = [
                    $value['year'] .' / '. $value['month'],
                    $visibleParams['month'][$value['month']*1]['sum'],
                ];
                $graphList['yearCount'][] = [
                    $value['year'] .' / '. $value['month'],
                    $visibleParams['month'][$value['month']*1]['count']
                ];

                $flag = true;
                foreach($visibleParams['month'][$value['month']*1]['days'] as $q => $dey){
                    if($flag){
                        $graphList['deyCount'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                            Yii::t('admin', 'День'),
                            Yii::t('admin', 'Количество'),
                        ];
                        $flag = false;
                    }
                    $graphList['deyCount'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                        $q,
                        $dey['count']
                    ];
                }
                $flag = true;
                foreach($visibleParams['month'][$value['month']*1]['days'] as $q => $dey){
                    if($flag){
                        $graphList['deyPrice'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                            Yii::t('admin', 'День'),
                            Yii::t('admin', 'Доход')
                        ];
                        $flag = false;
                    }
                    $graphList['deyPrice'][date('M', strtotime($value['year'].'-'.$value['month'].'-01') )][] = [
                        $q,
                        $dey['sum']
                    ];
                }
            }

            $listValue['valueList'] = $graphList;
            $graphList = [];
            $graphList['valueList'] = $listValue['valueList'];
            $graphList['title'] = Yii::t('admin', 'Статистика продаж за год.');
            $graphList['subTitle'] = Yii::t('admin', 'Доход');


       //     Zloradnij::print_arr($graphList);die();

            return $this->render('/shop/index', [
                'model' => $model,
                'visibleParams' => json_encode($graphList),
                'statistic' => $statistic,
                'payment' => $payment,
                'manager' => $manager,
            ]);
    }

    public function getShopManager()
    {

        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;


        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
//            return $this->redirect(['/']);
        }else{
            $shop = Shops::findOne($shopId);
            $manager = UserShop::findOne($shop->user_id);
            if(!$manager){
                $manager = [];
            }

            return $manager;
        }
    }

    public function getPaymentAccount()
    {
       // $shopId = UserShop::getIdentityShop();

        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

        $payment = [
            'title' => [
                'rentalPaymentDate' => Yii::t('admin', 'Дата оплаты аренды'),
                'rentPaidTo' => Yii::t('admin', 'Аренда оплачена до'),
                'UntilEndWork' => Yii::t('admin', 'До конца аренды осталось'),
            ],
            'value' => [
                'rentalPaymentDate' => '-',
                'rentPaidTo' => '-',
                'UntilEndWork' => '-',
            ],
        ];
        if($shopId){
            $shopParams = ShopsOptions::find()->where(['shop_id' => $shopId])->andWhere(['status' => 1])->orderBy('date_start DESC')->one();
            if($shopParams){
                $dateStart = date_create($shopParams->date_start);
                $dateStop = clone $dateStart;
                $dateCurrent = date_create();
                date_add($dateStop, date_interval_create_from_date_string('1 month'));

                $interval = date_diff($dateCurrent, $dateStop);
                $intervalValue = $interval->format('%R%a дней');
                if($interval->days <= 3){
                    $intervalValue = '<span class="bold label-danger" style="padding: 2px 10px;">'.$interval->format('%R%a дней').'</span>';
                }

                $payment['value'] = [
                    'rentalPaymentDate' => date_format($dateStart, 'Y-m-d'),
                    'rentPaidTo' => date_format($dateStop, 'Y-m-d'),
                    'UntilEndWork' => $intervalValue,
                ];
            }
            if(!isset($payment['value']['rentalPaymentDate']) || empty($payment['value']['rentalPaymentDate'])){
                $payment['value']['rentalPaymentDate'] = '-';
            }
            if(!isset($payment['value']['rentPaidTo']) || empty($payment['value']['rentPaidTo'])){
                $payment['value']['rentPaidTo'] = '-';
            }
            if(!isset($payment['value']['UntilEndWork']) || empty($payment['value']['UntilEndWork'])){
                $payment['value']['UntilEndWork'] = '-';
            }
        }
        return $payment;
    }

    public function getStatisticShop(){
//
//
//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();
        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
        }else{
            $shopParams = Shops::findOne($shopId);
            $statistic = [
                'title' => [
                    'all' => Yii::t('admin', 'Всего товаров'),
                    'accessCount' => Yii::t('admin', 'Доступно для продажи'),
                    'showcase' => Yii::t('admin', 'Выставлено товаров'),
                    'confirm' => Yii::t('admin', 'Одобрено товаров'),
                    'notConfirm' => Yii::t('admin', 'На проверке'),
                    'blocked' => Yii::t('admin', 'Не прошли проверку'),
                ],
                'value' => [
                    'all' => 0,
                    'accessCount' => isset($shopParams->count)?$shopParams->count:0,
                    'showcase' => 0,
                    'confirm' => 0,
                    'notConfirm' => 0,
                    'blocked' => 0,
                ],
            ];

            $model = new Goods();

            $productsConfirm = $model->find()
                ->select(['confirm','status','show','id'])
                ->where(['goods.shop_id' => $shopId])
                ->andWhere('goods.status >= 0')
                ->andWhere('goods.show > 0')
                ->asArray()
                ->all();

            if($productsConfirm){
                foreach($productsConfirm as $item){
                    $statistic['value']['all']++;
                    if($item['confirm'] == 0){
                        $statistic['value']['notConfirm']++;
                        if($item['status'] == 1){
                            $statistic['value']['showcase']++;
                        }
                    }
                    if($item['confirm'] < 0){
                        $statistic['value']['blocked']++;
                    }
                    if($item['confirm'] == 1){
                        $statistic['value']['confirm']++;
                        if($item['status'] == 1){
                            $statistic['value']['showcase']++;
                        }
                    }
                }
            }

            return $statistic;
        }
    }

    public function actionGoods()
    {
        $this->view->registerCssFile('/shop/css/shop.css');
//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();

        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
//            return $this->redirect(['/']);
        }else{
            $statistic = $this->getStatisticShop();

            $statusList = [
                -1 => Yii::t('admin', 'Заблокирован'),
                0 => Yii::t('admin', 'На модерации'),
                1 => Yii::t('admin', 'Промодерирован'),
            ];

            $model = new Goods();

            $productCount = $model->find()
                ->select(['COUNT(*) as cnt'])
                ->where(['shop_id' => $shopId])
                ->andWhere(['>=','status','0'])
                ->andWhere(['>','show','0'])
                ->all();

            $searchModel = new GoodsSearch();
            $dataProvider = $searchModel->searchForProvider(Yii::$app->request->queryParams);

            $productIds = [];
            $dataProviderModel = $dataProvider->getModels();
            foreach($dataProviderModel as $product){
                $productIds[] = $product->id;
            }

            $variants = GoodsVariations::find()
                ->select(['id','good_id'])
                ->where(['IN','good_id',$productIds])
                ->indexBy('id')
                ->groupBy('good_id')
                ->asArray()
                ->all();
            ;
            $variantIds = [];
            foreach($variants as $key => $variantId){
                $variantIds[] = $key;
            }

            $tagsListValue = TagsLinks::find()
                ->select('tags.*, tags_links.*')
                ->joinWith(['tags'])
                ->where(['IN','tags_links.variation_id',$variantIds])
                ->andWhere(['tags_links.status' => 1])
                ->all();


            $tagsListValueByGroup = [];
            foreach($tagsListValue as $item){
                if(in_array($item->tags->group_id,[1007,1008])){
                    $tagsListValueByGroup[$variants[$item->variation_id]['good_id']][$item->tags->group_id]/*[$item->tags->id]*/ = $item->tags->value;
                }
            }

            return $this->render('goods', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'statistic' => $statistic,
                'productCount' => $productCount,
                'statusList' => $statusList,
                'producers' => Tags::find()->where(['group_id' => 1008])->andWhere(['status' => 1])->indexBy('id')->orderBy('value')->all(),
                'tagsListValueByGroup' => $tagsListValueByGroup,
            ]);
        }
    }

    /**
     * Updates an existing Shops model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionUpdateProduct($id)
    {
        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;
//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();
        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
        }else{
            $this->view->registerCssFile('/shop/css/dropzone.min.css');

            $model = Goods::findOne($id);
            $modelVariant = GoodsVariations::find()
                ->select([
                    'id',
                    'code',
                    'full_name',
                    'name',
                    'description',
                    "get_tags(`id`) AS `tags_name`",
                    'price',
                    'comission',
                    'status',
                ])
                ->where(['good_id' => $id])
                ->all();
            //print '<pre style="display: none;text-align: left;">';print_r($modelVariant);print '</pre>';

            $modelVariantIds = [];
            foreach($modelVariant as $variant){
                $modelVariantIds[] = $variant->id;
            }

            $countVariation = GoodsCounts::find()->where(['IN','variation_id',$modelVariantIds])->andWhere(['status' => 1])->indexBy('variation_id')->asArray()->all();
            foreach($modelVariantIds as $ids){
                if(!isset($countVariation[$ids]) || empty($countVariation[$ids])){
                    $countVariation[$ids] = 0;
                }
            }
            //print '<pre>';print_r($countVariation);print '</pre>';

            $images = GoodsImagesLinks::find()->where(['IN','variation_id',$modelVariantIds])->all();
            $variantImages = [];
            foreach($images as $image){
                $variantImages[$image->variation_id][$image->image_id] = \Yii::$app->params['galleryPath'] . substr(md5($image->image_id), 0, 2) . '/' . $shopId.'_'.$image->image_id . '_min.jpg';
            }

            $tagsListValue = TagsLinks::find()
                ->select('tags.*, tags_links.*')
                ->joinWith(['tags'])
                ->where(['IN','tags_links.variation_id',$modelVariantIds])
                ->andWhere(['tags_links.status' => 1])
                ->all();

            $tagsListValueByGroup = [];
            foreach($tagsListValue as $item){
                $tagsListValueByGroup[$item->variation_id][$item->tags->group_id][$item->tags->id] = $item->tags->value;
            }

            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                $producerAll = isset($post['producer-all'])?$post['producer-all']:false;
                $countryAll = isset($post['country-all'])?$post['country-all']:false;
                $variantsList = Yii::$app->request->post();
                $variantsList = $variantsList['GoodsVariations'];
                if(!empty($variantsList)){
                    $connection = \Yii::$app->db;
                    $transaction = $connection->beginTransaction();

                    try {
                        if ($model->load(Yii::$app->request->post())){
                            $model->date_update = date('Y-m-d H:i:s');
                            if($model->save()){
                                $log = new Logs();
                                $log->time=date("Y-m-d H:i:s");
                                $log->user_id=$userId;
                                $log->action="PRODUCT UPDATE";
                                $log->shop_id=$shopId;
                                $log->sql=var_export($model, true);
                                $log->save();

                                Yii::info(
                                    date("Y-m-d H:i:s").
                                    " UserID: ".$userId.
                                    ", ProductID: ".$model->id.
                                    ", status: ".$model->status.
                                    ', Action: UPDATE PRODUCT',
                                    'Shop'
                                );
                                foreach($variantsList as $key => $variant) {
                                    if (isset($variant['price']) && $variant['price'] > 0) {
                                        if (isset($variant['id']) && !empty($variant['id'])) {
                                            $modelOldVariant = GoodsVariations::findOne($variant['id']);
                                        } else {
                                            $modelOldVariant = new GoodsVariations();
                                        }

                                        if ($modelOldVariant->load(['GoodsVariations' => $variant])) {
                                            $modelOldVariant->good_id = $model->id;
                                            //$modelOldVariant->status = (isset($modelOldVariant->count) && $modelOldVariant->count > 0) ? $modelOldVariant->status : 0;
                                            if ($modelOldVariant->save()) {
                                                //file_put_contents('/var/www/shopTest/shop/framework/controllers/r.txt',var_export($modelOldVariant));

                                                $goodsCount = GoodsCounts::find()->where(['good_id' => $model->id])->andWhere(['variation_id' => $modelOldVariant->id])->one();

                                                if (!$goodsCount) {
                                                    $goodsCount = new GoodsCounts();
                                                    $goodsCount->good_id = $model->id;
                                                    $goodsCount->variation_id = $modelOldVariant->id;
                                                }

                                                if (!isset($goodsCount->store_id) && $goodsCount->store_id > 0) {

                                                } else {
                                                    $currentStore = ShopsStores::find()->where(['shop_id' => $shopId])->one();
                                                    if ($currentStore) {
                                                        $currentStore = $currentStore->id;
                                                    } else {
                                                        $currentStore = 0;
                                                    }
                                                    $goodsCount->store_id = $currentStore;
                                                }

                                                $variantCount = 1;
                                                if (isset($modelOldVariant->count) && !empty($modelOldVariant->count)) {
                                                    $variantCount = $modelOldVariant->count;
                                                }
                                                $goodsCount->count = $variantCount;//(isset($modelOldVariant->count) && $modelOldVariant->count > 0)?$modelOldVariant->count:1;
                                                $goodsCount->status = (isset($modelOldVariant->count) && $modelOldVariant->count > 0) ? 1 : 0;
                                                //$modelOldVariant->status = (isset($modelOldVariant->count) && $modelOldVariant->count > 0) ? $modelOldVariant->status : 0;
                                                $goodsCount->save();
                                                $log = new Logs();
                                                $log->time=date("Y-m-d H:i:s");
                                                $log->user_id=$userId;
                                                $log->action="GOODS COUNT CREATE/UPDATE";
                                                $log->shop_id=$shopId;
                                                $log->sql=var_export($goodsCount, true);
                                                $log->save();
                                                Yii::info(
                                                    date("Y-m-d H:i:s").
                                                    " UserID: ".$userId.
                                                    ", VariationID: ".$modelOldVariant->id.
                                                    ", price: ".$modelOldVariant->price.
                                                    ", status: ".$modelOldVariant->status.
                                                    ", count: ".$modelOldVariant->count.
                                                    ', Action: UPDATE VARIATION',
                                                    'Shop'
                                                );
                                            }

                                            if ($modelOldVariant->save()) {
                                                $log = new Logs();
                                                $log->time=date("Y-m-d H:i:s");
                                                $log->user_id=$userId;
                                                $log->action="GOODS VARIATION CREATE/UPDATE";
                                                $log->shop_id=$shopId;
                                                $log->sql=var_export($modelOldVariant, true);
                                                $log->save();

                                                $notDeleteTagsLinks = TagsLinks::find()->where(['variation_id' => $modelOldVariant->id])->all();
                                                if ($notDeleteTagsLinks) {
                                                    foreach ($notDeleteTagsLinks as $itemTagLinks) {
                                                        $notDeleteTags = Tags::find()->where(['id' => $itemTagLinks->tag_id])->one();
                                                        if (!in_array($notDeleteTags->group_id, Yii::$app->params['breadcrumbsTagId'])) {
                                                            $itemTagLinks->delete();
                                                        }
                                                    }
                                                }

                                                if (!$producerAll) {

                                                } else {
                                                    $tagsLinks = new TagsLinks();
                                                    $tagsLinks->variation_id = $modelOldVariant->id;
                                                    $tagsLinks->tag_id = $producerAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if (!$countryAll) {

                                                } else {
                                                    $tagsLinks = new TagsLinks();
                                                    $tagsLinks->variation_id = $modelOldVariant->id;
                                                    $tagsLinks->tag_id = $countryAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if (!empty($_POST['variations_add'][$key]['tags'])) {
                                                    foreach ($_POST['variations_add'][$key]['tags'] as $tagCode => $tagValue) {
                                                        if (!in_array($tagCode, Yii::$app->params['breadcrumbsTagId'])) {
                                                            $tagsLinks = new TagsLinks();
                                                            $tagsLinks->variation_id = $modelOldVariant->id;
                                                            $tagsLinks->tag_id = $tagCode;
                                                            $tagsLinks->status = 1;

                                                            $tagsLinks->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $transaction->commit();
                                return $this->redirect(['/shop/goods']);
                            }
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                }
            }

            //print_r($variantImages);
            return $this->render('update-product', [
                'model' => $model,
                'typeProduct' => GoodsTypes::find()->all(),
                //'producers' => Producers::find()->orderBy('name')->all(),
                'producers' => Tags::find()->where(['group_id' => 1008])->andWhere(['status' => 1])->orderBy('value')->all(),
                //'country' => Countries::find()->orderBy('name')->all(),
                'country' => Tags::find()->where(['group_id' => 1007])->andWhere(['status' => 1])->orderBy('value')->all(),
                'modelVariant' => $modelVariant,
                'tagsList' => TagsGroups::find()->where(['type' => 1])->andWhere(['status' => 1])->orderBy('position')->all(),
                'tagsListValue' => $tagsListValueByGroup,
                'tags' => new Tags(),
                'variantImages' => $variantImages,
                'countVariation' => $countVariation,
            ]);
        }
    }

    /**
     * Creates a new Shops model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*
        public function actionCreateProduct()
        {
            $shopId = UserShop::getIdentityShop();
            $userId = UserShop::getIdentityUser();
            if(!$shopId || !$userId){
                return $this->redirect('https://extremeshop.ru');
    //            return $this->redirect(['/']);
            }else{
                $model = new Goods();

                if(Yii::$app->request->post()){
                    $post = Yii::$app->request->post();
                    $producerAll = isset($post['producer-all'])?$post['producer-all']:false;
                    $countryAll = isset($post['country-all'])?$post['country-all']:false;
                    $variantsList = Yii::$app->request->post();
                    $variantsList = $variantsList['GoodsVariations'];
                    if(!empty($variantsList)){
                        $connection = \Yii::$app->db;
                        $transaction = $connection->beginTransaction();

                        try {
                            if ($model->load(Yii::$app->request->post())){
                                $model->shop_id = $shopId;
                                $model->status = 0;
                                $model->date_create = $model->date_update = date('Y-m-d H:i:s');
                                $model->count_pack = 1;

                                if($model->save()){
                                    $log = new Logs();
                                    $log->time=date("Y-m-d H:i:s");
                                    $log->user_id=$userId;
                                    $log->action="GOODS CREATE";
                                    $log->shop_id=$shopId;
                                    $log->sql=var_export($model, true);
                                    $log->save();

                                    foreach($variantsList as $key => $variant){
                                        $modelVariant = new GoodsVariations();
                                        $loadVariant = [];
                                        foreach($variant as $keyCode => $valueVariant){
                                            $loadVariant['GoodsVariations'][$keyCode] = $valueVariant;
                                        }
                                        if ($modelVariant->load($loadVariant)){
                                            $modelVariant->good_id = $model->id;

                                            //$modelVariant->status = 0;

                                            if($modelVariant->save()){
                                                $log = new Logs();
                                                $log->time=date("Y-m-d H:i:s");
                                                $log->user_id=$userId;
                                                $log->action="GOODS VARIATION CREATE";
                                                $log->shop_id=$shopId;
                                                $log->sql=var_export($modelVariant, true);
                                                $log->save();

                                                $shopStore = ShopsStores::find()->where(['shop_id' => $shopId])->one();
                                                $goodsCount = new GoodsCounts();
                                                $goodsCount->good_id = $model->id;
                                                $goodsCount->variation_id = $modelVariant->id;
                                                $goodsCount->store_id = $shopStore->id;
                                                $goodsCount->count = (isset($modelVariant->count) && $modelVariant->count > 0)?$modelVariant->count:1;
                                                //$goodsCount->status = (isset($modelVariant->count) && $modelVariant->count > 0)?1:0;
                                                $goodsCount->save();
                                                $log = new Logs();
                                                $log->time=date("Y-m-d H:i:s");
                                                $log->user_id=$userId;
                                                $log->action="GOODS COUNT CREATE";
                                                $log->shop_id=$shopId;
                                                $log->sql=var_export($goodsCount, true);
                                                $log->save();

                                                if(!$producerAll){

                                                }else{
                                                    $tagsLinks = new TagsLinks();
                                                    $tagsLinks->variation_id = $modelVariant->id;
                                                    $tagsLinks->tag_id = $producerAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }

                                                if(!$countryAll){

                                                }else{
                                                    $tagsLinks = new TagsLinks();
                                                    $tagsLinks->variation_id = $modelVariant->id;
                                                    $tagsLinks->tag_id = $countryAll;
                                                    $tagsLinks->status = 1;
                                                    $tagsLinks->save();
                                                }


                                                if(!empty($_POST['variations_add'][$key]['tags'])){
                                                    foreach($_POST['variations_add'][$key]['tags'] as $tagCode => $tagValue){
                                                        $tagsLinks = new TagsLinks();
                                                        $tagsLinks->variation_id = $modelVariant->id;
                                                        $tagsLinks->tag_id = $tagCode;
                                                        $tagsLinks->status = 1;

                                                        $tagsLinks->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $transaction->commit();
                                    return $this->redirect(['/shop/goods']);
                                }
                            }
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                            throw $e;
                        }
                    }
                }

                return $this->render('create-product', [
                    'model' => $model,
                    'typeProduct' => GoodsTypes::find()->all(),
                    //'producers' => Producers::find()->orderBy('name')->all(),
                    'producers' => Tags::find()->where(['group_id' => 1008])->andWhere(['status' => 1])->orderBy('value')->all(),
                    //'country' => Countries::find()->orderBy('name')->all(),
                    'country' => Tags::find()->where(['group_id' => 1007])->andWhere(['status' => 1])->orderBy('value')->all(),
                    'modelVariant' => new GoodsVariations(),
                    'tagsList' => TagsGroups::find()->where(['type' => 1])->andWhere(['status' => 1])->orderBy('position')->all(),
                    'tagsListValue' => [],
                    'tags' => new Tags(),
                    'variantImages' => [],
                    'countVariation' => [],
                ]);
            }
        }
    */
    /**
     * Deletes an existing Shops model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

//        $shopId = UserShop::getIdentityShop();
//        $userId = UserShop::getIdentityUser();
        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
//            return $this->redirect(['/']);
        }else{
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();

            try {
                $product = Goods::findOne($id);
                $product->show = 0;
                $product->save();

                $log = new Logs();
                $log->time=date("Y-m-d H:i:s");
                $log->user_id=$userId;
                $log->action="GOODS DELETE";
                $log->shop_id=$shopId;
                $log->sql=var_export($product, true);
                $log->save();
                /*
                GoodsCounts::deleteAll('good_id = "' . $id . '"');
                GoodsImagesLinks::deleteAll('good_id = "' . $id . '"');

                $modelVariant = GoodsVariations::find()->where(['good_id' => $id])->all();
                foreach($modelVariant as $variant){
                    TagsLinks::deleteAll('variation_id = "' . $variant->id . '"');
                }
                GoodsVariations::deleteAll('good_id = "' . $id . '"');
                */
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            return $this->redirect('http://www.esalad.ru/shop/goods');

            //return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Shops model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shops the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shops::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionOrderReport(){
    

	Header("Access-Control-Allow-Origin: *");
        $getUserRole = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            ->asArray()
            ->one();

        $shopId =$getUserRole['shop_id'];

        $userId =  \Yii::$app->user->identity->id;

        if(!$shopId || !$userId){
            return $this->redirect('https://Esalad.ru');
        }else{
            $post = Yii::$app->request->get();

            if(isset($post['orders-provider-date-start'])){
                $post['orders-provider-date-start']=date("Y-m-d", strtotime($post['orders-provider-date-start']));
                $post['orders-provider-date-start'] .= ' 00:00:00';
            }
            else{
                $post['orders-provider-date-start']=date("Y-m-d 00:00:00", time());
            }
            if(isset($post['orders-provider-date-stop'])){
                $post['orders-provider-date-stop']=date("Y-m-d", strtotime($post['orders-provider-date-stop']));
                $post['orders-provider-date-stop'] .= ' 23:59:59';
            }
            else{
                $post['orders-provider-date-stop']=date("Y-m-d 23:59:59", strtotime('1 day'));
            }

            if(empty($post) || isset($post['delFilter'])){
                $post = [];
            }

            $searchModel = new OrdersItemsSearch();
            $dataProvider = $searchModel->searchOrderReport(array_merge(Yii::$app->request->queryParams,$post));
            $dataProviderOrderIds = [];

            foreach($dataProvider->getModels() as $orderItem){
                $dataProviderOrderIds[$orderItem['orderId']] = 1;
            }

            $filterOredrs = [
                'orders-provider-date-start' => isset($post['orders-provider-date-start'])?str_replace(' 00:00:00','',$post['orders-provider-date-start']):'',
                'orders-provider-date-stop' => isset($post['orders-provider-date-stop'])?str_replace(' 23:59:59','',$post['orders-provider-date-stop']):'',
                'orders-provider-status' => isset($post['orders-provider-status'])?$post['orders-provider-status']:'',
                'orders-provider-club' => isset($post['orders-provider-club'])?$post['orders-provider-club']:'',
                'orders-provider-confirm' => isset($post['orders-provider-confirm'])?$post['orders-provider-confirm']:'',
                'orders-provider-date-variant' => isset($post['orders-provider-date-variant'])?$post['orders-provider-date-variant']:'delivery',
            ];

            if($filterOredrs['orders-provider-confirm'] == 'noconfirm'){
                $filterOredrs['orders-provider-status'] = 'all';
            }

            $statusList = OrdersStatus::find()->andWhere(['status' => 1])->indexBy('id')->all();
            $dateList = [];

//            Zloradnij::print_arr($dataProvider);
//            Zloradnij::print_arr($searchModel);
//            die();

            return $this->render('order-report', [
                'searchModel'           => $searchModel,
                'dataProvider'          => $dataProvider,
                'statusList'            => $statusList,
                'dateList'              => $dateList,
                'filterOredrs'          => $filterOredrs,
                'shopId'                => $shopId,
                'dataProviderOrderIds'  => $dataProviderOrderIds,
            ]);
        }
    }

    public function actionProductList(){
        $shopList = Shops::find()->leftJoin('users_roles','users_roles.shop_id = shops.id')->where(['users_roles.user_id' => Yii::$app->user->identity->id])->all();
        if(empty($shopList)){
            throw new NotFoundHttpException(Yii::t('admin', 'У Вас не ни одного магазина!'));
        }

        $shopListIds = [];
        foreach ($shopList as $item) {
            $shopListIds[] = $item->id;
        }

        return $this->render('product-list',[
            'dataProvider' => new ActiveDataProvider(['query' => Shops::find()->leftJoin('users_roles','users_roles.shop_id = shops.id')->where(['users_roles.user_id' => Yii::$app->user->identity->id])]),
        ]);
    }

    public function actionEditStore(){


        if(Yii::$app->request->isPost){
            $request = Yii::$app->request->post();
            $editableKey = $request['editableKey'];
            $editableIndex = $request['editableIndex'];
            $count = (int)$request['GoodsCounts'][$editableIndex]['count'];
            if($count<0){
                $answer = ['message'=>"Количество должно быть положительным"];
                return json_encode($answer);
            }
            if($count > 999){
                $answer = ['output' => '','message'=>"Введено большое количество"];
                return json_encode($answer);
            }
            $good = GoodsCounts::find()->where(['id'=>$editableKey])->One();
            $good->count = $count;
            if($good->save()){
                $answer = ['output' => $count,'message'=>""];
                return json_encode($answer);
            }else{
                $answer = ['message'=>"Ошибка"];
                return json_encode($answer);
            }
        }
        $arShops = [];
        $arShops = UserRoles::find()
            ->where(['user_id' => Yii::$app->user->identity ? \Yii::$app->user->identity->id : 0])
            ->andWhere(['status' => 1])
            //->asArray()
            ->All();
            
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $shopID = $request['shop_id'];
            $shop = Shops::find()->where(['id'=>$shopID,'edit_count_good'=>1])->One();
            $arStores = ShopsStores::find()->select('id')->where(['shop_id'=>$shopID])->All();
            $tmp = [];
            foreach ($arStores as $store){
                $tmp[] = $store->id;
            }
            $arStores = $tmp;
            $dataProvider = new ActiveDataProvider(['query'=> GoodsCounts::find()->where(['IN','store_id',$arStores])->andWhere(['status'=>1])->andWhere(['>','variation_id',0])]);
            $searchModel = new GoodsCountsSearch();
            return $this->render('edit-store',['dataProvider'=>$dataProvider,'searchModel'=>$searchModel,'shop'=>$shop,'arShops'=>$arShops,'shop_id'=>$shopID]);
        }else{
            return $this->render('edit-store',['arShops'=>$arShops]);
        }


    }
}