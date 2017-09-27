<?php

use yii\helpers\Html;
use \app\modules\common\models\ModFunctions;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Операции с балансом');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content">

    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-3">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">
            <h1 class="title my"><?= $this->title; ?></h1>
            <div class="form-pay">
                <div class="my-balance">
                    <div class="item">
                        <div class="money"><span class="name"><?=\Yii::t('app','Баланс Esalad') ?>:</span> <?= ModFunctions::money($user->money)?></div>
                        <div class="text-micro"><?=\Yii::t('app','Баланс интернет-магазина Esalad. Совершайте покупки в интернет-магазине Esalad с удовльствием.') ?></div>
                    </div>
                    <div class="item">
                   <div class="money"><span class="name"><?=\Yii::t('app','Бонусный баланс') ?>:</span> <?= ModFunctions::bonus($user->bonus)?></div>
                        <div class="text-micro"><?=\Yii::t('app','Бонусный баланс. Потратить бонусные деньги вы можете в магазине Esalad на товары, участвующие в бонусной программе.') ?></div>
                    </div>
                </div>

                <div class="error hidden"><?=\Yii::t('app','Сообщения!')?></div>
                <?php
                if(!empty($pays)){
                    $template = ' 
                        <div class="my-balance table-balance">
                            <table cellpadding="0" cellspacing="0" border="0" class="my-table mob-table">
                                <tr class="res">
                                    <th>№</th>
                                    <th>'.Yii::t('app','Дата  покупки').'</th>
                                    <th>№ '.Yii::t('app','заказа').'</th>
                                    <th>'.Yii::t('app','Тип').'</th>
                                    <th>'.Yii::t('app','Сумма').'</th>
                                </tr>';
                    $i=1;
                    foreach ($pays as $pay){
                        $payType = '';
                        $koef = 1;
                        switch (intval($pay->type)) {
                            case 0: $payType = Yii::t('app',"не указан"); break;
                            case 1: $payType = Yii::t('app',"Пополнение счета"); break;
                            case 2: $payType = Yii::t('app',"Пополнение счета (ExtremeFitness)"); break;
                            case 3: $payType = Yii::t('app',"Старые продажи"); break;
                            case 4: $payType = Yii::t('app',"Оплата заказа на сайте"); $koef = 1; break;
                            case 5: $payType = Yii::t('app',"Отмена товара"); break;
                            case 6: $payType = Yii::t('app',"Комиссия за продажу товара"); break;
                            case 8: $payType = Yii::t('app',"Старые продажи"); break;
                            case 9: $payType = Yii::t('app',"Оплата доставки"); $koef = 1; break;
                            case 10: $payType = Yii::t('app',"Перевод с клиента на клиента"); break;
                            case 13: $payType = Yii::t('app',"Оплата заказа через терминал"); $koef = 1; break;
                            case 20: $payType = Yii::t('app',"Зачисление наличными"); break;
                            case 21: $payType = Yii::t('app',"Комиссия за доставку товара"); break;
                            case 22: $payType = Yii::t('app',"Вывод средств"); $koef = 1; break;
                            case 23: $payType = Yii::t('app',"Зачисление средств"); break;
                            case 31: $payType = Yii::t('app',"Оплата бонусов"); $koef = 1; break;
                            case 32: $payType = Yii::t('app',"cashback за покупку по акции"); break;
                            case 33: $payType = Yii::t('app',"списание cashback за отмену заказа"); $koef = 1; break;
                        }
                        $template .= '
                                <tr>
                                    <td>'.$i.'</td>
                                    <td>'.Date('d.m.Y H:i', strtotime($pay->date)).'</td>
                                    <td>'.$pay->order_id.'</td>
                                    <td>'.$payType.'</td>
                                    <td>'.($pay->money==0? '0' : $pay->money*($koef)).'</td>
                                </tr>';
                        $i++;
                    }
                    $template .= '
                            </table>
                        </div>';
                }
                echo !empty($template) ? $template : '';
                ?>
                <div class="form___gl">
                    <span class="caption"><?=\Yii::t('app','Пополнить на') ?>:</span>
                    <form action="" method="post" class="money" role="form">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->csrfToken?>">
                        <div class="money form-group min-1"><input type="text" name="money" value="0" maxlength="8" class="money form-control" /></div>
                        <span class="caption"><?=\Yii::t('app','Выберите способ оплаты')?>:</span>
                        <?= \app\components\WPaymentSelectMy::widget([
//                            'basket' => $basket,
                        ])?>
                        <div class="button"><input type="submit" name="pay" value="<?=\Yii::t('app','Пополнить счет') ?>"  class="button_oran"/></div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

