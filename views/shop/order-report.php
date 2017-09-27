<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\shop\models\OrdersStatus;
use app\modules\common\models\UserShop;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчёт по заказам';
$this->params['breadcrumbs'][] = $this->title;

$ordersList = [];
GLOBAL $dataProviderOrderIdsBig;
$dataProviderOrderIdsBig = $dataProviderOrderIds;
?>

<div class="shops-order-report">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php



    ?>

    <form method="GET" action="" id="dateFilterForm">
        <label style="vertical-align: top;">
            <input
                class="form-control"
                type="radio"
                name="orders-provider-confirm"
                value="all"
                style="display: inline-block;width:15px;margin-right: 15px;"
                <?=(!isset($filterOredrs['orders-provider-confirm']) || empty($filterOredrs['orders-provider-confirm']) || $filterOredrs['orders-provider-confirm'] == 'all')?' checked':''?>
            ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Все</span>
        </label>
        <label style="vertical-align: top;">
            <input
                class="form-control"
                type="radio"
                name="orders-provider-confirm"
                value="noconfirm"
                style="display: inline-block;width:15px;margin-right: 15px;"
                <?=(!empty($filterOredrs['orders-provider-confirm']) && $filterOredrs['orders-provider-confirm'] == 'noconfirm')?' checked':''?>
            ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Необработанные заказы</span>
        </label>
        <label style="vertical-align: top;">
            <input
                class="form-control"
                type="radio"
                name="orders-provider-confirm"
                style="display: inline-block;width:15px;margin-right: 15px;"
                value="overdue"
                <?=(!empty($filterOredrs['orders-provider-confirm']) && $filterOredrs['orders-provider-confirm'] == 'overdue')?' checked':''?>
            ><span style="display: inline-block;line-height: 35px;margin-top: 4px;vertical-align: top;">Просроченные заказы</span>
        </label>
        <br />
        <label style="vertical-align: top;">Дата
            <select name="orders-provider-date-variant" class="form-control">
                <option value="delivery"<?=(($filterOredrs['orders-provider-date-variant'] == 'delivery') ? 'selected' : '')?>>Доставки</option>
                <option value="order"<?=(($filterOredrs['orders-provider-date-variant'] == 'order') ? 'selected' : '')?>>Заказа</option>
            </select>
        </label>

        <label style="vertical-align: top;">От

            <!--
            <input
                class="form-control"
                id="personalShopDateStart"
                type="date"
                name="orders-provider-date-start"
                value="<?=(!empty($filterOredrs['orders-provider-date-start']) ? $filterOredrs['orders-provider-date-start']:'')?>"
            >-->
            <?php
            echo DatePicker::widget([
                'id'=> 'personalShopDateStart1',
                'name' => 'orders-provider-date-start',
                'type' => DatePicker::TYPE_INPUT,
                'value' => (!empty($filterOredrs['orders-provider-date-start']) ? date('d.m.Y',strtotime($filterOredrs['orders-provider-date-start'])):date('d.m.Y')),
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]);
            ?>
        </label>
        <label style="vertical-align: top;">До
            <!--
            <input
                class="form-control"
                id="personalShopDateStop"
                type="date"
                name="orders-provider-date-stop"
                value="<?=(!empty($filterOredrs['orders-provider-date-stop'])?$filterOredrs['orders-provider-date-stop']:date('Y-m-d'))?>"
            >-->
            <?php
                echo DatePicker::widget([
                    'id'=> 'personalShopDateStop1',
                    'name' => 'orders-provider-date-stop',
                    'type' => DatePicker::TYPE_INPUT,
                    'value' => (!empty($filterOredrs['orders-provider-date-stop']) ? date('d.m.Y',strtotime($filterOredrs['orders-provider-date-stop'])):date('d.m.Y')),
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]);
            ?>
        </label>
        <br />
        <label style="vertical-align: top;">Cостояние заказа
            <select name="orders-provider-status" class="form-control">
                <option value="all">Все</option>
                <option value="disable"<?=(($filterOredrs['orders-provider-status'] == 'disable') ? 'selected' : '')?>>Отменён</option>
                <?php
                $statusListFilter = OrdersStatus::find()->where(['type' => 2])/*->andWhere(['<>','id',1007])*/->andWhere(['status' => 1])->indexBy('id')->all();
                foreach ($statusListFilter as $key => $status) {
                    print '
                        <option value="' . $status->id . '" ' . (($status->id == $filterOredrs['orders-provider-status']) ? 'selected' : '') . '>' . $status->name . '</option>
                        ';
                }
                ?>
            </select>
        </label>
        <?php
        if($shopId == 10000130 || $shopId == 10000154){
            ?>

            <label style="vertical-align: top;">Клуб
                <select name="orders-provider-club" class="form-control">
                    <option value="all">Все</option>
                    <option
                        value="home" <?= (('home' == $filterOredrs['orders-provider-club']) ? 'selected' : '') ?>>
                        Прочее
                    </option>
                    <?php
                    $extremeAddress = \app\modules\basket\models\BasketLg::getClubDelivery();

                    foreach ($extremeAddress as $key => $club) {
                        print '
                        <option value="' . $club['value'] . '" ' . (($club['value'] == $filterOredrs['orders-provider-club']) ? 'selected' : '') . '>' . $club['address'] . '</option>
                        ';
                    }
                    ?>
                </select>
            </label>
            <?php
        }
        ?>
        <br />
        <br />
        <button type="submit" name="setFilter" value="1" class="btn btn-primary" />Показать</button>
        &nbsp;
        <button type="submit" name="delFilter" value="1" class="btn btn-primary" />Сбросить фильтр</button>
    </form>

    <?php
    if(!empty($dataProvider)) {
        $gridColumns = [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'ordersUserId',
                    'label' => '',
                    'value'=>function ($data, $model) use ($shopId) {
                        if($shopId == 10000145 || $shopId == 10000154){
                            $user = UserShop::find()
                                ->select('id, name, phone, level')
                                ->where(['id' => $data['ordersUserId']])
                                ->one();
                            if ($user['level'] > 0) {
                                return 'Сотрудник ('.$user['id'].', '.$user['name'].', '.$user['phone'].','.$user['level'].')';
                            } else {
                                return '-';
                            }
                        }
                        else {
                            return ' ';
                        }
                    },
                    'mergeHeader'=>true,
                    'format'=>'raw',
                ],
                [
                    'attribute' => 'orderId',
                    'label' => 'Номер заказа',
                    'value' => function($data){
                        GLOBAL $dataProviderOrderIdsBig;
                        if(isset($dataProviderOrderIdsBig[$data['orderId']])){
                            return $data['orderId'];
                        }
                        return '';
                    },
                    //'mergeHeader'=>true,
                    'format'=>'raw',
                ],
                [
                    'attribute' => 'productName',
                    'label' => 'Товар',
                    'value' => function($data){
                        return '<a href="'.app\modules\catalog\models\Goods::getPath($data['productId']).'">'.$data['productName'] . ' ' . $data['tags'] . '</a>';
                    },
                    'mergeHeader'=>true,
                    'format'=>'html',
                ],
            [
                    'attribute' => 'productCode',
                    'label' => 'Атикул',
                    'mergeHeader'=>true,
                    'format'=>'html',
                ],
                [
                    'attribute' => 'store_id',
                    'label' => 'Склад',
                    'value' => function($data){
                        $ad =  \app\modules\common\models\Address::find()->leftJoin('shops_stores','shops_stores.address_id = address.id')->where(['shops_stores.id' => $data['store_id']])->one();
                        return $ad->street . ', ' . $ad->house;
                    },
                    'mergeHeader'=>true,
                    'format'=>'raw',
                ],
                [
                    'attribute' => 'productCount',
                    'label' => 'Количество',

                    'value' => function($data){
                        $address = \app\modules\common\models\Address::find()
                            ->leftJoin('shops_stores','shops_stores.address_id = address.id')
                            ->where(['shops_stores.id' => $data['store_id']])
                            ->one();
                        return $data['productCount'];
                    },
                    'mergeHeader'=>true,
                    'format' => 'html',
                ],
                [
                    'attribute' => 'allMoney',
                    'label' => 'Сумма',
                    'value' => function($data){
                        return $data['allMoney'] . ' р.';
                    },
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'productPrice',
                    'label' => 'Price',
                    'value' => function($data){
                        return $data['productPrice'] . ' р.';
                    },
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'ordersItemStatusId',
                    'label' => 'Статус',
                    'content' => function($data) use ($statusList) {
                        //$htmlSelectStatus = $data['ordersStatusId'].' '.$data['orderItemStatus'].' '.$data['orderItemStatusId'].' '.$data['orderItemId'] . ' ';
                        $htmlSelectStatus = '';
                        if($data['ordersStatusId'] == 1 && $data['orderItemStatus'] == 1 && isset($data['orderItemStatusId']) && !empty($data['orderItemStatusId'])){
                            $htmlSelectStatus .= '<span class="text-warning">' . $statusList[$data['orderItemStatusId']]->name . '</span>';
                        }elseif($data['ordersStatusId'] == 0 || $data['orderItemStatus'] == 0){
                            $htmlSelectStatus .= '<span class="text-danger">Отменён</span>';
                        }elseif(!isset($data['orderItemStatusId']) && empty($data['orderItemStatusId'])){
                            $htmlSelectStatus .= '<span class="bg-success">Новый заказ</span><br /><br />';
                            $htmlSelectStatus .= '
                            <select class="seller_status" id="status_change_'.$data['orderItemId'].'" style="width: 140px;">
                                <option value="1001">' . $statusList[1001]->name . '</option>
                                <option value="1002">' . $statusList[1002]->name . '</option>
                            </select>
                            <br /><br />
                            <div style="text-align: center;">
                                <div data-id="'.$data['orderItemId'].'" data-order-item="'.$data['orderItemId'].'" class="button_blue button-save-status-order button_ok" data-action="save-status"><div>Сохранить</div></div>
                            </div>
                            ';
                        }
                        return $htmlSelectStatus;

                    },
                    'filter' => ArrayHelper::map(OrdersStatus::find()->where(['type' => 2])->andWhere(['status' => 1])->indexBy('id')->all(), 'id', 'name'),
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'orderDate',
                    'label' => 'Дата заказа',
                    'value' => function($data){
                        return $data['orderDate'];
                    },
                    'filter' => false,
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'deliveryDate',
                    'label' => 'Дата доставки',
                    'value' => function($data){
                        return $data['deliveryDate'];
                    },
                    'filter' => false,
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'userAddress',
                    'label' => 'Адрес',
                    'value' => function($data){
                        return $data['deliveryName'] . ' ' . $data['userAddress'];
                    },
                    'filter'=>array("1"=>"Активно","2"=>"Не активно"),
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'commentShop',
                    'label' => 'Комментарий',
                    'format' => 'raw',
                    'value' => function($data){
                        $comment = '
                        <a aria-label="Комментарий" title="Комментарий" href="" class="order-report-comment-control">
                            <span class="glyphicon glyphicon-eye-close"></span>
                        </a>
                        <div class="order-report-comment-text">'.$data['commentShop'].'</div>
                        <div class="not-visible order-report-comment-popup">
                            <textarea rows="7" style="width:100%;text-align:left;">'.$data['commentShop'].'</textarea>
                            <input type="submit" value="Сохранить" class="button-save-comment-order" data-order-item="'.$data['orderItemId'].'">
                        </div>
                        ';
                        return $comment;
                    },
                    'mergeHeader'=>true,
                ],
                [
                    'attribute' => 'comments',
                    'label' => 'Комментарий клиента',
                    'mergeHeader'=>true,
                    'format' => 'raw',
                ],
            ];
        echo GridView::widget([
            'dataProvider'=>$dataProvider,
            'filterModel'=>$searchModel,
            'columns'=>$gridColumns,

           // 'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
            //'headerRowOptions'=>['class'=>'kartik-sheet-style'],
            'filterRowOptions'=>['class'=>'kartik-sheet-style'],
            'responsiveWrap'=>false,
            'responsive'=>false,
           // 'hover'=> true,
            'pjax'=>true, // pjax is set to always true for this demo
            // set your toolbar
            'toolbar'=> [
                '{export}',
                '{toggleData}',
            ],
            // set export properties
            'export'=>[
                'fontAwesome'=>true
            ],
            // parameters from the demo form

            'panel'=>[
               // 'type'=>GridView::TYPE_DEFAULT,
                'heading'=>false,
                //'before'=> false,
               // 'after'=> false,
            ],

            'persistResize'=>false,
            //'exportConfig'=>$exportConfig,
        ]);
    }
    ?>

</div>
