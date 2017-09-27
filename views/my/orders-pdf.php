<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Html;

$this->title = Yii::t('app','Накладная');
$this->params['breadcrumbs'][] = $this->title;
$weightAll=0;


?>

 <div class="content pdf">
     <br>
      <h5 class="text-center" style="margin: 20px 0px 20px">НАКЛАДНАЯ № <b><?=$order->id?></b> от <b><?=date('d.m.Y',strtotime($order->date))?></b> г</h5>
     <div class="text-center" >
         ООО "Экстрим Стиль" ИНН: 5406384981 КПП: 540601001 ОГРН: 1075406005377 ОКПО: 99827035<br>
         Расчетный счет: 40702810100400012243 Банк: ФИЛИАЛ N 5440 ВТБ 24 (ПАО)<br>
         БИК: 045004751 Корр. счет: 30101810450040000751<br>
         <p></p>
         Юридический адрес: 630091, Новосибирская обл, Новосибирск г, Советская ул, дом № 64<br>
         Телефон: 203-44-42 Директор: Гнилицкая Екатерина Евгеньевна<br>
     </div>
     <br>
     <br>
     <?php $i = 1; ?>
     <table class="table table-bordered">
         <tr>
             <th colspan="4" class="title"><?=Yii::t('app','Наименование товара')?></th>
             <th><?=Yii::t('app','Цена')?> </th>
             <th><?=Yii::t('app','Скидка')?></th>
             <th><?=Yii::t('app','Количество')?></th>
             <th><?=Yii::t('app','Состояние заказа')?></th>
             <th><?=Yii::t('app','Вес')?></th>
             <th><?=Yii::t('app','Итого')?></th>
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
                                     <td class="name" colspan="4"><b class="num"><?= $n + 1;?>.</b><?= $ordersItem->good->name?><br /><span class="tag"><?= !empty($ordersItem->goodsVariations) ? $ordersItem->goodsVariations->titleWithPropertiesForCatalog : ''?></span></td>
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
                     </tr>
                     <tr>
                         <td  class="delivery-price " colspan="8"><b>Итого:</b></td>
                         <td ><b><?=$weightAll .' г'?></b></td>
                         <td class="money"><b><?=ModFunctions::money($order->money + $ordersGroup->delivery_price)?></b></td>
                     </tr>
                     <?php
                 }
             }
         }
         else{
             foreach ($order->ordersGroups as $ordersGroup) {
                 foreach ($ordersGroup->ordersItems as $n => $ordersItem) {?>
                     <tr class="i groups">
                         <td class="name" colspan="4"><strong><?= $n + 1;?>.</strong><?= $ordersItem->good->name?><br /><span class="tag"><?= !empty($ordersItem->goodsVariations) ? $ordersItem->goodsVariations->titleWithPropertiesForCatalog : ''?></span></td>
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
                     </tr><?php
                 }
             }
             ?>
             <tr class="i delivery groups">
                 <td  class="delivery-price " colspan="7">
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
             </tr>

             <?php
         }?>
     </table>
     <br>
     <div style="float: left; width: 50%"><img src="/images/document/signature.jpg" width="350px"></div>
     <div style="float: left; width: 50%;text-align: right"><img src="/images/document/printing.jpg" width="160px"></div>
     <div class="clear"></div>

 </div>