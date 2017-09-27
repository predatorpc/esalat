<?php

use app\modules\control\assets\ControlAsset;
ControlAsset::register($this);

$this->title = 'Заказ № '.$order->id;
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="control-order-update">
    <h3>Покупатель</h3>
    <div class="container user-data">
        <?= \app\modules\control\widgets\UserData::widget(['user' => $order->user])?>
    </div>
    <hr />
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right"><span class="text-uppercase">Редактируем заказ</span></div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"><span class="bold"><?= $order->id?></span> от <?= $order->date?></div>
        </div>
    </div>
    <hr />
    <div class="container order-data">
        <?= \app\modules\control\widgets\OrderData::widget(['order' => $order])?>
    </div>
    <hr />
    <div class="container basket-data">
        <?= \app\modules\control\widgets\BasketData::widget(['order' => $order])?>
    </div>
    <hr />
    <div class="container user-reserve-data">
        <?= \app\modules\control\widgets\UserReserveData::widget(['order' => $order])?>
    </div>
    <hr />
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3 text-right"><span class="bold">Товары в заказе</span></div>
            <div class="col-xs-6 col-sm-7 col-md-8 col-lg-9"></div>
        </div>
    </div>
    <hr />
    <div class="container"><?php
        if(!empty($order->ordersGroups)){
            print \app\modules\control\widgets\OrderProductsData::widget(['order' => $order]);
        }?>
    </div>
    <?php

    ?>
</div>
<?php
\app\modules\common\models\Zloradnij::print_arr($orderBasket);
\app\modules\common\models\Zloradnij::print_arr($order);
