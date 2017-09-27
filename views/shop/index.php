
<?php

use app\modules\common\models\Zloradnij;

//Zloradnij::print_arr($statistic);die()
?>


<div id="shop-statistics">
    <div class="statisticBlock small">
        <h5>Статистика по товарам</h5>
        <?php if(!empty($statistic)){ ?>
        <?php foreach($statistic['value'] as $key => $item) { ?>
                <div class="row">
                    <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                        <div class="group small"><?=$statistic['title'][$key]?></div>
                    </div>
                    <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                        <div class="visibleShopParams group small" data-param="<?=$key?>"><?=$item?></div>
                    </div>
                </div>
        <?php } ?>
        <?php } ?>
    </div>

    <div class="statisticBlock small">
        <h5>Оплата аренды</h5>
        <?php if(!empty($payment)){ ?>
        <?php foreach($payment['value'] as $key => $item) { ?>
                <div class="row">
                    <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                        <div class="group small"><?=$payment['title'][$key]?></div>
                    </div>
                    <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                        <div class="visibleShopParams group small" data-param="<?=$key?>"><?=$item?></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="statisticBlock small">
        <h5>Ваш менеджер</h5>
        <?php if(!empty($manager)){ ?>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small">Имя</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small"><?=$manager['name']?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small">Телефон</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small"><?=$manager['phone']?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small">Email</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small"><?=$manager['email']?>&nbsp;</div>
                </div>
            </div>
    <?php } else {?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="group small">Ещё не назначен</div>
                </div>
            </div>
        <?php } ?>
    </div>


    <span id="current-graph" data-value="getNewValue"></span>

    <span class="getNewValue active" id="yearPrice" data-variant="Price" data-period="year">
        Доход / Год
        <span class="monthVariantSimbil">$</span>
    </span>
    <span class="getNewValue" id="yearCount" data-variant="Count" data-period="year">
        Количество / Год
        <span class="monthVariantSimbil">C</span>
    </span>
    <hr />

    <span id="shopStatisticData" data-value='<?=$visibleParams?>'></span>
    <div id="shopStatisticCanvas" style="height:420px;padding:30px 50px;background:#FFF;"></div>
</div>
