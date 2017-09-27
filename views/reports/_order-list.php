<?php
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use app\modules\catalog\models\Category;
use yii\helpers\Url;
use kartik\widgets\DateTimePicker;
use app\modules\common\models\ModFunctions;


?>
    <div class="block order-element" data-order-id="<?= $model->id?>">
        <?php
          $filter = Yii::$app->request->get();
          $filter = !empty($filter['OwnerOrderFilter'])?$filter['OwnerOrderFilter']:false;
          $order_groups = OrdersGroups::find()->where(['order_id' => $model->id])->All();
         // echo '<input type="hidden" value="'.count($order_groups).'">';
          foreach ($order_groups as $order_group){

          $address = \app\modules\common\models\Address::find()->where(['id'=> $order_group->address_id])->One();
          $total = 0;
          foreach ($order_group->ordersItems as $ordersItem){
              $total = $total + ($ordersItem->price - $ordersItem->bonus - $ordersItem->discount) * $ordersItem->count;
          }

        ?>
        <div class="order-info col-xs-12 js-goods-order <?=count($order_groups) >= 2 ? 'warning':' '?>  <?= (!empty($order_group->delivery_date) && date('d.m.Y',strtotime($order_group->delivery_date)) == date('d.m.Y') ? 'new' : '') ?>">
            <div class="info-user"><b class="order"># <?= $model->id?></b>, <?= Yii::t('admin', 'Сумма заказа') ?>:<b> <?=$total?> р.</b> <?= date('d.m.Y, H:i',strtotime($model->date))?>. <?= Yii::t('admin', 'Статус') ?> - <b><?= ($model->status ? Yii::t('admin', 'Принято') : Yii::t('admin', 'Отменен'))?></b></div>
            <div class="glyphicon glyphicon-chevron-down"></div>
            <div class="clear"></div>
        </div>
        <div class="col-xs-12 panel-user">
           <div class="col-md-5">
             <div><b class="grey"><?= Yii::t('admin', 'Покупатель') ?>:</b><a target="_blank" href="/user/view?id=<?= $model->user->id?>"><strong><?= $model->user->name?></strong></a>, <?= $model->user->phone?><?= !empty($model->user->staff) ? ' <span class="label-com label-success">'.Yii::$app->params['typeOfStaff'][$model->user->typeof].'</span>' : ''?><span title="Есть претензия?" class="button-res text-warning glyphicon glyphicon-question-sign <?=$model->negative_review == 1 ? 'text-dander' : ''?> " onclick="modal_admin('<?=$model->id?>')"></span><a target="_blank" class="dashed" style="margin-left: 5px" href="/support/sms-send?user_id=<?= $model->user->id?>">Отправить СМС</a> </div>
             <div><b class="grey"><?= Yii::t('admin', 'Промо код') ?>:</b><?= (!empty($model->promoCode) && isset($model->promoCode->code))? $model->promoCode->code . ', <strong>' .(!empty($model->promoCode->user->name)? '<a target="_blank" href="/shop-management/promo-code-statistic?CodesSearch%5BdateStart%5D=2017-04-02&CodesSearch%5BdateStop%5D=2017-05-02&CodesSearch%5Bclub%5D=0&CodesSearch%5Btypeof%5D=0&CodesSearch%5Bcode%5D='.$model->promoCode->code.'&CodesSearch%5Bwocode%5D=1">'.$model->promoCode->user->name.'</a>':'').'</strong>': 'Нет'?></div>
             <div><b class="grey"> <?= Yii::t('admin', 'Комментарий') ?>:</b><?= $model->comments ? $model->comments : 'Нет'?></div>
           </div>
           <div class="col-md-3">
             <div><b class="grey"> <?= Yii::t('admin', 'Адрес доставки') ?>:</b><?= (isset($address->street)) ? $address->street : '';?>, <?= (isset($address->house)) ? $address->house : '';?>, кв. <?= (isset($address->room)) ? $address->room : '';?></div>
               <div><b class="grey"> <?= Yii::t('admin', 'Дата достаки') ?>:</b> <span class="desktop-hidden"><?=date('d.m.Y, H:i',strtotime($order_group->delivery_date));?></span>
                   <div class="time">
                      <div class="mobile-hidden">
                           <?php
                           if(OrdersItems::find()->where(['order_group_id'=>$order_group->id, 'status' => 1, 'status_id' => NULL])->count() > 0 || Yii::$app->user->can('GodMode')){
                               echo  DateTimePicker::widget([
                                       'name' => 'order_date_'.$order_group->id,
                                       'value' => date('d.m.Y, H:i',strtotime($order_group->delivery_date)),
                                       'removeButton' => false,
                                       'pickerButton' => ['icon' => 'time'],
                                       'pluginOptions' => [
                                           'autoclose' => true,
                                           'format' => 'dd.mm.yyyy, hh:ii'
                                       ],
                                       'options' => ['class' => 'form-control input-sm']
                                   ]).'<button  data-order-id="'.$model->id.'" data-order-group-id="'.$order_group->id.'" class="btn btn-success btn-sm changeOrderTime">Применить</button>';

                           }else{
                               echo date('d.m.Y, H:i',strtotime($order_group->delivery_date));
                           }?>
                      </div>

                     <div class="clear"></div>
                   </div>
               </div>
           </div>
            <div class="col-md-3">
                <div><b class="grey"> <?= Yii::t('admin', 'Сумма заказа') ?>:</b><?=$total?> р.</div>
                <div><b class="grey"> <?= Yii::t('admin', 'Сумма доставки') ?>:</b><?= $order_group->delivery_price;?> р.</div>
            </div>
            <div class="col-md-1">
                <?php
                   $count = OrdersItems::find()->where(['order_group_id'=>$order_group->id, 'status' => 1, 'store_id' => '10000196'])->andWhere('status_id is NULL')->count();
                  ?>

            <?php   if($count>0){ ?>
                <a class="btn btn-success goodsMetro mobile-hidden" id="<?= $model->id?>" >Принять товары метро</a>
            <?php }?>
            </div>
            <div class="clear"></div>
            <?php if($order_group->delivery_price > 0): ?>
                <div class="deliveries-group mobile-hidden">
                    <span class="delivery-cancel " title="Возврат средств покупателю за доставку" onclick="order_delivery_cancel(' <?=$order_group->id?>');">отмена доставки</span>
                    <span class="delivery-double hidden" title="Двойная доставка (списать средства с покупателя и начислить курьеру)" onclick="order_delivery_double('<?=$order_group->id?>');">двойная доставка</span>
                </div>
            <?php endif;?>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div class="test goods-item hidden table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr style="background:#E6E6FA">
                    <th><?= Yii::t('admin', 'Товар') ?></th>
                    <th><?= Yii::t('admin', 'Цена входная') ?></th>
                    <th><?= Yii::t('admin', 'Наценка') ?></th>
                    <th><?= Yii::t('admin', 'Цена выходная') ?></th>
                    <th><?= Yii::t('admin', 'Скидка / шт.') ?></th>
                    <th><?= Yii::t('admin', 'Кол-во') ?></th>
                    <th><?= Yii::t('admin', 'Бонусы') ?></th>
                    <th><?= Yii::t('admin', 'Сумма') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php (new \app\modules\shop\models\Orders())->getOrdersItemsQuery();
                $ordersItemsQuery = $model->getOrdersItemsQuery();
                $ordersItemsQuery->where(['order_group_id'=>$order_group->id]);
                if(isset($filter['status_id']) && !empty($filter['status_id'])){
                    if($filter['status_id'] > 1000){
                        $ordersItemsQuery->andWhere(['orders_items.status_id' => intval($filter['status_id'])]);
                        $ordersItemsQuery->andWhere(['orders_items.status' => 1]);
                    }
                    if($filter['status_id'] == 1008){
                        $ordersItemsQuery->andWhere(['orders_items.status' => 0]);
                    }

                    if($filter['status_id'] == '-1'){
                        $ordersItemsQuery->andWhere(['orders_items.status' => 0]);
                    }

                    if($filter['status_id'] == 'NO'){
                        $ordersItemsQuery->andWhere(['orders_items.status' => 1]);
                        $ordersItemsQuery->andWhere([
                            'OR',
                            ['!=','orders_items.status_id',1007],
                            ['IS','orders_items.status_id',NULL]
                        ]);
                    }
                    if($filter['status_id'] == 999){
                        $ordersItemsQuery->andWhere(['orders_items.status' => 1]);
                        $ordersItemsQuery->andWhere(['IS','orders_items.status_id',NULL]);
                    }
                    if($filter['status_id'] == 0){
                        $ordersItemsQuery->andWhere(['orders_items.status' => 0]);
                        $ordersItemsQuery->andWhere([
                            'OR',
                            ['!=','orders_items.status_id',1008],
                            ['IS','orders_items.status_id',NULL]
                        ]);
                    }
                }
                if(isset($filter['shops']) && !empty($filter['shops'])){
                    $ordersItemsQuery->leftJoin('shops_stores','shops_stores.id = orders_items.store_id')
                        ->leftJoin('shops','shops.id = shops_stores.shop_id')
                        ->andWhere(['IN','shops.id',$filter['shops']]);
                }
                if(isset($filter['category']) && !empty($filter['category'])){
                    $arCategory[] = $filter['category'];
                    $arCategory = Category::getChildrenCategory($filter['category'],$arCategory);
                    $ordersItemsQuery->leftJoin('category_links','category_links.product_id = orders_items.good_id')
                        ->andWhere(['IN','category_links.category_id',$arCategory]);
                }
                if(isset($filter['productType']) && !empty($filter['productType'])){
                    $ordersItemsQuery->leftJoin('goods','goods.id = orders_items.good_id')
                        ->andWhere(['goods.type_id'=>$filter['productType']]);
                }
                if(isset($filter['good_id']) && !empty($filter['good_id'])){
                    $ordersItemsQuery->andWhere(['orders_items.good_id' => $filter['good_id']]);
                }
                //echo '<pre>'.print_r($ordersItemsQuery,1).'</pre>';
                foreach ($ordersItemsQuery->all() as $orderItem) {
                    $visible = false;
                    if(!empty(Yii::$app->request->post()['OrdersItems']['status_id'])){
                        $params = Yii::$app->request->post();
                        if($params['OrdersItems']['status_id'] != '999' && $orderItem->status_id == $params['OrdersItems']['status_id']){
                            $visible = true;
                        }
                        elseif($params['OrdersItems']['status_id'] == '999' && empty($orderItem->status_id)){
                            $visible = true;
                        }
                        else{

                        }
                    }else{
                        $visible = true;
                    }

                    if($visible){
                        ?>
                        <tr class="order-item-<?=$orderItem->id;?>" count="<?=$orderItem->count;?>">
                            <td class="max name" data-label="<?= Yii::t('admin', 'Товар') ?>" width="30%" rowspan="">
                                <div class="order-item-image" onclick="return good_edit('<?=$orderItem->good_id?>');">
                                    <img style="width:100%" src="http://www.esalad.ru<?=\app\modules\catalog\models\Goods::findProductImage($orderItem->good_id,'min');?>" />
                                </div>
                                <div class="order-item-product">
                                    <div class="grey">
                                        <?= $orderItem->shops_stores ? $orderItem->shops_stores->shopNameStringTitle . ' ('.$orderItem->shops_stores->addressStringTitle.')' : ''?>
                                    </div>
                                    <div class="bold">
                                        <a href="<?=Url::to(['product/update','id'=>$orderItem->good->id])?>"><?= $orderItem->good->name?></a>
                                    </div>
                                    <div class="grey variants">
                                        <?= $orderItem->goodsVariations ? $orderItem->goodsVariations->titleWithProperties : ''?>
                                    </div>
                                    <?php if($orderItem->status == 1 && $orderItem->status_id != 1001){?>
                                        <span class="cancel" style="cursor: pointer;" title="Возврат средств за товар" onclick="order_item_cancel_now(<?=$orderItem->id;?>, <?=$order_group->id;?>,<?=$model->id;?>)">отмена</span>
                                    <?php }?>
                                    <div class="clear"></div>
                                    <div class="status">
                                        <?= \app\components\shopManagment\WidgetReportOrderItemStatus::widget([
                                            'item' => $orderItem->id,
                                            'order' => $model->id
                                        ]);?>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                            </td>
                            <td data-label="<?= Yii::t('admin', 'Цена входная') ?>">
                                <?= number_format($orderItem->price - $orderItem->comission,2,',',' ');?></td>
                            <td data-label="<?= Yii::t('admin', 'Наценка') ?>">
                                <?= $orderItem->goodsVariations ? $orderItem->comission : Yii::t('admin', '0 руб.') ?> /
                                <?= $orderItem->goodsVariations ? round($orderItem->comission/(($orderItem->price-$orderItem->comission)/100),2) : 0?> %
                            </td >
                            <td data-label="<?= Yii::t('admin', 'Цена выходная') ?>"><?= \app\modules\common\models\ModFunctions::money($orderItem->price)?></td>
                            <td data-label="<?= Yii::t('admin', 'Скидка / шт.') ?>"><?= \app\modules\common\models\ModFunctions::money($orderItem->discount)?></td>
                            <td data-label="<?= Yii::t('admin', 'Кол-во') ?>" class="count order-item-<?=$orderItem->id;?>-count"><?= $orderItem->count?> шт.</td>
                            <td data-label="<?= Yii::t('admin', 'Бонусы') ?>"><?= $orderItem->bonus?> β.</td>
                            <td data-label="<?= Yii::t('admin', 'Сумма') ?>"><?= ($orderItem->price - $orderItem->bonus - $orderItem->discount) * $orderItem->count?></td>
                        </tr>
                        <!--
                        <tr >
                            <td colspan="8" data-label="">

                            </td>
                        </tr>-->
                        <?php
                    }
                }?>
                </tbody>
            </table>
        </div>
        <?php }?>
    </div>
    <div class="clear"></div>
