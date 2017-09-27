<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказ успешно создан';
$this->params['breadcrumbs'][] = $this->title;
//print_arr($orders);
//Yii::$app->request->get('ORDER')
?>
<div class="content">
    <div class="path"><a href="/">Главная</a></div>
    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-12">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-12">

            <!--История заказака -->
            <div class="my-orders my-list ">
                Заказ успешно создан - # <?= !empty($order->id) ? $order->id : ''?>
            </div> <!--/История заказака -->
        </div>
        <div class="clear"></div>
    </div>
</div>
