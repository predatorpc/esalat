<?php

use app\modules\common\models\ModFunctions;
//\app\modules\common\models\Zloradnij::print_arr($filter);

$o = new \app\modules\shop\models\OwnerOrderFilter();
$o->load($_POST);
\app\modules\common\models\Zloradnij::print_arr($o);

echo $this->render('_search', [
    'modelOrder' => $modelOrder,
    'modelOrderType' => $modelOrderType,
    'modelOrderId' => $modelOrderId,
    'modelOrderId' => $modelOrderUser,

    'modelOrderGroup' => new \app\modules\shop\models\OrdersGroups(),
    'modelOrderItem' => $modelOrderItem,
    'modelPromoCode' => $modelPromoCode,

    'modelOrderDateStart' => $modelOrderDateStart,
    'modelOrderDateEnd' => $modelOrderDateEnd,

    'modelUser' => $modelUser,
    'modelUserForStore' => $modelUserForStore,
    'modelOrdersGroupsForStore' => $modelOrdersGroupsForStore,

    'params' => $filter->getFilterParams(),
    'filter' => $filter,
]);?>
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-1">
            Заказов<br><span class="bold"><?= $orders->count?></span>
        </div>

        <div class="col-xs-10 col-sm-6 col-md-5 col-lg-4">
            <?= Yii::t('admin', 'Полная стоимость') ?> = <b><?= ModFunctions::money($filter->priceResult['fullPrice'])?></b><br>
            <?= Yii::t('admin', 'Скидка') ?> = <b>-<?= ModFunctions::money($filter->priceResult['discount'])?></b><br>
            <?= Yii::t('admin', 'Бонусы') ?> = <b><?= ModFunctions::bonus($filter->priceResult['bonus'])?></b><br>
            <?= Yii::t('admin', 'Доставка') ?> = <b><?= ModFunctions::money($filter->priceResult['deliveryPrice'])?></b><br>
            <?= Yii::t('admin', 'Сумма') ?> = <b><?= ModFunctions::money($filter->priceResult['fullPrice'] - $filter->priceResult['discount'] - $filter->priceResult['bonus'] + $filter->priceResult['deliveryPrice'])?></b>
        </div>

        <div class="col-xs-5 col-sm-6 col-md-3 col-lg-3">
            <?= Yii::t('admin', 'Выплаты поставщикам') ?> = <b><?= ModFunctions::money($filter->priceResult['shopsPays'])?></b><br>
            <?= Yii::t('admin', 'Выплаты агентам') ?> = <b><?= ModFunctions::money($filter->priceResult['feePays'])?></b> <span class="info" title="Выплаты владельцам промо-кодов">?</span><br>
            <?= Yii::t('admin', 'Начислено таксистам') ?> = <b><?= ModFunctions::money($filter->priceResult['deliveryPays'])?></b><br><br>
            <?= Yii::t('admin', 'Сумма') ?> =<b><?= ModFunctions::money($filter->priceResult['shopsPays'] + $filter->priceResult['feePays'])?></b>
        </div>

        <div class="col-xs-5 col-sm-6 col-md-3 col-lg-3">
            <?= Yii::t('admin', 'Комиссия за товар') ?> = <b><?= ModFunctions::money($filter->priceResult['commission'])?></b><br>
            <?= Yii::t('admin', 'Удержания') ?> = <b><?= ModFunctions::money(-1*$filter->priceResult['commissionMinus'])?></b> <span class="info" title="Скидки по промо-кодам, оплаты бонусами и выплаты таксистам">?</span><br>
            <?= Yii::t('admin', 'Комиссия за доставку') ?> = <b><?= ModFunctions::money($filter->priceResult['commissionDelivery'])?></b> <span class="info" title="Фактическая комиссия за выбранный период">?</span><br><br>
            <?= Yii::t('admin', 'Сумма') ?> = <b><?= ModFunctions::money($filter->priceResult['commission'] - $filter->priceResult['commissionMinus'] + $filter->priceResult['commissionDelivery'])?></b>
        </div>

        <div class="col-xs-5 col-sm-5 col-md-2 col-lg-2">
            <div class="info-cancel">
                <?= Yii::t('admin', 'Отмены') ?> = <b><?= ModFunctions::money($filter->priceResult['cancel'])?></b> <span class="info" title="Сумма отмененных заказов">?</span>
            </div>
            <div class="clear"></div>
            <div class="exports-xls" onclick="return exports_xls('orders');"></div>
            <div class="exports-xml" onclick="return exports_xml('orders');"></div>
        </div>
    </div>

    <hr />

<?php
print \yii\widgets\ListView::widget([
    'dataProvider' => $orders,
    'itemView' => '_order-list',
    'layout' => "<div class='items-order'>{items}<div class='clear'></div></div>\n{pager}",

]);
