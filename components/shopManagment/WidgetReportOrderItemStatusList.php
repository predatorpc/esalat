<?php

namespace app\components\shopManagment;

use app\modules\catalog\models\GoodsCounts;
use app\modules\catalog\models\GoodsVariations;
use app\modules\managment\models\Shops;
use app\modules\catalog\models\TagsGroups;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersItems;
use app\modules\shop\models\OrdersItemsStatus;
use app\modules\shop\models\OrdersStatus;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UserShop;

class WidgetReportOrderItemStatusList extends Widget{
    public $item;
    public $order;

    public function init(){
        parent::init();
        if($this->item === null || $this->order === null){
            return false;
        }
    }

    public function run(){?>
        <div class="shop-list-fixed-position">
        <div class="content-shop">
        <?php
            if(\Yii::$app->user->can('clubAdmin')){
                $where = [
                    'status'=>1,
                    'id'=>[1006,1007]
                ];
            }
            else{
                $where = ['status'=>1];
            }
            $statusList = OrdersStatus::find()->where($where)->select(['name','id'])->indexBy('id')->asArray()->all();

            $order = Orders::findOne($this->order);
            $orderItem = OrdersItems::findOne($this->item);

            $ordersGoList = OrdersItemsStatus::find()->where(['order_item_id' => $orderItem->id])->orderBy('id DESC')->all();

            if(!$ordersGoList){

            }else{
                foreach ($ordersGoList as $go) {?>
                    <div class="old-transaction"><?= date('Y-m-d H:i',strtotime($go['date']))?> -<?php
                        if($go->statusTitle){
                            print $go->statusTitle->name;
                        }else{
                            if($order->status == 1 && $orderItem->status == 1 ) {?>
                                <span class=" btn-danger">Не обработан</span><?php
                            }elseif($order->status == 0) {?>
                                <span class=" btn-danger">Брошенный заказ</span><?php
                            }elseif($order->status == 1 && $orderItem->status == 0) {?>
                                <span class=" btn-danger">Отменён</span><?php
                            }
                        }?> (<?= $go->user->name?>)
                    </div><?php
                }
            }?>

            <hr /><div class="old-transaction">ИЗМЕНИТЬ СТАТУС:</div><hr /><?php

            foreach ($statusList as $id => $status) {
                if($orderItem->status_id != $id){?>
                    <div class="change-status" data-new-status-id="<?= $id?>"><?= $status['name']?></div><?php
                }
            }
            if($order->status == 1 && $orderItem->status == 1 ) {

            }else{?>
                <div class="change-status" data-new-status-id="notJob">Не обработан</div><?php
            }

            if(($order->status == 1 && $orderItem->status == 0) || (\Yii::$app->user->can('clubAdmin'))) {

            }
            else{?>
                <div class="change-status" data-new-status-id="revers">Отменён</div><?php
            }?>
            <hr />
            <div class="text-center"><span class="btn btn-success">Закрыть</span></div>
        </div>
        </div>
        <?php
    }
}
