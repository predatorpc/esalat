<?php

namespace app\components\shopManagment;

use app\modules\catalog\models\Category;
use app\modules\catalog\models\CategoryLinks;
use app\modules\catalog\models\Goods;
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
use yii\bootstrap\Modal;
use Yii;
class WidgetReportOrderItemStatus extends Widget{
    public $item;
    public $order;

    public function init(){
        parent::init();
        if($this->item === null || $this->order === null){
            return false;
        }
    }

    public function run(){
        $order = Orders::findOne($this->order);
        $orderItem = OrdersItems::findOne($this->item);?>
        <div
            class="col-xs-3 col-sm-2 col-md-2 col-lg-2 order-item-status bold btn"
            data-order-id="<?= $order->id?>"
            data-order-group-id="<?= $orderItem->orderGroup->id?>"
            data-order-item-id="<?= $orderItem->id?>"
        >
            <?php
            if($orderItem->statusTitle){?>
                <?php if($orderItem->status_id == 1002){?>
                    <?php Modal::begin([
                    'header' => '<h2>Варианты для замены</h2>',
                         'toggleButton' => [
                             'tag' => 'button',
                             'class' => 'btn btn-info',
                             'label' => 'Заменить',
                             'id' => 'btn-change_'.$orderItem->id,
                         ]
                         ]);?>
                    <?php
                        $category_id = CategoryLinks::find()->where(['product_id'=> $orderItem->good_id])->One();
                        $category = Category::find()->where(['id'=>$category_id->category_id])->andWhere(['active'=>1])->One();
                        if(!empty($category)){
                            $firstLevel = Category::find()->select('id')->where(['parent_id'=>$category->parent->id])->andWhere(['active'=>1])->asArray()->All();
                            if(!empty($firstLevel)){
                                $tmp =[];
                                foreach ($firstLevel as $key => $value){
                                    $tmp[] = $value['id'];
                                }
                                $firstLevel = $tmp;
        //                        $secondLevel = Category::find()->select('id')->where(['IN','parent_id',$firstLevel])->andWhere(['active'=>1])->asArray()->All();
        //                        $tmp =[];
        //                        foreach ($secondLevel as $key => $value){
        //                            $tmp[] = $value['id'];
        //                        }
        //                        $secondLevel = $tmp;
                                $arGoodIDs = CategoryLinks::find()->select('product_id')->where(['IN','category_id',$firstLevel])->asArray()->All();
                                $tmp =[];
                                foreach ($arGoodIDs as $key => $value){
                                    $tmp[] = $value['product_id'];
                                }
                                $arGoodIDs = $tmp;
                                $arVariations = GoodsVariations::find()->where(['in','good_id',$arGoodIDs])->andWhere(['status'=>1])->andWhere(['<=','price',$orderItem->price])->All();
                                foreach ($arVariations as $variation){
                                    if($variation->product->status == 1){
                                        $price = $variation->getPriceValue();
                                        $maxSum = $orderItem->price * $orderItem->count;
                                        $maxCount = floor ($maxSum / $price);
                                        // Лучше так не делать =);
                                        /*
                                        echo '<pre id="'.$variation->id.'" class="change">
                                                <input type="hidden" value="'.$price.'"  name="price">
                                                <input name="order-data" type="hidden" data-order-id="'.$this->order.'" data-order-group-id="'.$orderItem->orderGroup->id.'"
                                                data-order-item-id="'.$orderItem->id.'" data-new-variation-id="'.$variation->id.'" data-order-item-status="'.$orderItem->status.'">
                                                <img style="width: 50px;" src="http://www.esalad.ru'.Goods::findProductImage($variation->product->id,'min').'">'.
                                                $variation->product->name.' <span class="sum">'.$price.'</span> руб.
                                                <input type="number"  min="1" max="'.$maxCount.'" name="number" value="1" id="'.$variation->id.'" style="width: 50px; float: right;">
                                                <a class="btn  change-good">Заменить</a></pre>';*/
                                        ?>
                                        <!--Список замены товаров-->
                                        <div id="<?=$variation->id?>" class="goods-list-modal col-sm-6 col-xs-12">
                                            <div class="content-goods">
                                                <input type="hidden" class="price" value="<?=$price?>"  name="price">
                                                <input name="order-data" type="hidden"
                                                       data-order-id="<?=$this->order?>"
                                                       data-order-group-id="<?=$orderItem->orderGroup->id?>"
                                                       data-order-item-id="<?=$orderItem->id?>"
                                                       data-new-variation-id="<?=$variation->id?>"
                                                       data-order-item-status="<?=$orderItem->status?>">
                                                <div class="images col-xs-2">
                                                    <img style="width: 50px;position: relative;left: -13px;" src="http://www.esalad.ru<?=Goods::findProductImage($variation->product->id,'min')?>">
                                                </div>
                                                <div class="block col-xs-10">
                                                    <div class="name" title="<?=$variation->product->name?>"><?=$variation->product->name?></div>
                                                    <div class="sum">Цена <span><?=$price?></span> руб.</div>
                                                    <div class="row">
                                                        <div class="col-xs-6"><input type="number"  min="1" max="<?=$maxCount?>" name="number" value="1" id="<?=$variation->id?>" class="form-control input-sm js-count-num"></div>
                                                        <div class="col-xs-6"><button type="button" class="btn btn-primary .btn-xs js-change-good">Заменить</button></div>
                                                    </div>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div> <!--/Список замены товаров-->
                                        <?php
                                    }
                                }
                            }
                        }

                    ?>
                         <div class="clear"></div>
                         <?php Modal::end();?>
                    <span id="<?=$order->id;?>_<?=$orderItem->id;?>" class="btn residence-status <?= \Yii::$app->params['buttonStatusColorClass'][$orderItem->statusTitle->id]?>"><?= $orderItem->statusTitle->name?></span>
                <?php }else{?>
                    <span id="<?=$order->id;?>_<?=$orderItem->id;?>" class="btn residence-status <?= \Yii::$app->params['buttonStatusColorClass'][$orderItem->statusTitle->id]?>"><?= $orderItem->statusTitle->name?></span>
                <?php }?>

                <?php
            }else{
                if($order->status == 1 && $orderItem->status == 1 ) {?>
                    <span class="btn residence-status btn-danger" id="<?=$order->id;?>_<?=$orderItem->id;?>">Не обработан</span><?php
                }elseif($order->status == 0) {?>
                    <span class="btn residence-status btn-danger">Брошенный заказ</span><?php
                }elseif($order->status == 1 && $orderItem->status == 0) {?>
                    <span class="btn residence-status btn-danger">Отменён</span>
                    <span class="btn residence-status btn-danger">заменить</span>
                    <?php
                }
            }
            ?>
            Статус - <span style="font-size: 11px;"><?= ($orderItem->status ? Yii::t('admin', 'Принят') : Yii::t('admin', 'Отменен'))?></span>
        </div><?php
    }
}
