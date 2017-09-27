<?php
namespace app\controllers;

use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use Yii;



use app\modules\common\controllers\BackendController;

use app\modules\analytics\models\Analytics;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AnalyticsController extends BackendController
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
                            'create',
                            'update',
                            'view',
                            'index',
                        ],
                        'allow' => true,
                        'roles' => ['GodMode', 'conflictManager' , 'callcenterOperator', 'categoryManager', 'HR'],
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

    public function actionIndex(){
        $request = Yii::$app->request->get();
        if(!empty($request['dateFrom']) && !empty($request['dateTo'])) {
            //Даты из запроса
            $dateFrom = $request['dateFrom'];
            $dateTo = $request['dateTo'];
            //Данные хранящиеся в базе
            $dataFromDB = Analytics::find()->where(['>=', 'date', $dateFrom])->andWhere(['<=', 'date', $dateTo])->asArray()->All();
            $dataFromDB = ArrayHelper::index($dataFromDB,'date');
            //Даты на год раньше
            $date = new \DateTime($dateFrom);
            $date->modify('-1 year');
            $pastDateFrom = $date->format('Y-m-d');
            $date = new \DateTime($dateTo);
            $date->modify('-1 year');
            $pastdateTo = $date->format('Y-m-d');
            $curOrders = Orders::find()->where(['>=', 'date', $dateFrom.' 00:00:00'])->andWhere(['<=', 'date', $dateTo.' 23:59:59'])->andWhere(['status' => 1])->orderBy('date')->All();
            $arOrderSum = [];
            $arTypes = [];//Типы товаров
            $arTypeDB =[];//Типы товаров по датм для загрузки в базу
            $arWeight = []; //Вес заказов по дням
            $arPrice = []; //Цены заказаов по датам c типом 1003
            foreach ($curOrders as $curOrder) {
                $dateOrder = date('Y-m-d', strtotime((string)$curOrder->date));
                //Если запись с датой заказа есть в базе, берем данные из базы
                if(isset($dataFromDB[$dateOrder])){
                    continue;
                }
                if (!isset($arOrderSum[$dateOrder])) {
                    $arOrderSum[$dateOrder] = 0;
                }
                if (!isset($arTypeDB[$dateOrder])) {
                    $arTypeDB[$dateOrder] = [];
                }
                if (!isset($arWeight[$dateOrder])) {
                    $arWeight[$dateOrder]['count'] = 0;
                    $arWeight[$dateOrder]['weight'] = 0;
                }
                if (!isset($arPrice[$dateOrder])) {
                    $arPrice[$dateOrder]['weight'] = 0;
                    $arPrice[$dateOrder]['price'] = 0;
                }
                $arWeight[$dateOrder]['count']++;
                $ordersGroups = OrdersGroups::find()->where(['order_id'=>$curOrder->id,'status'=> 1])->All();
                foreach ($ordersGroups as $ordersGroup) {
                    $ordersGroupSum = 0;
                    $orderItems = OrdersItems::find()->where(['status'=> 1, 'order_group_id'=>$ordersGroup->id])->All();
                    foreach ($orderItems as $ordersItem) {
                        $type_id = $ordersItem->good->type_id;
                        $weight = 0;
                        if(isset($ordersItem->goodsVariations->weight->value)){
                            $weight = $ordersItem->goodsVariations->weight->value;
                        }
                        $price= $ordersItem->price;
                        $discount = $ordersItem->discount;
                        $count = $ordersItem->count;
                        if(!isset( $arTypes[$type_id])){
                            $arTypes[$type_id] = 0;
                        }
                        if(!isset($arTypeDB[$dateOrder][$type_id])){
                            $arTypeDB[$dateOrder][$type_id] = 0;
                        }
                        $arTypes[$type_id] = $arTypes[$type_id] + ($price - $discount) * $count;//$ordersItem->count;
                        $arTypeDB[$dateOrder][$type_id] =  $arTypeDB[$dateOrder][$type_id] + ($price - $discount) * $count;//$ordersItem->count;

                        $ordersItemSum = ($price - $discount) * $count;
                        $ordersGroupSum = $ordersGroupSum + $ordersItemSum;

                        $arWeight[$dateOrder]['weight'] = $arWeight[$dateOrder]['weight'] + $weight;

                        if( $type_id == 1003){
                            $arPrice[$dateOrder]['weight'] = $arPrice[$dateOrder]['weight'] + $count * $weight;
                            $arPrice[$dateOrder]['price'] = $arPrice[$dateOrder]['price'] + (($price-$discount)*$count);
                        }
                    }
                    $arOrderSum[$dateOrder] = $arOrderSum[$dateOrder] + $ordersGroupSum;


                }
            }

            //Перебираем готовый массив и если в нем есть новые записи, записываем в базу
            foreach ($arOrderSum as $key => $value){
                if(!isset($dataFromDB[$key])){
                    $model = new Analytics();
                    $model->date = $key;
                    $model->sum =  $value;
                    $model->items = json_encode($arTypeDB[$key]);
                    $model->weight = json_encode($arWeight[$key]);
                    $model->price = json_encode($arPrice[$key]);
                    if($model->save() === false){
                        echo '<pre>'.print_r($model->getErrors(),1).'</pre>';
                    }
                }
            }

            $dataFromDB = Analytics::find()->where(['>=', 'date', $dateFrom])->andWhere(['<=', 'date', $dateTo])->orderBy('date')->asArray()->All();
            $dataFromDB = ArrayHelper::index($dataFromDB,'date');
            $arTypes = [];
            foreach ($dataFromDB as $key => $value){
                $arOrderSum[$key] = $value['sum'];
                $arWeightDB = json_decode($value['weight'],true);
                if($arWeightDB['count']>0){
                    $arWeight[$key] = $arWeightDB['weight']/$arWeightDB['count']/1000;
                }else{
                    $arWeight[$key] = 0;
                }

                $arPriceDB = json_decode($value['price'],true);
                if($arPriceDB['weight']>0){
                    $arPrice[$key] = $arPriceDB['price']/($arPriceDB['weight']/1000);
                }else{
                    $arPrice[$key] = 0;
                }
                $itemsDB = json_decode($value['items'],true);
                foreach ($itemsDB as $type => $count){
                    if(!isset($arTypes[$type])){
                        $arTypes[$type] = 0;
                    }
                    $arTypes[$type] = $arTypes[$type] +$count;
                }

            }

            //Расчет данных из прошлого, в базу не пишутся, а надо бы
            $arOldOrderSum = [];
            $oldOrders = Orders::find()->where(['>=', 'date', $pastDateFrom.' 00:00:00'])->andWhere(['<=', 'date', $pastdateTo.' 23:59:59'])->andWhere(['status' => 1])->orderBy('date')->All();
            foreach ($oldOrders as $oldOrder) {
                $date = new \DateTime((string)$oldOrder->date);
                $date->modify('+1 year');
                $dateOrder = $date->format('Y-m-d');
                if (!isset($arOldOrderSum[$dateOrder])) {
                    $arOldOrderSum[$dateOrder] = 0;
                }
                $ordersGroup = OrdersGroups::find()->where(['status'=>1, 'order_id'=>$oldOrder->id])->All();
                foreach ($ordersGroup as $ordersGroup) {
                    $ordersGroupSum = 0;
                    $ordersItems = OrdersItems::find()->where(['status'=>1,'order_group_id'=>$ordersGroup->id])->All();
                    foreach ($ordersItems as $ordersItem) {
                        $ordersItemSum = ($ordersItem->price -  $ordersItem->discount) * $ordersItem->count;
                        $ordersGroupSum = $ordersGroupSum + $ordersItemSum;
                    }
                    $arOldOrderSum[$dateOrder] = $arOldOrderSum[$dateOrder] + $ordersGroupSum;
                }
            }
            return $this->render('index', ['curOrders' => $arOrderSum,'oldOrders'=> $arOldOrderSum,'arTypes'=> $arTypes, 'dateFrom'=>$dateFrom,'dateTo'=>$dateTo, 'arWeight' => $arWeight, 'arPrice' => $arPrice]);
        }else{
            return $this->render('index',['dateFrom' =>'','dateTo'=>'']);
        }
    }
}
?>

