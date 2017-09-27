<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Url;

// Отключаем стили;
/*
use app\assets\AppAsset;
$css = AppAsset::register($this);
foreach($css->css as $key=>$item) {
   $_css = explode('/',$item);
   if(!empty($_css[2]) == 'shop.css') {
       unset($css->css[$key]);
   }
}
*/
?>

<!--Прием товаров-->

        <?php
        // Фильтер блок;
        print $this->render('_search-order-list', [
            'filter' => $filter,
        ]);?>
<div id="repost_order_list">
        <?php
        // Список товаров;
        print \yii\widgets\ListView::widget([
            'dataProvider' => $orders,
            'itemView' => '_order-list',
            'layout' => "{items}<div class='clear'></div>\n{pager}",
            'itemOptions' => [
                'tag' => 'div',
                'class' => 'items',
            ],
        ]);
        ?>
</div>

