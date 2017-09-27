<?php
use app\modules\common\models\ModFunctions;
use kartik\export\ExportMenu;

$this->title = 'Отчет о продажах';
?>
<!--Отчет о продажам-->

<?php

        $gridColumns = [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'header' => 'Дата заказа',
                'value' => function($orderItem){
                    return $orderItem->orders->date;
                }
            ],
            [
                'header' => 'Номер заказа',
                'value' => function($orderItem){
                    return $orderItem->orders->id;
                }
            ],
            [
                'header' => 'Поставщик',
                'value' => function($orderItem){
                    return $orderItem->shop->name;
                }
            ],
            [
                'header' => 'Товар',
                'value' => function($orderItem){
                    return $orderItem->good->name;
                }
            ],
            [
                'header' => 'Опции',
                'value' => function($orderItem){
                    return $orderItem->goodsVariations ? $orderItem->goodsVariations->titleWithProperties : '';
                }
            ],
            [
                'header' => 'Цена входная',
                'value' => function($orderItem){
                    return $orderItem->price - $orderItem->comission;
                }
            ],
            [
                'header' => 'Наценка',
                'value' => function($orderItem){
                    return $orderItem->comission;
                }
            ],
            [
                'header' => 'Цена выходная',
                'value' => function($orderItem){
                    return $orderItem->price;
                }
            ],
            [
                'header' => 'Скидка',
                'value' => function($orderItem){
                    return $orderItem->discount;
                }
            ],
            [
                'header' => 'Бонусы',
                'value' => function($orderItem){
                    return $orderItem->bonus;
                }
            ],
            [
                'header' => 'Количество',
                'value' => function($orderItem){
                    return $orderItem->count;
                }
            ],
            [
                'header' => 'Сумма',
                'value' => function($orderItem){
                    return ($orderItem->price - $orderItem->comission - $orderItem->bonus)*$orderItem->count;
                }
            ],
            [
                'header' => 'Агентские',
                'value' => function($orderItem){
                    return $orderItem->fee;
                }
            ],
            [
                'header' => 'Итого',
                'value' => function($orderItem){
                    return ($orderItem->comission - $orderItem->fee)*$orderItem->count;
                }
            ],
            [
                'header' => 'Место доставки',
                'value' => function($orderItem){
                    return 'Доставка по адресу: '.$orderItem->orderGroup->users_address->ConcatAddressFull;
                }
            ],
            [
                'header' => 'Дата доставки',
                'value' => function($orderItem){
                    return $orderItem->orderGroup->delivery_date;
                }
            ],
            [
                'header' => 'Стоимость доставки',
                'value' => function($orderItem){
                    return $orderItem->orderGroup->delivery_price;
                }
            ],
            [
                'header' => 'Client',
                'value' => function($orderItem){
                    return $orderItem->orders->user->name;
                }
            ],
            [
                'header' => 'tel',
                'value' => function($orderItem){
                    return $orderItem->orders->user->phone;
                }
            ],
        ];

    // Фильтер блок;
    print $this->render('_order-new', [
        'filter' => $filter,
    ]); ?>
    <!--<div class="exports-xls" onclick="return exports_xls('orders');" style="cursor: pointer;">XLS</div></b>-->
    <!--<div class="exports-xml" onclick="return exports_xml('orders');" style="cursor: pointer;">XML</div>-->
    <div id="panel-content" class=" panel">
        <div class="panel-heading hidden">Итого</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <b>Заказов</b>
                    <div class="orders-num"><?=$filter->getOrderList()->count();?></div>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-5">

                    <div class="report-numder">Полная стоимость = <b><?= ModFunctions::moneyFormat($filter->priceResult['fullPrice']);?> р.</b></div>
                    <div class="report-numder">Скидка = <b><?= ModFunctions::moneyFormat($filter->priceResult['discount']);?> р.</b></div>
                    <div class="report-numder">Расчет бонусами = <b><?= ModFunctions::moneyFormat($filter->priceResult['bonus']);?> β.</b></div>
                    <div class="report-numder">Прибыль = <b><?= ModFunctions::moneyFormat($filter->priceResult['fullPrice'] - ($filter->priceResult['fullPrice'] - $filter->priceResult['commission'] +  $filter->priceResult['discount'] +  $filter->priceResult['feePays'] - $filter->priceResult['discount']) + $filter->priceResult['deliveryPrice'] - $filter->priceResult['bonus'] - $filter->priceResult['discount']); ?> р.</b></div>
                    <div class="report-numder"> Выручка = <b><?= ModFunctions::moneyFormat($filter->priceResult['fullPrice'] + $filter->priceResult['deliveryPrice'] - $filter->priceResult['bonus'] - $filter->priceResult['discount']);?> р.</b></div>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-5">
                    <div class="report-numder">Доставка = <b><?=  ModFunctions::moneyFormat($filter->priceResult['deliveryPrice']);?> р.</b></div>
                    <div class="report-numder">Выплаты поставщикам = <b><?=  ModFunctions::moneyFormat($filter->priceResult['fullPriceIn']);?> р.</b></div>
                    <div class="report-numder">Кэшбэк = <b><?= ModFunctions::moneyFormat($filter->priceResult['feePays']);?> р.</b></div>
                    <div class="report-numder">Себестоимость = <b><?=  ModFunctions::moneyFormat($filter->priceResult['fullPrice'] - $filter->priceResult['commission'] +  $filter->priceResult['discount'] +  $filter->priceResult['feePays']);?> р.</b></div>
                    <div class="report-numder">Комиссия за товар = <b><?= ModFunctions::moneyFormat($filter->priceResult['commission']);?> р.</b></div>
                    <div class="report-numder">Отмены = <b><?=$filter->priceResult['cancel'];?></b></div>
                </div>
                <div class="col-md-4 col-sm-2 col-xs-12">
                    <?php
                        echo ExportMenu::widget([
                            'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $items,]),
                            'columns' => $gridColumns,
                            'target' => ExportMenu::TARGET_SELF,
                            'fontAwesome' => true,
                            'clearBuffers' => true,
                            'initProvider' => true,
                            'showColumnSelector' => false,
                            'filename' => 'Отчет о продажах '.date('d.m.Y H:m:s'),
                            //'folder' => $_SERVER['DOCUMENT_ROOT'].'/tmp/',
                            //'stream' => false,
                            //'linkPath' => '/tmp/',
                            'exportConfig' => [
                                ExportMenu::FORMAT_EXCEL_X =>false,
//                                ExportMenu::FORMAT_TEXT => true,
//                                ExportMenu::FORMAT_PDF => true,
//                                ExportMenu::FORMAT_HTML => true,
//                                ExportMenu::FORMAT_EXCEL => true,
//                                ExportMenu::FORMAT_CSV => true,
                            ],
                        ]);
                    ?>
                    <div class="text-danger data-slice" style="cursor: pointer;position: absolute;right: 100px;top: 105px;border-bottom: 1px dashed;">Посмотреть срез</div>
                    <div class="toggleCloseAllNew text-danger">Открыть все</div>
                </div>
            </div>
        </div>
    </div>
    <div id="repost_order_list">
        <?php
        print \yii\widgets\ListView::widget([
            'dataProvider' => $orders,

            'itemView' => '_order-list',
            'layout' => "{items}<div class='clear'></div>\n{pager}",
            'itemOptions' => [
                'tag' => 'div',
                'class' => 'items',
            ],
            'emptyText' => '',
        ]);
        ?>
    </div>

    <div class="clear"></div>
