<?php

use app\modules\coders\models\GetPayment;

//Указываем локализацию (доступно ru | en | fr)
$Language = "ru";
// Указываем идентификатор мерчанта
$MerchantId='70664';
//Указываем приватный ключ (см. в ЛК PayOnline в разделе Сайты -> настройка -> Параметры интеграции)
$PrivateSecurityKey='aa9c8615-9577-4061-a4e5-b36dbdd12ba7';
//Номер заказа (Строка, макс.50 символов)
//$OrderId='1242212й33';
//Валюта (доступны следующие валюты | USD, EUR, RUB)
$Currency='RUB';
//Сумма к оплате (формат: 2 знака после запятой, разделитель ".")
//$Amount=2;
//Описание заказа (не более 100 символов, запрещено использовать: адреса сайтов, email-ов и др.) необязательный параметр
//$OrderDescription="Оплата коммунальных услуг за Август 2013. Cумма 100,00 ФЛС 113";
//Срок действия платежа (По UTC+0) необязательный параметр
//$ValidUntil="2013-10-10 12:45:00";
//В случае неуспешной оплаты, плательщик будет переадресован, на данную страницу.
//$FailUrl="http://payonline.ru";
// В случае успешной оплаты, плательщик будет переадресован, на данную страницу.
//$ReturnUrl="yandex.ru";

//Создаем класс
$pay = new GetPayment;
//Показываем ссылку на оплату
$result=$pay->GetPaymentURL(
    $pay->Language=$Language,
    $pay->MerchantId=$MerchantId,
    $pay->PrivateSecurityKey=$PrivateSecurityKey,
    $pay->OrderId=$OrderId,
    $pay->Amount=number_format($Amount, 2, '.', ''),
    $pay->Currency=$Currency,
    $pay->OrderDescription=$OrderDescription,
//    $pay->ValidUntil=$ValidUntil,
    $pay->ReturnUrl=$ReturnUrl,
    $pay->FailUrl=$FailUrl
);

echo "<meta http-equiv='refresh'  content='0; URL=".$result."'>";