<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Проблемы с заказом';
$this->params['breadcrumbs'][] = $this->title;
//print_arr($orders);
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
                С заказом возникли проблемы, обратитесь к оператору Call центра.<br /><br /><br /><?php
                if($errorCode > 0){
                    print Yii::$app->params['payOnlineErrorCode'][$errorCode];
                }?>
            </div> <!--/История заказака -->
        </div>
        <div class="clear"></div>
    </div>
</div>
