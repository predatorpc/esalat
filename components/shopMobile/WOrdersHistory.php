<?php

namespace app\components\shopMobile;
use yii\helpers\Html;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Widget;
use app\modules\my\models\OrdersHistory;
use app\modules\common\models\ModFunctions;
/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
class WOrdersHistory extends Widget
{
    public $orders;

    public function init()
    {
        parent::init();
        if ($this->orders === null) {
            $this->orders = false;
        }
    }

    public function run(){

?>
<!--Мобильная версия -->
<div class="my-orders-m">
    <?php $i = 1; ?>
    <?php $weightAll=0;?>
    <?php if(!empty($this->orders)):?>
        <?php foreach($this->orders as $key => $order):?>
            <?php $tableId = 'key'.$key;?>
            <table cellpadding="0" cellspacing="0" border="0" class="mob-table-order" id="key<?=$key?>">
                <tr>
                    <th class="title" onclick="m_orders_open('<?=$key?>');">
                        <div class="code open" ><b>#<?= $order->id?></b> — <?=ModFunctions::datetime($order->date);?>
                            <span class="total"><?= \Yii::t('app','Итог')?>: <b><?= ModFunctions::money($order->money)?></b>
                                <?php
                                echo Html::a('Распечатать', ['/my/orders-pdf?id='.$order->id], [
                                    'class'=>'ver_pdf',
                                    'style'=>'color:#fff;margin-left: 5px;',
                                    'target'=>'_blank',
                                    'data-toggle'=>'tooltip',
                                    'data-order'=> $order->id,
                                    'title'=>'Распечатать'
                                ]);
                                ?>
                            </span>

                        </div>
                    </th>
                </tr>
                <?php

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
                                        <tr class="hidden_r">
                                            <td>
                                                <div class="name"><span class="num"><?= $n + 1;?>.</span> <?=$ordersItem->good->name?> / <span class="tag"><?= !empty($ordersItem->goodsVariations) ? $ordersItem->goodsVariations->titleWithPropertiesForCatalog : ''?></span></div>
                                                <div class="money">
                                                    <span class="title"><?=Yii::t('app','Цена')?>:</span><?php if(($ordersItem->price - $ordersItem->bonus) > 0): ?><?=ModFunctions::money($ordersItem->price)?><?php endif;?>  <?php if($ordersItem->bonus  > 0): ?>/ <span class="bonus"><?=ModFunctions::bonus($ordersItem->bonus); ?></span><?php endif;?>
                                                    <div class="check">
                                                        <?php if(!empty($ordersItem->good) && !empty($ordersItem->shop) && !empty($ordersItem->goodsVariations) && $ordersItem->good->status == 1 && $ordersItem->shop->status == 1 && $ordersItem->goodsVariations->status == 1): ?>
                                                            <input type="checkbox" id="<?=$tableId.'-'.$index?>" checked="checked" count="<?=$ordersItem->count?>" name="goods[<?= $ordersItem->good->id?>]" value="<?= $ordersItem->variation_id?>"/>
                                                        <?php else: ?>
                                                            <b><?=Yii::t('app','Нет в наличии')?></b>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="money"><span class="title"><?=Yii::t('app','Скидка')?>:</span><?php if($ordersItem->discount > 0): ?>—<?php else:?><?=ModFunctions::money($ordersItem->discount)?><?php endif;?></div>
                                                <div class="count"><span class="title"><?=Yii::t('app','Количество')?>:</span><?=$ordersItem->count?> шт.</div>
                                                <div class="count"><span class="title"><?=Yii::t('app','Вес')?>:</span>
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
                                                </div>
                                                <div class="status_name"><span class="title"><?=Yii::t('app','Состояние заказа')?>:</span>
                                                    <?php
                                                        if(!empty($ordersItem->statusTitle)){
                                                        print $ordersItem->statusTitle->name;
                                                        }elseif(empty($ordersItem->status_id) && $ordersItem->status == 1){?>
                                                        Обрабатывается<?php
                                                    }elseif($ordersItem->status == 0){?>
                                                        отменён<?php
                                                    }?>
                                                </div>
                                                <div class="money"><span class="title">Итого:</span>
                                                    <?php if($ordersItem->bonus > 0): ?>
                                                        <span class="bonus">
                                                            <?=ModFunctions::bonus($ordersItem->bonus * $ordersItem->count)?>
                                                            <?php if(($ordersItem->price - $ordersItem->bonus) > 0){
                                                                print ' / ' .  ModFunctions::money(($ordersItem->price - $ordersItem->bonus - $ordersItem->discount) * $ordersItem->count);
                                                            }?>
                                                        </span>
                                                    <?php else:?>
                                                        <?=ModFunctions::money(($ordersItem->price - $ordersItem->discount) * $ordersItem->count)?>
                                                    <?php endif;?></div>
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
                            <tr class="delivery groups hidden_r">
                                <td  class="delivery-price">
                                    <?= $ordersGroup->deliveries->name?> —
                                    <?php if(!empty($ordersGroup->users_address)){
                                        print !empty($ordersGroup->users_address->street) ? $ordersGroup->users_address->street : '';
                                        print !empty($ordersGroup->users_address->house) ? ', д.'.$ordersGroup->users_address->house : '';
                                        print !empty($ordersGroup->users_address->room) ? ', кв.'.$ordersGroup->users_address->room : '';
                                    }?>
                                    <div><b><?php //= (($ordersItemsStatusList > 0) ? 'Выдача' : 'Выдан') . ': ' . date('Y.m.d H:i',strtotime($deliveryDate)).' - '.date('H:i',strtotime($deliveryDate) + 7200);
                                            //корректное обображение времени
                                            $templateExit = '';
                                            if($ordersItemsStatusList > 0){
                                                $templateExit .='Выдача : ';
                                            }
                                            else{
                                                $templateExit .='Выдан : ';
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
                                            ?></b><br> <?=Yii::t('app','Вес')?>: <b><?=$weightAll .' г'?></b></div>
                                </td>
                            </tr>
                            <tr class="footer groups hidden_r">
                                <td>
                                    <div class="button">
                                        <input
                                            type="button"
                                            value="<?=Yii::t('app','В корзину')?>"
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
                            </tr>

                        <?php }
                    }
                }else{
                    foreach ($order->ordersGroups as $ordersGroup) {
                        foreach ($ordersGroup->ordersItems as $n => $ordersItem) {?>
                         <tr class="grey hidden_r">
                            <td>
                                <div class="name"><span class="num"><?= $n + 1;?>. </span> <?= $ordersItem->good->name?><br /><span class="tag"><?= !empty($ordersItem->goodsVariations) ? $ordersItem->goodsVariations->titleWithPropertiesForCatalog : ''?></span></div>
                                <div class="money">
                                    <span class="title"><?=Yii::t('app','Цена')?>:</span>
                                       <?php if($ordersItem->bonus > 0): ?><span class="bonus"><?=ModFunctions::bonus($ordersItem->price)?></span> <?php else: ?><?=ModFunctions::money($ordersItem->price)?> <?php endif;?>
                                    <div class="check">
                                        <?php if(!empty($ordersItem->good) && !empty($ordersItem->shop) && $ordersItem->good->status == 1 && $ordersItem->shop->status == 1 && !empty($ordersItem->goodsVariations) && $ordersItem->goodsVariations->status == 1): ?>
                                            <input class="hidden" type="checkbox" checked="checked"  name="goods[{$i.good_id}]" value="{$i.good_id}"/>
                                        <?php else: ?>
                                            <b><?=Yii::t('app','Нет в наличии')?></b>
                                        <?php endif;?>
                                    </div>
                                </div>
                                <div class="money"><span class="title"><?=Yii::t('app','Скидка')?>:</span>
                                    <?php if($ordersItem->bonus > 0): ?>—<?php else:?><?=ModFunctions::money($ordersItem->discount)?><?php endif;?>
                                </div>
                                <div class="count"><span class="title"><?=Yii::t('app','Количество')?>:</span> <?=$ordersItem->count?> шт.</div>
                                <div class="count weight"><span class="title"><?=Yii::t('app','Вес')?>:</span>
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
                                    ?>
                                </div>
                                <div class="status_name"><span class="title"><?=Yii::t('app','Состояние заказа')?>:</span><?php
                                if(!empty($ordersItem->statusTitle)){
                                    print $ordersItem->statusTitle->name;
                                }elseif(empty($ordersItem->status_id) && $ordersItem->status == 1){?>
                                    <?=\Yii::t('app','Обрабатывается')?><?php
                                }elseif($ordersItem->status == 0){?>
                                    <?=\Yii::t('app','отменён')?><?php
                                }?>
                                </div>
                                <div class="money"><span class="title"><?= \Yii::t('app','Итог')?>:</span>
                                    <?php if($ordersItem->bonus > 0): ?><span class="bonus"><?=ModFunctions::bonus($ordersItem->bonus)?></span> <?php else: ?><?=ModFunctions::money(($ordersItem->price - $ordersItem->discount) * $ordersItem->count)?> <?php endif;?>
                                </div>
                            </td>
                         </tr>
               <?php }
                    }
                    ?>
                    <tr class="delivery groups hidden_r">
                        <td  class="delivery-price">
                            <?= (!empty($ordersGroup->deliveries))? $ordersGroup->deliveries->name : '' ?> -
                            <?php if(!empty($ordersGroup->users_address)){
                                print !empty($ordersGroup->users_address->street) ? $ordersGroup->users_address->street : '';
                                print !empty($ordersGroup->users_address->house) ? ', д.'.$ordersGroup->users_address->house : '';
                                print !empty($ordersGroup->users_address->room) ? ', кв.'.$ordersGroup->users_address->room : '';
                            }?>
                            <div>
                                <b>
                                <?= 'Выдан ';?>
                                <?php
                                if(!empty($order->ordersGroups[0])){
                                    print date('Y.m.d H:i',strtotime($order->ordersGroups[0]->delivery_date)).' - '.date('H:i',strtotime($order->ordersGroups[0]->delivery_date) + 7200);
                                }
                                ?>
                                </b>
                            </div>
                        </td>
                        <td class="money"><?=ModFunctions::money((!empty($ordersGroup->delivery_price)) ? $ordersGroup->delivery_price : 0 )?></td>
                        <td ></td>
                    </tr>
                <?php
                }?>
            </table>
        <?php endforeach; ?>
    <?php else:?>
        <table cellpadding="0" cellspacing="0" border="0" class="mob-table">
            <tr>
                <td><?=\Yii::t('app','Нет записей')?></td>
            </tr>
        </table>
    <?php endif; ?>

</div><!--Мобильная версия -->

<?php
    }
}