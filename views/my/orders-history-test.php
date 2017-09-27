<?php

use app\modules\common\models\ModFunctions;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $order \app\modules\shop\models\Orders */

$this->title = Yii::t('app','История заказов');
$this->params['breadcrumbs'][] = $this->title;
$weightAll=0;

/*
 *  Внимание!!! если поменял функ-я или переменна, то тут тоже надо менять /my/orders-pdf  и  \app\components\shopMobile\WOrdersHistory
 * Controller actionOrdersPdf;
 */

?>
<div class="content">

    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-12">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <!--История заказака -->
            <?php if(!empty(Yii::$app->params['mobile'])):?>
                <?= \app\components\shopMobile\WOrdersHistory::widget(['orders' => $orders]); ?>
            <?php else: ?>
            <!--История заказака -->
            <div class="my-orders my-list ">
                <h1><?=$this->title?></h1>
                <?php $i = 1; ?>
                <?php if(!empty($orders)):?>
                    <?php foreach($orders as $key => $order):?>
                        <?php $tableId = 'key'.$key;?>
                        <table cellpadding="0" cellspacing="0" border="0" id="key<?=$key?>" data-order="<?=$order->id?>" >
                            <tr class=" res">
                                <th colspan="4">
                                    <div class="code open" >
                                        <a href="/" onclick="orders_open('<?=$key?>'); return false;" ><b>#<?= $order->id?></b> — <?=ModFunctions::datetime($order->date);?></a>
                                         <span class="total"><?= \Yii::t('app','Итог')?> : <?= ModFunctions::money($order->money)?></span>
                                        <?php
                                                echo Html::a('Распечатать', ['/my/orders-pdf?id='.$order->id], [
                                                    'class'=>'ver_pdf',
                                                    'target'=>'_blank',
                                                    'data-toggle'=>'tooltip',
                                                    'data-order'=> $order->id,
                                                    'title'=>'Распечатать'
                                                ]);
                                        ?>
                                    </div>
                                </th>
                                <th class="title groups"><?=Yii::t('app','Цена')?> </th>
                                <th class="title groups"><?=Yii::t('app','Скидка')?></th>
                                <th class="title groups"><?=Yii::t('app','Количество')?></th>
                                <th class="title groups"><?=Yii::t('app','Состояние заказа')?></th>
                                <th class="title groups"><?=Yii::t('app','Вес')?></th>
                                <th class="title groups"><?=Yii::t('app','Итого')?></th>
                                <th class="title groups"><?=Yii::t('app','Статус')?></th>
                            </tr><?php

                            if(!empty($order->ordersGroups[0]->type_id)){
                                $deliveryGroup = new \app\modules\basket\models\DeliveryGroup();
                                $deliveryGroup->setProducts($order->ordersItems);
                                $deliveryGroup->setDeliveryId($order->ordersGroups[0]->delivery_id);
                                $deliveryGroup->setProductDeliveryGroup();

                                if(!empty($deliveryGroup->productDeliveryGroup)){
                                    $index = 0;
                                    foreach ($deliveryGroup->productDeliveryGroup as $key => $group) {
                                        $ordersItemsStatusList = 0;
                                        $indexStart = $index;
                                        $weightAll=0;
                                        foreach ($order->ordersGroups as $ordersGroup) {
                                            if(in_array($ordersGroup->type_id,$group)){
                                                foreach ($ordersGroup->ordersItems as $n => $ordersItem) {?>
                                                    <tr class="i groups">
                                                    <td class="name" colspan="4"><span class="num"><?= $n + 1;?>.</span><?= $ordersItem->good->name?><br /><span class="tag"><?= !empty($ordersItem->goodsVariations) ? $ordersItem->goodsVariations->titleWithPropertiesForCatalog : ''?></span></td>
                                                    <td class="money">
                                                        <?php if($ordersItem->bonus > 0){?>
                                                            <span class="bonus"><?=ModFunctions::bonus($ordersItem->bonus)?></span><?php
                                                        } ?>
                                                        <?php if(($ordersItem->price - $ordersItem->bonus) > 0){?>
                                                            <?=ModFunctions::money($ordersItem->price)?><?php
                                                        }?>
                                                    </td>
                                                    <td class="money">
                                                        <?php if($ordersItem->discount > 0): ?>—<?php else:?><?=ModFunctions::money($ordersItem->discount)?><?php endif;?>
                                                    </td>
                                                    <td class="count"><?=$ordersItem->count?> шт.</td>
                                                    <td class="status_name"><?php
                                                        if(!empty($ordersItem->statusTitle)){
                                                            print $ordersItem->statusTitle->name;
                                                        }elseif(empty($ordersItem->status_id) && $ordersItem->status == 1){?>
                                                            <?=\Yii::t('app','Обрабатывается')?><?php
                                                        }elseif($ordersItem->status == 0){?>
                                                            <?=\Yii::t('app','отменён')?><?php
                                                        }?>
                                                    </td>
                                                        <td class="money">
                                                            <?php
                                                            if(!empty($ordersItem->goodsVariations)){
                                                                $weight = $ordersItem->goodsVariations->weight;
                                                                if(!empty($weight)){
                                                                    $weightAll = $weightAll +($weight->value*$ordersItem->count);
                                                                    echo $weight->value*$ordersItem->count .' г';
                                                                }
                                                                else{
                                                                    echo '0 г';
                                                                }
                                                            }
                                                            else{
                                                                echo '0 г';
                                                            }
                                                            ?>
                                                        </td>
                                                    <td class="money">
                                                        <?php if($ordersItem->bonus > 0): ?>
                                                            <span class="bonus">
                                                                <?=ModFunctions::bonus($ordersItem->bonus * $ordersItem->count)?>
                                                                <?php if(($ordersItem->price - $ordersItem->bonus) > 0){
                                                                    print ' / ' .  ModFunctions::money(($ordersItem->price - $ordersItem->bonus - $ordersItem->discount) * $ordersItem->count);
                                                                }?>
                                                            </span>
                                                        <?php else: ?>
                                                            <?=ModFunctions::money(($ordersItem->price - $ordersItem->discount) * $ordersItem->count)?>
                                                        <?php endif;?>
                                                    </td>
                                                    <td class="basket item-{$key}">
                                                        <?php if(!empty($ordersItem->good) && !empty($ordersItem->shop) && !empty($ordersItem->goodsVariations) && $ordersItem->good->status == 1 && $ordersItem->shop->status == 1 && $ordersItem->goodsVariations->status == 1): ?>
                                                            <input type="checkbox" id="<?=$tableId.'-'.$index?>" checked="checked" count="<?=$ordersItem->count?>" name="goods[<?= $ordersItem->good->id?>]" value="<?= $ordersItem->variation_id?>"/>
                                                        <?php else: ?>
                                                            <b> <?=Yii::t('app','Нет в наличии')?></b>
                                                        <?php endif;?>
                                                    </td>

                                                    </tr>
                                                    <?php
                                                    if($ordersItem->status == 1 && $ordersItem->status_id != 1007){
                                                        $ordersItemsStatusList++;
                                                    };
                                                    $index++;
                                                }
                                                $deliveryDate = $deliveryGroup->getTimeWithDeltaTwo($key,$ordersGroup->delivery_date);

                                            }
                                        }?>
                                        <tr class="i delivery groups">
                                            <td  class="delivery-price " colspan="8">
                                                <?= $ordersGroup->deliveries->name?> -
                                                <?php if(!empty($ordersGroup->users_address)){
                                                    print !empty($ordersGroup->users_address->street) ? $ordersGroup->users_address->street : '';
                                                    print !empty($ordersGroup->users_address->house) ? ', д.'.$ordersGroup->users_address->house : '';
                                                    print !empty($ordersGroup->users_address->room) ? ', кв.'.$ordersGroup->users_address->room : '';
                                                }?>
                                                <div><?php //= (($ordersItemsStatusList > 0) ? 'Выдача' : 'Выдан') . ': ' . date('Y.m.d H:i',strtotime($deliveryDate)).' - '.date('H:i',strtotime($deliveryDate) + 7200);
                                                    //корректное обображение времени
                                                    $templateExit = '';
                                                    if($ordersItemsStatusList > 0){
                                                        $templateExit .= Yii::t('app','Выдача').' : ';
                                                    }
                                                    else{
                                                        $templateExit .= Yii::t('app','Выдан').' : ';
                                                    }
                                                    if($key =='farFarWay_2' && $ordersGroup->delivery_id==1003){
                                                        $templateExit .= date('Y.m.d H:i',strtotime($deliveryDate)).' - '.date('H:i',strtotime($deliveryDate) + 50400);
                                                    }
                                                    elseif($key =='farFarWay_2' && ($ordersGroup->delivery_id==1006 || $ordersGroup->delivery_id==1007)){
                                                        $templateExit .= date('Y.m.d H:i',strtotime($deliveryDate)+36000).' - '.date('H:i',strtotime($deliveryDate) + 50400);
                                                    }
                                                    else{
                                                        $templateExit .= date('Y.m.d H:i',strtotime($deliveryDate)).' - '.date('H:i',strtotime($deliveryDate) + 10800);
                                                    }
                                                    echo $templateExit;
                                                    ?></div>
                                            </td>
                                            <td ><?=$weightAll .' г'?></td>
                                            <td class="money"><?=ModFunctions::money($ordersGroup->delivery_price)?></td>
                                            <td ></td>
                                        </tr>
                                        <tr class="footer groups">
                                            <td colspan="10">
                                                <div class="button">
                                                <input
                                                    type="button"
                                                    value="<?=\Yii::t('app','В корзину')?>"
                                                    class="basket_button_repeat button_oran"
                                                    data-action="bay"
                                                    data-group="<?=$ordersGroup->id?>"
                                                    data-key="<?=$key?>"
                                                    data-min-prod="<?=$indexStart?>"
                                                    data-max-prod="<?=$index?>"
                                                    disabled
                                                    />
                                                    </div>

                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            else{

                                foreach ($order->ordersGroups as $ordersGroup) {
                                    foreach ($ordersGroup->ordersItems as $n => $ordersItem) {?>
                                        <tr class="i groups">
                                        <td class="name" colspan="4"><span class="num"><?= $n + 1;?>.</span><?= $ordersItem->good->name?><br /><span class="tag"><?= !empty($ordersItem->goodsVariations) ? $ordersItem->goodsVariations->titleWithPropertiesForCatalog : ''?></span></td>
                                        <td class="money"><?php if($ordersItem->bonus > 0): ?><span class="bonus"><?=ModFunctions::bonus($ordersItem->price)?></span> <?php else: ?><?=ModFunctions::money($ordersItem->price)?> <?php endif;?></td>
                                        <td class="money"> <?php if($ordersItem->bonus > 0): ?>—<?php else:?><?=ModFunctions::money($ordersItem->discount)?><?php endif;?></td>
                                        <td class="count"><?=$ordersItem->count?> шт.</td>
                                        <td class="status_name"><?php
                                            if(!empty($ordersItem->statusTitle)){
                                                print $ordersItem->statusTitle->name;
                                            }elseif(empty($ordersItem->status_id) && $ordersItem->status == 1){?>
                                                <?=\Yii::t('app','Обрабатывается')?><?php
                                            }elseif($ordersItem->status == 0){?>
                                                <?=\Yii::t('app','отменён')?><?php
                                            }?>
                                        </td>
                                        <td class="money"><?php if($ordersItem->bonus > 0): ?><span class="bonus"><?=ModFunctions::bonus($ordersItem->bonus)?></span> <?php else: ?><?=ModFunctions::money(($ordersItem->price - $ordersItem->discount) * $ordersItem->count)?> <?php endif;?></td>
                                        <td class="basket item-{$key}">
                                            <?php if(!empty($ordersItem->good) && !empty($ordersItem->shop) && $ordersItem->good->status == 1 && $ordersItem->shop->status == 1 && !empty($ordersItem->goodsVariations) && $ordersItem->goodsVariations->status == 1): ?>
                                                <input type="checkbox"  class="hidden" checked="checked"  name="goods[<?= $ordersItem->good->id?>]" value="<?= $ordersItem->good->id?>"/>
                                            <?php else: ?>
                                                <b><?=\Yii::t('app','Нет в наличии')?></b>
                                            <?php endif;?>
                                        </td>
                                        </tr><?php
                                    }
                                }
                                ?>
                                <tr class="i delivery groups">

                                    <td  class="delivery-price " colspan="8">
                                        <?= (!empty($ordersGroup->deliveries))? $ordersGroup->deliveries->name : '' ?> -
                                        <?php if(!empty($ordersGroup->users_address)){
                                            print !empty($ordersGroup->users_address->street) ? $ordersGroup->users_address->street : '';
                                            print !empty($ordersGroup->users_address->house) ? ', д.'.$ordersGroup->users_address->house : '';
                                            print !empty($ordersGroup->users_address->room) ? ', кв.'.$ordersGroup->users_address->room : '';
                                        }?>
                                        <div>
                                            <?= 'Выдан ';?>
                                            <?php
                                            if(!empty($order->ordersGroups[0])){
                                                print date('Y.m.d H:i',strtotime($order->ordersGroups[0]->delivery_date)).' - '.date('H:i',strtotime($order->ordersGroups[0]->delivery_date) + 7200);
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="money"><?=ModFunctions::money((!empty($ordersGroup->delivery_price)) ? $ordersGroup->delivery_price : 0 )?></td>
                                    <td ></td>
                                </tr>
                                <tr class="footer groups hidden">

                                    <td colspan="10"><div class="button"><input type="button" value="<?=\Yii::t('app','В корзину')?>" class="button_oran" onclick="orders_basket('{$key}');"  disabled/></div></td>
                                </tr><?php
                            }?>
                        </table>
                    <?php endforeach; ?>
                <?php else:?>
                    <table cellpadding="0" cellspacing="0" border="0" class="mob-table hidden">
                        <tr>
                            <td><?=\Yii::t('app','Нет записей')?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div> <!--/История заказака -->
            <?php endif;?>
        </div>
        <div class="clear"></div>
    </div>
</div>
