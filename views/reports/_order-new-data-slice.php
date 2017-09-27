<?php
use app\modules\common\models\ModFunctions;
?>
<div>
    <b>Куплено</b><br>
    Продуктов на <?=ModFunctions::moneyFormat($data['totalFood']);?> руб.<br>
    Спортивных товаров на <?=ModFunctions::moneyFormat($data['totalSport']);?> руб.<br>
    Товаров для дома на <?=ModFunctions::moneyFormat($data['totalHome']);?> руб.<br>
    Средняя комиссия = <?=ModFunctions::moneyFormat($data['midComission']);?> %<br>
    Средняя стоимость 1 кг = <?=ModFunctions::moneyFormat($data['midWeight']);?>  руб.<br>
    <br>
        <?php
        $totalHome = 0;
        $totalFood = 0;
        $totalSport = 0;
        if($data['totalHome'] !== 0){
            $totalHome = round($data['homeComission']/($data['totalHome']/100),2);
        }
        if($data['totalFood'] !== 0){
            $totalFood = round($data['foodComission']/($data['totalFood']/100),2);
        }
        if($data['totalSport'] !== 0){
            $totalSport = round($data['sportComission']/($data['totalSport']/100),2);
        }
        ?>
    <b>Средний процент</b><br>
        Продукты = <?=ModFunctions::moneyFormat($totalFood);?> % <br>
        Товары для дома = <?= ModFunctions::moneyFormat($totalHome);?> % <br>
        Спортпит = <?=ModFunctions::moneyFormat($totalSport);?> %
</div>