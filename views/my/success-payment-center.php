<?php
$this->title = 'Заказ успешно создан';
$this->params['breadcrumbs'][] = $this->title;
//print_arr($orders);
?>
<div class="content">
    <div class="path"><a href="/">Главная</a></div>
    <div class="row">
        <!--sidebar-->
        <div class="sidebar col-md-3 col-xs-3">
            <?= \app\components\WSidebar::widget(); ?>
        </div>
        <!--sidebar-->
        <div class="col-md-9 col-xs-9">

            <!--История заказака -->
            <div class="my-orders my-list ">
                Заказ успешно создан
            </div> <!--/История заказака -->
        </div>
        <div class="clear"></div>
    </div>
</div>
