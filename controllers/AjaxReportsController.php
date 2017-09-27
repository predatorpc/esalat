<?php

//last version 10072016

namespace app\controllers;

use app\components\shopManagment\WidgetReportOrderClientList;
use app\components\shopManagment\WidgetReportOrderFilterClient;
use app\components\shopManagment\WidgetReportOrderFilterShop;
use app\components\shopManagment\WidgetReportOrderItemStatus;
use app\components\shopManagment\WidgetReportOrderItemStatusList;
use app\components\shopManagment\WidgetReportOrderShopList;
use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsVariations;
use app\modules\coders\models\Payment;
use app\modules\coders\models\ShopPayment;
use app\modules\common\controllers\FrontController;
use app\modules\common\models\Address;
use app\modules\common\models\User;
use app\modules\common\models\UsersPays;
use app\modules\common\models\Zloradnij;
use app\modules\managment\models\Shops;
use app\modules\managment\models\ShopsStores;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use app\modules\shop\models\OrdersItemsStatus;
use app\modules\common\models\Messages;
use Yii;
use yii\helpers\ArrayHelper;

class AjaxReportsController extends FrontController
{
    public function actionGetShop(){
        $shopName = $_POST['shopName'];
        if(!empty($shopName)){
            //$shopList = ArrayHelper::map(Shops::find()->where(['LIKE','name',$shopName])->all(),'id','name');
            $shopList = Shops::find()->where(['LIKE','name',$shopName])->all();
            if(!empty($shopList)){
                print WidgetReportOrderShopList::widget([
                    'shopList' => $shopList,
                ]);
            }
        }
    }

    public function actionAddShop(){
        $shopId = $_POST['shopId'];
        if(!empty($shopId)){
            $shop = Shops::findOne($shopId);
            if(!empty($shop)){
                print WidgetReportOrderFilterShop::widget([
                    'shop' => $shop,
                    'i' => $_POST['i'],
                ]);
            }
        }
    }

    public function actionRemoveShop(){

    }
    //--------------------
    public function actionGetClient(){
        $clientName = $_POST['clientName'];
        if(!empty($clientName)){
            //$shopList = ArrayHelper::map(Shops::find()->where(['LIKE','name',$shopName])->all(),'id','name');
            $clientList = User::find()->where(['LIKE','name',$clientName])->all();
            if(!empty($clientList)){
                print  WidgetReportOrderClientList::widget([
                    'clientList' => $clientList,
                ]);
            }
        }
    }

    public function actionAddClient(){
        $clientId = $_POST['clientId'];
        if(!empty($clientId)){
            $client = User::findOne($clientId);
            if(!empty($client)){
                print WidgetReportOrderFilterClient::widget([
                    'client' => $client,
                    'i' => $_POST['i'],
                ]);
            }
        }
    }
    //--------------------
    public function actionGetOrderItemData(){
        $item = $_POST;

        print WidgetReportOrderItemStatusList::widget([
            'item' => $item['itemId'],
            'order' => $item['orderId']
        ]);
    }

    public function actionChangeOrderItemStatus(){
        $item = $_POST;

        $orderItem = OrdersItems::findOne(intval($item['itemId']));
        if(!$orderItem){

        }else{
            if($item['status'] == 'revers'){
                $orderItem->status == 0;
            }else{
                $orderItem->status_id = intval($item['status']);
            }
            if($orderItem->save()){

            }
        }

        print WidgetReportOrderItemStatus::widget([
            'item' => $item['itemId'],
            'order' => $item['orderId']
        ]);
    }

    // Статус негатива;
    public function actionNegativeStatus()
    {
        // Пост данные негагив;
        if(Yii::$app->request->post('negative')) {
            $order_id = Yii::$app->request->post('order_id');
            $negative_status = Yii::$app->request->post('negative_status');
            $comments = Yii::$app->request->post('comments');
            // Проверка полей;
            if(!$comments) return 'Ошибка';

            // Обновляем поле negative_review 1-0;
            if($order = Orders::findOne(intval($order_id))) {
                $order->negative_review = $negative_status;
                $order->comments_call_center = $comments;
                $order->save(false);
                // Обновляем поле compliment 1-0;
                if($user = User::findOne($order->user_id)) {
                    $user->compliment = $negative_status;
                    $user->save(false);
                }
                // Если совпадает номер заказа обновляем поле active 1-0;
                if($messages = Messages::findOne(['user_id'=>$order->user_id,'order'=>$order->id])) {
                    $messages->active = $negative_status;
                    $messages->save(false);
                }
                return  'order_id- '.$order->id.' user_id- '.$user->id.' messages_id- '.(!empty($messages)? $messages->id : 'нет');
            }else {
                return false;
            }


        }
    }

    public function actionMetro($order_id){
        $updateGoods = [];
        $order_group = OrdersGroups::find()->where(['order_id' => $order_id])->One();
        $order_items = OrdersItems::find()->where(['order_group_id'=>$order_group->id, 'status' => 1, 'store_id' => '10000196'])->All();
        foreach ($order_items as $order_item){
            $order_item->status_id = '1001';
            if($order_item->save()){
                $updateGoods[] = $order_item->id;
            }

        }
        echo json_encode($updateGoods);
    }


}





