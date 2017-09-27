<?php

$this->title = 'Master Tanya';

if(!empty($orders)){?>
    <div>All orders - <?= count($orders)?></div>
    <div class="row">
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            User Name
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            Order ID
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            Order Date
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            Order Bonus
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            Transactions Bonus
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
            Current User Bonus
        </div>
    </div><?php
    $i = 0;
    foreach ($orders as $order) {
        $bonus = \app\modules\common\models\UsersBonus::find()->where(['order_id' => $order->id])->andWhere(['<','bonus',0])->sum('bonus');
        if($order->bonus != abs($bonus)){?>
            <div class="row" style="<?= ($i % 2 == 0) ? 'background:#CCC' : ''?>" data-i="<?= $i?>" data-dev="<?= $i % 2?>">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?= ($i + 1) . ' ' . $order->user->name?>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <?= $order->id?>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    <?= $order->date?>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <?= $order->bonus?>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <?= $bonus?>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <?= $order->user->bonus?>
                </div>
            </div><?php
            $i++;
        }
    }
}