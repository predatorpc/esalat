<?php

print 'OK !!!';

\app\modules\common\models\Zloradnij::print_arr($_POST);
\app\modules\common\models\Zloradnij::print_arr($_GET);
/*
array (
    'DateTime' => '2016-07-14 09:25:16',
    'TransactionID' => '77393366',
    'OrderId' => '10047354',
    'Amount' => '10.00',
    'Currency' => 'RUB',
    'SecurityKey' => 'fb0c4babe33c3a1ece46eaef004ff54e',
    'OrderDescription' => 'Оплата заказа',
    'lang' => 'ru',
    'Provider' => 'Card',
    'PaymentAmount' => '10.00',
    'PaymentCurrency' => 'RUB',
    'CardHolder' => 'QWERQWER',
    'CardNumber' => '************1111',
    'Country' => 'RU',
    'City' => 'Новосибирск',
    'ECI' => '7',
)
    */

if(!empty($_POST['OrderId']) && !empty($_POST['TransactionID']) && !empty($_POST['Amount']) && empty($_POST['ErrorCode'])) {
    $emptyTransaction = \app\modules\common\models\UsersPays::find()->where(['id' => intval($_POST['OrderId']), 'status' => 0]);
    if ($emptyTransaction->count() == 1) {
        $emptyTransaction = $emptyTransaction->one();
    }

    $order = \app\modules\shop\models\Orders::findOne($emptyTransaction->order_id);

    $payment = new \app\modules\coders\models\Payment([
        'orderReport' => $order ? $order->id : false,
        //'orderId' => $emptyTransaction->id,
        'amount' => $_POST['Amount'],
        'orderDescription' => $_POST['OrderDescription'],
    ]);

    // Проверяем ключ
    if ($payment->checkSecurityKey($_POST['SecurityKey'])) {
        // Устанавливаем юзера платежа
        $user = \app\modules\common\models\User::findOne($emptyTransaction->user_id);
        $payment->setUser($user);

        $payment->checkEmptyAccount($emptyTransaction->id);

        if (!empty($_POST['RebillAnchor'])) {
            //  Сохраняем карту
            $card = \app\modules\common\models\UsersCards::find()
                ->where([
                    'user_id' => $user->id,
                    'card_number' => $_POST['CardNumber'],
                    'status' => 1,
                ])
                ->one();
            if (!$card) {
                $card = new \app\modules\common\models\UsersCards();
                $card->user_id = $user->id;
                $card->card_number = $_POST['CardNumber'];
                $card->rebill_anchor = $_POST['RebillAnchor'];
                $card->date = date('Y-m-d H:i:s');
                $card->status = 1;
                $card->save();
            }
        }

        if (!$order) {

        } else {
            $payment->setOrder();
            $payment->orderPayment();
        }
    }
}
