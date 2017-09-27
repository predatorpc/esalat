<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $model app\models\Shops */
$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $code_id
 * @property integer $type
 * @property string $extremefitness
 * @property string $comments
 * @property string $comments_call_center
 * @property string $date
 * @property integer $call_status
 * @property integer $status
 *
 * @property UsersPays[] $usersPays
 */
?>
<div id="shop-params">
    <div class="statisticBlock row small">
        <div class="viewCountOrders">
            <div class="buttonViewCount">
                <a <?=(!isset($_GET['filter']))?'class="active"':'' ?>href="<?=Url::toRoute(['order'])?>">Всего заказов: <span><?=$countList['allTotalCount']?></span></a>
            </div>
            <div class="buttonViewCount">
                Необработанных заказов: <span><?=$countList['allTotalCountNoConfirm']?></span>
            </div>
            <div class="buttonViewCount">
                Просроченные заказы: <span><?=$countList['allTotalCountOverdue']?></span>
            </div>
        </div>

        <h5></h5>

        <form method="GET" action="" id="dateFilterForm">
            <label style="vertical-align: top;">
                <input
                    class="form-control"
                    type="radio"
                    name="orders-provider-confirm"
                    value="all"
                    style="display: inline-block;width:15px;margin-right: 15px;"
                    <?=(!isset($filterOredrs['orders-provider-confirm']) || empty($filterOredrs['orders-provider-confirm']) || $filterOredrs['orders-provider-confirm'] == 'all')?' checked':''?>
                ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Все</span>
            </label>
            <label style="vertical-align: top;">
                <input
                    class="form-control"
                    type="radio"
                    name="orders-provider-confirm"
                    value="noconfirm"
                    style="display: inline-block;width:15px;margin-right: 15px;"
                    <?=(!empty($filterOredrs['orders-provider-confirm']) && $filterOredrs['orders-provider-confirm'] == 'noconfirm')?' checked':''?>
                ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Необработанные заказы</span>
            </label>
            <label style="vertical-align: top;">
                <input
                    class="form-control"
                    type="radio"
                    name="orders-provider-confirm"
                    style="display: inline-block;width:15px;margin-right: 15px;"
                    value="overdue"
                    <?=(!empty($filterOredrs['orders-provider-confirm']) && $filterOredrs['orders-provider-confirm'] == 'overdue')?' checked':''?>
                ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Просроченные заказы</span>
            </label>
            <br />
            <label style="vertical-align: top;">Дата
                <select name="orders-provider-date-variant" class="form-control">
                    <option value="delivery"<?=(($filterOredrs['orders-provider-date-variant'] == 'delivery') ? 'selected' : '')?>>Доставки</option>
                    <option value="order"<?=(($filterOredrs['orders-provider-date-variant'] == 'order') ? 'selected' : '')?>>Заказа</option>
                </select>
            </label>
            <label style="vertical-align: top;">От
                <input
                    class="form-control"
                    id="personalShopDateStart"
                    type="date"
                    name="orders-provider-date-start"
                    value="<?=(!empty($filterOredrs['orders-provider-date-start'])?$filterOredrs['orders-provider-date-start']:date('Y-m-d',$dateList['min']))?>"
                >
            </label>
            <label style="vertical-align: top;">До
                <input
                    class="form-control"
                    id="personalShopDateStop"
                    type="date"
                    name="orders-provider-date-stop"
                    value="<?=(!empty($filterOredrs['orders-provider-date-stop'])?$filterOredrs['orders-provider-date-stop']:date('Y-m-d',$dateList['max']))?>"
                >
            </label>
            <br />
            <label style="vertical-align: top;">Cостояние заказа
                <select name="orders-provider-status" class="form-control">
                    <option value="all">Все</option>
                    <option value="disable"<?=(($filterOredrs['orders-provider-status'] == 'disable') ? 'selected' : '')?>>Отменён</option>
                    <?php
                    $statusListFilter = app\models\OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->indexBy('id')->all();
                    foreach ($statusListFilter as $key => $status) {
                        print '
                        <option value="' . $status->id . '" ' . (($status->id == $filterOredrs['orders-provider-status']) ? 'selected' : '') . '>' . $status->name . '</option>
                        ';
                    }
                    ?>
                </select>
            </label>
            <?php
            if($shopId == 10000130 || $shopId == 10000154){
                ?>

                <label style="vertical-align: top;">Клуб
                    <select name="orders-provider-club" class="form-control">
                        <option value="all">Все</option>
                        <option
                            value="home" <?= (('home' == $filterOredrs['orders-provider-club']) ? 'selected' : '') ?>>
                            Прочее
                        </option>
                        <?php
                        $extremeAddress = \app\modules\basket\models\BasketLg::getClubDelivery();

                        foreach ($extremeAddress as $key => $club) {
                            print '
                        <option value="' . $club['value'] . '" ' . (($club['value'] == $filterOredrs['orders-provider-club']) ? 'selected' : '') . '>' . $club['address'] . '</option>
                        ';
                        }
                        ?>
                    </select>
                </label>
                <?php
            }
            ?>
            <br />
            <br />
            <button type="submit" name="setFilter" value="1" class="btn btn-primary" />Показать</button>
            &nbsp;
            <button type="submit" name="delFilter" value="1" class="btn btn-primary" />Сбросить фильтр</button>
        </form>
        <?php



        if(!$dataProvider){
            print '
            <p>Заказов пока нет</p>
            ';
        }else{
            foreach($dataProvider as $orderId => $items){
                ?>
                <div class="order_info"></div>
                <table cellpadding="0" cellspacing="0" border="0" class="table_sellers">
                    <tbody>
                        <tr class="table_sellers_th">
                            <th colspan="2">Номер заказа <span>#<?=$orderId?></span>, <?=date('d.m.Y в H:i',strtotime($items[0]['order_date']))?></th>
                            <th style="text-align: center">Количество</th>
                            <th style="text-align: center">Цена</th>
                            <th style="text-align: center">Время доставки</th>
                            <th>Адрес доставки</th>
                            <th style="text-align: center">Статус</th>
                            <th style="text-align: center">Действие</th>
                        </tr>
                        <?php
                        foreach($items as $item){
                            //print '<pre style="display:none" id="z_11111111111111111">';
                            //print_r($item);
                            //print '</pre>';
                            ?>
                            <tr class="tr30  grey" style="height: 45px;">
                                <td style="width:60px;"><img width="50" src="http://www.Esalad.ru<?=$item['image']?>"></td>
                                <td style="width:300px;"><b><?=isset($item['producer_name']->value)?$item['producer_name']->value:''?></b> <?=$item['good_name']?><?=($item['tags'])?'<br />'.$item['tags']:''?></td>
                                <td style="width:100px; text-align:center;"><?=$item['count']?> шт.</td>
                                <td style="width:100px; text-align:center;"><?=$item['all_money']?> р.</td>
                                <td data-tt="<?=$item['delivery_date']?>" style="width:200px; text-align:center;"><?=date('d.m.Y в H:i',strtotime($item['delivery_date']))?></td>
                                <td style="width:200px;"><?=$item['delivery_address']?></td>
                                <td style="width:200px; text-align:center;">
                                    <?php
                                    $orderStatusLine = \Yii::$app->params['orderStatusLine'];

                                    $statusId = $item['status_id'];
                                    print '<span style="display:none;">'.$statusId.' - '.$item['status_id'].' - '.$item['seller_status_id_'].'</span>';
                                    print '<span style="display:none;">'.$statusId.' - '.$item['status'].' - '.$item['order_status'].'</span>';

                                    if($item['status'] == 1 && $item['order_status'] == 1 && isset($statusId) && !empty($statusId)){
                                        ?>
                                        <select class="seller_status" id="status_change_<?=$item['id']?>" style="width: 140px;">
                                            <?php
                                            if($statusId == 1001){
                                                //<option value="1003">' . $statusList[1003]->name . '</option>
                                                print '
                                                <option value="' . $statusId . '" selected>' . $statusList[1001]->name . '</option>
                                                <option value="1002">' . $statusList[1002]->name . '</option>
                                                ';
                                            }elseif($statusId == 1002){
                                                print '
                                                <option value="' . $statusId . '" selected>' . $statusList[1002]->name . '</option>
                                                ';
                                            }elseif($statusId == 1003){
                                                //<option value="' . $statusId . '" selected>' . $statusList[1003]->name . '</option>
                                                print '
                                                <option value="1005">' . $statusList[1005]->name . '</option>
                                                ';
                                            }elseif($statusId == 1005){
                                                print '
                                                <option value="' . $statusId . '" selected>' . $statusList[1005]->name . '</option>
                                                ';
                                            }elseif($statusId == 1004){
                                                print '
                                                <option value="' . $statusId . '" selected>' . $statusList[1004]->name . '</option>
                                                <option value="1005">' . $statusList[1005]->name . '</option>
                                                ';
                                            }else{
                                                print '
                                                <option value="' . $statusId . '" selected>' . $statusList[1007]->name . '</option>
                                                ';
                                            }

/*

                                            foreach ($statusList as $key => $status) {
                                                print '
                                                <option value="' . $status->id . '" ' . (($status->id == $statusId) ? 'selected' : '') . '>' . $status->name . '</option>
                                                ';
                                            }*/
                                            ?>
                                        </select>
                                    <?php
                                    }elseif($item['status'] == 0 || $item['order_status'] == 0){
                                        print '<span class="text-danger">Отменён</span>';
                                    }elseif(!isset($statusId) && empty($statusId)){
                                        print '<span class="text-warning">Новый заказ</span>';
                                    }



                                    /*
                                    if($statusId != 1006 && $statusId != 1007 && $statusId != 1007 && $statusId != 10010 && ($item['status'] == 1 && $item['order_status'] == 1)){
                                        print '
                                        <select class="seller_status '.(($statusId == 1001)?'hidden':'' ).'" id="status_change_'.$item['id'].'" style="width: 140px;">
                                        ';
                                            foreach ($statusList as $key => $status){
                                                print '
                                                <option value="'.$status->id.'" '.(($status->id == $statusId)?'selected':'').'>'.$status->name.'</option>
                                                ';
                                            }
                                        print '
                                        </select>
                                        ';
                                    }elseif($statusId == 0 || $item['order_status'] == 0 && ($statusId != 1009 && $statusId != 1010)){
                                        print 'Заказ отменен';
                                    }elseif($statusId != 1009){
                                        print 'Передан курьеру';
                                    }elseif($statusId != 1010){
                                        print 'Передан курьеру';
                                    }else{
                                        print 'Доставлен в пункт назначения';
                                    }
                                    */
                                    ?>
                                </td>
                                <td style="width: 140px;">
                                    <div style="text-align: center;">
                                        <?php
                                        if($item['status'] == 1 && $item['order_status'] == 1 && isset($statusId) && !empty($statusId)){
                                            print '
                                            <div style="text-align: center;">
                                                <div data-id="'.$item['id'].'" data-order-item="'.$item['id'].'" class="button_ok" data-action="save">Сохранить</div>
                                            </div>
                                            ';
                                        }elseif($item['status'] == 0 || $item['order_status'] == 0){

                                        }elseif(!isset($statusId) || empty($statusId)){
                                            print '
                                            <div style="text-align: center;">
                                                <div data-id="'.$item['id'].'" data-order-item="'.$item['id'].'" class="button_ok" data-action="accept">Принять</div>
                                            </div>
                                            ';
                                        }

                                        /*
                                        if($statusId == 1001){
                                            print '
                                            <div style="text-align: center;">
                                                <div data-id="'.$item['id'].'" data-order-item="'.$item['id'].'" class="button_ok" data-action="accept">Принять</div>
                                            </div>
                                            ';
                                        }else{
                                            print '
                                            <div style="text-align: center;">
                                                <div data-id="'.$item['id'].'" data-order-item="'.$item['id'].'" class="button_ok" data-action="save">Сохранить</div>
                                            </div>
                                            ';
                                        }
                                        */
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="1" style="color: #999999;"><?=$item['good_code']?></td>
                                <td colspan="5" ><?=$item['comments_shop']?></td>
                                <td style="text-align: center;">
                                    <span class="open_comment_shop " id="open_comment_shop_<?=$item['id']?>" data-id="<?=$item['id']?>">Комментировать</span>
                                    <div class="hidden" id="comment_wrapper_<?=$item['id']?>" data-action="add_comment" style="width: 300px; height: 100px; position: absolute;">
                                        <textarea class="aresize" id="comment_<?=$item['id']?>" style="width: 100%; height: 100%;"><?=$item['comments_shop']?></textarea>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        }
        ?>
        <div class="test">
            <?php
            echo LinkPager::widget([
                'pagination'=>$dataProviderOriginal->pagination,
            ]);
            ?>
        </div>
    </div>
</div>