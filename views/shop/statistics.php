<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use app\modules\shop\models\OrdersStatus;
//use yii\grid\GridView;

$this->title = 'Статистика продаж';
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="shop-statistics">
    <div class="statisticBlock  small">
        <h5>Статистика по товарам</h5>
        <?php
        foreach($statistic['value'] as $key => $item){
            ?>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small"><?=$statistic['title'][$key]?></div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small" data-param="<?=$key?>"><?=$item?></div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <?php
    if($manager){
        ?>
        <div class="statisticBlock  small">
            <h5>Ваш менеджер</h5>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small">Имя</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small"><?=$manager->name?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small">Телефон</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small"><?=$manager->phone?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                    <div class="group small">Email</div>
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                    <div class="visibleShopParams group small"><?=$manager->email?></div>
                </div>
            </div>
        </div>
        <?php
    }else{

    }
    ?>

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
    <div>
        <span class="arrowMonthLeft">&nbsp;&nbsp;&nbsp;<<&nbsp;&nbsp;&nbsp;</span>
        <?php
        foreach($monthLine as $month){
            ?>
            <span class="monthVariant">
            <span class="monthTitle">
                <?=$monthLanguage[$month]?>
            </span>
            <span class="monthStatisticValue">
                <span class="getNewValue" id="<?=$month?>Count" data-variant="Count" data-period="<?=$month?>">
                    Количество / <?=$monthLanguage[$month]?>
                </span>
                <span class="getNewValue" id="<?=$month?>Count" data-variant="Price" data-period="<?=$month?>">
                    Доход / <?=$monthLanguage[$month]?>
                </span>
            </span>
        </span>
            <?php
        }
        ?>
        <span class="arrowMonthRight">&nbsp;&nbsp;&nbsp;>>&nbsp;&nbsp;&nbsp;</span>
    </div>
    <hr />

    <span id="shopStatisticData" data-value='<?=$visibleParams?>'></span>

    <div id="shopStatisticCanvas" style="padding:30px 50px;background:#FFF;"></div>
    <br />
    <hr />
    <br />
    <form method="GET" action="" id="dateFilterForm">
        <div class="container-input">
            <span class="name_label" style="bottom: -30px;">Дата заказа</span>
            <label class="label-form" style="vertical-align: top;">
                От
                <input
                    class="form-control date"
                    id="personalShopDateStart"
                    type="date"
                    name="orders-provider-date-start"
                    value="<?=(!empty($filterOrders['orders-provider-date-start'])?$filterOrders['orders-provider-date-start']:date('Y-m-d',$dateList['min']))?>"
                >
            </label>
            <label class="label-form" style="vertical-align: top;">До
                <input
                    class="form-control date"
                    id="personalShopDateStop"
                    type="date"
                    name="orders-provider-date-stop"
                    value="<?=(!empty($filterOrders['orders-provider-date-stop'])?$filterOrders['orders-provider-date-stop']:date('Y-m-d',$dateList['max']))?>"
                >
            </label>
        </div>
        <br />
        <div class="container-input">
            <span class="name_label">Cостояние заказа</span>
            <label class="label-form" style="vertical-align: top;">
                <select name="orders-provider-status" class="form-control">
                    <option value="all">Все</option>
                    <option value="disable"<?=(($filterOrders['orders-provider-status'] == 'disable') ? 'selected' : '')?>>Отменён</option>
                    <?php
                    $statusListFilter = OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->indexBy('id')->all();
                    foreach ($statusListFilter as $key => $status) {
                        print '
                            <option value="' . $status->id . '" ' . (($status->id == $filterOrders['orders-provider-status']) ? 'selected' : '') . '>' . $status->name . '</option>
                            ';
                    }
                    ?>
                </select>
            </label>
        </div>
        <div class="clear"></div>
        <br />
        <br />
        <button type="submit" name="setFilter" value="1" class="btn btn-primary" />Показать</button>
        &nbsp;
        <button type="submit" name="delFilter" value="1" class="btn btn-primary" />Сбросить фильтр</button>
    </form>
    <br />
    <div class="reportSaleProducts">
        <?php
        if(isset($goodsReport) && !empty($goodsReport)){

            echo ExportMenu::widget([
                    'dataProvider' => $goodsReport,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            'attribute'=>'goodName',
                            'label'=>'Товар',
                            'contentOptions' =>['class' => 'table_class'],
                            'content'=>function($data){
                                return '<a href="/catalog/'.$data['goodId'].'">'.$data['goodName'].' - '.$data['variantParams'].'</a>';
                            }
                        ],
                        [
                            'attribute'=>'count',
                            'label'=>'Продано',
                            'contentOptions' =>['class' => 'table_class'],
                            'content'=>function($data){
                                return $data['count'];
                            }
                        ],
                    ],
                    //'export' => false,
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_CSV => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_EXCEL => false,
                    ],
                    'fontAwesome' => true,
                    'dropdownOptions' => [
                        'label' => 'Экспорт',
                        'class' => 'btn btn-default'
                    ]
                ]) . "<hr>\n";

            print GridView::widget([
                'dataProvider' => $goodsReport,
                'filterModel' => $searchModel,
                'responsiveWrap'=> false,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute'=>'goodName',
                        'label'=>'Товар',
                        'contentOptions' =>['class' => 'table_class'],
                        'content'=>function($data){
                            return '<a href="/catalog/'.$data['goodId'].'">'.$data['goodName'].' - '.$data['variantParams'].'</a>';
                        }
                    ],
                    [
                        'attribute'=>'count',
                        'label'=>'Продано',
                        'contentOptions' =>['class' => 'table_class'],
                        'content'=>function($data){
                            return $data['count'];
                        }
                    ],
                ],
                'export' => false,
                /*'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_CSV => false,
                    ExportMenu::FORMAT_PDF => false,

                ],*/
            ]);
        }
        ?>
    </div>
</div>

