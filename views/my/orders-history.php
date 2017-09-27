<?php

use yii\helpers\Html;
use app\modules\common\models\ModFunctions;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'История заказов';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="content">
    <div class="path"><a href="/">Главная</a></div>
    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-12">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <!--История заказака -->
            <?php if(!empty(Yii::$app->params['mobile'])):?>
               <?= \app\components\shopMobile\WOrdersHistory::widget(); ?>
            <?php else: ?>
            <div class="my-orders my-list ">
                <?php $i = 1; ?>
                <?php if(isset($orders) && !empty($orders)):?>
                    <?php foreach($orders as $key=>$item):
                        ?>
                       <table cellpadding="0" cellspacing="0" border="0" id="key<?=$key?>" >
                        <tr class=" res">
                            <th colspan="4"><div class="code open" ><a href="/" onclick="orders_open('<?=$key?>'); return false;" ><b>#<?= $item['order_id']?></b> — <?=ModFunctions::datetime($item['date']);?></a> <span class="total">Итог: <?= ModFunctions::money($item['total'])?></span></div>
                            </th>
                            <th class="title groups">Цена</th>
                            <th class="title groups">Скидка</th>
                            <th class="title groups">Количество</th>
                            <th class="title groups">Состояние заказа</th>
                            <th class="title groups">Итого</th>
                            <th class="title groups">Повторить заказ</th>
                        </tr>

                        <tr class="i groups">
                            <td class="name" colspan="4">
                                <span class="num">1.</span> <?=$item['good_name']?><br /><span class="tag"><?=$item['tags']?></span></td>
                            <td class="money">
                                <?php if($item['bonus'] > 0): ?><span class="bonus"><?=ModFunctions::bonus($item['bonus']); ?></span><?php else:?> <?=ModFunctions::money($item['price'])?><?php endif;?></td>
                            <td class="money">
                                <?php if($item['bonus'] > 0): ?>—<?php else:?><?=ModFunctions::money($item['discount'])?><?php endif;?></td>
                            <td class="count">
                                <?= $item['count'] ?> шт.</td>
                            <td class="status_name">
                                <?php if($item['status_name']): ?><?=$item['status_name']?><?php else:?> <?php if($item['status_g'] == 0): ?><?=$item['status']?><?php endif;?><?php endif;?></td>
                            <td class="money">
                                <?php if($item['bonus'] > 0): ?><span class="bonus"><?=ModFunctions::bonus($item['bonus'])?></span><?php else:?><?= ModFunctions::money($item['money'])?><?php endif;?></td>
                            <td class="basket item-{$key}">
                                <?php if(isset($item['status_good']) && isset($item['good_confirm']) && isset($item['shop_status']) == '' || isset($item['shop_status']) && isset($item['good_show']) && isset($item['variation_status'])): ?>
                                    <input class="hidden"  type="checkbox" checked="checked"  name="goods[{$item.good_id}]" value="{$item.good_id}" />
                               
                                <?php else: ?>
                                    <b>Нет в наличии</b>
                                <?php endif;?>
                            </td>
                            <?php $n = 2; ?>
                                <?php foreach($item['group_orders'] as $k=>$i):
                                //\app\modules\common\models\Zloradnij::print_arr($i);
                                ?>
                                    <tr class="i groups">
                                        <td class="name" colspan="4"><span class="num"><?=$n++;?>.</span><?=$i['good_name']?><br /><span class="tag"><?=$i['tags']?></span></td>
                                        <td class="money"><?php if($i['bonus'] > 0): ?><span class="bonus"><?=ModFunctions::bonus($i['price'])?></span> <?php else: ?><?=ModFunctions::money($i['price'])?> <?php endif;?></td>
                                        <td class="money"> <?php if($i['bonus'] > 0): ?>—<?php else:?><?=ModFunctions::money($i['discount'])?><?php endif;?></td>
                                        <td class="count"><?=$i['count']?> шт.</td>
                                        <td class="status_name"><?php if($i['status_name']): ?><?=$i['status_name']?><?php else:?> <?php if($i['status_g'] == 0): ?><?=$i['status']?><?php endif;?><?php endif;?></td>
                                        <td class="money"><?php if($i['bonus'] > 0): ?><span class="bonus"><?=ModFunctions::bonus($i['bonus'])?></span> <?php else: ?><?=ModFunctions::money($i['money'])?> <?php endif;?></td>
                                        <td class="basket item-{$key}">
                                            <?php if(isset($i['status_good']) && isset($i['good_confirm']) && isset($i['shop_status']) == '' || isset($i['shop_status']) && isset($i['good_show']) && isset($i['variation_status'])): ?>
                                                <input type="checkbox" class="hidden" checked="checked"  name="goods[{$i.good_id}]" value="{$i.good_id}"/>
                                            <?php else: ?>
                                               <b>Нет в наличии</b>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                  <tr class="i delivery groups">
                                    <td  class="delivery-price " colspan="8">
                                       <?=$item['delivery_name']?> <?php if(isset($item['store_name'])): ?> — <?=$item['store_name']?><?= $item['store_name']?><?php else: ?> — <?=$item['address']?><?php if(isset($item['house'])): ?> д. <?=$item['house']?><?php endif; ?><?php if(isset($item['room'])): ?> кв. <?=$item['room']?><?php endif; ?><?php endif; ?><div><?php if(isset($item['status_g']) && $item['status_g'] == 1): ?><?=$item['status']?><?php endif; ?> </div></td>
                                    <td class="money"><?=ModFunctions::money($item['delivery_price'])?></td>
                                    <td ></td>
                                </tr>
                                  <tr class="footer groups">
                                     <td colspan="10"><div class="button"><input type="button" value="В корзину" class="button_oran" onclick="orders_basket('{$key}');"  disabled/></div></td>
                                  </tr>
                           </tr>
                    </table>
                    <?php endforeach; ?>
              <?php else:?>
                <table cellpadding="0" cellspacing="0" border="0" class="mob-table hidden">
                    <tr>
                        <td>Нет записей</td>
                    </tr>
                </table>
              <?php endif; ?>
            </div> <!--/История заказака -->
        <?php endif;?>
        </div>
        <div class="clear"></div>
    </div>
</div>
